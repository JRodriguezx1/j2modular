<?php

declare(strict_types=1);

/**
 * Compara cuatro consultas históricas de Cash con sus acciones originales.
 * Solo lee contapos y j2a1. cliente2 se omite por acuerdo con el usuario.
 * Ejecutar: php tests/cash-reports-baseline.php
 */

use App\Controllers\cajacontrolador;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router as CoreRouter;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Modules\Cash\Controllers\CajaController;
use App\Repositories\BaseRepository;
use App\services\caja\CajaConsultasService;
use Dotenv\Dotenv;
use MVC\Router as LegacyRouter;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class LegacyReportsSpy extends LegacyRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [])
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

class CoreReportsSpy extends CoreRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [], ?string $layout = null)
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

$results = [];
$failed = false;
$configurations = require dirname(__DIR__) . '/config/tenants.php';
$actions = ['zetadiario', 'fechazetadiario', 'ultimoscierres', 'detallecierrecaja'];

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
            'login' => true, 'perfil' => '1', 'id' => 1,
            'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
            'permisos' => ['Habilitar modulo de caja'],
        ];

        $router = new CoreReportsSpy();
        (new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/Cash/routes.php');
        foreach ($actions as $action) {
            if (!method_exists(CajaController::class, $action)
                || ($router->getRoutes['/admin/caja/' . $action] ?? null) !== [CajaController::class, $action]) {
                throw new RuntimeException("La ruta {$action} no apunta a Cash.");
            }
        }

        $cierres = (new CajaConsultasService())->listarCierresFinalizados((int) $branch->id);
        $cierreId = $cierres[0]->id ?? null;
        $cases = [
            ['zetadiario', []],
            ['fechazetadiario', ['id' => '-1']],
            ['fechazetadiario', ['id' => '0']],
            ['fechazetadiario', ['id' => '999999999']],
            ['fechazetadiario', ['id' => 'invalido']],
            ['fechazetadiario', ['id' => '-2']],
            ['ultimoscierres', []],
            ['detallecierrecaja', ['id' => '999999999']],
            ['detallecierrecaja', ['id' => 'invalido']],
        ];
        if ($cierreId !== null) {
            $cases[] = ['fechazetadiario', ['id' => (string) $cierreId]];
            $cases[] = ['detallecierrecaja', ['id' => (string) $cierreId]];
        }

        foreach ($cases as [$action, $query]) {
            $_GET = $query;
            $old = new LegacyReportsSpy();
            cajacontrolador::$action($old);

            $new = new CoreReportsSpy();
            (new RouteLoader($new))->load(dirname(__DIR__) . '/app/Modules/Cash/routes.php');
            $_SERVER['REQUEST_URI'] = '/admin/caja/' . $action . ($query ? '?' . http_build_query($query) : '');
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $new->comprobarRutas();

            if (json_encode($new->rendered, JSON_THROW_ON_ERROR) !== json_encode($old->rendered, JSON_THROW_ON_ERROR)) {
                throw new RuntimeException("Contrato diferente en {$action} con " . json_encode($query));
            }
        }

        $_SESSION['perfil'] = '4';
        $_SESSION['permisos'] = [];
        $_GET = ['id' => (string) ($cierreId ?? 1)];
        foreach ($actions as $action) {
            $denied = new CoreReportsSpy();
            CajaController::$action($denied);
            if ($denied->rendered !== null) throw new RuntimeException("{$action} permitió acceso sin permiso.");
        }

        $results[] = [
            'tenant' => $tenant, 'resultado' => 'OK',
            'casos' => count($cases), 'cierres_finalizados' => count($cierres),
            'contrato' => 'mismas vistas, datos y permiso',
        ];
        $db->close();
    } catch (Throwable $error) {
        $failed = true;
        $results[] = ['tenant' => $tenant, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
exit($failed ? 1 : 0);
