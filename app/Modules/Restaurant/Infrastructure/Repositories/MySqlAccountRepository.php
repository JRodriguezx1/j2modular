<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;
use RuntimeException;

use App\Modules\Restaurant\Domain\Entities\Account;
use App\Modules\Restaurant\Domain\Repositories\AccountRepository;

class MySqlAccountRepository implements AccountRepository{

    public function __construct(private mysqli $db)
    {}

    public function findByOccupationId(int $occupationId): array{
        $sql = "
            SELECT id, ocupacion_id, idfactura, nombre, estado
            FROM cuentas WHERE ocupacion_id = ? ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $occupationId);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $accounts = [];
            while ($row = $result->fetch_assoc()) {
                $accounts[] = $this->mapToEntity($row);
            }
            return $accounts;
        } finally {
            $stmt->close();
        }
    }

    public function findById(int $accountId): ?Account{
        $sql = "
            SELECT id, ocupacion_id, idfactura, nombre, estado
            FROM cuentas WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $accountId);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? $this->mapToEntity($row) : null;
        } finally {
            $stmt->close();
        }
    }

    public function create(int $occupationId, string $name = 'General'): int{
        $sql = "INSERT INTO cuentas (ocupacion_id, nombre, estado) VALUES (?, ?, 'abierta')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('is', $occupationId, $name);
        try {
            $stmt->execute();
            return (int)$this->db->insert_id;
        } finally {
            $stmt->close();
        }
    }

    private function mapToEntity(array $row): Account{
        return new Account(
            id: (int) $row['id'],
            occupationId: (int) $row['ocupacion_id'],
            invoiceId: $row['idfactura'] !== null ? (int) $row['idfactura'] : null,
            name: $row['nombre'],
            status: $row['estado']
        );
    }


    public function createMain(int $occupationId): int{

        $sql = "INSERT INTO cuentas (ocupacion_id, nombre, es_principal, estado)
            VALUES (?, 'General', 1, 'abierta')";

        $stmt = $this->db->prepare($sql);

        try{
            $stmt->bind_param('i', $occupationId);
            $stmt->execute();

            return (int)$this->db->insert_id;
        }finally{
            $stmt->close();
        }
    }


    public function findMainByOccupationId(int $occupationId): ?Account {

        $sql = "SELECT
                id, ocupacion_id, idfactura,
                nombre, estado
            FROM cuentas
            WHERE ocupacion_id = ?
            AND es_principal = 1
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        try{
            $stmt->bind_param('i', $occupationId);
            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();

            return $row ? $this->mapToEntity($row) : null;
        }finally{
            $stmt->close();
        }
    }

}