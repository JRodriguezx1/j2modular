<?php

declare(strict_types=1);

// Prueba la ruta y el controlador con un caso de uso simulado, sin MySQL.
// Ejecutar: php tests/restaurant-first-order-controller.php

use App\Core\Container\Container;
use App\Core\Routing\Router;
use App\Modules\Restaurant\Application\UseCases\AddProductToAccount;
use App\Modules\Restaurant\Application\UseCases\GetAccountSummary;
use App\Modules\Restaurant\Application\UseCases\GetOccupationAccounts;
use App\Modules\Restaurant\Application\UseCases\SaveFirstTableOrder;
use App\Modules\Restaurant\Interfaces\Http\Controllers\OccupationAccountController;

require __DIR__ . '/../vendor/autoload.php';

class FirstOrderControllerUseCaseStub extends SaveFirstTableOrder
{
    public array $calls = [];
    public ?Throwable $failure = null;

    public function __construct() {}

    public function execute(int $resourceId, array $items, int $branchId, int $sellerId, ?int $customerId = null): array
    {
        $this->calls[] = [$resourceId, $items, $branchId, $sellerId, $customerId];
        if ($this->failure !== null) {
            throw $this->failure;
        }

        return [
            'resourceId' => $resourceId,
            'occupationId' => 21,
            'accountId' => 31,
            'orderId' => 41,
            'lineIds' => [51],
            'totales' => ['total' => '238.00'],
        ];
    }
}

function checkFirstOrder(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$useCase = new FirstOrderControllerUseCaseStub();
// Las dependencias de las otras acciones no se ejecutan en esta prueba.
$controller = new OccupationAccountController(
    (new ReflectionClass(GetOccupationAccounts::class))->newInstanceWithoutConstructor(),
    (new ReflectionClass(AddProductToAccount::class))->newInstanceWithoutConstructor(),
    (new ReflectionClass(GetAccountSummary::class))->newInstanceWithoutConstructor(),
    $useCase
);

$container = new Container();
$container->instance(OccupationAccountController::class, $controller);
$router = new Router();
$router->setContainer($container);
require __DIR__ . '/../app/Modules/Restaurant/routes.php';

$path = '/restaurant/api/tables/first-order';
checkFirstOrder(
    ($router->postRoutes[$path] ?? null) === [OccupationAccountController::class, 'saveFirstOrder'],
    'La ruta POST no apunta a la acción esperada.'
);

$dispatch = function (array $post, array $session) use ($router, $path): array {
    $_POST = $post;
    $_SESSION = $session;
    $_SERVER['REQUEST_METHOD'] = 'POST';
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

$items = [['productId' => 5, 'quantity' => '2']];
$validPost = ['resourceId' => '7', 'items' => json_encode($items)];
$validSession = ['login' => true, 'idsucursal' => '3', 'id' => '4'];

// Entradas rechazadas antes de ejecutar el caso de uso.
$invalidCases = [
    ['sin sesión', $validPost, [], 401],
    ['mesa ausente', ['items' => json_encode($items)], $validSession, 422],
    ['mesa inválida', array_replace($validPost, ['resourceId' => '0']), $validSession, 422],
    ['sucursal inválida', $validPost, array_replace($validSession, ['idsucursal' => '0']), 422],
    ['vendedor inválido', $validPost, array_replace($validSession, ['id' => 'abc']), 422],
    ['cliente inválido', $validPost + ['customerId' => '-1'], $validSession, 422],
    ['items ausentes', ['resourceId' => '7'], $validSession, 422],
    ['items sin serializar', array_replace($validPost, ['items' => $items]), $validSession, 422],
    ['JSON mal formado', array_replace($validPost, ['items' => '[']), $validSession, 422],
    ['JSON escalar', array_replace($validPost, ['items' => 'null']), $validSession, 422],
    ['lista vacía', array_replace($validPost, ['items' => '[]']), $validSession, 422],
    ['objeto en vez de lista', array_replace($validPost, ['items' => '{"productId":5}']), $validSession, 422],
];

foreach ($invalidCases as [$name, $post, $session, $expectedStatus]) {
    $before = count($useCase->calls);
    [$status, $body] = $dispatch($post, $session);
    checkFirstOrder($status === $expectedStatus && $body['success'] === false, $name);
    checkFirstOrder(count($useCase->calls) === $before, $name . ': ejecutó el caso de uso.');
}

// La identidad comercial viene de sesión aunque el formulario intente cambiarla.
foreach ([null, '', '8'] as $customer) {
    $post = $validPost + ['branchId' => '999', 'sellerId' => '999', 'idsucursal' => '999', 'id' => '999'];
    if ($customer !== null) {
        $post['customerId'] = $customer;
    }
    [$status, $body] = $dispatch($post, $validSession);
    checkFirstOrder($status === 201 && $body['success'] === true, 'No devolvió 201.');
    checkFirstOrder($body['data']['accountId'] === 31 && $body['data']['orderId'] === 41, 'Respuesta incompleta.');
    checkFirstOrder(end($useCase->calls) === [7, $items, 3, 4, $customer === '8' ? 8 : null], 'Argumentos incorrectos.');
}

$useCase->failure = new InvalidArgumentException('La mesa ya tiene una atención activa.');
[$status, $body] = $dispatch($validPost, $validSession);
checkFirstOrder($status === 422 && $body['message'] === $useCase->failure->getMessage(), 'Error de negocio incorrecto.');

$useCase->failure = new RuntimeException('Fallo interno simulado');
[$status, $body] = $dispatch($validPost, $validSession);
checkFirstOrder($status === 500 && $body['success'] === false, 'Error interno incorrecto.');
checkFirstOrder($body['message'] === 'No fue posible guardar el pedido.', 'Expuso detalles internos.');

echo "OK: ruta, validaciones, sesión, respuesta 201 y errores 422/500. Sin MySQL.\n";
