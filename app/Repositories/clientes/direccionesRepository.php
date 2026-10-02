<?php

namespace App\Repositories\clientes;

use App\Models\clientes\direcciones;
use App\Repositories\operationRepository;

/** Persistencia de direcciones y lectura de su tarifa asociada. */
class direccionesRepository extends operationRepository
{
    protected string $table = 'direcciones';
    protected string $entityClass = direcciones::class;
    protected array $allowedColumns = [];

    public function __construct()
    {
        $this->allowedColumns = array_keys((new $this->entityClass())->toArray());
    }

    public function findDeCliente(int $idDireccion, int $idCliente):?direcciones
    {
        return $this->uniqueWhere(['id'=>$idDireccion, 'idcliente'=>$idCliente]);
    }

    /**
     * Conserva el contrato JSON que consumen ventas.ts y clientes.ts:
     * cada direccion incluye un objeto anidado llamado tarifa.
     */
    public function conTarifaPorCliente(int $idCliente):array
    {
        $sql = "SELECT d.*,
                       t.id AS tarifa_id,
                       t.nombre AS tarifa_nombre,
                       t.valor AS tarifa_valor
                FROM {$this->table} d
                LEFT JOIN tarifas t ON t.id = d.idtarifa
                WHERE d.idcliente = ".(int)$idCliente."
                ORDER BY d.id";
        $rows = $this->fetchAll($sql);

        return array_map(function(array $row){
            $direccion = new direcciones($row);
            $direccion->tarifa = $row['tarifa_id'] === null
                ? null
                : (object)[
                    'id'=>$row['tarifa_id'],
                    'nombre'=>$row['tarifa_nombre'],
                    'valor'=>$row['tarifa_valor'],
                ];
            return $direccion;
        }, $rows);
    }
}
