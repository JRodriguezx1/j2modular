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

    public function getSummary(int $orderId, int $branchId): array{
        return $this->openOrderService->getSummary($orderId, $branchId);
    }

    // llamado por el caso de uso: AddProductsToAccount
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array{
        return $this->openOrderService->addSimpleProducts($orderId, $items, $branchId);
    }

    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array{
        return $this->openOrderService->procesarNuevaAbierta($branchId, $customerId, $sellerId, $items);
    }
    
}