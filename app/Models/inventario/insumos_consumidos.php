<?php

namespace App\Models\inventario;

class insumos_consumidos extends \App\Models\ActiveRecord{
    protected static $tabla = 'insumos_consumidos';
    protected static $columnasDB = ['id', 'idordenproduccion', 'subproducto_id', 'id_loteproduccion', 'nombresubproducto', 'cantidadutilizada'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idordenproduccion = $args['idordenproduccion']??'';
        $this->subproducto_id = $args['subproducto_id']??'';
        $this->id_loteproduccion = $args['id_loteproduccion']??'';
        $this->nombresubproducto = $args['nombresubproducto']??'';
        $this->cantidadutilizada = $args['cantidadutilizada']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombresubproducto)self::$alertas['error'][] = "Nombre del insumo no especificado";
        return self::$alertas;
    }

}