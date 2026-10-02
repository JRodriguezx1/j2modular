<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\costosproductos;
use App\Models\inventario\productos;
use App\Models\inventario\productos_sub;
use RuntimeException;
use Throwable;

final class RecetaService{

    public function ensamblar(array $datos, int $sucursalId): array{
        $formula = new productos_sub($datos);
        $errores = $formula->validar();
        if(!empty($errores))return $errores;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // Bloquea primero el producto para serializar los cambios de su formula.
            $producto = productos::findForUpdate('id', (int)$formula->id_producto);
            if(!$producto)throw new RuntimeException('El producto seleccionado no existe.');

            $existente = productos_sub::uniquewhereArrayForUpdate(['id_producto'=>(int)$formula->id_producto, 'id_subproducto'=>(int)$formula->id_subproducto]);
            if($existente){
                $existente->compara_objetobd_post((array)$formula);
                $existente->actualizar();
                $mensaje = 'subproducto asociado y actualizado al producto principal.';
            }else{
                $resultado = $formula->crear_guardar();
                if(empty($resultado[0]))throw new RuntimeException('No fue posible asociar el subproducto.');
                $mensaje = 'subproducto asociado al producto principal.';
            }

            $this->recalcularCostoYRegistrarHistorico($producto, $sucursalId);
            $db->commit();
            return ['exito'=>[$mensaje]];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }

    public function desasociarInsumodeReceta(int $productoId, int $subproductoId, int $sucursalId): array{
        if($productoId <= 0 || $subproductoId <= 0)
            return ['error'=>['Hubo un error, intentalo nuevamente']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // Bloquea primero el producto para serializar los cambios de su formula.
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new RuntimeException('El producto principal no existe.');

            // Obtiene y bloquea la asociacion exacta en una sola consulta.
            $formula = productos_sub::uniquewhereArrayForUpdate(['id_producto'=>$productoId, 'id_subproducto'=>$subproductoId]);
            if(!$formula)throw new RuntimeException('La asociacion no existe.');

            if(!productos_sub::eliminar_wherearray(['id_producto'=>$productoId, 'id_subproducto'=>$subproductoId]))
                throw new RuntimeException('No fue posible eliminar la asociacion.');

            $this->recalcularCostoYRegistrarHistorico($producto, $sucursalId);
            $db->commit();
            return ['exito'=>['Subproducto desasociado del producto principal.']];
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }

    public function setRendimientoReceta(array $datos, int $sucursalId): array{
        $productoId = (int)($datos['id'] ?? 0);
        $rendimiento = $datos['rendimientoestandar'] ?? null;

        if($productoId <= 0 || !is_numeric($rendimiento) || (float)$rendimiento <= 0)
            return ['error'=>['El rendimiento debe ser mayor que cero']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();

        try{
            // Bloquea el producto para serializar los cambios de rendimiento y formula.
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new RuntimeException('El producto no existe.');

            $producto->rendimientoestandar = (float)$rendimiento;
            $this->recalcularCostoYRegistrarHistorico($producto, $sucursalId);

            $db->commit();
            return ['exito'=>[1]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al establecer rendimiento de formula: '.$error->getMessage());
            return ['error'=>['Error al establecer el rendimiento de la formula']];
        }
    }

    /**
     * Debe ejecutarse dentro de una transaccion y con el producto bloqueado.
     */
    private function recalcularCostoYRegistrarHistorico(productos $producto, int $sucursalId): void{
        $rendimiento = (float)$producto->rendimientoestandar;
        if($rendimiento <= 0)throw new RuntimeException('El rendimiento estandar debe ser mayor que cero.');

        $costoFormula = (float)(productos_sub::sumcolum('id_producto', $producto->id, 'costo') ?? 0);
        $producto->precio_compra = $costoFormula / $rendimiento;
        $producto->actualizar();
        (new costosproductos(['sucursalfk_id'=>$sucursalId, 'productofk'=>$producto->id, 'tipocosto'=>0, 'precio_compra'=>$producto->precio_compra]))->crear_guardar();
    }
}
