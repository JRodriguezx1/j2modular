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

    public function addSimpleProduct(int $orderId, int $productId, string $quantity, int $branchId): array{
        return $this->openOrderService->addSimpleProduct($orderId, $productId, $quantity, $branchId);
    }

    public function getSummary(int $orderId, int $branchId): array{
        return $this->openOrderService->getSummary($orderId, $branchId);
    }

    public function addSimpleProducts(
    int $orderId,
    array $items,
    int $branchId
): array {
    // El adaptador delega la operación al servicio comercial.
    return $this->openOrderService->addSimpleProducts(
        $orderId,
        $items,
        $branchId
    );
}
    
}