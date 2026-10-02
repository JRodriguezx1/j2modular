<?php

namespace App\Models\configuraciones;

class bancos extends \App\Models\ActiveRecord{
    protected static $tabla = 'bancos';
    protected static $columnasDB = ['id', 'nombre', 'numerocuenta', 'saldo'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->numerocuenta = $args['numerocuenta']??'';
        $this->saldo = $args['saldo']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Nombre del banco no especificado";
        if(strlen($this->nombre)>28)self::$alertas['error'][] = "Has excecido el limite de caracteres del nombre del banco";
        if(strlen($this->numerocuenta)>15)self::$alertas['error'][] = "Has excecido el limite para el numero de cuenta";
        return self::$alertas;
    }

}