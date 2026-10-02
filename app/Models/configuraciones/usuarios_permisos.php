<?php

namespace App\Models\configuraciones;

class usuarios_permisos extends \App\Models\ActiveRecord{
    public $permisos;
    protected static $tabla = 'usuarios_permisos';
    protected static $columnasDB = ['id', 'usuarioid', 'permisoid'];
    protected array $with = ['permisos'];

    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->usuarioid = $args['usuarioid']??'';
        $this->permisoid = $args['permisoid']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function permisos(){
        return permisos::find('id', $this->permisoid)->nombre;
    }

}