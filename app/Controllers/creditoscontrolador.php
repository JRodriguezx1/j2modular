<?php

namespace App\Controllers;

use App\Models\ActiveRecord;
use App\Core\Routing\Router;
use App\Models\clientes\clientes;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\mediospago;
use App\Models\creditos\creditos;
use App\Models\creditos\cuotas;
use App\Models\creditos\productosseparados;
use App\Models\inventario\productos;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Repositories\creditos\creditosRepository;
use App\Repositories\creditos\cuotasRepository;
use App\Repositories\creditos\productsSeparadosRepository;
use App\Repositories\creditos\separadomediopagoRepository;
use App\Repositories\ventas\canalVentaRepository;
use App\Request\separadoRequest;
use App\services\creditosService;
use App\services\paymentService;

class creditoscontrolador{

    public static function index(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        //validar permisso de usuario
        if(!tienePermiso('Habilitar módulo de credito/separados')&&userPerfil()>3)return;
        $conflocal = config_local::getParamCaja();
        $creditos = new creditosRepository();
        $creditos = $creditos->unJoinWhereArrayObj('clientes', 'cliente_id', 'id', ['id_fksucursal'=>id_sucursal(), 'idestadocreditos'=>2]);
        $router->render('admin/creditos/index', ['titulo'=>'Creditos', 'creditos'=>$creditos, 'conflocal'=>$conflocal, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function separado(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        $clientes = clientes::all();
        $mediospago = mediospago::whereArray(['estado'=>1]);
        $cajas = caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
        $consecutivos = consecutivos::whereArray(['id_sucursalid'=>$idsucursal, 'estado'=>1]);
        $conflocal = config_local::getParamCaja();
        $canalesVentaRepo = new canalVentaRepository();
        $canalesVenta = $canalesVentaRepo->all();
        $router->render('admin/creditos/separado', ['titulo'=>'Creditos', 'clientes'=>$clientes, 'mediospago'=>$mediospago, 'cajas'=>$cajas, 'consecutivos'=>$consecutivos, 'conflocal'=>$conflocal, 'canalesVenta'=>$canalesVenta, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function detallecredito(Router $router){
        //session_start();
        isadmin();
        //if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
        $alertas = [];
        $id = $_GET['id'];  //id del credito
        if(!is_numeric($id))return;

        $conflocal = config_local::getParamGlobal();
        $datos = creditosService::detallecredito($id);

        //validar que el separado no se este abriendo desde otra sucursal
        if($datos['credito']->idtipofinanciacion == 2 && $datos['credito']->id_fksucursal != id_sucursal())
            return;
        
        $viewData = array_merge($datos, ['conflocal'=>$conflocal, 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION ]);
        $router->render('admin/creditos/detallecredito', $viewData);
    }


    public static function adicionarProducto(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Editar separados activos')&&userPerfil()>3)return;
        $alertas = [];
        $id = $_GET['id'];  //id del credito
        if(!is_numeric($id))return;

        $conflocal = config_local::getParamGlobal();
        $datos = creditosService::detallecredito($id);

        //validar que el separado no se este abriendo desde otra sucursal
        if($datos['credito']->idtipofinanciacion == 2 && $datos['credito']->id_fksucursal != id_sucursal())
            return;
        
        $viewData = array_merge($datos, ['conflocal'=>$conflocal, 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION ]);
        $router->render('admin/creditos/adicionarProducto', $viewData);
    }


    /*public static function registrarAbono(Router $router){
        date_default_timezone_set('America/Bogota');
        //session_start();
        isadmin();

        $conflocal = config_local::getParamGlobal();
        $factura = null;
        if(isset($credito->factura_id))$factura = facturas::find('id', $credito->factura_id);

        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = creditosService::registrarAbono($_POST);   // crear factory de repositorios
        }
        $datos = creditosService::detallecredito($_POST['id_credito']);  //// crear factory de repositorios
        
        //validar que el separado no se este abriendo desde otra sucursal
        if($datos['credito']->idtipofinanciacion == 2 && $datos['credito']->id_fksucursal != id_sucursal())
            return;

        $viewData = array_merge($datos, ['conflocal'=>$conflocal, 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION ]);
        
        $router->render('admin/creditos/detallecredito', $viewData);
    }


    public static function pagoTotal(Router $router){
        date_default_timezone_set('America/Bogota');
        //session_start();
        isadmin();
        $conflocal = config_local::getParamGlobal();
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = creditosService::registrarAbono($_POST);   // crear factory de repositorios
        }
        $datos = creditosService::detallecredito($_POST['id_credito']);  //// crear factory de repositorios
        //validar que el separado no se este abriendo desde otra sucursal
        if($datos['credito']->idtipofinanciacion == 2 && $datos['credito']->id_fksucursal != id_sucursal())
            return;
        $viewData = array_merge($datos, ['conflocal'=>$conflocal, 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION ]);
        
        $router->render('admin/creditos/detallecredito', $viewData);
    }*/



    /////////      API      ///////////

    public static function allcredits(){
        isadmin();
        $creditos = new creditosRepository();
        $creditos = $creditos->unJoinWhereArrayObj('clientes', 'cliente_id', 'id', ['id_fksucursal'=>id_sucursal()]);
        echo json_encode($creditos);
    }


    public static function registrarAbono():void{
        date_default_timezone_set('America/Bogota');
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )
            $alertas = creditosService::registrarAbono($_POST);   // crear factory de repositorios
        
        $datos = creditosService::detallecredito($_POST['id_credito']);  //// crear factory de repositorios
        //validar que el separado no se este abriendo desde otra sucursal
        if($datos['credito']->idtipofinanciacion == 2 && $datos['credito']->id_fksucursal != id_sucursal())
            return;
        //$viewData = array_merge($datos, ['conflocal'=>$conflocal, 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION ]);
        echo json_encode($alertas);
        return;
    }

    
    public static function crearSeparado(){  //llamada desde separado.ts
        isadmin();
        $alertas = [];
        $valoresCredito = (array)json_decode($_POST['valoresCredito']);
        $carrito = json_decode($_POST['carrito']);
        $mediospago = json_decode($_POST['mediospago']);
        $factimpuestos = json_decode($_POST['factimpuestos']);
        $valorefectivo = $_POST['valorefectivo']??0;
        $valoresCredito = ['usuariofk'=>$_SESSION['id'],'idtipofinanciacion' => 2]+$valoresCredito;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $validate = new separadoRequest($valoresCredito);
            $alertas = $validate->validate();
            if(empty($alertas)){
                $alertas = creditosService::ejecutarCrearSeparado($valoresCredito, $carrito, $mediospago, $valorefectivo);
            }
        }
        echo json_encode($alertas);
    }


    public static function cambioMedioPagoSeparado(){
        //session_start();
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )$alertas = creditosService::cambioMedioPagoSeparado($_POST);
        echo json_encode($alertas);
    }


    public static function anularSeparado(){
        //session_start();
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )$alertas = creditosService::anularSeparado($_POST['id']);
        echo json_encode($alertas);
    }


    public static function ajustarCreditoAntiguo(){
        //session_start();
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )$alertas = creditosService::ajustarCreditoAntiguo($_POST);
        echo json_encode($alertas);
    }

    public static function detalleProductosCredito(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar orden.";
            echo json_encode($alertas);
            return;
        }
        $alertas = creditosService::detalleProductosCredito($id);
        echo json_encode($alertas);
    }


