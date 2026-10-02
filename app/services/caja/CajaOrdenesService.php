<?php

namespace App\services\caja;

use App\Models\ActiveRecord;
use App\Models\caja\cierrescajas;
use App\Models\caja\factmediospago;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\emisores;
use App\Models\configuraciones\mediospago;
use App\Models\configuraciones\usuarios;
use App\Models\parametrizacion\config_local;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\Repositories\creditos\creditosRepository;
use App\services\ventasService;
use App\services\whatsAppService;
use Throwable;

/**
 * Casos de uso de órdenes administradas desde caja.
 *
 * Casos de uso planeados:
 * - consultar pedidos guardados y despachos pendientes;
 * - preparar el resumen de una orden;
 * - consultar y cambiar medios de pago;
 * - eliminar un pedido guardado;
 * - despachar una orden;
 * - cambiar la caja o el emisor de una factura.
 *
 * La migración se realiza método por método. Este servicio no conoce Router,
 * sesiones, parámetros HTTP ni serialización JSON.
 */
final class CajaOrdenesService
{
    private CajaDocumentosService $documentosService;
    private creditosRepository $creditosRepository;

    public function __construct(
        ?CajaDocumentosService $documentosService = null,
        ?creditosRepository $creditosRepository = null
    ) {
        $this->documentosService = $documentosService ?? new CajaDocumentosService();
        $this->creditosRepository = $creditosRepository ?? new creditosRepository();
    }

    /**
     * Lista las cotizaciones guardadas de la sucursal activa.
     *
     * Consumidor actual: cajacontrolador::pedidosguardados(), ruta
     * GET /admin/caja/pedidosguardados. Conserva los filtros y el orden
     * histórico esperados por la vista admin/caja/pedidosguardados.
     */
    public function listarPedidosGuardados(int $sucursalId): array{
        return facturas::whereArray(['cotizacion'=>1, 'estado'=>'guardado', 'id_sucursal'=>$sucursalId]);
    }

    /**
     * Lista las órdenes pagadas o remisiones que todavía no se han entregado.
     *
     * Consumidor actual: cajacontrolador::despachosPendientes(), ruta
     * GET /admin/caja/despachosPendientes. Conserva el contrato de objetos
     * usado directamente por la tabla de la vista.
     */
    public function listarDespachosPendientes(int $sucursalId): array{
        return facturas::despachosPendientes($sucursalId) ?? [];
    }

    /**
     * Prepara el detalle operativo completo mostrado en el resumen de orden.
     *
     * Consumidor actual: cajacontrolador::ordenresumen(), ruta
     * GET /admin/caja/ordenresumen?id={factura}. Reutiliza el detalle común de
     * CajaDocumentosService y añade los catálogos usados por los modales de la
     * vista. Retorna null si la factura no pertenece a la sucursal activa.
     */
    public function prepararResumenOrden(int $facturaId, int $sucursalId): ?array{
        $datos = $this->documentosService->obtenerDetalleVenta($facturaId, $sucursalId);
        if(!$datos)return null;

        $factura = $datos['factura'];
        if((string)$factura->tipoventa === 'Credito'){
            $credito = $this->creditosRepository->uniqueWhere(['factura_id'=>$facturaId]);
            $factura->ref_creditoid = $credito?->id ?? $factura->ref_creditoid ?? null;
        }

        $emisores = emisores::whereArray(['idsucursal'=>$sucursalId, 'estado'=>1]);

        return [
            'factura'=>$factura,
            'productos'=>$datos['productos'],
            'cliente'=>$datos['cliente'],
            'tarifa'=>$datos['tarifa'],
            'direccion'=>$datos['direccion'],
            'vendedor'=>$datos['vendedor'],
            'mediospago'=>mediospago::whereArray(['estado'=>1]),
            'cajas'=>caja::whereArray(['idsucursalid'=>$sucursalId, 'estado'=>1]),
            'consecutivos'=>consecutivos::whereArray(['id_sucursalid'=>$sucursalId, 'estado'=>1]),
            'usuarios'=>usuarios::whereArray(['idsucursal'=>$sucursalId]),
            'emisores'=>$emisores,
            'conflocal'=>config_local::getParamCaja(),
            'sucursal'=>$datos['sucursal'],
            'mediosPago'=>$datos['mediosPago']
        ];
    }

