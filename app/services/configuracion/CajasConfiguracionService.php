<?php

namespace App\services\configuracion;

use App\Models\caja\cierrescajas;
use App\Models\configuraciones\caja as CajaModel;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\emisores;
use App\Models\sucursales;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class CajasConfiguracionService{

    private const CAMPOS_EDITABLES = ['idemisor', 'idtipoconsecutivo', 'nombre'];

    /** @return CajaModel[] */
    public function listarCajas(int $idSucursal): array{
        if($idSucursal <= 0)return [];

        $cajas = CajaModel::whereArray(['idsucursalid'=>$idSucursal, 'estado'=>1]);
        $consecutivosSucursal = consecutivos::whereArray(['id_sucursalid'=>$idSucursal, 'estado'=>1]);
        $consecutivoPorId = [];
        foreach($consecutivosSucursal as $consecutivo)$consecutivoPorId[(int)$consecutivo->id] = $consecutivo;
        foreach($cajas as $caja)$caja->nombreconsecutivo = $consecutivoPorId[(int)$caja->idtipoconsecutivo] ?? '';
        return $cajas;
    }

    public function crearCaja(array $datos, int $idSucursal): array{
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $caja = $this->construirCaja($datos, $idSucursal);
        $alertas = $caja->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = CajaModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la creacion de la caja.');
            $transaccionIniciada = true;

            [$sucursal, $consecutivo] = $this->obtenerRelacionesBloqueadas($caja, $idSucursal);
            $caja->negocio = $sucursal->nombre;

            [$cajaCreada, $idCaja] = $caja->crear_guardar();
            if(!$cajaCreada)throw new RuntimeException('No fue posible crear la caja.');

            $cierreCaja = new cierrescajas(['idsucursal_id'=>$idSucursal, 'idcaja'=>(int)$idCaja, 'nombrecaja'=>$caja->nombre]);
            [$cierreCreado] = $cierreCaja->crear_guardar();
            if(!$cierreCreado)throw new RuntimeException('No fue posible crear el cierre inicial de la caja.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion de la caja.');

            $caja->id = (int)$idCaja;
            $caja->nombreconsecutivo = $consecutivo;

            return ['exito'=>['Caja creada correctamente'], 'caja'=>$caja];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear caja: '.$error->getMessage());
            return ['error'=>['Error durante la creacion de la caja.']];
        }
    }


    public function actualizarCaja(int $idCaja, array $datos, int $idSucursal): array{
        if($idCaja <= 0)throw new InvalidArgumentException('Identificador de caja invalido.', 422);
        if($idSucursal <= 0) throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $db = CajaModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la actualizacion de la caja.');
            $transaccionIniciada = true;

            $caja = CajaModel::findForUpdate('id', $idCaja);
            if(!$caja || (int)$caja->idsucursalid !== $idSucursal)
                throw new InvalidArgumentException('Caja no encontrada en la sucursal activa.', 404);

            $datosEditables = $this->normalizarDatos($datos);
            $caja->compara_objetobd_post($datosEditables);
            $caja->id = $idCaja;
            $caja->idsucursalid = $idSucursal;

            [$sucursal] = $this->obtenerRelacionesBloqueadas($caja, $idSucursal);
            $caja->negocio = $sucursal->nombre;

            $alertas = $caja->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$caja->actualizar())throw new RuntimeException('No fue posible actualizar la caja.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion de la caja.');

            return ['exito'=>['Datos de la caja actualizados'], 'caja'=>[$caja]];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar caja '.$idCaja.': '.$error->getMessage());
            return ['error'=>['Error al actualizar caja']];
        }
    }

    public function eliminarCaja(int $idCaja, int $idSucursal): array{
        if($idCaja <= 0)throw new InvalidArgumentException('Identificador de caja invalido.', 422);
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $db = CajaModel::getDB();
        $transaccionIniciada = false;
        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la eliminacion de la caja.');
            $transaccionIniciada = true;

            $caja = CajaModel::findForUpdate('id', $idCaja);
            if(!$caja || (int)$caja->idsucursalid !== $idSucursal)
                throw new InvalidArgumentException('Caja no encontrada en la sucursal activa.', 404);

            $cierreCaja = cierrescajas::uniquewhereArrayForUpdate(['idsucursal_id'=>$idSucursal, 'idcaja'=>$idCaja, 'estado'=>0]);

            if($cierreCaja && (float)$cierreCaja->ingresoventas > 0){
                $db->rollback();
                return ['error'=>['No se puede eliminar la caja hasta que se haga el cierre de caja']];
            }

            try{
                $caja->eliminar_registro();
            }catch(Throwable){
                // Si existen registros relacionados, la caja se desactiva.
                $caja->estado = 0;
                if(!$caja->actualizar())throw new RuntimeException('No fue posible desactivar la caja.');
            }

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion de la caja.');
            return ['exito'=>['Caja eliminado correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar caja '.$idCaja.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion']];
        }
    }

    private function construirCaja(array $datos, int $idSucursal): CajaModel{
        $caja = new CajaModel($this->normalizarDatos($datos));
        $caja->idsucursalid = $idSucursal;
        return $caja;
    }

    private function normalizarDatos(array $datos): array{
        $datosEditables = array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));

        $idEmisorSolicitado = $datosEditables['idemisor'] ?? null;
        if($idEmisorSolicitado === null || $idEmisorSolicitado === ''){
            $datosEditables['idemisor'] = '';
        }else{
            $idEmisor = filter_var($idEmisorSolicitado, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) == false ? 0 : (int)$idEmisorSolicitado;
            if($idEmisor === 0)throw new InvalidArgumentException('La caja contiene relaciones que no pertenecen a la sucursal activa.', 422);
            $datosEditables['idemisor'] = $idEmisor;
        }

        return $datosEditables;
    }

    /** @return array{0:sucursales, 1:consecutivos, 2:emisores|null} */
    private function obtenerRelacionesBloqueadas(CajaModel $caja, int $idSucursal): array{
        $sucursal = sucursales::findForUpdate('id', $idSucursal);
        $consecutivo = consecutivos::uniquewhereArrayForUpdate(['id'=>(int)$caja->idtipoconsecutivo, 'id_sucursalid'=>$idSucursal]);
        $emisor = empty($caja->idemisor) ? null : emisores::uniquewhereArrayForUpdate(['id'=>(int)$caja->idemisor, 'idsucursal'=>$idSucursal]);

        if(!$sucursal || !$consecutivo || (!empty($caja->idemisor) && !$emisor))
            throw new InvalidArgumentException('La caja contiene relaciones que no pertenecen a la sucursal activa.', 422);

        return [$sucursal, $consecutivo, $emisor];
    }

}