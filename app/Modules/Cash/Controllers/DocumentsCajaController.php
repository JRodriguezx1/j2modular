<?php

namespace App\Modules\Cash\Controllers;

use App\classes\Email;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Models\configuraciones\usuarios;
use App\Models\ventas\facturas;
use App\Models\clientes\clientes;
use App\Models\clientes\direcciones;
use App\Models\configuraciones\tarifas;
use App\Models\ventas\ventas;
use App\services\cajaService;
use App\services\caja\CajaDocumentosService;
use App\services\caja\CajaOrdenesService;

use App\Core\Routing\Router;

/** Acciones HTTP del cierre de caja; las reglas permanecen en los servicios. */
class DocumentsCajaController{

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


    /**
   * GET /admin/caja/pedidosguardados.
   *
   * Renderiza las cotizaciones pendientes de la sucursal. La consulta se
   * delega a CajaOrdenesService y el controlador conserva permisos y vista.
   */
  public static function pedidosguardados(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    $pedidosguardados = (new CajaOrdenesService())->listarPedidosGuardados(id_sucursal());
    $router->render('admin/caja/pedidosguardados', ['titulo'=>'Caja', 'pedidosguardados'=>$pedidosguardados, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  /**
   * GET /admin/caja/despachosPendientes.
   *
   * Renderiza las órdenes pendientes de entrega. La consulta limitada a la
   * sucursal se delega a CajaOrdenesService.
   */
  public static function despachosPendientes(Router $router){
    isadmin();
    //if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    $despachosPendientes = (new CajaOrdenesService())->listarDespachosPendientes(id_sucursal());
    $router->render('admin/caja/despachosPendientes', ['titulo'=>'Caja', 'despachosPendientes'=>$despachosPendientes, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function printfacturacarta(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararFacturaCarta($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printFacturaCarta', $datos + ['titulo'=>'Impresion factura', 'user'=>$_SESSION]);
  }

  /**
   * GET /printcotizacion?id={factura}.
   *
   * Es abierto desde ordenresumen.ts. CajaDocumentosService comparte el mismo
   * detalle validado de la factura y sus relaciones.
   */
  public static function printcotizacion(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararCotizacion($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printcotizacion', $datos + ['titulo'=>'Impresion cotizacion', 'user'=>$_SESSION]);
  }

  /**
   * GET /printdetallecierre?id={cierre}.
   *
   * Es abierto desde cerrarcaja.ts y detallecierrecaja.ts. El servicio combina
   * el resumen financiero compartido con el encabezado de la sucursal.
   */
  public static function printdetallecierre(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararDetalleCierre($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printdetallecierre', $datos + ['titulo'=>'detalle cierre Caja', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

    //////////////////////     API      /////////////////////////


    /**
   * GET /admin/api/mediospagoXfactura?id={factura}.
   *
   * Devuelve a caja.ts los pagos registrados para una factura. La consulta y
   * la validación de sucursal se delegan a CajaOrdenesService.
   */
    public static function mediospagoXfactura(){
        isadmin();
        header('Content-Type: application/json; charset=utf-8');
        $id = self::obtenerIdDocumento();
        if($id === null){
        http_response_code(400);
        echo json_encode(['error'=>'El identificador de la factura no es válido.']);
        return;
        }

        $factmediospago = (new CajaOrdenesService())->obtenerMediosPagoFactura($id, id_sucursal());
        if($factmediospago === null){
        http_response_code(404);
        echo json_encode(['error'=>'Factura no encontrada.']);
        return;
        }
        echo json_encode($factmediospago);
    }


    /**
     * POST /admin/api/cambioMedioPago.
     *
     * Recibe desde caja.ts la nueva distribución del pago. Toda validación de
     * negocio y escritura transaccional se delega a CajaOrdenesService.
     */
    public static function cambioMedioPago(){
        isadmin();
        header('Content-Type: application/json; charset=utf-8');

        $idfactura = filter_var($_POST['id_factura'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        $nuevosmediospago = json_decode($_POST['nuevosMediosPago'] ?? '', true);
        if($idfactura === false || !is_array($nuevosmediospago) || json_last_error() !== JSON_ERROR_NONE){
        echo json_encode(['error'=>['Los datos para cambiar los medios de pago no son válidos.']]);
        return;
        }

        $resultado = (new CajaOrdenesService())->cambiarMediosPagoFactura((int)$idfactura, $nuevosmediospago, id_sucursal());
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
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


  /**
   * POST /admin/api/eliminarPedidoGuardado.
   *
   * Solicita la baja lógica de una cotización desde pedidosguardados.ts. La
   * transición de estado y el contador del cierre pertenecen al servicio.
   */
  public static function eliminarPedidoGuardado(){
    isadmin();
    header('Content-Type: application/json; charset=utf-8');
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    if($id === false){
      echo json_encode(['error'=>['El identificador de la cotizacion no es válido.']]);
      return;
    }

    $resultado = (new CajaOrdenesService())->eliminarPedidoGuardado((int)$id, id_sucursal());
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
  }

  /**
   * GET /admin/api/getInvoice?id={factura}.
   *
   * Devuelve el DTO utilizado por las impresoras POS de caja.ts y
   * detallecierrecaja.ts. La consulta y transformación pertenecen a
   * CajaDocumentosService; aquí solo se adapta la solicitud HTTP.
   */
  public static function getInvoice(){
    isadmin();
    header('Content-Type: application/json; charset=utf-8');
    $id = self::obtenerIdDocumento();
    if($id === null){
      http_response_code(400);
      echo json_encode(['error'=>'El identificador de la factura no es válido.']);
      return;
    }

    $result = (new CajaDocumentosService())->prepararInvoiceParaImpresion($id, id_sucursal());
    if(!$result){
      http_response_code(404);
      echo json_encode(['error'=>'Factura no encontrada.']);
      return;
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  /** Normaliza el parámetro id compartido por las tres rutas documentales. */
  private static function obtenerIdDocumento(): ?int{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    return $id === false ? null : (int)$id;
  }

  /** Devuelve una respuesta controlada cuando el documento no está en alcance. */
  private static function responderDocumentoNoEncontrado(): void{
    http_response_code(404);
    echo 'Documento no encontrado.';
  }

}