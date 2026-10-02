<?php

namespace App\services\caja;

use App\Models\ActiveRecord;
use App\Models\caja\cierrescajas;
use App\Models\caja\declaracionesdineros;
use App\Models\configuraciones\bancos;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\mediospago;
use App\Models\parametrizacion\config_local;
use App\Models\ventas\facturas;
use App\Repositories\creditos\separadoMediopagoRepository;
use stdClass;

/**
 * Construye las consultas y proyecciones de lectura utilizadas por caja.
 *
 * No conoce Router, vistas, variables superglobales ni respuestas HTTP. El
 * controlador sigue siendo responsable de autenticar, normalizar la petición
 * y decidir si el resultado se renderiza como HTML o se devuelve como JSON.
 */
final class CajaConsultasService
{
    /**
     * Prepara el panel principal de caja.
     *
     * Llamado desde cajacontrolador::index(), ruta GET /admin/caja. Devuelve
     * exactamente las variables de negocio consumidas por admin/caja/index.
     */
    public function obtenerPanelCaja(int $sucursalId, int $perfil, int $usuarioId): array{
        $mediospago = mediospago::all();
        $ultimoscierres = cierrescajas::whereArray(['idsucursal_id' => $sucursalId, 'estado' => 0]);

        $idsCierresConActividad = [];
        $ingresosVentas = 0.0;

        foreach($ultimoscierres as $cierre){
            if(
                (float)$cierre->ingresoventas > 0 ||
                (int)$cierre->totalcotizaciones > 0 ||
                (int)$cierre->totalfacturas > 0 ||
                (float)$cierre->creditocapital > 0
            ){
                $idsCierresConActividad[] = (int)$cierre->id;
                $ingresosVentas += (float)$cierre->ingresoventas;
            }
        }

        $facturas = [];
        if ($idsCierresConActividad !== []) {
            $facturas = facturas::facturasConMediosPago('idcierrecaja', $idsCierresConActividad, ['id_sucursal', $sucursalId, $perfil, $usuarioId]);

            foreach($facturas as $factura)
                $factura->mediosdepago = json_decode($factura->mediosdepago ?? '[]');
        }

        return [
            'conflocal' => config_local::getParamGlobal(),
            'datacierrescajas' => $ingresosVentas,
            'categoriasgastos' => (new CategoriasGastoService())->listarCategorias(),
            'cajas' => caja::whereArray(['idsucursalid' => $sucursalId, 'estado' => 1]),
            'bancos' => bancos::all(), 'facturas' => $facturas, 'mediospago' => $mediospago
            ];
    }

    /**
     * Prepara la pantalla de cierre para la caja principal de la sucursal.
     *
     * Llamado desde cajacontrolador::cerrarcaja(), ruta
     * GET /admin/caja/cerrarcaja. La caja principal es la primera caja activa,
     * salvo que exista una marcada como no editable.
     */
    public function obtenerCierrePrincipal(int $sucursalId): array{
        $cajas = caja::whereArray(['idsucursalid' => $sucursalId, 'estado' => 1]);
        $conflocal = config_local::getParamGlobal();
        $mediospagos = mediospago::whereArray(['estado' => 1]);

        $datos = $this->resumenVacio();
        $datos['cajas'] = $cajas;
        $datos['conflocal'] = $conflocal;
        $datos['mediospagos'] = $mediospagos;

        if($cajas === [])return $datos;


        $cajaPrincipalId = (int)$cajas[0]->id;
        foreach ($cajas as $cajaActiva) {
            if ((int)$cajaActiva->editable === 0) {
                $cajaPrincipalId = (int)$cajaActiva->id;
                break;
            }
        }

        $cierre = cierrescajas::uniquewhereArray(['estado' => 0, 'idcaja' => $cajaPrincipalId, 'idsucursal_id' => $sucursalId]);

        if (!$cierre)return $datos;
        return array_replace($datos, $this->construirResumenCierre($cierre, $cajaPrincipalId, $sucursalId, $conflocal));
    }

