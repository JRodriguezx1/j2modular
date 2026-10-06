<?php

namespace App\Modules\Restaurant\Application\UseCases;

use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

use App\Core\Database\TransactionManager;

use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;


class StartReservationOccupation
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private OccupationRepository $occupationRepository,
        private RestaurantTableRepository $tableRepository,
        private TransactionManager $transactionManager
    ) {
    }


    public function execute(
        int $reservationId
    ): int {

        if ($reservationId <= 0) {
            throw new InvalidArgumentException(
                'La reserva indicada no es válida.'
            );
        }


        /*
         * Podemos consultar inicialmente fuera de la
         * transacción para hacer las validaciones básicas.
         */
        $reservation =
            $this->reservationRepository
                ->findById($reservationId);


        if (!$reservation) {
            throw new InvalidArgumentException(
                'La reserva no existe.'
            );
        }


        if ($reservation->getStatus() !== 'confirmada') {
            throw new InvalidArgumentException(
                'Solo una reserva confirmada puede iniciar atención.'
            );
        }


        $resources =
            $reservation->getResources();


        if (empty($resources)) {
            throw new InvalidArgumentException(
                'La reserva debe tener una mesa asignada antes de iniciar atención.'
            );
        }


        $this->transactionManager->begin();


        try {

            /*
             * Bloqueamos primero todos los recursos.
             */
            foreach ($resources as $resource) {

                $resourceId =
                    (int) $resource['id'];

                $this->tableRepository
                    ->lockResource(
                        $resourceId
                    );
            }


            /*
             * IMPORTANTE:
             *
             * Una vez obtenidos los locks volvemos
             * a consultar la reserva.
             */
            $reservation =
                $this->reservationRepository
                    ->findById(
                        $reservationId
                    );


            if (!$reservation) {
                throw new InvalidArgumentException(
                    'La reserva no existe.'
                );
            }


            if (
                $reservation->getStatus()
                !== 'confirmada'
            ) {
                throw new InvalidArgumentException(
                    'La reserva ya no puede iniciar atención.'
                );
            }


            $resources =
                $reservation->getResources();


            if (empty($resources)) {
                throw new InvalidArgumentException(
                    'La reserva ya no tiene una mesa asignada.'
                );
            }


            /*
             * Comprobar que ninguno de los recursos
             * tenga una ocupación activa.
             */
            foreach ($resources as $resource) {

                $resourceId =
                    (int) $resource['id'];


                $currentOccupation =
                    $this->occupationRepository
                        ->findCurrentByResourceId(
                            $resourceId
                        );


                if ($currentOccupation !== null) {

                    throw new InvalidArgumentException(
                        sprintf(
                            'La mesa %s ya se encuentra ocupada.',
                            $resource['name']
                                ?? $resourceId
                        )
                    );
                }
            }


            $now =
                new DateTimeImmutable();


            /*
             * Para una reserva conocemos su horario
             * previsto de finalización.
             */
            $estimatedEndDate =
                $reservation->getEndDate();


            $occupationId =
                $this->occupationRepository
                    ->create(
                        reservationId:
                            $reservation->getId(),

                        clientId:
                            $reservation->getClientId(),

                        type:
                            'reserva',

                        startDate:
                            $now->format(
                                'Y-m-d H:i:s'
                            ),

                        estimatedEndDate:
                            $estimatedEndDate,

                        observations:
                            $reservation->getObservations()
                    );


            /*
             * Vincular recursos a la ocupación.
             */
            foreach ($resources as $resource) {

                $this->occupationRepository
                    ->attachResource(
                        occupationId:
                            $occupationId,

                        resourceId:
                            (int) $resource['id'],

                        baseValue:
                            0
                    );
            }


            /*
             * La reserva ya fue atendida.
             */
            $this->reservationRepository
                ->updateStatus(
                    $reservationId,
                    'atendida'
                );


            $this->transactionManager
                ->commit();


            return $occupationId;


        } catch (Throwable $e) {

            $this->transactionManager
                ->rollback();

            throw $e;
        }
    }
}