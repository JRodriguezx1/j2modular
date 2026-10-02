<?php

namespace App\Modules\Restaurant\Domain\Entities;

class RestaurantTable{

    public function __construct(
        private int $id,
        private int $resourceId,
        private string $name,
        private int $capacity,
        private bool $active,
        private string $resourceStatus,
        private ?int $zoneId = null,
        private ?string $shape = null
    ) {
    }

    public function getId(): int{
        return $this->id;
    }

    public function getResourceId(): int{
        return $this->resourceId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getCapacity(): int{
        return $this->capacity;
    }

    public function isActive(): bool{
        return $this->active;
    }

    public function getResourceStatus(): string{
        return $this->resourceStatus;
    }

    public function getZoneId(): ?int{
        return $this->zoneId;
    }

    public function getShape(): ?string{
        return $this->shape;
    }

}