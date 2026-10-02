<?php

namespace App\services\caja;

use App\Models\caja\cierrescajas;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\ventas\facturas;
use DateTimeImmutable;
use stdClass;

/**
 * Consultas y consolidaciones utilizadas por los reportes Z de caja.
 *
 * No conoce Router, sesiones, parámetros GET ni vistas. El controlador entrega
 * el selector del reporte y conserva la responsabilidad HTTP.
 */
final class CajaReportesService
{
    /**
     * Prepara el índice del reporte Z.
     *
     * Consumidor actual: cajacontrolador::zetadiario(), ruta
     * GET /admin/caja/zetadiario. Lista primero los cierres más recientes y
     * conserva -1 como selector convencional del consolidado de cajas abiertas.
     */
    public function obtenerIndiceZ(int $sucursalId): array{
        return [
            'ultimoscierres'=>cierrescajas::whereArray(['estado'=>1, 'idsucursal_id'=>$sucursalId], 'DESC'),
            'idultimocierreabierto'=>-1
        ];
    }

    /**
     * Prepara la pantalla de detalle según el selector recibido.
     *
     * Consumidor actual: cajacontrolador::fechazetadiario(), ruta
     * GET /admin/caja/fechazetadiario?id={selector}. Los modos son:
     * - -1: consolida todos los cierres abiertos de la sucursal;
     * -  0: entrega la pantalla vacía para consultar un rango por JavaScript;
     * - >0: carga un cierre histórico concreto de la sucursal.
     */
    public function obtenerDetalleZ(int $selector, int $sucursalId): array{
        $cajas = caja::whereArray(['idsucursalid'=>$sucursalId, 'estado'=>1]);
        $datos = [
            'cajas'=>$cajas,
            'consecutivos'=>consecutivos::whereArray(['id_sucursalid'=>$sucursalId, 'estado'=>1]),
            'cierreselected'=>$this->crearResumenVacio(),
            'cajaselected'=>'',
            'discriminarmediospagos'=>[],
            'discriminarimpuestos'=>[]
        ];

        if($selector === -1)return array_replace($datos, $this->consolidarCierresAbiertos($sucursalId, $cajas));
        if($selector === 0)return $datos;

        $cierre = cierrescajas::uniquewhereArray(['id'=>$selector, 'estado'=>1, 'idsucursal_id'=>$sucursalId]);
        if(!$cierre)return $datos + ['error'=>'El cierre solicitado no existe o no pertenece a la sucursal.'];

        return array_replace($datos, [
            'cierreselected'=>$cierre,
            'cajaselected'=>(string)$cierre->nombrecaja,
            'discriminarmediospagos'=>cierrescajas::discriminarmediospagos((string)$cierre->id),
            'discriminarimpuestos'=>cierrescajas::discriminarimpuesto((string)$cierre->id)
        ]);
    }

    /**
     * Consulta ventas y medios de pago del Z para un rango seleccionado.
     *
     * Consumidor actual: reportescontrolador::consultafechazetadiario(), ruta
     * POST /admin/api/consultafechazetadiario, llamada desde
     * src/ts/caja/fechazetadiario.ts. Valida fechas, normaliza IDs y comprueba
     * que cajas y consecutivos activos pertenezcan a la sucursal.
     */
    public function consultarZPorRango(string $fechaInicio, string $fechaFin, array $cajaIds, array $consecutivoIds, int $sucursalId): array {
        $cajasNormalizadas = $this->normalizarIds($cajaIds);
        $consecutivosNormalizados = $this->normalizarIds($consecutivoIds);
        $cajaIds = $cajasNormalizadas['ids'];
        $consecutivoIds = $consecutivosNormalizados['ids'];
        $errores = $this->validarConsultaPorRango($fechaInicio, $fechaFin, $cajaIds,  $consecutivoIds, $sucursalId, $cajasNormalizadas['tieneInvalidos'], $consecutivosNormalizados['tieneInvalidos']);
        if($errores)return $this->crearResultadoRangoVacio() + ['error'=>$errores];

        // Los métodos heredados aún reciben listas SQL. Aquí sólo se forman
        // después de validar y convertir cada elemento a entero positivo.
        $cajasSql = implode(', ', $cajaIds);
        $consecutivosSql = implode(', ', $consecutivoIds);
        $datosVenta = facturas::zDiarioTotalVentas($cajasSql, $consecutivosSql, $sucursalId, $fechaInicio, $fechaFin);
        $datosMediosPago = facturas::zDiarioMediosPago($cajasSql, $consecutivosSql, $sucursalId, $fechaInicio, $fechaFin);

        return [
            'datosventa'=>$this->normalizarTotalesVenta($datosVenta),
            'datosmediospago'=>$datosMediosPago
        ];
    }

