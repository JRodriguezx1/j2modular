<?php

namespace App\Modules\Restaurant\Domain\Entities;

class Reservation{

    public function __construct(
        private int $id,
        private int $clientId,
        private ?int $numberOfPeople,
        private string $startDate,
        private string $endDate,
        private string $status,
        private ?string $observations,
        private string $clientName,
        private array $resources = []
    ) {
    }

    public function getId(): int{
        return $this->id;
    }

    public function getClientId(): int{
        return $this->clientId;
    }

    public function getNumberOfPeople(): ?int{
        return $this->numberOfPeople;
    }

    public function getStartDate(): string{
        return $this->startDate;
    }

    public function getEndDate(): string{
        return $this->endDate;
    }

    public function getStatus(): string{
        return $this->status;
    }

    public function getObservations(): ?string{
        return $this->observations;
    }

    public function getClientName(): string{
        return $this->clientName;
    }

    public function getResources(): array{
        return $this->resources;
    }

}