<?php

namespace App\Modules\POS\Services;

use App\Models\ventas\facturas;
use App\Models\inventario\productos;
use App\Models\parametrizacion\config_local;
use App\Models\ventas\ventas;
use InvalidArgumentException;

class OpenOrderService{

    public function create(int $branchId, ?int $customerId, int $sellerId): int{
        if($branchId <= 0 || $sellerId <= 0 || ($customerId !== null && $customerId <= 0))
            throw new InvalidArgumentException('Los datos de la orden abierta no son válidos.');
        return facturas::crearOrdenAbierta($branchId, $customerId, $sellerId);
    }
    

    /**
     * El llamador debe mantener abierta una transacción sobre la conexión
     * compartida. Este método no hace commit ni rollback.
     */
    public function addSimpleProduct(int $orderId, int $productId, string $quantity, int $branchId): array{
        if($orderId <= 0 || $productId <= 0 || $branchId <= 0 || preg_match('/\A(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?\z/', $quantity) !== 1 || (float)$quantity <= 0)
            throw new InvalidArgumentException('Orden, producto o cantidad no válidos.');

        $orden = facturas::bloquearOrdenAbierta($orderId, $branchId);
        if($orden === null)
            throw new InvalidArgumentException('La orden no está abierta en esta sucursal.');
        
        $producto = productos::buscarSimpleParaOrden($productId, $branchId);
        if($producto === null)
            throw new InvalidArgumentException('El producto simple no está habilitado para venta en esta sucursal.');

        $impuesto = config_local::impuestoVentaSucursal($branchId, $producto['impuesto'] === null ? null : (int)$producto['impuesto']);

        // precio_venta ya incluye el impuesto, como en el POS actual.
        $precio = (float)$producto['precio_venta'];
        if(!is_finite($precio) || $precio < 0)throw new \RuntimeException('El precio del producto no es válido.');

        $subtotal = round($precio*(float)$quantity, 2);
        $tasa = $impuesto === 'excluido' ? 0 : (int)$impuesto;
        $valorImp = round($subtotal*$tasa/(100 + $tasa), 3);
        $base = round($subtotal - $valorImp, 3);

        $porcentajeComision = (int)$producto['prioridadcomision'] === 1 ? (float)$producto['percentcomision'] : (float)$orden['porcentajeganancia'];
        $valorComision = round($subtotal*$porcentajeComision / 100, 2);

        $linea = [
            'idfactura' => $orderId,
            'idproducto' => $productId,
            'tipoproducto' => 0,
            'tipoproduccion' => (int) $producto['tipoproduccion'],
            'rendimientoestandar' => $producto['rendimientoestandar'] ?? '1.00',
            'nombreproducto' => $producto['nombre'],
            'foto' => $producto['foto'],
            'costo' => $producto['precio_compra'] ?? '0.00',
            'valorunidad' => $producto['precio_venta'],
            'cantidad' => $quantity,
            'promediostock' => $producto['promediostock'] ?? '0.00',
            'percentcomision' => number_format($porcentajeComision, 2, '.', ''),
            'valorcomision' => number_format($valorComision, 2, '.', ''),
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'base' => number_format($base, 3, '.', ''),
            'impuesto' => $impuesto,
            'valorimp' => number_format($valorImp, 3, '.', ''),
            'descuento' => '0.00',
            'total' => number_format($subtotal, 2, '.', ''),
        ];

        $lineId = ventas::insertarLineaOrdenAbierta($linea);
        $totales = facturas::recalcularTotalesOrdenAbierta($orderId, $branchId);

        return ['lineId' => $lineId, 'totales' => $totales];
    }


    //Este método reúne la cabecera y sus productos sin modificar datos. Una orden abierta sin productos devolverá consumos: [] y los totales iniciales de su cabecera.
    public function getSummary(int $orderId, int $branchId): array{
        if ($orderId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La orden o la sucursal no es válida.');
        $order = facturas::buscarResumenOrdenAbierta($orderId, $branchId);
        if ($order === null)
            throw new InvalidArgumentException('La orden no está abierta en esta sucursal.');

        return ['orden' => $order, 'consumos' => ventas::listarConsumosOrden($orderId, $branchId)];
    }


    /**
     * Agrega productos pendientes a una factura Abierta existente.
     *
     * El llamador debe abrir la transacción y hacer rollback si falla.
     * Este método no confirma ni revierte la transacción.
     *
     * @param array<int, array{productId: int, quantity: string}> $items
     * @return array{lineIds: array<int, int>, totales: array}
     */
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array{
        if($orderId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La factura o la sucursal no es válida.');
        // Debemos recibir una lista con al menos un producto.
        if($items === [] || !array_is_list($items))
            throw new InvalidArgumentException('El pedido debe contener una lista de productos.');

        // Primero comprobamos la estructura de todos los elementos.
        $validatedItems = [];

        foreach($items as $item){
            if(!is_array($item))
                throw new InvalidArgumentException('Hay un producto con formato inválido.');
            $productId = filter_var($item['productId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $quantity = $item['quantity'] ?? null;
            if($productId === false || !is_string($quantity))
                throw new InvalidArgumentException('El producto o la cantidad no tiene el formato esperado.');
            $validatedItems[] = ['productId' => $productId, 'quantity' => $quantity];
        }

        $lineIds = [];
        $totales = [];

        foreach($validatedItems as $item){
            // Reutilizamos el método existente:
            // - comprueba que la factura esté Abierta;
            // - valida producto y cantidad;
            // - obtiene precio e impuesto en el servidor;
            // - inserta la línea y recalcula la factura.
            $result = $this->addSimpleProduct($orderId, $item['productId'], $item['quantity'], $branchId);
            $lineIds[] = $result['lineId'];
            // La última respuesta contiene los totales de toda la factura:
            // consumos anteriores más los productos recién agregados.
            $totales = $result['totales'];
        }

        return ['lineIds' => $lineIds, 'totales' => $totales];
    }

}