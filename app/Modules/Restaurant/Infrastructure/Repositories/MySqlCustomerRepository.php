<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;

use App\Modules\Restaurant\Domain\Entities\Customer;
use App\Modules\Restaurant\Domain\Repositories\CustomerRepository;

class MySqlCustomerRepository implements CustomerRepository{

    public function __construct(private mysqli $db)
    {}


    public function search(string $term, int $limit = 10): array{

        $sql = "
            SELECT
                id,
                nombre,
                apellido,
                identificacion,
                telefono,
                email
            FROM clientes
            WHERE nombre LIKE ? OR apellido LIKE ? OR CONCAT(nombre, ' ', apellido) LIKE ? OR identificacion LIKE ? OR telefono LIKE ?
            ORDER BY nombre ASC, apellido ASC LIMIT ?";


        $stmt = $this->db->prepare($sql);
        if(!$stmt)throw new \RuntimeException('Error preparando búsqueda de clientes: ' . $this->db->error);
        $search = '%' . $term . '%';
        $stmt->bind_param('sssssi', $search, $search, $search, $search, $search, $limit);

        $stmt->execute();

        $result = $stmt->get_result();
        $customers = [];

        while ($row = $result->fetch_assoc()) {
            $customers[] =
                new Customer(
                    id: (int) $row['id'],
                    name: $row['nombre'],
                    lastName: $row['apellido'],
                    identification: $row['identificacion'],
                    phone:  $row['telefono'],
                    email:  $row['email']
                );
        }

        $stmt->close();
        return $customers;
    }


    public function exists(int $customerId): bool{
        $sql = "SELECT 1 FROM clientes WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if(!$stmt)throw new \RuntimeException('Error preparando validación de cliente: ' . $this->db->error);
        $stmt->bind_param('i', $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
}