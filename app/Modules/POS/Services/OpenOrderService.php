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
    
}
