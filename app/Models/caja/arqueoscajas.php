<?php

namespace App\Models\caja;

class arqueoscajas extends \App\Models\ActiveRecord{
    protected static $tabla = 'arqueoscajas';
    protected static $columnasDB = ['id', 'id_cierrecajaid', 'cienmil', 'cincuentamil', 'veintemil', 'diezmil', 'cincomil', 'dosmil', 'mil', 'quinientos', 'docientos', 'cien', 'cincuenta', 'dato1', 'dato2'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_cierrecajaid = $args['id_cierrecajaid']??0;
        $this->cienmil = $args['cienmil']??'';
        $this->cincuentamil = $args['cincuentamil']??0;
        $this->veintemil = $args['veintemil']??0;
        $this->diezmil = $args['diezmil']??0;
        $this->cincomil = $args['cincomil']??0;
        $this->dosmil = $args['dosmil']??0;
        $this->mil = $args['mil']??0;
        $this->quinientos = $args['quinientos']??0;
        $this->docientos = $args['docientos']??0;
        $this->cien = $args['cien']??0;
        $this->cincuenta = $args['cincuenta']??0;
        $this->dato1 = $args['dato1']??0;
        $this->dato2 = $args['dato2']??0;
    }

}

?>