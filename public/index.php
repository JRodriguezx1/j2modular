<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/app.php'; //apunta al directorio raiz y luego a app.php, el archivo app contiene: las variables de entorno para el deploy,
                    //la clase ActiveRecord, el autoload de composer = localizador de clases, archivo de funciones debuguear y sanitizar html
                    //archivo de conexion de bd mysql con variables de entorno y me establece la conexion mediante: ActiveRecord::setDB($db);

//me importa clases del controlador

use App\Controllers\logincontrolador; //clase para logueo, registro de usuario, recuperacion, deslogueo etc..
use App\Controllers\dashboardcontrolador;
use App\Controllers\contabilidadcontrolador;
use App\Controllers\almacencontrolador;
use App\Controllers\apidiancontrolador;
use App\Controllers\archivocontroller;
use App\Controllers\cajacontrolador;
//use App\Controllers\ventascontrolador;
use App\Controllers\reportescontrolador;
use App\Controllers\clientescontrolador;
use App\Controllers\comisionescontrolador;
use App\Controllers\direccionescontrolador;
use App\Controllers\configcontrolador;
use App\Controllers\creditoscontrolador;
use App\Controllers\parqueaderocontrolador;
use App\Controllers\modorapidocontrolador;
use App\Controllers\nominaelectcontrolador;
use App\Controllers\paginacontrolador;
use App\Controllers\parametroscontrolador;
use App\Controllers\printcontrolador;
use App\Controllers\suscripcioncontrolador;
use App\Controllers\trasladosinvcontrolador;
use App\Controllers\whatsAppControlador;
use App\Middlewares\MembershipMiddleware;

use App\Core\Database\TransactionManager;
use App\Core\Database\MySqlTransactionManager;

// me importa la clase router
use App\Core\Container\Container;
use App\Core\Routing\Router;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\RouteManager;
use App\Core\Modules\ModuleManager;

$container = new Container();
$container->instance(mysqli::class, $db);
$container->instance(TransactionManager::class, new MySqlTransactionManager($db));

$router = new Router();
$router->setContainer($container);
$routeLoader = new RouteLoader($router);
$routeManager = new RouteManager($routeLoader);
//$routeManager->loadCoreRoutes();
$moduleManager = new ModuleManager($container, $routeManager);
$moduleManager->register($config['modules']);  //registra provider como RestaurantServiceProvider
$moduleManager->loadRoutes( $config['modules']);  //config viene de require_once __DIR__ . '/../includes/app.php';

$suscripcion = new MembershipMiddleware($router);
$suscripcion->validarSuscripcion();

$container->instance(Router::class, $router);  //registrar la misma instancia del Router en el Container. para que el controlador resuelva la instancia existente de Router.


// Login
$router->get('/loginauth', [logincontrolador::class, 'loginauth']);
$router->post('/loginauth', [logincontrolador::class, 'loginauth']);
$router->get('/login', [logincontrolador::class, 'login']);
$router->post('/login', [logincontrolador::class, 'login']);
$router->get('/logout', [logincontrolador::class, 'logout']);

// Crear Cuenta
$router->get('/registro', [logincontrolador::class, 'registro']);
$router->post('/registro', [logincontrolador::class, 'registro']);

// Formulario de olvide mi password
$router->get('/olvide', [logincontrolador::class, 'olvide']);
$router->post('/olvide', [logincontrolador::class, 'olvide']);

// Colocar el nuevo password
$router->get('/recuperarpass', [logincontrolador::class, 'recuperarpass']);
$router->post('/recuperarpass', [logincontrolador::class, 'recuperarpass']);

// Confirmación de Cuenta
$router->get('/mensaje', [logincontrolador::class, 'mensaje']);
$router->get('/confirmar-cuenta', [logincontrolador::class, 'confirmar_cuenta']);

//area publica
//$router->get('/', [paginacontrolador::class, 'index']);
///////////     print     ////////////
$router->get('/', [logincontrolador::class, 'login']);
$router->get('/printfacturacarta', [cajacontrolador::class, 'printfacturacarta']); //llamado desde ordenresumen y desde index caja
$router->get('/printcotizacion', [cajacontrolador::class, 'printcotizacion']); //llamado desde ordenresumen
$router->get('/printdetallecierre', [cajacontrolador::class, 'printdetallecierre']); //llamado desde cerrarcaja
$router->get('/printDetalleCompra', [reportescontrolador::class, 'printDetalleCompra']);  //lamada desde detalle compra.


