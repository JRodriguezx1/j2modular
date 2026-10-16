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

}