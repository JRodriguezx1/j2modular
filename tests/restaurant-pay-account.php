<?php

declare(strict_types=1);

// Prueba aislada: no conecta a MySQL ni ejecuta cobros reales.
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Database\TransactionManager;
use App\Modules\POS\Services\OpenOrderService;
use App\Modules\Restaurant\Application\UseCases\PayAccount;
use App\Modules\Restaurant\Application\UseCases\ListTables;
use App\Modules\Restaurant\Domain\Entities\Account;
use App\Modules\Restaurant\Domain\Entities\Occupation;
use App\Modules\Restaurant\Domain\Entities\RestaurantTable;
use App\Modules\Restaurant\Domain\Entities\Reservation;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlAccountRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlOccupationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlTableRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlReservationRepository;
use App\Modules\Restaurant\Providers\RestaurantServiceProvider;

class PaymentScenario {
    public array $events = [];
    public array $arguments = [];
    public ?string $failure = null;
    public ?Account $account;
    public ?Occupation $occupation;
    public string $finishedAt = '';
    public array $tables;
    public array $currentOccupations = [];
    public array $reservations = [];
    public array $tableArguments = [];
    public array $occupationArguments = [];
    public array $reservationArguments = [];
    public array $result = ['exito' => ['Pago procesado'], 'idfactura' => 30, 'dataInvoice' => ['numero' => 'POS1']];

    public function __construct() {
        $this->account = new Account(10, 20, 30, 'General', 'abierta');
        $this->occupation = self::occupation();
        // IDs distintos para detectar una confusión entre mesa y recurso.
        $this->tables = [self::table()];
        $this->currentOccupations = [105 => $this->occupation];
    }

    public static function occupation(int $branchId = 2, string $status = 'en_uso'): Occupation {
        return new Occupation(20, $branchId, null, null, 'restaurant', '2026-11-21 12:00:00', null, null, $status, null);
    }

    public static function table(int $resourceId = 105, string $status = 'disponible', bool $active = true): RestaurantTable {
        return new RestaurantTable(5, $resourceId, 'Mesa de prueba', 4, $active, $status);
    }

    public static function reservation(): Reservation {
        return new Reservation(40, 1, 4, '2026-11-21 13:00:00', '2026-11-21 15:00:00', 'confirmada', null, 'Cliente de prueba');
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
        // Simular que las lecturas posteriores ya no encuentran esta atención.
        foreach ($this->scenario->currentOccupations as $resourceId => $occupation) {
            if ($occupation->getId() === $occupationId) unset($this->scenario->currentOccupations[$resourceId]);
        }
    }
    public function findCurrentByResourceIds(array $resourceIds): array {
        $this->scenario->occupationArguments[] = $resourceIds;
        $this->scenario->record('current_occupations');
        return array_intersect_key($this->scenario->currentOccupations, array_flip($resourceIds));
    }
}

class PaymentTables extends MySqlTableRepository {
    public function __construct(private PaymentScenario $scenario) {}
    public function findByOccupationId(int $occupationId, int $branchId): array {
        $this->scenario->tableArguments[] = [$occupationId, $branchId];
        $this->scenario->record('tables');
        return $this->scenario->tables;
    }
    public function findAll(): array {
        // Si PayAccount usa este método, la secuencia esperada fallará.
        $this->scenario->record('all_tables');
        return $this->scenario->tables;
    }
}

