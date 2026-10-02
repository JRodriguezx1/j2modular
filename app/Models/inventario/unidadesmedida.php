<?php

namespace App\Models\inventario;

class unidadesmedida extends \App\Models\ActiveRecord{
    protected static $tabla = 'unidadesmedida';
    protected static $columnasDB = ['id', 'nombre', 'simbolo', 'editable', 'fechacreacion', 'fechaupdate'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->simbolo = $args['simbolo']??'';
        $this->editable = $args['editable']??1;
        $this->fechacreacion = $args['fechacreacion']??date('Y-m-d H:i:s');
        $this->fechaupdate = $args['fechaupdate']??'';
    }


    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Unidad de medida no especificado";
        if(strlen($this->nombre)>28)self::$alertas['error'][] = "Has excecido el limite de caracteres";
        return self::$alertas;
    }

}

?>