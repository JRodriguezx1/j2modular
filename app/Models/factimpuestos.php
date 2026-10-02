<?php

namespace App\Models;

class factimpuestos extends \App\Models\ActiveRecord{
    protected static $tabla = 'factimpuestos';
    protected static $columnasDB = ['id', 'id_impuesto', 'facturaid', 'basegravable', 'valorimpuesto'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_impuesto = $args['id_impuesto']??'';
        $this->facturaid = $args['facturaid']??'';
        $this->basegravable = $args['basegravable']??'';
        $this->valorimpuesto = $args['valorimpuesto']??'';
    }


    public function validar():array
    {
        if(!$this->id_impuesto)self::$alertas['error'][] = "Error intenta nuevamnete";
        return self::$alertas;
    }
    
}