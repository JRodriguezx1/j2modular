<?php

namespace App\services\configuracion;

use App\Models\clientes\direcciones;
use App\Models\configuraciones\tarifas as TarifaModel;
use App\Models\ventas\facturas;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class TarifasConfiguracionService{

    private const CAMPOS_EDITABLES = ['nombre', 'valor'];

    /** @return TarifaModel[] */
    public function crearTarifa(array $datos): array{
        $tarifa = new TarifaModel($this->obtenerDatosEditables($datos));
        $alertas = $tarifa->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = TarifaModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la creacion de la tarifa.');
            $transaccionIniciada = true;

            [$creada, $idTarifa] = $tarifa->crear_guardar();
            if(!$creada)throw new RuntimeException('No fue posible crear la tarifa.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion de la tarifa.');
            $tarifa->id = (int)$idTarifa;
            return ['exito'=>['Tarifa creada correctamente'], 'tarifa'=>$tarifa];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear tarifa: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarTarifa(int $idTarifa, array $datos): array{
        if($idTarifa <= 0)throw new InvalidArgumentException('Identificador de tarifa invalido.', 422);
        $db = TarifaModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la actualizacion de la tarifa.');
            $transaccionIniciada = true;

            $tarifa = TarifaModel::findForUpdate('id', $idTarifa);
            if(!$tarifa)throw new InvalidArgumentException('Tarifa no encontrada.', 404);

            $tarifa->compara_objetobd_post($this->obtenerDatosEditables($datos));
            $tarifa->id = $idTarifa;
            $alertas = $tarifa->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$tarifa->actualizar())throw new RuntimeException('No fue posible actualizar la tarifa.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion de la tarifa.');
            return ['exito'=>['Datos de la tarifa actualizados'], 'tarifa'=>[$tarifa]];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar tarifa '.$idTarifa.': '.$error->getMessage());
            return ['error'=>['Error al actualizar tarifa']];
        }
    }

    public function eliminarTarifa(int $idTarifa): array{
        if($idTarifa <= 0)throw new InvalidArgumentException('Identificador de tarifa invalido.', 422);
        $db = TarifaModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la eliminacion de la tarifa.');
            $transaccionIniciada = true;

            $tarifa = TarifaModel::findForUpdate('id', $idTarifa);
            if(!$tarifa)throw new InvalidArgumentException('Tarifa no encontrada.', 404);

            if($idTarifa === 1){
                $db->rollback();
                return ['error'=>['La tarifa predeterminada del sistema no se puede eliminar.']];
            }

            $direccionRelacionada = direcciones::uniquewhereArrayForUpdate(['idtarifa'=>$idTarifa]);
            $facturaRelacionada = facturas::uniquewhereArrayForUpdate(['idtarifazona'=>$idTarifa]);
            if($direccionRelacionada || $facturaRelacionada){
                $db->rollback();
                return ['error'=>['La tarifa tiene direcciones o facturas asociadas y no se puede eliminar.']];
            }

            if(!$tarifa->eliminar_registro())throw new RuntimeException('No fue posible eliminar la tarifa.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion de la tarifa.');
            return ['exito'=>['Tarifa eliminada correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar tarifa '.$idTarifa.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion de la tarifa']];
        }
    }

    private function obtenerDatosEditables(array $datos): array{
        return array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
    }
    
}
