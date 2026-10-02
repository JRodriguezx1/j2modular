<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class ListUpcomingReservations{

    public function __construct(private ReservationRepository $reservationRepository)
    {}

    public function execute(int $limit = 5): array{

        return [
            'reservations' => $this->reservationRepository->findUpcomingForToday($limit),
            'total' => $this->reservationRepository->countRelevantForToday()
        ];
    }

}