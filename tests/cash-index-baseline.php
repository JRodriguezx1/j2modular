<?php

declare(strict_types=1);

/**
 * Compara el contrato de GET /admin/caja entre el controlador antiguo y Cash.
 * Solo realiza consultas SELECT en contapos y j2a1. La base local j2a2 está
 * desactualizada y se asume que su esquema vigente equivale a contapos.
 *
 * Ejecutar: php tests/cash-index-baseline.php
 */

use App\Controllers\cajacontrolador;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router as CoreRouter;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Modules\Cash\Controllers\CajaController;
use App\Repositories\BaseRepository;
use Dotenv\Dotenv;
use MVC\Router as LegacyRouter;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class LegacyCashSpy extends LegacyRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [])
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

class CoreCashSpy extends CoreRouter
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

foreach ($configurations as $tenant => $configuration) {
    if ($tenant === 'cliente2') {
        $results[] = [
            'tenant' => $tenant,
            'resultado' => 'OMITIDO',
            'detalle' => 'Base local desactualizada; se asume el esquema de cliente (contapos).',
        ];
        continue;
    }
    try {
        if (!in_array('Cash', $configuration['modules'], true)) {
            throw new RuntimeException('Cash no está habilitado para el tenant.');
        }
        $db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configuration['database']);
        $db->set_charset('utf8mb4');
        ActiveRecord::setDB($db);
        BaseRepository::setDB($db);

        $branch = sucursales::all()[0] ?? null;
        if ($branch === null) {
            throw new RuntimeException('No existe una sucursal para la prueba.');
        }
        $_SESSION = [
            'login' => true,
            'perfil' => '1',
            'id' => 1,
            'idsucursal' => (int) $branch->id,
            'sucursal' => $branch,
            'permisos' => ['Habilitar modulo de caja'],
        ];

        $oldRouter = new LegacyCashSpy();
        cajacontrolador::index($oldRouter);
        if ($oldRouter->rendered === null || $oldRouter->rendered['view'] !== 'admin/caja/index') {
            throw new RuntimeException('El controlador antiguo no preparó la vista esperada.');
        }
        $oldData = $oldRouter->rendered['data'];
        $expectedKeys = ['conflocal', 'datacierrescajas', 'categoriasgastos', 'cajas', 'bancos', 'facturas', 'mediospago', 'titulo', 'sucursal', 'alertas', 'sucursales', 'user'];
        if (array_keys($oldData) !== $expectedKeys) {
            throw new RuntimeException('Cambió el conjunto u orden de variables de la vista antigua.');
        }

        if (!class_exists(CajaController::class)) {
            throw new RuntimeException('Falta el controlador migrado de Cash.');
        }
        $newRouter = new CoreCashSpy();
        $routeFile = dirname(__DIR__) . '/app/Modules/Cash/routes.php';
        (new RouteLoader($newRouter))->load($routeFile);
        if (($newRouter->getRoutes['/admin/caja'][0] ?? null) !== CajaController::class) {
            throw new RuntimeException('La ruta GET /admin/caja no apunta al controlador de Cash.');
        }
        $_SERVER['REQUEST_URI'] = '/admin/caja';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $newRouter->comprobarRutas();
        if ($newRouter->rendered === null || $newRouter->rendered['view'] !== $oldRouter->rendered['view']) {
            throw new RuntimeException('La nueva ruta no renderizó la misma vista.');
        }
        if (json_encode($newRouter->rendered['data'], JSON_THROW_ON_ERROR) !== json_encode($oldData, JSON_THROW_ON_ERROR)) {
            throw new RuntimeException('Los datos de vista nuevos no coinciden con el contrato antiguo.');
        }

        $_SESSION['perfil'] = '4';
        $_SESSION['permisos'] = [];
        $deniedRouter = new CoreCashSpy();
        CajaController::index($deniedRouter);
        if ($deniedRouter->rendered !== null) {
            throw new RuntimeException('Un usuario sin permiso pudo renderizar la vista.');
        }

        $results[] = [
            'tenant' => $tenant,
            'resultado' => 'OK',
            'vista' => $oldRouter->rendered['view'],
            'cajas' => count($oldData['cajas']),
            'facturas' => count($oldData['facturas']),
            'migracion' => 'mismo contrato y permiso',
        ];
        $db->close();
    } catch (Throwable $error) {
        $failed = true;
        $results[] = ['tenant' => $tenant, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

// El despachador activo entrega CoreRouter. El controlador antiguo exige
// LegacyRouter: esta incompatibilidad existía antes de la migración.
try {
    $_SESSION = ['login' => true, 'perfil' => '1', 'permisos' => []];
    $_SERVER['REQUEST_URI'] = '/admin/caja';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $oldDispatch = new CoreRouter();
    $oldDispatch->get('/admin/caja', [cajacontrolador::class, 'index']);
    $oldDispatch->comprobarRutas();
    $results[] = ['prueba' => 'Ruta antigua con CoreRouter', 'resultado' => 'No produjo TypeError'];
} catch (TypeError $error) {
    $results[] = ['prueba' => 'Ruta antigua con CoreRouter', 'resultado' => 'TypeError confirmado'];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
exit($failed ? 1 : 0);
