<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\categorias;
use App\Models\inventario\productos;
use RuntimeException;
use Throwable;

/**
 * Casos de uso para crear, actualizar y eliminar categorias del inventario.
 */
final class CategoriasInventarioService{

    public function crearCategoria(array $datos): array{
        $categoria = new categorias($datos);
        $alertas = $categoria->validar_nueva_categoria();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $categoria->crear_guardar();
            $db->commit();
            return ['exito'=>['Categoria creada correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al crear categoria de inventario: '.$error->getMessage());
            $mensaje = str_contains($error->getMessage(), 'Duplicate entry')
                ? 'Ya existe una categoria con el mismo nombre o codigo'
                : 'Error al crear la categoria, intenta nuevamente';
            return ['error'=>[$mensaje]];
        }
    }

    public function actualizarCategoria(array $datos): array{
        $categoriaId = (int)($datos['id'] ?? 0);
        if($categoriaId <= 0)return ['error'=>['La categoria no es valida']];
        // Se valida antes de abrir la transaccion para no bloquear durante una solicitud invalida.
        $categoriaValidar = new categorias($datos);
        $alertas = $categoriaValidar->validar_nueva_categoria();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $categoria = categorias::findForUpdate('id', $categoriaId);
            if(!$categoria){
                $db->rollback();
                return ['error'=>['La categoria no existe']];
            }

            $categoria->compara_objetobd_post($datos);
            $categoria->actualizar();
            $db->commit();
            return ['exito'=>['Nombre de la categoria actualizado'], 'categoria'=>[$categoria]];
        }catch(Throwable $error){
            $db->rollback();
            error_log("Error al actualizar categoria de inventario {$categoriaId}: {$error->getMessage()}");
            $mensaje = str_contains($error->getMessage(), 'Duplicate entry')
                ? 'Ya existe una categoria con el mismo nombre o codigo'
                : 'Error al actualizar la categoria, intenta nuevamente';
            return ['error'=>[$mensaje]];
        }
    }

    public function eliminarCategoria(array $datos): array{
        $categoriaId = (int)($datos['id'] ?? 0);
        if($categoriaId <= 0)return ['error'=>['La categoria no es valida']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $categoria = categorias::findForUpdate('id', $categoriaId);
            if(!$categoria){
                $db->rollback();
                return ['error'=>['La categoria no existe']];
            }
            // Evita activar el borrado en cascada de productos y sus registros relacionados.
            if((int)productos::numreg_where('idcategoria', $categoriaId) > 0){
                $db->rollback();
                return ['error'=>['No se puede eliminar la categoria porque tiene productos asociados']];
            }

            if(!$categoria->eliminar_registro())throw new RuntimeException('No fue posible eliminar la categoria.');
            $db->commit();
            return ['exito'=>['Categoria eliminada correctamente']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>["Error al eliminar la categoria, intenta nuevamente - {$error->getMessage()}"]];
        }
    }
    
}
