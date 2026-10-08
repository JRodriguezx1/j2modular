<?php

namespace App\Modules\Cash\Controllers;

use App\Core\Routing\Router;
use App\Models\sucursales;
use App\services\caja\CajaConsultasService;
use App\services\caja\CajaReportesService;

class CajaController
{
    /** GET /admin/caja: conserva el contrato de la pantalla de caja. */
    public static function index(Router $router): void{
        isadmin();
        if (!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $datos = (new CajaConsultasService())->obtenerPanelCaja(id_sucursal(), (int) $_SESSION['perfil'], (int) $_SESSION['id']);

        $router->render('admin/caja/index', $datos + ['titulo' => 'Caja', 'sucursal' => nombreSucursal(), 'alertas' => [], 'sucursales' => sucursales::all(), 'user' => $_SESSION,]);
    }

    /** GET /admin/caja/cerrarcaja: conserva el resumen del cierre principal. */
    public static function cerrarcaja(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $datos = (new CajaConsultasService())->obtenerCierrePrincipal(id_sucursal());

        $router->render('admin/caja/cerrarcaja', $datos + ['titulo' => 'Caja', 'alertas' => [], 'sucursales' => sucursales::all(), 'user' => $_SESSION]);
    }

    /** GET /admin/caja/zetadiario: índice de reportes Z. */
    public static function zetadiario(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $datos = (new CajaReportesService())->obtenerIndiceZ(id_sucursal());
        $router->render('admin/caja/zetadiario', $datos + ['titulo' => 'Caja', 'alertas' => [], 'sucursales' => sucursales::all(), 'user' => $_SESSION]);
    }

    /** GET /admin/caja/fechazetadiario?id={selector}: detalle del reporte Z. */
    public static function fechazetadiario(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $selector = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if($selector === false || (int) $selector < -1)return;

        $datos = (new CajaReportesService())->obtenerDetalleZ((int) $selector, id_sucursal());
        $alertas = [];
        if(isset($datos['error'])){
            $alertas['error'][] = $datos['error'];
            unset($datos['error']);
        }

        $router->render('admin/caja/fechazetadiario', $datos + ['titulo' => 'Caja', 'alertas' => $alertas, 'sucursales' => sucursales::all(), 'user' => $_SESSION,]);
    }

    /** GET /admin/caja/ultimoscierres: cierres finalizados de la sucursal. */
    public static function ultimoscierres(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $ultimoscierres = (new CajaConsultasService())->listarCierresFinalizados(id_sucursal());
        $router->render('admin/caja/ultimoscierres', ['titulo' => 'Caja', 'ultimoscierres' => $ultimoscierres,  'alertas' => [], 'sucursales' => sucursales::all(), 'user' => $_SESSION,]);
    }

    /** GET /admin/caja/detallecierrecaja?id={id}: cierre finalizado. */
    public static function detallecierrecaja(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja') && userPerfil() > 3)return;
        $id = $_GET['id'];
        if(!is_numeric($id))return;

        $datos = (new CajaConsultasService())->obtenerDetalleCierreFinalizado((int) $id, id_sucursal());
        if($datos === null)return;
        
        $router->render('admin/caja/detallecierrecaja', $datos + ['titulo' => 'Caja', 'alertas' => [], 'sucursales' => sucursales::all(), 'user' => $_SESSION]);
    }

}
