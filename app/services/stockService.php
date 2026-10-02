<?php 

namespace App\services;

use App\Models\inventario\movimientos_insumos;
use App\Models\inventario\movimientos_productos;
use stdClass;

//**SERVICIO DE MOVIMIENTOS DE INVENTARIOS

class stockService {

    public static function upDate_movimientoProductos(array $sumarSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
        $movInv = new movimientos_productos;
        //registrar ajuste de movimiento de inventario de productos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($sumarSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->idfksucursal = id_sucursal();
            $obj->idproducto_id = $value->productoid;
            $obj->id_usuarioid = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->productoid];
            $obj->stockanterior = $value->stock;
            $obj->stocknuevo = $obj->cantidad;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
    }


    public static function upStock_movimientoProductos(array $sumarSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
        $movInv = new movimientos_productos;
        //registrar aumento de movimiento de inventario de productos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($sumarSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->idfksucursal = id_sucursal();
            $obj->idproducto_id = $value->productoid;
            $obj->id_usuarioid = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->productoid];
            $obj->stockanterior = $value->stock - $cantidadxitem[$value->productoid];
            $obj->stocknuevo = $value->stock;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
    }

    public static function downStock_movimientoProductos(array $sumarSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
         $movInv = new movimientos_productos;
        //registrar descuento de movimiento de inventario de productos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($sumarSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->idfksucursal = id_sucursal();
            $obj->idproducto_id = $value->productoid;
            $obj->id_usuarioid = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->productoid];
            $obj->stockanterior = $value->stock + $cantidadxitem[$value->productoid];
            $obj->stocknuevo = $value->stock;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
    }


    public static function upDate_movimientoInsumos(array $upDateSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
        $movInv = new movimientos_insumos;
        //registrar ajuste de movimiento de inventario de productos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($upDateSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->fksucursal_id = id_sucursal();
            $obj->id_subproductoid = $value->subproductoid;
            $obj->idusuario_id = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->subproductoid];
            $obj->stockanterior = $value->stock;
            $obj->stocknuevo = $obj->cantidad;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
    }


    public static function upStock_movimientoInsumos(array $sumarSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
         $movInv = new movimientos_insumos;
        //registrar aumento de movimiento de inventario de insumos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($sumarSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->fksucursal_id = id_sucursal();
            $obj->id_subproductoid = $value->subproductoid;
            $obj->idusuario_id = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->subproductoid];
            $obj->stockanterior = $value->stock - $cantidadxitem[$value->subproductoid];
            $obj->stocknuevo = $value->stock;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
    }


    //registrar descuento de movimiento de inventario de insumos de forma masiva
    public static function downStock_movimientoInsumos(array $descontarSubproductos, array $returnInsumos, string $tipo, string $referencia):bool{
        
        $movInv = new movimientos_insumos;
        //registrar descuento de movimiento de inventario de insumos de forma masiva
        $arrayMovInv = [];
        $cantidadxitem = array_column($descontarSubproductos, 'stock', 'id');
        foreach($returnInsumos as $value){
            $obj = new stdClass();
            $obj->fksucursal_id = id_sucursal();
            $obj->id_subproductoid = $value->subproductoid;
            $obj->idusuario_id = $_SESSION['id'];
            $obj->nombreusuario = $_SESSION['nombre'];
            $obj->tipo = $tipo;
            $obj->referencia = $referencia;
            $obj->cantidad = $cantidadxitem[$value->subproductoid];
            $obj->stockanterior = $value->stock + $cantidadxitem[$value->subproductoid];
            $obj->stocknuevo = $value->stock;
            $obj->comentario = $referencia;
            $arrayMovInv[] = $obj;
        }
        $rmov = $movInv->crear_varios_reg_arrayobj($arrayMovInv);
        return $rmov[0];
        
    }
    
}