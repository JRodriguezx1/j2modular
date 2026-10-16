<?php

declare(strict_types=1);

use App\Models\ventas\facturas;
use App\Modules\POS\Services\OpenOrderService;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
Dotenv::createImmutable(__DIR__ . '/../includes')->safeLoad();

function assertOrderTotals(mysqli $db, int $orderId, int $expectedLines, string $expectedQuantity): array
{
    $summary = $db->query(
        "SELECT COUNT(*) AS line_count, COALESCE(SUM(cantidad), 0) AS quantity,
                COALESCE(SUM(subtotal), 0) AS subtotal, COALESCE(SUM(base), 0) AS base,
                COALESCE(SUM(valorimp), 0) AS tax, COALESCE(SUM(descuento), 0) AS discount,
                COALESCE(SUM(total), 0) AS total, COALESCE(SUM(valorcomision), 0) AS commission
         FROM ventas WHERE idfactura = {$orderId}"
    )->fetch_assoc();
    $order = $db->query(
        "SELECT estado, totalunidades, subtotal, base, valorimpuestototal, descuento, total,
                valorgananciauser FROM facturas WHERE id = {$orderId}"
    )->fetch_assoc();

    if ($order === null || $order['estado'] !== 'Abierta'
        || (int) $summary['line_count'] !== $expectedLines
        || (float) $summary['quantity'] !== (float) $expectedQuantity) {
        throw new RuntimeException('La orden no conserva el estado, las líneas o la cantidad esperados.');
    }

    foreach ([
        'totalunidades' => ['quantity', 2],
        'subtotal' => ['subtotal', 2],
        'base' => ['base', 3],
        'valorimpuestototal' => ['tax', 3],
        'descuento' => ['discount', 2],
        'total' => ['total', 2],
        'valorgananciauser' => ['commission', 2],
    ] as $headerColumn => [$detailColumn, $scale]) {
        if (number_format((float) $order[$headerColumn], $scale, '.', '')
            !== number_format((float) $summary[$detailColumn], $scale, '.', '')) {
            throw new RuntimeException("El total {$headerColumn} no coincide con las líneas.");
        }
    }

    return $order;
}

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

    $products = $db->query(
        'SELECT s.sucursalid AS branch_id, p.id AS product_id, p.precio_venta
         FROM productos p
         JOIN stockproductossucursal s ON s.productoid = p.id
         WHERE p.estado = 1 AND p.visible = 1 AND p.tipoproducto = 0
           AND s.habilitarventa = 1 AND p.precio_venta > 0
         ORDER BY s.sucursalid, p.id'
    );
    $branchId = 0;
    $candidates = [];
    while ($product = $products->fetch_assoc()) {
        $candidateBranch = (int) $product['branch_id'];
        if ($branchId === 0) {
            $branchId = $candidateBranch;
        }
        if ($candidateBranch !== $branchId) {
            break;
        }
        $candidates[] = $product;
        if (count($candidates) === 2) {
            break;
        }
    }
    $sellerId = (int) ($db->query('SELECT id FROM usuarios ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 0);
    if (count($candidates) < 2 || $sellerId === 0) {
        throw new RuntimeException('Se requieren dos productos simples habilitados en la misma sucursal y un vendedor.');
    }

    $service = new OpenOrderService();
    $orderId = 0;
    $lineIds = [];
    $db->begin_transaction();
    try {
        $orderId = $service->create($branchId, null, $sellerId);

        $first = $service->addSimpleProduct($orderId, (int) $candidates[0]['product_id'], '1.00', $branchId);
        $lineIds[] = (int) $first['lineId'];
        $firstOrder = assertOrderTotals($db, $orderId, 1, '1.00');
        if (number_format((float) $firstOrder['total'], 2, '.', '') !== $candidates[0]['precio_venta']) {
            throw new RuntimeException('El primer producto no quedó al precio esperado.');
        }

        $second = $service->addSimpleProduct($orderId, (int) $candidates[1]['product_id'], '2.00', $branchId);
        $lineIds[] = (int) $second['lineId'];
        $secondOrder = assertOrderTotals($db, $orderId, 2, '3.00');
        $expectedTotal = (float) $candidates[0]['precio_venta'] + 2 * (float) $candidates[1]['precio_venta'];
        if (number_format((float) $secondOrder['total'], 2, '.', '') !== number_format($expectedTotal, 2, '.', '')) {
            throw new RuntimeException('El total acumulado de los dos productos es incorrecto.');
        }
        if ($lineIds[0] <= 0 || $lineIds[1] <= 0 || $lineIds[0] === $lineIds[1]) {
            throw new RuntimeException('Las dos adiciones no crearon líneas distintas.');
        }

        foreach ([
            fn () => $service->addSimpleProduct($orderId, (int) $candidates[0]['product_id'], '0', $branchId),
            fn () => $service->addSimpleProduct($orderId, 2147483647, '1.00', $branchId),
            fn () => $service->addSimpleProduct($orderId, (int) $candidates[0]['product_id'], '1.00', 2147483647),
        ] as $rejectedCall) {
            try {
                $rejectedCall();
                throw new RuntimeException('Se aceptó una adición inválida.');
            } catch (InvalidArgumentException $e) {
                // Cada caso debe ser rechazado por el servicio.
            }
        }
        assertOrderTotals($db, $orderId, 2, '3.00');

        $db->query("UPDATE facturas SET estado = 'Paga' WHERE id = {$orderId}");
        try {
            $service->addSimpleProduct($orderId, (int) $candidates[0]['product_id'], '1.00', $branchId);
            throw new RuntimeException('Se permitió agregar un producto a una factura pagada.');
        } catch (InvalidArgumentException $e) {
            // Una factura pagada no admite nuevas líneas.
        }
        $db->query("UPDATE facturas SET estado = 'Abierta' WHERE id = {$orderId}");
        assertOrderTotals($db, $orderId, 2, '3.00');
    } finally {
        $db->rollback();
    }

    $remainingOrder = (int) $db->query("SELECT COUNT(*) AS n FROM facturas WHERE id = {$orderId}")->fetch_assoc()['n'];
    $remainingLines = (int) $db->query('SELECT COUNT(*) AS n FROM ventas WHERE id IN (' . implode(',', $lineIds) . ')')->fetch_assoc()['n'];
    if ($remainingOrder !== 0 || $remainingLines !== 0) {
        throw new RuntimeException('El rollback dejó persistida la orden o sus líneas.');
    }

    echo "Orden abierta: dos productos, totales, rechazos y rollback correctos.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Prueba fallida: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
