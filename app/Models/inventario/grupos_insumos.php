<?php

namespace App\Models\inventario;

class grupos_insumos extends \App\Models\ActiveRecord{
    protected static $tabla = 'grupos_insumos';
    protected static $columnasDB = ['id', 'nombre', 'minimo', 'maximo', 'tipo', 'es_sistema', 'activo'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->minimo = $args['minimo']??'';
        $this->maximo = $args['maximo']??'';
        $this->tipo = $args['tipo']??0;  //0 = unico, 1 = multiple
        $this->es_sistema = $args['es_sistema']??0;
        $this->activo = $args['activo']??1;
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!$this->nombre)self::$alertas['error'][] = "El nombre de la variacion es obligatorio.";
        if(strlen($this->nombre)>43)self::$alertas['error'][] = "Has alcanzado el limite de caracteres para el nombre del producto.";
        if($this->minimo<0)self::$alertas['error'][] = "El valor minimo no debe ser menor a 0.";
        if($this->maximo<$this->minimo)self::$alertas['error'][] = "El valor maximo no puede ser menor al valor minimo.";
        return self::$alertas;
    }


}