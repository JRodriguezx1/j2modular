<?php

use App\Modules\Restaurant\Interfaces\Http\Controllers\RestaurantController;


$router->get('/restaurant/tables', [RestaurantController::class, 'index']);
$router->get('/restaurant/reservations', [RestaurantController::class, 'reservations']);

///////// API /////////
$router->post('/restaurant/api/reservations/available-tables', [RestaurantController::class, 'availableTables']);
$router->get('/restaurant/api/customers/search', [RestaurantController::class, 'searchCustomers']);