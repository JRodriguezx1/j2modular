<?php

namespace App\Models\inventario;

class productos_sub extends \App\Models\ActiveRecord{
    protected static $tabla = 'productos_sub';
    protected static $columnasDB = ['id', 'id_producto', 'id_subproducto', 'cantidadsubproducto', 'costo', 'grupos_insumos', 'seleccionado', 'permite_aumentar', 'precio_extra'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_producto = $args['id_producto']??'';
        $this->id_subproducto = $args['id_subproducto']??'';
        $this->cantidadsubproducto = $args['cantidadsubproducto']??'';
        $this->costo = $args['costo']??0;
        $this->grupos_insumos = $args['grupos_insumos']??NULL;
        $this->seleccionado = $args['seleccionado']??1;
        $this->permite_aumentar = $args['permite_aumentar']??1;
        $this->precio_extra = $args['precio_extra']??0;
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!$this->id_producto)self::$alertas['error'][] = "Error intenta nuevamnete";
        if(strlen($this->id_subproducto)>31)self::$alertas['error'][] = "Error intenta nuevamnete";
        if(!$this->cantidadsubproducto || !is_numeric($this->cantidadsubproducto))self::$alertas['error'][] = "cantidad del subproducto no especificado";
        if($this->grupos_insumos == 0)$this->grupos_insumos = '';
        return self::$alertas;
    }


    //////// SE UTILIZA ESTE METODO CUANDO SE REALIZA COMPRAS
    public static function actualizar_costos_de_prosub($array=[], $colums=[]):bool
    {
        $query = "UPDATE ".static::$tabla." SET ";          //$coums = ['stock', 'precio_compra',..]
        $in = "";
        $ctrl=1;
        foreach($colums as $idx => $col){
            $query .= $col." = CASE ";
                foreach($array as $index => $value){
                    $query .= "WHEN id = $value->id THEN cantidadsubproducto*{$value->$col} ";
                    if($ctrl){
                        if(array_key_last($array) === $index){
                            $in .= "$value->id";
                        }else{
                            $in .= "$value->id, ";
                        }
                    }
                }
                $ctrl=0;
            if(array_key_last($colums) === $idx){
                $query .= "ELSE $col END WHERE id IN ($in);";
            }else{
                $query .= "ELSE $col END, ";
            }
        }//UPDATE productos_sub SET costo = CASE WHEN id = 2 THEN cantidadsubproducto*20 WHEN id = 3 THEN cantidadsubproducto*100 WHEN id = 13 THEN cantidadsubproducto*100 ELSE costo END WHERE id IN (2, 3, 13);
        $resultado = self::$db->query($query);
        return $resultado;
    }

    
    //////////// SE UTILIZA ESTE METODO CUANDO SE REALIZA UNA VENTA DE PRODUCTOS COMPUESTOS, PARA CALCULUAR LA CANTIDAD DE SUBPRODUCTOS ASOCIADOS AL PRODUCTO COMPUESTO A DESCONTAR
    public static function cantidadSubproductosXventa(array $array=[],  int $sucursalid = 1):array
    {
        $query = "SELECT ps.*, sis.promediostock, CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN ps.id_producto = $value->id THEN ps.cantidadsubproducto*{$value->porcion} ";
            if(array_key_last($array) === $index){
                $in .= "$value->id";
            }else{
                $in .= "$value->id, ";
            }
        }   
        $query .= "ELSE ps.cantidadsubproducto END AS stock FROM ".static::$tabla." as ps LEFT JOIN stockinsumossucursal sis ON sis.subproductoid = ps.id_subproducto AND sis.sucursalid = $sucursalid WHERE ps.id_producto IN ($in);";
        //SELECT *, CASE WHEN id_producto = 1 THEN cantidadsubproducto*3 WHEN id_producto = 2 THEN cantidadsubproducto*2 ELSE cantidadsubproducto END as x FROM productos_sub WHERE id_producto IN (1, 2);
        $arreglo = [];
        if(!empty($array)){
            $resultado = self::$db->query($query);
            while($row = $resultado->fetch_object()){  
                $arreglo[] = $row;
            }
        $resultado->free();  //[{'id':'1', 'costo':'23'}, {}]  cada obj es una fila de la tabla
        }
        return $arreglo;
    }

    /**
     * Obtiene la receta autorizada de varios productos junto con el promedio de
     * stock de la sucursal. Se utiliza para validar en el servidor la
     * configuracion de insumos enviada por el carrito.
     */
    public static function recetasParaVenta(array $idsProductos, int $sucursalid):array
    {
        $idsProductos = array_values(array_unique(array_filter(
            array_map('intval', $idsProductos),
            fn(int $id):bool => $id > 0
        )));

        if(empty($idsProductos))return [];

        $ids = implode(', ', $idsProductos);
        $sql = "SELECT ps.*, p.rendimientoestandar AS rendimiento_producto,
                       sis.promediostock,
                       sp.nombre AS nombre_insumo,
                       gi.tipo AS tipo_variacion
                FROM productos_sub ps
                INNER JOIN productos p ON p.id = ps.id_producto
                INNER JOIN subproductos sp ON sp.id = ps.id_subproducto
                LEFT JOIN grupos_insumos gi ON gi.id = ps.grupos_insumos
                LEFT JOIN stockinsumossucursal sis
                  ON sis.subproductoid = ps.id_subproducto
                 AND sis.sucursalid = $sucursalid
                WHERE ps.id_producto IN ($ids);";

        return self::camposJoinObj($sql);
    }
}
?>
