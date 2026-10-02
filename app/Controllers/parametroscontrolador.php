<?php

namespace App\Controllers;

use App\classes\Email;
use App\Models\ActiveRecord;
use App\Models\configuraciones\negocio;
use App\Models\parametrizacion\config_global;
use App\Models\parametrizacion\config_local;
use App\Models\parametrizacion\monedas;
use App\Models\sucursales;
use App\Models\ventas\ventas;
use App\Models\configuraciones\tarifas;
use MVC\Router;  //namespace\clase

class parametroscontrolador{
    
  //metodo para los inputs radio
  public static function parametrosSistema():void{
    isadmin();
    $alertas = [];
    $clave = array_key_first($_POST);
    $valorLocal = $_POST[$clave];

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
        $valordeFault = config_global::uncampo('clave', $clave, 'valor_default');
        if($valorLocal != $valordeFault){  //registrar con parametros local por sucursal
            $parametroLocal = new config_local(['clave'=>$clave, 'valor'=>$valorLocal]);
            $r = $parametroLocal->crear_guardar();
            if($r[0]){
                $alertas['exito'][] = "Ajuste procesado";
            }else{
                $alertas['error'][] = "Error intentalo nuevamente";
            }
        }else{
            $parametroLocal = config_local::uniquewhereArray(['fk_sucursalid'=>id_sucursal(), 'clave'=>$clave]);
            if($parametroLocal){
                $r = $parametroLocal->eliminar_registro();
                if($r){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }
        }
        echo json_encode($alertas);
    }
  }


  public static function parametrosSistemaClaves():void{
    isadmin();
    $alertas = [];
    $clave = array_key_first($_POST);
    $valorLocal = $_POST[$clave];

    //debuguear($valorLocal);
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
        $valordeFault = config_global::uncampo('clave', $clave, 'valor_default');
        if($valorLocal != $valordeFault){  //registrar con parametros local por sucursal
            $x = config_local::uniquewhereArray(['fk_sucursalid'=>id_sucursal(), 'clave'=>$clave]);
            if($x){
                $x->compara_objetobd_post(['clave'=>$clave, 'valor'=>$valorLocal]);
                $ra = $x->actualizar();
                if($ra){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }else{
                $parametroLocal = new config_local(['clave'=>$clave, 'valor'=>$valorLocal]);
                $r = $parametroLocal->crear_guardar();
                if($r[0]){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }
        }else{
            $parametroLocal = config_local::uniquewhereArray(['fk_sucursalid'=>id_sucursal(), 'clave'=>$clave]);
            if($parametroLocal){
                $r = $parametroLocal->eliminar_registro();
                if($r){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }
        }
        echo json_encode($alertas);
    }
  }

  //metodo para el select del impuesto
  public static function parametrosSistemaTipoSelect():void{
    isadmin();
    $alertas = [];
    $clave = array_key_first($_POST);
    $valorLocal = $_POST[$clave];

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
        $valordeFault = config_global::uncampo('clave', $clave, 'valor_default');
        if($valorLocal != $valordeFault){  //registrar con parametros local por sucursal
            $x = config_local::uniquewhereArray(['fk_sucursalid'=>id_sucursal(), 'clave'=>$clave]);
            if($x){
                $x->compara_objetobd_post(['clave'=>$clave, 'valor'=>$valorLocal]);
                $ra = $x->actualizar();
                if($ra){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }else{
                $parametroLocal = new config_local(['clave'=>$clave, 'valor'=>$valorLocal]);
                $r = $parametroLocal->crear_guardar();
                if($r[0]){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }
        }else{
            $parametroLocal = config_local::uniquewhereArray(['fk_sucursalid'=>id_sucursal(), 'clave'=>$clave]);
            if($parametroLocal){
                $r = $parametroLocal->eliminar_registro();
                if($r){
                    $alertas['exito'][] = "Ajuste procesado";
                }else{
                    $alertas['error'][] = "Error intentalo nuevamente";
                }
            }
        }
        echo json_encode($alertas);
    }

  }


  public static function changeTasaCambio():void{
    isadmin();
    header('Content-Type: application/json; charset=utf-8');
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405);
      echo json_encode(['error'=>'Método no permitido.']);
      return;
    }

    $datos = json_decode(file_get_contents('php://input'), true);
    if(!is_array($datos)){
      http_response_code(400);
      echo json_encode(['error'=>'La solicitud no contiene un JSON válido.']);
      return;
    }

    $idmoneda = filter_var($datos['idmoneda'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    $tasacambio = $datos['tasacambio'] ?? null;

    if($idmoneda === false || !is_numeric($tasacambio) || (float)$tasacambio <= 0){
      http_response_code(422);
      echo json_encode(['error'=>'La moneda o la tasa de cambio no son válidas.']);
      return;
    }

    $moneda = monedas::uniquewhereArray(['id'=>$idmoneda, 'activo'=>1]);
    if(!$moneda){
      http_response_code(404);
      echo json_encode(['error'=>'La moneda seleccionada no existe o está inactiva.']);
      return;
    }

    $sucursal = sucursales::find('id', id_sucursal());
    if(!$sucursal){
      http_response_code(404);
      echo json_encode(['error'=>'No se encontró la sucursal activa.']);
      return;
    }

    $sucursal->idmoneda = (int)$idmoneda;
    $sucursal->tasacambio = (float)$tasacambio;
    try {
      if(!$sucursal->actualizar())
        throw new \RuntimeException('No fue posible guardar la sucursal.');
      $_SESSION['sucursal'] = $sucursal;
      echo json_encode([
        'success'=>'Tasa de cambio actualizada correctamente.',
        'data'=>['idmoneda'=>(int)$sucursal->idmoneda, 'tasacambio'=>(float)$sucursal->tasacambio]
      ]);
    }catch(\Throwable $th){
      http_response_code(500);
      echo json_encode(['error'=>'No fue posible actualizar la tasa de cambio. '.$th->getMessage()]);
    }
  }


  public static function getPasswords():void{
    isadmin();
    $r = config_local::getPasseords();
    echo json_encode($r);
  }


  public static function getParamGlobal():void{
    isadmin();
    $conflocal = config_local::getParamGlobal();
    echo json_encode($conflocal);
    return;
  }

}