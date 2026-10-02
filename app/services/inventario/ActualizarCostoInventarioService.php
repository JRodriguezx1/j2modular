<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\costosinsumos;
use App\Models\inventario\costosproductos;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use RuntimeException;
use Throwable;

final class ActualizarCostoInventarioService{

    public function ejecutar(array $datos, int $sucursalId): array
    {
        $tipoElemento = (int)($datos['tipoelemento'] ?? 0);
        $elementoId = (int)($datos['idelemento'] ?? 0);
        if($elementoId <= 0 || !isset($datos['precio_compra']) || !is_numeric($datos['precio_compra']))
            return ['error'=>['Error, intenta nuevamente']];

        $precioCompra = (float)$datos['precio_compra'];
        $db = ActiveRecord::getDB();
        $db->begin_transaction();

        try{
            if($tipoElemento === 1){
                // Mantiene el mismo orden de bloqueos que el registro de compras:
                // productos compuestos, formulas y finalmente el insumo.
                $insumo = subproductos::find('id', $elementoId);
                if(!$insumo)throw new RuntimeException('El elemento seleccionado no existe.');
                $insumo->precio_compra = $precioCompra;
                $productosAfectados = (new RecalcularCostosFormulasService())->ejecutar([$insumo]);

                $insumo = subproductos::findForUpdate('id', $elementoId);
                if(!$insumo)throw new RuntimeException('El elemento seleccionado no existe.');
                $insumo->precio_compra = $precioCompra;
                $insumo->actualizar();
                $this->registrarHistoricosDelInsumo($insumo, $productosAfectados, $sucursalId);
            }else{
                $producto = productos::findForUpdate('id', $elementoId);
                if(!$producto)throw new RuntimeException('El elemento seleccionado no existe.');
                $producto->precio_compra = $precioCompra;
                $producto->actualizar();

                // Registra el ajuste manual del costo del producto simple.
                (new costosproductos(['sucursalfk_id'=>$sucursalId, 'productofk'=>$producto->id, 'tipocosto'=>0, 'precio_compra'=>$producto->precio_compra]))->crear_guardar();
            }
            $db->commit();
            return ['exito'=>[1]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al actualizar costos de inventario: '.$error->getMessage());
            return ['error'=>['Error, intenta nuevamente']];
        }
    }

    private function registrarHistoricosDelInsumo(subproductos $insumo, array $productosAfectados, int $sucursalId): void{
        // Guarda el historico del ajuste realizado al insumo.
        (new costosinsumos(['sucursal_fkid'=>$sucursalId, 'idsubproductoid'=>$insumo->id, 'precio_compra'=>$insumo->precio_compra]))->crear_guardar();
        if(empty($productosAfectados))return;
        // Inserta en conjunto el historico de los productos compuestos recalculados.
        $historicos = array_map(
            static fn(productos $producto): costosproductos => new costosproductos([
                'sucursalfk_id'=>$sucursalId,
                'productofk'=>$producto->id,
                'tipocosto'=>0,
                'precio_compra'=>$producto->precio_compra
            ]),
            $productosAfectados
        );
        (new costosproductos())->crear_varios_reg_arrayobj($historicos);
    }
}
