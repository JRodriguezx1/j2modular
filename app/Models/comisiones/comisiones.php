<?php

namespace App\Models\comisiones;

class comisiones {
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->fk_idsucursal = $args['fk_idsucursal']??id_sucursal();
        $this->idfacturaid = $args['idfacturaid']??'';
        $this->idusuariofk = $args['idusuariofk']??'';
        $this->valorfactura = $args['valorfactura']??0;
        $this->percentcomision = $args['percentcomision']??'';
        $this->valorcomision = $args['valorcomision']??0;
        $this->valorentregado = $args['valorentregado']??0;
        $this->fecha = $args['fecha']??date('Y-m-d H:i:s');
        $this->estado = $args['estado']??0; //-- pendiente(0), pagada(1), parcial(2)
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->idusuariofk)$alertas['error'][] = "Debe seleccionar el empleado";
        if($this->valorcomision<0)$alertas['error'][] = "el valor de la comision no es valido.";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id,
            'fk_idsucursal'=>$this->fk_idsucursal,
            'idfacturaid' => $this->idfacturaid,
            'idusuariofk' => $this->idusuariofk,
            'valorfactura' => $this->valorfactura,
            'percentcomision' => $this->percentcomision,
            'valorcomision' => $this->valorcomision,
            'valorentregado' => $this->valorentregado,
            'fecha' => $this->fecha, 
            'estado' => $this->estado,
        ];
    }

    public function actualizarComision($valorpagadoCuota, int|null $idf) {
        $this->numcuota += 1;
        $this->abonodecuotas += $valorpagadoCuota;
        $this->saldopendiente -= $valorpagadoCuota;
        if($this->saldopendiente<=0){
            $this->factura_id = $idf;
            $this->estado = 1;  //credito cerrado
            $this->idestadocreditos = 1; //credito finalizado
            $this->fechafin = date('Y-m-d H:i:s');
            $this->productoentregado = 1;  //producto entregado
        }
    }

}