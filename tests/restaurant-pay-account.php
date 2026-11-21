<?php

declare(strict_types=1);

// Prueba aislada: no conecta a MySQL ni ejecuta cobros reales.
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Database\TransactionManager;
use App\Modules\POS\Services\OpenOrderService;
use App\Modules\Restaurant\Application\UseCases\PayAccount;
use App\Modules\Restaurant\Domain\Entities\Account;
use App\Modules\Restaurant\Domain\Entities\Occupation;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlAccountRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlOccupationRepository;
use App\Modules\Restaurant\Providers\RestaurantServiceProvider;

class PaymentScenario {
    public array $events = [];
    public array $arguments = [];
    public ?string $failure = null;
    public ?Account $account;
    public ?Occupation $occupation;
    public string $finishedAt = '';
    public array $result = ['exito' => ['Pago procesado'], 'idfactura' => 30, 'dataInvoice' => ['numero' => 'POS1']];

    public function __construct() {
        $this->account = new Account(10, 20, 30, 'General', 'abierta');
        $this->occupation = self::occupation();
    }

    public static function occupation(int $branchId = 2, string $status = 'en_uso'): Occupation {
        return new Occupation(20, $branchId, null, null, 'restaurant', '2026-11-21 12:00:00', null, null, $status, null);
    }

    public function record(string $event): void {
        $this->events[] = $event;
        if ($this->failure === $event) throw new RuntimeException('Fallo simulado: ' . $event);
    }
}

class PaymentTransactions implements TransactionManager {
    public function __construct(private PaymentScenario $scenario) {}
    public function begin(): void { $this->scenario->record('begin'); }
    public function commit(): void { $this->scenario->record('commit'); }
    public function rollback(): void { $this->scenario->record('rollback'); }
}

class PaymentAccounts extends MySqlAccountRepository {
    public function __construct(private PaymentScenario $scenario) {}
    public function findByIdForUpdate(int $accountId): ?Account {
        $this->scenario->record('account:' . $accountId);
        return $this->scenario->account;
    }
    public function close(int $accountId): void { $this->scenario->record('close:' . $accountId); }
}

class PaymentOccupations extends MySqlOccupationRepository {
    public function __construct(private PaymentScenario $scenario) {}
    public function findByIdForUpdate(int $id): ?Occupation {
        $this->scenario->record('occupation:' . $id);
        return $this->scenario->occupation;
    }
    public function finish(int $occupationId, string $finishedAt): void {
        $this->scenario->finishedAt = $finishedAt;
        $this->scenario->record('finish:' . $occupationId);
    }
}

class PaymentPos extends OpenOrderService {
    public function __construct(private PaymentScenario $scenario) {}
    public function pay(int $orderId, int $branchId, int $userId, array $data): array {
        $this->scenario->arguments = func_get_args();
        $this->scenario->record('pos');
        return $this->scenario->result;
    }
}

function paymentUseCase(PaymentScenario $scenario): PayAccount {
    $container = new Container();
    (new RestaurantServiceProvider())->register($container);
    $container->instance(AccountRepository::class, new PaymentAccounts($scenario));
    $container->instance(OccupationRepository::class, new PaymentOccupations($scenario));
    $container->instance(TransactionManager::class, new PaymentTransactions($scenario));
    $container->instance(OpenOrderService::class, new PaymentPos($scenario));
    // Resolver también el adaptador real con los bindings existentes.
    return $container->get(PayAccount::class);
}

function checkPayment(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$data = ['idcaja' => 3, 'idconsecutivo' => 4, 'expectedTotal' => '100.00', 'mediosPago' => [['idmediopago' => 1, 'valor' => '100.00']]];
$scenario = new PaymentScenario();
$result = paymentUseCase($scenario)->execute(10, 2, 7, $data + ['idfactura' => 999, 'estado' => 'Guardado']);
checkPayment($scenario->events === ['begin', 'account:10', 'occupation:20', 'pos', 'close:10', 'finish:20', 'commit'], 'Orden de cobro y cierre incorrecto.');
checkPayment($scenario->arguments === [30, 2, 7, $data], 'Debe usar la factura de la cuenta y filtrar los datos del navegador.');
checkPayment($result === $scenario->result + ['accountId' => 10, 'occupationId' => 20], 'Se perdió la respuesta de POS.');
$finished = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $scenario->finishedAt, new DateTimeZone('America/Bogota'));
checkPayment($finished !== false && abs(time() - $finished->getTimestamp()) < 5, 'Fecha de finalización incorrecta.');
$passed = 1;

foreach ([[0, 2, 7], [10, 0, 7], [10, 2, 0]] as $ids) {
    $scenario = new PaymentScenario();
    try {
        paymentUseCase($scenario)->execute($ids[0], $ids[1], $ids[2], $data);
        throw new LogicException('Se aceptaron identificadores inválidos.');
    } catch (InvalidArgumentException $e) {
        checkPayment($scenario->events === [], 'Se inició una transacción con identificadores inválidos.');
    }
    $passed++;
}

foreach (['missing_account', 'closed_account', 'missing_occupation', 'closed_occupation', 'other_branch', 'missing_invoice', 'invalid_invoice'] as $case) {
    $scenario = new PaymentScenario();
    switch ($case) {
        case 'missing_account': $scenario->account = null; break;
        case 'closed_account': $scenario->account = new Account(10, 20, 30, 'General', 'cerrada'); break;
        case 'missing_occupation': $scenario->occupation = null; break;
        case 'closed_occupation': $scenario->occupation = PaymentScenario::occupation(2, 'finalizada'); break;
        case 'other_branch': $scenario->occupation = PaymentScenario::occupation(99); break;
        case 'missing_invoice': $scenario->account = new Account(10, 20, null, 'General', 'abierta'); break;
        case 'invalid_invoice': $scenario->account = new Account(10, 20, 0, 'General', 'abierta'); break;
    }
    try {
        paymentUseCase($scenario)->execute(10, 2, 7, $data);
        throw new LogicException('Se aceptó el caso ' . $case);
    } catch (InvalidArgumentException $e) {
        checkPayment(end($scenario->events) === 'rollback', 'No se solicitó rollback: ' . $case);
        checkPayment(!in_array('pos', $scenario->events, true), 'Se intentó cobrar un contexto inválido: ' . $case);
    }
    $passed++;
}

// Un fallo en POS o en cualquiera de los cierres debe impedir el commit.
$steps = ['begin', 'account:10', 'occupation:20', 'pos', 'close:10', 'finish:20', 'commit'];
foreach (['pos', 'close:10', 'finish:20', 'commit'] as $failure) {
    $scenario = new PaymentScenario();
    $scenario->failure = $failure;
    try {
        paymentUseCase($scenario)->execute(10, 2, 7, $data);
        throw new LogicException('No propagó el fallo: ' . $failure);
    } catch (RuntimeException $e) {
        checkPayment($e->getMessage() === 'Fallo simulado: ' . $failure, 'Se perdió el error original.');
        $expected = array_slice($steps, 0, array_search($failure, $steps, true) + 1);
        $expected[] = 'rollback';
        checkPayment($scenario->events === $expected, 'El flujo continuó después del fallo: ' . $failure);
    }
    $passed++;
}

echo "OK: {$passed} escenarios de cobro de cuenta (sin MySQL).\n";
