<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;
use Throwable;

class AddProductToAccount
{
    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ) {}

    public function execute(int $accountId, int $productId, string $quantity, int $branchId, int $sellerId): array{
        if($accountId <= 0 || $productId <= 0 || $branchId <= 0 || $sellerId <= 0)
            throw new InvalidArgumentException('Los datos para agregar el producto no son válidos.');

        $this->transactionManager->begin();
        try {
            // El bloqueo impide que dos solicitudes creen facturas
            // distintas para la misma cuenta.
            $account = $this->accountRepository->findByIdForUpdate($accountId);

            if($account === null)
                throw new InvalidArgumentException('La cuenta no existe.');

            if($account->getStatus() !== 'abierta')
                throw new InvalidArgumentException('La cuenta no está abierta.');

            $occupation = $this->occupationRepository->findById($account->getOccupationId());

            if($occupation === null || $occupation->getStatus() !== 'en_uso')
                throw new InvalidArgumentException('La atención de esta cuenta no está en uso.');

            if($occupation->getSucursalId() !== $branchId)
                throw new InvalidArgumentException('La cuenta no pertenece a la sucursal actual.');

            $orderId = $account->getInvoiceId();

            if($orderId === null){
                $orderId = $this->openOrderPort->create($branchId,  $occupation->getClientId(),  $sellerId);
                $this->accountRepository->linkOpenOrder($accountId, $orderId);
            }

            // POS valida el producto y la cantidad, inserta la línea
            // y recalcula los totales de la factura Abierta.
            $result = $this->openOrderPort->addSimpleProduct($orderId, $productId, $quantity, $branchId);
            $this->transactionManager->commit();

            return ['accountId'=>$accountId, 'orderId'=>$orderId, 'lineId'=>$result['lineId'], 'totales'=>$result['totales']];
        }catch(Throwable $e){
            $this->transactionManager->rollback();
            throw $e;
        }
    }
    
}