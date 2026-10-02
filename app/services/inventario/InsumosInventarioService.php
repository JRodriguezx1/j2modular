<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\conversionunidades;
use App\Models\inventario\costosinsumos;
use App\Models\inventario\costosproductos;
use App\Models\inventario\productos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\subproductos;
use App\Models\inventario\unidadesmedida;
use App\Models\sucursales;
use RuntimeException;
use Throwable;

final class InsumosInventarioService{
    ////////  crear insumo-subproducto  //////////
    public function crearInsumo(array $datos, int $sucursalId): array{
        $subproducto = new subproductos($datos);
        // La unidad solamente se consulta para validar su existencia y copiar su nombre.
        $unidadmedida = unidadesmedida::find('id', (int)$subproducto->id_unidadmedida);
        if(!$unidadmedida)return $this->respuesta(['error'=>['La unidad de medida no existe.']], $subproducto);

        $subproducto->unidadmedida = $unidadmedida->nombre;
        $alertas = $subproducto->validar_nuevo_subproducto();
        if(!empty($alertas))return $this->respuesta($alertas, $subproducto);

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $resultadoSubproducto = $subproducto->crear_guardar();
            $subproducto->id = (int)$resultadoSubproducto[1];

            // Crea en una sola consulta las conversiones de la unidad base.
            $equivalencias = $subproducto->equivalencias($subproducto->id, (int)$unidadmedida->id);
            if(empty($equivalencias))
                throw new RuntimeException('No fue posible generar las conversiones del insumo.');
            (new conversionunidades())->crear_varios_reg_arrayobj($equivalencias);

            // Crea en una sola consulta el inventario inicial de todas las sucursales.
            $stocksSucursal = $this->construirStocksSucursal($subproducto, sucursales::all(), $sucursalId);
            $resultadoStocks = (new stockinsumossucursal())->crear_varios_reg($stocksSucursal);
            if(!$resultadoStocks[0])
                throw new RuntimeException('No fue posible crear el inventario por sucursal.');

            (new costosinsumos([
                'sucursal_fkid'=>$sucursalId,
                'idsubproductoid'=>$subproducto->id,
                'tipocosto'=>0,
                'precio_compra'=>$subproducto->precio_compra
            ]))->crear_guardar();

            $db->commit();
            return $this->respuesta(['exito'=>['Insumo creado correctamente']], $subproducto);
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al crear insumo: '.$error->getMessage());
            return $this->respuesta(['error'=>['Error, intentalo nuevamente']], $subproducto);
        }
    }

    private function construirStocksSucursal(subproductos $subproducto, array $sucursales, int $sucursalId): array{
        if(empty($sucursales))
            throw new RuntimeException('No existen sucursales para configurar el inventario.');
        $stocks = [];
        $sucursalActualEncontrada = false;
        foreach($sucursales as $sucursal){
            $esSucursalActual = (int)$sucursal->id === $sucursalId;
            if($esSucursalActual)$sucursalActualEncontrada = true;
            $stocks[] = [
                'subproductoid'=>$subproducto->id,
                'sucursalid'=>$sucursal->id,
                'stock'=>$esSucursalActual ? $subproducto->stock : 0,
                'stockminimo'=>$esSucursalActual ? $subproducto->stockminimo : 0,
                'stockaux'=>0,
                'promediostock'=>1
            ];
        }
        if(!$sucursalActualEncontrada)throw new RuntimeException('La sucursal actual no esta configurada.');
        return $stocks;
    }

    private function respuesta(array $alertas, subproductos $subproducto): array{
        return ['alertas'=>$alertas, 'subproducto'=>$subproducto];
    }

    
    ///////// actualizar insumo-subproducto /////////
    public function actualizarInsumo(array $datos, int $sucursalId): array{
        $subproductoId = (int)($datos['id'] ?? 0);
        if($subproductoId <= 0 || !isset($datos['precio_compra']) || !is_numeric($datos['precio_compra']))
            return ['error'=>['Error, intenta nuevamente']];

        $subproductoValidar = new subproductos($datos);
        $alertas = $subproductoValidar->validar_nuevo_subproducto();
        if(!empty($alertas))return $alertas;
        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // Esta lectura no bloqueante permite decidir si es necesario bloquear formulas.
            $subproductoConsultado = subproductos::find('id', $subproductoId);
            if(!$subproductoConsultado)throw new RuntimeException('El insumo no existe.');

            $costoLeido = (float)$subproductoConsultado->precio_compra;
            $costoSolicitado = (float)$datos['precio_compra'];
            $cambioCosto = $costoLeido != $costoSolicitado;
            $productosAfectados = [];

            // Conserva el orden global: productos, formulas y finalmente el insumo.
            if($cambioCosto){
                $insumoCosto = (object)['id'=>$subproductoId, 'precio_compra'=>$costoSolicitado];
                $productosAfectados = (new RecalcularCostosFormulasService())->ejecutar([$insumoCosto]);
            }

            $subproducto = subproductos::findForUpdate('id', $subproductoId);
            if(!$subproducto)throw new RuntimeException('El insumo no existe.');
            if((float)$subproducto->precio_compra != $costoLeido)
                throw new RuntimeException('El costo del insumo cambio durante la actualizacion.');

            $unidadAnterior = (int)$subproducto->id_unidadmedida;
            $subproducto->compara_objetobd_post($datos);
            $subproducto->precio_compra = $costoSolicitado;
            $subproducto->actualizar();

            if($cambioCosto)$this->registrarHistoricos($subproducto, $productosAfectados, $sucursalId);

            $stockInsumo = stockinsumossucursal::uniquewhereArrayForUpdate(['subproductoid'=>$subproducto->id, 'sucursalid'=>$sucursalId]);
            if(!$stockInsumo)
                throw new RuntimeException('El insumo no tiene inventario configurado en esta sucursal.');
            $stockInsumo->stockminimo = $subproducto->stockminimo;
            $stockInsumo->actualizar();
            $this->sincronizarConversiones($subproducto, $unidadAnterior);
            $db->commit();
            return ['exito'=>['Datos del sub-producto actualizados'], 'subproducto'=>[$subproducto]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al actualizar insumo: '.$error->getMessage());
            return ['error'=>['Error, intenta nuevamente']];
        }
    }


    private function registrarHistoricos(subproductos|null $subproducto, array $productosAfectados, int $sucursalId): void{
        if($subproducto)
            (new costosinsumos([
                'sucursal_fkid'=>$sucursalId,
                'idsubproductoid'=>$subproducto->id,
                'tipocosto'=>0,
                'precio_compra'=>$subproducto->precio_compra
            ]))->crear_guardar();

        if(empty($productosAfectados))return;
        $historicosProductos = array_map(
            static fn(productos $producto): costosproductos => new costosproductos([
                'sucursalfk_id'=>$sucursalId,
                'productofk'=>$producto->id,
                'tipocosto'=>0,
                'precio_compra'=>$producto->precio_compra
            ]),
            $productosAfectados
        );
        (new costosproductos())->crear_varios_reg_arrayobj($historicosProductos);
    }


    private function sincronizarConversiones(subproductos $subproducto,  int $unidadAnterior): void{
        if($unidadAnterior === (int)$subproducto->id_unidadmedida)return;

        $conversionesDB = conversionunidades::idregistrosForUpdate('idsubproducto', (int)$subproducto->id);
        $equivalencias = $subproducto->equivalencias((int)$subproducto->id, (int)$subproducto->id_unidadmedida);
        if(empty($equivalencias))
            throw new RuntimeException('No fue posible generar las conversiones de la nueva unidad.');
        if($conversionesDB && !conversionunidades::eliminar_idregistros('idsubproducto', [$subproducto->id]))
            throw new RuntimeException('No fue posible eliminar las conversiones anteriores.');

        (new conversionunidades())->crear_varios_reg_arrayobj($equivalencias);
    }


    //Eliminar insumo-subproducto
    public function eliminarInsumo(array $datos, int $sucursalId): array{
        $subproductoId = (int)($datos['id'] ?? 0);
        if($subproductoId <= 0)return ['error'=>['Error, intenta nuevamente']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // Calcula cada formula como si el insumo ya no hiciera parte de ella.
            $insumoSinCosto = (object)['id'=>$subproductoId, 'precio_compra'=>0];
            $productosAfectados = (new RecalcularCostosFormulasService())->ejecutar([$insumoSinCosto]);

            // Mantiene el orden global: productos, formulas y finalmente el insumo.
            $subproducto = subproductos::findForUpdate('id', $subproductoId);
            if(!$subproducto)throw new RuntimeException('El insumo no existe.');

            // Impide eliminar con una asociacion concurrente que no haya sido recalculada.
            $formulasActuales = productos_sub::idregistrosForUpdate('id_subproducto', $subproductoId);
            
            $idsProductosRecalculados = $this->idsProductos($productosAfectados);
            $idsProductosActuales = $this->idsProductosDeFormulas($formulasActuales);
            if($idsProductosRecalculados !== $idsProductosActuales)
                throw new RuntimeException('Las formulas cambiaron durante la eliminacion del insumo.');

            $this->registrarHistoricos(null, $productosAfectados, $sucursalId);
            $subproducto->eliminar_registro();
            $db->commit();
            return ['exito'=>['Subproducto eliminado.']];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al eliminar insumo: '.$error->getMessage());
            return ['error'=>['Error durante el proceso, intenta nuevamente.']];
        }
    }

    /** @return int[] */
    private function idsProductos(array $productos): array{
        $ids = array_values(array_unique(array_map(
            static fn(productos $producto): int => (int)$producto->id, $productos
        )));
        sort($ids, SORT_NUMERIC);
        return $ids;
    }

    /** @return int[] */
    private function idsProductosDeFormulas(array $formulas): array{
        $ids = array_values(array_unique(array_map(
            static fn(productos_sub $formula): int => (int)$formula->id_producto, $formulas
        )));
        sort($ids, SORT_NUMERIC);
        return $ids;
    }

    /*private function registrarHistoricos(array $productosAfectados, int $sucursalId): void{
        if(empty($productosAfectados))return;
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
    }*/

}