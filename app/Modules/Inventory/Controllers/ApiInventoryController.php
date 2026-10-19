<?php

namespace App\Modules\Inventory\Controllers;

use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\categorias;
Use App\Models\inventario\unidadesmedida;
use App\Models\inventario\conversionunidades;
use App\Models\inventario\grupos_insumos;
use App\Models\inventario\precios_personalizados;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockproductossucursal;
use App\Models\parametrizacion\config_local;
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


class ApiInventoryController{

  private static function autorizarComandoInventario():bool{
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario') && userPerfil()>3){
      http_response_code(403);
      echo json_encode(['error'=>['No tienes permiso para modificar el inventario.']]);
      return false;
    }
    return true;
  }


  ////////////////////////////   API   //////////////////////////////
  public static function getAllcategorias(){ //obtener todas las categorias menos las de visible = 0.
    isadmin();
    echo json_encode((new CategoriasInventarioService())->getAllcategorias());
    return;
  }

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

  //obtener productos para paginador
  public static function listSimpleProducts():void{
    header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode([ 'success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try{
            $branchId = filter_var($_SESSION['idsucursal'] ?? null, FILTER_VALIDATE_INT);
            $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
            $categoryId = array_key_exists('categoriaId', $_GET) ? filter_var($_GET['categoriaId'], FILTER_VALIDATE_INT) : null;
            $busqueda = trim((string)($_GET['search'] ?? ''));

            if(!is_int($branchId) || $branchId <= 0 || !is_int($page) || $page <= 0 || ($categoryId !== null && (!is_int($categoryId) || $categoryId <= 0)))
                throw new \InvalidArgumentException('Sucursal o página no válida.');
            $result = (new ProductosInventarioService())->listSimpleProducts($branchId, $busqueda, $page, $categoryId);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(\InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(\Throwable $e){
            error_log('ProductCatalogController: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar los productos.'], JSON_UNESCAPED_UNICODE);
        }
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