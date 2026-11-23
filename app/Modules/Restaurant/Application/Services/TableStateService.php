<?php

namespace App\Modules\Restaurant\Application\Services;

use App\Modules\Restaurant\Domain\Entities\RestaurantTable;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class TableStateService{

    // Misma anticipación que ya utiliza el listado actual.
    private const RESERVATION_WINDOW_MINUTES = 30;

    public function __construct(private OccupationRepository $occupationRepository, private ReservationRepository $reservationRepository)
    {}

    /**
     * Calcula el estado únicamente de las mesas recibidas.
     *
     * @param RestaurantTable[] $tables
     */
    public function resolve(array $tables): array{
        if($tables === [])return [];
        $resourceIds = array_map(fn(RestaurantTable $table) => $table->getResourceId(), $tables);
        // Reutilizar las consultas existentes.
        $occupations = $this->occupationRepository->findCurrentByResourceIds($resourceIds);
        $reservations = $this->reservationRepository->findCurrentByResourceIds($resourceIds, self::RESERVATION_WINDOW_MINUTES);
        $result = [];
        foreach($tables as $table){
            $resourceId = $table->getResourceId();
            $occupation = $occupations[$resourceId] ?? null;
            $reservation = $reservations[$resourceId] ?? null;
            // Primero respetar el estado del recurso:
            // limpieza, mantenimiento, fuera_servicio, etc.
            $status = $table->getResourceStatus();

            if($status === 'disponible'){
                if($occupation !== null){
                    $status = 'ocupada';
                }elseif($reservation !== null){
                    $status = 'reservada';
                }
            }

            // Conservar el formato que ya utiliza ListTables.
            $result[] = ['table' => $table, 'occupation' => $occupation, 'reservation' => $reservation, 'operationalStatus' => $status];
        }

        return $result;
    }
}