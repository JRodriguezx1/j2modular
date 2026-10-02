<?php

namespace App\Controllers;

use App\Models\configuraciones\bancos;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\categorias;
Use App\Models\inventario\unidadesmedida;
use App\Models\inventario\conversionunidades;
use App\Models\configuraciones\caja;
use App\Models\inventario\grupos_insumos;
use App\Models\inventario\precios_personalizados;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockproductossucursal;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\services\exportService;
use App\services\inventario\ActualizarCostoInventarioService;
use App\services\inventario\ActualizarPrecioVentaInventarioService;
use App\services\inventario\CategoriasInventarioService;
use App\services\inventario\InsumosInventarioService;
use App\services\inventario\PanelInventarioService;
use App\services\inventario\ProductosInventarioService;
use App\services\inventario\ProveedoresInventarioService;
use App\services\inventario\RecetaService;
use App\services\inventario\RegistrarCompraInventarioService;
use App\services\inventario\ReiniciarInventarioService;
use App\services\inventario\StockInventarioService;
use App\services\inventario\UnidadesMedidaInventarioService;
use App\services\inventarioService;
use App\Core\Routing\Router;

class almacencontrolador{

  private static function autorizarComandoInventario():bool{
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario') && userPerfil()>3){
      http_response_code(403);
      echo json_encode(['error'=>['No tienes permiso para modificar el inventario.']]);
      return false;
    }
    return true;
  }

  public static function index(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $panel = (new PanelInventarioService())->obtenerPanel(id_sucursal());
    $router->render('admin/almacen/index', array_merge(['titulo'=>'Almacen'], $panel, ['alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]));
  }


  public static function categorias(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    //$alertas = usuarios::getAlertas();
    $router->render('admin/almacen/categorias', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_categoria(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new CategoriasInventarioService())->crearCategoria($_POST);
    $categorias = categorias::all();
    $router->render('admin/almacen/categorias', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  public static function productos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    //$productos = productos::all();
    $categorias = categorias::all();
    $unidadesmedida = unidadesmedida::all();
    $producto = new productos;

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>$categorias, 'unidadesmedida'=>$unidadesmedida, 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_producto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $producto = new productos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $resultado = (new ProductosInventarioService())->crearProducto($_POST, $_FILES, $_SERVER, (int)id_sucursal());
      $alertas = $resultado['alertas'];
      $producto = $resultado['producto'];
    }

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>categorias::all(), 'unidadesmedida'=>unidadesmedida::all(), 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function subproductos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproductos = subproductos::all();
    $unidadesmedida = unidadesmedida::all();
    $subproducto = new subproductos;
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproducto'=>$subproducto, 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_subproducto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproducto = new subproductos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $resultado = (new InsumosInventarioService())->crearInsumo($_POST, (int)id_sucursal());
      $alertas = $resultado['alertas'];
      $subproducto = $resultado['subproducto'];
    }
    $sucursales = sucursales::all();
    $unidadesmedida = unidadesmedida::all();
    $subproductos = subproductos::all();
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'subproducto'=>$subproducto, 'alertas'=>$alertas, 'sucursales'=>$sucursales, 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function componer(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $producto = productos::find('id', $_GET['id']);
    $subproductos = subproductos::all();
                                      // segunda tabla,   ON t1.id_subproducto = t2.id
    $subproductosenlazados = productos_sub::unJoinWhereArrayObj(subproductos::class, "id_subproducto", "id", ['productos_sub.id_producto'=>$producto->id]);
    $gi = array_column(grupos_insumos::whereArray(['activo'=>1]), 'nombre', 'id');
    foreach($subproductosenlazados as $value)
      $value->grupos_insumos = $gi[$value->grupos_insumos]??'Insumo por defecto';
    $router->render('admin/almacen/componer', ['titulo'=>'Almacen', 'producto'=>$producto, /*'productos'=>$productos,*/ 'subproductos'=>$subproductos, 'subproductosenlazados'=>$subproductosenlazados, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function ajustarcostos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $productos = productos::all();
    $subproductos = subproductos::all();
    $router->render('admin/almacen/ajustarcostos', ['titulo'=>'Almacen', 'productos'=>$productos, 'subproductos'=>$subproductos, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function compras(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $proveedores = proveedores::all();
    $cajas = caja::idregistros('idsucursalid', id_sucursal());
    $bancos = bancos::all();
    $totalitems = []; //array_merge($productos, $subproductos);
    $router->render('admin/almacen/compras', ['titulo'=>'Almacen', 'proveedores'=>$proveedores, 'totalitems'=>$totalitems, 'categorias'=>$categorias, 'cajas'=>$cajas, 'bancos'=>$bancos, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }
  

  public static function distribucion(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $router->render('admin/almacen/distribucion', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function inventariar(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $router->render('admin/almacen/inventariar', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function unidadesmedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->eliminarUnidad($_POST);
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_unidadmedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->crearUnidad($_POST);

    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function editarunidademedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->actualizarUnidad($_POST);
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function downexcelproducts(Router $router){
    if($_SERVER['REQUEST_METHOD'] !== 'POST' ) //para exportar a excel productos
      return;
    if(!isset($_POST['downexcel']))
      return; 
    $excelproductos = productos::formatExportExcel();
    exportService::exportproducts($excelproductos, 'excelproductos');
  }


  public static function uploadExcel(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $unidadesmedida = unidadesmedida::all();
    $producto = new productos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_FILES['archivoexcel']['name']){ //para importar productos desde excel
        $url_temp = $_FILES["archivoexcel"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES['archivoexcel']['name'], PATHINFO_EXTENSION));
        // Validar extensión
        $extensiones_permitidas = ['xlsx', 'xls', 'csv'];
        if(in_array($extension, $extensiones_permitidas)){
          $alertas = inventarioService::importarExcel($url_temp);
          if(empty($alertas)){
            $alertas['exito'][] = "Extension del archivo no valido";
          }
        }else{
          $alertas['error'][] = "Extension del archivo no valido";
        }
    }

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>$categorias, 'unidadesmedida'=>$unidadesmedida, 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function uploadInsumosExcel(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproductos = subproductos::all();
    $unidadesmedida = unidadesmedida::all();
    $subproducto = new subproductos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_FILES['archivoexcel']['name']){ //para importar productos desde excel
        $url_temp = $_FILES["archivoexcel"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES['archivoexcel']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['xlsx', 'xls', 'csv'];
        if(in_array($extension, $extensiones_permitidas)){
          $alertas = inventarioService::importarInsumosExcel($url_temp);
          if(empty($alertas))
            $alertas['exito'][] = "Extension del archivo no valido";
        }else{
          $alertas['error'][] = "Extension del archivo no valido";
        }
    }
    
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproducto'=>$subproducto, 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function downexcelinsumos(Router $router){
    if($_SERVER['REQUEST_METHOD'] !== 'POST' )return;
    if(!isset($_POST['downexcel']))return; 
    $excelinsumos = subproductos::formatExportExcel();
    exportService::exportproducts($excelinsumos, 'excelinsumos.csv');
  }


  public static function cambioPrecios(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $productos = [];
    $productos = productos::all();
    $router->render('admin/almacen/cambioPrecios', ['titulo'=>'Almacen', 'productos'=>$productos, 'sucursales'=>sucursales::all()]);
  }


  public static function estadisticas(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $router->render('admin/almacen/estadisticas', ['titulo'=>'Almacen', 'alertas'=>$alertas, 'sucursales'=>sucursales::all()]);
  }


  public static function productosParaFormulas(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $productos = productos::all();
    $router->render('admin/almacen/productosParaFormulas', ['titulo'=>'Almacen', 'productos'=>$productos, 'sucursales'=>sucursales::all()]);
  }


  public static function conversionUnidades(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $subproductos = subproductos::all();
    $router->render('admin/almacen/conversionUnidades', ['titulo'=>'Almacen', 'subproductos'=>$subproductos, 'sucursales'=>sucursales::all()]);
  }


  ////////////////////////////   API   //////////////////////////////
  public static function actualizar_categoria(){ //actualizar editar categoria
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode(['error'=>['Metodo no permitido']]);
      return;
    }
    echo json_encode((new CategoriasInventarioService())->actualizarCategoria($_POST));
    return;
  }

  public static function eliminarCategoria(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode(['error'=>['Metodo no permitido']]);
      return;
    }
    echo json_encode((new CategoriasInventarioService())->eliminarCategoria($_POST));
    return;
  }

  public static function allproducts():void{
    isadmin();
    //$productos = productos::all(); 
    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    ////////////// calcular el impuesto como global o como discriminado por producto /////////////////////
    $conflocal = config_local::getParamGlobal();
    $gi = grupos_insumos::sqlLibreIndexKey('SELECT *FROM grupos_insumos WHERE activo = 1;', 'id');
    foreach($productos as $index=>$producto){
      //unset($productos[$index]->descripcion);
      if($conflocal['discriminar_impuesto_por_producto']->valor_final == 0){ //si es 0, es no, toma el impuesto global
        $producto->impuesto = $conflocal['porcentaje_de_impuesto']->valor_final;
      }
      $producto->id = $producto->ID; //esto se hace por la union de las tablas con unJoinWhereArrayObj
      $producto->categoria = categorias::uncampo('id', $producto->idcategoria, 'nombre');
      $producto->preciosadicionales = precios_personalizados::idregistros('idproductoid', $producto->id);
      $producto->precio_original = $producto->precio_venta;
      $producto->insumos = productos_sub::unJoinWhereArrayObj(subproductos::class, 'id_subproducto', 'id', ['id_producto'=>$producto->id]);
      foreach($producto->insumos as $value){
        $value->grupos_insumos = $gi[$value->grupos_insumos]??null;
      }
    }
   echo json_encode($productos);
   return;
  }


  public static function actualizarproducto(){ //actualizar editar producto
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ProductosInventarioService())->actualizarProducto($_POST, $_FILES, $_SERVER, id_sucursal());
    echo json_encode($resultado);
    return;
  }

  public static function eliminarProducto(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ProductosInventarioService())->eliminarProducto($_POST, $_SERVER);
    echo json_encode($resultado);
    return;
  }


  public static function allsubproducts(){
    $subproductos = subproductos::all();  //arreglo de obj = [{},{},{}]
   echo json_encode($subproductos); 
  }


  public static function actualizarsubproducto(){ //actualizar editar sub-producto
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new InsumosInventarioService())->actualizarInsumo($_POST, id_sucursal());
    echo json_encode($resultado);
    return;
  }


  public static function eliminarSubProducto(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new InsumosInventarioService())->eliminarInsumo($_POST, id_sucursal());
    echo json_encode($resultado);
    return;
  }

  //ESTABLECER RENDIMIENTO ESTANDAR DE LA FORMULA DE SALIDA
  public static function setrendimientoestandar(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new RecetaService())->setRendimientoReceta($_POST, id_sucursal());
    echo json_encode($resultado);
  }


  public static function ensamblar(){  //asociar un o unos subproductos a un producto principal
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new RecetaService())->ensamblar($_POST, id_sucursal());
    echo json_encode($resultado);
  }


  public static function desasociarsubproducto(){  //desasociar subproducto de un producto principal
    if(!self::autorizarComandoInventario())return;
    $resultado = (new RecetaService())->desasociarInsumodeReceta((int)($_GET['idproducto'] ?? 0), (int)($_GET['idsubproducto'] ?? 0), id_sucursal());
    echo json_encode($resultado);
    return;
  }


  public static function allUnidadesMedida():void{
    $allUnidadesMedida = unidadesmedida::all();
    echo json_encode($allUnidadesMedida);
    return;
  }


  public static function allConversionesUnidades(){  //Envia todas las equivalencias o conversiones de unidades
    $conversionUnidades = conversionunidades::all();
    echo json_encode($conversionUnidades);
  }


  public static function actualizarcostos(){  //asociar un o unos subproductos a un producto principal
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ActualizarCostoInventarioService())->ejecutar($_POST, id_sucursal());
    echo json_encode($resultado);
  }


  public static function actualizarPreciosVenta(): void{
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ActualizarPrecioVentaInventarioService())->ejecutar($_POST);
    echo json_encode($resultado);
    return;
  }


  public static function totalitems(){  //Envia los productos simples y subproductos en un solo arreglo de objetos
    //$productos = productos::idregistros('tipoproducto', 0);
    $productos = productos::camposJoinObj('SELECT * FROM productos WHERE NOT (tipoproducto = 1 AND tipoproduccion = 0) AND visible = 1;');
    $subproductos = subproductos::all();
    $totalitems = array_merge($productos, $subproductos);
    echo json_encode($totalitems);
  }


  public static function registrarCompra(): void{
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }

    date_default_timezone_set('America/Bogota');
    $resultado = (new RegistrarCompraInventarioService())->ejecutar($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    echo json_encode($resultado);
    return;
  }


  public static function descontarstock(){  //descontar cantidad a inventario
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new StockInventarioService())->descontarStock($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    echo json_encode($resultado);
    return;
  }

  public static function aumentarstock(){  //sumar o ingresar cantidad o produccion a inventario
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }

    if((int)($_POST['construccion']??0) === 1){
      $resultado = (new StockInventarioService())->registrarProduccion($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    }else{
      $resultado = (new StockInventarioService())->aumentarStock($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    }
    echo json_encode($resultado);
  }


  public static function ajustarstock(){  //ajustar o reiniciar inventario
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new StockInventarioService())->ajustarStock($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    echo json_encode($resultado);
    return;
  }


  public static function reiniciarinv():void{
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    if((int)userPerfil() !== 1){
      http_response_code(403);
      echo json_encode(['error'=>['Usuario no tiene permisos.']]);
      return;
    }
    $resultado = (new ReiniciarInventarioService())->ejecutar(id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre']);
    echo json_encode($resultado);
    return;
  }


  public static function cambiarestadoproducto(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new StockInventarioService())->cambiarestadoproducto($_POST, id_sucursal());
    echo json_encode($resultado);
  }


  public static function getStockproductosXsucursal(){
    isadmin();
    echo json_encode((new PanelInventarioService())->obtenerStockPorSucursal());
    return;
  }


  //////////////    GESTION DE PROVEEDORES    //////////////////

  ///////////// procesando la gestion de los proveedores ////////////////
    public static function allproveedores(){  //api llamado desde gestionproveedores.js
      $proveedores = proveedores::all();
      echo json_encode($proveedores);
    }

    public static function crearProveedor(){ //api llamada desde el modulo de gestionproveedores.ts cuando se crea un cliente
        if(!self::autorizarComandoInventario())return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['error'=>['Metodo no permitido']]);
            return;
        }
        echo json_encode((new ProveedoresInventarioService())->crearProveedor($_POST));
    }

    public static function actualizarProveedor(){
        if(!self::autorizarComandoInventario())return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['error'=>['Metodo no permitido']]);
            return;
        }
        echo json_encode((new ProveedoresInventarioService())->actualizarProveedor($_POST));
    }

    public static function eliminarProveedor(){
        if(!self::autorizarComandoInventario())return;
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['error'=>['Metodo no permitido']]);
            return;
        }
        echo json_encode((new ProveedoresInventarioService())->eliminarProveedor($_POST));
    }


    public static function generarBarCode():void{
        $alertas = []; 
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = inventarioService::generarBarCode($_POST);
        }
        echo json_encode($alertas);
        return;
    }


    public static function getItemsBajoStock():void{
      isadmin();
      echo json_encode((new PanelInventarioService())->obtenerItemsBajoStock(id_sucursal()));
      return; 
    }


    public static function crearNuevaConversionUnidad():void{
      if(!self::autorizarComandoInventario())return;
      if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        echo json_encode(['error'=>['Metodo no permitido']]);
        return;
      }
      echo json_encode((new UnidadesMedidaInventarioService())->crearConversion($_POST));
      return;
    }

    public static function eliminarConversionUnidad():void{
      if(!self::autorizarComandoInventario())return;
      echo json_encode((new UnidadesMedidaInventarioService())->eliminarConversion((int)($_GET['id'] ?? 0)));
      return;
    }

}
