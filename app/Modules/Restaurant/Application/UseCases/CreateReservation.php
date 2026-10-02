<?php

namespace App\Modules\Restaurant\Application\UseCases;

use DateTimeImmutable;
use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\CustomerRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;

class CreateReservation
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private ReservationRepository $reservationRepository,
        private RestaurantTableRepository $tableRepository
    ) {
    }


    public function execute(
        int $clientId,
        int $numberOfPeople,
        string $startDate,
        string $endDate,
        ?int $resourceId = null,
        ?string $observations = null
    ): int {

        if ($clientId <= 0) {
            throw new InvalidArgumentException(
                'Debes seleccionar un cliente.'
            );
        }


        if (
            !$this->customerRepository->exists(
                $clientId
            )
        ) {
            throw new InvalidArgumentException(
                'El cliente seleccionado no existe.'
            );
        }


        if ($numberOfPeople <= 0) {
            throw new InvalidArgumentException(
                'El número de personas debe ser mayor que cero.'
            );
        }


        $start =
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $startDate
            );

        $end =
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $endDate
            );


        if (!$start || !$end) {
            throw new InvalidArgumentException(
                'La fecha u hora de la reserva no es válida.'
            );
        }


        if ($end <= $start) {
            throw new InvalidArgumentException(
                'La hora de salida debe ser posterior a la hora de entrada.'
            );
        }


        if ($resourceId !== null) {

            $available =
                $this->tableRepository
                    ->isAvailableForPeriod(
                        $resourceId,
                        $startDate,
                        $endDate,
                        $numberOfPeople
                    );


            if (!$available) {
                throw new InvalidArgumentException(
                    'La mesa seleccionada ya no está disponible.'
                );
            }
        }


        return $this
            ->reservationRepository
            ->create(
                clientId: $clientId,
                numberOfPeople: $numberOfPeople,
                startDate: $startDate,
                endDate: $endDate,
                status: 'pendiente',
                observations: $observations
            );
    }
}