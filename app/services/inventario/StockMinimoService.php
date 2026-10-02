<?php

namespace App\services\inventario;

use App\Models\inventario\productos;
use App\Models\inventario\subproductos;

/**
 * Detecta los articulos que acaban de cruzar su stock minimo.
 *
 * Este servicio no modifica inventario ni envia notificaciones. Debe llamarse
 * inmediatamente despues de aplicar un descuento, mientras el movimiento que
 * produjo el nuevo stock todavia esta disponible.
 */
final class StockMinimoService{
    /**
     * Recibe el mismo inventario preparado por ventasService y devuelve una
     * lista uniforme de productos e insumos que llegaron al stock minimo.
     */
    public function detectarCrucesDespuesDeDescuento(array $inventario, array $stockActualProductos, array $stockActualInsumos, int $sucursalId): array{
        if($sucursalId <= 0)return [];
        return array_merge(
            $this->detectarProductos($inventario['productosSimples'] ?? [], $stockActualProductos, $sucursalId),
            $this->detectarInsumos($inventario['insumos'] ?? [], $stockActualInsumos, $sucursalId)
        );
    }

    private function detectarProductos(array $movimientos, array $stocks, int $sucursalId): array{
        $cantidades = $this->cantidadesPorId($movimientos);
        if(empty($cantidades))return [];

        // Las existencias y los datos maestros se consultan en bloque para no
        // generar una consulta adicional por cada producto vendido.
        $datosProductos = productos::IN_Where('id', array_keys($cantidades));
        $productosPorId = array_column($datosProductos, null, 'id');

        $alertas = [];
        foreach($stocks as $stock){
            $id = (int)$stock->productoid;
            $cantidadDescontada = $cantidades[$id] ?? 0.0;
            if($cantidadDescontada <= 0)continue;

            $stockActual = (float)$stock->stock;
            $stockMinimo = (float)$stock->stockminimo;
            $stockAnterior = $stockActual + $cantidadDescontada;

            // Solo alerta cuando este movimiento cruza el limite. Si el
            // producto ya estaba bajo, una venta posterior no repite el aviso.
            if($stockAnterior <= $stockMinimo || $stockActual > $stockMinimo)continue;

            $producto = $productosPorId[$id] ?? null;
            $alertas[] = (object)[
                'tipo'=>'Producto',
                'id'=>$id,
                'nombre'=>(string)($producto?->nombre ?? ''),
                'sku'=>(string)($producto?->sku ?? ''),
                'unidadmedida'=>(string)($producto?->unidadmedida ?? ''),
                'stock_anterior'=>$stockAnterior,
                'stock_actual'=>$stockActual,
                'stock_minimo'=>$stockMinimo,
            ];
        }
        return $alertas;
    }

    private function detectarInsumos(array $movimientos, array $stocks, int $sucursalId): array{
        $cantidades = $this->cantidadesPorId($movimientos);
        if(empty($cantidades))return [];

        $datosInsumos = subproductos::IN_Where('id', array_keys($cantidades));
        $insumosPorId = array_column($datosInsumos, null, 'id');

        $alertas = [];
        foreach($stocks as $stock){
            $id = (int)$stock->subproductoid;
            $cantidadDescontada = $cantidades[$id] ?? 0.0;
            if($cantidadDescontada <= 0)continue;

            $stockActual = (float)$stock->stock;
            $stockMinimo = (float)$stock->stockminimo;
            $stockAnterior = $stockActual + $cantidadDescontada;

            if($stockAnterior <= $stockMinimo || $stockActual > $stockMinimo)continue;

            $insumo = $insumosPorId[$id] ?? null;
            $alertas[] = (object)[
                'tipo'=>'Insumo',
                'id'=>$id,
                'nombre'=>(string)($insumo?->nombre ?? ''),
                'sku'=>(string)($insumo?->sku ?? ''),
                'unidadmedida'=>(string)($insumo?->unidadmedida ?? ''),
                'stock_anterior'=>$stockAnterior,
                'stock_actual'=>$stockActual,
                'stock_minimo'=>$stockMinimo,
            ];
        }
        return $alertas;
    }

    /** Suma los descuentos repetidos del mismo producto o insumo. */
    private function cantidadesPorId(array $movimientos): array{
        $cantidades = [];
        foreach($movimientos as $movimiento){
            if(!is_object($movimiento))continue;
            $id = (int)($movimiento->id ?? 0);
            $cantidad = (float)($movimiento->stock ?? 0);
            if($id <= 0 || $cantidad <= 0)continue;
            $cantidades[$id] = ($cantidades[$id] ?? 0.0) + $cantidad;
        }
        return $cantidades;
    }

}