<?php

namespace App\Modules\Restaurant\Interfaces\Http\Controllers;

use App\Modules\Restaurant\Application\UseCases\ListTables;
use App\Modules\Restaurant\Application\UseCases\ListZones;
use App\Modules\Restaurant\Application\UseCases\ListUpcomingReservations;
use App\Modules\Restaurant\Application\UseCases\ListReservationsByDate;
use App\Modules\Restaurant\Application\UseCases\FindAvailableTables;
use App\Modules\Restaurant\Application\UseCases\SearchCustomers;
use App\Core\Routing\Router;

class RestaurantController
{
    public function __construct(
        private ListTables $listTables, 
        private ListZones $listZones, 
        private ListUpcomingReservations $listUpcomingReservations, 
        private ListReservationsByDate $listReservationsByDate,
        private FindAvailableTables $findAvailableTables,
        private SearchCustomers $searchCustomers,
        private Router $router
    ){}

    public function index(): void{
        $tables = $this->listTables->execute();
        $zones = $this->listZones->execute();
        $upcomingReservations = $this->listUpcomingReservations->execute(5);


        $tablesView = array_map(
            function ($item) {
                $table = $item['table'];
                $occupation = $item['occupation'];
                $reservation = $item['reservation'];
                return [
                    'id' => $table->getId(),
                    'resourceId' => $table->getResourceId(),
                    'name' => $table->getName(),
                    'capacity' => $table->getCapacity(),
                    'active' => $table->isActive(),
                    'resourceStatus' => $table->getResourceStatus(),
                    'operationalStatus' => $item['operationalStatus'],
                    'zoneId' => $table->getZoneId(),
                    'shape' => $table->getShape(),
                    'occupationId' => $occupation?->getId(),
                    'occupiedSince' => $occupation?->getStartDate(),
                    'reservationId' => $reservation?->getId(),
                    'reservationStart' =>  $reservation?->getStartDate(),
                    'reservationEnd' => $reservation?->getEndDate(),
                    'reservationClientName' => $reservation?->getClientName(),
                    'reservationPeople' => $reservation?->getNumberOfPeople()
                ];
            }, $tables
        );

        $zonesView = array_map(
            fn($zone) => [
                'id' => $zone->getId(),
                'nombre' => $zone->getName(),
                'activo' => $zone->isActive(),
            ],
            $zones
        );


        $reservationsView = array_map(
            function ($reservation) {
                return [
                    'id' =>  $reservation->getId(),
                    'clientName' => $reservation->getClientName(),
                    'numberOfPeople' => $reservation->getNumberOfPeople(),
                    'startDate' => $reservation->getStartDate(),
                    'endDate' => $reservation->getEndDate(),
                    'status' => $reservation->getStatus(),
                    'observations' => $reservation->getObservations(),
                    'resources' => $reservation->getResources()
                ];
            },
            $upcomingReservations['reservations']
        );

        $this->router->render('restaurant/tables/index', ['page' => 'tables', 'titulo' => 'Restaurante', 'tables' => $tablesView, 'zones' => $zonesView, 'reservations' => $reservationsView, 'totalReservations' => $upcomingReservations['total']], 'restaurant');

        // Temporalmente:
        //debuguear(['tables' => $tablesView, 'zones' => $zonesView]);

    }


    public function reservations(): void{
        /*
        * Primera versión:
        *
        * /restaurant/reservations
        *      ↓
        * hoy
        *
        * /restaurant/reservations?date=2026-09-25
        *      ↓
        * fecha seleccionada
        */
        $date = $_GET['date'] ?? date('Y-m-d');
        try {
            $reservations = $this->listReservationsByDate->execute($date);
        } catch (\InvalidArgumentException $e) {
            $date = date('Y-m-d');
            $reservations = $this->listReservationsByDate->execute($date);
        }

        $reservationsView = array_map(
            function ($reservation) {
                return [
                    'id' => $reservation->getId(),
                    'clientId' => $reservation->getClientId(),
                    'clientName' => $reservation->getClientName(),
                    'numberOfPeople' => $reservation->getNumberOfPeople(),
                    'startDate' => $reservation->getStartDate(),
                    'endDate' => $reservation->getEndDate(),
                    'status' => $reservation->getStatus(),
                    'observations' => $reservation->getObservations(),
                    'resources' => $reservation->getResources()
                ];
            },
            $reservations
        );

        $zones = $this->listZones->execute();
        $zonesView = array_map( function ($zone){
                return [
                    'id' => $zone->getId(),
                    'name' => $zone->getName(),
                    'active' => $zone->isActive()
                ];
            },
            $zones
        );

        $this->router->render('restaurant/reservations/index', ['page' => 'reservations', 'titulo' => 'Reservas', 'selectedDate' => $date,'reservations' => $reservationsView, 'zones' => $zonesView], 'restaurant');
    }


    public function availableTables(): void{
        header('Content-Type: application/json; charset=utf-8');
        try{
            $startDate = trim($_POST['startDate'] ?? '');
            $endDate = trim($_POST['endDate'] ?? '');
            $numberOfPeople = (int)($_POST['numberOfPeople'] ?? 0);

            $tables = $this->findAvailableTables->execute($startDate, $endDate, $numberOfPeople);

            $tablesView = array_map(
                function ($table) {
                    return [
                        'id' => $table->getId(),
                        'resourceId' => $table->getResourceId(),
                        'name' => $table->getName(),
                        'capacity' => $table->getCapacity(),
                        'zoneId' => $table->getZoneId(),
                        'shape' => $table->getShape()
                    ];
                },
                $tables
            );

            http_response_code(200);
            echo json_encode(['success' => true, 'tables' => $tablesView], JSON_UNESCAPED_UNICODE);
        }catch(\InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch (\Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar las mesas disponibles. '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }


    public function searchCustomers(): void{
        header('Content-Type: application/json; charset=utf-8');

        try {
            $term = trim($_GET['q'] ?? '');
            $customers = $this->searchCustomers->execute($term, 10);

            $customersView = array_map(
                                function($customer){
                                    return [
                                        'id' => $customer->getId(),
                                        'fullName' => $customer->getFullName(),
                                        'identification' => $customer->getIdentification(),
                                        'phone' => $customer->getPhone(),
                                        'email' => $customer->getEmail()
                                    ];
                                },
                                $customers
                            );

            http_response_code(200);
            echo json_encode(['success' => true, 'customers' => $customersView], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }catch(\Throwable $e){
            error_log( $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible buscar los clientes.'], JSON_UNESCAPED_UNICODE);
        }
    }
    
}