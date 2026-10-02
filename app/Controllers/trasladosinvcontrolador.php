<?php

namespace App\Controllers;

Use App\Models\inventario\unidadesmedida;
use App\Models\inventario\traslado_inv;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\services\trasladosinventario\TrasladosConsultaService;
use App\services\trasladosinventario\TrasladosInventarioService;
use App\services\trasladosinventario\TrasladosOrdenService;
use App\services\whatsAppService;
use MVC\Router;  //namespace\clase

/*
 * ÓRDENES EN LAS QUE LA SUCURSAL ACTUAL FIGURA COMO DESTINO
 *
 * Se filtra:
 * id_sucursaldestino = id_sucursal()
 *
 * - tipo = 'Solicitud':
 *   Otra sucursal, registrada como origen, solicita mercancía a la sucursal
 *   actual. La sucursal actual debe despacharla cuando la orden esté pendiente.
 *
 * - tipo = 'Salida':
 *   Otra sucursal está enviando mercancía a la sucursal actual.
 *   La sucursal actual debe recibirla cuando la orden esté en tránsito.
 */

/*
 * ÓRDENES EN LAS QUE LA SUCURSAL ACTUAL FIGURA COMO ORIGEN
 *
 * Se filtra:
 * id_sucursalorigen = id_sucursal()
 *
 * - tipo = 'Solicitud':
 *   La sucursal actual solicitó mercancía a la sucursal destino.
 *   Debe esperar a que la sucursal destino la despache y, cuando la orden
 *   esté en tránsito, confirmar su recepción.
 *
 * - tipo = 'Salida':
 *   La sucursal actual está enviando mercancía a otra sucursal.
 *   Debe realizar el despacho cuando la orden esté pendiente.
 */

class trasladosinvcontrolador{


//VER DETALLE DE SOLICITUDES RECIBIDAS DE QUE VIENE MERCANCIA O DE QUE DEBO DESPACHAR
  public static function solicitudesrecibidas(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $sucursales = sucursales::idregistros('estado', 1);
    $result = (new TrasladosConsultaService())->listarComoDestino((int)id_sucursal());
    $solicitudesrecividas = $result['ordenes'];
    $pendientes = $result['count']['pendiente'];
    $aprobadas = $result['count']['aprobada'];
    $rechazadas = $result['count']['rechazada'];
    $entregadas = $result['count']['entregada'];
    $entransito = $result['count']['entransito'];
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/trasladosinventarios/solicitudesrecibidas', ['titulo'=>'Almacen', 'solicitudesrecividas'=>$solicitudesrecividas, 'unidadesmedida'=>$unidadesmedida, 'pendientes'=>$pendientes, 'aprobadas'=>$aprobadas, 'rechazadas'=>$rechazadas, 'entregadas'=>$entregadas, 'entransito'=>$entransito, 'sucursales'=>$sucursales, 'alertas'=>$alertas, 'user'=>$_SESSION]);
  }

//TABLA DONDE VEO LOS TRASLADOS O SALIDAS QUE HAGO O SOLICITUDES QUE HAGO A OTRAS SUCURSALES
  public static function trasladarinventario(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $id = $_GET['id'] ?? null;
    $error = $_GET['error'] ?? null;
    if($id && is_numeric($id) || $error){
      $alertas['error'][] = $error ?? 'Error al procesar orden.';
    }

    $sucursales = sucursales::idregistros('estado', 1);
    $result = (new TrasladosConsultaService())->listarComoOrigen((int)id_sucursal());
    $transferirinventario = $result['ordenes'];
    $pendientes = $result['count']['pendiente'];
    $aprobadas = $result['count']['aprobada'];
    $rechazadas = $result['count']['rechazada'];
    $entregadas = $result['count']['entregada'];
    $entransito = $result['count']['entransito'];
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/trasladosinventarios/trasladarinventario', ['titulo'=>'Almacen', 'transferirinventario'=>$transferirinventario, 'unidadesmedida'=>$unidadesmedida, 'pendientes'=>$pendientes, 'aprobadas'=>$aprobadas, 'rechazadas'=>$rechazadas, 'entregadas'=>$entregadas, 'entransito'=>$entransito, 'sucursales'=>$sucursales, 'alertas'=>$alertas, 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  //REALIZAR ORDEN TRASLADO DE MERCANCIA
  public static function nuevotrasladoinv(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $sucursalorigen = sucursales::find('id', id_sucursal());
    $sucursales = sucursales::all();
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/trasladosinventarios/nuevotrasladoinv', ['titulo'=>'Almacen', 'sucursalorigen'=>$sucursalorigen, 'sucursales'=>$sucursales, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'user'=>$_SESSION]);
  }

  //REALIZAR ORDEN DE SOLICITUD A OTRA SEDE DE MERCANCIA PARA QUE ME DESPACHEN
  public static function solicitarinventario(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $sucursalorigen = sucursales::find('id', id_sucursal());
    $sucursales = sucursales::all();
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/trasladosinventarios/solicitarinventario', ['titulo'=>'Almacen', 'sucursalorigen'=>$sucursalorigen, 'sucursales'=>$sucursales, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'user'=>$_SESSION]);
  }


  //EDITAR LOS PRODUCTOS A TRASLADAR A OTRA SEDE
  public static function editartrasladoinv(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $id=$_GET['id'] ?? null;
    if(!is_numeric($id))return;
    $alertas = [];
    $ordentraslado = (new TrasladosConsultaService())->obtenerParaEditar((int)$id, (int)id_sucursal());
    if(!$ordentraslado){
      header('Location: /admin/almacen/trasladarinventario?id='.$id.'&error=No se puede editar la orden. Debe estar en estado pendiente.');
      exit;
    }
    $sucursales = sucursales::all();
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/trasladosinventarios/editartrasladoinv', ['titulo'=>'Almacen', 'ordentraslado'=>$ordentraslado, 'sucursales'=>$sucursales, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'user'=>$_SESSION]);
  }


  //---------------------  API  -----------------------//
    //metodo llamado desde trasladarinv.ts para el detalle de la orden trasnaldo/solicitud
    public static function idOrdenTrasladoSolicitudInv(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'] ?? null;
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar orden.";
            echo json_encode($alertas);
            return;
        }
        $orden = (new TrasladosConsultaService())->obtenerDetalle((int)$id, (int)id_sucursal());
        if($orden){
            $alertas['exito'][] = "Consulta procesada";
            $alertas['orden'] = [$orden];
        }else{
            $alertas['error'][] = "Orden no existe";
        }
        echo json_encode($alertas);
    }

