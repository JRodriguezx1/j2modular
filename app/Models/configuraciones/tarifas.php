<?php

namespace App\Models\configuraciones;

class tarifas extends \App\Models\ActiveRecord{
    protected static $tabla = 'tarifas';
    protected static $columnasDB = ['id', 'nombre', 'valor'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->valor = $args['valor']??'';
    }

    public function validar(){
        if(!$this->nombre)self::$alertas['error'][] = "Nombre de tarifa no ingresada";
        if(strlen($this->nombre)>22)self::$alertas['error'][] = "Nombre de tarifa muy extenso";
        if(!$this->valor)self::$alertas['error'][] = "Valor de la tarifa no ingresada";
        if(!is_numeric($this->valor))self::$alertas['error'][] = 'Valor de la tarifa es incorrecto debe ser numerico';
        return self::$alertas;
    }
}

?>