<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

class PayAccount{
    
    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ) {}

    public function execute(int $accountId, int $branchId, int $userId, array $data): array{
        if($accountId <= 0 || $branchId <= 0 || $userId <= 0)
            throw new InvalidArgumentException('La cuenta, la sucursal o el usuario no es válido.');

        // Una sola transacción para cobrar y cerrar la atención.
        $this->transactionManager->begin();
        try{
            // Mismo orden de bloqueo que los demás casos de uso: cuenta → ocupación → factura.
            $account = $this->accountRepository->findByIdForUpdate($accountId);
            if($account === null || $account->getStatus() !== 'abierta')
                throw new InvalidArgumentException('La cuenta no existe o ya no está abierta.');

            $occupation = $this->occupationRepository->findByIdForUpdate($account->getOccupationId());
            if($occupation === null || $occupation->getStatus() !== 'en_uso' || $occupation->getSucursalId() !== $branchId)
                throw new InvalidArgumentException('La atención no está en uso en esta sucursal.');

            // La factura se obtiene de la cuenta bloqueada, no del navegador.
            $orderId = $account->getInvoiceId();
            if($orderId === null || $orderId <= 0)
                throw new InvalidArgumentException('La cuenta todavía no tiene una factura.');

            // POS valida caja, facturador, total y medios de pago.
            $paymentData = [
                'idcaja' => $data['idcaja'] ?? null,
                'idconsecutivo' => $data['idconsecutivo'] ?? null,
                'expectedTotal' => $data['expectedTotal'] ?? null,
                'mediosPago' => $data['mediosPago'] ?? null,
            ];

            $result = $this->openOrderPort->pay($orderId, $branchId, $userId, $paymentData);
            // POS procesó el cobro, pero todavía no hay commit.
            $this->accountRepository->close($accountId);
            $finishedAt = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
            $this->occupationRepository->finish($occupation->getId(), $finishedAt->format('Y-m-d H:i:s'));
            // Conservamos exito, idfactura y dataInvoice para el frontend.
            $result['accountId'] = $accountId;
            $result['occupationId'] = $occupation->getId();
            $this->transactionManager->commit();
            return $result;
        }catch(Throwable $e){
            // POS y Restaurant deben utilizar la misma conexión/transacción.
            $this->transactionManager->rollback();
            throw $e;
        }
    }
}
