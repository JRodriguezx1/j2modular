<?php

declare(strict_types=1);

/** Revisa rutas, cobertura y diferencias de código del traslado de almacén. */

use App\Controllers\almacencontrolador;
use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router;
use App\Modules\Inventory\Controllers\ApiInventoryController;
use App\Modules\Inventory\Controllers\InventoryController;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';

function cuerpoNormalizado(ReflectionMethod $method): string
{
    $lines = file($method->getFileName());
    $source = implode('', array_slice($lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1));
    $body = substr($source, strpos($source, '{'));
    $tokens = token_get_all('<?php ' . $body);
    $normalized = '';
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_OPEN_TAG, T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) continue;
            $normalized .= $token[1];
        } else {
            $normalized .= $token;
        }
    }
    return $normalized;
}

$router = new Router();
(new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/Inventory/routes.php');
$callbacks = array_merge(array_values($router->getRoutes), array_values($router->postRoutes));
$routedMethods = [];
foreach ($callbacks as $callback) {
    if (!is_array($callback) || !method_exists($callback[0], $callback[1])) {
        throw new RuntimeException('Ruta de Inventory sin método callable.');
    }
    $routedMethods[] = $callback[1];
}

$old = new ReflectionClass(almacencontrolador::class);
$new = [];
foreach ([InventoryController::class, ApiInventoryController::class] as $controller) {
    foreach ((new ReflectionClass($controller))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
        if ($method->isPublic() && $method->isStatic() && $method->getDeclaringClass()->getName() === $controller) {
            $new[$method->getName()] = $method;
        }
    }
}

$missing = [];
$changed = [];
$authorizedChanges = [];
$expectedAuthorizationChanges = ['downexcelproducts', 'downexcelinsumos'];
$covered = 0;
foreach ($old->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
    if (!$method->isPublic() || !$method->isStatic() || $method->getDeclaringClass()->getName() !== almacencontrolador::class) continue;
    $name = $method->getName();
    if (!isset($new[$name]) || !in_array($name, $routedMethods, true)) {
        $missing[] = $name;
        continue;
    }
    $covered++;
    if (cuerpoNormalizado($method) !== cuerpoNormalizado($new[$name])) {
        if (in_array($name, $expectedAuthorizationChanges, true)) $authorizedChanges[] = $name;
        else $changed[] = $name;
    }
}

$_SESSION = ['login' => true, 'perfil' => '4', 'permisos' => []];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['downexcel' => '1'];
$deniedExports = [];
foreach ($expectedAuthorizationChanges as $action) {
    ob_start();
    InventoryController::$action(new Router());
    $response = ob_get_clean();
    if ($response !== '') throw new RuntimeException("La exportación {$action} respondió sin permiso.");
    $deniedExports[] = $action;
}

$result = [
    'metodos_publicos_cubiertos' => $covered,
    'rutas_GET' => count($router->getRoutes),
    'rutas_POST' => count($router->postRoutes),
    'faltantes' => $missing,
    'cambios_de_autorizacion' => $authorizedChanges,
    'exportaciones_denegadas_sin_permiso' => $deniedExports,
    'cuerpos_distintos' => $changed,
];
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
exit($missing || $changed ? 1 : 0);
