<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;
use App\Modules\Restaurant\Domain\Entities\Zonas;
use App\Modules\Restaurant\Domain\Repositories\ZoneRepository;

class MySqlZoneRepository implements ZoneRepository
{
    public function __construct(private mysqli $db)
    {}

    public function findAll(): array
    {
        $sql = "SELECT *FROM zonas WHERE activo = 1";

        $result = $this->db->query($sql);

        if(!$result)
            throw new \RuntimeException('Error al consultar las zonas: ' . $this->db->error);
        

        $tables = [];

        while ($row = $result->fetch_assoc()) {
            $tables[] = new Zonas(
                id: (int) $row['id'],
                nombre: $row['nombre'],
                activo: (bool) $row['activo'],
            );
        }

        return $tables;
    }

}