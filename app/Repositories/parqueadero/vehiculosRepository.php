<?php

namespace App\Repositories\parqueadero;

use App\Models\parqueadero\vehiculos;
use App\Repositories\operationRepository;

class PDOVehiculoRepository extends operationRepository
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByPlaca(string $placa): ?vehiculos 
    {
        // Estandarizamos la búsqueda en mayúsculas y sin espacios
        $placaLimpia = strtoupper(str_replace([' ', '-'], '', $placa));
        
        $stmt = $this->db->prepare("SELECT * FROM vehiculos WHERE placa = :placa");
        $stmt->execute(['placa' => $placaLimpia]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new vehiculos($row) : null;
    }

    public function save(vehiculos $vehiculo): int 
    {
        $sql = "INSERT INTO vehiculos (placa, tipo_vehiculo_id, propietario, telefono) 
                VALUES (:placa, :tipo_vehiculo_id, :propietario, :telefono)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'placa' => $vehiculo->placa,
            'tipo_vehiculo_id' => $vehiculo->tipoVehiculoId,
            'propietario' => $vehiculo->propietario,
            'telefono' => $vehiculo->telefono
        ]);

        return (int)$this->db->lastInsertId();
    }
}