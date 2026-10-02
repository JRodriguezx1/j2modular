<?php

namespace App\Models\suscripcioncuenta;

class suscripcion_formas_pago {
    
    public function __construct($args = [])
    {
        $this->id = $args['id']??null;
        $this->descripcion = $args['descripcion']??id_sucursal();
        $this->telefono = $args['telefono']??'';
        $this->token = $args['token']??'';
        $this->payment_token = $args['payment_token']??'';
        $this->fuente_pago = $args['fuente_pago']??1;
        $this->url = $args['url']??0;
        $this->fecha = $args['fecha']??0;
        $this->fecha_expira = $args['fecha_expira']??0;
        $this->is_default = $args['is_default']??date('Y-m-d');
        $this->tipo = $args['tipo']?? date('Y-m-d H:i:s');
        $this->cuenta = $args['cuenta']??'1';  //1 = si registra en caja
        $this->is_available = $args['is_available']??'0';  //0 = no es cuota antigua
        $this->estado = $args['estado']??0;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->url || $this->url<=0)$alertas['error'][] = "Numero de cuotas es incorrecto, validar nuevamente";
        if($this->fecha<=0)$alertas['error'][] = "Monto de la cuota no es valido, verificar nuevamente.";
        if(!$this->fecha_expira || $this->fecha_expira<=0)$alertas['error'][] = "Valor de la cuota no es valido, verificar nuevamente.";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id, 
            'descripcion' => $this->descripcion, 
            'telefono' => $this->telefono,
            'token' => $this->token,
            'payment_token' => $this->payment_token, 
            'fuente_pago' => $this->fuente_pago, 
            'url' => $this->url, 
            'fecha' => $this->fecha, 
            'fecha_expira' => $this->fecha_expira, 
            'is_default' => $this->is_default, 
            'tipo' => $this->tipo, 
            'cuenta' => $this->cuenta,
            'is_available' => $this->is_available,
            'estado' => $this->estado,
            //'created_at' => $this->created_at
        ];
    }

}