/////area dashboard/////
$router->get('/admin/dashboard', [dashboardcontrolador::class, 'index']);
$router->get('/admin/perfil', [dashboardcontrolador::class, 'perfil']);
$router->post('/admin/perfil', [dashboardcontrolador::class, 'perfil']);
$router->post('/admin/actualizaremail', [dashboardcontrolador::class, 'actualizaremail']);
///// area de contabilidad /////
$router->get('/admin/contabilidad', [contabilidadcontrolador::class, 'index']);
///// area de nomina electronica /////
$router->get('/admin/nominaelectronica', [nominaelectcontrolador::class, 'index']);
///// area de almacen categorias, productos, subproductos, compras etc /////
$router->get('/admin/almacen', [almacencontrolador::class, 'index']);
$router->get('/admin/almacen/categorias', [almacencontrolador::class, 'categorias']);
$router->post('/admin/almacen/crear_categoria', [almacencontrolador::class, 'crear_categoria']);
$router->get('/admin/almacen/productos', [almacencontrolador::class, 'productos']);
$router->post('/admin/almacen/crear_producto', [almacencontrolador::class, 'crear_producto']);
$router->get('/admin/almacen/subproductos', [almacencontrolador::class, 'subproductos']);
$router->post('/admin/almacen/crear_subproducto', [almacencontrolador::class, 'crear_subproducto']);
$router->get('/admin/almacen/componer', [almacencontrolador::class, 'componer']);
$router->get('/admin/almacen/ajustarcostos', [almacencontrolador::class, 'ajustarcostos']);
$router->get('/admin/almacen/compras', [almacencontrolador::class, 'compras']);
$router->get('/admin/almacen/distribucion', [almacencontrolador::class, 'distribucion']);
$router->get('/admin/almacen/inventariar', [almacencontrolador::class, 'inventariar']);
$router->get('/admin/almacen/unidadesmedida', [almacencontrolador::class, 'unidadesmedida']);
$router->post('/admin/almacen/unidadesmedida', [almacencontrolador::class, 'unidadesmedida']);
$router->post('/admin/almacen/crear_unidadmedida', [almacencontrolador::class, 'crear_unidadmedida']);
$router->post('/admin/almacen/editarunidademedida', [almacencontrolador::class, 'editarunidademedida']);
$router->post('/admin/almacen/downexcelproducts', [almacencontrolador::class, 'downexcelproducts']);
$router->post('/admin/almacen/uploadExcel', [almacencontrolador::class, 'uploadExcel']);
$router->post('/admin/almacen/uploadInsumosExcel', [almacencontrolador::class, 'uploadInsumosExcel']);
$router->post('/admin/almacen/downexcelinsumos', [almacencontrolador::class, 'downexcelinsumos']);
$router->get('/admin/almacen/cambioPrecios', [almacencontrolador::class, 'cambioPrecios']);
$router->get('/admin/almacen/estadisticas', [almacencontrolador::class, 'estadisticas']);
$router->get('/admin/almacen/productosParaFormulas', [almacencontrolador::class, 'productosParaFormulas']);
$router->get('/admin/almacen/conversionUnidades', [almacencontrolador::class, 'conversionUnidades']);
////// area de traslados de inventario  //////
$router->get('/admin/almacen/solicitudesrecibidas', [trasladosinvcontrolador::class, 'solicitudesrecibidas']);
$router->get('/admin/almacen/trasladarinventario', [trasladosinvcontrolador::class, 'trasladarinventario']);
$router->get('/admin/almacen/solicitarinventario', [trasladosinvcontrolador::class, 'solicitarinventario']);
$router->get('/admin/almacen/nuevotrasladoinv', [trasladosinvcontrolador::class, 'nuevotrasladoinv']);
$router->get('/admin/almacen/editartrasladoinv', [trasladosinvcontrolador::class, 'editartrasladoinv']);
///// area de caja /////
// GET /admin/caja se registra en Modules/Cash/routes.php; el controlador antiguo se conserva.
// GET /admin/caja/cerrarcaja se registra en Modules/Cash/routes.php.
// Las consultas de reportes Z y cierres históricos se registran en Modules/Cash/routes.php.
$router->get('/admin/caja/pedidosguardados', [cajacontrolador::class, 'pedidosguardados']);
$router->get('/admin/caja/trasladosRetirosDinero', [cajacontrolador::class, 'trasladosRetirosDinero']);
$router->get('/admin/caja/despachosPendientes', [cajacontrolador::class, 'despachosPendientes']);
//$router->post('/admin/caja/ingresoGastoCaja', [cajacontrolador::class, 'ingresoGastoCaja']);
//$router->get('/admin/caja/categoriaGasto', [cajacontrolador::class, 'categoriaGasto']);
//$router->post('/admin/caja/categoriaGasto', [cajacontrolador::class, 'categoriaGasto']);
//$router->post('/admin/caja/crear_categoriaGasto', [cajacontrolador::class, 'crear_categoriaGasto']);
//$router->post('/admin/caja/editarcategoriagasto', [cajacontrolador::class, 'editarcategoriagasto']);
//$router->get('/admin/caja/ordenresumen', [cajacontrolador::class, 'ordenresumen']);  //resumen de la orden
//$router->get('/admin/caja/printfacturacarta', [cajacontrolador::class, 'printfacturacarta']);  //imprimir factura tipo carta
//$router->get('/admin/caja/detalleorden', [cajacontrolador::class, 'detalleorden']); //detalle de la orden
///// area de ventas /////
//$router->get('/admin/ventas', [ventascontrolador::class, 'index']);
///// area de ventas-modorapido /////
$router->get('/admin/ventas/modorapido', [modorapidocontrolador::class, 'index']);
///// print ticket //////
$router->get('/admin/printPDFPOS', [printcontrolador::class, 'printPDFPOS']);  //llamada desde ventas.ts cuando se realiza una venta exitosa
$router->get('/admin/printPDFPOSSeparado', [printcontrolador::class, 'printPDFPOSSeparado']);  //llamada desde separado.ts cuando se realiza un separado exitoso
$router->get('/admin/printPDFAbonoCredito', [printcontrolador::class, 'printPDFAbonoCredito']);  //llamada desde modulo creditos, vista detallecredito
$router->get('/admin/printComprobanteCompraPDF', [printcontrolador::class, 'printComprobanteCompraPDF']);  //llamada desde compras.ts
$router->get('/admin/printPDFPOSPagoComision', [printcontrolador::class, 'printPDFPOSPagoComision']);
///// Creditos /////
$router->get('/admin/creditos', [creditoscontrolador::class, 'index']);
$router->get('/admin/creditos/separado', [creditoscontrolador::class, 'separado']);
$router->get('/admin/creditos/detallecredito', [creditoscontrolador::class, 'detallecredito']); //detalle del credito
$router->get('/admin/creditos/adicionarProducto', [creditoscontrolador::class, 'adicionarProducto']); //detalle del credito
//$router->post('/admin/creditos/registrarAbono', [creditoscontrolador::class, 'registrarAbono']);
//$router->post('/admin/creditos/pagoTotal', [creditoscontrolador::class, 'pagoTotal']);
///// area de comisiones /////
$router->get('/admin/comisiones', [comisionescontrolador::class, 'index']);

