<?php

namespace App\Models\inventario;

class movimientos_insumos extends \App\Models\ActiveRecord{
    protected static $tabla = 'movimientos_insumos';
    protected static $columnasDB = ['id', 'fksucursal_id', 'id_subproductoid', 'idusuario_id', 'nombreusuario', 'tipo', 'referencia', 'cantidad', 'stockanterior', 'stocknuevo', 'comentario'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->fksucursal_id = $args['fksucursal_id']??id_sucursal();
        $this->id_subproductoid = $args['id_subproductoid']??'';
        $this->idusuario_id = $args['idusuario_id']??'';
        $this->nombreusuario = $args['nombreusuario']??'';
        $this->tipo = $args['tipo']??'';
        $this->referencia = $args['referencia']??'';
        $this->cantidad = $args['cantidad']??'';
        $this->stockanterior = $args['stockanterior']??'';
        $this->stocknuevo = $args['stocknuevo']??'';
        $this->comentario = $args['comentario']??'';
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!$this->fksucursal_id)self::$alertas['error'][] = "Sucursal no especificado";
        if(!$this->id_subproductoid)self::$alertas['error'][] = "id del producto no especificado";
        if(!$this->idusuario_id)self::$alertas['error'][] = "usuario no especificado";
        if(!$this->tipo)self::$alertas['error'][] = "Tipo de movimiento no especificado";
        return self::$alertas;
    }

}