    /**
     * Suma los indicadores de todos los cierres abiertos y obtiene sus medios
     * de pago e impuestos como un único Z diario actual.
     */
    private function consolidarCierresAbiertos(int $sucursalId, array $cajas): array{
        $cierres = cierrescajas::whereArray(['estado'=>0, 'idsucursal_id'=>$sucursalId]);
        $resumen = $this->crearResumenVacio();
        $nombres = [];

        foreach($cierres as $cierre){
            $resumen->id[] = (int)$cierre->id;
            $nombres[] = (string)$cierre->nombrecaja;
            $resumen->ingresoventas += (float)$cierre->ingresoventas;
            $resumen->valorimpuestototal += (float)$cierre->valorimpuestototal;
            $resumen->totaldescuentos += (float)$cierre->totaldescuentos;
            $resumen->realventas += (float)$cierre->realventas;
            $resumen->facturaselectronicas += (int)$cierre->facturaselectronicas;
            $resumen->facturaspos += (int)$cierre->facturaspos;
            $resumen->valorfe += (float)$cierre->valorfe;
            $resumen->valorpos += (float)$cierre->valorpos;
        }

        if($resumen->id === []){
            $nombres = array_map(static fn(object $cajaActiva): string => (string)$cajaActiva->nombre, $cajas);
            return ['cierreselected'=>$resumen, 'cajaselected'=>implode(' - ', $nombres)];
        }

        $resumen->nombrecaja = implode(' - ', $nombres);
        return [
            'cierreselected'=>$resumen,
            'cajaselected'=>$resumen->nombrecaja,
            'discriminarmediospagos'=>cierrescajas::discriminarmediospagoscajas($resumen->id),
            'discriminarimpuestos'=>cierrescajas::discriminarimpuestocaja($resumen->id)
        ];
    }

    /** Valida fechas, selecciones y pertenencia a la sucursal activa. */
    private function validarConsultaPorRango(string $fechaInicio, string $fechaFin, array $cajaIds, array $consecutivoIds, int $sucursalId, bool $cajasInvalidas, bool $consecutivosInvalidos): array {
        $errores = [];
        $inicio = $this->crearFecha($fechaInicio);
        $fin = $this->crearFecha($fechaFin);

        if(!$inicio || !$fin){
            $errores[] = 'El rango de fechas no es válido.';
        }elseif($inicio > $fin){
            $errores[] = 'La fecha inicial no puede ser posterior a la fecha final.';
        }
        if($sucursalId < 1)$errores[] = 'La sucursal activa no es válida.';
        if($cajasInvalidas)$errores[] = 'La selección de cajas contiene identificadores inválidos.';
        if($consecutivosInvalidos)$errores[] = 'La selección de facturadores contiene identificadores inválidos.';
        if($cajaIds === [])$errores[] = 'Selecciona al menos una caja.';
        if($consecutivoIds === [])$errores[] = 'Selecciona al menos un facturador.';

        if($cajaIds !== []){
            $permitidas = array_map(
                static fn(object $cajaActiva): int => (int)$cajaActiva->id,
                caja::whereArray(['idsucursalid'=>$sucursalId, 'estado'=>1])
            );
            if(array_diff($cajaIds, $permitidas))$errores[] = 'Una o más cajas no pertenecen a la sucursal activa.';
        }
        if($consecutivoIds !== []){
            $permitidos = array_map(
                static fn(object $consecutivo): int => (int)$consecutivo->id,
                consecutivos::whereArray(['id_sucursalid'=>$sucursalId, 'estado'=>1])
            );
            if(array_diff($consecutivoIds, $permitidos))$errores[] = 'Uno o más facturadores no pertenecen a la sucursal activa.';
        }
        return $errores;
    }

    /** Acepta exclusivamente el formato emitido por el selector de fechas. */
    private function crearFecha(string $fecha): ?DateTimeImmutable{
        $fecha = trim($fecha);
        $objeto = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fecha);
        $errores = DateTimeImmutable::getLastErrors();
        if(!$objeto || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0)))return null;
        return $objeto->format('Y-m-d H:i:s') === $fecha ? $objeto : null;
    }

    /**
     * Convierte una selección externa en IDs positivos, únicos y ordenados, y
     * conserva una bandera cuando algún valor recibido no era válido.
     */
    private function normalizarIds(array $ids): array{
        $normalizados = [];
        $tieneInvalidos = false;
        foreach($ids as $id){
            $entero = filter_var($id, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if($entero === false){
                $tieneInvalidos = true;
                continue;
            }
            $normalizados[] = (int)$entero;
        }
        $normalizados = array_values(array_unique($normalizados));
        sort($normalizados, SORT_NUMERIC);
        return ['ids'=>$normalizados, 'tieneInvalidos'=>$tieneInvalidos];
    }

    /** Sustituye SUM nulos por ceros para mantener estable el contrato JSON. */
    private function normalizarTotalesVenta(?array $datosVenta): array{
        $vacio = $this->crearResultadoRangoVacio()['datosventa'];
        if(!$datosVenta)return $vacio;

        foreach($vacio as $campo=>$valor){
            $vacio[$campo] = $datosVenta[$campo] ?? $valor;
        }
        return $vacio;
    }

    /** Crea la respuesta segura utilizada cuando no hay datos o hay errores. */
    private function crearResultadoRangoVacio(): array{
        return [
            'datosventa'=>[
                'subtotalventa'=>0,
                'base'=>0,
                'valorimpuestototal'=>0,
                'totalventa'=>0,
                'ELECTRONICAS'=>0,
                'POS'=>0,
                'total_ELECTRONICAS'=>0,
                'total_POS'=>0
            ],
            'datosmediospago'=>[]
        ];
    }

    /** Crea el objeto estable que la vista puede renderizar sin condicionales. */
    private function crearResumenVacio(): stdClass{
        return (object)[
            'id'=>[],
            'nombrecaja'=>'',
            'fechainicio'=>'',
            'fechacierre'=>'',
            'ingresoventas'=>0.0,
            'valorimpuestototal'=>0.0,
            'totaldescuentos'=>0.0,
            'realventas'=>0.0,
            'facturaselectronicas'=>0,
            'facturaspos'=>0,
            'valorfe'=>0.0,
            'valorpos'=>0.0
        ];
    }
    
}