////// Parqueadero //////
$router->get('/admin/parqueadero', [parqueaderocontrolador::class, 'index']);

///// area de reportes /////
$router->get('/admin/reportes', [reportescontrolador::class, 'index']);
$router->get('/admin/reportes/ventasgenerales', [reportescontrolador::class, 'ventasgenerales']);
$router->get('/admin/reportes/ventasxtransaccion', [reportescontrolador::class, 'ventasxtransaccion']);
$router->get('/admin/reportes/ventasxcliente', [reportescontrolador::class, 'vistaVentasxcliente']);
$router->get('/admin/reportes/ventaProductosUsuarios', [reportescontrolador::class, 'ventaProductosUsuarios']);
$router->get('/admin/reportes/reporteEmisores', [reportescontrolador::class, 'reporteEmisores']);
$router->get('/admin/reportes/facturaspagas', [reportescontrolador::class, 'facturaspagas']);
$router->get('/admin/reportes/remisiones', [reportescontrolador::class, 'remisiones']);
$router->get('/admin/reportes/creditos', [reportescontrolador::class, 'creditos']);
$router->get('/admin/reportes/creditos/cuotas-creditos', [reportescontrolador::class, 'cuotasCreditos']);
$router->get('/admin/reportes/creditos/creditos-finalizados', [reportescontrolador::class, 'creditosFinalizados']);
$router->get('/admin/reportes/creditos/creditos-anulados', [reportescontrolador::class, 'creditosAnulados']);
$router->get('/admin/reportes/facturasanuladas', [reportescontrolador::class, 'facturasanuladas']);
$router->get('/admin/reportes/facturaselectronicas', [reportescontrolador::class, 'facturaselectronicas']);
$router->get('/admin/reportes/facturaselectronicaspendientes', [reportescontrolador::class, 'facturaselectronicaspendientes']);
$router->get('/admin/reportes/recibosCaja', [reportescontrolador::class, 'recibosCaja']);
$router->get('/admin/reportes/inventarioxproducto', [reportescontrolador::class, 'inventarioxproducto']);
$router->get('/admin/reportes/movimientosinventarios', [reportescontrolador::class, 'movimientosinventarios']);
$router->get('/admin/reportes/compras', [reportescontrolador::class, 'compras']);
$router->get('/admin/reportes/productosComprados', [reportescontrolador::class, 'productosComprados']);
$router->get('/admin/reportes/detallecompra', [reportescontrolador::class, 'detallecompra']);
$router->get('/admin/reportes/utilidadRentabilidad', [reportescontrolador::class, 'utilidadRentabilidad']);
$router->get('/admin/reportes/utilidadxproducto', [reportescontrolador::class, 'utilidadxproducto']);
$router->get('/admin/reportes/gastoseingresos', [reportescontrolador::class, 'gastoseingresos']);
$router->get('/admin/reportes/clientesnuevos', [reportescontrolador::class, 'clientesnuevos']);
$router->get('/admin/reportes/clientesrecurrentes', [reportescontrolador::class, 'clientesrecurrentes']);
$router->get('/admin/reportes/detalleInvoice', [reportescontrolador::class, 'detalleInvoice']);  //detalle de la factura electronica
///// area de clientes /////
$router->get('/admin/clientes', [clientescontrolador::class, 'index']);
$router->post('/admin/clientes', [clientescontrolador::class, 'index']); //filtro de busqueda
$router->get('/admin/clientes/marketing', [clientescontrolador::class, 'marketing']);
$router->get('/admin/clientes/marketing/crearcampania', [clientescontrolador::class, 'crearcampania']);
$router->post('/admin/clientes/crear', [clientescontrolador::class, 'crear']);  //crear cliente en vista de clientes
$router->post('/admin/clientes/actualizar', [clientescontrolador::class, 'actualizar']);
$router->get('/admin/clientes/detalle', [clientescontrolador::class, 'detalle']);
$router->get('/admin/clientes/hab_desh', [clientescontrolador::class, 'hab_desh']); //habilitar deshabilitar cliente
$router->get('/admin/clientes/preciosXCliente', [clientescontrolador::class, 'preciosXCliente']);
///// direcciones de los clientes /////
$router->post('/admin/direcciones/crear', [direccionescontrolador::class, 'crear']);  //crear direccion en vista de clientes
///// area de configuracion /////
$router->get('/admin/configuracion', [configcontrolador::class, 'index']);
$router->post('/admin/configuracion/editarnegocio', [configcontrolador::class, 'editarnegocio']);
$router->post('/admin/configuracion/crear_empleado', [configcontrolador::class, 'crear_empleado']);
//// Suscripcion /////
$router->get('/suspendido', [suscripcioncontrolador::class, 'suspendido']);
//// Descargas /////
$router->get('/admin/descarga/plantillaimportarproductos', [archivocontroller::class, 'descargarExcel']);
$router->get('/admin/descarga/plantillaImportarInsumos', [archivocontroller::class, 'descargarInsumosExcel']);
$router->get('/admin/descarga/instruccionesimportarproductos', [archivocontroller::class, 'descargarInstrucciones']);
$router->get('/admin/descarga/logo', [archivocontroller::class, 'descargarLogo']);


