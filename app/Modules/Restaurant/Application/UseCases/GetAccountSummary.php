<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;

class GetAccountSummary{
    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort
    ) {}

    public function execute(int $accountId, int $branchId): array{
        if($accountId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La cuenta o la sucursal no es válida.');

        $account = $this->accountRepository->findById($accountId);
        if($account === null)
            throw new InvalidArgumentException('La cuenta no existe.');

        $occupation = $this->occupationRepository->findById($account->getOccupationId());
        if($occupation === null || $occupation->getSucursalId() !== $branchId)
            throw new InvalidArgumentException('La cuenta no existe en esta sucursal.');

        if($occupation->getStatus() !== 'en_uso' || $account->getStatus() !== 'abierta')
            throw new InvalidArgumentException('La cuenta no está activa.');

        $orderId = $account->getInvoiceId();
        $summary = $orderId === null ? null : $this->openOrderPort->getSummary($orderId, $branchId);

        $order = $summary['orden'] ?? [];
        return [
            'accountId' => $account->getId(),
            'occupationId' => $account->getOccupationId(),
            'name' => $account->getName(),
            'orderId' => $orderId,
            'consumos' => $summary['consumos'] ?? [],
            'totales' => [
                'totalunidades' => $order['totalunidades'] ?? '0.00',
                'subtotal' => $order['subtotal'] ?? '0.00',
                'base' => $order['base'] ?? '0.000',
                'valorimpuestototal' => $order['valorimpuestototal'] ?? '0.000',
                'descuento' => $order['descuento'] ?? '0.00',
                'total' => $order['total'] ?? '0.00'
            ]
        ];
    }
}