    /**
     * Lista los cierres finalizados de una sucursal del más reciente al más
     * antiguo.
     *
     * Llamado desde cajacontrolador::ultimoscierres(), ruta
     * GET /admin/caja/ultimoscierres.
     */
    public function listarCierresFinalizados(int $sucursalId): array{
        return cierrescajas::whereArray(['estado' => 1, 'idsucursal_id' => $sucursalId,], 'DESC');
    }

    /**
     * Obtiene el resumen de un cierre finalizado para su vista de detalle.
     *
     * Llamado desde cajacontrolador::detallecierrecaja(), ruta
     * GET /admin/caja/detallecierrecaja?id={id}.
     */
    public function obtenerDetalleCierreFinalizado(int $cierreId, int $sucursalId): ?array{
        $cierre = cierrescajas::uniquewhereArray(['id' => $cierreId, 'estado' => 1, 'idsucursal_id' => $sucursalId]);
        if (!$cierre)return null;
        $conflocal = config_local::getParamGlobal();
        return $this->construirResumenCierre($cierre, (int)$cierre->idcaja, $sucursalId, $conflocal) + ['conflocal' => $conflocal, 'mediospagos' => mediospago::all()];
    }

    /**
     * Obtiene el resumen del cierre abierto de una caja elegida por el usuario.
     *
     * Llamado desde cajacontrolador::datoscajaseleccionada(), endpoint POST
     * /admin/api/datoscajaseleccionada, utilizado por src/ts/caja/cerrarcaja.ts.
     */
    public function obtenerCajaSeleccionada(int $cajaId, int $sucursalId): ?array{
        $cierre = cierrescajas::uniquewhereArray(['idsucursal_id' => $sucursalId, 'estado' => 0, 'idcaja' => $cajaId]);
        if (!$cierre) return null;
        $conflocal = config_local::getParamGlobal();
        return $this->construirResumenCierre($cierre, $cajaId, $sucursalId, $conflocal);
    }

    /**
     * Proyección financiera reutilizada por CajaDocumentosService.
     *
     * CajaDocumentosService::prepararDetalleCierre() añade a esta información
     * la sucursal y los encabezados necesarios para la vista solicitada desde
     * GET /printdetallecierre?id={id}.
     */
    public function obtenerCierreParaImpresion(int $cierreId, int $sucursalId): ?array{
        $cierre = cierrescajas::uniquewhereArray(['id' => $cierreId, 'idsucursal_id' => $sucursalId]);
        if(!$cierre)return null;
        $resumen = $this->construirResumenCierre($cierre, (int)$cierre->idcaja, $sucursalId, config_local::getParamGlobal());
        return [
            'sobrantefaltante' => $resumen['sobrantefaltante'],
            'mediospagos' => mediospago::all(),
            'discriminarmediospagos' => $resumen['discriminarmediospagos'],
            'discriminarimpuesto' => $resumen['discriminarimpuesto'],
            'ultimocierre' => $resumen['ultimocierre'],
            'facturas' => $resumen['facturas'],
            'ventasxusuarios' => $resumen['ventasxusuarios'],
        ];
    }

    /**
     * Construye una única representación financiera de un cierre. Sustituye la
     * lógica que estaba repetida en cerrarcaja, detallecierrecaja,
     * datoscajaseleccionada y CajaDocumentosService::prepararDetalleCierre.
     */
    private function construirResumenCierre(cierrescajas $cierre, int $cajaId, int $sucursalId, array $conflocal): array {
        $facturas = facturas::idregistros('idcierrecaja', $cierre->id);
        $discriminarmediospagos = $this->agruparMediosPago((int)$cierre->id);
        $discriminarimpuesto = cierrescajas::discriminarimpuesto((string)$cierre->id);
        $discriminargastos = cierrescajas::discriminargastos((string)$cierre->id, $cajaId, $sucursalId);
        $ventasxusuarios = cierrescajas::ventasXusuario((string)$cierre->id);
        $declaracion = declaracionesdineros::idregistros('idcierrecajaid', $cierre->id);

        $indicadorCaja = (int)($conflocal['indicador_caja']->valor_final ?? 0);
        $diferencial = $this->calcularDiferencial($cierre, $indicadorCaja);
        $sobrantefaltante = $this->cruzarDeclaraciones($declaracion, $discriminarmediospagos, $diferencial, (int)$cierre->id);

        foreach($facturas as $factura){
            $facturaId = (int)$factura->id;
            $factura->mediosdepago = ActiveRecord::camposJoinObj("SELECT * FROM factmediospago JOIN mediospago ON factmediospago.idmediopago = mediospago.id WHERE id_factura = {$facturaId};");
        }

        return [
            'discriminarimpuesto' => $discriminarimpuesto,
            'discriminargastos' => $discriminargastos,
            'sobrantefaltante' => $sobrantefaltante,
            'discriminarmediospagos' => $discriminarmediospagos,
            'ultimocierre' => $cierre,
            'facturas' => $facturas,
            'ventasxusuarios' => $ventasxusuarios,
        ];
    }

