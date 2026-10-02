<?php

namespace App\Models;

class compras extends \App\Models\ActiveRecord{
    protected static $tabla = 'compras';
    protected static $columnasDB = ['id', 'id_sucursal_id', 'idusuario', 'idproveedor', 'idformapago', 'idorigencaja', 'idorigenbanco', 'nombreorigencaja', 'nombreorigenbanco', 'nombreusuario', 'nombreproveedor', 'formapago', 'nfactura', 'impuesto', 'valorimp', 'base', 'origenpago', 'nombreorigenpago', 'cantidaditems', 'observacion', 'subtotal', 'valortotal', 'estado', 'fechacompra'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_sucursal_id = $args['id_sucursal_id']??'';
        $this->idusuario = $args['idusuario']??'';
        $this->idproveedor = $args['idproveedor']??1;
        $this->idformapago = $args['idformapago']??1;
        $this->idorigencaja = $args['idorigencaja']??'';
        $this->idorigenbanco = $args['idorigenbanco']??'';
        $this->nombreorigencaja = $args['nombreorigencaja']??'';
        $this->nombreorigenbanco = $args['nombreorigenbanco']??'';
        $this->nombreusuario = $args['nombreusuario']??'';
        $this->nombreproveedor = $args['nombreproveedor']??'';
        $this->formapago = $args['formapago']??'Contado';
        $this->nfactura = $args['nfactura']??'';
        $this->impuesto = $args['impuesto']??0;
        $this->valorimp = $args['valorimp']??0;
        $this->base = $args['base']??0;
        $this->origenpago = $args['origenpago']??0;  //0 = caja,  1 = banco
        $this->nombreorigenpago = $args['nombreorigenpago']??'';
        $this->cantidaditems = $args['cantidaditems']??'';
        $this->observacion = $args['observacion']??'';
        $this->subtotal = $args['subtotal']??0;
        $this->valortotal = $args['valortotal']??0;
        $this->estado = $args['estado']??'';
        $this->fechacompra = $args['fechacompra']??date('Y-m-d H:i:s');
    }


    public function validar():array
    {
        if(!is_numeric($this->impuesto))self::$alertas['error'][] = 'Error en valor del impuesto.';
        if(!is_numeric($this->cantidaditems))self::$alertas['error'][] = 'La cantidad debe ser tipo numero.';
        if(is_numeric($this->cantidaditems)){
            if((float)$this->cantidaditems==0)self::$alertas['error'][] = 'Debe de haber minimo un item para comprar.';
        }
        if(!is_numeric($this->valortotal))self::$alertas['error'][] = 'Error en el valor total de la compra.';
        if(is_numeric($this->valortotal)){
            if((int)$this->valortotal==0)self::$alertas['error'][] = 'Error en el valor total de la compra.';
        }
        if(strlen($this->observacion)>225)self::$alertas['error'][] = 'El campo observaciones no debe superar los 225 caracteres.';
        return self::$alertas;
    }

}

?>