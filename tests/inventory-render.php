<?php

declare(strict_types=1);

/** Renderiza una página de Inventory con el Router activo en un proceso aislado. */

use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Repositories\BaseRepository;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$page = $argv[1] ?? '';
if (!in_array($page, ['index', 'productos', 'subproductos', 'compras', 'unidadesmedida'], true)) {
    fwrite(STDERR, "Uso: php tests/inventory-render.php index|productos|subproductos|compras|unidadesmedida\n");
    exit(2);
}

try {
    $configurations = require dirname(__DIR__) . '/config/tenants.php';
    $db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configurations['cliente']['database']);
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
    $_GET = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/admin/almacen' . ($page === 'index' ? '' : '/' . $page);

    $warnings = [];
    set_error_handler(static function (int $severity, string $message, string $file, int $line) use (&$warnings): bool {
        $warnings[] = "{$message} ({$file}:{$line})";
        return true;
    });
    ob_start();
    $router = new Router();
    (new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/Inventory/routes.php');
    $router->comprobarRutas();
    $html = ob_get_clean();
    restore_error_handler();

    if ($html === '' || $warnings !== []) {
        throw new RuntimeException('Render vacío o con advertencias: ' . implode(' | ', $warnings));
    }
    echo json_encode(['pagina' => $page, 'html_bytes' => strlen($html), 'warnings' => 0]) . PHP_EOL;
    $db->close();
} catch (Throwable $error) {
    while (ob_get_level() > 0) ob_end_clean();
    restore_error_handler();
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
