<?php

namespace App\Repositories\comisiones;

use App\Models\comisiones\comisiones;
use App\Repositories\operationRepository;

class comisionesRepository extends operationRepository{

    //private $db;
    protected string $table = 'comisiones';
    protected string $entityClass = comisiones::class;
    protected array $allowedColumns = [];

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
        
    }

    
    public function getConexion(){ return self::getDB(); }


    public function comisionTotalGeneradaBusiness(int $idsucursal):array{
        $sql = "SELECT
                    COALESCE(SUM(c.valorfactura), 0) as totalfacturado,
                    COALESCE(SUM(c.valorcomision), 0) as comisiontotalempleados
                FROM $this->table c WHERE c.fk_idsucursal = $idsucursal;";
        $rows = $this->fetchAllStd($sql);
        return $rows;
    }

    public function comisionTotalGeneradaUser(int $idsucursal, int $idusuario, string $fechainicio, string $fechafin):array{
        $sql = "SELECT 
                    COALESCE(SUM(c.valorcomision), 0) as comisiontotal
                FROM $this->table c WHERE c.fk_idsucursal = $idsucursal AND c.idusuariofk = $idusuario AND fecha >= '$fechainicio' AND fecha <= '$fechafin';";
        $rows = $this->fetchAllStd($sql);
        return $rows;
    }
    
}