<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

class SaveFirstTableOrder
{
    // Misma anticipación utilizada actualmente por ListTables.
    private const RESERVATION_WINDOW_MINUTES = 30;

    public function __construct(
        private RestaurantTableRepository $tableRepository,
        private OccupationRepository $occupationRepository,
        private ReservationRepository $reservationRepository,
        private AccountRepository $accountRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ){}

    /**
     * Guarda el primer pedido de una mesa sin ocupación.
     *
     * resourceId corresponde a recursos.id, no a mesas.id.
     *
     * @param array<int, array{productId: int, quantity: string}> $items
     */
    public function execute(int $resourceId, array $items, int $branchId, int $sellerId, ?int $customerId = null): array{
        if($resourceId <= 0 || $branchId <= 0 || $sellerId <= 0 || ($customerId !== null && $customerId <= 0))
            throw new InvalidArgumentException('Los datos del pedido no son válidos.');
        
        if($items === [] || !array_is_list($items))
            throw new InvalidArgumentException('El pedido debe contener una lista de productos.');

        // Una sola transacción para toda la operación.
        $this->transactionManager->begin();

        try {
            // Primero bloqueamos la mesa.
            $this->tableRepository->lockTableForOrder($resourceId, $branchId);
            $occupation = $this->occupationRepository->findCurrentByResourceId($resourceId);
            // La mesa pudo ocuparse mientras el usuario preparaba el carrito.
            if($occupation !== null)
                throw new InvalidArgumentException('La mesa ya tiene una atención activa. Actualiza la mesa antes de guardar.');

            //Respetamos y buscamos las reservas que ya afectan la disponibilidad de la mesa según la misma regla de la lista de mesas.
            $reservations = $this->reservationRepository->findCurrentByResourceIds([$resourceId], self::RESERVATION_WINDOW_MINUTES);

            if(isset($reservations[$resourceId]))
                throw new InvalidArgumentException('La mesa tiene una reserva vigente o próxima. Inicia la atención desde la reserva.');

            $now = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));

            // Atención directa, sin reserva.
            $occupationId = $this->occupationRepository->create(
                sucursalId: $branchId,
                reservationId: null,
                clientId: $customerId,
                type: 'restaurant',
                startDate: $now->format('Y-m-d H:i:s'),
                estimatedEndDate: null,
                observations: null
            );

            // Relacionamos la mesa con la nueva ocupación.
            $this->occupationRepository->attachResource($occupationId, $resourceId, 0);
            // POS crea la factura Abierta y guarda todos los productos
            // mediante OpenInvoiceService y ventasService. Estos servicios participan en esta misma transacción.
            $result = $this->openOrderPort->procesarNuevaAbierta($branchId, $customerId, $sellerId, $items);
            $invoiceId = $result['idfactura'];
            // La cuenta se crea al guardar el primer pedido.
            $accountId = $this->accountRepository->createMain($occupationId, $invoiceId);
            // El frontend necesitará estos identificadores para abandonar
            // el contexto temporal y trabajar con la cuenta creada.
            $response = [
                'resourceId' => $resourceId,
                'occupationId' => $occupationId,
                'accountId' => $accountId,
                'orderId' => $invoiceId,
                'lineIds' => $result['lineIds'],
                'totales' => $result['totales'],
            ];

            $this->transactionManager->commit();
            return $response;
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            throw $e;
        }
    }

}