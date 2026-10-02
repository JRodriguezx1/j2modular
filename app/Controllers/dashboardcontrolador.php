<?php


namespace App\Controllers;
//require __DIR__ . '/../classes/dompdf/autoload.inc.php';
//require __DIR__ . '/../classes/twilio-php-main/src/Twilio/autoload.php';
//require __DIR__ . '/../classes/aws/aws-autoloader.php';
//require __DIR__ . '/../classes/RECEIPT-main/ticket.php';

use App\Models\caja\cierrescajas;
use App\Core\Routing\Router;
use App\Models\configuraciones\usuarios;
use App\Models\inventario\stockproductossucursal;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
//use ticketPOS;


//use Dompdf\Dompdf;
//use Twilio\Rest\Client;
//use Aws\Sns\SnsClient;


class dashboardcontrolador{

    public static function index(Router $router) {
        //session_start();
        isadmin();
        date_default_timezone_set('America/Bogota');
        $idsucursal = id_sucursal();
        /*
        $dompdf = new Dompdf();
        ...
        ...
        ...
        */
        //$pos = new ticketPOS();
        //$pos->generar();
        

        if(!tienePermiso('Mostrar dashboard')&&userPerfil()>3)return;

        // calculo de los ingresos y gastos de las cajas abiertas
        $sql = "SELECT SUM(ventasenefectivo) AS efectivofacturado, SUM(ingresoventas) AS totalingreso, SUM(totalfacturaseliminadas) AS totalfacturaseliminadas, 
                SUM(totalfacturas) AS totalfacturas, SUM(gastoscaja) AS gastoscaja, SUM(descuentofe+descuentopos) AS totaldescuentos
                FROM cierrescajas WHERE idsucursal_id = $idsucursal AND estado = 0;";
        $indicadoreseconomicos = cierrescajas::camposJoinObj($sql);

        //total productos vendids, el producto mas vendido y top de productos vendidos ultimos 30 dias
        $sql = "SELECT p.nombre, p.id AS idproducto, SUM(v.cantidad) AS topunidadesvendidas, SUM(SUM(v.cantidad)) OVER () AS totalproductosvendidos,
                SUM(v.subtotal) AS totaldinero, ROUND((SUM(v.cantidad) / SUM(SUM(v.cantidad)) OVER ()) * 100, 2) AS porcentaje
                FROM ventas v
                INNER JOIN facturas f ON f.id = v.idfactura
                INNER JOIN productos p ON p.id = v.idproducto
                WHERE f.fechapago >= CURDATE() - INTERVAL 30 DAY AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
                GROUP BY p.id, p.nombre ORDER BY topunidadesvendidas DESC LIMIT 8;";
        $cantidadesproductos = ventas::camposJoinObj($sql);
        
        //calcular el stock minimo de los primeros 6 productos mas agotados
        $sql = "SELECT p.nombre, p.unidadmedida, sps.stock, sps.stockminimo
                FROM stockproductossucursal sps
                INNER JOIN productos p ON p.id = sps.productoid
                WHERE sps.stock <= sps.stockminimo AND sps.sucursalid = $idsucursal ORDER BY sps.stock ASC LIMIT 6;";
        $productosSotckMin = stockproductossucursal::camposJoinObj($sql);

        $router->render('admin/dashboard/index', ['titulo'=>'Inicio', 'indicadoreseconomicos'=>$indicadoreseconomicos, 'cantidadesproductos'=>$cantidadesproductos, 'productosSotckMin'=>$productosSotckMin, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function perfil(Router $router) {
        $alertas = [];
        //session_start();
        isadmin();
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $usuario = usuarios::find('id', $_SESSION['id']);
            $hashpass = $usuario->password;
            $x = $usuario->comprobar_password($_POST['password']);
            $usuario->compara_objetobd_post($_POST);
            $alertas = $usuario->validarEmail();
            $usuario->password = $hashpass;
            if(empty($alertas)){
                //////// validar el password principal
                if($x){
                    //////// validar si hubo cambio de passoword y que no se vacio el nuevo password
                    if(isset($_POST['password2'])&&!empty(trim($_POST['password2']))){
                        $usuario->password = $usuario->password2;
                        $usuario->hashPassword();
                    }
                    $r = $usuario->actualizar();
                    if($r){
                        $alertas['exito'][] = "Datos actualizados";
                    }else{ $alertas['error'][] = "Hubo un error al actualizar los datos de usuario"; }
                }else{
                    $alertas['error'][] = "Password incorrecto";
                }
            }
        }
        $usuario = usuarios::find('id', $_SESSION['id']);
        $router->render('admin/dashboard/perfil', ['titulo'=>'Perfil', 'usuario'=>$usuario, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function actualizaremail(Router $router) {
        $alertas = [];
        //session_start();
        isadmin();
        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            $usuario = usuarios::find('id', $_SESSION['id']);
            $usuario->compara_objetobd_post($_POST);
            $r = $usuario->actualizar();
            if($r){
                $alertas['exito'][] = "Email actualizado";
            }else{ $alertas['error'][] = "Hubo un error al actualizar el email"; }
        }
        $usuario = usuarios::find('id', $_SESSION['id']);
        $router->render('admin/dashboard/perfil', ['titulo'=>'Perfil', 'usuario'=>$usuario, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }
    

    ////////////////////////------   API   -------//////////////////////////////

    public static function alldays(){  //api
        $alldays = pagosxdia::ordenarlimite('id', 'DESC', 8);
        echo json_encode($alldays);
    }

    public static function totalcitas(){  //api
        date_default_timezone_set('America/Bogota');
        $fecha = date('Y-m-d'); //dia actual hoy
        $citasxdia = citas::idregistros('start', $fecha);
        //$citasxdia = citas::whereArray(['start'=>$fecha]);
        foreach($citasxdia as $value){
           // $value->idservicio = empserv::uncampo('id', $value->id_empserv, 'idservicio');
            $value->facturacion = facturacion::find('idcita', $value->id);
        }
        echo json_encode($citasxdia);
    }

    public static function ventasVsGastos(){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        //COALESCE(v.ventas_totales, 0) - COALESCE(g.gastos_totales, 0) AS utilidad
        $sql = "SELECT CONCAT(v.anio, '-', LPAD(v.mes, 2, '0')) AS periodo, COALESCE(v.ventas_totales, 0) AS ventas_totales, COALESCE(g.gastos_totales, 0) AS gastos_totales
                FROM (
                    SELECT YEAR(fechapago) AS anio, MONTH(fechapago) AS mes, SUM(total) AS ventas_totales
                    FROM facturas
                    WHERE fechapago >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND estado = 'Paga' AND id_sucursal = $idsucursal
                    GROUP BY YEAR(fechapago), MONTH(fechapago)
                ) v
                LEFT JOIN (
                    SELECT YEAR(fecha) AS anio, MONTH(fecha) AS mes, SUM(valor) AS gastos_totales
                    FROM gastos
                    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND id_sucursalfk = $idsucursal
                    GROUP BY YEAR(fecha), MONTH(fecha)
                ) g
                ON v.anio = g.anio AND v.mes = g.mes ORDER BY periodo DESC;";
        $ventasvsgastos = facturas::camposJoinObj($sql);
        $periodo = [];
        $ventastotales = [];
        $gastostotales = [];
        foreach($ventasvsgastos as $value){
            $periodo[] = $value->periodo;
            $ventastotales[] = $value->ventas_totales;
            $gastostotales[] = $value->gastos_totales;
        }
        echo json_encode(['periodo'=>$periodo, 'ventastotales'=>$ventastotales, 'gastostotales'=>$gastostotales]);
    }


    public static function ultimos7dias(){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        $sql = "SELECT d.dia, COALESCE(SUM(f.total), 0) AS ventas_totales
                FROM (
                    SELECT CURDATE() AS dia
                    UNION ALL SELECT CURDATE() - INTERVAL 1 DAY
                    UNION ALL SELECT CURDATE() - INTERVAL 2 DAY
                    UNION ALL SELECT CURDATE() - INTERVAL 3 DAY
                    UNION ALL SELECT CURDATE() - INTERVAL 4 DAY
                    UNION ALL SELECT CURDATE() - INTERVAL 5 DAY
                    UNION ALL SELECT CURDATE() - INTERVAL 6 DAY
                ) d
                LEFT JOIN facturas f ON DATE(f.fechapago) = d.dia
                AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
                GROUP BY d.dia ORDER BY d.dia DESC;";
        $ventasultimos7dias = facturas::camposJoinObj($sql);
        echo json_encode($ventasultimos7dias);
    }
    
    
}

?>