    /**
     * Obtiene las relaciones de pago de una factura de la sucursal activa.
     *
     * Consumidor actual: cajacontrolador::mediospagoXfactura(), ruta
     * GET /admin/api/mediospagoXfactura?id={factura}, llamada desde caja.ts
     * al abrir el modal de cambio de medios de pago.
     *
     * Retorna null si la factura no pertenece a la sucursal y un arreglo vacío
     * si la factura existe pero todavía no tiene pagos asociados.
     */
    public function obtenerMediosPagoFactura(int $facturaId, int $sucursalId): ?array{
        $factura = facturas::uniquewhereArray(['id'=>$facturaId, 'id_sucursal'=>$sucursalId]);
        if(!$factura)return null;

        return factmediospago::idregistros('id_factura', $facturaId);
    }

    /**
     * Sustituye los medios de pago de una factura dentro de una transacción.
     *
     * Consumidor actual: cajacontrolador::cambioMedioPago(), ruta
     * POST /admin/api/cambioMedioPago, llamada desde caja.ts. El efectivo
     * anterior y nuevo se calculan desde los registros persistidos y el cierre
     * se bloquea para impedir cambios mientras otra solicitud intenta cerrarlo.
     */
    public function cambiarMediosPagoFactura(int $facturaId, array $nuevosMediosPago, int $sucursalId): array {
        $normalizados = $this->normalizarMediosPago($nuevosMediosPago);
        if(isset($normalizados['error']))return $normalizados;
        $nuevos = $normalizados['pagos'];

        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar el cambio de medios de pago.');
            $transaccionIniciada = true;

            $factura = facturas::findForUpdate('id', $facturaId);
            if(!$factura || (int)$factura->id_sucursal !== $sucursalId){
                $db->rollback();
                return ['error'=>['Factura no encontrada.']];
            }
            if((string)$factura->estado !== 'Paga'){
                $db->rollback();
                return ['error'=>['Solo se pueden cambiar los medios de pago de una factura pagada.']];
            }

            $cierre = cierrescajas::findForUpdate('id', (int)$factura->idcierrecaja);
            if(
                !$cierre ||
                (int)$cierre->idsucursal_id !== $sucursalId ||
                (int)$cierre->estado !== 0
            ){
                $db->rollback();
                return ['error'=>['El cierre de caja de la factura ya está cerrado o no es válido.']];
            }

            $actuales = factmediospago::obtenerPorFacturaParaActualizar($facturaId);
            if($actuales === []){
                $db->rollback();
                return ['error'=>['La factura no tiene medios de pago registrados.']];
            }

            $totalAnterior = $this->sumarPagos($actuales);
            $totalNuevo = $this->sumarPagos($nuevos);
            if(abs($totalAnterior - $totalNuevo) > 0.009){
                $db->rollback();
                return ['error'=>['El total de los nuevos medios de pago debe coincidir con el valor pagado.']];
            }

            $efectivoAnterior = $this->sumarPagos($actuales, 1);
            $efectivoNuevo = $this->sumarPagos($nuevos, 1);
            $this->sincronizarPagos($actuales, $nuevos);

            $diferenciaEfectivo = $efectivoNuevo - $efectivoAnterior;
            if(abs($diferenciaEfectivo) > 0.009){
                $cierre->ventasenefectivo = (float)$cierre->ventasenefectivo + $diferenciaEfectivo;
                if(!$cierre->actualizar())throw new \RuntimeException('No fue posible actualizar el efectivo del cierre.');
            }

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el cambio de medios de pago.');
            $transaccionIniciada = false;

            return [
                'exito'=>['Cambio de medios de pago aplicados.'],
                'mediosPagoUpdate'=>$this->obtenerMediosPagoConNombre($facturaId)
            ];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['Error al cambiar los medios de pago, intenta nuevamente.']];
        }
    }

    /**
     * Realiza la baja lógica de una cotización que continúa guardada.
     *
     * Consumidor actual: cajacontrolador::eliminarPedidoGuardado(), ruta
     * POST /admin/api/eliminarPedidoGuardado, llamada desde
     * pedidosguardados.ts. La orden y su cierre se bloquean para impedir que
     * una conversión a venta ocurra simultáneamente con la eliminación.
     */
    public function eliminarPedidoGuardado(int $facturaId, int $sucursalId): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar la eliminación.');
            $transaccionIniciada = true;

            $pedido = facturas::findForUpdate('id', $facturaId);
            if(!$pedido || (int)$pedido->id_sucursal !== $sucursalId){
                $db->rollback();
                return ['error'=>['Cotizacion no encontrado']];
            }

            if(
                (int)$pedido->cotizacion !== 1 ||
                strcasecmp((string)$pedido->estado, 'Guardado') !== 0 ||
                (int)$pedido->cambioaventa !== 0
            ){
                $db->rollback();
                return ['error'=>['La cotizacion ya no está disponible para eliminar.']];
            }

            $cierre = cierrescajas::findForUpdate('id', (int)$pedido->idcierrecaja);
            if(
                !$cierre ||
                (int)$cierre->idsucursal_id !== $sucursalId
            ){
                $db->rollback();
                return ['error'=>['El cierre de caja de la cotizacion no es válido.']];
            }

            $pedido->estado = 'Eliminada';
            $pedido->fechaanulacion = date('Y-m-d H:i:s');

            if(!$pedido->actualizar())throw new \RuntimeException('No fue posible actualizar la cotización.');
            // Un cierre histórico no debe reescribirse. El contador solo se
            // ajusta si la cotización se elimina durante el período abierto.
            if((int)$cierre->estado === 0){
                $cierre->totalcotizaciones = max(0, (int)$cierre->totalcotizaciones - 1);
                if(!$cierre->actualizar())throw new \RuntimeException('No fue posible actualizar el cierre.');
            }
            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar la eliminación.');
            $transaccionIniciada = false;

            return ['exito'=>['Cotizacion eliminada correctamente']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['error en el proceso de eliminacion']];
        }
    }

    /**
     * Marca una orden como entregada y descuenta su inventario una sola vez.
     *
     * Consumidor actual: cajacontrolador::despacharOrden(), ruta temporal
     * GET /admin/api/caja/despacharOrden?id={factura}, llamada desde
     * ordenresumen.ts. La factura se bloquea antes de comprobar `entregado`
     * para impedir que dos solicitudes simultáneas descuenten el mismo
     * inventario. Factura, existencias y movimientos comparten la transacción.
     */
    public function despacharOrden(int $facturaId, int $sucursalId): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar el despacho.');
            $transaccionIniciada = true;

            $factura = facturas::findForUpdate('id', $facturaId);
            if(!$factura || (int)$factura->id_sucursal !== $sucursalId){
                $db->rollback();
                return ['error'=>['Orden no encontrada.']];
            }

            if(
                (int)$factura->entregado !== 0 ||
                !in_array((string)$factura->estado, ['Paga', 'Remision'], true)
            ){
                $db->rollback();
                return ['error'=>['Error, verificar si ya se despacho como domicilio']];
            }

            $productos = ventas::idregistros('idfactura', $facturaId);
            $inventario = ventasService::prepararInventarioPersistido($productos, $sucursalId);
            $configuracion = config_local::getParamCaja();
            $permitirSinStock = (int)($configuracion['permitir_venta_de_productos_sin_stock']->valor_final ?? 0);

            if($permitirSinStock === 0){
                $erroresStock = ventasService::validarDisponibilidadInventario($inventario, $sucursalId);
                if($erroresStock){
                    throw new \RuntimeException(implode(' | ', $erroresStock));
                }
            }

            $itemsStockMinimo = [];
            if(!ventasService::descontarInventarioXVenta($inventario, $sucursalId, 'venta', 'descuento de unidades por despacho de venta', false, $itemsStockMinimo))
                throw new \RuntimeException('No fue posible actualizar el inventario de la orden.');

            $factura->entregado = 1;
            $factura->fechaentrega = (new \DateTimeImmutable('now', new \DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s');
            if(!$factura->actualizar())throw new \RuntimeException('No fue posible marcar la orden como entregada.');

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el despacho.');
            $transaccionIniciada = false;
            //enviar notificacion de despacho a whatsapp
            if(!empty($itemsStockMinimo) && ($configuracion['notificacion_por_whatsApp_stock_bajo']->valor_final ?? 0) == 1){
                try {
                    (new whatsAppService())->productoBajoStock($itemsStockMinimo);
                } catch (\Throwable $th) {
                     error_log("No fue posible notificar el stock minimo del despacho ".$facturaId.": ".$th->getMessage());
                }
            }
            return ['exito'=>['Orden despachada.']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['Error al procesar solicitud >>'.$error->getMessage()]];
        }
    }

    /** Valida y normaliza el arreglo recibido antes de abrir la transacción. */
    private function normalizarMediosPago(array $pagos): array{
        if($pagos === [])return ['error'=>['Debe registrar al menos un medio de pago.']];

        $resultado = [];
        $ids = [];
        foreach($pagos as $pago){
            $datos = is_object($pago) ? get_object_vars($pago) : $pago;
            if(!is_array($datos))return ['error'=>['Los medios de pago enviados no son válidos.']];

            $medioId = filter_var($datos['idmediopago'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $valor = $datos['valor'] ?? null;
            if($medioId === false || !is_numeric($valor) || (float)$valor <= 0)
                return ['error'=>['Los medios de pago enviados no son válidos.']];
            
            if(isset($ids[$medioId]))return ['error'=>['No se puede repetir un medio de pago.']];

            $medio = mediospago::find('id', (int)$medioId);
            if(!$medio)return ['error'=>['Uno de los medios de pago no existe.']];

            $ids[$medioId] = true;
            $resultado[] = (object)[
                'idmediopago'=>(int)$medioId,
                'valor'=>(float)$valor
            ];
        }
        return ['pagos'=>$resultado];
    }

    /** Suma todos los pagos o solamente los asociados a un medio específico. */
    private function sumarPagos(array $pagos, ?int $medioId = null): float{
        $total = 0.0;
        foreach($pagos as $pago){
            if($medioId !== null && (int)$pago->idmediopago !== $medioId)continue;
            $total += (float)$pago->valor;
        }
        return $total;
    }

    /**
     * Actualiza las filas comunes, crea las adicionales y elimina las
     * sobrantes. Se ejecuta únicamente con factura, cierre y pagos bloqueados.
     */
    private function sincronizarPagos(array $actuales, array $nuevos): void{
        $cantidadComun = min(count($actuales), count($nuevos));
        $actualizar = [];
        for($indice = 0; $indice < $cantidadComun; $indice++){
            $actuales[$indice]->idmediopago = $nuevos[$indice]->idmediopago;
            $actuales[$indice]->valor = $nuevos[$indice]->valor;
            $actualizar[] = $actuales[$indice];
        }
        if($actualizar && !factmediospago::updatemultiregobj($actualizar, ['idmediopago', 'valor']))
            throw new \RuntimeException('No fue posible actualizar los pagos existentes.');

        if(count($nuevos) > count($actuales)){
            $referencia = $actuales[0];
            $crear = [];
            for($indice = count($actuales); $indice < count($nuevos); $indice++){
                $crear[] = [
                    'cierrecajaid'=>$referencia->cierrecajaid ?: 'NULL',
                    'id_factura'=>$referencia->id_factura,
                    'idcuota'=>$referencia->idcuota ?: 'NULL',
                    'idmediopago'=>$nuevos[$indice]->idmediopago,
                    'valor'=>$nuevos[$indice]->valor
                ];
            }
            $resultado = (new factmediospago())->crear_varios_reg($crear);
            if(!($resultado[0] ?? false))throw new \RuntimeException('No fue posible crear los nuevos pagos.');
        }

        if(count($actuales) > count($nuevos)){
            $eliminar = [];
            for($indice = count($nuevos); $indice < count($actuales); $indice++)
                $eliminar[] = (int)$actuales[$indice]->id;
            if(!factmediospago::eliminar_idregistros('id', $eliminar))
                throw new \RuntimeException('No fue posible eliminar los pagos sobrantes.');
        }
    }

    /** Construye la misma respuesta enriquecida que consume caja.ts. */
    private function obtenerMediosPagoConNombre(int $facturaId): array{
        return ActiveRecord::camposJoinObj('SELECT * FROM factmediospago '.'JOIN mediospago ON factmediospago.idmediopago = mediospago.id '."WHERE id_factura = {$facturaId};") ?? [];
    }
}
