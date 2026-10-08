<?php

namespace App\Modules\Cash\Controllers;

use App\Models\parametrizacion\config_local;
use App\services\caja\CajaCierreService;
use App\services\caja\CajaConsultasService;

use App\Core\Routing\Router;
/** Acciones HTTP del cierre de caja; las reglas permanecen en los servicios. */
class detalleOrdenController{

    public static function ordenresumen(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;

    $datos = (new CajaOrdenesService())->prepararResumenOrden($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }

    $router->render('admin/caja/ordenresumen', $datos + ['titulo'=>'Caja', 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function detalleorden(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    $id = $_GET['id'];
    if(!is_numeric($id))return;
    $router->render('admin/caja/detallepedidox', ['titulo'=>'Caja', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  //Enviar orden por email a cliente
  public static function sendOrdenEmailToCustemer(){
    isadmin();
    $alertas = [];

    $id = $_POST['id'];
    $sendEmail = $_POST['email'];
    $path = __DIR__ . "/../../views/templates/plantillafacturaemail.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $factura = facturas::find('id', $id);
      $productos = ventas::idregistros('idfactura', $id);
      $cliente = clientes::find('id', $factura->idcliente);
      $direccion = direcciones::uniquewhereArray(['id'=>$factura->iddireccion, 'idcliente'=>$factura->idcliente]);
      if(!$direccion)$direccion = direcciones::find('id', 1);
      $tarifa = tarifas::find('id', $direccion->idtarifa);
      $vendedor = usuarios::find('id', $factura->idvendedor);
      $sucursal = sucursales::find('id', id_sucursal());
      $lineasencabezado = explode("\n", $sucursal->datosencabezados);
      $sql="SELECT mediospago.* FROM facturas JOIN factmediospago ON factmediospago.id_factura = facturas.id 
            JOIN mediospago ON mediospago.id = factmediospago.idmediopago WHERE facturas.id = {$factura->id};";
      $mediospago = ActiveRecord::camposJoinObj($sql);

      ob_start();
      include $path;
      $html = ob_get_clean();

      $email = new Email($sendEmail, 'Julian Rodriguez', '', '', $html);
      $r = $email->enviarConfirmacion();
    }
    echo json_encode($r);
  }


  /**
   * GET /admin/api/caja/despacharOrden?id={factura}.
   *
   * Atiende la confirmación de entrega enviada desde ordenresumen.ts. El
   * controlador valida HTTP y CajaOrdenesService bloquea la orden, descuenta
   * inventario y registra la entrega dentro de una única transacción.
   */
  public static function despacharOrden(){
    isadmin();
    header('Content-Type: application/json; charset=utf-8');
    $id = self::obtenerIdDocumento();
    if($id === null){
      echo json_encode(['error'=>['El identificador de la orden no es válido.']], JSON_UNESCAPED_UNICODE);
      return;
    }

    $resultado = (new CajaOrdenesService())->despacharOrden($id, id_sucursal());
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
  }


  public static function cambiarEmisor(){
    isadmin();
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )$alertas = cajaService::cambiarEmisor($_POST);
    echo json_encode($alertas);
    return;
  }

}