class PaymentReservations extends MySqlReservationRepository {
    public function __construct(private PaymentScenario $scenario) {}
    public function findCurrentByResourceIds(array $resourceIds, int $minutesBefore = 30): array {
        $this->scenario->reservationArguments[] = [$resourceIds, $minutesBefore];
        $this->scenario->record('reservations');
        return array_intersect_key($this->scenario->reservations, array_flip($resourceIds));
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

function paymentContainer(PaymentScenario $scenario): Container {
    $container = new Container();
    (new RestaurantServiceProvider())->register($container);
    $container->instance(AccountRepository::class, new PaymentAccounts($scenario));
    $container->instance(OccupationRepository::class, new PaymentOccupations($scenario));
    $container->instance(TransactionManager::class, new PaymentTransactions($scenario));
    $container->instance(OpenOrderService::class, new PaymentPos($scenario));
    $container->instance(RestaurantTableRepository::class, new PaymentTables($scenario));
    $container->instance(ReservationRepository::class, new PaymentReservations($scenario));
    // TableStateService y el adaptador POS se resuelven con sus clases reales.
    return $container;
}

function paymentUseCase(PaymentScenario $scenario): PayAccount {
    return paymentContainer($scenario)->get(PayAccount::class);
}

function checkPayment(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$data = ['idcaja' => 3, 'idconsecutivo' => 4, 'expectedTotal' => '100.00', 'mediosPago' => [['idmediopago' => 1, 'valor' => '100.00']]];
$scenario = new PaymentScenario();
$result = paymentUseCase($scenario)->execute(10, 2, 7, $data + ['idfactura' => 999, 'estado' => 'Guardado']);
checkPayment($scenario->events === ['begin', 'account:10', 'occupation:20', 'pos', 'close:10', 'finish:20', 'tables', 'current_occupations', 'reservations', 'commit'], 'Los estados deben calcularse después de finalizar y antes del commit.');
checkPayment($scenario->arguments === [30, 2, 7, $data], 'Debe usar la factura de la cuenta y filtrar los datos del navegador.');
$availableTable = ['resourceId' => 105, 'active' => true, 'operationalStatus' => 'disponible', 'occupationId' => null, 'occupiedSince' => null, 'reservationId' => null, 'reservationStart' => null];
checkPayment($result === $scenario->result + ['tables' => [$availableTable], 'accountId' => 10, 'occupationId' => 20], 'Respuesta de cobro incorrecta.');
checkPayment($scenario->tableArguments === [[20, 2]], 'Debe consultar la ocupación y sucursal del cobro.');
checkPayment($scenario->occupationArguments === [[105]], 'Debe consultar únicamente los recursos vinculados.');
checkPayment($scenario->reservationArguments === [[[105], 30]], 'Debe conservar la anticipación de 30 minutos.');
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
$steps = ['begin', 'account:10', 'occupation:20', 'pos', 'close:10', 'finish:20', 'tables', 'current_occupations', 'reservations', 'commit'];
foreach (['pos', 'close:10', 'finish:20', 'tables', 'current_occupations', 'reservations', 'commit'] as $failure) {
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

// Prioridad compartida: recurso, ocupación y por último reserva.
foreach (['reservada', 'ocupada', 'limpieza', 'mantenimiento', 'fuera_servicio', 'inactiva'] as $case) {
    $scenario = new PaymentScenario();
    $reservation = PaymentScenario::reservation();
    $scenario->reservations = [105 => $reservation];
    $expected = $availableTable;
    $expected['reservationId'] = 40;
    $expected['reservationStart'] = $reservation->getStartDate();

    if ($case === 'reservada') {
        $expected['operationalStatus'] = 'reservada';
    } elseif ($case === 'inactiva') {
        // active es independiente del estado operativo; se conserva para la UI.
        $scenario->tables = [PaymentScenario::table(active: false)];
        $expected['active'] = false;
        $expected['operationalStatus'] = 'reservada';
    } else {
        // Otra ocupación permanece vigente; finish solo finaliza la 20.
        $other = new Occupation(21, 2, null, null, 'restaurant', '2026-11-21 12:30:00', null, null, 'en_uso', null);
        $scenario->currentOccupations = [105 => $other];
        $expected['occupationId'] = 21;
        $expected['occupiedSince'] = $other->getStartDate();
        $expected['operationalStatus'] = $case;
        if ($case !== 'ocupada') $scenario->tables = [PaymentScenario::table(status: $case)];
    }

    $result = paymentUseCase($scenario)->execute(10, 2, 7, $data);
    checkPayment($result['tables'] === [$expected], 'Prioridad o datos incorrectos: ' . $case);
    $passed++;
}

// Varias mesas de una atención, con estados distintos y recursos ajenos excluidos.
$scenario = new PaymentScenario();
$scenario->tables[] = new RestaurantTable(6, 106, 'Mesa adicional', 2, true, 'disponible');
$scenario->reservations = [106 => PaymentScenario::reservation(), 999 => PaymentScenario::reservation()];
$result = paymentUseCase($scenario)->execute(10, 2, 7, $data);
checkPayment(array_column($result['tables'], 'resourceId') === [105, 106], 'Se incluyeron mesas ajenas o se perdieron mesas vinculadas.');
checkPayment(array_column($result['tables'], 'operationalStatus') === ['disponible', 'reservada'], 'Se mezclaron estados entre mesas.');
checkPayment($scenario->occupationArguments === [[105, 106]], 'Consulta de ocupaciones fuera del conjunto solicitado.');
checkPayment($scenario->reservationArguments === [[[105, 106], 30]], 'Consulta de reservas fuera del conjunto solicitado.');
$passed++;

// Sin mesas, el servicio evita consultar ocupaciones y reservas.
$scenario = new PaymentScenario();
$scenario->tables = [];
$result = paymentUseCase($scenario)->execute(10, 2, 7, $data);
checkPayment($result['tables'] === [], 'La respuesta sin mesas debe ser una lista vacía.');
checkPayment($scenario->occupationArguments === [] && $scenario->reservationArguments === [], 'Se hicieron consultas con una lista vacía.');
checkPayment(end($scenario->events) === 'commit', 'No confirmó el cobro sin mesas.');
$passed++;

// ListTables conserva el contrato que consume RestaurantController.
$scenario = new PaymentScenario();
$reservation = PaymentScenario::reservation();
$scenario->reservations = [105 => $reservation];
$result = paymentContainer($scenario)->get(ListTables::class)->execute();
checkPayment($result === [[
    'table' => $scenario->tables[0],
    'occupation' => $scenario->occupation,
    'reservation' => $reservation,
    'operationalStatus' => 'ocupada',
]], 'ListTables cambió su contrato o no priorizó la ocupación.');
checkPayment($scenario->events === ['all_tables', 'current_occupations', 'reservations'], 'ListTables no utiliza el cálculo compartido.');
$passed++;

$scenario = new PaymentScenario();
$scenario->tables = [];
checkPayment(paymentContainer($scenario)->get(ListTables::class)->execute() === [], 'El listado vacío debe conservar su formato.');
checkPayment($scenario->events === ['all_tables'], 'El listado vacío no debe consultar estados.');
$passed++;

echo "OK: {$passed} escenarios de cobro y estado de mesas (servicios reales, sin MySQL).\n";
