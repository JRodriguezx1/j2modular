<?php

namespace App\services\inventario;

use App\Models\inventario\productos;
use App\Models\inventario\proveedor_costos;
use App\Models\inventario\proveedores;
use App\Models\inventario\subproductos;


final class CostosProveedorConsultaService{

    /** Prepara los artículos y sus costos vigentes para la sucursal indicada. */
    public function obtener(int $sucursalId): array{
        $proveedores = [];
        $nombresProveedores = [];
        foreach(proveedores::all() as $proveedor){
            $id = (int)$proveedor->id;
            $nombre = (string)$proveedor->nombre;
            $proveedores[] = ['id'=>$id, 'name'=>$nombre];
            $nombresProveedores[$id] = $nombre;
        }

        // La clave incluye el tipo porque productos y subproductos pueden tener el mismo ID.
        $productos = productos::whereArray(['visible'=>1, 'tipoproducto'=>0]);
        $articulos = [];
        foreach($productos as $producto){
            $id = (int)$producto->id;
            $articulos["0:$id"] = [
                'id'=>$id,
                'tipoelemento'=>0,
                'code'=>(string)$producto->sku,
                'name'=>(string)$producto->nombre,
                'suppliers'=>[]
            ];
        }
        foreach(subproductos::all() as $insumo){
            $id = (int)$insumo->id;
            $articulos["1:$id"] = [
                'id'=>$id,
                'tipoelemento'=>1,
                'code'=>(string)$insumo->sku,
                'name'=>(string)$insumo->nombre,
                'suppliers'=>[]
            ];
        }

        $costos = proveedor_costos::whereArray(['sucursal_id'=>$sucursalId, 'activo'=>1]);
        foreach($costos as $registro){
            $tipo = (int)$registro->producto_id > 0 ? 0 : 1;
            $id = $tipo === 0 ? (int)$registro->producto_id : (int)$registro->subproducto_id;
            $clave = "$tipo:$id";
            $proveedorId = (int)$registro->proveedor_id;
            if(!isset($articulos[$clave], $nombresProveedores[$proveedorId]))continue;

            $articulos[$clave]['suppliers'][] = [
                'id'=>$proveedorId,
                'name'=>$nombresProveedores[$proveedorId],
                'cost'=>(float)$registro->costo,
                'updated'=>(string)$registro->fecha_actualizacion
            ];
        }

        return ['products'=>array_values($articulos), 'suppliers'=>$proveedores];
    }
}
