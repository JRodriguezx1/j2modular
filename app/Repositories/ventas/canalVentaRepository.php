<?php

namespace App\Repositories\ventas;

use App\Models\ventas\canaldeventa;
use App\Repositories\operationRepository;

class canalVentaRepository extends operationRepository{

    //private $db;
    protected string $table = 'canaldeventa';
    protected string $entityClass = canaldeventa::class;
    protected array $allowedColumns = [];
    //protected string $pagoDestino = 'idcuota';

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
        
    }


    /*public function getPagoDestino():string{
        return $this->pagoDestino;
    }*/

    public function getEntityClass():string{
        return $this->entityClass;
    }
    
}