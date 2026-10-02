<?php

namespace App\Models\inventario;

class proveedores extends \App\Models\ActiveRecord {
    protected static $tabla = 'proveedores';
    protected static $columnasDB = ['id', 'nit', 'nombre', 'telefono', 'email', 'direccion', 'pais', 'ciudad'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nit = $args['nit'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->direccion = $args['direccion']??'';
        $this->pais = $args['pais']??'';
        $this->ciudad = $args['ciudad']??'';
        $this->created_at = $args['created_at']??'';
    }

    // Validación para categorias nuevas
    public function validar_nueva_categoria() {
        if(!$this->nombre)self::$alertas['error'][] = 'El Nombre del proveedor es Obligatorio';
        
        if(strlen($this->nombre)>25)self::$alertas['error'][] = 'El Nombre del proveedor no debe superar los 25 caracteres';
        
        if($this->nit) {
            if(strlen($this->nit)>15)self::$alertas['error'][] = 'El nit no debe superar los 15 caracteres';
        }
        return self::$alertas;
    }

    
}