<?php

use App\Modules\Restaurant\Interfaces\Http\Controllers\RestaurantController;


$router->get('/restaurant/tables', [RestaurantController::class, 'index']);
$router->get('/restaurant/reservations', [RestaurantController::class, 'reservations']);

///////// API /////////
$router->post('/restaurant/api/reservations/available-tables', [RestaurantController::class, 'availableTables']); //buscar mesas
$router->get('/restaurant/api/customers/search', [RestaurantController::class, 'searchCustomers']); //buscar cliente
$router->post('/restaurant/api/reservations', [RestaurantController::class, 'createReservation']); //crear reservacion
$router->get('/restaurant/api/reservations/detail', [RestaurantController::class, 'getReservation']); //obtener detalle de una reservacion
$router->post('/restaurant/api/reservations/confirm', [RestaurantController::class, 'confirmReservation']); //confirmar reservacion
$router->post('/restaurant/api/reservations/cancel', [RestaurantController::class, 'cancelReservation']); //cancelar reservacion