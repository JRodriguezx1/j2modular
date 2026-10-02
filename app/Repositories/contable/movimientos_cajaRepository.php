<?php

namespace App\Repositories\contable;

use App\Models\contable\movimientos_caja;
use App\Repositories\operationRepository;

class movimientos_cajaRepository extends operationRepository
{
    protected string $table = 'movimientos_caja';
    protected string $entityClass = movimientos_caja::class;
    protected string $idsucursal = 'id_sucursal';
    /*private $db; // Tu conexión PDO

    public function __construct($db) {
        $this->db = $db;
    }*/


    public function allActive(): array 
    {
        $rows = $this->fetchAll("SELECT * FROM {$this->table} WHERE estado = 1");
        return array_map(fn($row) => new movimientos_caja($row), $rows);
    }
    
}