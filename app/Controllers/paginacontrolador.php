<?php

namespace App\Controllers;

use App\Models\configuraciones\negocio;
use App\Models\categoriaserv;
use App\Models\servicios;
use App\Models\empserv;
use App\Models\empleados;
//use DateTime;
use MVC\Router;



class paginacontrolador{

    public static function index(Router $router) { //metodo para mostrar la pagina inicial antes del login y registro
        //verificar la fecha de cada promocion para poner su estado en cero '0', inhabilitado. y para las promos pendientes colocar en '1' para activar
        date_default_timezone_set('America/Bogota');
        $fechaactual = new \DateTime(date('Y-m-d'));
        
    $router->render('paginas/index', ['titulo'=>'app salon'/*, 'logo'=>negocio::uncampo('id', 1, 'logo')*/]);
    }


    public static function promos(Router $router){ //metodo de la vista de las promociones
        
    $router->render('paginas/promos', ['titulo'=>'app salon', 'negocio'=>negocio::get(1)]);
    }

    
    public static function citarapida(Router $router){
        $categoriaserv = categoriaserv::all();
        $servicios = servicios::all();
        $empserv = empserv::all();
        foreach($empserv as $value){
            $value->nombre = empleados::uncampo('id', $value->idempleado, 'nombre').' '.empleados::uncampo('id', $value->idempleado, 'apellido');
            $value->servicio = servicios::find('id', $value->idservicio);
            $value->img = empleados::uncampo('id', $value->idempleado, 'img');
        }
        $router->render('paginas/citarapida', ['titulo'=>'app salon', 'categorias'=>$categoriaserv, 'servicios'=>$servicios, 'empserv'=>$empserv]);
    }
}

?>