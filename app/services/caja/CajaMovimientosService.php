<?php

namespace App\services\caja;

use App\Models\caja\cierrescajas;
use App\Models\caja\categoriagastos;
use App\Models\caja\ingresoscajas;
use App\Models\configuraciones\bancos;
use App\Models\configuraciones\caja;
use App\Models\gastos;
use Throwable;

/**
 * Casos de uso para entradas y salidas operativas de dinero.
 *
 * No conoce Router, sesiones, archivos subidos ni variables superglobales. El
 * controlador entrega el usuario, la sucursal y la ruta del comprobante que ya
 * fue validado y almacenado.
 */
final class CajaMovimientosService
{
    /**
     * Registra un ingreso o gasto y actualiza el cierre correspondiente.
     *
     * Consumidor actual: cajacontrolador::ingresoGastoCaja(), llamado por el
     * formulario de views/admin/caja/index.php mediante
     * POST /admin/caja/ingresoGastoCaja. Si la caja no tiene cierre abierto,
     * crea uno antes de registrar el movimiento. Todo se confirma en una única
     * transacción.
     */
    public function registrarMovimiento(array $datos, int $sucursalId, int $usuarioId, ?string $rutaComprobante = null): array {
        $movimiento = $this->normalizarMovimiento($datos, $rutaComprobante);
        $errores = $this->validarMovimiento($movimiento, $sucursalId, $usuarioId);
        if($errores)return ['error'=>$errores];

        $db = cierrescajas::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar el movimiento de caja.');
            $transaccionIniciada = true;

            $cajaSeleccionada = caja::findForUpdate('id', $movimiento['id_caja']);
            if(
                !$cajaSeleccionada ||
                (int)$cajaSeleccionada->idsucursalid !== $sucursalId ||
                (int)$cajaSeleccionada->estado !== 1
            ){
                $db->rollback();
                return ['error'=>['La caja seleccionada no pertenece a la sucursal o está inactiva.']];
            }

            $cierre = $this->obtenerOCrearCierre((int)$movimiento['id_caja'], $sucursalId, (string)$cajaSeleccionada->nombre);

            if($movimiento['operacion'] === 'ingreso'){
                $this->registrarIngreso($movimiento, $cierre, $sucursalId, $usuarioId);
                $mensaje = 'Ingreso de dinero a caja es correcto';
            }else{
                $this->registrarGasto($movimiento, $cierre, $sucursalId, $usuarioId);
                $mensaje = 'El gasto fue registrado correctamente';
            }

            $cierre->actualizar();
            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el movimiento de caja.');

            return ['exito'=>[$mensaje]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log("No fue posible registrar el movimiento de caja: {$error->getMessage()}");
            return ['error'=>['No fue posible registrar el movimiento de caja. Intenta nuevamente.']];
        }
    }

    /**
     * Obtiene y bloquea el cierre abierto o crea el primero para la caja.
     * El bloqueo previo de la fila de caja serializa aperturas concurrentes.
     */
    private function obtenerOCrearCierre(int $cajaId, int $sucursalId, string $nombreCaja): cierrescajas{
        $cierre = cierrescajas::uniquewhereArray(['estado'=>0, 'idcaja'=>$cajaId, 'idsucursal_id'=>$sucursalId]);

        if($cierre){
            $cierre = cierrescajas::findForUpdate('id', (int)$cierre->id);
            if(
                !$cierre ||
                (int)$cierre->estado !== 0 ||
                (int)$cierre->idcaja !== $cajaId ||
                (int)$cierre->idsucursal_id !== $sucursalId
            )throw new \RuntimeException('El cierre de caja ya no está abierto.');
            return $cierre;
        }

        $nuevoCierre = new cierrescajas(['idcaja'=>$cajaId, 'nombrecaja'=>$nombreCaja, 'estado'=>0, 'idsucursal_id'=>$sucursalId]);
        $resultado = $nuevoCierre->crear_guardar();
        $nuevoCierre->id = (int)($resultado[1] ?? 0);
        if($nuevoCierre->id === 0)throw new \RuntimeException('No fue posible abrir la caja.');
        return $nuevoCierre;
    }

    /** Crea el ingreso y suma su valor a la base del cierre abierto. */
    private function registrarIngreso(array $movimiento, cierrescajas $cierre, int $sucursalId, int $usuarioId): void{
        $ingreso = new ingresoscajas(['idusuario'=>$usuarioId,'id_caja'=>$movimiento['id_caja'],'id_cierrecaja'=>(int)$cierre->id,'operacion'=>'ingreso','valor'=>$movimiento['valor'],'descripcion'=>$movimiento['descripcion']]);
        // El modelo toma por defecto la sucursal global; el caso de uso fuerza
        // la sucursal que recibió explícitamente del controlador.
        $ingreso->idsucursal_idfk = $sucursalId;
        $ingreso->crear_guardar();

        $cierre->basecaja = (float)$cierre->basecaja + $movimiento['valor'];
    }

