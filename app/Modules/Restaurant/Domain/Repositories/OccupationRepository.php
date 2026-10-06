<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Occupation;

interface OccupationRepository{
    /**
     * Retorna la ocupacion actualmente en uso
     * para un recurso.
     */
    public function findCurrentByResourceId(int $resourceId): ?Occupation;
    public function findCurrentByResourceIds(array $resourceIds): array;
    public function create(?int $reservationId, ?int $clientId, string $type, string $startDate, ?string $estimatedEndDate, ?string $observations): int;
    public function attachResource(int $occupationId, int $resourceId, float $baseValue = 0): void;
}