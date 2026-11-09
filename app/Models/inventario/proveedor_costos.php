<?php

namespace App\Models\inventario;

class proveedor_costos extends \App\Models\ActiveRecord{
    protected static $tabla = 'proveedor_costos';
    protected static $columnasDB = ['id', 'sucursal_id', 'proveedor_id', 'producto_id', 'subproducto_id', 'costo', 'fecha_actualizacion', 'activo'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->sucursal_id = $args['sucursal_id']??id_sucursal();
        $this->proveedor_id = $args['proveedor_id']??'';
        $this->producto_id = $args['producto_id']??NULL;
        $this->subproducto_id = $args['subproducto_id']??NULL;
        $this->costo = $args['costo']??0;
        $this->fecha_actualizacion = $args['fecha_actualizacion']??date('Y-m-d H:i:s');
        $this->activo = $args['activo']??1;
    }


    public function validar(): array{
        if(!is_numeric($this->costo))self::$alertas['error'][] = "Costo no es correcto, verifica nuevamente.";
        return self::$alertas;
    }

}

?>