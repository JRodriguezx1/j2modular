<?php

namespace App\Models\inventario;

class costosproductos extends \App\Models\ActiveRecord{
    protected static $tabla = 'costosproductos';
    protected static $columnasDB = ['id', 'sucursalfk_id', 'productofk', 'tipocosto', 'precio_compra', 'fechaajuste'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->sucursalfk_id = $args['sucursalfk_id']??id_sucursal();
        $this->productofk = $args['productofk']??'';
        $this->tipocosto = $args['tipocosto']??0;  // 0 = ajuste, 1 = compra
        $this->precio_compra = $args['precio_compra']??0;
        $this->fechaajuste = $args['fechaajuste']??date('Y-m-d H:i:s');
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!is_numeric($this->precio_compra))self::$alertas['error'][] = "Costo no es correcto, verifica nuevamente.";
        return self::$alertas;
    }

}

?>