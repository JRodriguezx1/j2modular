<?php

namespace App\Models\clientes;

class municipalities extends \App\Models\ActiveRecord{
    protected static $tabla = 'municipalities';
    protected static $columnasDB = ['id', 'department_id', 'name', 'code', 'updated_at', 'codefacturador'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->department_id = $args['department_id']??'';
        $this->name = $args['name']??'';
        $this->code = $args['code']??'';
        $this->created_at = $args['created_at']??'';
        $this->updated_at = $args['updated_at']??'';
        $this->codefacturador = $args['codefacturador']??'';
    }


}