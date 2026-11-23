<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Application\Services\TableStateService;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

class PayAccount{
    
    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager,
        private RestaurantTableRepository $tableRepository,
        private TableStateService $tableStateService
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

            $result = $this->openOrderPort->pay($orderId, $branchId, $userId, $paymentData);  //devuelve estas llaves: exito, idfactura y dataInvoice
            // POS procesó el cobro, pero todavía no hay commit.
            $this->accountRepository->close($accountId);
            $finishedAt = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
            $this->occupationRepository->finish($occupation->getId(), $finishedAt->format('Y-m-d H:i:s'));
            // La ocupación ya está finalizada dentro de esta transacción. Ahora calculamos el estado de sus mesas.
            $result['tables'] = $this->getTableStates($occupation->getId(), $branchId);

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


    private function getTableStates(int $occupationId, int $branchId): array{
        // Consultar solamente las mesas de esta atención.
        $tables = $this->tableRepository->findByOccupationId($occupationId, $branchId);
        // Aplicar las mismas reglas utilizadas por ListTables.
        $states = $this->tableStateService->resolve($tables);
        $result = [];
        foreach($states as $item){
            $table = $item['table'];
            $occupation = $item['occupation'];
            $reservation = $item['reservation'];
            // Convertir las entidades en datos simples para el JSON.
            $result[] = [
                'resourceId' => $table->getResourceId(),
                'active' => $table->isActive(),
                'operationalStatus' => $item['operationalStatus'],
                'occupationId' => $occupation?->getId(),
                'occupiedSince' => $occupation?->getStartDate(),
                'reservationId' => $reservation?->getId(),
                'reservationStart' => $reservation?->getStartDate(),
            ];
        }

        return $result;
    }


}
