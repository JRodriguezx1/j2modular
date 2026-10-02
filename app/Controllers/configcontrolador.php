<?php

namespace App\Controllers;

use App\DTO\configuracion\EmpleadoResponseDTO;
use App\Models\configuraciones\usuarios; //namespace\clase hija
use App\Models\configuraciones\bancos;
use App\Models\configuraciones\permisos;
use App\Models\configuraciones\tarifas;
use App\Models\configuraciones\usuarios_permisos;
use App\Models\sucursales;
use App\Policies\EmpleadoPolicy;
use App\services\configuracion\BancosConfiguracionService;
use App\services\configuracion\CajasConfiguracionService;
use App\services\configuracion\ConfiguracionPaginaService;
use App\services\configuracion\EmisoresConfiguracionService;
use App\services\configuracion\EmpleadosConfiguracionService;
use App\services\configuracion\FacturadoresConfiguracionService;
use App\services\configuracion\ImagenStorage;
use App\services\configuracion\ImpresorasConfiguracionService;
use App\services\configuracion\MediosPagoConfiguracionService;
use App\services\configuracion\NegocioConfiguracionService;
use App\services\configuracion\TarifasConfiguracionService;
use MVC\Router;  //namespace\clase
 
class configcontrolador{

  /**
   * Protege las APIs de configuracion sin responder con redirecciones HTML.
   * Los perfiles 1 y 2 conservan el acceso superior existente; los demas
   * requieren el permiso explicito del modulo.
   */
  private static function autorizarApiConfiguracion():bool{
    header('Content-Type: application/json; charset=utf-8');
    isadmin();
    if((int)userPerfil() >= 3 && !tienePermiso('Habilitar modulo de configuracion'))
      return self::responderErrorApi('No tiene permiso para administrar la configuracion.', 403);
    return true;
  }

  private static function exigirPostApi():bool{
    if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST')return self::responderErrorApi('Metodo no permitido.', 405);
    return true;
  }

  private static function exigirGetApi():bool{
    if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET')return self::responderErrorApi('Metodo no permitido.', 405);
    return true;
  }

  private static function responderErrorApi(string $mensaje, int $estado):bool{
    http_response_code($estado);
    echo json_encode(['error'=>[$mensaje]]);
    return false;
  }

  /** @param usuarios[] $empleados */
  private static function filtrarEmpleadosGestionables(array $empleados):array{
    $perfilActor = (int)userPerfil();
    return array_values(array_filter($empleados, static fn($empleado)=>EmpleadoPolicy::puedeGestionarEmpleado($perfilActor, (int)$empleado->perfil)));
  }

  /** Devuelve exclusivamente los campos que consume empleados.ts. */
  private static function empleadoParaApi(usuarios $empleado):EmpleadoResponseDTO{
    $relaciones = usuarios_permisos::unJoinWhereArrayObj(permisos::class, 'permisoid', 'id', ['usuarioid'=>$empleado->id]);
    return EmpleadoResponseDTO::desdeModelo($empleado, $relaciones);
  }

  public static function index(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de configuracion')&&userPerfil()>=3)return;
    $alertas = [];
    $idsucursal = id_sucursal();
    $empleado = new \stdClass();
    $empleado->perfil = '';
    $datosPagina = (new ConfiguracionPaginaService())->obtenerDatos($idsucursal, (int)userPerfil());
    $router->render('admin/configuracion/index', ['titulo'=>'Configuracion', 'paginanegocio'=>'checked', 'negocio'=>$_SESSION['sucursal'], 'empleado'=>$empleado, 'alertas'=>$alertas, 'user'=>$_SESSION,] + $datosPagina);
  }


