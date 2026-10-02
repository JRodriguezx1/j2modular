<?php

namespace App\Models\inventario;

class costosinsumos extends \App\Models\ActiveRecord{
    protected static $tabla = 'costosinsumos';
    protected static $columnasDB = ['id', 'sucursal_fkid', 'idsubproductoid', 'tipocosto', 'precio_compra', 'fechaajuste'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->sucursal_fkid = $args['sucursal_fkid']??id_sucursal();
        $this->idsubproductoid = $args['idsubproductoid']??'';
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