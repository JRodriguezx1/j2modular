<?php

namespace App\Models\inventario;

class ordenes_produccion extends \App\Models\ActiveRecord{
    protected static $tabla = 'ordenes_produccion';
    protected static $columnasDB = ['id', 'idsucursal_fk', 'id_productoid', 'nombreproducto', 'cantidadsolicitada', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idsucursal_fk = $args['idsucursal_fk']??'';
        $this->id_productoid = $args['id_productoid']??'';
        $this->nombreproducto = $args['nombreproducto']??'';
        $this->cantidadsolicitada = $args['cantidadsolicitada']??0;
        $this->estado = $args['estado']??'';  //pendiente, en proceso, finalizado
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombreproducto)self::$alertas['error'][] = "Nombre del producto a producir no especificado.";
        if($this->cantidadsolicitada<=0)self::$alertas['error'][] = "Cantidad solicitada no es correcto, debe ser mayor a cero '0'.";
        return self::$alertas;
    }

}