<?php

namespace App\services\configuracion;

use App\Models\configuraciones\caja as CajaModel;
use App\Models\configuraciones\emisores as EmisorModel;
use App\Models\sucursales;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EmisoresConfiguracionService{

    private const CAMPOS_EDITABLES = ['nombre', 'nit', 'datosencabezados', 'telefono'];

    /** @return EmisorModel[] */
    public function listarEmisores(int $idSucursal): array{
        if($idSucursal <= 0)return [];
        return EmisorModel::whereArray(['idsucursal'=>$idSucursal]);
    }

    public function crearEmisor(array $datos, int $idSucursal): array{
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $datosEditables = array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
        $emisor = new EmisorModel($datosEditables);
        $emisor->idsucursal = $idSucursal;
        $alertas = $emisor->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = EmisorModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la creacion del emisor.');
            $transaccionIniciada = true;

            $this->obtenerSucursalBloqueada($idSucursal);
            [$creado, $idEmisor] = $emisor->crear_guardar();
            if(!$creado)throw new RuntimeException('No fue posible crear el emisor.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion del emisor.');

            $emisor->id = (int)$idEmisor;
            $emisor->created_at = date('Y-m-d H:i:s');
            return ['exito'=>['Emisor creado correctamente'], 'emisor'=>$emisor];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear emisor: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarEmisor(int $idEmisor, array $datos, int $idSucursal): array{
        $this->validarContexto($idEmisor, $idSucursal);
        $db = EmisorModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la actualizacion del emisor.');
            $transaccionIniciada = true;

            $this->obtenerSucursalBloqueada($idSucursal);
            $emisor = $this->obtenerEmisorBloqueado($idEmisor, $idSucursal);
            $datosEditables = array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
            $emisor->compara_objetobd_post($datosEditables);
            $emisor->id = $idEmisor;
            $emisor->idsucursal = $idSucursal;
            $alertas = $emisor->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$emisor->actualizar())throw new RuntimeException('No fue posible actualizar el emisor.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion del emisor.');
            return ['exito'=>['Datos del emisor actualizados'], 'emisor'=>[$emisor]];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar emisor '.$idEmisor.': '.$error->getMessage());
            return ['error'=>['Error al actualizar emisor']];
        }
    }

    public function actualizarEstadoEmisor(int $idEmisor, mixed $estadoSolicitado, int $idSucursal): array{
        $this->validarContexto($idEmisor, $idSucursal);
        $estado = filter_var($estadoSolicitado, FILTER_VALIDATE_INT);
        if($estado === false || !in_array($estado, [0, 1], true))
            throw new InvalidArgumentException('Estado de emisor invalido.', 422);

        $db = EmisorModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar el cambio de estado del emisor.');
            $transaccionIniciada = true;

            $this->obtenerSucursalBloqueada($idSucursal);
            $emisor = $this->obtenerEmisorBloqueado($idEmisor, $idSucursal);
            if((int)$emisor->estado !== $estado){
                $emisor->estado = $estado;
                if(!$emisor->actualizar())throw new RuntimeException('No fue posible actualizar el estado del emisor.');
            }

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar el cambio de estado del emisor.');
            return ['exito'=>['Emisor actualizado.']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar estado del emisor '.$idEmisor.': '.$error->getMessage());
            return ['error'=>['Emisor no es posible actualizar su estado.']];
        }
    }

    public function eliminarEmisor(int $idEmisor, int $idSucursal): array{
        $this->validarContexto($idEmisor, $idSucursal);
        $db = EmisorModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la eliminacion del emisor.');
            $transaccionIniciada = true;

            $this->obtenerSucursalBloqueada($idSucursal);
            $emisor = $this->obtenerEmisorBloqueado($idEmisor, $idSucursal);
            $cajaRelacionada = CajaModel::uniquewhereArrayForUpdate(['idemisor'=>$idEmisor]);
            if($cajaRelacionada){
                $db->rollback();
                return ['error'=>['El emisor esta asociado a una caja y no se puede eliminar.']];
            }

            if(!$emisor->eliminar_registro())throw new RuntimeException('No fue posible eliminar el emisor.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion del emisor.');
            return ['exito'=>['Emisor eliminado correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar emisor '.$idEmisor.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion del emisor']];
        }
    }


    private function validarContexto(int $idEmisor, int $idSucursal): void{
        if($idEmisor <= 0)throw new InvalidArgumentException('Identificador de emisor invalido.', 422);
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal activa no es valida.', 422);
    }

    private function obtenerSucursalBloqueada(int $idSucursal): sucursales{
        $sucursal = sucursales::findForUpdate('id', $idSucursal);
        if(!$sucursal)throw new InvalidArgumentException('Sucursal activa no encontrada.', 404);
        return $sucursal;
    }

    private function obtenerEmisorBloqueado(int $idEmisor, int $idSucursal): EmisorModel{
        $emisor = EmisorModel::findForUpdate('id', $idEmisor);
        if(!$emisor || (int)$emisor->idsucursal !== $idSucursal)
            throw new InvalidArgumentException('Emisor no encontrado en la sucursal activa.', 404);
        return $emisor;
    }

}