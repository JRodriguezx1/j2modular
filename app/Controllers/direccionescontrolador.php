<?php

namespace App\Controllers;

use MVC\Router;  //namespace\clase
use App\Models\configuraciones\tarifas;
use App\Repositories\clientes\clientesRepository;
use App\services\clientesService;

 
class direccionescontrolador{

    public static function index(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $buscar = '';
        $clientesRepo = new clientesRepository();
        $clientes = $clientesRepo->all();

        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            if(($_POST['filtro'] ?? 'all') !== 'all')
                $clientes = $clientesRepo->buscar($_POST['filtro'], $_POST['buscar'] ?? '');
            $buscar = $_POST['buscar'] ?? '';

        }

        $router->render('admin/clientes/index', ['titulo'=>'Clientes', 'clientes'=>$clientes, 'alertas'=>$alertas, 'buscar'=>$buscar, 'user'=>$_SESSION]);
    }

    public static function crear(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = (new clientesService())->crearDireccion($_POST);
        }
        $clientes = (new clientesRepository())->all();
        $tarifas = tarifas::all();
        $router->render('admin/clientes/index', ['titulo'=>'clientes', 'clientes'=>$clientes, 'tarifas'=>$tarifas, 'alertas'=>$alertas, 'user'=>$_SESSION]);
    }
     


    ///////////////////////////////////  Apis ////////////////////////////////////

    public static function addDireccionCliente(){ //api llamada desde el modulo de ventas.ts cuando se crea una direccion
        //session_start();
        isadmin();
        $alertas = [];  
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $alertas = (new clientesService())->crearDireccion($_POST, true);
        }
        echo json_encode($alertas);
    }
}
