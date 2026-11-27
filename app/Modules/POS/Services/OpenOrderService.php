<?php

namespace App\Modules\POS\Services;

use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\services\facturacionService;
use App\services\caja\CajaDocumentosService;
use RuntimeException;
use InvalidArgumentException;

class OpenOrderService{

    public function create(int $branchId, ?int $customerId, int $sellerId): int{
        if($branchId <= 0 || $sellerId <= 0 || ($customerId !== null && $customerId <= 0))
            throw new InvalidArgumentException('Los datos de la orden abierta no son válidos.');
        return facturas::crearOrdenAbierta($branchId, $customerId, $sellerId);
    }
    

    //Crea la factura Abierta junto con su primer lote.Requiere una transacción abierta por el llamador.
    public function procesarNuevaAbierta(int $branchId, ?int $customerId, int $sellerId, array $items): array{
        return (new OpenInvoiceService())->procesarNuevaAbierta($branchId, $customerId, $sellerId, $items);
    }
    

    /**
     * @param array<int, array{productId: int, quantity: string}> $items
     * @return array{lineIds: array<int, int>, totales: array}
     */
    public function addSimpleProducts(int $orderId, array $items, int $branchId): array{
        return (new OpenInvoiceService())->addSimpleProducts($orderId, $items, $branchId);
    }


    public function updateCustomer(int $orderId, int $branchId, ?int $customerId): void{
        (new OpenInvoiceService())->updateCustomer($orderId, $branchId, $customerId);
    }

    // La transacción la controla Restaurant; POS realiza la anulación y el recálculo.
    public function cancelLine(int $orderId, int $lineId, int $branchId, int $userId, string $reason, string $quantity): array{
        return (new OpenInvoiceService())->cancelLine($orderId, $lineId, $branchId, $userId, $reason, $quantity);
    }

    //Este método reúne la cabecera y sus productos sin modificar datos. Una orden abierta sin productos devolverá consumos: [] y los totales iniciales de su cabecera.
    public function getSummary(int $orderId, int $branchId): array{
        if ($orderId <= 0 || $branchId <= 0)
            throw new InvalidArgumentException('La orden o la sucursal no es válida.');
        $order = facturas::buscarResumenOrdenAbierta($orderId, $branchId);
        if ($order === null)
            throw new InvalidArgumentException('La orden no está abierta en esta sucursal.');

        return ['orden' => $order, 'consumos' => ventas::listarConsumosOrden($orderId, $branchId)];
    }

    public function applyLineDiscount(int $orderId, int $lineId, int $branchId, string $type, string $value, string $password): array{
        return (new OpenInvoiceService())->applyLineDiscount($orderId, $lineId, $branchId, $type, $value, $password);
    }

    public function setLineCourtesy(
        int $orderId, int $lineId, int $branchId, bool $active, string $password): array{
        return (new OpenInvoiceService())->setLineCourtesy($orderId, $lineId, $branchId, $active, $password);
    }
    

    public function pay(int $orderId, int $branchId, int $userId, array $data): array{
        // Restaurant ya inició la transacción. Este método no hace commit ni rollback.
        $result = (new facturacionService())->facturarOrdenAbierta($orderId, $branchId, $userId, $data);
        // La factura ya está en estado Paga dentro de la transacción. Consultar sus productos y medios de pago guardados.
        $dataInvoice = (new CajaDocumentosService())->prepararInvoiceParaImpresion($orderId, $branchId);
        if($dataInvoice === null)throw new RuntimeException('No fue posible preparar los datos de la factura.');
        // Sustituir los datos incompletos por el comprobante completo. Conservar las demás propiedades de la respuesta.
        $result['dataInvoice'] = $dataInvoice;
        return $result;
    }
}
