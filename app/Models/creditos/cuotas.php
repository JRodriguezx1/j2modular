<?php

namespace App\Models\creditos;

class cuotas {
    
    public function __construct($args = [])
    {
        $this->id = $args['id']??null;
        $this->id_sucursal_idfk = $args['id_sucursal_idfk']??id_sucursal();
        $this->id_credito = $args['id_credito']??'';
        $this->cierrecaja_id = $args['cierrecaja_id']??'';
        $this->cajaid = $args['cajaid']??'';
        $this->mediopagoid = $args['mediopagoid']??1;
        $this->idusuario = $args['idusuario']??'';
        $this->num_orden = $args['num_orden']??'';
        $this->numerocuota = $args['numerocuota']??0;
        $this->montocuota = $args['montocuota']??0;
        $this->valorpagado = $args['valorpagado']??0;
        $this->fechavencimiento = $args['fechavencimiento']??date('Y-m-d');
        $this->fechapagado = $args['fechapagado']?? date('Y-m-d H:i:s');
        $this->cuotascreditos = $args['cuotascreditos']??NULL;
        $this->registrarencaja = $args['registrarencaja']??'1';  //1 = si registra en caja
        $this->cuotaantigua = $args['cuotaantigua']??'0';  //0 = no es cuota antigua
        $this->fechacuotaantigua = $args['fechacuotaantigua']??NULL;
        $this->concepto = $args['concepto']??'ABONO A FACTURA';
        $this->detalle = $args['detalle']??0;
        $this->estado = $args['estado']??0;
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->numerocuota || $this->numerocuota<=0)$alertas['error'][] = "Numero de cuotas es incorrecto, validar nuevamente";
        if($this->montocuota<=0)$alertas['error'][] = "Monto de la cuota no es valido, verificar nuevamente.";
        if(!$this->valorpagado || $this->valorpagado<=0)$alertas['error'][] = "Valor de la cuota no es valido, verificar nuevamente.";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id, 
            'id_sucursal_idfk' => $this->id_sucursal_idfk, 
            'id_credito' => $this->id_credito,
            'cierrecaja_id' => $this->cierrecaja_id,
            'cajaid' => $this->cajaid, 
            'mediopagoid' => $this->mediopagoid,
            'idusuario' => $this->idusuario,
            'num_orden' => $this->num_orden,
            'numerocuota' => $this->numerocuota, 
            'montocuota' => $this->montocuota, 
            'valorpagado' => $this->valorpagado, 
            'fechavencimiento' => $this->fechavencimiento, 
            'fechapagado' => $this->fechapagado, 
            'cuotascreditos' => $this->cuotascreditos,
            'registrarencaja' => $this->registrarencaja,
            'cuotaantigua' => $this->cuotaantigua,
            'fechacuotaantigua' => $this->fechacuotaantigua,
            'concepto' => $this->concepto,
            'detalle' => $this->detalle,
            'estado' => $this->estado,
            //'created_at' => $this->created_at
        ];
    }


    public function preparar(object $credito) {
        $this->numerocuota = $credito->numerodecuota;
        $this->montocuota = $credito->montocuota;
    }

}