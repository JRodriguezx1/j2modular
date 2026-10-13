<?php

declare(strict_types=1);

/** Compara rutas de lectura del módulo Inventory con el controlador original. */

use App\Controllers\almacencontrolador;
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

class LegacyInventorySpy extends LegacyRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [])
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

class CoreInventorySpy extends CoreRouter
{
    public ?array $rendered = null;

    public function render($view, $datos = [], ?string $layout = null)
    {
        $this->rendered = ['view' => $view, 'data' => $datos];
    }
}

$configurations = require dirname(__DIR__) . '/config/tenants.php';
$tenant = $argv[1] ?? 'cliente';
if (!in_array($tenant, ['cliente', 'cliente1'], true)) {
    fwrite(STDERR, "Uso: php tests/inventory-read-baseline.php [cliente|cliente1]\n");
    exit(2);
}
$db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configurations[$tenant]['database']);
$db->set_charset('utf8mb4');
ActiveRecord::setDB($db);
BaseRepository::setDB($db);
$branch = sucursales::all()[0] ?? null;
if ($branch === null) throw new RuntimeException('No existe sucursal para la prueba.');

$_SESSION = [
    'login' => true, 'perfil' => '1', 'id' => 1, 'nombre' => 'Prueba Inventory',
    'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
    'permisos' => ['Habilitar modulo de inventario'],
];

$cases = [
    ['index', '/admin/almacen'],
    ['categorias', '/admin/almacen/categorias'],
    ['productos', '/admin/almacen/productos'],
    ['subproductos', '/admin/almacen/subproductos'],
    ['ajustarcostos', '/admin/almacen/ajustarcostos'],
    ['compras', '/admin/almacen/compras'],
    ['distribucion', '/admin/almacen/distribucion'],
    ['inventariar', '/admin/almacen/inventariar'],
    ['unidadesmedida', '/admin/almacen/unidadesmedida'],
    ['cambioPrecios', '/admin/almacen/cambioPrecios'],
    ['estadisticas', '/admin/almacen/estadisticas'],
    ['productosParaFormulas', '/admin/almacen/productosParaFormulas'],
    ['conversionUnidades', '/admin/almacen/conversionUnidades'],
    ['allproducts', '/admin/api/allproducts'],
    ['allsubproducts', '/admin/api/allsubproducts'],
    ['allUnidadesMedida', '/admin/api/almacen/allUnidadesMedida'],
    ['allConversionesUnidades', '/admin/api/allConversionesUnidades'],
    ['totalitems', '/admin/api/totalitems'],
    ['getStockproductosXsucursal', '/admin/api/getStockproductosXsucursal'],
    ['allproveedores', '/admin/api/allproveedores'],
    ['getItemsBajoStock', '/admin/api/getItemsBajoStock'],
];

$product = $db->query('SELECT id FROM productos ORDER BY id DESC LIMIT 1')->fetch_assoc();
if ($product !== null) $cases[] = ['componer', '/admin/almacen/componer', ['id' => (string) $product['id']]];

$results = [];
$failed = false;
foreach ($cases as $case) {
    [$method, $path] = $case;
    $query = $case[2] ?? [];
    $_GET = $query;
    $_POST = [];
    $_FILES = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $path . ($query ? '?' . http_build_query($query) : '');

    try {
        http_response_code(200);
        $legacy = new LegacyInventorySpy();
        ob_start();
        almacencontrolador::$method($legacy);
        $old = ['rendered' => $legacy->rendered, 'output' => ob_get_clean(), 'status' => http_response_code()];

        http_response_code(200);
        $modern = new CoreInventorySpy();
        (new RouteLoader($modern))->load(dirname(__DIR__) . '/app/Modules/Inventory/routes.php');
        ob_start();
        $modern->comprobarRutas();
        $new = ['rendered' => $modern->rendered, 'output' => ob_get_clean(), 'status' => http_response_code()];

        if (json_encode($old, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)
            !== json_encode($new, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)) {
            throw new RuntimeException('La vista, los datos o la salida cambiaron.');
        }
        $results[] = ['accion' => $method, 'resultado' => 'OK'];
    } catch (Throwable $error) {
        while (ob_get_level() > 0) ob_end_clean();
        $failed = true;
        $results[] = ['accion' => $method, 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
    }
}

echo json_encode(['tenant' => $tenant, 'casos' => count($results), 'fallos' => array_values(array_filter($results, static fn(array $r): bool => $r['resultado'] === 'FALLO'))], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
$db->close();
exit($failed ? 1 : 0);
