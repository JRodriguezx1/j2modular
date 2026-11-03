<?php

namespace App\Modules\POS\Services;

use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use InvalidArgumentException;

class OpenOrderService{

    public function create(int $branchId, ?int $customerId, int $sellerId): int{
        if($branchId <= 0 || $sellerId <= 0 || ($customerId !== null && $customerId <= 0))
            throw new InvalidArgumentException('Los datos de la orden abierta no son válidos.');
        return facturas::crearOrdenAbierta($branchId, $customerId, $sellerId);
    }
    

    //Crea la factura Abierta junto con su primer lote.Requiere una transacción abierta por el llamador.
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array{
        return (new OpenInvoiceService())->procesarNuevaAbierta($branchId, $customerId, $sellerId, $items);
    }
    

    /**
     * @param array<int, array{productId: int, quantity: string}> $items
     * @return array{lineIds: array<int, int>, totales: array}
     */
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array{
        return (new OpenInvoiceService())->addSimpleProducts($orderId, $items, $branchId);
    }


    public function updateCustomer(int $orderId, int $branchId, ?int $customerId): void{
        (new OpenInvoiceService())->updateCustomer($orderId, $branchId, $customerId);
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

}