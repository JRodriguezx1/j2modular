<?php

use App\Modules\Restaurant\Interfaces\Http\Controllers\RestaurantController;
use App\Modules\Restaurant\Interfaces\Http\Controllers\OccupationAccountController;


$router->get('/restaurant/tables', [RestaurantController::class, 'index']);
$router->get('/restaurant/reservations', [RestaurantController::class, 'reservations']);

///////// API /////////
$router->post('/restaurant/api/reservations/available-tables', [RestaurantController::class, 'availableTables']); //buscar mesas
$router->get('/restaurant/api/customers/search', [RestaurantController::class, 'searchCustomers']); //buscar cliente
$router->post('/restaurant/api/reservations', [RestaurantController::class, 'createReservation']); //crear reservacion
$router->get('/restaurant/api/reservations/detail', [RestaurantController::class, 'getReservation']); //obtener detalle de una reservacion
$router->post('/restaurant/api/reservations/confirm', [RestaurantController::class, 'confirmReservation']); //confirmar reservacion
$router->post('/restaurant/api/reservations/cancel', [RestaurantController::class, 'cancelReservation']); //cancelar reservacion
$router->post('/restaurant/api/reservations/start-occupation', [RestaurantController::class, 'startReservationOccupation']); //iniciar ocupacion de reservacion
$router->get('/restaurant/api/occupations/active', [RestaurantController::class, 'getActiveOccupation']);
$router->get('/restaurant/api/occupations/accounts', [OccupationAccountController::class, 'index']);
$router->post('/restaurant/api/accounts/products', [OccupationAccountController::class, 'addProduct']); //añadir producto
$router->get('/restaurant/api/accounts/summary', [OccupationAccountController::class, 'summary']);