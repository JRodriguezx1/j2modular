<?php

namespace App\Models\parametrizacion;

class config_global extends \App\Models\ActiveRecord{
    protected static $tabla = 'config_global';
    protected static $columnasDB = ['id', 'modulo', 'clave', 'valor_default'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->modulo = $args['modulo']??'';
        $this->clave = $args['clave']??'';
        $this->valor_default = $args['valor_default']??'';
        $this->created_at = $args['created_at']??'';
    }


}