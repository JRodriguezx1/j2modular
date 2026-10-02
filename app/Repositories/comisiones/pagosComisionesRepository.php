<?php

namespace App\Repositories\comisiones;

use App\Models\comisiones\pagos_comisiones;
use App\Repositories\operationRepository;

class pagosComisionesRepository extends operationRepository{

    //private $db;
    protected string $table = 'pagos_comisiones';
    protected string $entityClass = pagos_comisiones::class;
    protected array $allowedColumns = [];

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
        
    }

    
    public function getConexion(){ return self::getDB(); }


    public function historialPagosXUser(int $idusuario, string $fechainicio, string $fechafin):array{
        $sql = "SELECT fecha, 'COMISION' as tipo, valorcomision as entrada, 0 as salida, 'Venta' as concepto, idfacturaid as id
                FROM comisiones
                WHERE idusuariofk = $idusuario AND fecha >= '$fechainicio' AND fecha <= '$fechafin'
                UNION ALL
                SELECT fechapago as fecha, tipo, 0 as entrada, valor as salida, mediopago as concepto, id
                FROM $this->table
                WHERE fkusuarioid = $idusuario AND fechapago >= '$fechainicio' AND fechapago <= '$fechafin'
                ORDER BY fecha ASC;";

        //$sql = "SELECT *FROM $this->table WHERE fkusuarioid = $idusuario AND fechapago BETWEEN '$fechainicio' AND '$fechafin';";
        $rows = $this->fetchAllStd($sql);

        $totalPagado = 0;
        foreach ($rows as $obj)$totalPagado += $obj->salida;

        return ['movimientos'=>$rows, 'totalPagado'=>$totalPagado];
    }


    public function totalPagadoAllUsers(int $idsucursal):array{
        $sql = "SELECT 
                    COALESCE(SUM(pc.valor), 0) as comisiontotalpagada
                FROM $this->table pc WHERE pc.fkidsucursal = $idsucursal;";
        $rows = $this->fetchAllStd($sql);
        return $rows;
    }
    
    
}