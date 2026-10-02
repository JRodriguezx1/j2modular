<?php

namespace App\Models\parametrizacion;

class monedas extends \App\Models\ActiveRecord {
    protected static $tabla = 'monedas';
    protected static $columnasDB = ['id', 'nombre', 'codigo', 'simbolo', 'activo'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? NULL;
        $this->codigo = $args['codigo'] ?? NULL;
        $this->simbolo = $args['simbolo']??NULL;
        $this->activo = $args['activo']??1;
    }

    
    public function validar():array{

        if(!$this->nombre)
            self::$alertas['error'][] = 'El nombre es Obligatorio';
        if(!$this->codigo)
            self::$alertas['error'][] = 'El codigo es Obligatorio';
        if(!$this->simbolo)
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        
        return self::$alertas;
    }

}