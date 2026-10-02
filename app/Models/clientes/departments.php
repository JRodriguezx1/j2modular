<?php

namespace App\Models\clientes;

class departments extends \App\Models\ActiveRecord{
    protected static $tabla = 'departments';
    protected static $columnasDB = ['id', 'country_id', 'name', 'code', 'updated_at'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->country_id = $args['country_id']??'';
        $this->name = $args['name']??'';
        $this->code = $args['code']??'';
        $this->created_at = $args['created_at']??'';
        $this->updated_at = $args['updated_at']??'';
    }


}