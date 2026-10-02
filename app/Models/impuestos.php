<?php

namespace App\Models;

class impuestos extends \App\Models\ActiveRecord{
    protected static $tabla = 'impuestos';
    protected static $columnasDB = ['id', 'nombre'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
    }


    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Impuesto no especificado";
        if(strlen($this->nombre)>50)self::$alertas['error'][] = "Nombre de impuesto muy extenso";
        return self::$alertas;
    }

}