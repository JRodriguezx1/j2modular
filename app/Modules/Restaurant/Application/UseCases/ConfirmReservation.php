<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class ConfirmReservation{

    public function __construct(private ReservationRepository $reservationRepository)
    {}

    public function execute(int $reservationId): void{
        if($reservationId <= 0)
            throw new InvalidArgumentException('La reserva indicada no es válida.');
        $reservation = $this->reservationRepository->findById($reservationId);
        if(!$reservation)
            throw new InvalidArgumentException('La reserva no existe.');
        if($reservation->getStatus() !== 'pendiente')
            throw new InvalidArgumentException('Solo las reservas pendientes pueden ser confirmadas.');
        $this->reservationRepository->updateStatus($reservationId, 'confirmada');
    }
    
}