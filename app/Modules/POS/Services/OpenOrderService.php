<?php

namespace App\Modules\POS\Services;

use App\Models\ventas\facturas;
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
    public function addSimpleProduct(
        int $orderId,
        int $productId,
        string $quantity,
        int $branchId
    ): array {
        if ($orderId <= 0 || $productId <= 0 || $branchId <= 0
            || preg_match('/\A(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?\z/', $quantity) !== 1
            || (float) $quantity <= 0) {
            throw new InvalidArgumentException('Orden, producto o cantidad no válidos.');
        }

        $orden = facturas::bloquearOrdenAbierta($orderId, $branchId);
        if ($orden === null) {
            throw new InvalidArgumentException('La orden no está abierta en esta sucursal.');
        }

        $producto = productos::buscarSimpleParaOrden($productId, $branchId);
        if ($producto === null) {
            throw new InvalidArgumentException(
                'El producto simple no está habilitado para venta en esta sucursal.'
            );
        }

        $impuesto = config_local::impuestoVentaSucursal(
            $branchId,
            $producto['impuesto'] === null ? null : (int) $producto['impuesto']
        );

        // precio_venta ya incluye el impuesto, como en el POS actual.
        $precio = (float) $producto['precio_venta'];
        if (!is_finite($precio) || $precio < 0) {
            throw new RuntimeException('El precio del producto no es válido.');
        }

        $subtotal = round($precio * (float) $quantity, 2);
        $tasa = $impuesto === 'excluido' ? 0 : (int) $impuesto;
        $valorImp = round($subtotal * $tasa / (100 + $tasa), 3);
        $base = round($subtotal - $valorImp, 3);

        $porcentajeComision =
            (int) $producto['prioridadcomision'] === 1
                ? (float) $producto['percentcomision']
                : (float) $orden['porcentajeganancia'];
        $valorComision = round($subtotal * $porcentajeComision / 100, 2);

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
}
