<?php

declare(strict_types=1);

/**
 * Contrato de GET /admin/caja/cerrarcaja. Solo lee contapos y j2a1;
 * la base local de cliente2 está desactualizada y se omite.
 *
 * Ejecutar: php tests/cash-close-baseline.php
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

class LegacyCloseSpy extends LegacyRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [])
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

class CoreCloseSpy extends CoreRouter
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
        $results[] = ['tenant' => $tenant, 'resultado' => 'OMITIDO', 'detalle' => 'Se asume el esquema de contapos.'];
        continue;
    }

    try {
        $db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configuration['database']);
        $db->set_charset('utf8mb4');
        ActiveRecord::setDB($db);
        BaseRepository::setDB($db);
        $branch = sucursales::all()[0] ?? null;
        if ($branch === null) {
            throw new RuntimeException('No existe sucursal para la prueba.');
        }

        $_SESSION = [
            'login' => true, 'perfil' => '1', 'id' => 1,
            'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
            'permisos' => ['Habilitar modulo de caja'],
        ];

        $oldRouter = new LegacyCloseSpy();
        cajacontrolador::cerrarcaja($oldRouter);
        if (($oldRouter->rendered['view'] ?? null) !== 'admin/caja/cerrarcaja') {
            throw new RuntimeException('La acción antigua no preparó la vista de cierre.');
        }
        $oldData = $oldRouter->rendered['data'];
        foreach (['cajas', 'conflocal', 'mediospagos', 'ultimocierre', 'facturas', 'costo_total', 'titulo', 'alertas', 'sucursales', 'user'] as $key) {
            if (!array_key_exists($key, $oldData)) {
                throw new RuntimeException("Falta la variable antigua {$key}.");
            }
        }

        if (!method_exists(CajaController::class, 'cerrarcaja')) {
            throw new RuntimeException('Falta la acción migrada de Cash.');
        }
        $newRouter = new CoreCloseSpy();
        (new RouteLoader($newRouter))->load(dirname(__DIR__) . '/app/Modules/Cash/routes.php');
        if (($newRouter->getRoutes['/admin/caja/cerrarcaja'][0] ?? null) !== CajaController::class) {
            throw new RuntimeException('La ruta de cierre no apunta a Cash.');
        }
        $_SERVER['REQUEST_URI'] = '/admin/caja/cerrarcaja';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $newRouter->comprobarRutas();
        if (($newRouter->rendered['view'] ?? null) !== $oldRouter->rendered['view']
            || json_encode($newRouter->rendered['data'], JSON_THROW_ON_ERROR) !== json_encode($oldData, JSON_THROW_ON_ERROR)) {
            throw new RuntimeException('El contrato nuevo difiere del antiguo.');
        }

        $_SESSION['perfil'] = '4';
        $_SESSION['permisos'] = [];
        $deniedRouter = new CoreCloseSpy();
        CajaController::cerrarcaja($deniedRouter);
        if ($deniedRouter->rendered !== null) {
            throw new RuntimeException('Se permitió acceso sin el permiso de caja.');
        }

        $results[] = [
            'tenant' => $tenant,
            'resultado' => 'OK',
            'cajas' => count($oldData['cajas']),
            'cierre_abierto' => $oldData['ultimocierre']->id ?? null,
            'migracion' => 'mismo contrato y permiso',
        ];
        $db->close();
    } catch (Throwable $error) {
        $failed = true;
        $results[] = ['tenant' => $tenant, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
exit($failed ? 1 : 0);
