<?php

namespace App\Models\suscripcioncuenta;

class suscripcion_pagos {
    
    public function __construct($args = [])
    {
        $this->id = $args['id']??null;
        $this->sucursalfkid = $args['sucursalfkid']??id_sucursal();
        $this->valor_pagado = $args['valor_pagado']??0;
        $this->fecha_pago = $args['fecha_pago']??date('Y-m-d');
        $this->cantidad_plan = $args['cantidad_plan']??1;
        $this->mediopago = $args['mediopago']??'';
        $this->descuento = $args['descuento']??0;
        $this->detalle_descuento = $args['detalle_descuento']?? '';
        $this->cargo = $args['cargo']??0;
        $this->detalle_cargo = $args['detalle_cargo']??'';
        $this->descripcion = $args['descripcion']??'';
        $this->estado = $args['estado']??1;  //1 activo, 0 inactivo.
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->valor_pagado || $this->valor_pagado<0)$alertas['error'][] = "Valor pagado no es valido, verificar nuevamente.";
        if($this->cantidad_plan<=0)$alertas['error'][] = "Cantidad del plan no es valido, verificar nuevamente.";
        if(!$this->mediopago || strlen($this->mediopago)<2)$alertas['error'][] = "Medio de pago no es valido, verificar nuevamente.";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id, 
            'sucursalfkid' => $this->sucursalfkid,
            'valor_pagado' => $this->valor_pagado,
            'fecha_pago' => $this->fecha_pago,
            'cantidad_plan' => $this->cantidad_plan, 
            'mediopago' => $this->mediopago, 
            'descuento' => $this->descuento, 
            'detalle_descuento' => $this->detalle_descuento, 
            'cargo' => $this->cargo,
            'detalle_cargo' => $this->detalle_cargo,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            //'created_at' => $this->created_at
        ];
    }

}