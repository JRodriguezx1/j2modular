<?php

namespace App\Modules\Restaurant\Domain\Entities;

class Occupation{
    public function __construct(
        private int $id,
        private ?int $reservationId,
        private ?int $clientId,
        private string $type,
        private string $startDate,
        private ?string $estimatedEndDate,
        private ?string $actualEndDate,
        private string $status,
        private ?string $observations
    ) {
    }

    public function getId(): int{
        return $this->id;
    }

    public function getReservationId(): ?int{
        return $this->reservationId;
    }

    public function getClientId(): ?int{
        return $this->clientId;
    }

    public function getType(): string{
        return $this->type;
    }

    public function getStartDate(): string{
        return $this->startDate;
    }

    public function getEstimatedEndDate(): ?string{
        return $this->estimatedEndDate;
    }

    public function getActualEndDate(): ?string{
        return $this->actualEndDate;
    }

    public function getStatus(): string{
        return $this->status;
    }

    public function getObservations(): ?string{
        return $this->observations;
    }
    
}