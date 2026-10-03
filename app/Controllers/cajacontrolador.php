<?php

namespace App\Controllers;

use App\classes\Email;
use App\Models\ActiveRecord;
use App\Models\configuraciones\usuarios;
use App\Models\ventas\facturas;
use App\Models\clientes\clientes;
use App\Models\clientes\direcciones;
use App\Models\configuraciones\tarifas;
use App\Models\ventas\ventas;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\services\cajaService;
use App\services\caja\CajaCierreService;
use App\services\caja\CajaConsultasService;
use App\services\caja\CajaDocumentosService;
use App\services\caja\CajaMovimientosService;
use App\services\caja\CajaOrdenesService;
use App\services\caja\CajaReportesService;
use App\services\caja\CategoriasGastoService;
use MVC\Router;

class cajacontrolador{

  /**
   * GET /admin/caja.
   *
   * Renderiza el panel general. La preparación de cierres abiertos, facturas,
   * medios de pago y catálogos se delega a CajaConsultasService.
  */
  public static function index(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $datos = (new CajaConsultasService())->obtenerPanelCaja( id_sucursal(), (int)$_SESSION['perfil'], (int)$_SESSION['id'] );

    $router->render('admin/caja/index', $datos + ['titulo'=>'Caja', 'sucursal'=>nombreSucursal(), 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  /**
   * GET /admin/caja/cerrarcaja.
   *
   * Renderiza el cierre abierto de la caja principal. El resumen financiero se
   * obtiene desde CajaConsultasService y se comparte con las demás consultas.
  */
  public static function cerrarcaja(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $datos = (new CajaConsultasService())->obtenerCierrePrincipal(id_sucursal());

    $router->render('admin/caja/cerrarcaja', $datos + ['titulo'=>'Caja', 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  
  /**
   * POST /admin/caja/ingresoGastoCaja.
   *
   * Recibe el formulario de gastos e ingresos de views/admin/caja/index.php.
   * El controlador conserva la autorización, el archivo subido y el render;
   * CajaMovimientosService ejecuta la apertura y el movimiento transaccional.
   */
  public static function ingresoGastoCaja(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    date_default_timezone_set('America/Bogota');

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $comprobante = ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>[]];
      if(($_POST['operacion'] ?? '') === 'gasto')
        $comprobante = self::guardarComprobanteGasto($_FILES['imgcomprobante'] ?? null);

      if($comprobante['alertas']){
        $alertas = ['error'=>$comprobante['alertas']];
      }else{
        $alertas = (new CajaMovimientosService())->registrarMovimiento($_POST, id_sucursal(), (int)$_SESSION['id'], $comprobante['ruta']);

        // El archivo acaba de crearse para esta solicitud. Si el comando no
        // se confirmó, se elimina para no dejar comprobantes huérfanos.
        if(isset($alertas['error']) && $comprobante['rutaAbsoluta'] && file_exists($comprobante['rutaAbsoluta']))
          unlink($comprobante['rutaAbsoluta']);
      }
    }

    $datosPanel = (new CajaConsultasService())->obtenerPanelCaja(id_sucursal(), (int)$_SESSION['perfil'], (int)$_SESSION['id']);
    $router->render('admin/caja/index', $datosPanel + ['titulo'=>'Caja', 'sucursal'=>nombreSucursal(), 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  /**
   * Valida y almacena el comprobante opcional recibido con un gasto.
   *
   * Sólo lo llama ingresoGastoCaja(). Devuelve la ruta relativa que se guarda
   * en la base de datos y la ruta absoluta para poder limpiar el archivo si el
   * caso de uso falla. Los ingresos no pasan por esta función.
   */
  private static function guardarComprobanteGasto(?array $archivo): array{
    if(!$archivo || (int)($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>[]];
    if((int)($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible cargar el comprobante.']];
    if((int)($archivo['size'] ?? 0) > 31000000)
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['El comprobante no puede superar los 31 MB.']];

    $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string)$archivo['tmp_name']);
    $extensiones = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
    if(!isset($extensiones[$mime]))
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['Seleccione una imagen en formato jpeg o png.']];

    $subdominio = preg_replace('/[^a-zA-Z0-9_-]/', '', explode('.', (string)($_SERVER['HTTP_HOST'] ?? 'cliente'))[0]) ?: 'cliente';
    $documentRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2).'/public'), '/\\');
    $directorio = $documentRoot.'/build/img/'.$subdominio.'/comprobantes';
    if(!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio))
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible preparar la carpeta de comprobantes.']];

    $rutaRelativa = $subdominio.'/comprobantes/'.bin2hex(random_bytes(12)).'.'.$extensiones[$mime];
    $rutaAbsoluta = $documentRoot.'/build/img/'.$rutaRelativa;
    if(!move_uploaded_file((string)$archivo['tmp_name'], $rutaAbsoluta))
      return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible guardar el comprobante.']];

    return ['ruta'=>$rutaRelativa, 'rutaAbsoluta'=>$rutaAbsoluta, 'alertas'=>[]];
  }


  /**
   * GET|POST /admin/caja/categoriaGasto.
   *
   * GET muestra el catálogo; POST recibe el formulario de eliminación de
   * views/admin/caja/categoriagasto.php. Las reglas se delegan al servicio.
   */
  public static function categoriaGasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new CategoriasGastoService())->eliminarCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }

