<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Application\Services\TableStateService;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;

class ListTables{

    private RestaurantTableRepository $RestaurantTableRepository;
    private TableStateService $tableStateService;

    public function __construct(RestaurantTableRepository $RestaurantTableRepository, TableStateService $tableStateService){
        $this->RestaurantTableRepository = $RestaurantTableRepository;
        $this->tableStateService = $tableStateService;
    }

    public function execute(): array{
        $tables = $this->RestaurantTableRepository->findAll();
        // El cálculo del estado ahora está en el servicio compartido.
        return $this->tableStateService->resolve($tables);
    }
    
}