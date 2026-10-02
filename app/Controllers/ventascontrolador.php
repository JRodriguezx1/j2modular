<?php

namespace App\Controllers;

use App\classes\Traits\DocumentTrait;
use App\Models\configuraciones\usuarios; //namespace\clase hija
use App\Models\inventario\productos;
use App\Models\inventario\categorias;
use App\Models\configuraciones\mediospago;
use App\Models\clientes\clientes;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\Models\configuraciones\tarifas;
use App\Models\clientes\departments;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\caja;
use App\Models\parametrizacion\config_local;
use App\Models\inventario\productos_sub;
use App\Models\sucursales;
use App\Repositories\ventas\canalVentaRepository;
use App\services\facturacionService;
use App\services\ventasService;

use App\Core\Routing\Router;
use stdClass;

class ventascontrolador{

  use DocumentTrait;

  public static function index(Router $router):void{
    isadmin();
    if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
    $alertas = [];
    $idsucursal = id_sucursal();
    $facturacotz = [];
    $productoscotz = [];
    $num_orden = facturas::calcularNumOrden(id_sucursal());

    if(isset($_GET['id'])){
      $id = $_GET['id'];
      if(!is_numeric($id))return;
      //obtener datos de la factura guardada o cotizacion
      $facturacotz = facturas::find('id', $id);
      if(($facturacotz->cotizacion == 1 || $facturacotz->remision == 1) && $facturacotz->cambioaventa == 0 && $facturacotz->id_sucursal == $idsucursal){
        $productoscotz = ventasService::adjuntarInsumos(ventas::idregistros('idfactura', $id));
        $num_orden = $facturacotz->num_orden;
      }else{ 
        return;
      }
    }

    $productos = productos::SelectProducts_Category_StockXsucursal(); //filtra habilitarventa = 1
    $categorias = categorias::all();
    $mediospago = mediospago::whereArray(['estado'=>1]);
    $clientes = clientes::all();
    $cajas = caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
    $consecutivos = consecutivos::whereArray(['id_sucursalid'=>$idsucursal, 'estado'=>1]);
    //$usuarios = usuarios::whereArray(['idsucursal'=>$idsucursal]);
    $usuarios = usuarios::camposJoinObj("SELECT * FROM usuarios WHERE idsucursal = $idsucursal OR perfil IN (1, 2, 3);");
    $conflocal = config_local::getParamCaja();
    $canalesVentaRepo = new canalVentaRepository();
    $canalesVenta = $canalesVentaRepo->all();
    //validar resoluciiones por rango y por fecha
    $hoy = new \DateTime();
    $resolucionesVencidas = [];
    foreach($consecutivos as $item){
      $diferencia = (int) $item->rangofinal - (int) $item->siguientevalor;
      $condicionRango = $diferencia <= 50;

      $fechaFin = new \DateTime($item->fechafin);
      $diasRestantes = (int) $hoy->diff($fechaFin)->format('%r%a');
      $condicionFecha = $diasRestantes <= 10;

      if($condicionRango || $condicionFecha){
        $resolucionesVencidas[] = $item;
        if($diferencia<=0 || $diasRestantes<=0)$item->vencido = 1;
      }
    }

    $router->render('admin/ventas/index', ['titulo'=>'Ventas', 'num_orden'=>$num_orden, 'facturacotz'=>$facturacotz, 'productoscotz'=>$productoscotz, 'categorias'=>$categorias, 'productos'=>$productos, 'mediospago'=>$mediospago, 'clientes'=>$clientes, 'cajas'=>$cajas, 'consecutivos'=>$consecutivos, 'canalesVenta'=>$canalesVenta, 'usuarios'=>$usuarios, 'conflocal'=>$conflocal, 'resolucionesVencidas'=>$resolucionesVencidas, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  ///////////  API REST llamada desde ventas.ts cuando se procesa un pago  ////////////
  public static function facturar():void{
    isadmin();
    if(!tienePermiso('Habilitar modulo de venta') && userPerfil() > 3)return;

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode(['error'=>['Metodo del endpoint no valido.']]);
      return;
    }

    try {
      // El controlador solo adapta la solicitud HTTP al caso de uso y devuelve
      // su resultado. Las reglas y la transaccion viven en facturacionService.
      $resultado = (new facturacionService())->procesar($_POST, id_sucursal());
    }catch(\Throwable $th){
      // Proteccion de ultimo nivel ante un error no controlado por el servicio.
      $resultado = ['error'=>['Error al procesar la solicitud. '.$th->getMessage()]];
    }
    echo json_encode($resultado);
    return;
  }


  public static function facturarCotizacion(){
    //session_start();
    isadmin();
    if(!tienePermiso('Habilitar modulo de venta') && userPerfil() > 3)return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode(['error'=>['Metodo del endpoint no valido.']]);
      return;
    }

    try {
      $resultado = (new facturacionService())->facturarCotizacionExistente($_POST, id_sucursal());
    } catch (\Throwable $th) {
      $resultado = ['error'=>['Error al procesar la solicitud. '.$th->getMessage()]];
    }
    echo json_encode($resultado);
    return;
  }



  /** Endpoint llamado desde ordenresumen.ts para anular una orden. */
  public static function eliminarOrden():void{
    isadmin();
    if(!tienePermiso('Habilitar modulo de venta') && userPerfil() > 3)return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode(['error'=>['Metodo del endpoint no valido.']]);
      return;
    }

    try {
      $resultado = (new facturacionService())->anularOrden($_POST, id_sucursal());
    } catch (\Throwable $th) {
      $resultado = ['error'=>['Error al anular la orden. '.$th->getMessage()]];
    }
    echo json_encode($resultado);
    return;
  }


