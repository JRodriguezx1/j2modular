<?php

namespace App\Controllers;

use App\classes\Traits\DocumentTrait;
use App\Models\ActiveRecord;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\services\parqueaderoService;
use MVC\Router;  //namespace\clase
use stdClass;

class parqueaderocontrolador{

  use DocumentTrait;

  public static function index(Router $router):void{
    //session_start();
    isadmin();
    //if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
    $alertas = [];
    $idsucursal = id_sucursal();
    $conflocal = config_local::getParamGlobal();
    $router->render('admin/parqueadero/index', ['titulo'=>'Parqueadero', 'conflocal'=>$conflocal, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  ///////////////////////  API /////////////////////////

  public static function createUpdateTarifa(){
    isadmin();
    //if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
    $alertas = [];
    $idsucursal = id_sucursal();
    $conflocal = config_local::getParamGlobal();
    $parquederoService = new parqueaderoService();

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }

    try {
      $parquederoService->createUpdateTarifa($_POST);
      echo json_encode(['exito' => 'Tarifa actualizada']);
      return;
    } catch (\Throwable $th) {
      echo json_encode(['error' => 'Error al actualizar tarifa >> '.$th->getMessage()]);
      return;
    }

  }


  public static function allTarifas(){
    isadmin();
    //if(!tienePermiso('Habilitar modulo de venta')&&userPerfil()>3)return;
    $idsucursal = id_sucursal();
    $conflocal = config_local::getParamGlobal();
    $parquederoService = new parqueaderoService();
    $tarifas = $parquederoService->allTarifas();
    echo json_encode($tarifas);
    return;
  }


}