<?php

namespace App\Repositories\creditos;

use App\Models\contracts\mediosPagoContract;
use App\Models\creditos\separadomediopago;
use App\Repositories\operationRepository;

class separadoMediopagoRepository extends operationRepository implements mediosPagoContract{

    //private $db;
    protected string $table = 'separadomediopago';
    protected string $entityClass = separadomediopago::class;
    protected array $allowedColumns = [];
    protected string $pagoDestino = 'idcuota';

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
    }


    public function getModel():object{
        return new $this->entityClass();
    }
    
    /*public function obtenerPorCredito(int $id):array{

        $sql = "SELECT *FROM separadomediopago WHERE idcredito = $id;";
        $resultado = $this->db->query($sql);
        while($row = $resultado->fetch_assoc()){ 
            $array[] = new separadomediopago($row);   
        }
        $resultado->free();
        return $array;

    }*/

    /*public function obtenerPorCredito(int $id):array{
        $sql = "SELECT *FROM separadomediopago WHERE id_credito = $id;";
        $rows = $this->fetchAll($sql);
        $array = array_map(fn($v) => new separadomediopago($v), $rows);
        return $array;
    }*/


    public function allMediospagoXCierrecaja(int $id):array{
        $sql = "SELECT m.id as idmediopago, m.mediopago, smp.valor FROM separadomediopago smp
                JOIN cuotas c ON smp.idcuota = c.id
                JOIN mediospago m ON smp.mediopago_id = m.id
                WHERE c.cierrecaja_id = $id;";
        $rows = $this->fetchAll($sql);
        return $rows;
    }

    public function getPagoDestino():string{
        return $this->pagoDestino;
    }

    public function getEntityClass():string{
        return $this->entityClass;
    }
    
}