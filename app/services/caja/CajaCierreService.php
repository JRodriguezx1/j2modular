<?php

namespace App\services\caja;

use App\Models\caja\arqueoscajas;
use App\Models\caja\cierrescajas;
use App\Models\caja\declaracionesdineros;
use App\Models\caja\ingresoscajas;
use App\Models\configuraciones\caja;
use App\Models\ventas\facturas;
use App\services\whatsAppService;
use Throwable;

/**
 * Casos de uso que modifican el cierre de caja.
 *
 * No conoce rutas, sesiones, $_POST ni serialización JSON. El controlador le
 * entrega datos ya obtenidos del contexto HTTP y conserva esa responsabilidad.
 */
final class CajaCierreService
{
    /**
     * Crea, actualiza o elimina la declaración de un medio de pago.
     *
     * Consumidor actual: cajacontrolador::declaracionDinero(), llamado por
     * src/ts/caja/cerrarcaja.ts mediante POST /admin/api/declaracionDinero.
     * Un valor cero elimina la declaración existente; si aún no existía se
     * conserva el comportamiento anterior y se registra en cero.
     */
    public function registrarDeclaracion(array $datos, int $sucursalId): array{
        $cierreId = $this->enteroPositivo($datos['idcierrecaja'] ?? null);
        $medioPagoId = $this->enteroPositivo($datos['id_mediopago'] ?? null);
        $nombreMedioPago = trim((string)($datos['nombremediopago'] ?? ''));
        $valorDeclarado = $datos['valordeclarado'] ?? null;

        $errores = [];
        if($cierreId === 0)$errores[] = 'Error con el id cierre de caja';
        if($medioPagoId === 0)$errores[] = 'Error al declarar el medio de pago';
        if($nombreMedioPago === '')$errores[] = 'Error con el nombre del medio de pago declarado';
        if(!is_numeric($valorDeclarado))$errores[] = 'Valor declarado no especificado';
        if($errores)return ['error'=>$errores];

        $db = cierrescajas::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar la declaración.');
            $transaccionIniciada = true;

            $cierre = $this->obtenerCierreAbiertoBloqueado($cierreId, $sucursalId);
            if(!$cierre){
                $db->rollback();
                return ['error'=>['Error!, ingresa nuevamente al modulo de caja para validar que la caja este ya cerrada.']];
            }

            $declaracion = new declaracionesdineros(['id_mediopago'=>$medioPagoId,'idcierrecajaid'=>$cierreId,'nombremediopago'=>$nombreMedioPago,'valordeclarado'=>$valorDeclarado]);
            $existente = declaracionesdineros::uniquewhereArray(['id_mediopago'=>$medioPagoId,'idcierrecajaid'=>$cierreId]);

            if($existente && (float)$valorDeclarado === 0.0){
                $existente->eliminar_registro();
            }elseif($existente){
                $existente->nombremediopago = $nombreMedioPago;
                $existente->valordeclarado = $valorDeclarado;
                $existente->actualizar();
            }else{
                $declaracion->crear_guardar();
            }

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar la declaración.');
            return ['exito'=>['1']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['No fue posible guardar la declaración de dinero.']];
        }
    }

    /**
     * Crea o reemplaza las denominaciones contadas en el arqueo.
     *
     * Consumidor actual: cajacontrolador::arqueocaja(), llamado por
     * src/ts/caja/cerrarcaja.ts mediante POST /admin/api/arqueocaja.
     * Al actualizar copia explícitamente el nuevo formulario al registro; el
     * controlador anterior omitía este paso y dejaba los valores anteriores.
     */
    public function registrarArqueo(array $datos, int $sucursalId): array{
        $cierreId = $this->enteroPositivo($datos['idcierrecaja'] ?? null);
        if($cierreId === 0)return ['error'=>['El cierre de caja no es válido.']];

        $db = cierrescajas::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar el arqueo.');
            $transaccionIniciada = true;

            $cierre = $this->obtenerCierreAbiertoBloqueado($cierreId, $sucursalId);
            if(!$cierre){
                $db->rollback();
                return ['error'=>['Error!, ingresa nuevamente al modulo de caja para validar que la caja este ya cerrada.']];
            }

            $datosArqueo = $this->normalizarDatosArqueo($datos);
            $datosArqueo['id_cierrecajaid'] = $cierreId;
            $arqueo = new arqueoscajas($datosArqueo);
            $existente = arqueoscajas::uniquewhereArray(['id_cierrecajaid'=>$cierreId]);

            if($existente){
                $existente->compara_objetobd_post($datosArqueo);
                $existente->actualizar();
            }else{
                $arqueo->crear_guardar();
            }

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el arqueo.');
            return ['exito'=>['Arqueo de caja aplicado']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['Error intenta nuevamente']];
        }
    }

    /**
     * Cierra el período actual y abre el siguiente de forma atómica.
     *
     * Consumidor actual: cajacontrolador::cierrecajaconfirmado(), llamado por
     * src/ts/caja/cerrarcaja.ts mediante POST
     * /admin/api/cierrecajaconfirmado. Valida sucursal, estado y órdenes
     * pendientes; calcula totales, crea el nuevo período y registra la base
     * automática antes de confirmar una única transacción.
     */
    public function confirmarCierre(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario, array $configuracion): array {
        $cierreId = $this->enteroPositivo($datos['idcierrecaja'] ?? null);
        if($cierreId === 0)return ['error'=>['El cierre de caja no es válido.']];

        $db = cierrescajas::getDB();
        $transaccionIniciada = false;
        $cierreCerradoId = 0;
        $fechaCierre = date('Y-m-d H:i:s');

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar el cierre de caja.');
            $transaccionIniciada = true;

            $cierre = $this->obtenerCierreAbiertoBloqueado($cierreId, $sucursalId);
            if(!$cierre){
                $db->rollback();
                return ['error'=>['Error ingresa nuevamente al cierre de caja.']];
            }

            if(!$this->permiteOrdenesPendientes($configuracion) && $this->tieneOrdenesPendientes($cierreId)){
                $db->rollback();
                return ['error'=>['No se puede hacer cierre de caja con ventas pendientes.']];
            }

            $cajaActual = caja::uniquewhereArray([
                'id'=>(int)$cierre->idcaja,
                'idsucursalid'=>$sucursalId
            ]);
            if(!$cajaActual)throw new \RuntimeException('La caja del cierre no pertenece a la sucursal.');

            $cierre->id_usuario = $usuarioId;
            $cierre->nombreusuario = $nombreUsuario;
            $cierre->fechacierre = $fechaCierre;
            $cierre->dineroencaja = (float)$cierre->basecaja + (float)$cierre->ventasenefectivo - (float)$cierre->gastoscaja;
            $cierre->realencaja = (float)$cierre->dineroencaja - (float)$cierre->domicilios;
            $cierre->realventas = (float)$cierre->ingresoventas - (float)$cierre->totaldescuentos;
            $cierre->estado = 1;

            $baseAutomatica = (float)($configuracion['base_de_caja_automatico_constante']->valor_final ?? 0);
            $esCajaPrincipal = (int)$cierre->idcaja === 1;
            $nuevoCierre = new cierrescajas(['idsucursal_id'=>$sucursalId, 'idcaja'=>(int)$cierre->idcaja, 'nombrecaja'=>$cajaActual->nombre, 'fechacierre'=>$fechaCierre, 'basecaja'=>$esCajaPrincipal ? $baseAutomatica : 0]);

            $nuevoResultado = $nuevoCierre->crear_guardar();
            $nuevoCierreId = (int)($nuevoResultado[1] ?? 0);
            if($nuevoCierreId === 0)throw new \RuntimeException('No fue posible abrir el siguiente cierre.');
            $cierre->actualizar();

            if($esCajaPrincipal && $baseAutomatica > 0){
                $ingreso = new ingresoscajas(['idusuario'=>$usuarioId, 'id_caja'=>(int)$cierre->idcaja, 'id_cierrecaja'=>$nuevoCierreId, 'operacion'=>'ingreso', 'valor'=>$baseAutomatica]);
                // El constructor heredado toma la sucursal global; aquí se
                // fuerza el contexto recibido por el caso de uso.
                $ingreso->idsucursal_idfk = $sucursalId;
                $ingreso->crear_guardar();
            }

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el cierre de caja.');
            $cierreCerradoId = (int)$cierre->id;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>['Error ingresa nuevamente al cierre de caja.']];
        }

        $this->notificarCierrePorWhatsApp($cierreCerradoId, $configuracion);

        return [
            'exito'=>["Cierre de caja realizado correctamente $fechaCierre"],
            'ultimocierre'=>[$cierreCerradoId]
        ];
    }

    /**
     * Bloquea el cierre durante un comando y verifica que siga abierto y que
     * pertenezca a la sucursal activa.
     */
    private function obtenerCierreAbiertoBloqueado(int $cierreId, int $sucursalId): ?cierrescajas{
        $cierre = cierrescajas::findForUpdate('id', $cierreId);
        if(!$cierre || (int)$cierre->idsucursal_id !== $sucursalId || (int)$cierre->estado !== 0)return null;
        return $cierre;
    }

    /** Indica si la configuración permite cerrar aun con órdenes sin pagar. */
    private function permiteOrdenesPendientes(array $configuracion): bool{
        return (int)($configuracion['permitir_cierre_de_caja_con_ordenes_sin_pagar']->valor_final ?? 0) === 1;
    }

    /** Busca cotizaciones del cierre que todavía no se convirtieron en venta. */
    private function tieneOrdenesPendientes(int $cierreId): bool{
        foreach(facturas::idregistros('idcierrecaja', $cierreId) as $factura)
            if((int)$factura->cotizacion === 1 && (int)$factura->cambioaventa === 0)return true;
        return false;
    }

    /**
     * Conserva únicamente las denominaciones persistibles del formulario.
     * Esto evita que campos extra puedan cambiar el id o la relación del arqueo.
     */
    private function normalizarDatosArqueo(array $datos): array{
        $campos = [
            'cienmil', 'cincuentamil', 'veintemil', 'diezmil', 'cincomil',
            'dosmil', 'mil', 'quinientos', 'docientos', 'cien', 'cincuenta',
            'dato1', 'dato2'
        ];
        $normalizados = [];

        foreach($campos as $campo)
            $normalizados[$campo] = $datos[$campo] ?? 0;
        return $normalizados;
    }

    /**
     * Envía la notificación después del commit para que una falla externa no
     * revierta ni deje incierto un cierre que ya fue guardado correctamente.
     */
    private function notificarCierrePorWhatsApp(int $cierreId, array $configuracion): void{
        if((int)($configuracion['notificacion_por_whatsApp_cierre_caja']->valor_final ?? 0) !== 1)return;

        try{
            (new whatsAppService($configuracion))->sendtextDetalleCierreCaja($cierreId);
        }catch(Throwable $error){
            error_log("No fue posible notificar por WhatsApp el cierre de caja {$cierreId}: {$error->getMessage()}");
        }
    }

    /** Convierte un identificador externo en entero positivo o devuelve cero. */
    private function enteroPositivo(mixed $valor): int{
        $entero = filter_var($valor, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        return $entero === false ? 0 : (int)$entero;
    }
}
