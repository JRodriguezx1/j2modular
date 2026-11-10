<?php

declare(strict_types=1);

// Prueba la ruta y el controlador reales con el caso de uso simulado, sin MySQL.
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Routing\Router;
use App\Modules\Restaurant\Application\UseCases\AddProductsToAccount;
use App\Modules\Restaurant\Application\UseCases\CancelAccountLine;
use App\Modules\Restaurant\Application\UseCases\GetAccountSummary;
use App\Modules\Restaurant\Application\UseCases\GetOccupationAccounts;
use App\Modules\Restaurant\Application\UseCases\SaveFirstTableOrder;
use App\Modules\Restaurant\Application\UseCases\UpdateAccountCustomer;
use App\Modules\Restaurant\Interfaces\Http\Controllers\OccupationAccountController;

class CancelControllerUseCase extends CancelAccountLine {
    public array $calls = [];
    public ?Throwable $failure = null;
    public bool $alreadyCancelled = false;
    public function __construct() {}
    public function execute(int $accountId, int $lineId, string $quantity, string $reason, int $branchId, int $userId): array {
        $this->calls[] = func_get_args();
        if ($this->failure !== null) throw $this->failure;
        return [
            'accountId' => $accountId, 'occupationId' => 20, 'orderId' => 30,
            'lineId' => $lineId, 'alreadyCancelled' => $this->alreadyCancelled,
            'totales' => ['total' => '238.00'],
        ];
    }
}

function checkCancelController(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$useCase = new CancelControllerUseCase();
$container = new Container();
// Las otras acciones del controlador no se ejecutan en esta prueba.
foreach ([GetOccupationAccounts::class, GetAccountSummary::class, SaveFirstTableOrder::class, AddProductsToAccount::class, UpdateAccountCustomer::class] as $class) {
    $container->instance($class, (new ReflectionClass($class))->newInstanceWithoutConstructor());
}
$container->instance(CancelAccountLine::class, $useCase);
$router = new Router();
$router->setContainer($container);
require __DIR__ . '/../app/Modules/Restaurant/routes.php';

$path = '/restaurant/api/accounts/lines/cancel';
checkCancelController(($router->postRoutes[$path] ?? null) === [OccupationAccountController::class, 'cancelLine'], 'Ruta POST incorrecta.');
checkCancelController(!isset($router->getRoutes[$path]), 'La anulación no debe registrarse como GET.');

$dispatch = function (array $post, array $session, string $method = 'POST') use ($router, $path): array {
    $_POST = $post;
    $_SESSION = $session;
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $path;
    http_response_code(200);
    ob_start();
    try {
        $router->comprobarRutas();
        $body = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    return [http_response_code(), json_decode($body, true, 512, JSON_THROW_ON_ERROR)];
};

$post = ['accountId' => '10', 'lineId' => '50', 'quantity' => '1.25', 'reason' => 'Error de pedido'];
$session = ['login' => true, 'idsucursal' => '2', 'id' => '7'];
$invalidCases = [
    [$post, [], 'POST', 401],
    [$post, array_replace($session, ['login' => 'true']), 'POST', 401],
    [$post, $session, 'PUT', 405],
    [array_replace($post, ['accountId' => null]), $session, 'POST', 422],
    [array_replace($post, ['accountId' => '0']), $session, 'POST', 422],
    [array_replace($post, ['accountId' => ['10']]), $session, 'POST', 422],
    [array_replace($post, ['lineId' => null]), $session, 'POST', 422],
    [array_replace($post, ['lineId' => '-1']), $session, 'POST', 422],
    [$post, array_replace($session, ['idsucursal' => null]), 'POST', 422],
    [$post, array_replace($session, ['id' => 'abc']), 'POST', 422],
    [array_replace($post, ['quantity' => null]), $session, 'POST', 422],
    [array_replace($post, ['quantity' => ['1']]), $session, 'POST', 422],
    [array_replace($post, ['reason' => null]), $session, 'POST', 422],
    [array_replace($post, ['reason' => ['Prueba']]), $session, 'POST', 422],
];
$passed = 0;
foreach ($invalidCases as [$input, $identity, $method, $expected]) {
    $before = count($useCase->calls);
    [$status, $body] = $dispatch($input, $identity, $method);
    checkCancelController($status === $expected && $body['success'] === false, 'Respuesta de rechazo incorrecta.');
    checkCancelController(count($useCase->calls) === $before, 'Se ejecutó el caso de uso con entrada inválida.');
    $passed++;
}

foreach ([false, true] as $alreadyCancelled) {
    $useCase->alreadyCancelled = $alreadyCancelled;
    // El formulario no puede reemplazar la identidad de la sesión ni elegir la factura.
    [$status, $body] = $dispatch($post + ['branchId' => '999', 'userId' => '999', 'idsucursal' => '999', 'id' => '999', 'orderId' => '999'], $session);
    checkCancelController($status === 200 && $body['success'] === true, 'Respuesta exitosa incorrecta.');
    checkCancelController(end($useCase->calls) === [10, 50, '1.25', 'Error de pedido', 2, 7], 'Argumentos o sesión incorrectos.');
    checkCancelController($body['data'] === ['accountId' => 10, 'occupationId' => 20, 'orderId' => 30, 'lineId' => 50, 'alreadyCancelled' => $alreadyCancelled, 'totales' => ['total' => '238.00']], 'Resultado incompleto.');
    $passed++;
}

$useCase->failure = new InvalidArgumentException('La cantidad supera las unidades disponibles.');
[$status, $body] = $dispatch($post, $session);
checkCancelController($status === 422 && $body['success'] === false && $body['message'] === $useCase->failure->getMessage(), 'No transmitió el error de validación.');
$passed++;

$useCase->failure = new RuntimeException('Fallo interno simulado por la prueba');
[$status, $body] = $dispatch($post, $session);
checkCancelController($status === 500 && $body === ['success' => false, 'message' => 'No fue posible anular el consumo.'], 'Se expuso un error interno.');
$passed++;

echo "OK: {$passed} escenarios de ruta, controlador y sesión, sin MySQL.\n";
