<?php

namespace App\Controllers;

use App\classes\Traits\DocumentTrait;
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
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\caja;
use App\Models\factimpuestos;
use App\Models\felectronicas\adquirientes;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\Repositories\ventas\canalVentaRepository;
use App\Models\inventario\stockproductossucursal;
use App\services\comisionesService;
use App\services\contableService;
use App\services\ventasService;
use App\Core\Routing\Router;  //namespace\clase
 
class modorapidocontrolador{

    use DocumentTrait;

    public static function index(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
        $alertas = [];
        $idsucursal = id_sucursal();
        $num_orden = facturas::calcularNumOrden(id_sucursal());
        $categorias = categorias::all();
        $mediospago = mediospago::whereArray(['estado'=>1]);
        $clientes = clientes::all();
        $tarifas = tarifas::all();
        $cajas = caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
        $consecutivos = consecutivos::whereArray(['id_sucursalid'=>$idsucursal, 'estado'=>1]);
        $departments = departments::all();
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

        $router->render('admin/modorapido/index', ['titulo'=>'Ventas', 'num_orden'=>$num_orden, 'categorias'=>$categorias, 'mediospago'=>$mediospago, 'clientes'=>$clientes, 'tarifas'=>$tarifas, 'cajas'=>$cajas, 'consecutivos'=>$consecutivos, 'canalesVenta'=>$canalesVenta, 'departments'=>$departments, 'conflocal'=>$conflocal, 'resolucionesVencidas'=>$resolucionesVencidas, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


  ///////////  API REST llamada desde ventas.ts cuando se procesa un pago  ////////////
  public static function facturarModorapido(){
    $comisionServicio = new comisionesService();
    $contableService = new contableService();
    isadmin();
    if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
    date_default_timezone_set('America/Bogota');
    $getDB = facturas::getDB();
    $carrito = json_decode($_POST['carrito']); //[{id: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}]
    $mediospago = json_decode($_POST['mediosPago']); //[{id: "1", id_factura: "3", idmediopago: "1", valor: "400050"}, {}]
    $factimpuestos = json_decode($_POST['factimpuestos']);
    $valoresCredito = json_decode($_POST['valoresCredito']);
    $datosAdquiriente = json_decode($_POST['datosAdquiriente']);
    $factura = new facturas($_POST);
    $factura->id_sucursal = id_sucursal();
    $venta = new ventas();
    $factmediospago = new factmediospago();
    $detalleimpuestos = new factimpuestos();
    $alertas = [];

    
    //////// EXTRAER LOS PRODUCTOS ACTUALIZADOS, ELIMINADOS O NUEVOS DEL CARRITO POR SI SE ACTUALIZA LA COTIZACION ////////
    $carritoupdate=[];
    $carritoinsert=[];
    $idsProductsupdate=[];
    $idsProductos = [];
    foreach($carrito as $value){ //si en carrito un id, viene vacio o null es porque desde el front se agrego y el front no save el id de la tabla venta
      $idsProductos[] = $value->idproducto; //obtener los ids de los productos enviados desde el front
      $mapCarrito[$value->idproducto] = $value->cantidad;  //se usa para saber si se vende sin stock
      if(!empty($value->id)){
        $carritoupdate[] = clone $value;
        $idsProductsupdate[] = $value->id;
      }else{
        $carritoinsert[] = $value;
      }
    }


    $conflocal = config_local::getParamCaja();
    $inventarioVenta = ventasService::prepararInventarioXVenta($carrito, id_sucursal());

    //////// CALCULAR PRODUCTOS AGOTADOS /////////
    if($conflocal['permitir_venta_de_productos_sin_stock']->valor_final == 0){ //no permitir vender sin stock
      $erroresStock = ventasService::validarDisponibilidadInventario($inventarioVenta, id_sucursal());
      if(!empty($erroresStock)){
        $alertas['error'] = $erroresStock;
        echo json_encode($alertas);
        return;
      }
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST' ){
      $alertas['error'][] = "Metodo del Endpoint no es valido";
      echo json_encode($alertas);
      return;
    }

    //obtener el ultimo cierre de caja abierto segun caja seleccionada
    $ultimocierre = cierrescajas::uniquewhereArray(['estado'=>0, 'idcaja'=>$_POST['idcaja'], 'idsucursal_id'=>id_sucursal()]);
    if(!$ultimocierre){
      echo json_encode(['error'=>['Error al procesar la solicitud, verifica el cierre de caja']]);
      return;
    }
    $factura->idcierrecaja = $ultimocierre->id;
    //calcular ultimo num_orden
    $factura->num_orden = facturas::calcularNumOrden(id_sucursal());
    $getDB->begin_transaction();
    try {
      $mensajeExito = '';
      $dataInvoice = null;

      //calcular siguiente consecutivo solo para facturas que se paguen
      if($_POST['estado']=='Paga'){
        $consecutivo = consecutivos::findForUpdate('id', $_POST['idconsecutivo']);
        $factura->num_consecutivo = $consecutivo->siguientevalor;
        $factura->prefijo = $consecutivo->prefijo;
        $factura->abono = $valoresCredito->abonoinicial??0;
        $factura->habilitada = 1;
        $r = $factura->crear_guardar();
        $consecutivo->siguientevalor += 1;
        $c = $consecutivo->actualizar();
        $fe = self::createInvoiceElectronic($carrito, $datosAdquiriente, $factura->idconsecutivo, $r[1], $factura->num_consecutivo, $mediospago, $factura->descuento, $factura->valortarifa, $factura->observacion);  //llamada al trait para crear el json y guardar la FE en DB

        /////////// calcular cantidad de facturas y discriminar por tipo
        // Y
        ///////// calcular ventas en efectivo, total descuentos, total ingreso de ventas
        ventasService::datosDelCierreCajaXVenta($ultimocierre, $factura, $mediospago, $factimpuestos, $r, $valoresCredito);
        //////////// Guardar los productos de la venta en tabla ventas //////////////
        foreach($carrito as $obj){
          $obj->dato1 = '';
          $obj->dato2 = '';
          $obj->idfactura = $r[1];
          if($obj->idproducto<0&&$obj->idcategoria<0&&$obj->id==''){ //para productos "Otros"
            $obj->id = 1;
            $obj->idproducto = 1;
            $obj->idcategoria = 1;
          }
        }

        //$venta->crear_varios_reg_arrayobj($carrito);  //crear los productos de la factura en tabla venta (detalle de los productos de la factura de venta)
        ventasService::guardarLineasVenta($carrito, false);
        if(!empty($mediospago))$factmediospago->crear_varios_reg_arrayobj($mediospago); //crear los distintos metodos de pago en tabla factmediospago
        if(!empty($factimpuestos))$detalleimpuestos->crear_varios_reg_arrayobj($factimpuestos);

        //$resultArray = ventasService::reducirIventarioXVenta($carrito);  //$resultArray['productosSimples'] y $resultArray['productosCompuestos']
        $inventarioActualizado = ventasService::descontarInventarioXVenta($inventarioVenta, id_sucursal(), 'venta', 'descuento de unidades por venta', false);

        $mensajeExito = "Pago procesado con exito";
        $dataInvoice = ventasService::dataInvoiceForPrinterServer($datosAdquiriente, $factura, $consecutivo);

      }else{ //Si es cotizacion o remision
        $r = $factura->crear_guardar();
        //////////// Guardar los productos de la venta en tabla ventas //////////////
        foreach($carrito as $obj){
          $obj->dato1 = '';
          $obj->dato2 = '';
          $obj->idfactura = $r[1];
          if($obj->idproducto<0&&$obj->idcategoria<0&&$obj->id==''){  //para productos "Otros"
            $obj->id = 1;  //este es el id de Otros.
            $obj->idproducto = 1;
            $obj->idcategoria = 1;
          }
        }
        //$venta->crear_varios_reg_arrayobj($carrito);
        //ventasService::actualizarLineasCotizacion($carritoupdate, $carritoinsert, (int)$r[1]);
        ventasService::guardarLineasVenta($carrito, false);
        if($factura->estado == 'Guardado'){
          $ultimocierre->totalcotizaciones += 1;
          $mensajeExito = "Cotizacion guardada con exito";
        }elseif($factura->estado == 'Remision'){
          $mensajeExito = "Remision generada con exito";
        }else{
          throw new \RuntimeException('Estado de la solicitud no valido.');
        }
        $ultimocierre->actualizar();
      }

      if($_POST['estado']=='Paga'){
        // El movimiento de caja y la comision comparten la misma conexion,
        // por lo que forman parte de esta transaccion.
        $numFactura = $factura->prefijo.$factura->num_consecutivo;
        $movimientoCaja = $contableService->createMovimiento([
          'fk_tipo_movimientocaja'=>$factura->tipoventa=='Contado'?1:11,
          'fk_tipo_documento'=>1,
          'id_documento'=>$r[1],
          'fk_tipo_tercero'=>1,
          'id_tercero'=>$factura->idcliente,
          'fk_caja'=>$factura->idcaja,
          'fk_usuario'=>$factura->idvendedor,
          'naturaleza'=>'I',
          'numero_documento'=>$numFactura,
          'num_orden'=>null,
          'valor'=>$factura->tipoventa=='Contado'?$factura->total:$factura->abono,
          'concepto'=>$factura->tipoventa=='Contado'?'PAGO DE CONTADO':'ABONO INICIAL',
          'observacion'=>$factura->tipoventa=='Contado'?'PAGO DE CONTADO A FACTURA':'ABONO INICIAL A FACTURA CREDITO'
        ]);
        if(!($movimientoCaja[0]??false))
          throw new \RuntimeException('No fue posible registrar el movimiento de caja.');

        if($factura->valorgananciauser>0)
          $comisionServicio->crearComision($r[1], $factura->idvendedor, $factura->total, $factura->porcentgananciauser, $factura->valorgananciauser);
      }

      $getDB->commit();

      $alertas['exito'][] = $mensajeExito;
      if($_POST['estado']=='Paga'){
        $alertas['idfactura'] = $r[1];
        $alertas['dataInvoice'] = $dataInvoice;
      }

      echo json_encode($alertas);
      return;
    } catch (\Throwable $th) {
      $getDB->rollback();
      $alertas = ['error'=>["Error al procesar la solicitud. >>{$th->getMessage()}"]];
      echo json_encode($alertas);
      return;
    }


  }


}
