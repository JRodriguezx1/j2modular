<?php

declare(strict_types=1);

/** Renderiza una ruta de lectura de Cash en un proceso PHP independiente. */

use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Repositories\BaseRepository;
use App\services\caja\CajaConsultasService;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$tenant = $argv[1] ?? '';
$action = $argv[2] ?? '';
$selector = $argv[3] ?? null;
$configurations = require dirname(__DIR__) . '/config/tenants.php';
if (!in_array($tenant, ['cliente', 'cliente1'], true)
    || !in_array($action, ['zetadiario', 'fechazetadiario', 'ultimoscierres', 'detallecierrecaja'], true)) {
    fwrite(STDERR, "Uso: php tests/cash-reports-render.php cliente|cliente1 accion [id]\n");
    exit(2);
}

try {
    $db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $configurations[$tenant]['database']);
    $db->set_charset('utf8mb4');
    ActiveRecord::setDB($db);
    BaseRepository::setDB($db);
    $branch = sucursales::all()[0] ?? null;
    if ($branch === null) throw new RuntimeException('No existe sucursal.');

    $_SESSION = [
        'login' => true, 'perfil' => '1', 'id' => 1, 'nombre' => 'Prueba Cash',
        'idsucursal' => (int) $branch->id, 'sucursal' => $branch,
        'permisos' => ['Habilitar modulo de caja'],
    ];
    if ($selector === 'finalizado') {
        $selector = (string) ((new CajaConsultasService())->listarCierresFinalizados((int) $branch->id)[0]->id ?? '');
    }
    $_GET = $selector === null ? [] : ['id' => $selector];
    $_SERVER['REQUEST_URI'] = '/admin/caja/' . $action . ($_GET ? '?' . http_build_query($_GET) : '');
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $warnings = [];
    set_error_handler(static function (int $severity, string $message, string $file, int $line) use (&$warnings): bool {
        $warnings[] = "{$message} ({$file}:{$line})";
        return true;
    });
    ob_start();
    $router = new Router();
    (new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/Cash/routes.php');
    $router->comprobarRutas();
    $html = ob_get_clean();
    restore_error_handler();

    if ($html === '' || $warnings !== []) {
        throw new RuntimeException('Render vacío o con advertencias: ' . implode(' | ', $warnings));
    }
    echo json_encode(['tenant' => $tenant, 'accion' => $action, 'id' => $selector, 'html_bytes' => strlen($html), 'warnings' => 0]) . PHP_EOL;
    $db->close();
} catch (Throwable $error) {
    while (ob_get_level() > 0) ob_end_clean();
    restore_error_handler();
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
