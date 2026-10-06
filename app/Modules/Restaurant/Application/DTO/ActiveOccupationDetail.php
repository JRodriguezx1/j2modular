<?php

namespace App\Modules\Restaurant\Application\DTO;

class ActiveOccupationDetail{

    public function __construct(
        public readonly int $occupationId,
        public readonly int $resourceId,
        public readonly ?int $reservationId,
        public readonly ?int $clientId,
        public readonly ?string $clientName,
        public readonly string $type,
        public readonly string $startDate,
        public readonly ?string $estimatedEndDate,
        public readonly string $status,
        public readonly ?int $numberOfPeople,
        public readonly ?string $observations
    ){}
    
}