<?php

namespace App\Repositories\suscripcioncuenta;

use App\Models\suscripcioncuenta\suscripcion_pagos;
use App\Repositories\operationRepository;

class suscripcionPagosRepository extends operationRepository{

    //private $db;
    protected string $table = 'suscripcion_pagos';
    protected string $entityClass = suscripcion_pagos::class;
    protected array $allowedColumns = [];  //es la misma propiedad heredada del padre, pero se redefine en el constructor de la clase hija.

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
        
    }

    
    public function getConexion(){ return self::getDB(); }


    /*
    public function activar() {
        $this->estado = 'activo';
    }

    public function actualizarFechaCorte($cantidad, $tipo = 'mes') {
        $this->fecha_corte = date('Y-m-d', strtotime("+$cantidad $tipo"));
    }*/

    public function getPlan(int $id){
        $sql = "SELECT * FROM planes WHERE id = {$id} LIMIT 1";
        $rows = $this->fetchAllStd($sql);
        return end($rows);
    }
    
    public function getEntityClass():string{
        return $this->entityClass;
    }
    
    
    
}