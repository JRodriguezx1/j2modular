<?php
namespace App\Models\configuraciones;

class notacreditoinvoice extends \App\Models\ActiveRecord {
    protected static $tabla = 'notacreditoinvoice';
    protected static $columnasDB = ['id', 'idsucursal_id_fk', 'id_compania', 'type_document_id', 'prefix', 'resolution', 'nextnumber', 'resolution_date', 'technical_key', 'fromNC', 'toNC', 'date_from', 'date_to', 'estado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idsucursal_id_fk = id_sucursal();
        $this->id_compania = $args['id_compania'] ?? null;
        $this->type_document_id = $args['type_document_id'] ?? 4;
        $this->prefix = $args['prefix'] ?? ''; 
        $this->resolution = $args['resolution'] ?? '';
        $this->nextnumber = $args['nextnumber'] ?? '';
        $this->resolution_date = $args['resolution_date'] ?? '';  //fecha de expedicion de resolucion
        $this->technical_key = $args['technical_key'] ?? '';
        $this->fromNC = $args['fromNC'] ?? 1;  //rango inicial del consecutivo
        $this->toNC = $args['toNC'] ?? 250000;    //rango final del consecutivo
        $this->date_from = $args['date_from'] ?? '';  //fecha incial de la resolucion
        $this->date_to = $args['date_to'] ?? '';      //fecha de vencimiento de la resolucion
        $this->estado = $args['estado'] ?? 1;
        $this->created_at = $args['created_at']??'';
    }
}