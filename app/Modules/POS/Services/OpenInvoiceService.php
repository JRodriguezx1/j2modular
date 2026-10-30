<?php

namespace App\Modules\POS\Services;

use App\Models\inventario\productos;
use App\Models\parametrizacion\config_local;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\services\ventasService;
use InvalidArgumentException;
use RuntimeException;


class OpenInvoiceService{

    /**
    * Primer pedido: crea la factura y guarda sus productos.
    *
    * @return array{idfactura: int, lineIds: array, totales: array}
    */
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array{
        if($branchId <= 0 || $sellerId <= 0 || ($customerId !== null && $customerId <= 0))
            throw new InvalidArgumentException('Sucursal, cliente o vendedor no válido.');

        // Comprobamos la estructura antes de crear la factura.
        $items = $this->validateItems($items);
        // Este método existente crea la cabecera con estado Abierta.
        $invoiceId = facturas::crearOrdenAbierta($branchId, $customerId, $sellerId);
        // Reutilizamos el mismo guardado de los consumos posteriores.
        $result = $this->saveItems($invoiceId, $items, $branchId);
        return ['idfactura' => $invoiceId, 'lineIds' => $result['lineIds'], 'totales' => $result['totales']];
    }

    /**
     * Agrega productos pendientes a una factura Abierta existente.
     *
     * @return array{lineIds: array, totales: array}
     */
    public function addSimpleProducts(int $invoiceId, array $items, int $branchId): array{
        if($invoiceId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La factura o la sucursal no es válida.');
        return $this->saveItems($invoiceId, $this->validateItems($items), $branchId);
    }

    /**
     * Valida y conserva únicamente producto y cantidad.
     */
    private function validateItems(array $items): array{
        if($items === [] || !array_is_list($items))
            throw new InvalidArgumentException('El pedido debe contener una lista de productos.');
        
        $validated = [];
        foreach($items as $item){
            if(!is_array($item))throw new InvalidArgumentException('Producto con formato inválido.');
            
            $productId = filter_var($item['productId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $quantity = $item['quantity'] ?? null;
            // Cantidad positiva, como texto, con máximo dos decimales.
            if($productId === false || !is_string($quantity) || preg_match('/\A(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?\z/', $quantity) !== 1 || (float)$quantity <= 0)
                throw new InvalidArgumentException('Producto o cantidad no válido.');
            $validated[] = ['productId' => $productId, 'quantity' => $quantity];
        }

        return $validated;
    }

    /**
     * Prepara el lote, lo guarda y recalcula la factura una sola vez.
     * Recibe elementos cuya estructura ya fue validada.
     */
    private function saveItems(int $invoiceId, array $items, int $branchId): array{
        // Comprueba sucursal, estado Abierta y vendedor. El bloqueo dura hasta que el llamador termine la transacción.
        $order = facturas::bloquearOrdenAbierta($invoiceId, $branchId);
        if($order === null)
            throw new InvalidArgumentException('La factura no está abierta en esta sucursal o su vendedor no existe.');

        // Una consulta para obtener todos los productos.
        $products = productos::buscarSimplesParaOrden(array_column($items, 'productId'), $branchId);
        $lines = [];
        $conflocal = config_local::getParamGlobal($branchId);
        $modoImpuesto = (string)($conflocal['discriminar_impuesto_por_producto']->valor_final ?? '');

        foreach($items as $item){
            $product = $products[$item['productId']] ?? null;
            if($product === null)
                throw new InvalidArgumentException("El producto #{$item['productId']} no está disponible como producto simple.");

            $tax = $modoImpuesto === '0' ? ($conflocal['porcentaje_de_impuesto']->valor_final??null) : $product['impuesto']; //product['impuesto'] puede obtener: null o 0, 5, 16, 19
            if($tax === null || trim($tax) === ''){
                $tax = 'excluido';
            }else{
                $porcentaje = filter_var($tax, FILTER_VALIDATE_INT);
                if($porcentaje===false || $porcentaje<0 || $porcentaje>100)throw new RuntimeException('El porcentaje de impuesto no es válido.');
                $tax = (string)$porcentaje;
            }

            $price = (float)$product['precio_venta'];

            if(!is_finite($price) || $price < 0)
                throw new RuntimeException('El precio del producto no es válido.');

            // El precio de venta incluye el impuesto.
            $subtotal = round($price * (float)$item['quantity'], 2);
            $rate = $tax === 'excluido' ? 0 : (int)$tax;
            $taxValue = round($subtotal * $rate / (100 + $rate), 3);
            $base = round($subtotal - $taxValue, 3);

            $commission = (int)$product['prioridadcomision'] === 1 ? (float)$product['percentcomision'] : (float)$order['porcentajeganancia'];
            // Construir el objeto no inserta ningún registro.
            // El modelo completa los campos opcionales de cada línea.
            $lines[] = new ventas([
                'idfactura' => $invoiceId,
                'idproducto' => $item['productId'],
                'tipoproducto' => 0,
                'tipoproduccion' => (int) $product['tipoproduccion'],
                'rendimientoestandar' => $product['rendimientoestandar'] ?? '1.00',
                'nombreproducto' => $product['nombre'],
                'foto' => $product['foto'] ?? '',
                'costo' => $product['precio_compra'] ?? '0.00',
                'valorunidad' => $product['precio_venta'],
                'cantidad' => $item['quantity'],
                'promediostock' => $product['promediostock'] ?? '0.00',
                'percentcomision' => number_format($commission, 2, '.', ''),
                'valorcomision' => number_format(round($subtotal * $commission / 100, 2), 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'base' => number_format($base, 3, '.', ''),
                'impuesto' => $tax,
                'valorimp' => number_format($taxValue, 3, '.', ''),
                'descuento' => '0.00',
                'total' => number_format($subtotal, 2, '.', ''),
            ]);
        }

        // Aquí se inserta todo el lote utilizando el motor existente.
        // false evita que ventasService controle la transacción.
        [$saved, $lineIds] = ventasService::guardarLineasVenta($lines, false);

        if(!$saved || count($lineIds) !== count($lines))
            throw new RuntimeException('No fue posible guardar todas las líneas.');
        
        return [
            'lineIds' => $lineIds,
            'totales' => facturas::recalcularTotalesOrdenAbierta($invoiceId, $branchId),
        ];
    }
    
}