    /**
     * Crea el gasto y suma su valor al acumulado de caja o banco del cierre.
     */
    private function registrarGasto(array $movimiento, cierrescajas $cierre, int $sucursalId, int $usuarioId): void{
        if(!categoriagastos::find('id', $movimiento['idcategoriagastos'])){
            throw new \RuntimeException('La categoría de gasto no existe.');
        }
        if($movimiento['origengasto'] === 'gastobanco'){
            $banco = bancos::find('id', $movimiento['id_banco']);
            if(!$banco || (int)$banco->estado !== 1)throw new \RuntimeException('El banco seleccionado no está activo.');
        }

        $gasto = new gastos([
            'idg_usuario'=>$usuarioId,
            'id_banco'=>$movimiento['origengasto'] === 'gastobanco' ? $movimiento['id_banco'] : null,
            'idg_caja'=>$movimiento['id_caja'],
            'idg_cierrecaja'=>(int)$cierre->id,
            'idcategoriagastos'=>$movimiento['idcategoriagastos'],
            'tipo_origen'=>$movimiento['origengasto'] === 'gastobanco' ? 1 : 0,
            'operacion'=>'gasto',
            'valor'=>$movimiento['valor'],
            'descripcion'=>$movimiento['descripcion'],
            'imgcomprobante'=>$movimiento['imgcomprobante']
        ]);
        $gasto->id_sucursalfk = $sucursalId;
        $gasto->crear_guardar();

        if($movimiento['origengasto'] === 'gastocaja'){
            $cierre->gastoscaja = (float)$cierre->gastoscaja + $movimiento['valor'];
        }else{
            $cierre->gastosbanco = (float)$cierre->gastosbanco + $movimiento['valor'];
        }
    }

    /** Normaliza los campos externos antes de aplicar reglas de negocio. */
    private function normalizarMovimiento(array $datos, ?string $rutaComprobante): array{
        $valorSinSeparadores = preg_replace('/[^0-9]/', '', (string)($datos['valor'] ?? ''));

        return [
            'operacion'=>strtolower(trim((string)($datos['operacion'] ?? ''))),
            'origengasto'=>strtolower(trim((string)($datos['origengasto'] ?? 'gastocaja'))),
            'id_caja'=>$this->enteroPositivo($datos['id_caja'] ?? null),
            'id_banco'=>$this->enteroPositivo($datos['id_banco'] ?? null),
            'idcategoriagastos'=>$this->enteroPositivo($datos['idcategoriagastos'] ?? null),
            'valor'=>$valorSinSeparadores === '' ? 0.0 : (float)$valorSinSeparadores,
            'descripcion'=>trim((string)($datos['descripcion'] ?? '')),
            'imgcomprobante'=>$rutaComprobante ?? ''
        ];
    }

    /** Valida los datos comunes y los requisitos propios de cada operación. */
    private function validarMovimiento(array $movimiento, int $sucursalId, int $usuarioId): array{
        $errores = [];
        if($sucursalId < 1)$errores[] = 'Error con la sucursal activa.';
        if($usuarioId < 1)$errores[] = 'Error con usuario de sistema';
        if(!in_array($movimiento['operacion'], ['ingreso', 'gasto'], true))$errores[] = 'La operación de caja no es válida.';
        if($movimiento['id_caja'] < 1)$errores[] = 'Caja no seleccionada';
        if($movimiento['valor'] <= 0)$errores[] = 'El valor del movimiento debe ser mayor que cero.';
        if(strlen($movimiento['descripcion']) > 244)$errores[] = 'Has excedido el límite de caracteres de la descripción';
        if(strlen($movimiento['imgcomprobante']) > 155)$errores[] = 'La ruta del comprobante excede el límite permitido.';

        if($movimiento['operacion'] === 'gasto'){
            if(!in_array($movimiento['origengasto'], ['gastocaja', 'gastobanco'], true))$errores[] = 'El origen del gasto no es válido.';
            if($movimiento['idcategoriagastos'] < 1)$errores[] = 'Tipo de gasto no seleccionado.';
            if($movimiento['origengasto'] === 'gastobanco' && $movimiento['id_banco'] < 1)$errores[] = 'Banco no seleccionado.';
        }

        return $errores;
    }

    /** Convierte un identificador externo en entero positivo o devuelve cero. */
    private function enteroPositivo(mixed $valor): int{
        $entero = filter_var($valor, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        return $entero === false ? 0 : (int)$entero;
    }
}
