<?php

namespace App\Modules\Restaurant\Application\UseCases;

use DateTimeImmutable;
use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;

class FindAvailableTables{

    public function __construct(private RestaurantTableRepository $tableRepository)
        {}

    public function execute(string $startDate, string $endDate, int $numberOfPeople): array {
        if($numberOfPeople < 1)throw new InvalidArgumentException('El número de personas debe ser mayor a cero.');

        $start = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $startDate);
        $end = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $endDate);

        if (!$start || !$end)throw new InvalidArgumentException('Las fechas de la reserva no son válidas.');
        
        if($end <= $start)throw new InvalidArgumentException('La hora de salida debe ser posterior a la hora de entrada.');
        
        return $this->tableRepository->findAvailableForPeriod($startDate, $endDate, $numberOfPeople);
    }
    
}