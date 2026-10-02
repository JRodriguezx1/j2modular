<?php
namespace App\Models\clientes;

class preciosporcliente extends \App\Models\ActiveRecord {
    protected static $tabla = 'preciosporcliente';
    protected static $columnasDB = ['id', 'idcliente', 'idproducto', 'precioxcliente', 'estado'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idcliente = $args['idcliente'] ?? '';
        $this->idproducto = $args['idproducto']??' ';
        $this->precioxcliente = $args['precioxcliente'] ?? 0;
        $this->estado = $args['estado'] ?? 1;
        $this->created_at = $args['created_at']??'';
    }

    /** Columnas controladas que los repositorios pueden persistir. */
    public function toArray():array
    {
        return [
            'idcliente'=>$this->idcliente,
            'idproducto'=>$this->idproducto,
            'precioxcliente'=>$this->precioxcliente,
            'estado'=>$this->estado,
        ];
    }

    // Validación para clientes nuevos
    public function validar_nuevo_cliente():array {
        if(!$this->precioxcliente)self::$alertas['error'][] = 'Precio personalizado no especificado';
        if($this->precioxcliente<0)self::$alertas['error'][] = 'Precio no debe ser menor a "0"';
        return self::$alertas;
    }
    
}