  public static function getcotizacion_venta(){
    //session_start();
    isadmin();
    $alertas = [];
    if(isset($_GET['id'])){
      $id = $_GET['id'];
      if(!is_numeric($id))return;
      //obtener datos de la factura guardada o cotizacion
      $facturacotz = facturas::uniquewhereArray(['id'=>$id, 'id_sucursal'=>id_sucursal()]);
      if(($facturacotz->cotizacion == 1 || $facturacotz->remision == 1) && $facturacotz->cambioaventa == 0){
        $productoscotz = ventasService::adjuntarInsumos(ventas::idregistros('idfactura', $id));
        foreach($productoscotz as $value){ //convertir a tipo de dato numero
          $value->valorunidad = (int)$value->valorunidad;
          $value->cantidad = (float)$value->cantidad;
          $value->stock = (float)$value->cantidad;
          $value->subtotal = (float)$value->subtotal;
          $value->base = (float)$value->base;
          $value->impuesto = (int)$value->impuesto;
          $value->valorimp = (float)$value->valorimp;
          $value->descuento = (int)$value->descuento;
          $value->total = (float)$value->total;
        }
        $alertas['exito'][] = "Cotizacion cargada con exito";
        $alertas['factura'] = $facturacotz;
        $alertas['productos'] = $productoscotz;
      }else{ 
        $alertas['error'][] = "No es posible obtener datos de factura";
      }
    }
    echo json_encode($alertas);
  }



  public static function detalleProductoCompuesto(){
      isadmin();
      $idsucursal = id_sucursal();
      $idproducto = $_GET['idproducto'];
      $idfactura = $_GET['idfactura'];
      if(!is_numeric($idproducto) || !is_numeric($idfactura))return;
      $productoCompuesto = ventas::uniquewhereArray(['idfactura'=>$idfactura, 'idproducto'=>$idproducto, 'tipoproducto'=>1]);
      $sql = "SELECT x.id, x.id_producto, x.id_subproducto, TRUNCATE((x.cantidadsubproducto*$productoCompuesto->cantidad)/$productoCompuesto->rendimientoestandar, 3) as cantidadcalculada, 
              TRUNCATE((x.costo*$productoCompuesto->cantidad)/$productoCompuesto->rendimientoestandar, 3) as costo, sp.nombre, sp.sku, sp.precio_compra, u.nombre as unidadmedida, u.simbolo, si.stock as disponibilidad, si.stockminimo 
              FROM productos_sub x
              INNER JOIN subproductos sp ON x.id_subproducto = sp.id
              INNER JOIN unidadesmedida u ON sp.id_unidadmedida = u.id
              INNER JOIN stockinsumossucursal si ON sp.id = si.subproductoid
              WHERE x.id_producto = $idproducto AND si.sucursalid = $idsucursal;";
      $detalleProducto = productos_sub::camposJoinObj($sql);
      echo json_encode($detalleProducto);
      return;
    }

}