  public static function editarnegocio(Router $router){ //metodo para el llenado y actualizacion de los datos del negocio
    isadmin();
    if(!tienePermiso('Habilitar modulo de configuracion')&&userPerfil()>=3)return;
    $alertas = [];
    $idsucursal = id_sucursal();
    $sucursal = sucursales::find('id', $idsucursal);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        try{
            $negocioService = new NegocioConfiguracionService(new ImagenStorage($_SERVER['DOCUMENT_ROOT'], explode('.', $_SERVER['HTTP_HOST'])[0], '', 'logo_', 500000));
            $resultado = $negocioService->guardarNegocio($_POST, $_FILES['logo'] ?? null, (int)$idsucursal);
            $sucursal = $resultado['sucursal'];
            $alertas = $resultado['alertas'];
        }catch(\Throwable $error){
            error_log('Error al preparar la configuracion del negocio: '.$error->getMessage());
            $alertas = ['error'=>['No fue posible preparar la configuracion del negocio.']];
        }
    }
    $empleado = new \stdClass();
    $empleado->perfil = '';
    $datosPagina = (new ConfiguracionPaginaService())->obtenerDatos($idsucursal, (int)userPerfil());
    $router->render('admin/configuracion/index', ['titulo'=>'configuracion', 'paginanegocio'=>'checked', 'negocio'=>$sucursal, 'empleado'=>$empleado, 'alertas'=>$alertas, 'user'=>$_SESSION] + $datosPagina);
  }


    public static function crear_empleado(Router $router){ //metodo para crear empleado
        isadmin();
        if(!tienePermiso('Habilitar modulo de configuracion')&&userPerfil()>=3)return;
        $alertas = [];
        $idsucursal = id_sucursal();
        $empleado = new \stdClass();
        $empleado->perfil = '';

        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $empleado = new usuarios($_POST);
            $permisos = $_POST['permisos'] ?? [];
            if(!is_array($permisos)){
                $alertas = ['error'=>['La lista de permisos no es valida.']];
            }else{
                try{
                    $empleadoService = new empleadosConfiguracionService(new ImagenStorage($_SERVER['DOCUMENT_ROOT'], explode('.', $_SERVER['HTTP_HOST'])[0], 'avatar', 'empleado_', 350000));
                    $alertas = $empleadoService->crearEmpleado($_POST, $permisos, $_FILES['img'] ?? null, (int)$idsucursal, (int)userPerfil());
                }catch(\Throwable $error){
                    $alertas = ['error'=>['No fue posible preparar la creacion del empleado.'.$error->getMessage()]];
                }
            }

            if(!empty($alertas['exito'])){
                $empleado = new \stdClass();
                $empleado->perfil = '';
            }
        }
        $datosPagina = (new ConfiguracionPaginaService())->obtenerDatos($idsucursal, (int)userPerfil());
        $router->render('admin/configuracion/index', ['titulo'=>'Administracion', 'paginaempleado'=>'checked', 'negocio'=>$_SESSION['sucursal'], 'empleado'=>$empleado, 'alertas'=>$alertas, 'user'=>$_SESSION] + $datosPagina);
    }

  ///////////////////////////////////  Apis ////////////////////////////////////
    public static function getAllemployee(){ //api llamada desde empleados.ts entrega todos los empleados con sus permisos
        if(!self::autorizarApiConfiguracion() || !self::exigirGetApi())return;
        $empleados = self::filtrarEmpleadosGestionables(usuarios::whereArray(['idsucursal'=>id_sucursal(), 'confirmado'=>1]));
        echo json_encode(array_map([self::class, 'empleadoParaApi'], $empleados));
    }

    public static function actualizarEmpleado(){ //actualizar editar empleado
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi())return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de empleado no valido.', 400); 
            return; 
        }
        $idpermisosFront = json_decode($_POST['idpermisos'] ?? '[]', true);
        if(!is_array($idpermisosFront)){
            self::responderErrorApi('La lista de permisos no es valida.', 400);
            return;
        }
        try{
            $empleadoService = new EmpleadosConfiguracionService(new ImagenStorage($_SERVER['DOCUMENT_ROOT'], explode('.', $_SERVER['HTTP_HOST'])[0], 'avatar', 'empleado_', 350000));
            $resultado = $empleadoService->actualizarEmpleado($id, $_POST, $idpermisosFront, $_FILES['img'] ?? null, (int)id_sucursal(), (int)userPerfil());
        }catch(\Throwable $error){
            error_log('Error al preparar la actualizacion del empleado: '.$error->getMessage());
            http_response_code(500);
            $resultado = ['error'=>['No fue posible preparar la actualizacion del empleado.']];
        }
        echo json_encode($resultado);
        return;
    }


    public static function eliminarEmpleado(){ //api llamada desde empleados.ts 
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi())return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de empleado no valido.', 400); 
            return; 
        }
        try{
            $empleadoService = new EmpleadosConfiguracionService(new ImagenStorage($_SERVER['DOCUMENT_ROOT'], explode('.', $_SERVER['HTTP_HOST'])[0], 'avatar', 'empleado_', 350000));
            $resultado = $empleadoService->eliminarEmpleado($id, (int)id_sucursal(), (int)($_SESSION['id'] ?? 0), (int)userPerfil());
        }catch(\Throwable $error){
            http_response_code(500);
            $resultado = ['error'=>['No fue posible preparar la eliminacion del empleado.'.$error->getMessage()]];
        }
        echo json_encode($resultado);
        return;
    }


    public static function updatepassword(){ //api llamada desde empleados.ts 
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi())return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de empleado no valido.', 400); 
            return; 
        }
        try{
            $empleadoService = new EmpleadosConfiguracionService(new ImagenStorage($_SERVER['DOCUMENT_ROOT'], explode('.', $_SERVER['HTTP_HOST'])[0], 'avatar', 'empleado_', 350000));
            $resultado = $empleadoService->actualizarPassword($id, (string)($_POST['password'] ?? ''), (int)id_sucursal(), (int)userPerfil());
        }catch(\Throwable $error){
            http_response_code(500);
            $resultado = ['error'=>['No fue posible preparar el cambio de password del empleado. '.$error->getMessage()]];
        }
        echo json_encode($resultado);
        return;
    }
  
    ///////////// procesando la gestion de la caja ////////////////
    public static function allcajas(){  //api llamado desde citas.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      try{
        $resultado = (new CajasConfiguracionService())->listarCajas((int)id_sucursal());
      }catch(\Throwable $error){
        error_log('Error al listar cajas: '.$error->getMessage());
        http_response_code(500);
        $resultado = ['error'=>['No fue posible consultar las cajas.']];
      }
      echo json_encode($resultado);
    }
  
    public static function crearCaja(){ //api llamada desde el modulo de gestioncajas.ts cuando se crea un cliente
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        try{
            $resultado = (new CajasConfiguracionService())->crearCaja($_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function actualizarCaja(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de caja invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new CajasConfiguracionService())->actualizarCaja($id, $_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarCaja(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){
            self::responderErrorApi('Identificador de caja invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new CajasConfiguracionService())->eliminarCaja($id, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    
///////////////////procesando la gestion de los facturadores //////////////////////
    public static function allfacturadores(){  //api llamado desde gestionfacturadores.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      try{
        $resultado = (new FacturadoresConfiguracionService())->listarFacturadores((int)id_sucursal());
      }catch(\Throwable $error){
        error_log('Error al listar facturadores: '.$error->getMessage());
        http_response_code(500);
        $resultado = ['error'=>['No fue posible consultar los facturadores.']];
      }
      echo json_encode($resultado);
    }
  
    public static function crearFacturador(){ //api llamada desde el modulo de gestionfacturadoes.ts cuando se crea un cliente
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        try{
            $resultado = (new FacturadoresConfiguracionService())->crearFacturador($_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function actualizarFacturador(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de facturador invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new FacturadoresConfiguracionService())->actualizarFacturador($id, $_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarFacturador(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de facturador invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new FacturadoresConfiguracionService())->eliminarFacturador($id, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


///////////// procesando la gestion de los bancos ////////////////
    public static function allbancos(){  //api llamado desde gestionbancos.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      echo json_encode(bancos::all());
    }

    public static function crearBanco(){ //api llamada desde el modulo de gestionbancos.ts cuando se crea un cliente
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        echo json_encode((new BancosConfiguracionService())->crearBanco($_POST));
    }

    public static function actualizarBanco(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de banco invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new BancosConfiguracionService())->actualizarBanco($id, $_POST);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarBanco(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de banco invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new BancosConfiguracionService())->eliminarBanco($id);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


///////////// procesando la gestion de las tarifas ////////////////
    public static function alltarifas(){  //api llamado desde gestiontarifas.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      echo json_encode(tarifas::all());
    }

    public static function crearTarifa(){ //api llamada desde el modulo de gestiontarifas.ts cuando se crea un cliente
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        echo json_encode((new TarifasConfiguracionService())->crearTarifa($_POST));
    }

    public static function actualizarTarifa(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de tarifa invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new TarifasConfiguracionService())->actualizarTarifa($id, $_POST);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarTarifa(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de tarifa invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new TarifasConfiguracionService())->eliminarTarifa($id);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


    ///////////// procesando la gestion de los medios de pago ////////////////
    public static function allmediospago(){  //api llamado desde gestionmediospago.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      try{
        $resultado = (new MediosPagoConfiguracionService())->listarMediosPago();
      }catch(\Throwable $error){
        error_log('Error al listar medios de pago: '.$error->getMessage());
        http_response_code(500);
        $resultado = ['error'=>['No fue posible consultar los medios de pago.']];
      }
      echo json_encode($resultado);
    }

    public static function crearMedioPago(){ //api llamada desde el modulo de gestiontarifas.ts cuando se crea un cliente
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        echo json_encode((new MediosPagoConfiguracionService())->crearMedioPago($_POST));
    }

    public static function actualizarMedioPago(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de medio de pago invalido.', 422);
             return; 
        }
        try{
            $resultado = (new MediosPagoConfiguracionService())->actualizarMedioPago($id, $_POST);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarMedioPago(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de medio de pago invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new MediosPagoConfiguracionService())->eliminarMedioPago($id);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


    public static function updateStateMedioPago(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de medio de pago invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new MediosPagoConfiguracionService())->actualizarEstadoMedioPago($id, $_POST['estado'] ?? null);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


    ///////////// procesando la gestion de las impresoras ////////////////
    public static function allPrinters(){  //api llamado desde gestionprinters.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
        $resultado = (new ImpresorasConfiguracionService())->listarImpresoras();
        echo json_encode($resultado);
    }

    public static function crearPrinter(){ //api llamada desde el modulo de gestionPrinters.ts
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        echo json_encode((new ImpresorasConfiguracionService())->crearImpresora($_POST));
    }

    public static function actualizarPrinter(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){
            self::responderErrorApi('Identificador de impresora invalido.', 422);
            return;
        }
        try{
            $resultado = (new ImpresorasConfiguracionService())->actualizarImpresora($id, $_POST);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarPrinter(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de impresora invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new ImpresorasConfiguracionService())->eliminarImpresora($id);
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }


    ///////////// procesando la gestion de los emisores ////////////////
    public static function allEmisores(){  //api llamado desde gestionemisores.js
      if(!self::autorizarApiConfiguracion() || !self::exigirGetApi()) return;
      try{
        $resultado = (new EmisoresConfiguracionService())->listarEmisores((int)id_sucursal());
      }catch(\Throwable $error){
        error_log('Error al listar emisores: '.$error->getMessage());
        http_response_code(500);
        $resultado = ['error'=>['No fue posible consultar los emisores.']];
      }
      echo json_encode($resultado);
    }

    public static function crearEmisor(){ //api llamada desde el modulo de gestionemisores.ts
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        try{
            $resultado = (new EmisoresConfiguracionService())->crearEmisor($_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function actualizarEmisor(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de emisor invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new EmisoresConfiguracionService())->actualizarEmisor($id, $_POST, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function updateStateEmisor(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de emisor invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new EmisoresConfiguracionService())->actualizarEstadoEmisor($id, $_POST['estado'] ?? null, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

    public static function eliminarEmisor(){
        if(!self::autorizarApiConfiguracion() || !self::exigirPostApi()) return;
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($id === false){ 
            self::responderErrorApi('Identificador de emisor invalido.', 422); 
            return; 
        }
        try{
            $resultado = (new EmisoresConfiguracionService())->eliminarEmisor($id, (int)id_sucursal());
        }catch(\InvalidArgumentException $error){
            self::responderErrorApi($error->getMessage(), $error->getCode() ?: 422);
            return;
        }
        echo json_encode($resultado);
    }

}
