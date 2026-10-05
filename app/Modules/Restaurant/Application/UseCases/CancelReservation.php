<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class CancelReservation{

    public function __construct(private ReservationRepository $reservationRepository)
    {}

    public function execute(int $reservationId): void {
        if($reservationId <= 0)
            throw new InvalidArgumentException('La reserva indicada no es válida.');
        $reservation = $this->reservationRepository->findById($reservationId);
        if(!$reservation)
            throw new InvalidArgumentException('La reserva no existe.');
        $status = $reservation->getStatus();
        if(!in_array($status, ['pendiente', 'confirmada'], true))
            throw new InvalidArgumentException('Esta reserva ya no puede ser cancelada.');
        $this->reservationRepository->updateStatus($reservationId, 'cancelada');
    }

}