<?php
//$dias = facturacion::inner_join('SELECT COUNT(id) AS servicios, fecha_pago, SUM(total) AS totaldia FROM facturacion GROUP BY fecha_pago ORDER BY COUNT(id) DESC;');
namespace App\Controllers;

use App\Models\sucursales;
use App\Models\suscripcioncuenta\suscripcion_pagos;
use App\Repositories\suscripcioncuenta\suscripcionPagosRepository;
use MVC\Router;  //namespace\clase
 
class suscripcioncontrolador{


public static function suspendido(Router $router){
    $sucursal = sucursales::find('id', id_sucursal());
    $plan = sucursales::camposJoinObj("SELECT * FROM planes WHERE id = {$sucursal->idplan} LIMIT 1");
    $sucursal->plan = end($plan);
    $router->render('suspension/index', ['sucursal'=>$sucursal, 'titulo'=>'Suspendido']);
}

//////////////////////////    API     /////////////////////////////

    public static function detalleSuscripcion(){
        isadmin();
        $alertas = [];
        $sucursal = sucursales::find('id', id_sucursal());
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405); // Método no permitido
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        $detalleSuscrip = json_decode(file_get_contents('php://input'), true);
        $sucursal->compara_objetobd_post($detalleSuscrip);
        $alertas = $sucursal->validar();
        if(empty($alertas)){
            $r = $sucursal->actualizar();
            if($r){
                $_SESSION['sucursal'] = $sucursal;  //actualizar variable de session que se incializa en el login
                $alertas['exito'][] = "Datos suscripcion actualizados";
            }else{
                $alertas['error'][] = "Error, intente nuevamente.";
            }
        }
        echo json_encode($alertas);
        return;
    }


    public static function registrarPago(){
        isadmin();
        $alertas = [];
        $sucursal = sucursales::find('id', id_sucursal());
        $suscripcionRepo = new suscripcionPagosRepository();
        $getDB = $suscripcionRepo->getConexion();
        $plan = $suscripcionRepo->getPlan($sucursal->idplan);

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405); // Método no permitido
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        $pago = json_decode(file_get_contents('php://input'), true);
        $entity = new suscripcion_pagos($pago);
        $alertas = $entity->validar();
        if(empty($alertas)){
            $fechaBase = !empty($sucursal->fecha_corte) ? $sucursal->fecha_corte : $entity->fecha_pago;
            $fechaCorte = new \DateTimeImmutable($fechaBase);
            $sucursal->fecha_corte = $fechaCorte->modify("+{$entity->cantidad_plan} {$plan->tipo_duracion}")->format('Y-m-d');
            $sucursal->estado = 1;
            $sucursal->descuento = 0;
            $sucursal->cargo = 0;
            $sucursal->detalledescuento = '';
            $sucursal->detallecargo = '';
            $getDB->begin_transaction();
            try {
                $r = $suscripcionRepo->insert($entity);
                if($r[0]){
                    $rs = $sucursal->actualizar();
                    if($rs){
                        $getDB->commit();
                        $alertas['exito'][] = "Pago registrado con exito";
                        $alertas['sucursal'] = $sucursal;
                        $_SESSION['sucursal'] = $sucursal; //actualizar variable de session que se incializa en el login
                    }else{
                        $alertas['error'][] = "Error, intente nuevamente.";
                        $getDB->rollback();
                    }
                }
            } catch (\Throwable $th) {
                $getDB->rollback();
                $alertas['error'][] = "Error al registrar el pago. {$th->getMessage()}";
            }
        }
        echo json_encode($alertas);
        return;
    }

  
}
