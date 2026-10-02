<?php

namespace App\services\inventario;

use App\Models\inventario\categorias;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;

/**
 * Consultas y composicion de datos para el panel principal del inventario.
 */
final class PanelInventarioService{

    public function obtenerPanel(int $sucursalId): array{
        $productos = stockproductossucursal::indicadoresAllProductsXSucursal($sucursalId);
        $subproductos = stockinsumossucursal::indicadoresAllSubproductsXSucursal($sucursalId);

        $indicadoresProductos = $productos[0] ?? null;
        $indicadoresInsumos = $subproductos[0] ?? null;
        $valorInventario = (float)($indicadoresProductos?->valorinv ?? 0)
            + (float)($indicadoresInsumos?->valorinv ?? 0);

        return [
            'proveedores'=>proveedores::all(),
            'productos'=>$productos,
            'subproductos'=>$subproductos,
            'valorInv'=>$this->formatearValorInventario($valorInventario),
            'cantidadProductos'=>(float)($indicadoresProductos?->cantidadproductos ?? 0),
            'cantidadReferencias'=>(int)($indicadoresProductos?->cantidadreferencias ?? 0)
                + (int)($indicadoresInsumos?->cantidadreferencias ?? 0),
            'cantidadCategorias'=>(int)categorias::numreg_where('visible', 1),
            'bajoStock'=>(int)($indicadoresProductos?->bajostock ?? 0)
                + (int)($indicadoresInsumos?->bajostock ?? 0),
            'productosAgotados'=>(int)($indicadoresProductos?->productosagotados ?? 0)
                + (int)($indicadoresInsumos?->productosagotados ?? 0)
        ];
    }

    public function obtenerItemsBajoStock(int $sucursalId): array{
        $productos = stockproductossucursal::getProductosBajoStock($sucursalId);
        foreach($productos as $producto)
            $producto->tipoitem = (int)$producto->tipoproducto === 1 ? 'Compuesto' : 'Simple';

        $insumos = stockinsumossucursal::getInsumosBajoStock($sucursalId);
        foreach($insumos as $insumo){
            // Conserva el campo consumido actualmente por la tabla del frontend.
            $insumo->productoid = $insumo->subproductoid;
            $insumo->tipoitem = 'Insumo';
        }
        return array_merge($productos, $insumos);
    }

    public function obtenerStockPorSucursal(): array{
        return array_merge(
            stockinsumossucursal::getStockinsumosXsucursal(),
            stockproductossucursal::getStockproductosXsucursal()
        );
    }

    private function formatearValorInventario(float $valor): float|string{
        if($valor >= 1000000000)return round($valor / 1000000000, 2).'MM';
        if($valor >= 1000000)return round($valor / 1000000, 2).'M';
        return $valor;
    }
}
