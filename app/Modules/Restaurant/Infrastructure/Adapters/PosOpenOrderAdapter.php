<?php

namespace App\Modules\Restaurant\Infrastructure\Adapters;

use App\Modules\POS\Services\OpenOrderService;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;

class PosOpenOrderAdapter implements OpenOrderPort{

    public function __construct(private OpenOrderService $openOrderService)
    {}

    public function create(int $branchId, ?int $customerId, int $sellerId): int{
        return $this->openOrderService->create($branchId, $customerId, $sellerId);
    }

    // llamado por el caso de uso: GetAccountSummary
    public function getSummary(int $orderId, int $branchId): array{
        return $this->openOrderService->getSummary($orderId, $branchId);
    }

    // llamado por el caso de uso: AddProductsToAccount
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array{
        return $this->openOrderService->addSimpleProducts($orderId, $items, $branchId);
    }

    // llamado por el caso de uso: SaveFirstTableOrder
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array{
        return $this->openOrderService->procesarNuevaAbierta($branchId, $customerId, $sellerId, $items);
    }

    public function updateCustomer(int $orderId, int $branchId, ?int $customerId): void {
        $this->openOrderService->updateCustomer($orderId, $branchId, $customerId);
    }

    // Llamado por CancelAccountLine; conserva la transacción del caso de uso.
    public function cancelLine(int $orderId, int $lineId, int $branchId, int $userId, string $reason, string $quantity): array{
        return $this->openOrderService->cancelLine($orderId, $lineId, $branchId, $userId, $reason, $quantity);
    }

    public function applyLineDiscount(
    int $orderId, int $lineId, int $branchId,
    string $type, string $value, string $password
): array {
    return $this->openOrderService->applyLineDiscount(
        $orderId, $lineId, $branchId, $type, $value, $password
    );
}
    
}
