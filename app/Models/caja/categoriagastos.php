<?php

namespace App\Models\caja;

class categoriagastos extends \App\Models\ActiveRecord {
    
    protected static $tabla = 'categoriagastos';
    protected static $columnasDB = ['id', 'nombre'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }

    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Nombre no especificado";
        if(strlen($this->nombre)>24)self::$alertas['error'][] = "Nombre del gasto muy extenso";
        return self::$alertas;
    }
}