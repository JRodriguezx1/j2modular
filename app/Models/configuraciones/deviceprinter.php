<?php

namespace App\Models\configuraciones;

class deviceprinter extends \App\Models\ActiveRecord{
    protected static $tabla = 'deviceprinter';
    protected static $columnasDB = ['id', 'nombre', 'nombrecompartido', 'estacion', 'mm', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->nombrecompartido = $args['nombrecompartido']??'';
        $this->estacion = $args['estacion']??1;
        $this->mm = $args['mm']??48;
        $this->estado = $args['estado']??1;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array{
        parent::validar(); //lama a validar de activerecord
        $this->nombre = trim((string)$this->nombre);
        $this->nombrecompartido = trim((string)$this->nombrecompartido);

        if($this->nombre === '')self::$alertas['error'][] = "Nombre de la impresora no especificado";
        if(strlen($this->nombre)>20)self::$alertas['error'][] = "El nombre de la impresora no puede superar los 20 caracteres";
        if($this->nombrecompartido === '')self::$alertas['error'][] = "Nombre compartido de la impresora no especificado";
        if(strlen($this->nombrecompartido)>20)self::$alertas['error'][] = "El nombre compartido no puede superar los 20 caracteres";

        $estacion = filter_var($this->estacion, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>200]]);
        if($estacion === false){
            self::$alertas['error'][] = "La estacion debe ser un numero entre 1 y 200";
        }else{
            $this->estacion = $estacion;
        }

        $mm = filter_var($this->mm, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>150]]);
        if($mm === false){
            self::$alertas['error'][] = "El ancho del papel debe ser un numero entre 1 y 200 mm";
        }else{
            $this->mm = $mm;
        }

        return self::$alertas;
    }

}
