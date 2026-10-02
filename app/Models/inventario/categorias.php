<?php

namespace App\Models\inventario;

class categorias extends \App\Models\ActiveRecord {
    protected static $tabla = 'categorias';
    protected static $columnasDB = ['id', 'nombre', 'codigo', 'totalproductos', 'foto', 'fechacreacion', 'visible'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->codigo = $args['codigo'] ?? '';
        $this->totalproductos = $args['totalproductos'] ?? '';
        $this->foto = $args['foto'] ?? '';
        $this->fechacreacion = $args['fechacreacion'] ?? date('Y-m-d');
        $this->visible = $args['visible']??1;
    }

    // Validación para categorias nuevas
    public function validar_nueva_categoria() {
        if(!$this->nombre)self::$alertas['error'][] = 'El Nombre de la categoria es Obligatorio';
        
        if(strlen($this->nombre)>32)self::$alertas['error'][] = 'El Nombre de la categoria no debe superar los 25 caracteres';
        
        if($this->codigo) {
            if(strlen($this->codigo)>15)self::$alertas['error'][] = 'El codigo no debe superar los 15 caracteres';
        }
        return self::$alertas;
    }

    
}