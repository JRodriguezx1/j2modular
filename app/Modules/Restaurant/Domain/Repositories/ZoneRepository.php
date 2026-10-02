<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Zonas;

interface ZoneRepository{
    /**
     * @return Zonas[]
     */
    public function findAll(): array;
}