<?php

declare(strict_types=1);

use App\Models\ventas\facturas;
use App\Modules\POS\Services\OpenOrderService;
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

    $branchId = (int) ($db->query('SELECT id FROM sucursales ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    $sellerId = (int) ($db->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    $customerId = (int) ($db->query('SELECT id FROM clientes ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    if ($branchId === 0 || $sellerId === 0 || $customerId === 0) {
        throw new RuntimeException('Faltan sucursal, vendedor o cliente para la prueba.');
    }

    $create = new OpenOrderService();
    try {
        $create->create(0, null, $sellerId);
        throw new RuntimeException('Se aceptó una sucursal inválida.');
    } catch (InvalidArgumentException) {
        // La validación debe ocurrir antes del INSERT.
    }

    $ids = [];
    $db->begin_transaction();
    try {
        foreach ([null, $customerId] as $idCliente) {
            $id = $create->create($branchId, $idCliente, $sellerId);
            $ids[] = $id;
            $row = $db->query("SELECT * FROM facturas WHERE id = {$id}")->fetch_assoc();
            if ($row === null
                || $row['estado'] !== 'Abierta'
                || (int) $row['id_sucursal'] !== $branchId
                || (int) $row['idvendedor'] !== $sellerId
                || ($row['idcliente'] === null ? null : (int) $row['idcliente']) !== $idCliente
                || (float) $row['total'] !== 0.0
                || (int) $row['cotizacion'] !== 0
                || (int) $row['remision'] !== 0
                || $row['num_orden'] !== null
                || $row['num_consecutivo'] !== null
                || $row['idconsecutivo'] !== null
                || $row['idcaja'] !== null
                || $row['idcierrecaja'] !== null
                || $row['fechapago'] !== null
                || $row['idcanaldeventa'] !== null
            ) {
                throw new RuntimeException('La cabecera abierta tiene valores inesperados.');
            }
        }
    } finally {
        $db->rollback();
    }

    $remaining = (int) $db->query('SELECT COUNT(*) AS n FROM facturas WHERE id IN (' . implode(',', $ids) . ')')->fetch_assoc()['n'];
    if ($remaining !== 0) {
        throw new RuntimeException('La operación confirmó la transacción del llamador.');
    }

    echo "Orden abierta: inserción, campos y rollback correctos.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Prueba fallida: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
