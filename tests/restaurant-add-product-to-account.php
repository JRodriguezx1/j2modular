<?php

declare(strict_types=1);

use App\Core\Container\Container;
use App\Core\Database\MySqlTransactionManager;
use App\Core\Database\TransactionManager;
use App\Models\ventas\facturas;
use App\Modules\Restaurant\Application\UseCases\AddProductToAccount;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Providers\RestaurantServiceProvider;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
Dotenv::createImmutable(__DIR__ . '/../includes')->safeLoad();

function orderSnapshot(mysqli $db, int $orderId): array
{
    $header = $db->query(
        "SELECT estado, total, totalunidades FROM facturas WHERE id = {$orderId}"
    )->fetch_assoc();
    $details = $db->query(
        "SELECT COUNT(*) AS line_count, COALESCE(SUM(total), 0) AS total,
                COALESCE(SUM(cantidad), 0) AS quantity
         FROM ventas WHERE idfactura = {$orderId}"
    )->fetch_assoc();
    if ($header === null || $header['estado'] !== 'Abierta'
        || $header['total'] !== $details['total']
        || $header['totalunidades'] !== $details['quantity']) {
        throw new RuntimeException('La cabecera de la orden no coincide con sus consumos.');
    }
    return $details;
}

$db = null;
$accountIds = [];
$testError = null;

try {
    $tenants = require __DIR__ . '/../config/tenants.php';
    try {
        $db = new mysqli(
            $_ENV['DB_HOST'] ?? '',
            $_ENV['DB_USER'] ?? '',
            $_ENV['DB_PASS'] ?? '',
            $tenants['cliente']['database']
        );
    } catch (mysqli_sql_exception) {
        throw new RuntimeException('No se pudo conectar a la base de datos de pruebas.');
    }
    $db->set_charset('utf8mb4');
    facturas::setDB($db);

    $branchId = (int) ($db->query(
        'SELECT s.sucursalid AS id
         FROM stockproductossucursal s
         JOIN productos p ON p.id = s.productoid
         WHERE p.estado = 1 AND p.visible = 1 AND p.tipoproducto = 0
           AND s.habilitarventa = 1 AND p.precio_venta > 0
         GROUP BY s.sucursalid HAVING COUNT(*) >= 2 ORDER BY s.sucursalid LIMIT 1'
    )->fetch_assoc()['id'] ?? 0);
    $products = $branchId > 0 ? $db->query(
        "SELECT p.id, p.precio_venta
         FROM productos p JOIN stockproductossucursal s ON s.productoid = p.id
         WHERE s.sucursalid = {$branchId} AND p.estado = 1 AND p.visible = 1
           AND p.tipoproducto = 0 AND s.habilitarventa = 1 AND p.precio_venta > 0
         ORDER BY p.id LIMIT 2"
    )->fetch_all(MYSQLI_ASSOC) : [];
    $occupationId = (int) ($db->query(
        "SELECT id FROM ocupaciones WHERE estado = 'en_uso' ORDER BY id LIMIT 1"
    )->fetch_assoc()['id'] ?? 0);
    $sellerId = (int) ($db->query(
        'SELECT id FROM usuarios ORDER BY id LIMIT 1'
    )->fetch_assoc()['id'] ?? 0);
    if ($branchId === 0 || count($products) !== 2 || $occupationId === 0 || $sellerId === 0) {
        throw new RuntimeException('Faltan dos productos simples, una ocupación activa o un vendedor para la prueba.');
    }

    $container = new Container();
    $container->instance(mysqli::class, $db);
    $container->instance(TransactionManager::class, new MySqlTransactionManager($db));
    (new RestaurantServiceProvider())->register($container);
    $accounts = $container->get(AccountRepository::class);
    $addProduct = $container->get(AddProductToAccount::class);

    $accountId = $accounts->create($occupationId, 'Prueba agregar productos');
    $accountIds[] = $accountId;
    $first = $addProduct->execute($accountId, (int) $products[0]['id'], '1.00', $branchId, $sellerId);
    $orderId = (int) $first['orderId'];
    $firstDetails = orderSnapshot($db, $orderId);
    if ($orderId <= 0 || (int) $first['lineId'] <= 0
        || $accounts->findById($accountId)?->getInvoiceId() !== $orderId
        || (int) $firstDetails['line_count'] !== 1
        || (float) $firstDetails['total'] !== (float) $products[0]['precio_venta']) {
        throw new RuntimeException('La primera adición no creó y enlazó la orden correctamente.');
    }

    $second = $addProduct->execute($accountId, (int) $products[1]['id'], '2.00', $branchId, $sellerId);
    $secondDetails = orderSnapshot($db, $orderId);
    $expectedTotal = (float) $products[0]['precio_venta'] + 2 * (float) $products[1]['precio_venta'];
    if ((int) $second['orderId'] !== $orderId
        || (int) $second['lineId'] === (int) $first['lineId']
        || (int) $secondDetails['line_count'] !== 2
        || (float) $secondDetails['quantity'] !== 3.0
        || round((float) $secondDetails['total'], 2) !== round($expectedTotal, 2)) {
        throw new RuntimeException('La segunda adición no reutilizó la orden o no acumuló los totales.');
    }

    try {
        $addProduct->execute($accountId, 2147483647, '1.00', $branchId, $sellerId);
        throw new RuntimeException('Se aceptó un producto inexistente.');
    } catch (InvalidArgumentException $e) {
        // El servicio POS debe rechazar el producto y el caso de uso hacer rollback.
    }
    $afterRejection = orderSnapshot($db, $orderId);
    if ($afterRejection !== $secondDetails) {
        throw new RuntimeException('El rechazo modificó los consumos de la cuenta.');
    }

    $emptyAccountId = $accounts->create($occupationId, 'Prueba rollback primera adición');
    $accountIds[] = $emptyAccountId;
    try {
        $addProduct->execute($emptyAccountId, 2147483647, '1.00', $branchId, $sellerId);
        throw new RuntimeException('Se aceptó un producto inexistente en una cuenta nueva.');
    } catch (InvalidArgumentException $e) {
        // La factura creada en esta llamada debe revertirse.
    }
    if ($accounts->findById($emptyAccountId)?->getInvoiceId() !== null) {
        throw new RuntimeException('El rechazo dejó enlazada una factura vacía.');
    }
} catch (Throwable $e) {
    $testError = $e;
} finally {
    if ($db instanceof mysqli) {
        try {
            foreach (array_reverse($accountIds) as $accountId) {
                $row = $db->query("SELECT idfactura FROM cuentas WHERE id = {$accountId}")->fetch_assoc();
                $orderId = $row['idfactura'] ?? null;
                $db->query("DELETE FROM cuentas WHERE id = {$accountId}");
                if ($orderId !== null) {
                    $db->query('DELETE FROM facturas WHERE id = ' . (int) $orderId);
                }
            }
        } catch (Throwable $cleanupError) {
            $testError ??= $cleanupError;
        }
        $db->close();
    }
}

if ($testError !== null) {
    fwrite(STDERR, 'Prueba fallida: ' . $testError->getMessage() . PHP_EOL);
    exit(1);
}

echo "Restaurant + POS: creación, reutilización, totales, rechazos y limpieza correctos.\n";
