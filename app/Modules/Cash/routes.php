<?php

use App\Modules\Cash\Controllers\CajaController;
use App\Modules\Cash\Controllers\CierreController;
use App\Modules\Cash\Controllers\IngresosGastosController;
use App\Modules\Cash\Controllers\DocumentsCajaController;

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
$router->get('/admin/caja/trasladosRetirosDinero', [IngresosGastosController::class, 'trasladosRetirosDinero']);

$router->get('/printfacturacarta', [DocumentsCajaController::class, 'printfacturacarta']); //llamado desde ordenresumen y desde index caja
$router->get('/printcotizacion', [DocumentsCajaController::class, 'printcotizacion']); //llamado desde ordenresumen
$router->get('/printdetallecierre', [DocumentsCajaController::class, 'printdetallecierre']); //llamado desde cerrarcaja
$router->get('/admin/caja/ordenresumen', [DocumentsCajaController::class, 'ordenresumen']);  //resumen de la orden
$router->get('/admin/caja/detalleorden', [DocumentsCajaController::class, 'detalleorden']); //detalle de la orden
$router->get('/admin/caja/pedidosguardados', [DocumentsCajaController::class, 'pedidosguardados']);
$router->get('/admin/caja/despachosPendientes', [DocumentsCajaController::class, 'despachosPendientes']);

//////////////////////////    API    ///////////////////////////
$router->post('/admin/api/declaracionDinero', [CierreController::class, 'declaracionDinero']);
$router->post('/admin/api/arqueocaja', [CierreController::class, 'arqueocaja']);
$router->post('/admin/api/cierrecajaconfirmado', [CierreController::class, 'cierrecajaconfirmado']);
$router->post('/admin/api/datoscajaseleccionada', [CierreController::class, 'datoscajaseleccionada']);

$router->get('/admin/api/mediospagoXfactura', [DocumentsCajaController::class, 'mediospagoXfactura']); //obtener los medios de pago segun factura elegido en caja.ts
$router->post('/admin/api/cambioMedioPago', [DocumentsCajaController::class, 'cambioMedioPago']);  //aip llamada desde caja.ts
$router->get('/admin/api/getInvoice', [DocumentsCajaController::class, 'getInvoice']); //obtener detalle invoice en caja.ts para imprimir
$router->post('/admin/api/sendOrdenEmailToCustemer', [DocumentsCajaController::class, 'sendOrdenEmailToCustemer']);  //api llamada desde ordenresumen.ts para enviar detalle de orden por email
$router->get('/admin/api/caja/despacharOrden', [DocumentsCajaController::class, 'despacharOrden']); //despachar orden desdes ordenresumen.ts
$router->post('/admin/api/caja/cambiarEmisor', [DocumentsCajaController::class, 'cambiarEmisor']); //llamada desde ordenresumen.ts
$router->post('/admin/api/eliminarPedidoGuardado', [DocumentsCajaController::class, 'eliminarPedidoGuardado']);  //api llamada desde pedidosguardados.ts