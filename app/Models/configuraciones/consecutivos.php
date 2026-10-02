<?php
namespace App\Models\configuraciones;

class consecutivos extends \App\Models\ActiveRecord {
    protected static $tabla = 'consecutivos';
    protected static $columnasDB = ['id', 'id_sucursalid', 'idcompania', 'idtipofacturador', 'idnegocio', 'nombre', 'rangoinicial', 'rangofinal', 'siguientevalor', 'fechainicio', 'fechafin', 'resolucion', 'consecutivoremplazo', 'prefijo', 'mostrarresolucion', 'mostrarimpuestodiscriminado', 'tokenfacturaelectronica', 'electronica', 'estado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->id_sucursalid = id_sucursal();
        $this->idcompania = $args['idcompania'] ?? null;
        $this->idtipofacturador = $args['idtipofacturador'] ?? 1;
        $this->idnegocio = $args['idnegocio'] ?? 1; 
        $this->nombre = $args['nombre'] ?? 1;
        $this->rangoinicial = $args['rangoinicial'] ?? 1;
        $this->rangofinal = $args['rangofinal'] ?? 1;
        $this->siguientevalor = $args['siguientevalor'] ?? 1;
        $this->fechainicio = $args['fechainicio'] ?? '';  
        $this->fechafin = $args['fechafin'] ?? '';  
        $this->resolucion = $args['resolucion'] ?? '';   
        $this->consecutivoremplazo = $args['consecutivoremplazo'] ?? '';  
        $this->prefijo = $args['prefijo'] ?? '';  
        $this->mostrarresolucion = $args['mostrarresolucion'] ?? '';  
        $this->mostrarimpuestodiscriminado = $args['mostrarimpuestodiscriminado'] ?? 0;
        $this->tokenfacturaelectronica = $args['tokenfacturaelectronica'] ?? 0;
        $this->electronica = $args['electronica'] ?? '';
        $this->estado = $args['estado'] ?? 1;
    }

    // Validación para clientes nuevos
    public function validar():array {
        if(!$this->idtipofacturador)self::$alertas['error'][] = 'Id tipofacturador no encontrado';
        if(!$this->nombre)self::$alertas['error'][] = 'nombre del consecutivo no especificado';
        if(strlen($this->nombre)>26)self::$alertas['error'][] = 'nombre del consecutivo es muy extenso';
        if(!isset($this->siguientevalor) || !is_numeric($this->siguientevalor) || $this->siguientevalor<=0)self::$alertas['error'][] = 'Error en el consecutivo inicial';
        return self::$alertas;
    }
}