<?php

declare(strict_types=1);

// Prueba aislada: no conecta a MySQL ni modifica datos reales.
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Database\TransactionManager;
use App\Modules\POS\Services\OpenOrderService;
use App\Modules\Restaurant\Application\UseCases\CancelAccountLine;
use App\Modules\Restaurant\Domain\Entities\Account;
use App\Modules\Restaurant\Domain\Entities\Occupation;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlAccountRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlOccupationRepository;
use App\Modules\Restaurant\Providers\RestaurantServiceProvider;

class CancelLineScenario {
    public array $events = [];
    public array $arguments = [];
    public ?Account $account;
    public ?Occupation $occupation;
    public ?Throwable $failure = null;
    public bool $failCommit = false;
    public array $result = ['lineId' => 50, 'alreadyCancelled' => false, 'totales' => ['total' => '238.00']];

    public function __construct() {
        $this->account = new Account(10, 20, 30, 'General', 'abierta');
        $this->occupation = self::occupation();
    }

    public static function occupation(int $branchId = 2, string $status = 'en_uso'): Occupation {
        return new Occupation(20, $branchId, null, null, 'restaurant', '2026-11-10 12:00:00', null, null, $status, null);
    }
}

class CancelLineTransactions implements TransactionManager {
    public function __construct(private CancelLineScenario $scenario) {}
    public function begin(): void { $this->scenario->events[] = 'begin'; }
    public function commit(): void {
        $this->scenario->events[] = 'commit';
        if ($this->scenario->failCommit) throw new RuntimeException('Fallo al confirmar.');
    }
    public function rollback(): void { $this->scenario->events[] = 'rollback'; }
}

class CancelLineAccounts extends MySqlAccountRepository {
    public function __construct(private CancelLineScenario $scenario) {}
    public function findByIdForUpdate(int $accountId): ?Account {
        $this->scenario->events[] = 'account:' . $accountId;
        return $this->scenario->account;
    }
}

class CancelLineOccupations extends MySqlOccupationRepository {
    public function __construct(private CancelLineScenario $scenario) {}
    public function findByIdForUpdate(int $id): ?Occupation {
        $this->scenario->events[] = 'occupation:' . $id;
        return $this->scenario->occupation;
    }
}

class CancelLinePos extends OpenOrderService {
    public function __construct(private CancelLineScenario $scenario) {}
    public function cancelLine(int $orderId, int $lineId, int $branchId, int $userId, string $reason, string $quantity): array {
        $this->scenario->events[] = 'pos';
        $this->scenario->arguments = func_get_args();
        if ($this->scenario->failure !== null) throw $this->scenario->failure;
        return $this->scenario->result;
    }
}

function cancelUseCase(CancelLineScenario $scenario): CancelAccountLine {
    $container = new Container();
    (new RestaurantServiceProvider())->register($container);
    $container->instance(AccountRepository::class, new CancelLineAccounts($scenario));
    $container->instance(OccupationRepository::class, new CancelLineOccupations($scenario));
    $container->instance(TransactionManager::class, new CancelLineTransactions($scenario));
    $container->instance(OpenOrderService::class, new CancelLinePos($scenario));
    // El contenedor resuelve el caso de uso y el adaptador real con los bindings existentes.
    return $container->get(CancelAccountLine::class);
}

function checkCancel(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$passed = 0;
foreach ([false, true] as $alreadyCancelled) {
    $scenario = new CancelLineScenario();
    $scenario->result['alreadyCancelled'] = $alreadyCancelled;
    $result = cancelUseCase($scenario)->execute(10, 50, '1.25', '  Error de pedido  ', 2, 7);
    checkCancel($scenario->events === ['begin', 'account:10', 'occupation:20', 'pos', 'commit'], 'Orden de bloqueos o confirmación incorrecto.');
    checkCancel($scenario->arguments === [30, 50, 2, 7, 'Error de pedido', '1.25'], 'El adaptador no transmitió correctamente los argumentos.');
    checkCancel($result === ['accountId' => 10, 'occupationId' => 20, 'orderId' => 30] + $scenario->result, 'Resultado incorrecto.');
    $passed++;
}

// Las entradas inválidas se rechazan antes de comenzar una transacción.
$invalidArguments = [];
foreach ([0, 1, 4, 5] as $position) {
    $args = [10, 50, '1.00', 'Prueba', 2, 7];
    $args[$position] = 0;
    $invalidArguments[] = $args;
}
foreach (['0', '-1', '1,5', '1.001', '1e2', '', '100000000'] as $quantity) {
    $invalidArguments[] = [10, 50, $quantity, 'Prueba', 2, 7];
}
foreach (['   ', str_repeat('á', 256)] as $reason) {
    $invalidArguments[] = [10, 50, '1', $reason, 2, 7];
}
foreach ($invalidArguments as $args) {
    $scenario = new CancelLineScenario();
    try {
        cancelUseCase($scenario)->execute(...$args);
        throw new RuntimeException('Se aceptaron datos inválidos.');
    } catch (InvalidArgumentException $e) {
        checkCancel($scenario->events === [], 'Se abrió una transacción con datos inválidos.');
    }
    $passed++;
}

foreach (['missing_account', 'closed_account', 'missing_occupation', 'closed_occupation', 'other_branch', 'missing_invoice', 'invalid_invoice', 'pos_failure', 'commit_failure'] as $case) {
    $scenario = new CancelLineScenario();
    switch ($case) {
        case 'missing_account': $scenario->account = null; break;
        case 'closed_account': $scenario->account = new Account(10, 20, 30, 'General', 'cerrada'); break;
        case 'missing_occupation': $scenario->occupation = null; break;
        case 'closed_occupation': $scenario->occupation = CancelLineScenario::occupation(2, 'finalizada'); break;
        case 'other_branch': $scenario->occupation = CancelLineScenario::occupation(3); break;
        case 'missing_invoice': $scenario->account = new Account(10, 20, null, 'General', 'abierta'); break;
        case 'invalid_invoice': $scenario->account = new Account(10, 20, 0, 'General', 'abierta'); break;
        case 'pos_failure': $scenario->failure = new RuntimeException('Fallo al guardar o recalcular.'); break;
        case 'commit_failure': $scenario->failCommit = true; break;
    }
    $failure = null;
    try {
        cancelUseCase($scenario)->execute(10, 50, '1', 'Prueba', 2, 7);
    } catch (Throwable $e) {
        $failure = $e;
    }
    checkCancel($failure !== null, 'No se rechazó el caso: ' . $case);
    checkCancel(end($scenario->events) === 'rollback', 'No se revirtió el caso: ' . $case);
    if ($case === 'pos_failure') {
        checkCancel($failure === $scenario->failure, 'Se perdió el error original de POS.');
        checkCancel(!in_array('commit', $scenario->events, true), 'Se confirmó después del error de POS.');
    } elseif ($case !== 'commit_failure') {
        checkCancel($failure instanceof InvalidArgumentException, 'Tipo de error inesperado: ' . $case);
        checkCancel(!in_array('pos', $scenario->events, true), 'Se llamó a POS con una cuenta no válida: ' . $case);
    }
    $passed++;
}

echo "OK: {$passed} escenarios; caso de uso, adaptador y contenedor reales, sin MySQL.\n";
