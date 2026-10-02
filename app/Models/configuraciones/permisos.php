<?php

namespace App\Models\configuraciones;

class permisos extends \App\Models\ActiveRecord{
    protected static $tabla = 'permisos';
    protected static $columnasDB = ['id', 'nombre', 'modulo', 'descripcion'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->modulo = $args['modulo']??'';
        $this->descripcion = $args['descripcion']??'';
        $this->created_at = $args['created_at']??'';
    }
}