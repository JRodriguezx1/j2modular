<?php

namespace App\Controllers;

use App\classes\Email;
use App\Models\ActiveRecord;
use App\Models\configuraciones\usuarios; //namespace\clase hija
use App\Models\inventario\productos;
use App\Models\inventario\categorias;
use App\Models\configuraciones\mediospago;
use App\Models\caja\factmediospago;
use App\Models\clientes\clientes;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\Models\configuraciones\tarifas;
use App\Models\caja\cierrescajas;
use App\Models\clientes\departments;
use App\Models\configuraciones\bancos;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\tipofacturador;
use App\Models\factimpuestos;
use App\Models\felectronicas\adquirientes;
use App\Models\impuestos;
use App\Models\parametrizacion\config_local;
use App\Models\inventario\productos_sub;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\subproductos;
use App\Models\sucursales;
use App\Repositories\ventas\canalVentaRepository;
use App\services\cajaService;
use App\services\comisionesService;
use App\services\creditosService;
use App\services\stockService;
use App\services\whatsAppService;
//use App\Models\configuraciones\negocio;
use MVC\Router;  //namespace\clase
use stdClass;

class comisionescontrolador{


  public static function index(Router $router):void{
    isadmin();
    if(userPerfil()>3)header('Location: /admin/perfil');
    $idsucursal = id_sucursal();
    $cajas = caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
    $bancos = bancos::all();
    $comisionServicio = new comisionesService();
    $usuarios = usuarios::whereArray(['idsucursal'=>$idsucursal]);
    $widgets = $comisionServicio->getWidgets($idsucursal);
    $conflocal = config_local::getParamCaja();
    $router->render('admin/comisiones/index', ['titulo'=>'Comisiones', 'cajas'=>$cajas, 'bancos'=>$bancos, 'widgets'=>$widgets, 'usuarios'=>$usuarios, 'conflocal'=>$conflocal, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  //////////////////////    API     /////////////////////
  public static function comisionesXUser():void{
    $comisionServicio = new comisionesService();
    isadmin();
    $fechainicio = $_POST['fechainicio']; 
    $fechafin = $_POST['fechafin'];
    $idempleado = $_POST['idempleado'];

    $comisionesUser = $comisionServicio->comisionesXUser(id_sucursal(), $idempleado, $fechainicio, $fechafin);
    echo json_encode($comisionesUser);
    return;
  }


  public static function liquidarComision():void{
    $comisionServicio = new comisionesService();
    isadmin();
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      try {
        $r = $comisionServicio->liquidarComision($_POST);
        if(isset($r['error'])){
          echo json_encode($r);
          return;
        }
        $alertas['exito'][] = "Liquidacion aplicada en sistema.";
        $alertas['id'] = $r[1];
      } catch (\Throwable $th) {
        $alertas['error'][] = "Error al actualizar el credito. {$th->getMessage()}";
      }
    }
    echo json_encode($alertas);
    return;
  }

  
  public static function eliminarMovimientoComision():void{
    $comisionServicio = new comisionesService();
    isadmin();
    $id = $_GET['id'];
    if(!is_numeric($id))return;
    $r = $comisionServicio->eliminarMovimientoComision($id);
    echo json_encode($r);
    return;
  }


  public static function detalleFacturaComision(){
    isadmin();
    $id = $_GET['id'];
    if(!is_numeric($id))return;
    $array = cajaService::detalleVenta($id);
    if(count($array)>0){
      $datos['exito'][] = 'Detalle de venta OK';
      $datos['factura'] = $array['factura'];
      $datos['productos'] = $array['productos'];
      $datos['vendedor'] = $array['vendedor'];
    }else{
       $datos['error'][] = 'Error al consultar detalle de venta';
    }
    echo json_encode($datos);
    return;
  }


}