<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;
use Throwable;

class AddProductsToAccount
{
    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ) {}

    /**
     * Guarda los productos pendientes en la factura de una cuenta.
     *
     * @param array<int, array{productId: int, quantity: string}> $items
     * @return array{
     *     accountId: int,
     *     orderId: int,
     *     lineIds: array<int, int>,
     *     totales: array
     * }
     */
    public function execute(int $accountId, array $items, int $branchId, int $sellerId): array{
        // Validaciones básicas antes de abrir la transacción.
        if($accountId <= 0 || $branchId <= 0 || $sellerId <= 0)
            throw new InvalidArgumentException('La cuenta, la sucursal o el vendedor no es válido.');

        if($items === [] || !array_is_list($items)) 
            throw new InvalidArgumentException('El pedido debe contener una lista de productos.');

        // Todas las escrituras deben usar esta misma transacción
        // y la conexión compartida de la aplicación.
        $this->transactionManager->begin();

        try {
            // Bloqueamos la cuenta mientras guardamos.
            // Así, dos solicitudes que sigan este flujo no crearán
            // facturas diferentes para la misma cuenta.
            $account = $this->accountRepository->findByIdForUpdate($accountId);

            if($account === null)
                throw new InvalidArgumentException('La cuenta no existe.');

            if($account->getStatus() !== 'abierta')
                throw new InvalidArgumentException( 'La cuenta no está abierta.');

            // La ocupación permite comprobar que la atención
            // siga en uso y pertenezca a la sucursal actual.
            $occupation = $this->occupationRepository->findById($account->getOccupationId());

            if($occupation === null || $occupation->getStatus() !== 'en_uso')
                throw new InvalidArgumentException('La atención de esta cuenta no está en uso.');

            if($occupation->getSucursalId() !== $branchId)throw new InvalidArgumentException('La cuenta no pertenece a la sucursal actual.');

            $orderId = $account->getInvoiceId();

            // Creamos la factura Abierta solamente cuando la cuenta
            // todavía no tiene una vinculada.
            if($orderId === null){
                $orderId = $this->openOrderPort->create($branchId, $occupation->getClientId(), $sellerId);
                // Guarda el vínculo en cuentas.idfactura.
                $this->accountRepository->linkOpenOrder($accountId, $orderId);
            }

            // POS valida los productos y sus cantidades.
            // También obtiene precios e impuestos del servidor,
            // inserta las líneas y recalcula la factura.
            $result = $this->openOrderPort->addSimpleProducts($orderId, $items, $branchId);

            // Confirmamos únicamente cuando todo el lote se guardó.
            $this->transactionManager->commit();

            return [
                'accountId' => $accountId,
                'orderId' => $orderId,
                'lineIds' => $result['lineIds'],
                'totales' => $result['totales'],
            ];
        } catch (Throwable $e) {
            // Si algo falla, deshacemos las líneas insertadas y,
            // si se crearon aquí, la factura y su vínculo con la cuenta.
            $this->transactionManager->rollback();
            // El controlador decidirá cómo responder al frontend.
            throw $e;
        }
        
    }
}