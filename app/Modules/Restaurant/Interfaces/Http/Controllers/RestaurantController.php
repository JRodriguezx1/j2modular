<?php

namespace App\Modules\Restaurant\Interfaces\Http\Controllers;

use App\Modules\Restaurant\Application\UseCases\ListTables;
use App\Modules\Restaurant\Application\UseCases\ListZones;
use App\Modules\Restaurant\Application\UseCases\ListUpcomingReservations;
use App\Modules\Restaurant\Application\UseCases\ListReservationsByDate;
use App\Modules\Restaurant\Application\UseCases\FindAvailableTables;
use App\Modules\Restaurant\Application\UseCases\SearchCustomers;
use App\Modules\Restaurant\Application\UseCases\CreateReservation;
use App\Modules\Restaurant\Application\UseCases\GetReservation;
use App\Modules\Restaurant\Application\UseCases\ConfirmReservation;
use App\Modules\Restaurant\Application\UseCases\CancelReservation;
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
        private CreateReservation $createReservation,
        private GetReservation $getReservation,
        private ConfirmReservation $confirmReservation,
        private CancelReservation $cancelReservation,
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


    //////////////////        API        ///////////////////
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
                }, $tables
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
                                }, $customers
                            );

            http_response_code(200);
            echo json_encode(['success' => true, 'customers' => $customersView], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }catch(\Throwable $e){
            error_log( $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible buscar los clientes.'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function createReservation(): void{
        header('Content-Type: application/json; charset=utf-8');
        try {
            $clientId = (int) ($_POST['clientId'] ?? 0);
            $numberOfPeople = (int) ($_POST['numberOfPeople'] ?? 0);
            $startDate = trim( $_POST['startDate'] ?? '');
            $endDate = trim($_POST['endDate'] ?? '');
            $observations = trim($_POST['observations'] ?? '');
            /*
            * La mesa es opcional.
            */
            $resourceId = isset($_POST['resourceId']) && $_POST['resourceId'] !== '' ? (int) $_POST['resourceId'] : null;

            $reservationId = $this->createReservation->execute(
                                clientId: $clientId,
                                numberOfPeople: $numberOfPeople,
                                startDate: $startDate,
                                endDate: $endDate,
                                resourceId: $resourceId,
                                observations: $observations !== '' ? $observations : null
                            );

            http_response_code(201);
            echo json_encode(
                [
                    'success' => true,
                    'reservationId' => $reservationId,
                    'message' => 'Reserva creada correctamente.'
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }catch(\InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(\Throwable $e){
            error_log(sprintf('CreateReservation: %s in %s:%d', $e->getMessage(), $e->getFile(), $e->getLine()));
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible crear la reserva.'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function getReservation():void{
        header('Content-Type: application/json; charset=utf-8');
        try {
            $reservationId = (int) ($_GET['id'] ?? 0);
            $reservation = $this->getReservation->execute($reservationId);
            http_response_code(200);

            echo json_encode(
                [
                    'success' => true,
                    'reservation' => [
                        'id' => $reservation->getId(),
                        'clientId' => $reservation->getClientId(),
                        'clientName' => $reservation->getClientName(),
                        'numberOfPeople' => $reservation->getNumberOfPeople(),
                        'startDate' => $reservation->getStartDate(),
                        'endDate' => $reservation->getEndDate(),
                        'status' => $reservation->getStatus(),
                        'observations' => $reservation->getObservations(),
                        'resources' => $reservation->getResources()
                    ]
                ],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }catch(\InvalidArgumentException $e){
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(\Throwable $e){
            error_log(
                sprintf(
                    'GetReservation: %s in %s:%d',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar la reserva.'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function confirmReservation(): void{
        header('Content-Type: application/json; charset=utf-8');
        try{
            $reservationId = (int)($_POST['reservationId'] ?? 0);
            $this->confirmReservation->execute($reservationId);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Reserva confirmada correctamente.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }catch(\InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(\Throwable $e){
            error_log(sprintf('ConfirmReservation: %s in %s:%d',$e->getMessage(), $e->getFile(),  $e->getLine()));
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible confirmar la reserva.'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function cancelReservation(): void{
        header('Content-Type: application/json; charset=utf-8');
        try{
            $reservationId = (int)($_POST['reservationId'] ?? 0);
            $this->cancelReservation->execute($reservationId);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Reserva cancelada correctamente.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }catch(\InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(\Throwable $e){
            error_log(sprintf('CancelReservation: %s in %s:%d',$e->getMessage(), $e->getFile(),  $e->getLine()));
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible cancelar la reserva.'], JSON_UNESCAPED_UNICODE);
        }
    }
    
}