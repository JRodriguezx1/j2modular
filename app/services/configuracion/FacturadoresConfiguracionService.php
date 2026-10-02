<?php

namespace App\services\configuracion;

use App\Models\configuraciones\caja as CajaModel;
use App\Models\configuraciones\consecutivos as FacturadorModel;
use App\Models\configuraciones\tipofacturador;
use App\Models\sucursales;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class FacturadoresConfiguracionService{

    private const CAMPOS_EDITABLES = [
        'idtipofacturador',
        'idnegocio',
        'nombre',
        'rangoinicial',
        'rangofinal',
        'siguientevalor',
        'fechainicio',
        'fechafin',
        'resolucion',
        'prefijo',
    ];

    /** @return FacturadorModel[] */
    public function listarFacturadores(int $idSucursal): array{
        if($idSucursal <= 0)return [];
        $facturadores = FacturadorModel::whereArray(['id_sucursalid'=>$idSucursal]);
        $tipos = tipofacturador::all();
        $nombreTipoPorId = array_column($tipos, 'nombre', 'id');
        foreach($facturadores as $facturador)$facturador->nombretipofacturador = $nombreTipoPorId[(int)$facturador->idtipofacturador] ?? '';
        return $facturadores;
    }

    public function crearFacturador(array $datos, int $idSucursal): array{
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $facturador = new FacturadorModel($this->normalizarDatos($datos, true));
        $facturador->id_sucursalid = $idSucursal;
        $alertas = $facturador->validar();
        if(!empty($alertas['error']))return $alertas;

        $db = FacturadorModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la creacion del facturador.');
            $transaccionIniciada = true;

            [, $tipoFacturador] = $this->obtenerRelacionesBloqueadas($facturador, $idSucursal);
            [$creado, $idFacturador] = $facturador->crear_guardar();
            if(!$creado)throw new RuntimeException('No fue posible crear el facturador.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion del facturador.');

            $facturador->id = (int)$idFacturador;
            $facturador->nombretipofacturador = $tipoFacturador;
            return ['exito'=>['Facturador creado correctamente'], 'facturador'=>$facturador];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear facturador: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarFacturador(int $idFacturador, array $datos, int $idSucursal): array{
        if($idFacturador <= 0)
            throw new InvalidArgumentException('Identificador de facturador invalido.', 422);
        if($idSucursal <= 0)
            throw new InvalidArgumentException('La sucursal activa no es valida.', 422);

        $db = FacturadorModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la actualizacion del facturador.');
            $transaccionIniciada = true;

            $facturador = FacturadorModel::findForUpdate('id', $idFacturador);
            if(!$facturador || (int)$facturador->id_sucursalid !== $idSucursal)
                throw new InvalidArgumentException('Facturador no encontrado en la sucursal activa.', 404);

            $facturador->compara_objetobd_post($this->normalizarDatos($datos));
            $facturador->id = $idFacturador;
            $facturador->id_sucursalid = $idSucursal;

            [, $tipoFacturador] = $this->obtenerRelacionesBloqueadas($facturador, $idSucursal);
            $alertas = $facturador->validar();
            if(!empty($alertas['error'])){
                $db->rollback();
                return $alertas;
            }

            if(!$facturador->actualizar())throw new RuntimeException('No fue posible actualizar el facturador.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la actualizacion del facturador.');

            $facturador->nombretipofacturador = $tipoFacturador->nombre;
            return ['exito'=>['Datos del facturador actualizados'], 'facturador'=>[$facturador]];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al actualizar facturador '.$idFacturador.': '.$error->getMessage());
            return ['error'=>['Error al actualizar facturador']];
        }
    }

    public function eliminarFacturador(int $idFacturador, int $idSucursal): array{
        if($idFacturador <= 0)
            throw new InvalidArgumentException('Identificador de facturador invalido.', 422);
        if($idSucursal <= 0)
            throw new InvalidArgumentException('La sucursal activa no es valida.', 422);
        $db = FacturadorModel::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la eliminacion del facturador.');
            $transaccionIniciada = true;

            $facturador = FacturadorModel::findForUpdate('id', $idFacturador);
            if(!$facturador || (int)$facturador->id_sucursalid !== $idSucursal)
                throw new InvalidArgumentException('Facturador no encontrado en la sucursal activa.', 404);
            if($idFacturador === 1){
                $db->rollback();
                return ['error'=>['El facturador principal del sistema no se puede eliminar.']];
            }

            $cajaRelacionada = CajaModel::uniquewhereArrayForUpdate(['idtipoconsecutivo'=>$idFacturador]);
            if($cajaRelacionada){
                $db->rollback();
                return ['error'=>['⚠️ Facturador usado en caja, desasociar de caja para eliminar.']];
            }

            try{
                $facturador->eliminar_registro();
            }catch(Throwable $errorEliminacion){
                if(!str_contains(strtolower($errorEliminacion->getMessage()), 'foreign key constraint fails'))
                    throw $errorEliminacion;

                // Otras relaciones historicas impiden borrarlo; se conserva inactivo.
                $facturador->estado = 0;
                if(!$facturador->actualizar())
                    throw new RuntimeException('No fue posible desactivar el facturador.');
            }

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la eliminacion del facturador.');
            return ['exito'=>['Consecutivo eliminado correctamente']];
        }catch(InvalidArgumentException $error){
            if($transaccionIniciada)$db->rollback();
            throw $error;
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al eliminar facturador '.$idFacturador.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion del consecutivo']];
        }
    }

    private function normalizarDatos(array $datos, bool $paraCrear = false): array{
        $datosEditables = array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));

        if(array_key_exists('idtipofacturador', $datosEditables)){
            $idTipo = filter_var($datosEditables['idtipofacturador'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $datosEditables['idtipofacturador'] = $idTipo === false ? 0 : (int)$idTipo;
        }elseif($paraCrear){
            $datosEditables['idtipofacturador'] = 0;
        }

        if($paraCrear){
            if(!array_key_exists('nombre', $datosEditables))$datosEditables['nombre'] = '';
            if(!array_key_exists('siguientevalor', $datosEditables))$datosEditables['siguientevalor'] = '';
        }

        return $datosEditables;
    }

    /** @return array{0:sucursales, 1:tipofacturador} */
    private function obtenerRelacionesBloqueadas(FacturadorModel $facturador, int $idSucursal): array{
        $sucursal = sucursales::findForUpdate('id', $idSucursal);
        if(!$sucursal)throw new InvalidArgumentException('Sucursal activa no encontrada.', 404);

        $tipoFacturador = tipofacturador::findForUpdate('id', (int)$facturador->idtipofacturador);
        if(!$tipoFacturador)throw new InvalidArgumentException('Tipo de facturador no valido.', 422);

        return [$sucursal, $tipoFacturador];
    }
}
