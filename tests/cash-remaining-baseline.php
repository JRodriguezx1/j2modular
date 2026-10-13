<?php

declare(strict_types=1);

/**
 * Revisa la cobertura de las acciones antiguas y compara rutas recién movidas.
 * Las solicitudes POST usan entradas inválidas y no modifican datos.
 * Ejecutar: php tests/cash-remaining-baseline.php
 */

use App\Controllers\cajacontrolador;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router as CoreRouter;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Repositories\BaseRepository;
use Dotenv\Dotenv;
use MVC\Router as LegacyRouter;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class LegacyRemainingSpy extends LegacyRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [])
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

class CoreRemainingSpy extends CoreRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [], ?string $layout = null)
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

$routeFile = dirname(__DIR__) . '/app/Modules/Cash/routes.php';
$routeSpy = new CoreRemainingSpy();
(new RouteLoader($routeSpy))->load($routeFile);
$routes = $routeSpy->getRoutes + $routeSpy->postRoutes;
$routedMethods = [];
foreach ($routes as $path => $callback) {
    if (!is_array($callback) || !str_starts_with($callback[0], 'App\\Modules\\Cash\\Controllers\\')
        || !method_exists($callback[0], $callback[1])) {
        throw new RuntimeException("Ruta inválida de Cash: {$path}");
    }
    $routedMethods[] = $callback[1];
}
foreach ((new ReflectionClass(cajacontrolador::class))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
    if ($method->isPublic() && $method->isStatic()
        && $method->getDeclaringClass()->getName() === cajacontrolador::class
        && !in_array($method->getName(), $routedMethods, true)) {
        throw new RuntimeException("Acción antigua sin ruta en Cash: {$method->getName()}");
    }
}

$cases = [
    ['GET', '/admin/caja/categoriaGasto', 'categoriaGasto', [], []],
    ['GET', '/admin/caja/trasladosRetirosDinero', 'trasladosRetirosDinero', [], []],
    ['GET', '/admin/caja/pedidosguardados', 'pedidosguardados', [], []],
    ['GET', '/admin/caja/despachosPendientes', 'despachosPendientes', [], []],
    ['GET', '/admin/caja/detalleorden', 'detalleorden', ['id' => '1'], []],
    ['GET', '/admin/caja/ordenresumen', 'ordenresumen', ['id' => '999999999'], []],
    ['GET', '/printfacturacarta', 'printfacturacarta', ['id' => '999999999'], []],
    ['GET', '/printcotizacion', 'printcotizacion', ['id' => '999999999'], []],
    ['GET', '/printdetallecierre', 'printdetallecierre', ['id' => '999999999'], []],
    ['GET', '/admin/api/mediospagoXfactura', 'mediospagoXfactura', ['id' => 'invalido'], []],
    ['GET', '/admin/api/getInvoice', 'getInvoice', ['id' => 'invalido'], []],
    ['GET', '/admin/api/caja/despacharOrden', 'despacharOrden', ['id' => 'invalido'], []],
    ['POST', '/admin/caja/ingresoGastoCaja', 'ingresoGastoCaja', [], []],
    ['POST', '/admin/caja/categoriaGasto', 'categoriaGasto', [], []],
    ['POST', '/admin/caja/crear_categoriaGasto', 'crear_categoriaGasto', [], []],
    ['POST', '/admin/caja/editarcategoriagasto', 'editarcategoriagasto', [], []],
    ['POST', '/admin/api/cambioMedioPago', 'cambioMedioPago', [], []],
    ['POST', '/admin/api/eliminarPedidoGuardado', 'eliminarPedidoGuardado', [], []],
];

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
        if ($branch === null) throw new RuntimeException('No existe sucursal para la prueba.');
        $_SESSION = [
            'login' => true, 'perfil' => '1', 'id' => 1, 'nombre' => 'Prueba Cash',
            'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
            'permisos' => ['Habilitar modulo de caja'],
        ];

        $tenantCases = $cases;
        $sucursalId = (int) $branch->id;
        $factura = $db->query("SELECT id FROM facturas WHERE id_sucursal = {$sucursalId} ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($factura !== null) {
            $id = ['id' => (string) $factura['id']];
            foreach ([
                ['/admin/caja/ordenresumen', 'ordenresumen'],
                ['/printfacturacarta', 'printfacturacarta'],
                ['/printcotizacion', 'printcotizacion'],
                ['/admin/api/mediospagoXfactura', 'mediospagoXfactura'],
                ['/admin/api/getInvoice', 'getInvoice'],
            ] as [$path, $action]) $tenantCases[] = ['GET', $path, $action, $id, []];
        }
        $cierre = $db->query("SELECT id FROM cierrescajas WHERE idsucursal_id = {$sucursalId} AND estado = 1 ORDER BY id DESC LIMIT 1")->fetch_assoc();
        if ($cierre !== null) {
            $tenantCases[] = ['GET', '/printdetallecierre', 'printdetallecierre', ['id' => (string) $cierre['id']], []];
        }

        foreach ($tenantCases as [$httpMethod, $path, $action, $query, $post]) {
            $_GET = $query;
            $_POST = $post;
            $_FILES = [];
            $_SERVER['REQUEST_METHOD'] = $httpMethod;
            $_SERVER['REQUEST_URI'] = $path . ($query ? '?' . http_build_query($query) : '');

            http_response_code(200);
            $oldRouter = new LegacyRemainingSpy();
            ob_start();
            cajacontrolador::$action($oldRouter);
            $old = ['rendered' => $oldRouter->rendered, 'output' => ob_get_clean(), 'status' => http_response_code()];

            http_response_code(200);
            $newRouter = new CoreRemainingSpy();
            (new RouteLoader($newRouter))->load($routeFile);
            ob_start();
            $newRouter->comprobarRutas();
            $new = ['rendered' => $newRouter->rendered, 'output' => ob_get_clean(), 'status' => http_response_code()];

            if (json_encode($old, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)
                !== json_encode($new, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)) {
                throw new RuntimeException("Contrato distinto: {$httpMethod} {$path}");
            }
        }

        $results[] = ['tenant' => $tenant, 'resultado' => 'OK', 'casos' => count($tenantCases), 'rutas_cash' => count($routes)];
        $db->close();
    } catch (Throwable $error) {
        while (ob_get_level() > 0) ob_end_clean();
        $failed = true;
        $results[] = ['tenant' => $tenant, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
exit($failed ? 1 : 0);
