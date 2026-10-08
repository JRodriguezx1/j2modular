<?php

declare(strict_types=1);

/**
 * Línea base del POS. Clona contapos en una base temporal, ejecuta los
 * controladores reales y elimina la copia al terminar, incluso ante errores.
 * No usa el servidor HTTP ni envía documentos a DIAN.
 *
 * Ejecutar desde la raíz: php tests/pos-baseline.php
 */

use App\Core\Routing\RouteLoader;
use App\Core\Routing\Router;
use App\Models\ActiveRecord;
use App\Models\sucursales;
use App\Modules\POS\Controllers\ventascontrolador;
use App\Repositories\BaseRepository;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/includes/funciones.php';
Dotenv::createImmutable(dirname(__DIR__) . '/includes')->safeLoad();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$source = 'contapos';
$scratch = 'codex_pos_baseline_' . bin2hex(random_bytes(4));
$db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$db->set_charset('utf8mb4');
$results = [];
$created = false;

function checkBaseline(bool $condition, string $name, array $details = []): void
{
    global $results;
    $results[] = ['prueba' => $name, 'resultado' => $condition ? 'OK' : 'FALLO'] + $details;
    if (!$condition) {
        throw new RuntimeException("Falló: {$name}");
    }
}

function scalar(mysqli $db, string $sql): mixed
{
    return $db->query($sql)->fetch_row()[0] ?? null;
}

function callController(callable $callback): array
{
    ob_start();
    try {
        $callback();
        $body = ob_get_clean();
    } catch (Throwable $error) {
        ob_end_clean();
        throw $error;
    }
    $value = json_decode($body, true);
    if (!is_array($value)) {
        throw new RuntimeException('Respuesta JSON inválida: ' . substr($body, 0, 200));
    }
    return $value;
}

