<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Core\Database\TransactionManager;
use App\Modules\Restaurant\Application\Ports\OpenOrderPort;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use InvalidArgumentException;
use Throwable;

class CancelAccountLine{

    public function __construct(
        private AccountRepository $accountRepository,
        private OccupationRepository $occupationRepository,
        private OpenOrderPort $openOrderPort,
        private TransactionManager $transactionManager
    ){}

    /**
     * Anula toda la cantidad de una línea o solamente una parte.
     * La sucursal y el usuario deben provenir de la sesión del controlador.
     *
     * @return array{accountId: int, occupationId: int, orderId: int, lineId: int, alreadyCancelled: bool, totales: array}
     */
    public function execute(int $accountId, int $lineId, string $quantity, string $reason, int $branchId, int $userId): array{
        // Rechazar datos inválidos antes de abrir la transacción.
        if($accountId <= 0 || $lineId <= 0 || $branchId <= 0 || $userId <= 0)
            throw new InvalidArgumentException('La cuenta, la línea, la sucursal o el usuario no es válido.');

        // Conservar la cantidad como texto decimal, sin convertirla a float.
        if(!preg_match('/\A(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?\z/', $quantity) || bccomp($quantity, '0', 2) <= 0)
            throw new InvalidArgumentException('La cantidad a anular no es válida.');

        $reason = trim($reason);
        if($reason === '' || mb_strlen($reason, 'UTF-8') > 255)
            throw new InvalidArgumentException('Indica un motivo de entre 1 y 255 caracteres.');

        $this->transactionManager->begin();
        try {
            // Mismo orden de bloqueo que al agregar productos o cambiar el cliente.
            $account = $this->accountRepository->findByIdForUpdate($accountId);
            if($account === null)
                throw new InvalidArgumentException('La cuenta no existe.');

            if($account->getStatus() !== 'abierta')
                throw new InvalidArgumentException('La cuenta no está abierta.');

            $occupation = $this->occupationRepository->findByIdForUpdate($account->getOccupationId());
            if($occupation === null || $occupation->getStatus() !== 'en_uso')
                throw new InvalidArgumentException('La atención de esta cuenta no está en uso.');

            if($occupation->getSucursalId() !== $branchId)
                throw new InvalidArgumentException('La cuenta no pertenece a la sucursal actual.');

            // Obtener la factura de la cuenta, nunca de un dato enviado por el navegador.
            $orderId = $account->getInvoiceId();
            if($orderId === null || $orderId <= 0)
                throw new InvalidArgumentException('La cuenta no tiene un pedido guardado para anular.');
            // POS bloquea la factura y la línea, valida las unidades disponibles,
            // guarda la anulación y recalcula los totales en esta misma transacción.
            $result = $this->openOrderPort->cancelLine($orderId, $lineId, $branchId, $userId, $reason, $quantity);
            $this->transactionManager->commit();

            return [
                'accountId' => $accountId,
                'occupationId' => $occupation->getId(),
                'orderId' => $orderId,
                'lineId' => $result['lineId'],
                'alreadyCancelled' => $result['alreadyCancelled'],
                'totales' => $result['totales'],
            ];
        }catch(Throwable $e){
            // Revertir juntas la modificación de la original, la fila anulada y los totales.
            $this->transactionManager->rollback();
            throw $e;
        }
    }

}