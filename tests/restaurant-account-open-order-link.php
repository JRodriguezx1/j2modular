<?php

declare(strict_types=1);

use App\Models\ventas\facturas;
use App\Modules\POS\Services\OpenOrderService;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlAccountRepository;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
Dotenv::createImmutable(__DIR__ . '/../includes')->safeLoad();

try {
    $tenants = require __DIR__ . '/../config/tenants.php';
    $db = new mysqli(
        $_ENV['DB_HOST'] ?? '',
        $_ENV['DB_USER'] ?? '',
        $_ENV['DB_PASS'] ?? '',
        $tenants['cliente']['database']
    );
    $db->set_charset('utf8mb4');
    facturas::setDB($db);

    $occupationId = (int) ($db->query("SELECT id FROM ocupaciones WHERE estado = 'en_uso' ORDER BY id LIMIT 1")->fetch_assoc()['id'] ?? 0);
    $branchId = (int) ($db->query('SELECT id FROM sucursales ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    $sellerId = (int) ($db->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    if ($occupationId === 0 || $branchId === 0 || $sellerId === 0) {
        throw new RuntimeException('Faltan ocupación activa, sucursal o vendedor para la prueba.');
    }

    $accounts = new MySqlAccountRepository($db);
    $orders = new OpenOrderService();
    $accountId = 0;
    $orderId = 0;

    $db->begin_transaction();
    try {
        $accountId = $accounts->create($occupationId, 'Prueba de enlace');
        $account = $accounts->findByIdForUpdate($accountId);
        if ($account === null || $account->getInvoiceId() !== null) {
            throw new RuntimeException('No fue posible bloquear la cuenta nueva.');
        }

        $orderId = $orders->create($branchId, null, $sellerId);
        $accounts->linkOpenOrder($accountId, $orderId);
        if ($accounts->findById($accountId)?->getInvoiceId() !== $orderId) {
            throw new RuntimeException('La cuenta no quedó vinculada a la orden.');
        }

        try {
            $accounts->linkOpenOrder($accountId, $orderId);
            throw new RuntimeException('Se permitió enlazar por segunda vez la cuenta.');
        } catch (RuntimeException $e) {
            if ($e->getMessage() !== 'La cuenta ya no admite enlazar una orden abierta.') {
                throw $e;
            }
        }
    } finally {
        $db->rollback();
    }

    $accountExists = (int) $db->query("SELECT COUNT(*) AS n FROM cuentas WHERE id = {$accountId}")->fetch_assoc()['n'];
    $orderExists = (int) $db->query("SELECT COUNT(*) AS n FROM facturas WHERE id = {$orderId}")->fetch_assoc()['n'];
    if ($accountExists !== 0 || $orderExists !== 0) {
        throw new RuntimeException('La cuenta o la orden persistió después del rollback.');
    }

    echo "Cuenta y orden enlazadas; bloqueo, segundo enlace y rollback correctos.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Prueba fallida: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
