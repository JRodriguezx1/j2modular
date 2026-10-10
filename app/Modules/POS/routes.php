<?php

use App\Modules\POS\Controllers\ventascontrolador;
use App\Modules\POS\Controllers\ModoRapidoController;

$router->get('/admin/ventas', [ventascontrolador::class, 'index']);
///// area de ventas-modorapido /////
$router->get('/admin/ventas/modorapido', [ModoRapidoController::class, 'index']);
////////////API////////////
$router->post('/admin/api/facturar', [ventascontrolador::class, 'facturar']);  //api llamada desde ventas.ts cuando se factura
$router->post('/admin/api/facturarCotizacion', [ventascontrolador::class, 'facturarCotizacion']);  //api llamada desde ordenresumen.ts cuando se factura una cotizacion guardada
$router->post('/admin/api/eliminarOrden', [ventascontrolador::class, 'eliminarOrden']);  //api llamada desde ordenresumen.ts cuando se se elimina orden ya sea cotizacion o factura paga
$router->get('/admin/api/getcotizacion_venta', [ventascontrolador::class, 'getcotizacion_venta']); //api llamada desde ventas.ts para traer la cotizacion y cargarla en el modulo de venta
$router->get('/admin/api/ventas/detalleProductoCompuesto', [ventascontrolador::class, 'detalleProductoCompuesto']);  //llamada desde ordenresumen.ts para el detalle del producto compuesto vendido
///// area de ventas-modorapido /////
$router->post('/admin/api/facturarModorapido', [ModoRapidoController::class, 'facturarModorapido']);  //aip llamada desde modorapido.ts cuando se factura en modo rapido