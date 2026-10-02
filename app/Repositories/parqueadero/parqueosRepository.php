<?php

namespace App\Repositories\parqueadero;


class parqueosRepository
{
    private $db; // Tu conexión PDO

    public function __construct($db) {
        $this->db = $db;
    }

    public function findActiveByPlaca(string $placa) {
        $sql = "SELECT p.* FROM parqueos p 
                INNER JOIN vehiculos v ON p.vehiculo_id = v.id 
                WHERE v.placa = :placa AND p.estado = 'ACTIVO' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['placa' => $placa]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function save(array $data): int {
        $sql = "INSERT INTO parqueos (vehiculo_id, fecha_hora_ingreso, estado, observaciones) 
                VALUES (:vehiculo_id, :fecha_hora_ingreso, 'ACTIVO', :observaciones)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE parqueos SET 
                    fecha_hora_salida = :fecha_hora_salida, 
                    horas = :horas, 
                    valor = :valor, 
                    estado = :estado 
                WHERE id = :id";
        $data['id'] = $id;
        return $this->db->prepare($sql)->execute($data);
    }

    public function findWithTarifa(int $id): ?stdClass {
        $sql = "SELECT p.*, tv.tarifa_hora, tv.tarifa_dia 
                FROM parqueos p
                INNER JOIN vehiculos v ON p.vehiculo_id = v.id
                INNER JOIN tipos_vehiculo tv ON v.tipo_vehiculo_id = tv.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }
}