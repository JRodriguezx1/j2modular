<?php

namespace App\Models\configuraciones;

class notificacionesws extends \App\Models\ActiveRecord{
    protected static $tabla = 'notificacionesws';
    protected static $columnasDB = ['id', 'sucursal_idfk', 'nombre', 'movil', 'chatid', 'tipo', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->sucursal_idfk = id_sucursal();
        $this->nombre = $args['nombre']??'';
        $this->movil = $args['movil']??'';
        $this->chatid = $args['chatid']??$this->movil.'@c.us';
        $this->tipo = $args['tipo']??'';
        $this->estado = $args['estado']??1;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombre || strlen($this->nombre)>56)self::$alertas['error'][] = "Nombre del contacto no es valido";
        if(strlen($this->movil)>12)self::$alertas['error'][] = "El numero del movil no es compatible";
        return self::$alertas;
    }

}