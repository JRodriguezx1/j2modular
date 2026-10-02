<?php

namespace App\Models\inventario;

class lotes_produccion extends \App\Models\ActiveRecord{
    protected static $tabla = 'lotes_produccion';
    protected static $columnasDB = ['id', 'id_ordenproduccion', 'idsucursalfk', 'num_lote', 'cantidadproducida', 'fechaproduccion', 'fechavencimiento'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_ordenproduccion = $args['id_ordenproduccion']??'';
        $this->idsucursalfk = $args['idsucursalfk']??'';
        $this->num_lote = $args['num_lote']??'A1';
        $this->cantidadproducida = $args['cantidadproducida']??0;
        $this->fechaproduccion = $args['fechaproduccion']?? date('Y-m-d');
        $this->fechavencimiento = $args['fechavencimiento']?? date('Y-m-d');
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if($this->cantidadproducida<=0)self::$alertas['error'][] = "La cantidad producida es 0 o menor a 0.";
        return self::$alertas;
    }

}