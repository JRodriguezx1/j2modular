<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;
use Throwable;

class ApplyAccountLineDiscount{

    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ){}

    
    public function execute(
        int $accountId,
        int $lineId,
        string $type,
        string $value,
        string $password,
        int $branchId
    ): array {
        if ($accountId <= 0 || $lineId <= 0 || $branchId <= 0) {
            throw new InvalidArgumentException('La cuenta, la línea o la sucursal no es válida.');
        }

        $this->transactionManager->begin();

        try {
            $account = $this->accountRepository->findByIdForUpdate($accountId);
            if ($account === null || $account->getStatus() !== 'abierta') {
                throw new InvalidArgumentException('La cuenta no está abierta.');
            }

            $occupation = $this->occupationRepository->findByIdForUpdate($account->getOccupationId());
            if ($occupation === null || $occupation->getStatus() !== 'en_uso' ||
                $occupation->getSucursalId() !== $branchId) {
                throw new InvalidArgumentException('La cuenta no está en uso en esta sucursal.');
            }

            // La factura se obtiene de la cuenta bloqueada.
            $orderId = $account->getInvoiceId();
            if ($orderId === null || $orderId <= 0) {
                throw new InvalidArgumentException('La cuenta todavía no tiene una factura.');
            }

            $result = $this->openOrderPort->applyLineDiscount(
                $orderId, $lineId, $branchId, $type, $value, $password
            );

            $this->transactionManager->commit();

            return [
                'accountId' => $accountId,
                'orderId' => $orderId,
                'lineId' => $result['lineId'],
                'totales' => $result['totales'],
            ];
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            throw $e;
        }
    }

}