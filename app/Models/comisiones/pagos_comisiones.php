<?php

namespace App\Models\comisiones;

class pagos_comisiones {
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->fkidsucursal = $args['fkidsucursal']??id_sucursal();
        $this->fkcierrecaja = $args['fkcierrecaja']??NULL;
        $this->fkusuarioid = $args['fkusuarioid']??'';
        $this->idmediopagoid = $args['idmediopagoid']??1;
        $this->valor = $args['valor']??0;
        $this->mediopago = $args['mediopago']??'';
        $this->fechapago = $args['fechapago']??date('Y-m-d H:i:s');
        $this->tipo = $args['tipo']??'pago';  //anticipo, pago
        $this->referencia = $args['referencia']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->fkusuarioid)$alertas['error'][] = "Debe seleccionar el empleado";
        if(strlen($this->mediopago)>20)$alertas['error'][] = "Medio de pago muy largo";
        if(!$this->idmediopagoid)$alertas['error'][] = "Debe seleccionar el medio de pago";
        if($this->valor<0)$alertas['error'][] = "el valor de la comision a pagar no es valido.";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id,
            'fkidsucursal' => $this->fkidsucursal,
            'fkcierrecaja' => $this->fkcierrecaja,
            'fkusuarioid' => $this->fkusuarioid,
            'idmediopagoid' => $this->idmediopagoid,
            'valor' => $this->valor,
            'mediopago' => $this->mediopago,
            'fechapago' => $this->fechapago, 
            'tipo' => $this->tipo,
            'referencia' => $this->referencia,
        ];
    }

    

}