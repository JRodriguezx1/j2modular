<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;
use Throwable;

class UpdateAccountCustomer{

    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ){}

    /**
     * Actualiza el cliente de la ocupación y de su factura.
     *
     * clientId:
     * - número positivo: asignar cliente.
     * - null: retirar cliente.
     *
     * @return array{ accountId: int, occupationId: int, orderId: int, clientId: int|null }
     */
    public function execute(int $accountId, int $branchId, ?int $clientId): array{
        // Validar los datos antes de iniciar la transacción.
        if($accountId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La cuenta o la sucursal no es válida.');

        if($clientId !== null && $clientId <= 0)
            throw new InvalidArgumentException('El cliente no es válido.');

        $this->transactionManager->begin();

        try {
            // Mismo orden de bloqueo que en AddProductsToAccount: primero la cuenta, después la ocupación.
            $account = $this->accountRepository->findByIdForUpdate($accountId);
            if($account === null)
                throw new InvalidArgumentException('La cuenta no existe.');
            
            if ($account->getStatus() !== 'abierta')
                throw new InvalidArgumentException('La cuenta no está abierta.');

            // El bloqueo impide que otra transacción cambie
            // la ocupación mientras actualizamos el cliente.
            $occupation = $this->occupationRepository->findByIdForUpdate($account->getOccupationId());
            if($occupation === null || $occupation->getStatus() !== 'en_uso')
                throw new InvalidArgumentException('La atención de esta cuenta no está en uso.');
            
            if($occupation->getSucursalId() !== $branchId)
                throw new InvalidArgumentException('La cuenta no pertenece a la sucursal actual.');
            
            $orderId = $account->getInvoiceId();

            // Esta operación requiere una factura ya vinculada.
            if($orderId === null)
                throw new InvalidArgumentException('Guarda el primer pedido antes de actualizar el cliente.');

            // POS bloquea la factura, comprueba que esté Abierta
            // y valida que el cliente exista cuando no es null.
            $this->openOrderPort->updateCustomer($orderId, $branchId, $clientId);
            // Guardamos exactamente el mismo cliente en la ocupación.
            $this->occupationRepository->updateCustomer($occupation->getId(), $clientId);
            // Ambas actualizaciones se confirman juntas.
            $this->transactionManager->commit();

            return [
                'accountId' => $accountId,
                'occupationId' => $occupation->getId(),
                'orderId' => $orderId,
                'clientId' => $clientId,
            ];
        }catch(Throwable $e){
            // Si falla cualquiera de las operaciones,
            // deshacemos los cambios y liberamos los bloqueos.
            $this->transactionManager->rollback();
            throw $e;
        }
    }
    
}