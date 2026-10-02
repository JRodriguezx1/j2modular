<?php

namespace App\services\configuracion;

use App\Models\compras;
use App\Models\configuraciones\bancos as BancoModel;
use App\Models\gastos;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class BancosConfiguracionService{

    private const CAMPOS_EDITABLES = ['nombre', 'numerocuenta'];

    /** @return BancoModel[] */

    public function crearBanco(array $datos): array{
        $banco = new BancoModel($this->obtenerDatosEditables($datos));
        $alertas = $banco->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = BancoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la creacion del banco.');
            $transaccionIniciada = true;

            [$creado, $idBanco] = $banco->crear_guardar();
            if(!$creado)throw new RuntimeException('No fue posible crear el banco.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion del banco.');
            $banco->id = (int)$idBanco;
            $banco->created_at = date('Y-m-d H:i:s');
            return ['exito'=>['Banco creado correctamente'], 'banco'=>$banco];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear banco: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarBanco(int $idBanco, array $datos): array{
        if($idBanco <= 0)throw new InvalidArgumentException('Identificador de banco invalido.', 422);
        $db = BancoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la actualizacion del banco.');
            $transaccionIniciada = true;

            $banco = BancoModel::findForUpdate('id', $idBanco);
            if(!$banco)throw new InvalidArgumentException('Banco no encontrado.', 404);

            $banco->compara_objetobd_post($this->obtenerDatosEditables($datos));
            $banco->id = $idBanco;
            $alertas = $banco->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$banco->actualizar())throw new RuntimeException('No fue posible actualizar el banco.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion del banco.');
            return ['exito'=>['Datos del banco actualizados'], 'banco'=>[$banco]];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar banco '.$idBanco.': '.$error->getMessage());
            return ['error'=>['Error al actualizar banco']];
        }
    }

    public function eliminarBanco(int $idBanco): array{
        if($idBanco <= 0)throw new InvalidArgumentException('Identificador de banco invalido.', 422);
        $db = BancoModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la eliminacion del banco.');
            $transaccionIniciada = true;

            $banco = BancoModel::findForUpdate('id', $idBanco);
            if(!$banco)throw new InvalidArgumentException('Banco no encontrado.', 404);

            $gastoRelacionado = gastos::uniquewhereArrayForUpdate(['id_banco'=>$idBanco]);
            $compraRelacionada = compras::uniquewhereArrayForUpdate(['idorigenbanco'=>$idBanco]);
            if($gastoRelacionado || $compraRelacionada){
                $db->rollback();
                return ['error'=>['El banco tiene movimientos asociados y no se puede eliminar.']];
            }

            if(!$banco->eliminar_registro())throw new RuntimeException('No fue posible eliminar el banco.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion del banco.');
            return ['exito'=>['Banco eliminado correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar banco '.$idBanco.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion del banco']];
        }
    }

    private function obtenerDatosEditables(array $datos): array{
        return array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
    }

}