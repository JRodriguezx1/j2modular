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
$router->get('/restaurant/api/occupations/accounts', [OccupationAccountController::class, 'index']);  //llamada desdes occupationApi.ts, consultar las cuentas asociadas a la ocupacion
$router->post('/restaurant/api/accounts/products', [OccupationAccountController::class, 'addProduct']); //añadir producto
$router->post('/restaurant/api/tables/crearOrdenComanda', [OccupationAccountController::class, 'crearOrdenComanda']); //primer pedido de una mesa libre
$router->post('/restaurant/api/accounts/products/addProducts', [OccupationAccountController::class, 'addProducts']); // Agregar un lote de productos a una cuenta existente.
$router->get('/restaurant/api/accounts/summary', [OccupationAccountController::class, 'summary']);