/////////////////////////////////////--   API'S   --////////////////////////////////////////
$router->post('/admin/api/changeSucursal/select', [logincontrolador::class, 'changeSucursal']);

$router->get('/admin/api/ventasVsGastos', [dashboardcontrolador::class, 'ventasVsGastos']);
$router->get('/admin/api/ultimos7dias', [dashboardcontrolador::class, 'ultimos7dias']);

$router->post('/admin/api/actualizar_categoria', [almacencontrolador::class, 'actualizar_categoria']);
$router->post('/admin/api/eliminarCategoria', [almacencontrolador::class, 'eliminarCategoria']);
$router->get('/admin/api/allproducts', [almacencontrolador::class, 'allproducts']); //trae todos los productos
$router->post('/admin/api/actualizarproducto', [almacencontrolador::class, 'actualizarproducto']);  //actualizar en general el producto
$router->post('/admin/api/eliminarProducto', [almacencontrolador::class, 'eliminarProducto']);
$router->get('/admin/api/allsubproducts', [almacencontrolador::class, 'allsubproducts']); //trae todos los sub-productos
$router->post('/admin/api/actualizarsubproducto', [almacencontrolador::class, 'actualizarsubproducto']);  //actualizar en general el producto
$router->post('/admin/api/eliminarSubProducto', [almacencontrolador::class, 'eliminarSubProducto']);
$router->post('/admin/api/setrendimientoestandar', [almacencontrolador::class, 'setrendimientoestandar']);  //establecer rendimiento estandar de la formula de salida
$router->post('/admin/api/ensamblar', [almacencontrolador::class, 'ensamblar']);  //asociar un o unos subproductos a un producto principal
$router->get('/admin/api/desasociarsubproducto', [almacencontrolador::class, 'desasociarsubproducto']);
$router->get('/admin/api/almacen/allUnidadesMedida', [almacencontrolador::class, 'allUnidadesMedida']); //trae todas las unidades de medida
$router->get('/admin/api/allConversionesUnidades', [almacencontrolador::class, 'allConversionesUnidades']); //trae todos los sub-productos con todas las unidades equivalentes
$router->post('/admin/api/almacen/crearNuevaConversionUnidad', [almacencontrolador::class, 'crearNuevaConversionUnidad']);
$router->get('/admin/api/almacen/eliminarConversionUnidad', [almacencontrolador::class, 'eliminarConversionUnidad']);
$router->post('/admin/api/actualizarcostos', [almacencontrolador::class, 'actualizarcostos']);  //actualizar costos, api llamada desde ajustarcostos.ts
$router->post('/admin/api/actualizarPreciosVenta', [almacencontrolador::class, 'actualizarPreciosVenta']);  //actualizar precios, api llamada desde ajustarprecios.ts
$router->get('/admin/api/totalitems', [almacencontrolador::class, 'totalitems']);  //api llamada desde compras.ts para obtener los productos simples y subproductos
$router->post('/admin/api/registrarCompra', [almacencontrolador::class, 'registrarCompra']);  //
$router->post('/admin/api/descontarstock', [almacencontrolador::class, 'descontarstock']);  //descontar unidades de inventario
$router->post('/admin/api/aumentarstock', [almacencontrolador::class, 'aumentarstock']);  //ingresar o aumentar unidades de inventario
$router->post('/admin/api/ajustarstock', [almacencontrolador::class, 'ajustarstock']);  //reiniciar o ajustar inventario
$router->post('/admin/api/reiniciarinv', [almacencontrolador::class, 'reiniciarinv']);  //reiniciar inv a cero, llamada desde almacen.ts
$router->post('/admin/api/cambiarestadoproducto', [almacencontrolador::class, 'cambiarestadoproducto']);  //cambiar el estado del producto desde producto.ts
$router->get('/admin/api/getStockproductosXsucursal', [almacencontrolador::class, 'getStockproductosXsucursal']);  //reiniciar inv a cero, llamada desde almacen.ts
$router->get('/admin/api/allproveedores', [almacencontrolador::class, 'allproveedores']); // me trae todos los proveedores desde gestionproveedores.js
$router->post('/admin/api/crearProveedor', [almacencontrolador::class, 'crearProveedor']); //api llamada desde gestionproveedores.js para crear proveedores
$router->post('/admin/api/actualizarProveedor', [almacencontrolador::class, 'actualizarProveedor']); //api llamada desde gestionproveedores.js para actualizar proveedores
$router->post('/admin/api/eliminarProveedor', [almacencontrolador::class, 'eliminarProveedor']); //api llamada desde gestionproveedores.js para eliminar proveedores
$router->post('/admin/api/generarBarCode', [almacencontrolador::class, 'generarBarCode']); // me trae todos los proveedores desde gestionproveedores.js
$router->get('/admin/api/getItemsBajoStock', [almacencontrolador::class, 'getItemsBajoStock']);

