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
}