    public static function editarOrdenCreditoSeparado(){
        isadmin();
        $alertas = [];
        $idcredito = $_POST['idcredito'];
        $idsdetalleproductos = json_decode($_POST['ids']);
        $nuevosproductosFront = json_decode($_POST['nuevosproductos']);

        $capital = json_decode($_POST['capital']);
        $saldopendiente = json_decode($_POST['saldopendiente']);
        $montocuota = json_decode($_POST['montocuota']);
        $montototal = json_decode($_POST['montototal']);
        $totalunidades = json_decode($_POST['totalunidades']);
        $dataCredit = ['capital'=>$capital, 'saldopendiente'=>$saldopendiente, 'montocuota'=>$montocuota, 'montototal'=>$montototal, 'totalunidades'=>$totalunidades];
        //debuguear($dataCredit);
        if($_SERVER['REQUEST_METHOD'] === 'POST' )
            $alertas = creditosService::editarOrdenCreditoSeparado($idcredito, $idsdetalleproductos, $nuevosproductosFront, $dataCredit);
        echo json_encode($alertas);
    }


    public static function totalCuotasXcliente(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar solicitud.";
            echo json_encode($alertas);
            return;
        }
        $creditos = new creditosRepository();
        $creditos = $creditos->totalCuotasXcliente($id, id_sucursal());
        echo json_encode($creditos);
        return;
    }


    public static function getCreditoSeparado(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar solicitud.";
            echo json_encode($alertas);
            return;
        }
        $sucursal = sucursales::find('id', id_sucursal());
        $detallecredito = creditosService::detallecredito($id);
        $data = self::getArrayData($sucursal, $detallecredito['credito'], $detallecredito['productos'], $detallecredito['cuotas'], $detallecredito['cliente'], $detallecredito['direccion'], $detallecredito['usuario']);
        echo json_encode($data);
        return;
    }


    public static function getAbono(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar solicitud.";
            echo json_encode($alertas);
            return;
        }
        $sucursal = sucursales::find('id', id_sucursal());
        $repoCuota = new cuotasRepository();
        $cuota = $repoCuota->find($id);
        $repoCredito = new creditosRepository();
        $credito = $repoCredito->find($cuota->id_credito);
        $cliente = clientes::find('id', $credito->cliente_id);
        $data = self::getArrayData($sucursal, $credito, [], [$cuota], $cliente, null, null);
        echo json_encode($data);
        return;
    }


    public static function anularAbono(){
        isadmin();
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id)){
            $alertas['error'][] = "Error al procesar solicitud.";
            echo json_encode($alertas);
            return;
        }
        $alertas = creditosService::anularAbono($id);
        echo json_encode($alertas);
        return;
    }


    public static function pagarDeudaTotal():void{
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )
            $alertas = creditosService::pagarDeudaTotal($_POST);
        echo json_encode($alertas);
        return;
    }


    public static function registrarAbonoFromCli(Router $router){
        date_default_timezone_set('America/Bogota');
        isadmin();
        $conflocal = config_local::getParamGlobal();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' )
            $alertas = creditosService::registrarAbono($_POST);   // crear factory de repositorios
        echo json_encode($alertas);
        return;
    }


    public static function getArrayData(object $sucursal, object $credito, array $productos, array $cuotas, object $cliente, object|null $direccionCliente = null, ?object $usuario = null): array{
        $data = [
            'negocio' => $sucursal->negocio,
            'sucursal' => $sucursal->nombre,
            'nit' => $sucursal->nit,
            'direccion' => $sucursal->direccion,
            'telefono' => $sucursal->telefono,
            'email' => $sucursal->email,
            'www' => $sucursal->www,
            'logo' => $sucursal->logo,
            'num_orden' => $credito->num_orden,
            'id' => $credito->id,
            'fechainicio' => $credito->fechainicio,
            'fechafin' => $credito->fechafin,
            'idestadocreditos' => $credito->idestadocreditos,
            'capital' => $credito->capital,
            'descuento' => $credito->descuento,
            'interestotal' => $credito->interestotal,
            'interesxcuota' => $credito->interesxcuota,
            'valorinterestotal' => $credito->valorinterestotal,
            'montototal' => $credito->montototal,
            'nombrecliente' => $credito->nombrecliente,
            'nota' => $credito->nota,
            'saldopendiente' => $credito->saldopendiente,
            'abonodecuotas' => $credito->abonodecuotas,
            'valorimpuestototal' => $credito->valorimpuestototal,
            'productos' => $productos,
            'cuotas' => $cuotas,
            'cliente' => $cliente,
            'direccionCliente' => $direccionCliente,
            'usuario' => $usuario
        ];
        return $data;
    }


    public static function buscarIntersucursal(): void{
        header('Content-Type: application/json; charset=utf-8');
        isadmin();
        /*if (!tienePermiso('Consultar créditos de otras sucursales')&& userPerfil() > 3){
            http_response_code(403);
            echo json_encode(['error' => ['No tiene autorización.']]);
            return;
        }*/
        $termino = trim($_GET['q'] ?? '');
        $longitud = mb_strlen($termino);
        $esIdNumerico = $termino !== '' && ctype_digit($termino);

        if($termino === '' || (!$esIdNumerico && $longitud < 4)){
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'error' => 'Ingrese el ID del crédito o al menos 4 caracteres del cliente o de su identificación.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if($longitud > 80){
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'error' => 'El término de búsqueda no puede superar los 80 caracteres.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try{
            $repo = new creditosRepository();
            echo json_encode([
                'ok' => true,
                'data' => $repo->buscarAbiertosIntersucursal($termino, id_sucursal())
            ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }catch(\Throwable $th){
            error_log('Error al buscar créditos intersucursal: '.$th->getMessage());
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'No fue posible realizar la búsqueda. Intente nuevamente.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

}
