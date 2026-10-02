<?php

namespace App\Models\inventario;

class traslado_inv extends \App\Models\ActiveRecord{
    protected static $tabla = 'traslado_inv';
    protected static $columnasDB = ['id', 'id_sucursalorigen', 'id_sucursaldestino', 'fkusuario', 'tipo', 'observacion', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_sucursalorigen = $args['id_sucursalorigen']??'';
        $this->id_sucursaldestino = $args['id_sucursaldestino']??'';
        $this->fkusuario = $args['fkusuario']??'';
        $this->tipo = $args['tipo']??'';
        $this->observacion = $args['observacion']??'';
        $this->estado = $args['estado']??'';  //pendiente, entransito, finalizado, rechazada, aprobada
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->id_sucursalorigen)self::$alertas['error'][] = "Sucursla de origen no seleccionada.";
        if(!$this->id_sucursaldestino)self::$alertas['error'][] = "Susucrsal de destino no seleccionada";
        return self::$alertas;
    }

}