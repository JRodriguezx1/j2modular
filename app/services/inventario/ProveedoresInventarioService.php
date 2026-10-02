<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\compras;
use App\Models\inventario\proveedores;
use Throwable;

/**
 * Casos de uso para crear, actualizar y eliminar proveedores del inventario.
 */
final class ProveedoresInventarioService{

    public function crearProveedor(array $datos): array{
        $proveedor = new proveedores($datos);
        $alertas = $proveedor->validar_nueva_categoria();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $resultado = $proveedor->crear_guardar();
            $proveedor->id = (int)$resultado[1];
            $proveedor->created_at = date('Y-m-d H:i:s');
            $db->commit();
            return [
                'exito'=>['Proveedor creado correctamente'],
                'proveedor'=>$proveedor
            ];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al crear proveedor: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarProveedor(array $datos): array{
        $proveedorId = (int)($datos['id'] ?? 0);
        if($proveedorId <= 0)return ['error'=>['El proveedor no es valido']];

        $proveedorValidar = new proveedores($datos);
        $alertas = $proveedorValidar->validar_nueva_categoria();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $proveedor = proveedores::findForUpdate('id', $proveedorId);
            if(!$proveedor){
                $db->rollback();
                return ['error'=>['El proveedor no existe']];
            }

            $proveedor->compara_objetobd_post($datos);
            $proveedor->actualizar();
            $db->commit();
            return [
                'exito'=>['Datos del proveedor actualizados'],
                'proveedor'=>[$proveedor]
            ];
        }catch(Throwable $error){
            $db->rollback();
            error_log("Error al actualizar proveedor {$proveedorId}: {$error->getMessage()}");
            return ['error'=>['Error al actualizar proveedor']];
        }
    }

    public function eliminarProveedor(array $datos): array{
        $proveedorId = (int)($datos['id'] ?? 0);
        if($proveedorId <= 0)return ['error'=>['El proveedor no es valido']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $proveedor = proveedores::findForUpdate('id', $proveedorId);
            if(!$proveedor){
                $db->rollback();
                return ['error'=>['El proveedor no existe']];
            }

            if((int)compras::numreg_where('idproveedor', $proveedorId) > 0){
                $db->rollback();
                return ['error'=>['No se puede eliminar el proveedor porque tiene compras asociadas']];
            }

            $proveedor->eliminar_registro();
            $db->commit();
            return ['exito'=>['Proveedor eliminado correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            error_log("Error al eliminar proveedor {$proveedorId}: {$error->getMessage()}");
            return ['error'=>['Error en el proceso de eliminacion']];
        }
    }
}
