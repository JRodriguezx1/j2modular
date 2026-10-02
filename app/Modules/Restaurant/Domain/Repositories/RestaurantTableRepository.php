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
    public function findAvailableForPeriod(string $startDate, string $endDate, int $capacity): array;
}