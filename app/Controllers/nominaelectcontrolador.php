<?php
//$dias = facturacion::inner_join('SELECT COUNT(id) AS servicios, fecha_pago, SUM(total) AS totaldia FROM facturacion GROUP BY fecha_pago ORDER BY COUNT(id) DESC;');
namespace App\Controllers;

use App\Models\sucursales;
use MVC\Router;  //namespace\clase
 
class nominaelectcontrolador{

    public static function index(Router $router){
        //session_start();
        isadmin();
        //if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/nominaelectronica/index', ['titulo'=>'Nomina electronica', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }
}