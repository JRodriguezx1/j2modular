<?php

namespace App\Modules\Cash\Controllers;

use App\Core\Routing\Router;
use App\Models\parametrizacion\config_local;
use App\services\caja\CajaCierreService;
use App\services\caja\CajaConsultasService;

/** Acciones HTTP del cierre de caja; las reglas permanecen en los servicios. */
class CierreController
{
    /** POST /admin/api/declaracionDinero. */
    public static function declaracionDinero(Router $router): void
    {
        isadmin();
        $resultado = (new CajaCierreService())->registrarDeclaracion($_POST, id_sucursal());
        echo json_encode($resultado);
    }

    /** POST /admin/api/arqueocaja. */
    public static function arqueocaja(Router $router): void
    {
        isadmin();
        $resultado = (new CajaCierreService())->registrarArqueo($_POST, id_sucursal());
        echo json_encode($resultado);
    }

    /** POST /admin/api/cierrecajaconfirmado. */
    public static function cierrecajaconfirmado(Router $router): void
    {
        isauth();
        date_default_timezone_set('America/Bogota');
        $resultado = (new CajaCierreService())->confirmarCierre(
            $_POST,
            id_sucursal(),
            (int) $_SESSION['id'],
            (string) $_SESSION['nombre'],
            config_local::getParamCaja()
        );
        echo json_encode($resultado);
    }

    /** POST /admin/api/datoscajaseleccionada. */
    public static function datoscajaseleccionada(Router $router): void
    {
        isadmin();
        $cajaId = filter_var($_POST['idcaja'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($cajaId === false) {
            echo json_encode(['error' => ['La caja seleccionada no es válida.']]);
            return;
        }

        $datos = (new CajaConsultasService())->obtenerCajaSeleccionada((int) $cajaId, id_sucursal());
        if ($datos === null) {
            echo json_encode(['error' => ['No existe un cierre abierto para la caja seleccionada.']]);
            return;
        }

        echo json_encode(['exito' => ['Cambio de caja.']] + $datos);
    }
}