    /** Agrupa pagos de facturas y de separados por identificador de medio. */
    private function agruparMediosPago(int $cierreId): array{
        $facturaPagos = cierrescajas::discriminarmediospagos((string)$cierreId);
        $separadoPagos = (new separadoMediopagoRepository())->allMediospagoXCierrecaja($cierreId);

        $agrupados = [];
        foreach (array_merge($facturaPagos, $separadoPagos) as $pago) {
            $medioId = (int)$pago['idmediopago'];
            if (!isset($agrupados[$medioId])) {
                $agrupados[$medioId] = $pago;
                $agrupados[$medioId]['valor'] = (float)$pago['valor'];
                continue;
            }
            $agrupados[$medioId]['valor'] += (float)$pago['valor'];
        }
        return $agrupados;
    }

    /** Aplica la configuración histórica del indicador de efectivo de caja. */
    private function calcularDiferencial(cierrescajas $cierre, int $indicadorCaja): float{
        return match ($indicadorCaja) {
            1 => (float)$cierre->basecaja - (float)$cierre->gastoscaja,
            2 => -(float)$cierre->gastoscaja,
            3 => (float)$cierre->basecaja
                - (float)$cierre->gastoscaja
                - (float)$cierre->domicilios,
            default => -(float)$cierre->gastoscaja - (float)$cierre->domicilios,
        };
    }

    /**
     * Cruza el valor declarado manualmente con el registrado por el sistema y
     * agrega medios presentes en el sistema que todavía no fueron declarados.
     */
    private function cruzarDeclaraciones(array $declaraciones, array $mediosSistema, float $diferencial, int $cierreId): array {
        $resultado = $declaraciones;

        foreach($mediosSistema as $pago){
            $valorSistema = (float)$pago['valor'];
            if((int)$pago['idmediopago'] === 1)
                $valorSistema += $diferencial;

            $encontrado = false;
            foreach($declaraciones as $indice => $declaracion){
                if ((int)$pago['idmediopago'] === (int)$declaracion->id_mediopago) {
                    $resultado[$indice]->valorsistema = $valorSistema;
                    $encontrado = true;
                    break;
                }
            }

            if($encontrado)continue;

            $declaracionFaltante = new stdClass();
            $declaracionFaltante->id_mediopago = $pago['idmediopago'];
            $declaracionFaltante->idcierrecajaid = $cierreId;
            $declaracionFaltante->nombremediopago = $pago['mediopago'];
            $declaracionFaltante->valordeclarado = 0;
            $declaracionFaltante->valorsistema = $valorSistema;
            $resultado[] = $declaracionFaltante;
        }

        return $resultado;
    }

    /** Valores seguros cuando no hay cajas activas o todavía no existe cierre. */
    private function resumenVacio(): array{
        return [
            'conflocal' => [],
            'cajas' => [],
            'discriminarimpuesto' => [],
            'discriminargastos' => [],
            'sobrantefaltante' => [],
            'mediospagos' => [],
            'discriminarmediospagos' => [],
            'ultimocierre' => null,
            'facturas' => [],
            'ventasxusuarios' => [],
        ];
    }


}