try {
    $tables = [];
    $list = $db->query("SHOW FULL TABLES FROM `{$source}` WHERE Table_type = 'BASE TABLE'");
    while ($row = $list->fetch_row()) {
        $tables[] = $row[0];
    }
    checkBaseline(count($tables) > 0, 'Base de origen disponible', ['tablas' => count($tables)]);

    $db->query("CREATE DATABASE `{$scratch}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $created = true;
    $db->query('SET FOREIGN_KEY_CHECKS = 0');
    $db->select_db($scratch);
    foreach ($tables as $table) {
        $definition = $db->query("SHOW CREATE TABLE `{$source}`.`{$table}`")->fetch_row()[1];
        $db->query($definition);
    }
    foreach ($tables as $table) {
        $db->query("INSERT INTO `{$scratch}`.`{$table}` SELECT * FROM `{$source}`.`{$table}`");
    }
    $db->query('SET FOREIGN_KEY_CHECKS = 1');
    checkBaseline(count($tables) > 0, 'Copia aislada creada', ['tablas' => count($tables)]);

    // Esta copia no debe emitir avisos de inventario ni de anulación.
    $notificationKeys = "'notificacion_por_whatsApp_stock_bajo','notificacion_por_whatsApp_eliminacion_de_factura'";
    $db->query("UPDATE config_global SET valor_default = '0' WHERE clave IN ({$notificationKeys})");
    $db->query("UPDATE config_local SET valor = '0' WHERE fk_sucursalid = 1 AND clave IN ({$notificationKeys})");

    ActiveRecord::setDB($db);
    BaseRepository::setDB($db);
    $_SESSION = [
        'login' => true,
        'perfil' => '1',
        'idsucursal' => 1,
        'id' => 1,
        'nombre' => 'Prueba POS',
        'permisos' => ['Habilitar modulo de venta'],
        'sucursal' => sucursales::find('id', 1),
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/admin/ventas';

    $router = new Router();
    (new RouteLoader($router))->load(dirname(__DIR__) . '/app/Modules/POS/routes.php');
    checkBaseline(
        count($router->getRoutes) === 3 && count($router->postRoutes) === 3,
        'Se registran las seis rutas POS',
        ['GET' => array_keys($router->getRoutes), 'POST' => array_keys($router->postRoutes)]
    );

    $spy = new class extends Router {
        public string $view = '';
        public array $values = [];

        public function render($view, $datos = [], ?string $layout = null)
        {
            $this->view = $view;
            $this->values = $datos;
        }
    };
    $_GET = [];
    ventascontrolador::index($spy);
    checkBaseline(
        $spy->view === 'admin/ventas/index' && !empty($spy->values['productos']) && !empty($spy->values['cajas']),
        'GET /admin/ventas prepara vista, productos y cajas',
        ['productos' => count($spy->values['productos'] ?? []), 'cajas' => count($spy->values['cajas'] ?? [])]
    );

    $product = $db->query("SELECT p.* , s.stock AS stock_sucursal FROM productos p JOIN stockproductossucursal s ON s.productoid=p.id AND s.sucursalid=1 WHERE p.tipoproducto=0 AND p.estado=1 AND s.stock>3 AND p.precio_venta>0 ORDER BY p.id LIMIT 1")->fetch_assoc();
    checkBaseline($product !== null, 'Producto simple con existencias disponible');
    $productId = (int) $product['id'];
    $stockBefore = (float) $product['stock_sucursal'];
    $price = (float) $product['precio_venta'];
    $taxRate = (float) $product['impuesto'];
    $base = round($price / (1 + $taxRate / 100), 3);
    $tax = round($price - $base, 3);
    $cashId = (int) scalar($db, 'SELECT id FROM caja WHERE idsucursalid=1 AND estado=1 ORDER BY id LIMIT 1');
    $sequenceId = (int) scalar($db, 'SELECT id FROM consecutivos WHERE id_sucursalid=1 AND idtipofacturador=2 AND estado=1 ORDER BY id LIMIT 1');
    $closeId = (int) scalar($db, "SELECT id FROM cierrescajas WHERE idsucursal_id=1 AND idcaja={$cashId} AND estado=0 ORDER BY id DESC LIMIT 1");
    checkBaseline($cashId > 0 && $sequenceId > 0 && $closeId > 0, 'Caja abierta y consecutivo POS disponibles');
    $quotesBefore = (int) scalar($db, "SELECT totalcotizaciones FROM cierrescajas WHERE id={$closeId}");
    $marker = 'BASELINE POS ' . date('Y-m-d H:i:s');

    $line = [
        'idproducto' => (string) $productId,
        'idcategoria' => (string) $product['idcategoria'],
        'tipoproducto' => '0',
        'tipoproduccion' => (string) $product['tipoproduccion'],
        'rendimientoestandar' => (string) $product['rendimientoestandar'],
        'nombreproducto' => $product['nombre'],
        'foto' => (string) ($product['foto'] ?? ''),
        'costo' => (string) $product['precio_compra'],
        'valorunidad' => (string) $price,
        'stock' => 1,
        'promediostock' => 0,
        'percentcomision' => 0,
        'valorcomision' => 0,
        'subtotal' => $price,
        'base' => $base,
        'impuesto' => (string) $product['impuesto'],
        'valorimp' => $tax,
        'descuento' => 0,
        'total' => $price,
        'insumos' => [],
    ];
    $payload = [
        'id' => '', 'idemisor' => '', 'idcliente' => '1', 'idvendedor' => '1',
        'idcaja' => (string) $cashId, 'idconsecutivo' => (string) $sequenceId,
        'iddireccion' => '1', 'idtarifazona' => '1', 'idcanaldeventa' => '1',
        'cliente' => 'Consumidor Final', 'vendedor' => 'Prueba POS',
        'caja' => 'Caja principal', 'tipofacturador' => 'POS',
        'direccion' => '', 'tarifazona' => '',
        'carrito' => json_encode([$line], JSON_THROW_ON_ERROR),
        'totalunidades' => '1', 'mediosPago' => '[]', 'factimpuestos' => '[]',
        'recibido' => (string) $price, 'transaccion' => '', 'tipoventa' => 'Contado',
        'valoresCredito' => '{"capital":0,"abonoinicial":0}', 'cotizacion' => '1', 'remision' => '0',
        'estado' => 'Guardado', 'porcentgananciauser' => '0', 'valorgananciauser' => '0',
        'subtotal' => (string) $price, 'base' => (string) $base,
        'valorimpuestototal' => (string) $tax, 'dctox100' => '0',
        'descuento' => '0', 'total' => (string) $price,
        'observacion' => $marker, 'departamento' => '', 'ciudad' => '',
        'entrega' => 'Presencial', 'entregado' => '0', 'valortarifa' => '0',
        'puntos_descontados' => '0', 'datosAdquiriente' => '{}', 'opc1' => '', 'opc2' => '',
    ];

    $_POST = $payload;
    $quoteResponse = callController(fn() => ventascontrolador::facturar());
    checkBaseline(isset($quoteResponse['exito']), 'POST /admin/api/facturar guarda cotización', ['respuesta' => $quoteResponse]);
    $escapedMarker = $db->real_escape_string($marker);
    $quoteId = (int) scalar($db, "SELECT id FROM facturas WHERE observacion='{$escapedMarker}' AND estado='Guardado' ORDER BY id DESC LIMIT 1");
    $quoteState = $db->query("SELECT estado,cotizacion,cambioaventa,idcierrecaja FROM facturas WHERE id={$quoteId}")->fetch_assoc();
    $stockAfterQuote = (float) scalar($db, "SELECT stock FROM stockproductossucursal WHERE productoid={$productId} AND sucursalid=1");
    $quotesAfter = (int) scalar($db, "SELECT totalcotizaciones FROM cierrescajas WHERE id={$closeId}");
    checkBaseline(
        $quoteId > 0 && $quoteState['estado'] === 'Guardado' && (int) $quoteState['cotizacion'] === 1
            && (int) scalar($db, "SELECT COUNT(*) FROM ventas WHERE idfactura={$quoteId}") === 1
            && $stockAfterQuote === $stockBefore && $quotesAfter === $quotesBefore + 1,
        'Cotización persiste línea y cierre sin descontar stock',
        ['stock_antes' => $stockBefore, 'stock_despues' => $stockAfterQuote, 'cotizaciones_antes' => $quotesBefore, 'cotizaciones_despues' => $quotesAfter]
    );

    $_GET = ['id' => (string) $quoteId];
    $readQuote = callController(fn() => ventascontrolador::getcotizacion_venta());
    checkBaseline(isset($readQuote['exito'], $readQuote['factura'], $readQuote['productos']) && count($readQuote['productos']) === 1, 'GET /admin/api/getcotizacion_venta recupera orden', ['productos' => count($readQuote['productos'] ?? [])]);

    $composite = $db->query("SELECT v.idfactura,v.idproducto FROM ventas v JOIN facturas f ON f.id=v.idfactura WHERE f.id_sucursal=1 AND v.tipoproducto=1 AND v.rendimientoestandar>0 ORDER BY v.id DESC LIMIT 1")->fetch_assoc();
    checkBaseline($composite !== null, 'Existe venta compuesta para consultar');
    $_GET = ['idfactura' => $composite['idfactura'], 'idproducto' => $composite['idproducto']];
    $detail = callController(fn() => ventascontrolador::detalleProductoCompuesto());
    checkBaseline(count($detail) > 0, 'GET /admin/api/ventas/detalleProductoCompuesto devuelve insumos', ['insumos' => count($detail)]);

    $_POST = [
        'id' => (string) $quoteId, 'idcaja' => (string) $cashId,
        'idconsecutivo' => (string) $sequenceId,
        'mediosPago' => json_encode([['idmediopago' => 1, 'id_factura' => 0, 'valor' => $price]], JSON_THROW_ON_ERROR),
        'recibido' => (string) $price, 'transaccion' => '', 'tipoventa' => 'Contado',
        'estado' => 'Paga', 'cambioaventa' => '1', 'observacion' => $marker . ' CONVERTIDA',
    ];
    $paidResponse = callController(fn() => ventascontrolador::facturarCotizacion());
    checkBaseline(isset($paidResponse['exito'], $paidResponse['idfactura']), 'POST /admin/api/facturarCotizacion convierte orden', ['respuesta' => array_intersect_key($paidResponse, array_flip(['exito', 'idfactura']))]);
    $paidId = (int) $paidResponse['idfactura'];
    $paidState = $db->query("SELECT estado,cotizacion,remision,cambioaventa,referencia,idcierrecaja FROM facturas WHERE id={$paidId}")->fetch_assoc();
    $originalState = $db->query("SELECT estado,cambioaventa FROM facturas WHERE id={$quoteId}")->fetch_assoc();
    $stockAfterPaid = (float) scalar($db, "SELECT stock FROM stockproductossucursal WHERE productoid={$productId} AND sucursalid=1");
    $saleLineId = (int) scalar($db, "SELECT id FROM ventas WHERE idfactura={$paidId} LIMIT 1");
    checkBaseline(
        $paidState['estado'] === 'Paga' && $originalState['estado'] === 'Aceptada'
            && (int) $originalState['cambioaventa'] === 1 && $stockAfterPaid === $stockBefore - 1
            && $saleLineId > 0 && (int) scalar($db, "SELECT COUNT(*) FROM factmediospago WHERE id_factura={$paidId}") > 0,
        'Pago crea factura, pago y línea; descuenta una unidad',
        ['factura' => $paidId, 'stock_antes' => $stockBefore, 'stock_despues' => $stockAfterPaid]
    );

    $_POST = [
        'id' => (string) $paidId, 'observacioneliminacion' => 'Anulación de prueba POS',
        'devolverinv' => '1',
        'inv' => json_encode([['idventa' => $saleLineId, 'cantidad' => 1]], JSON_THROW_ON_ERROR),
    ];
    $cancelResponse = callController(fn() => ventascontrolador::eliminarOrden());
    checkBaseline(isset($cancelResponse['exito']), 'POST /admin/api/eliminarOrden anula factura', ['respuesta' => $cancelResponse]);
    $stockAfterCancel = (float) scalar($db, "SELECT stock FROM stockproductossucursal WHERE productoid={$productId} AND sucursalid=1");
    $cancelState = (string) scalar($db, "SELECT estado FROM facturas WHERE id={$paidId}");
    checkBaseline($cancelState === 'Eliminada' && $stockAfterCancel === $stockBefore, 'Anulación devuelve existencias', ['stock_final' => $stockAfterCancel]);

    $_POST = array_replace($payload, [
        'estado' => 'Paga', 'cotizacion' => '0', 'observacion' => $marker . ' DIRECTA',
        'mediosPago' => json_encode([['idmediopago' => 1, 'id_factura' => 0, 'valor' => $price]], JSON_THROW_ON_ERROR),
        'factimpuestos' => json_encode([['id_impuesto' => $taxRate === 19.0 ? 4 : 2, 'facturaid' => 0, 'basegravable' => $base, 'valorimpuesto' => $tax]], JSON_THROW_ON_ERROR),
    ]);
    $directResponse = callController(fn() => ventascontrolador::facturar());
    checkBaseline(isset($directResponse['exito'], $directResponse['idfactura'], $directResponse['dataInvoice']), 'POST /admin/api/facturar cobra venta directa', ['factura' => $directResponse['idfactura'] ?? null]);
    $directId = (int) $directResponse['idfactura'];
    $directLineId = (int) scalar($db, "SELECT id FROM ventas WHERE idfactura={$directId} LIMIT 1");
    $stockAfterDirect = (float) scalar($db, "SELECT stock FROM stockproductossucursal WHERE productoid={$productId} AND sucursalid=1");
    checkBaseline(
        (string) scalar($db, "SELECT estado FROM facturas WHERE id={$directId}") === 'Paga'
            && $directLineId > 0 && (int) scalar($db, "SELECT COUNT(*) FROM factmediospago WHERE id_factura={$directId}") > 0
            && $stockAfterDirect === $stockBefore - 1,
        'Venta directa persiste factura, línea y pago; descuenta stock',
        ['stock_antes' => $stockBefore, 'stock_despues' => $stockAfterDirect]
    );

    $_POST = [
        'id' => (string) $directId, 'observacioneliminacion' => 'Anulación de venta directa de prueba',
        'devolverinv' => '1',
        'inv' => json_encode([['idventa' => $directLineId, 'cantidad' => 1]], JSON_THROW_ON_ERROR),
    ];
    $cancelDirect = callController(fn() => ventascontrolador::eliminarOrden());
    $stockAfterDirectCancel = (float) scalar($db, "SELECT stock FROM stockproductossucursal WHERE productoid={$productId} AND sucursalid=1");
    checkBaseline(
        isset($cancelDirect['exito']) && (string) scalar($db, "SELECT estado FROM facturas WHERE id={$directId}") === 'Eliminada'
            && $stockAfterDirectCancel === $stockBefore,
        'Anulación de venta directa restituye stock',
        ['stock_final' => $stockAfterDirectCancel]
    );

    $invoicesBeforeInvalid = (int) scalar($db, 'SELECT COUNT(*) FROM facturas');
    $_POST = array_replace($payload, ['carrito' => '[]']);
    $invalidCart = callController(fn() => ventascontrolador::facturar());
    checkBaseline(isset($invalidCart['error']) && str_contains($invalidCart['error'][0], 'carrito no contiene productos'), 'Facturar rechaza carrito vacío');

    $_POST = ['id' => (string) $quoteId, 'idcaja' => (string) $cashId, 'idconsecutivo' => (string) $sequenceId, 'estado' => 'Guardado'];
    $invalidConversion = callController(fn() => ventascontrolador::facturarCotizacion());
    checkBaseline(isset($invalidConversion['error']) && str_contains($invalidConversion['error'][0], 'solo permite pagar'), 'Conversión rechaza estado distinto de Paga');

    $_POST = ['id' => '0', 'devolverinv' => '0', 'observacioneliminacion' => ''];
    $invalidCancellation = callController(fn() => ventascontrolador::eliminarOrden());
    checkBaseline(isset($invalidCancellation['error']) && str_contains($invalidCancellation['error'][0], 'identificador de la orden'), 'Anulación rechaza identificador inválido');
    checkBaseline((int) scalar($db, 'SELECT COUNT(*) FROM facturas') === $invoicesBeforeInvalid, 'Solicitudes inválidas no crean facturas');
} catch (Throwable $error) {
    $results[] = ['prueba' => 'Ejecución', 'resultado' => 'FALLO', 'detalle' => $error->getMessage()];
} finally {
    if ($created) {
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->query("DROP DATABASE `{$scratch}`");
    }
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;
}

exit(in_array('FALLO', array_column($results, 'resultado'), true) ? 1 : 0);
