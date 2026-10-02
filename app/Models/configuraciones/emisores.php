<?php

namespace App\Models\configuraciones;

class emisores extends \App\Models\ActiveRecord{
    protected static $tabla = 'emisores';
    protected static $columnasDB = ['id', 'idsucursal', 'nombre', 'nit', 'datosencabezados', 'telefono', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idsucursal = $args['idsucursal']??1;
        $this->nombre = $args['nombre']??'';
        $this->nit = $args['nit']??'';
        $this->datosencabezados = $args['datosencabezados']??'';
        $this->telefono = $args['telefono']??'';
        $this->estado = $args['estado']??1;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "Nombre del emisor invalido";
        if(strlen($this->nombre)>74)self::$alertas['error'][] = "Has excecido el limite de caracteres del nombre del emisor";
        if(!$this->nit)self::$alertas['error'][] = "Nit del emisor es obligatorio";
        if(strlen($this->nit)>14)self::$alertas['error'][] = "Has excecido el limite de caracteeres para el nit del emisor";
        if(strlen($this->datosencabezados)>500)self::$alertas['error'][] = "Has excecido el limite de caracteres para los encabezados del emisor";
        return self::$alertas;
    }

}