<?php

namespace App\Models\inventario;

class precios_personalizados extends \App\Models\ActiveRecord{
    protected static $tabla = 'precios_personalizados';
    protected static $columnasDB = ['id', 'idproductoid', 'precio', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idproductoid = $args['idproductoid']??'';
        $this->precio = $args['precio']??'';
        $this->estado = $args['estado']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->precio)self::$alertas['error'][] = "Se debe indicar el precio.";
        return self::$alertas;
    }

}