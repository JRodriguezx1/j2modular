<?php

namespace App\Models\inventario;

class conversionunidades extends \App\Models\ActiveRecord{
    protected static $tabla = 'conversionunidades';
    protected static $columnasDB = ['id', 'idproducto', 'idsubproducto', 'idunidadmedidabase', 'idunidadmedidadestino', 'nombreunidadbase', 'nombreunidaddestino', 'factorconversion'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
         $this->idproducto = $args['idproducto']??'';
        $this->idsubproducto = $args['idsubproducto']??'';
        $this->idunidadmedidabase = $args['idunidadmedidabase']??'';
        $this->idunidadmedidadestino = $args['idunidadmedidadestino']??'';
        $this->nombreunidadbase = $args['nombreunidadbase']??'';
        $this->nombreunidaddestino = $args['nombreunidaddestino']??'';
        $this->factorconversion = $args['factorconversion']??'';
    }

    public function validar():array
    {
        if(!$this->idunidadmedidabase)self::$alertas['error'][] = "Error unidad base intenta nuevamnete.";
        if(!$this->idunidadmedidadestino)self::$alertas['error'][] = "Error unidad destino intenta nuevamnete.";
        if(!$this->factorconversion)self::$alertas['error'][] = "Error factor de conversion intenta nuevamnete.";
        return self::$alertas;
    }  
}
?>