  ///////  generar orden de solicitar inventario  //////////
    public static function apisolicitarinventario(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
            echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
            return;
        }

        $datos = [
          'sucursal_destino_id'=>$_POST['idsucursaldestino'] ?? null,
          'observacion'=>$_POST['observacion'] ?? '',
          'items'=>json_decode($_POST['productos'] ?? '[]', true),
        ];
        $resultado = (new TrasladosOrdenService())->crearSolicitud($datos, (int)id_sucursal(), (int)($_SESSION['id'] ?? 0));
        echo json_encode($resultado);
    }


  //////  generar orden de traslado de inventario /////////
    public static function apinuevotrasladoinv(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
            echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
            return;
        }

        $datos = [
          'sucursal_destino_id'=>$_POST['idsucursaldestino'] ?? null,
          'observacion'=>$_POST['observacion'] ?? '',
          'items'=>json_decode($_POST['productos'] ?? '[]', true),
        ];
        $resultado = (new TrasladosOrdenService())->crearSalida($datos, (int)id_sucursal(), (int)($_SESSION['id'] ?? 0));
        echo json_encode($resultado);
    }


    public static function editarOrdenTransferencia(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
          echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
          return;
        }

        $datos = ['items'=>json_decode($_POST['nuevosproductos'] ?? '[]', true),];
        if(array_key_exists('idsucursaldestino', $_POST))
          $datos['sucursal_destino_id'] = $_POST['idsucursaldestino'];
        if(array_key_exists('observacion', $_POST))
          $datos['observacion'] = $_POST['observacion'];

        $resultado = (new TrasladosOrdenService())->editar((int)($_POST['id_trasladoinv'] ?? 0), $datos, (int)id_sucursal(), (int)($_SESSION['id'] ?? 0));
        echo json_encode($resultado);
    }


    //cuando presiona btn ver checkout para confirmar el envio de mercancia
    public static function confirmarnuevotrasladoinv(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
          echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
          return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $resultado = (new TrasladosInventarioService())->despachar($id, (int)id_sucursal(), (int)($_SESSION['id'] ?? 0), (string)($_SESSION['nombre'] ?? ''));
        // WhatsApp se ejecuta fuera de la transaccion y solo despues de un
        // despacho confirmado. Su fallo no revierte ni invalida el resultado.
        if(!empty($resultado['data']['notificar_despacho']) && $resultado['data']['orden'] ?? null){
          try{
            $conflocal = config_local::getParamGlobal();
            $configWhatsApp = $conflocal['notificacion_por_whatsApp_envio_mercancia'] ?? null;
            if((int)($configWhatsApp->valor_final ?? 0) === 1)
              (new whatsAppService())->sendMsgTrasladoInvDespachado($resultado['data']['orden'], []);
          }catch(\Throwable $error){
            error_log('Error al notificar despacho del traslado '.$id.': '.$error->getMessage());
          }
        }
        unset($resultado['data']['orden']);
        echo json_encode($resultado);
    }


    public static function confirmaringresoinv(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
          echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
          return;
        }

        $resultado = (new TrasladosInventarioService())->recibir((int)($_POST['id'] ?? 0), (int)id_sucursal(), (int)($_SESSION['id'] ?? 0), (string)($_SESSION['nombre'] ?? ''));
        echo json_encode($resultado);
    }


    //llamada desde trasladarinv.ts / trasladarinventario para anular envio o solicitud de que me despachen
    public static function anularnuevotrasladoinv(){
        isadmin();
        if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'){
          echo json_encode(['error'=>['Metodo de solicitud no valido.']]);
          return;
        }
        $resultado = (new TrasladosOrdenService())->cancelarORechazar((int)($_POST['id'] ?? 0), (int)id_sucursal(), (int)($_SESSION['id'] ?? 0));
        echo json_encode($resultado);
    }

}