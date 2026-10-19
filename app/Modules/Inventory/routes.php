<?php


use App\Modules\Inventory\Controllers\InventoryController;
use App\Modules\Inventory\Controllers\ApiInventoryController;

$router->get('/admin/almacen', [InventoryController::class, 'index']);
$router->get('/admin/almacen/categorias', [InventoryController::class, 'categorias']);
$router->post('/admin/almacen/crear_categoria', [InventoryController::class, 'crear_categoria']);
$router->get('/admin/almacen/productos', [InventoryController::class, 'productos']);
$router->post('/admin/almacen/crear_producto', [InventoryController::class, 'crear_producto']);
$router->get('/admin/almacen/subproductos', [InventoryController::class, 'subproductos']);
$router->post('/admin/almacen/crear_subproducto', [InventoryController::class, 'crear_subproducto']);
$router->get('/admin/almacen/componer', [InventoryController::class, 'componer']);
$router->get('/admin/almacen/ajustarcostos', [InventoryController::class, 'ajustarcostos']);
$router->get('/admin/almacen/compras', [InventoryController::class, 'compras']);
$router->get('/admin/almacen/distribucion', [InventoryController::class, 'distribucion']);
$router->get('/admin/almacen/inventariar', [InventoryController::class, 'inventariar']);
$router->get('/admin/almacen/unidadesmedida', [InventoryController::class, 'unidadesmedida']);
$router->post('/admin/almacen/unidadesmedida', [InventoryController::class, 'unidadesmedida']);
$router->post('/admin/almacen/crear_unidadmedida', [InventoryController::class, 'crear_unidadmedida']);
$router->post('/admin/almacen/editarunidademedida', [InventoryController::class, 'editarunidademedida']);
$router->post('/admin/almacen/downexcelproducts', [InventoryController::class, 'downexcelproducts']);
$router->post('/admin/almacen/uploadExcel', [InventoryController::class, 'uploadExcel']);
$router->post('/admin/almacen/uploadInsumosExcel', [InventoryController::class, 'uploadInsumosExcel']);
$router->post('/admin/almacen/downexcelinsumos', [InventoryController::class, 'downexcelinsumos']);
$router->get('/admin/almacen/cambioPrecios', [InventoryController::class, 'cambioPrecios']);
$router->get('/admin/almacen/estadisticas', [InventoryController::class, 'estadisticas']);
$router->get('/admin/almacen/productosParaFormulas', [InventoryController::class, 'productosParaFormulas']);
$router->get('/admin/almacen/conversionUnidades', [InventoryController::class, 'conversionUnidades']);

//////////////////////     API     //////////////////////////
$router->get('/admin/api/inventory/getAllcategorias', [ApiInventoryController::class, 'getAllcategorias']);
$router->post('/admin/api/actualizar_categoria', [ApiInventoryController::class, 'actualizar_categoria']);
$router->post('/admin/api/eliminarCategoria', [ApiInventoryController::class, 'eliminarCategoria']);
$router->get('/admin/api/inventory/listSimpleProducts', [ApiInventoryController::class, 'listSimpleProducts']); //trae todos los productos
$router->get('/admin/api/allproducts', [ApiInventoryController::class, 'allproducts']); //trae todos los productos
$router->post('/admin/api/actualizarproducto', [ApiInventoryController::class, 'actualizarproducto']);  //actualizar en general el producto
$router->post('/admin/api/eliminarProducto', [ApiInventoryController::class, 'eliminarProducto']);
$router->get('/admin/api/allsubproducts', [ApiInventoryController::class, 'allsubproducts']); //trae todos los sub-productos
$router->post('/admin/api/actualizarsubproducto', [ApiInventoryController::class, 'actualizarsubproducto']);  //actualizar en general el producto
$router->post('/admin/api/eliminarSubProducto', [ApiInventoryController::class, 'eliminarSubProducto']);
$router->post('/admin/api/setrendimientoestandar', [ApiInventoryController::class, 'setrendimientoestandar']);  //establecer rendimiento estandar de la formula de salida
$router->post('/admin/api/ensamblar', [ApiInventoryController::class, 'ensamblar']);  //asociar un o unos subproductos a un producto principal
$router->get('/admin/api/desasociarsubproducto', [ApiInventoryController::class, 'desasociarsubproducto']);
$router->get('/admin/api/almacen/allUnidadesMedida', [ApiInventoryController::class, 'allUnidadesMedida']); //trae todas las unidades de medida
$router->get('/admin/api/allConversionesUnidades', [ApiInventoryController::class, 'allConversionesUnidades']); //trae todos los sub-productos con todas las unidades equivalentes
$router->post('/admin/api/almacen/crearNuevaConversionUnidad', [ApiInventoryController::class, 'crearNuevaConversionUnidad']);
$router->get('/admin/api/almacen/eliminarConversionUnidad', [ApiInventoryController::class, 'eliminarConversionUnidad']);
$router->post('/admin/api/actualizarcostos', [ApiInventoryController::class, 'actualizarcostos']);  //actualizar costos, api llamada desde ajustarcostos.ts
$router->post('/admin/api/actualizarPreciosVenta', [ApiInventoryController::class, 'actualizarPreciosVenta']);  //actualizar precios, api llamada desde ajustarprecios.ts
$router->get('/admin/api/totalitems', [ApiInventoryController::class, 'totalitems']);  //api llamada desde compras.ts para obtener los productos simples y subproductos
$router->post('/admin/api/registrarCompra', [ApiInventoryController::class, 'registrarCompra']);  //
$router->post('/admin/api/descontarstock', [ApiInventoryController::class, 'descontarstock']);  //descontar unidades de inventario
$router->post('/admin/api/aumentarstock', [ApiInventoryController::class, 'aumentarstock']);  //ingresar o aumentar unidades de inventario
$router->post('/admin/api/ajustarstock', [ApiInventoryController::class, 'ajustarstock']);  //reiniciar o ajustar inventario
$router->post('/admin/api/reiniciarinv', [ApiInventoryController::class, 'reiniciarinv']);  //reiniciar inv a cero, llamada desde almacen.ts
$router->post('/admin/api/cambiarestadoproducto', [ApiInventoryController::class, 'cambiarestadoproducto']);  //cambiar el estado del producto desde producto.ts
$router->get('/admin/api/getStockproductosXsucursal', [ApiInventoryController::class, 'getStockproductosXsucursal']);  //reiniciar inv a cero, llamada desde almacen.ts
$router->get('/admin/api/allproveedores', [ApiInventoryController::class, 'allproveedores']); // me trae todos los proveedores desde gestionproveedores.js
$router->post('/admin/api/crearProveedor', [ApiInventoryController::class, 'crearProveedor']); //api llamada desde gestionproveedores.js para crear proveedores
$router->post('/admin/api/actualizarProveedor', [ApiInventoryController::class, 'actualizarProveedor']); //api llamada desde gestionproveedores.js para actualizar proveedores
$router->post('/admin/api/eliminarProveedor', [ApiInventoryController::class, 'eliminarProveedor']); //api llamada desde gestionproveedores.js para eliminar proveedores
$router->post('/admin/api/generarBarCode', [ApiInventoryController::class, 'generarBarCode']); // me trae todos los proveedores desde gestionproveedores.js
$router->get('/admin/api/getItemsBajoStock', [ApiInventoryController::class, 'getItemsBajoStock']);