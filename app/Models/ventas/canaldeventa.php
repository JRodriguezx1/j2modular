<?php

namespace App\Models\ventas;

class canaldeventa {
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->nombre = $args['nombre']??'';
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        $alertas = [];
        if(!$this->nombre)$alertas['error'][] = "Nombre del canal de venta es obligatorio";
        return $alertas;
    }

    public function toArray():array {
        return [
            'nombre' => $this->nombre,  
            //'created_at' => $this->created_at
        ];
    }

    /*public function pagoDestino(int $id):void{
        $this->idcuota = $id;
    }*/
    
}