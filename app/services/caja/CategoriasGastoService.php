<?php

namespace App\services\caja;

use App\Models\caja\categoriagastos;
use App\Models\gastos;
use Throwable;

/**
 * Casos de uso para administrar el catálogo de categorías de gasto.
 *
 * No conoce Router, sesiones, formularios ni vistas. Devuelve arreglos de
 * dominio que el controlador transforma en variables para la interfaz.
 */
final class CategoriasGastoService
{
    private const ULTIMA_CATEGORIA_SISTEMA = 11;

    /**
     * Lista el catálogo utilizado por la pantalla de categorías y el selector
     * del formulario de gastos.
     *
     * Consumidores actuales: las tres acciones de categorías en
     * cajacontrolador y CajaConsultasService::obtenerPanelCaja().
     */
    public function listarCategorias(): array{
        return categoriagastos::ordenar('id', 'ASC');
    }

    /**
     * Crea una categoría con nombre único.
     *
     * Consumidor actual: cajacontrolador::crear_categoriaGasto(), llamado por
     * POST /admin/caja/crear_categoriaGasto desde
     * src/ts/caja/categoriasgastos.ts.
     */
    public function crearCategoria(array $datos): array{
        $nombre = $this->normalizarNombre($datos['nombre'] ?? '');
        $errores = $this->validarNombre($nombre);
        if($errores)return ['error'=>$errores];
        if(!$this->nombreDisponible($nombre))return ['error'=>['Ya existe una categoría de gasto con ese nombre.']];

        try{
            (new categoriagastos(['nombre'=>$nombre]))->crear_guardar();
            return ['exito'=>['Categoría de gasto creada correctamente']];
        }catch(Throwable $error){
            error_log("No fue posible crear la categoría de gasto: {$error->getMessage()}");
            return ['error'=>['Error en la creación de la categoría de gasto.']];
        }
    }

    /**
     * Actualiza una categoría creada por el usuario.
     *
     * Consumidor actual: cajacontrolador::editarcategoriagasto(), llamado por
     * POST /admin/caja/editarcategoriagasto desde
     * src/ts/caja/categoriasgastos.ts. Las categorías 1 a 11 son catálogo base
     * del sistema y no se pueden modificar.
     */
    public function editarCategoria(array $datos): array{
        $categoriaId = $this->enteroPositivo($datos['id'] ?? null);
        $nombre = $this->normalizarNombre($datos['nombre'] ?? '');
        $errores = $this->validarNombre($nombre);
        if($categoriaId === 0)$errores[] = 'La categoría de gasto no es válida.';
        if($errores)return ['error'=>$errores];
        if($this->esCategoriaSistema($categoriaId))return ['error'=>['Las categorías base del sistema no se pueden modificar.']];
        if(!$this->nombreDisponible($nombre, $categoriaId))return ['error'=>['Ya existe una categoría de gasto con ese nombre.']];

        $db = categoriagastos::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar la edición.');
            $transaccionIniciada = true;
            $categoria = categoriagastos::findForUpdate('id', $categoriaId);
            if(!$categoria){
                $db->rollback();
                return ['error'=>['La categoría de gasto no existe.']];
            }

            $categoria->nombre = $nombre;
            $categoria->actualizar();
            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar la edición.');
            return ['exito'=>['Categoría de gasto actualizada correctamente']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log("No fue posible editar la categoría de gasto {$categoriaId}: {$error->getMessage()}");
            return ['error'=>['Error al actualizar la categoría de gasto.']];
        }
    }

    /**
     * Elimina una categoría creada por el usuario si ningún gasto la utiliza.
     *
     * Consumidor actual: cajacontrolador::categoriaGasto() cuando recibe POST
     * /admin/caja/categoriaGasto desde el formulario de eliminación. Las
     * categorías base del sistema están protegidas también en backend.
     */
    public function eliminarCategoria(array $datos): array{
        $categoriaId = $this->enteroPositivo($datos['id'] ?? null);
        if($categoriaId === 0)return ['error'=>['La categoría de gasto no es válida.']];
        if($this->esCategoriaSistema($categoriaId))return ['error'=>['Las categorías base del sistema no se pueden eliminar.']];

        $db = categoriagastos::getDB();
        $transaccionIniciada = false;

        try{
            if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar la eliminación.');
            $transaccionIniciada = true;
            $categoria = categoriagastos::findForUpdate('id', $categoriaId);
            if(!$categoria){
                $db->rollback();
                return ['error'=>['La categoría de gasto no existe.']];
            }
            if(gastos::uniquewhereArray(['idcategoriagastos'=>$categoriaId])){
                $db->rollback();
                return ['error'=>['No se puede eliminar porque la categoría está asociada a uno o más gastos.']];
            }

            $categoria->eliminar_registro();
            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar la eliminación.');
            return ['exito'=>['Categoría de gasto eliminada correctamente']];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log("No fue posible eliminar la categoría de gasto {$categoriaId}: {$error->getMessage()}");
            return ['error'=>['Error al eliminar la categoría de gasto.']];
        }
    }

    /** Valida las mismas restricciones de longitud usadas por el modelo. */
    private function validarNombre(string $nombre): array{
        $errores = [];
        if($nombre === '')$errores[] = 'Nombre no especificado';
        if(mb_strlen($nombre) > 24)$errores[] = 'Nombre del gasto muy extenso';
        return $errores;
    }

    /**
     * Comprueba duplicados ignorando mayúsculas y espacios de los extremos.
     * Al editar, excluye la propia categoría de la comparación.
     */
    private function nombreDisponible(string $nombre, int $categoriaIdIgnorada = 0): bool{
        $nombreComparable = mb_strtolower($nombre, 'UTF-8');
        foreach($this->listarCategorias() as $categoria){
            if((int)$categoria->id === $categoriaIdIgnorada)continue;
            if(mb_strtolower(trim((string)$categoria->nombre), 'UTF-8') === $nombreComparable)return false;
        }
        return true;
    }

    /** Normaliza espacios para almacenar nombres consistentes. */
    private function normalizarNombre(mixed $nombre): string{
        return trim((string)preg_replace('/\s+/u', ' ', (string)$nombre));
    }

    /** Indica si el id pertenece al catálogo base protegido por la interfaz. */
    private function esCategoriaSistema(int $categoriaId): bool{
        return $categoriaId <= self::ULTIMA_CATEGORIA_SISTEMA;
    }

    /** Convierte un identificador externo en entero positivo o devuelve cero. */
    private function enteroPositivo(mixed $valor): int{
        $entero = filter_var($valor, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        return $entero === false ? 0 : (int)$entero;
    }
}
