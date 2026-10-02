<?php

namespace App\Controllers;

use App\classes\Email;
use App\Models\configuraciones\usuarios; //namespace\clase hija
//use Model\configuraciones\negocio;
use MVC\Router;  //namespace\clase
 
class contabilidadcontrolador{

  public static function index(Router $router){
    //session_start();
    isadmin();
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            
    }
    //$alertas = usuarios::getAlertas();
    $router->render('admin/contabilidad/index', ['titulo'=>'Contabilidad', 'alertas'=>$alertas, 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);   //  'autenticacion/login' = carpeta/archivo
  }


}