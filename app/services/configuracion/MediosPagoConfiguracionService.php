<?php

namespace App\services\configuracion;

use App\Models\configuraciones\mediospago as MedioPagoModel;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class MediosPagoConfiguracionService{

    private const CAMPOS_EDITABLES = ['mediopago'];
    private const MEDIO_PAGO_EFECTIVO = 1;
    private const RELACIONES = [
        ['tabla'=>'factmediospago', 'columna'=>'idmediopago'],
        ['tabla'=>'separadomediopago', 'columna'=>'mediopago_id'],
        ['tabla'=>'cuotas', 'columna'=>'mediopagoid'],
        ['tabla'=>'pagos_comisiones', 'columna'=>'idmediopagoid'],
        ['tabla'=>'declaracionesdineros', 'columna'=>'id_mediopago'],
    ];

    /** @return MedioPagoModel[] */
    public function listarMediosPago(): array{
        return MedioPagoModel::all();
    }

    public function crearMedioPago(array $datos): array{
        $medioPago = new MedioPagoModel($this->obtenerDatosEditables($datos));
        $alertas = $medioPago->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = MedioPagoModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la creacion del medio de pago.');
            $transaccionIniciada = true;

            [$creado, $idMedioPago] = $medioPago->crear_guardar();
            if(!$creado)throw new RuntimeException('No fue posible crear el medio de pago.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion del medio de pago.');
            $medioPago->id = (int)$idMedioPago;
            return ['exito'=>['Medio de pago creado correctamente'], 'mediopago'=>$medioPago];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear medio de pago: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarMedioPago(int $idMedioPago, array $datos): array{
        $this->validarId($idMedioPago);
        $db = MedioPagoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la actualizacion del medio de pago.');
            $transaccionIniciada = true;

            $medioPago = $this->obtenerMedioPagoBloqueado($idMedioPago);
            if($idMedioPago === self::MEDIO_PAGO_EFECTIVO)
                throw new InvalidArgumentException('El medio de pago efectivo no se puede modificar.', 422);

            $medioPago->compara_objetobd_post($this->obtenerDatosEditables($datos));
            $medioPago->id = $idMedioPago;
            $alertas = $medioPago->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$medioPago->actualizar())throw new RuntimeException('No fue posible actualizar el medio de pago.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion del medio de pago.');
            return ['exito'=>['Datos del medio de pago actualizados'], 'mediopago'=>$medioPago];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar medio de pago '.$idMedioPago.': '.$error->getMessage());
            return ['error'=>['Error al actualizar el medio de pago']];
        }
    }

    public function actualizarEstadoMedioPago(int $idMedioPago, mixed $estadoSolicitado): array{
        $this->validarId($idMedioPago);
        $estado = filter_var($estadoSolicitado, FILTER_VALIDATE_INT);
        if($estado === false || !in_array($estado, [0, 1], true))
            throw new InvalidArgumentException('Estado de medio de pago invalido.', 422);

        $db = MedioPagoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar el cambio de estado del medio de pago.');
            $transaccionIniciada = true;

            $medioPago = $this->obtenerMedioPagoBloqueado($idMedioPago);
            if($idMedioPago === self::MEDIO_PAGO_EFECTIVO && $estado === 0)
                throw new InvalidArgumentException('El medio de pago efectivo debe permanecer activo.', 422);

            if((int)$medioPago->estado !== $estado){
                $medioPago->estado = $estado;
                if(!$medioPago->actualizar())
                    throw new RuntimeException('No fue posible actualizar el estado del medio de pago.');
            }

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar el cambio de estado del medio de pago.');
            return ['exito'=>['Medio de pago actualizado.']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar estado del medio de pago '.$idMedioPago.': '.$error->getMessage());
            return ['error'=>['El medio de pago no pudo actualizar su estado.']];
        }
    }

    public function eliminarMedioPago(int $idMedioPago): array{
        $this->validarId($idMedioPago);
        $db = MedioPagoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la eliminacion del medio de pago.');
            $transaccionIniciada = true;

            $medioPago = $this->obtenerMedioPagoBloqueado($idMedioPago);
            if($idMedioPago === self::MEDIO_PAGO_EFECTIVO)
                throw new InvalidArgumentException('El medio de pago efectivo no se puede eliminar.', 422);

            if($this->tieneMovimientosAsociados($idMedioPago)){
                $db->rollback();
                return ['error'=>['El medio de pago tiene movimientos asociados y no se puede eliminar.']];
            }

            if(!$medioPago->eliminar_registro())throw new RuntimeException('No fue posible eliminar el medio de pago.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion del medio de pago.');
            return ['exito'=>['Medio de pago eliminado correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar medio de pago '.$idMedioPago.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion del medio de pago']];
        }
    }

    private function obtenerDatosEditables(array $datos): array{
        return array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
    }

    private function validarId(int $idMedioPago): void{
        if($idMedioPago <= 0)throw new InvalidArgumentException('Identificador de medio de pago invalido.', 422);
    }

    private function obtenerMedioPagoBloqueado(int $idMedioPago): MedioPagoModel{
        $medioPago = MedioPagoModel::findForUpdate('id', $idMedioPago);
        if(!$medioPago)throw new InvalidArgumentException('Medio de pago no encontrado.', 404);
        return $medioPago;
    }

    private function tieneMovimientosAsociados(int $idMedioPago): bool{
        $db = MedioPagoModel::getDB();

        foreach(self::RELACIONES as $relacion){
            $consulta = "SELECT id FROM {$relacion['tabla']} WHERE {$relacion['columna']} = ? LIMIT 1 FOR UPDATE";
            $sentencia = $db->prepare($consulta);
            if(!$sentencia)
                throw new RuntimeException('No fue posible comprobar las relaciones del medio de pago.');

            try{
                if(!$sentencia->bind_param('i', $idMedioPago) || !$sentencia->execute() || !$sentencia->store_result())
                    throw new RuntimeException('No fue posible comprobar las relaciones del medio de pago.');
                if($sentencia->num_rows > 0)return true;
            }finally{
                $sentencia->close();
            }
        }

        return false;
    }

}