//$router->get('/admin/api/allordenestrasladoinv', [trasladosinvcontrolador::class, 'allordenestrasladoinv']); //trae todos las ordenes de traslados
$router->get('/admin/api/idOrdenTrasladoSolicitudInv', [trasladosinvcontrolador::class, 'idOrdenTrasladoSolicitudInv']); //trae todos las ordenes de traslados
$router->post('/admin/api/apisolicitarinventario', [trasladosinvcontrolador::class, 'apisolicitarinventario']); //api llamada desde solicitarinventario.ts para solicitar productos a otras sedes
$router->post('/admin/api/apinuevotrasladoinv', [trasladosinvcontrolador::class, 'apinuevotrasladoinv']); //api llamada desde nuevotrasladoinv.ts para enviar productos a otras sedes
$router->post('/admin/api/editarOrdenTransferencia', [trasladosinvcontrolador::class, 'editarOrdenTransferencia']); //api llamada desde editartrasladoinv.ts para actualizar lista de productos a enviar
$router->post('/admin/api/confirmarnuevotrasladoinv', [trasladosinvcontrolador::class, 'confirmarnuevotrasladoinv']); //api llamada desde trasladarinv.ts para confirmar lista de productos a enviar y descontar de inventario y pasar a estado en transito
$router->post('/admin/api/confirmaringresoinv', [trasladosinvcontrolador::class, 'confirmaringresoinv']); //api llamada desde solicitudesrecibidasinv.ts para confirmar lista de productos a recibir y sumar de inventario y pasar a estado en entregado
$router->post('/admin/api/anularnuevotrasladoinv', [trasladosinvcontrolador::class, 'anularnuevotrasladoinv']); //api llamada desde trasladarinv.ts y solicitudesrecibidasinv para cancelar orden de traslado o solicitud

// Las cuatro acciones HTTP del cierre se registran en Modules/Cash/routes.php.
$router->get('/admin/api/mediospagoXfactura', [cajacontrolador::class, 'mediospagoXfactura']); //obtener los medios de pago segun factura elegido en caja.ts
$router->post('/admin/api/cambioMedioPago', [cajacontrolador::class, 'cambioMedioPago']);  //aip llamada desde caja.ts
$router->post('/admin/api/eliminarPedidoGuardado', [cajacontrolador::class, 'eliminarPedidoGuardado']);  //api llamada desde pedidosguardados.ts
//$router->post('/admin/api/sendOrdenEmailToCustemer', [cajacontrolador::class, 'sendOrdenEmailToCustemer']);  //api llamada desde ordenresumen.ts para enviar detalle de orden por email
$router->get('/admin/api/getInvoice', [cajacontrolador::class, 'getInvoice']); //obtener detalle invoice en caja.ts para imprimir
//$router->get('/admin/api/caja/despacharOrden', [cajacontrolador::class, 'despacharOrden']); //despachar orden desdes ordenresumen.ts
//$router->post('/admin/api/caja/cambiarEmisor', [cajacontrolador::class, 'cambiarEmisor']); //llamada desde ordenresumen.ts

$router->post('/admin/api/facturarModorapido', [modorapidocontrolador::class, 'facturarModorapido']);  //aip llamada desde modorapido.ts cuando se factura en modo rapido

$router->get('/admin/api/allcredits', [creditoscontrolador::class, 'allcredits']);
$router->post('/admin/api/creditos/registrarAbono', [creditoscontrolador::class, 'registrarAbono']);
$router->post('/admin/api/crearSeparado', [creditoscontrolador::class, 'crearSeparado']);
$router->get('/admin/api/detalleProductosCredito', [creditoscontrolador::class, 'detalleProductosCredito']);
$router->post('/admin/api/cuota/cambioMedioPagoSeparado', [creditoscontrolador::class, 'cambioMedioPagoSeparado']);
$router->post('/admin/api/anularSeparado', [creditoscontrolador::class, 'anularSeparado']);
$router->post('/admin/api/ajustarCreditoAntiguo', [creditoscontrolador::class, 'ajustarCreditoAntiguo']);
$router->post('/admin/api/editarOrdenCreditoSeparado', [creditoscontrolador::class, 'editarOrdenCreditoSeparado']);
$router->get('/admin/api/totalCuotasXcliente', [creditoscontrolador::class, 'totalCuotasXcliente']);
$router->get('/admin/api/getCreditoSeparado', [creditoscontrolador::class, 'getCreditoSeparado']);  //llamada desde creditos/index.ts para detalle de credito/separado e imprimir
$router->get('/admin/api/creditos/getAbono', [creditoscontrolador::class, 'getAbono']);  //llamada desde creditos/detallecredito.ts para detalle del abono e imprimir
$router->get('/admin/api/creditos/anularAbono', [creditoscontrolador::class, 'anularAbono']);  //llamada desde creditos/detallecredito.ts para anular el abono.
$router->post('/admin/api/creditos/pagarDeudaTotal', [creditoscontrolador::class, 'pagarDeudaTotal']);  //llamada desde clientes/detalle.ts
$router->post('/admin/api/creditos/registrarAbonoFromCli', [creditoscontrolador::class, 'registrarAbonoFromCli']); //llamada desde clientes/detalle.ts
$router->get('/admin/api/creditos/buscarIntersucursal', [creditoscontrolador::class, 'buscarIntersucursal']);

