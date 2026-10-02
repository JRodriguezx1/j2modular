<?php

namespace App\Modules\Restaurant\Application\UseCases;

use DateTimeImmutable;
use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class ListReservationsByDate{
    public function __construct(private ReservationRepository $reservationRepository)
    {}


    public function execute(string $date): array {

        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if(!$parsedDate || $parsedDate->format('Y-m-d') !== $date){
            throw new InvalidArgumentException('La fecha de reservas no es válida.');
        }

        return $this->reservationRepository->findByDate($date);
    }
    
}