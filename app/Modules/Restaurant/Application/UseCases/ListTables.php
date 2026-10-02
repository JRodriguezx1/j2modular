<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class ListTables{
    private const RESERVATION_WINDOW_MINUTES = 30;

    private RestaurantTableRepository $RestaurantTableRepository;
    private OccupationRepository $occupationRepository;
    private ReservationRepository $reservationRepository;

    public function __construct(RestaurantTableRepository $RestaurantTableRepository, OccupationRepository $occupationRepository, ReservationRepository $reservationRepository){
        $this->RestaurantTableRepository = $RestaurantTableRepository;
        $this->occupationRepository = $occupationRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function execute(): array{
        $tables = $this->RestaurantTableRepository->findAll();


        if(empty($tables))return [];

        /*
        * Obtenemos los IDs de recurso de todas las mesas.
        */
        $resourceIds = array_map(fn($table) => $table->getResourceId(), $tables);
        /*
        * Una sola consulta para todas las ocupaciones.
        *
        * El resultado viene indexado por recurso_id.
        */
        $occupations = $this->occupationRepository->findCurrentByResourceIds($resourceIds);

        $reservations = $this->reservationRepository->findCurrentByResourceIds($resourceIds, self::RESERVATION_WINDOW_MINUTES);


        $result = [];

        foreach ($tables as $table) {
            $resourceId = $table->getResourceId();
            $occupation = $occupations[$resourceId] ?? null;
            $reservation = $reservations[$resourceId] ?? null;
            $operationalStatus = 'disponible';

            if ($table->getResourceStatus() !== 'disponible') {
                $operationalStatus = $table->getResourceStatus();
            /*
             * Si el recurso está habilitado, comprobamos
             * si actualmente está siendo utilizado.
             */
            }elseif($occupation !== null){
                $operationalStatus = 'ocupada';
            }elseif($reservation !== null){
                $operationalStatus = 'reservada';
            }

            $result[] = [
                'table' => $table,
                'occupation' => $occupation,
                'reservation' => $reservation,
                'operationalStatus' => $operationalStatus
            ];
        }
        return $result;
    }
    
}