$router->post('/admin/api/comisiones/comisionesXUser', [comisionescontrolador::class, 'comisionesXUser']);
$router->post('/admin/api/comisiones/liquidarComision', [comisionescontrolador::class, 'liquidarComision']);
$router->get('/admin/api/comisiones/eliminarMovimientoComision', [comisionescontrolador::class, 'eliminarMovimientoComision']);
$router->get('/admin/api/comisiones/detalleFacturaComision', [comisionescontrolador::class, 'detalleFacturaComision']);

$router->post('/admin/api/parqueadero/createUpdateTarifa', [parqueaderocontrolador::class, 'createUpdateTarifa']);
$router->get('/admin/api/parqueadero/allTarifas', [parqueaderocontrolador::class, 'allTarifas']);  //lamadas desde parqueadero.ts

$router->post('/admin/api/consultafechazetadiario', [reportescontrolador::class, 'consultafechazetadiario']); //aip llamada desde fechazetadiario.ts

$router->post('/admin/api/apiCrearCliente', [clientescontrolador::class, 'apiCrearCliente']);  // crear cliente desde modulo de ventas.ts
$router->post('/admin/api/addDireccionCliente', [direccionescontrolador::class, 'addDireccionCliente']); //add direccion segun cliente elegido desde ventas.ts
$router->get('/admin/api/allclientes', [clientescontrolador::class, 'allclientes']); // me trae todos los clientes desde clientes.js
$router->post('/admin/api/actualizarCliente', [clientescontrolador::class, 'apiActualizarcliente']);  //actualizar cliente en clientes.ts
$router->post('/admin/api/eliminarCliente', [clientescontrolador::class, 'apiEliminarCliente']); //eliminar cliente en clientes.ts
$router->get('/admin/api/clientes/direccionesXcliente', [clientescontrolador::class, 'direccionesXcliente']); //obtener direcciones segun cliente elegido en ventas.ts y en clientes.ts
$router->get('/admin/api/clientes/comprasXMesXCliente', [clientescontrolador::class, 'comprasXMesXCliente']);
$router->get('/admin/api/clientes/ventasXCategoriasXCliente', [clientescontrolador::class, 'ventasXCategoriasXCliente']);
$router->post('/admin/api/clientes/preciospersonalizados', [clientescontrolador::class, 'preciospersonalizados']);
$router->get('/admin/api/clientes/eliminarPrecioPersonalizado', [clientescontrolador::class, 'eliminarPrecioPersonalizado']);

