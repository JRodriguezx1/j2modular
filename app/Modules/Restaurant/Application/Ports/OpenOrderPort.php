<?php

namespace App\Modules\Restaurant\Application\Ports;

interface OpenOrderPort{
    
    public function create(int $branchId, ?int $customerId, int $sellerId): int;

    /**
     * Debe llamarse dentro de una transacción abierta.
     *
     * @return array{lineId: int, totales: array}
     */
    public function addSimpleProduct(int $orderId, int $productId, string $quantity, int $branchId): array;
    public function getSummary(int $orderId, int $branchId): array;
    /**
    * Agrega un lote dentro de la transacción del llamador.
    *
    * @param array<int, array{productId: int, quantity: string}> $items
    * @return array{lineIds: array<int, int>, totales: array}
    */
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array;
    /**
    * Crea una factura Abierta con su primer lote.
    * Requiere una transacción abierta por el llamador.
    *
    * @param array<int, array{productId: int, quantity: string}> $items
    * @return array{idfactura: int, lineIds: array, totales: array}
    */
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array;

}