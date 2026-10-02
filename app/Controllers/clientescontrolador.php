<?php

namespace App\Controllers;

use App\Core\Routing\Router;
use App\Models\configuraciones\usuarios;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\mediospago;
use App\Models\configuraciones\tarifas;
use App\Models\inventario\productos;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\Repositories\clientes\clientesRepository;
use App\Repositories\clientes\direccionesRepository;
use App\Repositories\clientes\preciosPorClienteRepository;
use App\Repositories\creditos\creditosRepository;
use App\services\clientesService;
use stdClass;

class clientescontrolador{
    

    public static function index(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $buscar = '';
        // Las lecturas propias del modulo pasan por Repository; los catalogos
        // externos (tarifas, sucursales, cajas) conservan su implementacion actual.
        $clientes = (new clientesRepository())->all();
        $tarifas = tarifas::all();
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            
        }
        $router->render('admin/clientes/index', ['titulo'=>'Clientes', 'clientes'=>$clientes, 'tarifas'=>$tarifas, 'alertas'=>$alertas, 'buscar'=>$buscar, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function crear(Router $router){
        /*$alertas = $usuario->validarEmail();    
        $usuarioexiste = $usuario->validar_registro();//retorna 1 si existe usuario(email), 0 si no existe
        $usuariotelexiste = $usuario->find('movil', $_POST['movil']);*/
        //session_start();
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $clienteService = new clientesService();
            $alertas = $clienteService->crearCliente($_POST, [
                'idtarifa'=>1,
                'iddepartamento'=>1,
                'idciudad'=>1,
                'departamento'=>'No definido',
                'ciudad'=>'No definido',
                'direccion'=>'Tienda',
            ]);
        }
        $clientes = (new clientesRepository())->all();
        $tarifas = tarifas::all();
        $router->render('admin/clientes/index', ['titulo'=>'Clientes', 'clientes'=>$clientes, 'tarifas'=>$tarifas, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function actualizar(Router $router){
        //session_start();
        isadmin();
        $alertas = [];  
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $cliente = usuarios::find('id', $_POST['id']);
            $cliente->compara_objetobd_post($_POST);
            $cliente->password2 = $cliente->password;
            $alertas = $cliente->validar_nueva_cuenta();
            $alertas = $cliente->validarEmail();
            if(empty($alertas)){
                $r = $cliente->actualizar();
                if($r)$alertas['exito'][] = 'Datos de cliente actualizados';
            }
        }
        $clientes = usuarios::whereArray(['confirmado'=>1, 'admin'=>0]);
        $router->render('admin/clientes/index', ['titulo'=>'Clientes', 'clientes'=>$clientes, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function hab_desh(Router $router){
        //session_start();
        isadmin();
        $alertas = [];  
        $id = $_GET['id'];
        if(!is_numeric($id))return;

        $cliente = usuarios::find('id', $id);
        if($cliente){
            if($cliente->habilitar){
                $cliente->habilitar = 0;
                $alertas['exito'][] = 'Cliente bloqueado de la base de datos';
            }else{
                $cliente->habilitar = 1;
                $alertas['exito'][] = 'Cliente habilitado de la base de datos';
            }
            $r = $cliente->actualizar();
        }else{
            header('Location: /admin/clientes');
        }

        if(!$r)$alertas['exito'][] = 'hubo un error';
        
        $clientes = usuarios::whereArray(['confirmado'=>1, 'admin'=>0]);
        $router->render('admin/clientes/index', ['titulo'=>'Clientes', 'clientes'=>$clientes, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function marketing(Router $router){
        //session_start();
        isadmin(); 
        $alertas = [];
        $router->render('admin/clientes/marketing', ['titulo'=>'Clientes/marketing', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function crearcampania(Router $router){
        //session_start();
        isadmin(); 
        $alertas = [];
        $router->render('admin/clientes/crearcampania', ['titulo'=>'Clientes/crearcampania', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function detalle(Router $router){
        //session_start();
        isadmin(); 
        $id = $_GET['id'];
        if(!is_numeric($id))return;
        $alertas = [];
        $conflocal = config_local::getParamGlobal();
        $clientesRepo = new clientesRepository();
        $cliente = $clientesRepo->find((int)$id);
        if(!$cliente){
            header('Location: /admin/clientes');
            return;
        }
        $indicadores = $clientesRepo->indicadoresVentas($cliente->id, id_sucursal());
        $creditos = new creditosRepository;
        //$creditos = $creditos->findAll('cliente_id', $cliente->id, 'DESC');
        $creditos = $creditos->creditosXclienteXemisor('cliente_id', $cliente->id, 'DESC');
        $cajas = caja::whereArray(['idsucursalid'=>id_sucursal(), 'estado'=>1]);
        $mediospago = mediospago::whereArray(['estado'=>1]);
        $router->render('admin/clientes/detalle', ['titulo'=>'Clientes', 'conflocal'=>$conflocal, 'cliente'=>$cliente, 'indicadores'=>$indicadores, 'cajas'=>$cajas, 'mediospago'=>$mediospago, 'creditos'=>$creditos, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function preciosXCliente(Router $router){
        //session_start();
        isadmin(); 
        $id = $_GET['id'];
        if(!is_numeric($id))return;
        $alertas = [];
        $conflocal = config_local::getParamGlobal();
        $clientesRepo = new clientesRepository();
        $preciosRepo = new preciosPorClienteRepository();
        $cliente = $clientesRepo->find((int)$id);
        if(!$cliente){
            header('Location: /admin/clientes');
            return;
        }
        $productos = productos::whereArray(['visible'=>1]);
        $arrayPreciosPorCliente = $preciosRepo->conProductoPorCliente($cliente->id);
        $router->render('admin/clientes/preciosXCliente', ['titulo'=>'Clientes', 'conflocal'=>$conflocal, 'cliente'=>$cliente, 'productos'=>$productos, 'arrayPreciosPorCliente'=>$arrayPreciosPorCliente, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    ///////////////////////////////////  Apis ////////////////////////////////////
    public static function allclientes(){  //api llamado desde citas.js
        $clientes = (new clientesRepository())->all();
        echo json_encode($clientes);
    }
    

    ///////////////////////////////////  Apis ////////////////////////////////////
    public static function direccionesXcliente(){  //api llamado desde ventas.js me trae todas las direcciones segun cliente elegido
        isadmin(); 
        $id = $_GET['id'];
        if(!is_numeric($id))return;
        $clientesRepo = new clientesRepository();
        $cliente = $clientesRepo->find((int)$id);
        if(!$cliente){
            echo json_encode(['error'=>['Cliente no encontrado.']]);
            return;
        }
        $cliente->direcciones = (new direccionesRepository())->conTarifaPorCliente((int)$id);
        //precios personalizados segun el cliente
        $cliente->preciospersonalizados = (new preciosPorClienteRepository())->findAll('idcliente', (int)$id);
        echo json_encode($cliente);
    }


    public static function apiCrearCliente(){ //api llamada desde el modulo de ventas.ts cuando se crea un cliente
        //session_start();
        isadmin();
        $alertas = [];
       
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = (new clientesService())->crearCliente($_POST, $_POST);
        }
        echo json_encode($alertas);
    }


    public static function apiActualizarcliente(){
        //session_start();
        isadmin();
        $alertas = $_SERVER['REQUEST_METHOD'] === 'POST'
            ? (new clientesService())->actualizarCliente($_POST)
            : ['error'=>['Metodo no permitido.']];
        echo json_encode($alertas);  
    }


    public static function apiEliminarCliente(){
        //session_start();
        isadmin();
        $alertas = $_SERVER['REQUEST_METHOD'] === 'POST'
            ? (new clientesService())->eliminarCliente((int)($_POST['id'] ?? 0))
            : ['error'=>['Metodo no permitido.']];
        echo json_encode($alertas); 
    }


    public static function comprasXMesXCliente(){
        //session_start();
        isadmin();
        $id = $_GET['id'];
        if(!is_numeric($id))return;
        $comprasXMesXCliente = (new clientesRepository())->comprasPorMes((int)$id, id_sucursal());
        echo json_encode($comprasXMesXCliente);
    }


    public static function ventasXCategoriasXCliente(){
        //session_start();
        isadmin();
        $id = $_GET['id'];
        if(!is_numeric($id))return;
        $ventasXCategoriasXCliente = (new clientesRepository())->ventasPorCategoria((int)$id, id_sucursal());
        echo json_encode($ventasXCategoriasXCliente);
    }


    public static function preciospersonalizados():void{
         isadmin();
        $clienteService = new clientesService();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')$alertas = $clienteService->preciospersonalizados($_POST);
        echo json_encode($alertas);
        return;
    }


    public static function eliminarPrecioPersonalizado():void{
        isadmin();
        $idcliente = $_GET['idcliente'];
        $idproducto = $_GET['idproducto'];
        if(!is_numeric($idcliente)||!is_numeric($idproducto)){
            $alertas['error'][] = "Hubo un error, intentalo nuevamente";
            echo json_encode($alertas);
            return;
        }
        $clienteService = new clientesService();
        $alertas = $clienteService->eliminarPrecioPersonalizado($idcliente, $idproducto);
        echo json_encode($alertas);
        return;
    }
}
