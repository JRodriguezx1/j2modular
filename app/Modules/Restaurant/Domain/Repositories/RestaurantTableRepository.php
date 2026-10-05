<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\RestaurantTable;

interface RestaurantTableRepository{
    /**
     * @return RestaurantTable[]
     */
    public function findAll(): array;
    /**
    * @return RestaurantTable[]
    */
    //findAvailableForPeriod Responde: ¿Qué mesas puedo ofrecer para este período y esta cantidad de personas?
    public function findAvailableForPeriod(string $startDate, string $endDate, int $capacity): array;
    //isAvailableForPeriod Responde algo diferente: ¿Esta mesa específica sigue disponible?
    public function isAvailableForPeriod(int $resourceId, string $startDate, string $endDate, int $capacity): bool;
    public function lockResource(int $resourceId): void;
}