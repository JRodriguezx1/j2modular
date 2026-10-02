<?php

namespace App\Models\creditos;

class separadomediopago { // extends \App\Models\ActiveRecord implements mediosPagoContract
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->idcuota = $args['idcuota']??'';
        $this->mediopago_id = $args['mediopago_id']??'';
        $this->valor = $args['valor']??'';
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->idcuota)$alertas['error'][] = "Error intenta nuevamnete";
        if(!$this->mediopago_id)$alertas['error'][] = "Error intenta nuevamnete";
        return $alertas;
    }
    

    public function toArray():array {
        return [
            //'id' => $this->id, 
            'idcuota' => $this->idcuota, 
            'mediopago_id' => $this->mediopago_id, 
            'valor' => $this->valor, 
            //'created_at' => $this->created_at
        ];
    }

    /*public function pagoDestino(int $id):void{
        $this->idcuota = $id;
    }*/
    
}