<?php

namespace App\Models\creditos;

class tipofinanciacion extends \App\Models\ActiveRecord{
    protected static $tabla = 'tipofinanciacion';
    protected static $columnasDB = ['id', 'nombre', 'genera_interes', 'entrega_inmediata'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->genera_interes = $args['genera_interes']??0;
        $this->entrega_inmediata = $args['entrega_inmediata']??0;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Nombre del tipo de financiacion no puede ir vacio";
        return self::$alertas;
    }

}