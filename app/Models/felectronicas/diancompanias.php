<?php

namespace App\Models\felectronicas;

class diancompanias extends \App\Models\ActiveRecord{
    protected static $tabla = 'diancompanias';
    protected static $columnasDB = ['id', 'business_name', 'identification_number', 'idsoftware', 'pinsoftware', 'token', 'estado'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->business_name = $args['business_name']??'';
        $this->identification_number = $args['identification_number']??'';
        $this->idsoftware = $args['idsoftware']??'';
        $this->pinsoftware = $args['pinsoftware']??'';
        $this->token = $args['token']??'';
        $this->estado = $args['estado']??'1';
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!is_numeric($this->identification_number) || !$this->identification_number)self::$alertas['error'][] = 'Numero de documento invalido.';
        if(!$this->business_name)self::$alertas['error'][] = 'El nombre es obligario';
        if(strlen($this->business_name)>65)self::$alertas['error'][] = 'El nombre es muy largo';
        if(!$this->idsoftware)self::$alertas['error'][] = 'Campo idsoftware vacio';
        if(!$this->token)self::$alertas['error'][] = 'Token no puede ir vacio';
        return self::$alertas;
    }


}