$router->get('/admin/api/allcajas', [configcontrolador::class, 'allcajas']); // me trae todos las cajas desde gestioncajas.ts
$router->post('/admin/api/crearCaja', [configcontrolador::class, 'crearCaja']); //api llamada desde gestioncajas.ts para crear cajas
$router->post('/admin/api/actualizarCaja', [configcontrolador::class, 'actualizarCaja']); //api llamada desde gestioncajas.ts para actualizar cajas
$router->post('/admin/api/eliminarCaja', [configcontrolador::class, 'eliminarCaja']); //api llamada desde gestioncajas.ts para eliminar cajas
$router->get('/admin/api/allfacturadores', [configcontrolador::class, 'allfacturadores']); // me trae todos las cajas desde gestioncajas.ts
$router->post('/admin/api/crearFacturador', [configcontrolador::class, 'crearFacturador']); //api llamada desde gestioncajas.ts para crear cajas
$router->post('/admin/api/actualizarFacturador', [configcontrolador::class, 'actualizarFacturador']); //api llamada desde gestioncajas.ts para actualizar cajas
$router->post('/admin/api/eliminarFacturador', [configcontrolador::class, 'eliminarFacturador']); //api llamada desde gestioncajas.ts para eliminar cajas
$router->get('/admin/api/allbancos', [configcontrolador::class, 'allbancos']); // me trae todos los bancos desde gestionbancos.ts
$router->post('/admin/api/crearBanco', [configcontrolador::class, 'crearBanco']); //api llamada desde gestionbancos.ts para crear bancos
$router->post('/admin/api/actualizarBanco', [configcontrolador::class, 'actualizarBanco']); //api llamada desde gestionbancos.ts para actualizar bancos
$router->post('/admin/api/eliminarBanco', [configcontrolador::class, 'eliminarBanco']); //api llamada desde gestionbancos.ts para eliminar bancos
$router->get('/admin/api/alltarifas', [configcontrolador::class, 'alltarifas']); // me trae todas las tarifas desde gestiontarifas.ts
$router->post('/admin/api/crearTarifa', [configcontrolador::class, 'crearTarifa']); //api llamada desde gestiontarifas.ts para crear tarifas
$router->post('/admin/api/actualizarTarifa', [configcontrolador::class, 'actualizarTarifa']); //api llamada desde gestiontarifas.ts para actualizar tarifas
$router->post('/admin/api/eliminarTarifa', [configcontrolador::class, 'eliminarTarifa']); //api llamada desde gestiontarifas.ts para eliminar tarifas
$router->get('/admin/api/allmediospago', [configcontrolador::class, 'allmediospago']); // me trae todos los medios de pago desde gestionmediospago.ts
$router->post('/admin/api/crearMedioPago', [configcontrolador::class, 'crearMedioPago']); //api llamada desde gestionmediospago.ts para crear medios de pagos
$router->post('/admin/api/actualizarMedioPago', [configcontrolador::class, 'actualizarMedioPago']); //api llamada desde gestionmediospago.ts para actualizar medios de pagos
$router->post('/admin/api/eliminarMedioPago', [configcontrolador::class, 'eliminarMedioPago']); //api llamada desde gestionmediospago.ts para eliminar medios de pagos
$router->post('/admin/api/updateStateMedioPago', [configcontrolador::class, 'updateStateMedioPago']); //api llamada desde gestionmediospago.ts para cambiar el estado de medio de pago
$router->get('/admin/api/getAllemployee', [configcontrolador::class, 'getAllemployee']); //fetch en empleados.ts
$router->post('/admin/api/actualizarEmpleado', [configcontrolador::class, 'actualizarEmpleado']); //fetch llamado en empleados.ts
$router->post('/admin/api/eliminarEmpleado', [configcontrolador::class, 'eliminarEmpleado']); //fetch llamado en empleados.ts
$router->post('/admin/api/updatepassword', [configcontrolador::class, 'updatepassword']); //fetch llamado en empleados.ts
$router->get('/admin/api/config/allPrinters', [configcontrolador::class, 'allPrinters']); // me trae todos las impresoras desde gestionimpresoras.ts
$router->post('/admin/api/config/crearPrinter', [configcontrolador::class, 'crearPrinter']); //api llamada desde gestionimpresoras.ts para crear impresora
$router->post('/admin/api/config/actualizarPrinter', [configcontrolador::class, 'actualizarPrinter']); //api llamada desde gestionimpresoras.ts para actualizar impresora
$router->post('/admin/api/config/eliminarPrinter', [configcontrolador::class, 'eliminarPrinter']);
$router->get('/admin/api/config/allEmisores', [configcontrolador::class, 'allEmisores']); // me trae todos los emisores desde gestionemisores.ts
$router->post('/admin/api/config/crearEmisor', [configcontrolador::class, 'crearEmisor']); //api llamada desde gestionemisores.ts para crear impresoras
$router->post('/admin/api/config/actualizarEmisor', [configcontrolador::class, 'actualizarEmisor']); //api llamada desde gestionemisores.ts para actualizar emisores
$router->post('/admin/api/config/eliminarEmisor', [configcontrolador::class, 'eliminarEmisor']);
$router->post('/admin/api/config/updateStateEmisor', [configcontrolador::class, 'updateStateEmisor']); //api llamada desde gestionemisores.ts para cambiar el estado del emisor

