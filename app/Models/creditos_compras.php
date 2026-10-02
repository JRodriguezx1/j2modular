<?php

namespace App\Models;

class creditos_compras extends \App\Models\ActiveRecord{
    protected static $tabla = 'creditos_compras';
    protected static $columnasDB = ['id', 'id_sucursal_fk', 'compra_id', 'id_proveedor', 'nombreproveedor', 'montototal', 'saldopendiente', 'cantidadcuotas', 'montocuota', 'frecuenciapago', 'fechainicio', 'tasainteres', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_sucursal_fk = $args['id_sucursal_fk']??'';
        $this->compra_id = $args['compra_id']??'';
        $this->id_proveedor = $args['id_proveedor']??'';
        $this->nombreproveedor = $args['nombreproveedor']??'';
        $this->montototal = $args['montototal']??0;
        $this->saldopendiente = $args['saldopendiente']??0;
        $this->cantidadcuotas = $args['cantidadcuotas']??0;
        $this->montocuota = $args['montocuota']??0;
        $this->frecuenciapago = $args['frecuenciapago']??'';
        $this->fechainicio = $args['fechainicio']??'';
        $this->tasainteres = $args['tasainteres']??0;
        $this->estado = $args['estado']??0;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if(!$this->nombreproveedor)self::$alertas['error'][] = "Nombre del proveedor no especificado.";
        if($this->montototal<=0)self::$alertas['error'][] = "el valor del credito total no es valido.";
        if($this->cantidadcuotas<=0)self::$alertas['error'][] = "Numero de cuotas no es valido, verifica nuevamente.";
        if($this->montocuota<=0)self::$alertas['error'][] = "Valor de la cuota no es valido.";
        if($this->tasainteres<0)self::$alertas['error'][] = "Tasa de interes no puede ser un valor negativo";
        return self::$alertas;
    }

}