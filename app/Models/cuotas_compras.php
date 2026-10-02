<?php

namespace App\Models;

class cuotas_compras extends \App\Models\ActiveRecord{
    protected static $tabla = 'cuotas_compras';
    protected static $columnasDB = ['id', 'id_creditocompra', 'numerocuota', 'montocuota', 'fechavencimiento', 'fechapagado', 'estado', 'cuotas_compras'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_creditocompra = $args['id_creditocompra']??'';
        $this->numerocuota = $args['numerocuota']??1;
        $this->montocuota = $args['montocuota']??0;
        $this->fechavencimiento = $args['fechavencimiento']??date('Y-m-d');
        $this->fechapagado = $args['fechapagado']?? date('Y-m-d');
        $this->estado = $args['estado']??0;
        $this->cuotas_compras = $args['cuotas_compras']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        if($this->numerocuota<=0)self::$alertas['error'][] = "Numero de cuotas es incorrecto, validar nuevamente";
        if($this->montocuota<=0)self::$alertas['error'][] = "Monto de la cuota no es valido, verificar nuevamente.";
        return self::$alertas;
    }

}