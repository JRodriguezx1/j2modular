<?php

use App\Modules\Cash\Controllers\CajaController;
use App\Modules\Cash\Controllers\CierreController;
use App\Modules\Cash\Controllers\IngresosGastosController;
use App\Modules\Cash\Controllers\PrintCajaController;

$router->get('/admin/caja', [CajaController::class, 'index']);
$router->get('/admin/caja/cerrarcaja', [CajaController::class, 'cerrarcaja']);
$router->get('/admin/caja/zetadiario', [CajaController::class, 'zetadiario']);
$router->get('/admin/caja/fechazetadiario', [CajaController::class, 'fechazetadiario']);
$router->get('/admin/caja/ultimoscierres', [CajaController::class, 'ultimoscierres']);
$router->get('/admin/caja/detallecierrecaja', [CajaController::class, 'detallecierrecaja']);

$router->post('/admin/caja/ingresoGastoCaja', [IngresosGastosController::class, 'ingresoGastoCaja']);
$router->get('/admin/caja/categoriaGasto', [IngresosGastosController::class, 'categoriaGasto']);
$router->post('/admin/caja/categoriaGasto', [IngresosGastosController::class, 'categoriaGasto']);
$router->post('/admin/caja/crear_categoriaGasto', [IngresosGastosController::class, 'crear_categoriaGasto']);
$router->post('/admin/caja/editarcategoriagasto', [IngresosGastosController::class, 'editarcategoriagasto']);

$router->get('/printfacturacarta', [PrintCajaController::class, 'printfacturacarta']); //llamado desde ordenresumen y desde index caja
$router->get('/printcotizacion', [PrintCajaController::class, 'printcotizacion']); //llamado desde ordenresumen
$router->get('/printdetallecierre', [PrintCajaController::class, 'printdetallecierre']); //llamado desde cerrarcaja

$router->post('/admin/api/declaracionDinero', [CierreController::class, 'declaracionDinero']);
$router->post('/admin/api/arqueocaja', [CierreController::class, 'arqueocaja']);
$router->post('/admin/api/cierrecajaconfirmado', [CierreController::class, 'cierrecajaconfirmado']);
$router->post('/admin/api/datoscajaseleccionada', [CierreController::class, 'datoscajaseleccionada']);