  /**
   * POST /admin/caja/crear_categoriaGasto.
   *
   * Es llamado por src/ts/caja/categoriasgastos.ts al confirmar el formulario
   * de creación. El controlador conserva HTTP y renderizado.
   */
  public static function crear_categoriaGasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = (new CategoriasGastoService())->crearCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }

  /**
   * POST /admin/caja/editarcategoriagasto.
   *
   * Es llamado por src/ts/caja/categoriasgastos.ts al confirmar una edición.
   * El servicio valida existencia, protección del catálogo base y duplicados.
   */
  public static function editarcategoriagasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = (new CategoriasGastoService())->editarCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }

  /**
   * Render compartido por listado, creación, edición y eliminación.
   * Centraliza las variables requeridas por views/admin/caja/categoriagasto.php.
   */
  private static function renderCategoriasGasto(Router $router, array $alertas): void{
    $categoriasgastos = (new CategoriasGastoService())->listarCategorias();
    $router->render('admin/caja/categoriagasto', ['titulo'=>'Caja', 'conflocal'=>config_local::getParamGlobal(), 'categoriasgastos'=>$categoriasgastos, 'sucursal'=>nombreSucursal(), 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  /**
   * GET /admin/caja/zetadiario.
   *
   * Muestra los cierres históricos disponibles y el acceso al consolidado de
   * hoy. El listado se prepara en CajaReportesService.
   */
  public static function zetadiario(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $datos = (new CajaReportesService())->obtenerIndiceZ(id_sucursal());
    $router->render('admin/caja/zetadiario', $datos + ['titulo'=>'Caja', 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  /**
   * GET /admin/caja/fechazetadiario?id={selector}.
   *
   * Recibe -1 para cajas abiertas, 0 para consulta por rango o el id positivo
   * de un cierre histórico. El servicio construye el contrato de la vista.
   */
  public static function fechazetadiario(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $selector = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if($selector === false || (int)$selector < -1)return;

    $datos = (new CajaReportesService())->obtenerDetalleZ((int)$selector, id_sucursal());
    $alertas = [];
    if(isset($datos['error'])){
      $alertas['error'][] = $datos['error'];
      unset($datos['error']);
    }

    $router->render('admin/caja/fechazetadiario', $datos + ['titulo'=>'Caja', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  /**
   * GET /admin/caja/ultimoscierres.
   *
   * Lista cierres finalizados. La consulta por sucursal y orden descendente se
   * delega a CajaConsultasService.
  */
  public static function ultimoscierres(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $ultimoscierres = (new CajaConsultasService())->listarCierresFinalizados(id_sucursal());
    $router->render('admin/caja/ultimoscierres', ['titulo'=>'Caja', 'ultimoscierres'=>$ultimoscierres, 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  /**
   * GET /admin/caja/detallecierrecaja?id={id}.
   *
   * Renderiza un cierre finalizado. El resumen financiero reutilizable se
   * obtiene desde CajaConsultasService.
  */
  public static function detallecierrecaja(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = $_GET['id'];
    if(!is_numeric($id))return;

    $datos = (new CajaConsultasService())->obtenerDetalleCierreFinalizado((int)$id, id_sucursal());

    if($datos === null)return;

    $router->render('admin/caja/detallecierrecaja', $datos + ['titulo'=>'Caja', 'alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
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


  public static function trasladosRetirosDinero(Router $router){
    isadmin();
    //if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    $router->render('admin/caja/trasladosRetiros', ['titulo'=>'Caja', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
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


  /**
   * GET /admin/caja/ordenresumen?id={factura}.
   *
   * Renderiza el detalle operativo de una orden. La preparación de factura,
   * relaciones y catálogos se delega a CajaOrdenesService.
   */
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
   * GET /printfacturacarta?id={factura}.
   *
   * Es abierto desde caja.ts y ordenresumen.ts. La preparación y validación de
   * sucursal se delegan a CajaDocumentosService; aquí sólo se renderiza.
   */
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


  //////////////////////////----    API      ----////////////////////////////////

  /**
   * POST /admin/api/declaracionDinero.
   *
   * Es llamado desde src/ts/caja/cerrarcaja.ts al editar la declaración de un
   * medio de pago. La regla de crear, actualizar o eliminar se delega al
   * servicio; esta acción conserva autorización, entrada HTTP y salida JSON.
   */
  public static function declaracionDinero(){
    isadmin();
    $resultado = (new CajaCierreService())->registrarDeclaracion($_POST, id_sucursal());
    echo json_encode($resultado);
  }

  /**
   * POST /admin/api/arqueocaja.
   *
   * Es llamado desde src/ts/caja/cerrarcaja.ts al confirmar las denominaciones
   * contadas. El servicio crea el arqueo o reemplaza sus valores si ya existe.
   */
  public static function arqueocaja(){   
    isadmin();
    $resultado = (new CajaCierreService())->registrarArqueo($_POST, id_sucursal());
    echo json_encode($resultado);
  }

  /**
   * POST /admin/api/cierrecajaconfirmado.
   *
   * Es llamado desde src/ts/caja/cerrarcaja.ts al aceptar el cierre. El
   * servicio ejecuta de forma transaccional el cierre actual, la apertura del
   * siguiente período y el eventual ingreso de base automática.
   */
  public static function cierrecajaconfirmado(){
    isauth();
    date_default_timezone_set('America/Bogota');
    $resultado = (new CajaCierreService())->confirmarCierre($_POST, id_sucursal(), (int)$_SESSION['id'], (string)$_SESSION['nombre'], config_local::getParamCaja());
    echo json_encode($resultado);
  }


  /**
   * POST /admin/api/datoscajaseleccionada.
   *
   * Es llamado por src/ts/caja/cerrarcaja.ts cuando el usuario cambia la caja
   * que desea revisar. La construcción del resumen se delega al servicio.
   */
  public static function datoscajaseleccionada(){
    isadmin();
    $cajaId = filter_var($_POST['idcaja'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);

    if($cajaId === false){
      echo json_encode(['error'=>['La caja seleccionada no es válida.']]);
      return;
    }

    $datos = (new CajaConsultasService())->obtenerCajaSeleccionada((int)$cajaId, id_sucursal());

    if($datos === null){
      echo json_encode(['error'=>['No existe un cierre abierto para la caja seleccionada.']]);
      return;
    }

    echo json_encode(['exito'=>['Cambio de caja.']] + $datos);
  }


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