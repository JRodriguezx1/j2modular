<?php

namespace App\Models\creditos;

class creditos {
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_fksucursal = $args['id_fksucursal']??id_sucursal();
        $this->idemisor = $args['idemisor']??'';
        $this->usuariofk = $args['usuariofk']??'';
        $this->idtipofinanciacion = $args['idtipofinanciacion']??'';
        $this->factura_id = $args['factura_id']??NULL;
        $this->cliente_id = $args['cliente_id']??'';
        $this->idestadocreditos = $args['idestadocreditos']??2;  //2 = abierto, 1 = finalizado, 3 = anulado
        $this->num_orden = $args['num_orden']??'';
        $this->nombrecliente = $args['nombrecliente']??'cliente';
        $this->capital = $args['capital']??0;
        $this->abonoinicial = $args['abonoinicial']??0;
        $this->abonodecuotas = $args['abonodecuotas']??0;
        $this->saldopendiente = $args['saldopendiente']??0;
        $this->numcuota = $args['numcuota']??0;
        $this->cantidadcuotas = $args['cantidadcuotas']??0;
        $this->montocuota = $args['montocuota']??0;
        $this->frecuenciapago = $args['frecuenciapago']??'';
        $this->fechainicio = $args['fechainicio']??date('Y-m-d');
        $this->fechafin = $args['fechafin']??date('Y-m-d H:i:s');
        $this->diasenmora = $args['diasenmora']??0;
        $this->interes = $args['interes']??'';
        $this->interesxcuota = $args['interesxcuota']??0;
        $this->interestotal = $args['interestotal']??0;
        $this->valorinteresxcuota = $args['valorinteresxcuota']??0;
        $this->valorinterestotal = $args['valorinterestotal']??0;
        $this->montototal = $args['montototal']??0;
        $this->fechavencimiento = $args['fechavencimiento']??'';
        $this->productoentregado = $args['productoentregado']??0;
        $this->totalunidades = $args['totalunidades']??'';
        $this->base = $args['base']??'';
        $this->valorimpuestototal = $args['valorimpuestototal']??'';
        $this->dctox100 = $args['dctox100']??'';
        $this->descuento = $args['descuento']??'';
        $this->abonototalantiguo = $args['abonototalantiguo']??'0';
        $this->cantidadcuotasantiguas = $args['cantidadcuotasantiguas']??'0';
        $this->fechaultimoabonoantiguo = $args['fechaultimoabonoantiguo']??'';
        $this->nota = $args['nota']??'';
        $this->estado = $args['estado']??0; //1 = credito finalizado
        $this->created_at = $args['created_at']??'';
    }


    public function validar():array
    {
        $alertas = [];
        if(!$this->cliente_id)$alertas['error'][] = "Debe seleccionar un cliente";
        if($this->capital<=0)$alertas['error'][] = "el valor del credito total no es valido.";
        if($this->cantidadcuotas<=0)$alertas['error'][] = "Numero de cuotas no es valido, verifica nuevamente.";
        if($this->montocuota<=0)$alertas['error'][] = "Valor de la cuota no es valido.";
        if($this->montototal<0)$alertas['error'][] = "El capital + interes no es valido";
        if(!$this->frecuenciapago)$alertas['error'][] = "Seleccionar dia de pago de la cuota";
        return $alertas;
    }

    public function toArray():array {
        return [
            //'id' => $this->id, 
            'id_fksucursal' => $this->id_fksucursal,
            'usuariofk' => $this->usuariofk,
            'idemisor' => $this->idemisor,
            'idtipofinanciacion' => $this->idtipofinanciacion, 
            'factura_id' => $this->factura_id, 
            'cliente_id' => $this->cliente_id,
            'idestadocreditos' => $this->idestadocreditos,  //2 = abierto, 1 = finalizado, 3 = anulado
            'num_orden' =>$this->num_orden,
            'nombrecliente' => $this->nombrecliente, 
            'capital' => $this->capital, 
            'abonoinicial' => $this->abonoinicial,
            'abonodecuotas' => $this->abonodecuotas,
            'saldopendiente' => $this->saldopendiente, 
            'numcuota' => $this->numcuota, 
            'cantidadcuotas' => $this->cantidadcuotas, 
            'montocuota' => $this->montocuota,
            'frecuenciapago' => $this->frecuenciapago, 
            'fechainicio' => $this->fechainicio, 
            'fechafin' => $this->fechafin,
            'diasenmora' => $this->diasenmora,
            'interes' => $this->interes, 
            'interesxcuota' => $this->interesxcuota, 
            'interestotal' => $this->interestotal, 
            'valorinteresxcuota' => $this->valorinteresxcuota,
            'valorinterestotal' => $this->valorinterestotal, 
            'montototal' => $this->montototal, 
            'fechavencimiento' => $this->fechavencimiento, 
            'productoentregado' => $this->productoentregado,
            'totalunidades' => $this->totalunidades,
            'base' => $this->base,
            'valorimpuestototal' => $this->valorimpuestototal,
            'dctox100' => $this->dctox100,
            'descuento' => $this->descuento,
            'abonototalantiguo' => $this->abonototalantiguo,
            'cantidadcuotasantiguas' => $this->cantidadcuotasantiguas,
            'fechaultimoabonoantiguo' => $this->fechaultimoabonoantiguo,
            'nota' => $this->nota,
            'estado' => $this->estado,
            //'created_at' => $this->created_at
        ];
    }

    public function actualizarCredito($valorpagadoCuota, int|null $idf) {
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