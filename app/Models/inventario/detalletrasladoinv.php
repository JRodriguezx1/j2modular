<?php

namespace App\Models\inventario;

class detalletrasladoinv extends \App\Models\ActiveRecord{
    protected static $tabla = 'detalletrasladoinv';
    protected static $columnasDB = ['id', 'id_trasladoinv', 'fkproducto', 'idsubproducto_id', 'cantidad', 'cantidadrecibida', 'cantidadrechazada'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_trasladoinv = $args['id_trasladoinv']??'';
        $this->fkproducto = $args['fkproducto']??'';
        $this->idsubproducto_id = $args['idsubproducto_id']??'';
        $this->cantidad = $args['cantidad']??0;
        $this->cantidadrecibida = $args['cantidadrecibida']??0;
        $this->cantidadrechazada = $args['cantidadrechazada']??0;
    }


    public function validar():array
    {
        if($this->cantidad<=0)self::$alertas['error'][] = "Cantidad solicitada no es correcto, debe ser mayor a cero '0'.";
        return self::$alertas;
    }

}