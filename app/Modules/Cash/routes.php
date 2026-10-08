<?php

use App\Modules\Cash\Controllers\CajaController;
use App\Modules\Cash\Controllers\CierreController;

$router->get('/admin/caja', [CajaController::class, 'index']);
$router->get('/admin/caja/cerrarcaja', [CajaController::class, 'cerrarcaja']);
$router->get('/admin/caja/zetadiario', [CajaController::class, 'zetadiario']);
$router->get('/admin/caja/fechazetadiario', [CajaController::class, 'fechazetadiario']);
$router->get('/admin/caja/ultimoscierres', [CajaController::class, 'ultimoscierres']);
$router->get('/admin/caja/detallecierrecaja', [CajaController::class, 'detallecierrecaja']);

$router->post('/admin/api/declaracionDinero', [CierreController::class, 'declaracionDinero']);
$router->post('/admin/api/arqueocaja', [CierreController::class, 'arqueocaja']);
$router->post('/admin/api/cierrecajaconfirmado', [CierreController::class, 'cierrecajaconfirmado']);
$router->post('/admin/api/datoscajaseleccionada', [CierreController::class, 'datoscajaseleccionada']);
