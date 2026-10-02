<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Domain\Repositories\ZoneRepository;

class ListZones{
    
    public function __construct(private ZoneRepository $zonaRepository) {
    }

    public function execute(): array{
        return $this->zonaRepository->findAll();
    }
    
}