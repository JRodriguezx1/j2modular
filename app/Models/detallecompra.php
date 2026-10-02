<?php

namespace App\Models;

class detallecompra extends \App\Models\ActiveRecord{
    protected static $tabla = 'detallecompra';
    protected static $columnasDB = ['id', 'idcompra', 'iditem', 'idpx', 'idsx', 'tipo', 'nombreitem', 'unidad', 'cantidad', 'factor', 'impuesto', 'valorunidad', 'subtotal', 'valorcompra'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idcompra = $args['idcompra']??'';
        $this->iditem = $args['iditem']??'';
        $this->idpx = $args['idpx']??'';
        $this->idsx = $args['idsx']??'';
        $this->tipo = $args['tipo']??'';
        $this->nombreitem = $args['nombreitem']??'';
        $this->unidad = $args['unidad']??'';
        $this->cantidad = $args['cantidad']??'';
        $this->factor = $args['factor']??'';
        $this->impuesto = $args['impuesto']??'';
        $this->valorunidad = $args['valorunidad']??'';
        $this->subtotal = $args['subtotal']??'';
        $this->valorcompra = $args['valorcompra']??'';
    }


    public function validar():array
    {
        if(!is_numeric($this->cantidad))self::$alertas['error'][] = 'La cantidad del item debe ser tipo numero.';
        if(is_numeric($this->cantidad)){
            if((int)$this->cantidad==0)self::$alertas['error'][] = 'Debe de haber minimo una cantidad del item para comprar.';
        }
        return self::$alertas;
    }

}

?>