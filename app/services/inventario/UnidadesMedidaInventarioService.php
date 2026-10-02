<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\conversionunidades;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\unidadesmedida;
use RuntimeException;
use Throwable;

/**
 * Casos de uso del catalogo de unidades y sus conversiones personalizadas.
 */
final class UnidadesMedidaInventarioService{

    public function crearUnidad(array $datos): array{
        $unidad = new unidadesmedida($datos);
        $unidad->editable = 1;
        $alertas = $unidad->validar();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $unidad->crear_guardar();
            $db->commit();
            return ['exito'=>['Unidad de medida creada correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Error en la creacion de la unidad de medida. '.$error->getMessage()]];
        }
    }

    public function actualizarUnidad(array $datos): array{
        $unidadId = (int)($datos['idunidad'] ?? 0);
        if($unidadId <= 0)return ['error'=>['La unidad de medida no es valida']];

        $unidadValidar = new unidadesmedida(['nombre'=>$datos['nombre'] ?? '']);
        $alertas = $unidadValidar->validar();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $unidad = unidadesmedida::findForUpdate('id', $unidadId);
            if(!$unidad){
                $db->rollback();
                return ['error'=>['La unidad de medida no existe']];
            }
            if((int)$unidad->editable !== 1){
                $db->rollback();
                return ['error'=>['Las unidades base del sistema no se pueden modificar']];
            }
            $unidad->nombre = $unidadValidar->nombre;
            $unidad->fechaupdate = date('Y-m-d H:i:s');
            $unidad->actualizar();
            $db->commit();
            return ['exito'=>['Unidad de medida actualizada correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Error de actualizacion en la unidad de medida. '.$error->getMessage()]];
        }
    }

    public function eliminarUnidad(array $datos): array{
        $unidadId = (int)($datos['id'] ?? 0);
        if($unidadId <= 0)return ['error'=>['La unidad de medida no es valida']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $unidad = unidadesmedida::findForUpdate('id', $unidadId);
            if(!$unidad){
                $db->rollback();
                return ['error'=>['La unidad de medida no existe']];
            }
            if((int)$unidad->editable !== 1){
                $db->rollback();
                return ['error'=>['Las unidades base del sistema no se pueden eliminar']];
            }

            $enProductos = (int)productos::numreg_where('idunidadmedida', $unidadId) > 0;
            $enInsumos = (int)subproductos::numreg_where('id_unidadmedida', $unidadId) > 0;
            if($enProductos || $enInsumos){
                $db->rollback();
                return ['error'=>['No se puede eliminar la unidad porque esta siendo utilizada']];
            }
            $unidad->eliminar_registro();
            $db->commit();
            return ['exito'=>['Unidad de medida eliminada correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>["Error en la eliminacion de la unidad de medida: {$error->getMessage()}"]];
        }
    }

    public function crearConversion(array $datos): array{
        $insumoId = (int)($datos['idsubproducto'] ?? 0);
        $unidadDestinoId = (int)($datos['idunidadmedidadestino'] ?? 0);
        $factor = $datos['factorconversion'] ?? null;
        if($insumoId <= 0 || $unidadDestinoId <= 0 || !is_numeric($factor) || (float)$factor <= 0)
            return ['error'=>['Los datos de la conversion no son validos']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $insumo = subproductos::findForUpdate('id', $insumoId);
            if(!$insumo || (int)$insumo->id_unidadmedida === $unidadDestinoId)
                throw new RuntimeException('El insumo no existe o La unidad destino debe ser diferente a la unidad base.');
            
            $unidades = unidadesmedida::findManyForUpdate([$insumo->id_unidadmedida, $unidadDestinoId]);
            if(count($unidades) !== 2)throw new RuntimeException('Una de las unidades de medida no existe.');

            $unidadesPorId = [];
            foreach($unidades as $unidad)$unidadesPorId[(int)$unidad->id] = $unidad;

            $existente = conversionunidades::uniquewhereArrayForUpdate(['idsubproducto'=>$insumoId, 'idunidadmedidadestino'=>$unidadDestinoId]);
            if($existente){
                $db->rollback();
                return ['error'=>['La conversion seleccionada ya existe']];
            }

            $conversion = new conversionunidades([
                'idsubproducto'=>$insumoId,
                'idunidadmedidabase'=>$insumo->id_unidadmedida,
                'idunidadmedidadestino'=>$unidadDestinoId,
                'nombreunidadbase'=>$unidadesPorId[$insumo->id_unidadmedida]->nombre,
                'nombreunidaddestino'=>$unidadesPorId[$unidadDestinoId]->nombre,
                'factorconversion'=>(float)$factor
            ]);
            $resultado = $conversion->crear_guardar();
            $conversion->id = (int)$resultado[1];
            $db->commit();
            return ['exito'=>['Conversion de unidad creada exitosamente'], 'newCv'=>$conversion];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>["Error al crear la conversion, intenta nuevamente: {$error->getMessage()}"]];
        }
    }

    public function eliminarConversion(int $conversionId): array{
        if($conversionId <= 0)return ['error'=>['El id de la conversion no es valido']];
        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $conversion = conversionunidades::findForUpdate('id', $conversionId);
            if(!$conversion){
                $db->rollback();
                return ['error'=>['La conversion no existe']];
            }
            $conversion->eliminar_registro();
            $db->commit();
            return ['exito'=>['Conversion de unidad eliminada exitosamente']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Error al eliminar la conversion, intenta nuevamente. '.$error->getMessage()]];
        }
    }

}
