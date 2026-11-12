<?php

namespace App\Modules\Restaurant\Application\Ports;

interface OpenOrderPort{
    
    public function create(int $branchId, ?int $customerId, int $sellerId): int;

    /**
     * Debe llamarse dentro de una transacción abierta.
     *
     * @return array{lineId: int, totales: array}
     */
    public function getSummary(int $orderId, int $branchId): array;
    /**
    * Agrega un lote dentro de la transacción del llamador.
    * @param array<int, array{productId: int, quantity: string}> $items
    * @return array{lineIds: array<int, int>, totales: array}
    */
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array;
    /**
    * Crea una factura Abierta con su primer lote.
    * Requiere una transacción abierta por el llamador.
    * @param array<int, array{productId: int, quantity: string}> $items
    * @return array{idfactura: int, lineIds: array, totales: array}
    */
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array;

    // Requiere una transacción abierta por el caso de uso.
    public function updateCustomer(int $orderId, int $branchId, ?int $customerId): void;

    /**
     * Anula una cantidad y recalcula la factura dentro de la transacción del llamador.
     * alreadyCancelled indica que la fila original ya estaba anulada.
     * @return array{lineId: int, alreadyCancelled: bool, totales: array}
     */
    public function cancelLine(int $orderId, int $lineId, int $branchId, int $userId, string $reason, string $quantity): array;

    /** @return array{lineId: int, totales: array} */
public function applyLineDiscount(
    int $orderId, int $lineId, int $branchId,
    string $type, string $value, string $password
): array;

}