$router->get('/admin/api/reporteventamensual', [reportescontrolador::class, 'reporteventamensual']);
$router->get('/admin/api/ventasGraficaMensual', [reportescontrolador::class, 'ventasGraficaMensual']);  //fetch llamado desde reportes.ts
$router->get('/admin/api/ventasGraficaDiario', [reportescontrolador::class, 'ventasGraficaDiario']);  //fetch llamado desde reportes.ts
$router->get('/admin/api/graficaValorInventario', [reportescontrolador::class, 'graficaValorInventario']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/reportes/reportesGenerales', [reportescontrolador::class, 'reportesGenerales']);  //fetch llamado desde ventasgenerales.ts
$router->get('/admin/api/ventasxtransaccionanual', [reportescontrolador::class, 'ventasxtransaccionanual']);  //fetch llamado desde reportes.ts
$router->get('/admin/api/ventasxtransaccionmes', [reportescontrolador::class, 'ventasxtransaccionmes']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/ventasxcliente', [reportescontrolador::class, 'ventasxcliente']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/reportes/reporteEmisores', [reportescontrolador::class, 'apiReporteEmisores']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/facturaspagas', [reportescontrolador::class, 'apifacturaspagas']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/reportes/remisiones', [reportescontrolador::class, 'apiRemisiones']);  //fetch llamado desde remisiones.ts
$router->post('/admin/api/reportes/creditos/estadosFinancieros', [reportescontrolador::class, 'estadosFinancierosCreditos']);  //fetch llamado desde reportes/creditos.ts
$router->post('/admin/api/reportes/creditos/cuotasCreditos', [reportescontrolador::class, 'apiCuotasCreditos']);  //fetch llamado desde cuotascreditoss.ts
$router->post('/admin/api/facturasanuladas', [reportescontrolador::class, 'apifacturasanuladas']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/facturaselectronicas', [reportescontrolador::class, 'apifacturaselectronicas']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/electronicaspendientes', [reportescontrolador::class, 'apielectronicaspendientes']);  //fetch llamado desde reportes.ts
$router->post('/admin/api/reportes/recibosCaja', [reportescontrolador::class, 'apirecibosCaja']);  //fetch llamado desde recibosCaja.ts
$router->post('/admin/api/movimientoInventario', [reportescontrolador::class, 'movimientoInventario']);  //fetch llamado desde movimientosinventarios.ts
$router->post('/admin/api/reportecompras', [reportescontrolador::class, 'reportecompras']);  //fetch llamado desde reportecompras.ts
$router->post('/admin/api/reportes/listaProductosComprados', [reportescontrolador::class, 'listaProductosComprados']);  //fetch llamado desde reporteproductosCompras.ts
$router->post('/admin/api/eliminarcompra', [reportescontrolador::class, 'eliminarcompra']);  //fetch llamado desde reportecompras.ts
$router->post('/admin/api/gastoseingresos', [reportescontrolador::class, 'apigastoseingresos']);  //fetch llamado desde gastosingresos.ts
$router->post('/admin/api/eliminargasto', [reportescontrolador::class, 'eliminargasto']);  //fetch llamado desde gastosingresos.ts
$router->post('/admin/api/eliminaringreso', [reportescontrolador::class, 'eliminaringresocaja']);  //fetch llamado desde gastosingresos.ts


$router->post('/admin/api/parametrosSistema', [parametroscontrolador::class, 'parametrosSistema']); //fetch llamado en configparametros.js
$router->post('/admin/api/parametrosSistemaClaves', [parametroscontrolador::class, 'parametrosSistemaClaves']); //fetch llamado en configparametros.js
$router->post('/admin/api/parametrosSistemaTipoSelect', [parametroscontrolador::class, 'parametrosSistemaTipoSelect']); //fetch llamado en configparametros.js
$router->get('/admin/api/getPasswords', [parametroscontrolador::class, 'getPasswords']); //obtener los password del sistema
$router->get('/admin/api/getParamGlobal', [parametroscontrolador::class, 'getParamGlobal']); //obtener los parametros del sistema
$router->post('/admin/api/param/changeTasaCambio', [parametroscontrolador::class, 'changeTasaCambio']); //api llamada desde app.ts para el cambio de divisa equivalente

$router->get('/admin/api/citiesXdepartments', [apidiancontrolador::class, 'citiesXdepartments']);  //Consulta municipios segun departamento
$router->post('/admin/api/crearCompanyJ2', [apidiancontrolador::class, 'crearCompanyJ2']);  // crear la compañia en j2
$router->get('/admin/api/getCompaniesAll', [apidiancontrolador::class, 'getCompaniesAll']);  //Consulta todas las compañias asociadas a la cuenta
$router->get('/admin/api/eliminarCompanyLocal', [apidiancontrolador::class, 'eliminarCompanyLocal']);  //Elimina la compañia de manera local
$router->post('/admin/api/guardarResolutionJ2', [apidiancontrolador::class, 'guardarResolutionJ2']);  // guardar resolucion en j2
$router->post('/admin/api/guardarNCInvoiceJ2', [apidiancontrolador::class, 'guardarNCInvoiceJ2']);  // guardar resolucion en j2
$router->get('/admin/api/filterAdquirientes', [apidiancontrolador::class, 'filterAdquirientes']);  //obtener todos los adquiriente, llamada desde ventas.adquiriente.ts
$router->post('/admin/api/guardarAdquiriente', [apidiancontrolador::class, 'guardarAdquiriente']);  // guardar adquiente, llamada desde ventas.adquiriente.ts
$router->post('/admin/api/sendInvoice', [apidiancontrolador::class, 'sendInvoice']);  // guardar adquiente, llamada desde ventas.sendinvoice.ts
$router->POST('/admin/api/sendNc', [apidiancontrolador::class, 'sendNc']);
$router->POST('/admin/api/crearFacturaPOSaElectronica', [apidiancontrolador::class, 'crearFacturaPOSaElectronica']);
$router->POST('/admin/api/asignarAdquirienteAFactura', [apidiancontrolador::class, 'asignarAdquirienteAFactura']);
$router->POST('/admin/api/eliminarFacturaElectronica', [apidiancontrolador::class, 'eliminarFacturaElectronica']);
$router->POST('/admin/api/editarResolutionFE', [apidiancontrolador::class, 'editarResolutionFE']);

$router->post('/admin/api/suscripcion/registrarPago', [suscripcioncontrolador::class, 'registrarPago']); //fetch llamado en suscripcionpago.ts
$router->post('/admin/api/suscripcion/detalleSuscripcion', [suscripcioncontrolador::class, 'detalleSuscripcion']); //fetch llamado en suscripcionpago.ts

$router->post('/admin/api/ws/notificacionWS/crearContacto', [whatsAppControlador::class, 'crearContacto']);
$router->get('/admin/api/ws/notificacionWS/eliminarContacto', [whatsAppControlador::class, 'eliminarContacto']);
$router->get('/admin/api/ws/notificacionWS/sendTest', [whatsAppControlador::class, 'sendTest']);
$router->get('/admin/api/ws/sendtextDetalleCierreCaja', [whatsAppControlador::class, 'sendtextDetalleCierreCaja']); 

$router->comprobarRutas();
