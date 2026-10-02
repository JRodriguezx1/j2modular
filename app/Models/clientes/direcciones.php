<?php

namespace App\Models\clientes;

class direcciones extends \App\Models\ActiveRecord{
    protected static $tabla = 'direcciones';
    protected static $columnasDB = ['id', 'idcliente', 'idtarifa', 'iddepartamento', 'idciudad', 'pais', 'departamento', 'ciudad', 'direccion', 'codigopostal', 'observacion'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idcliente = $args['idcliente']??'';
        $this->idtarifa = $args['idtarifa']??1;
        $this->iddepartamento = $args['iddepartamento']??1;
        $this->idciudad = $args['idciudad']??1;
        $this->pais = $args['pais']??'';
        $this->departamento = $args['departamento']??' ';
        $this->ciudad = $args['ciudad']??' ';
        $this->direccion = $args['direccion']??'Tienda';
        $this->codigopostal = $args['codigopostal']??'';
        $this->observacion = $args['observacion']??'';
        $this->created_at = $args['created_at']??'';
    }

    /** Columnas controladas que los repositorios pueden persistir. */
    public function toArray():array
    {
        return [
            'idcliente'=>$this->idcliente,
            'idtarifa'=>$this->idtarifa,
            'iddepartamento'=>$this->iddepartamento,
            'idciudad'=>$this->idciudad,
            'pais'=>$this->pais,
            'departamento'=>$this->departamento,
            'ciudad'=>$this->ciudad,
            'direccion'=>$this->direccion,
            'codigopostal'=>$this->codigopostal,
            'observacion'=>$this->observacion,
        ];
    }


    public function validarDireccion(){
        //if(!$this->departamento)self::$alertas['error'][] = "departamento no especificado";
        if(strlen($this->departamento)>34)self::$alertas['error'][] = 'Has excecido el limite de caracteres';
        //if(!$this->ciudad)self::$alertas['error'][] = "ciudad no especificada";
        if(strlen($this->ciudad)>34)self::$alertas['error'][] = 'Has excecido el limite de caracteres';
        //if(!$this->direccion)self::$alertas['error'][] = "Direccion no especificada";
        if(isset($this->direccion) && trim($this->direccion) == '')$this->direccion = 'Tienda';
        if(strlen($this->direccion)>72)self::$alertas['error'][] = 'Has excecido el limite de caracteres';
        return self::$alertas;
    }
}

?>
