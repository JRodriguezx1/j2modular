<?php

declare(strict_types=1);

/**
 * Contrato HTTP del cierre de caja. Usa entradas de escritura inválidas para
 * comparar las respuestas sin modificar datos; la selección de caja solo lee.
 * Ejecutar: php tests/cash-close-actions-baseline.php
 */

use App\Controllers\cajacontrolador;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router;
use App\Models\ActiveRecord;
use App\Models\caja\cierrescajas;
use App\Models\sucursales;
use App\Modules\Cash\Controllers\CierreController;
use App\Repositories\BaseRepository;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$results = [];
$failed = false;
$configurations = require dirname(__DIR__) . '/config/tenants.php';
$actions = ['declaracionDinero', 'arqueocaja', 'cierrecajaconfirmado', 'datoscajaseleccionada'];

foreach ($configurations as $tenant => $configuration) {
    if ($tenant === 'cliente2') {
        $results[] = ['tenant' => $tenant, 'resultado' => 'OMITIDO', 'detalle' => 'Se asume el esquema de contapos.'];
        continue;
    }

    try {
        $db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configuration['database']);
        $db->set_charset('utf8mb4');
        ActiveRecord::setDB($db);
        BaseRepository::setDB($db);
        $branch = sucursales::all()[0] ?? null;
        if ($branch === null) throw new RuntimeException('No existe sucursal para la prueba.');

        $_SESSION = [
            'login' => true, 'perfil' => '1', 'id' => 1, 'nombre' => 'Prueba Cash',
            'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
            'permisos' => ['Habilitar modulo de caja'],
        ];
        $router = new Router();
        (new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/Cash/routes.php');
        foreach ($actions as $action) {
            if (!method_exists(CierreController::class, $action)
                || ($router->postRoutes['/admin/api/' . $action] ?? null) !== [CierreController::class, $action]) {
                throw new RuntimeException("La ruta {$action} no apunta a Cash.");
            }
        }

        $cases = [
            ['declaracionDinero', []],
            ['declaracionDinero', ['idcierrecaja' => 'invalido', 'id_mediopago' => '1', 'nombremediopago' => 'Efectivo', 'valordeclarado' => '1']],
            ['arqueocaja', []],
            ['cierrecajaconfirmado', []],
            ['datoscajaseleccionada', []],
            ['datoscajaseleccionada', ['idcaja' => '999999999']],
        ];
        $abiertos = cierrescajas::whereArray(['estado' => 0, 'idsucursal_id' => (int) $branch->id]);
        if ($abiertos !== []) $cases[] = ['datoscajaseleccionada', ['idcaja' => (string) $abiertos[0]->idcaja]];

        foreach ($cases as [$action, $post]) {
            $_POST = $post;
            ob_start();
            cajacontrolador::$action();
            $old = ob_get_clean();

            $_SERVER['REQUEST_URI'] = '/admin/api/' . $action;
            $_SERVER['REQUEST_METHOD'] = 'POST';
            ob_start();
            $router->comprobarRutas();
            $new = ob_get_clean();

            if ($new !== $old || !is_array(json_decode($new, true))) {
                throw new RuntimeException("Respuesta distinta o JSON inválido en {$action} con " . json_encode($post));
            }
        }

        $results[] = [
            'tenant' => $tenant, 'resultado' => 'OK', 'casos' => count($cases),
            'cajas_abiertas' => count($abiertos), 'contrato' => 'mismo JSON',
        ];
        $db->close();
    } catch (Throwable $error) {
        if (ob_get_level() > 0) ob_end_clean();
        $failed = true;
        $results[] = ['tenant' => $tenant, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
exit($failed ? 1 : 0);
