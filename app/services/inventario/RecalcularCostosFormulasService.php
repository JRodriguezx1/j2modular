<?php

namespace App\services\inventario;

use App\Models\inventario\productos;
use App\Models\inventario\productos_sub;
use DomainException;
use RuntimeException;

final class RecalcularCostosFormulasService{
    /**
     * Recibe insumos con las propiedades id y precio_compra.
     * Debe ejecutarse dentro de la transaccion iniciada por el caso de uso.
     *
     * @return productos[] Productos compuestos con su costo recalculado.
     */
    public function ejecutar(array $insumos): array{
        if(empty($insumos))return [];

        // Localiza los productos compuestos afectados por los insumos recibidos.
        $idsInsumos = array_values(array_unique(array_map(static fn(object $insumo): int => (int)$insumo->id, $insumos)));
        $coincidencias = productos_sub::paginarwhere('', '', 'id_subproducto', $idsInsumos);
        if(empty($coincidencias))return [];

        // Bloquea primero los productos y después todas sus formulas.
        $idsProductos = array_values(array_unique(array_map(static fn(object $formula): int => (int)$formula->id_producto, $coincidencias)));
        $productosAfectados = productos::findManyForUpdate($idsProductos);
        if(count($productosAfectados) !== count($idsProductos))
            throw new DomainException('No fue posible obtener todos los productos compuestos.');

        $formulasConsultadas = productos_sub::paginarwhere('', '', 'id_producto', $idsProductos);
        $formulas = productos_sub::findManyForUpdate(array_map(static fn(object $formula): int => (int)$formula->id, $formulasConsultadas));
        if(count($formulas) !== count($formulasConsultadas))
            throw new DomainException('Las formulas cambiaron durante la actualizacion de costos.');

        $costoPorInsumo = array_column($insumos, 'precio_compra', 'id');
        $formulasActualizar = [];
        $costoTotalPorProducto = [];

        // Sustituye los costos recibidos y suma el costo completo de cada formula.
        foreach($formulas as $formula){
            $insumoId = (int)$formula->id_subproducto;
            if(array_key_exists($insumoId, $costoPorInsumo)){
                $costoUnitario = (float)$costoPorInsumo[$insumoId];
                // El metodo del modelo multiplica este valor por cantidadsubproducto.
                $formula->costo = $costoUnitario;
                $formulasActualizar[] = $formula;
                $costoFormula = (float)$formula->cantidadsubproducto * $costoUnitario;
            }else{
                $costoFormula = (float)$formula->costo;
            }

            $productoId = (int)$formula->id_producto;
            $costoTotalPorProducto[$productoId] = ($costoTotalPorProducto[$productoId] ?? 0.0) + $costoFormula;
        }

        if($formulasActualizar && !productos_sub::actualizar_costos_de_prosub($formulasActualizar, ['costo']))
            throw new RuntimeException('No fue posible actualizar los costos de las formulas.');

        // Convierte el costo total de cada formula en costo unitario del producto.
        foreach($productosAfectados as $producto){
            $rendimiento = (float)$producto->rendimientoestandar;
            if($rendimiento <= 0)throw new DomainException('El rendimiento estandar debe ser mayor que cero.');
            $producto->precio_compra = ($costoTotalPorProducto[(int)$producto->id] ?? 0.0) / $rendimiento;
        }

        if(!productos::updatemultiregobj($productosAfectados, ['precio_compra']))
            throw new RuntimeException('No fue posible actualizar el costo de los productos compuestos.');

        return $productosAfectados;
    }
    
}