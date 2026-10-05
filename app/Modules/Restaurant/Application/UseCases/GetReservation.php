<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;
use App\Modules\Restaurant\Domain\Entities\Reservation;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;


class GetReservation{

    public function __construct(private ReservationRepository $reservationRepository)
    {}

    public function execute(int $reservationId): Reservation {
        if($reservationId <= 0)
            throw new InvalidArgumentException('La reserva indicada no es válida.');
        $reservation = $this->reservationRepository->findById($reservationId);
        if(!$reservation)throw new InvalidArgumentException('La reserva no existe.');
        return $reservation;
    }
    
}