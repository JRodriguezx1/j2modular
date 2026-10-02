<?php

namespace App\Models\felectronicas;

class adquirientes extends \App\Models\ActiveRecord{
    protected static $tabla = 'adquirientes';
    protected static $columnasDB = ['id', 'type_document_identification_id', 'identification_number', 'business_name', 'email', 'address', 'municipality_id', 'type_organization_id', 'type_regime_id', 'phone', 'department_id', 'departamento_nombre', 'ciudad_nombre'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->type_document_identification_id = $args['type_document_identification_id']??'';
        $this->identification_number = $args['identification_number']??'';
        $this->business_name = $args['business_name']??'';
        $this->email = $args['email']??'';
        $this->address = $args['address']??'';
        $this->municipality_id = $args['municipality_id']??'';
        $this->type_organization_id = $args['type_organization_id']??1;
        $this->type_regime_id = $args['type_regime_id']??2;
        $this->phone = $args['phone']??'';
        $this->department_id = $args['department_id']??'';
        $this->departamento_nombre = $args['departamento_nombre']??'';
        $this->ciudad_nombre = $args['ciudad_nombre']??'';
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!is_numeric($this->type_document_identification_id))self::$alertas['error'][] = 'Error en el tipo de documento.';
        if(!is_numeric($this->identification_number) || !$this->identification_number)self::$alertas['error'][] = 'Numero de documento invalido.';
        if(!$this->business_name)self::$alertas['error'][] = 'El nombre es obligario';
        if(strlen($this->business_name)>65)self::$alertas['error'][] = 'El nombre es muy largo';
        if($this->email)if(!filter_var($this->email, FILTER_VALIDATE_EMAIL))self::$alertas['error'][] = 'Email no valido';
        if($this->address)if(strlen($this->address)>75)self::$alertas['error'][] = 'La direccion a superado el limite de caracteres';
        if($this->phone)if(strlen($this->phone)>15)self::$alertas['error'][] = 'El telefono es muy extenso';
        
        return self::$alertas;
    }


}