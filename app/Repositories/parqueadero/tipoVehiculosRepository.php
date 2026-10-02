<?php

namespace App\Repositories\parqueadero;

use App\Models\parqueadero\tipo_vehiculo;
use App\Repositories\operationRepository;
//use PDO;

class tipoVehiculosRepository extends operationRepository{

    protected string $table = 'tipo_vehiculo';
    /*private $db;

    public function __construct($db) {
        $this->db = $db;
    }*/

    

    public function allActive(): array 
    {
        $rows = $this->fetchAll("SELECT * FROM {$this->table} WHERE estado = 1");
        return array_map(fn($row) => new tipo_vehiculo($row), $rows);
    }

}