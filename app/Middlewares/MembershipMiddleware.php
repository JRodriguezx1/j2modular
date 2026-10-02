<?php 

namespace App\Middlewares;

use App\Core\Routing\Router;

class MembershipMiddleware{

    protected Router $router;
    public function __construct(Router $routerApp) {
        $this->router = $routerApp;
    }
    
    public function validarSuscripcion():void{
        session_start();
        if(empty($_SESSION))return;

        //consultar la fecha de vencimiento en DB
        $sucursal = $_SESSION['sucursal'];
        date_default_timezone_set($sucursal->timezone);
        //$host = $_SERVER['HTTP_HOST'];  //cliente.contapos.test/
        //$subdominio = explode('.', $host)[0];
        //if($subdominio == 'demo'  || $subdominio == 'megatecho'){
        //$hoy = date('Y-m-d', strtotime("-4 day"));
        $hoy = new \DateTime();
        $vencimiento = new \DateTime($sucursal->fecha_corte??'');
        $diferencia = $hoy->diff($vencimiento);
        $dias = (int)$diferencia->format('%r%a'); 
        //$dias = $hoy->diff($vencimiento)->days;
         if($dias<-4 || $sucursal->estado == 0){  //si ya han pasado 4 dias despues de la fecha de vencimiento
            if($sucursal->estado==1){
                $sucursal->estado = 0;
                $sucursal->actualizar();
            }
            if($_SERVER['REQUEST_URI'] != '/suspendido' && $_SERVER['REQUEST_URI'] != '/login' && $_SERVER['REQUEST_URI'] != '/'  && userPerfil()>1){  //si la ruta es suspendido evita volver redireccionar
                header('Location: /suspendido');
                exit;
            }
        }
        if($dias<=4 && $dias>=-4){
            $this->router->set('Aviso_vencimiento', true);
            $this->router->set('msj_titulo_aviso_vencimiento', 'Aviso de vencimiento de suscripción');
            $this->router->set('msj_texto_aviso_vencimiento', 'Tu suscripción de j2 software POS esta próximo a vencer, te invitamos a renovarla oportunamente');
        }

    }


    public function estadoConsecutivos():void{
        
    }


}
