<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;
use App\Modules\Restaurant\Domain\Entities\RestaurantTable;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;

class MySqlTableRepository implements RestaurantTableRepository{

    public function __construct(private mysqli $db)
    {}

    public function findAll(): array{
        $sql = "
            SELECT
                m.id,
                m.recurso_id,
                m.zona_id,
                m.forma,
                r.nombre,
                r.capacidad,
                r.activo,
                r.estado
            FROM mesas AS m
            INNER JOIN recursos AS r ON r.id = m.recurso_id
            ORDER BY r.nombre ASC
        ";

        $result = $this->db->query($sql);
        if(!$result)
            throw new \RuntimeException('Error al consultar las mesas: ' . $this->db->error);

        $tables = [];
        while ($row = $result->fetch_assoc()) {
            $tables[] = new RestaurantTable(
                id: (int) $row['id'],
                resourceId: (int) $row['recurso_id'],
                name: $row['nombre'],
                capacity: (int) $row['capacidad'],
                active: (bool) $row['activo'],
                resourceStatus: $row['estado'], //disponible, mantenimiento, limpieza, fuera_servicio estados del recurso
                zoneId: $row['zona_id'] !== null ? (int) $row['zona_id'] : null,
                shape: $row['forma']
            );
        }

        return $tables;
    }


    public function findAvailableForPeriod( string $startDate, string $endDate, int $capacity): array{
        $sql = "
            SELECT
                m.id,
                m.recurso_id,
                m.zona_id,
                m.forma,
                r.nombre,
                r.capacidad,
                r.activo,
                r.estado,
                z.nombre AS zona_nombre
            FROM mesas AS m
            LEFT JOIN zonas AS z ON z.id = m.zona_id
            INNER JOIN recursos AS r ON r.id = m.recurso_id
            WHERE r.activo = 1 AND r.estado = 'disponible' AND r.capacidad >= ?
                AND NOT EXISTS (
                    SELECT 1
                    FROM reserva_recursos AS rr
                    INNER JOIN reservas AS res ON res.id = rr.reserva_id
                    WHERE rr.recurso_id = r.id
                        AND res.estado NOT IN ( 'cancelada', 'atendida', 'no_asistio')
                        AND res.fecha_entrada < ?
                        AND res.fecha_salida > ?
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM ocupacion_recursos AS ocr
                    INNER JOIN ocupaciones AS o ON o.id = ocr.ocupacion_id
                    WHERE ocr.recurso_id = r.id AND o.estado = 'en_uso' AND o.fecha_inicio < ?
                        AND (o.fecha_fin_estimada IS NULL OR o.fecha_fin_estimada > ?)
                ) 

            ORDER BY r.capacidad ASC, r.nombre ASC";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)throw new \RuntimeException( 'Error preparando búsqueda de mesas disponibles: ' . $this->db->error);

        $stmt->bind_param('issss', $capacity, $endDate, $startDate, $endDate, $startDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $tables = [];

        while($row = $result->fetch_assoc()){
            $tables[] = new RestaurantTable(
                id: (int) $row['id'],
                resourceId: (int) $row['recurso_id'],
                name: $row['nombre'],
                capacity: (int) $row['capacidad'],
                active: (bool) $row['activo'],
                resourceStatus: $row['estado'],
                zoneId: $row['zona_id'] !== null ? (int) $row['zona_id'] : null,
                shape: $row['forma']
            );
        }

        $stmt->close();
        return $tables;
    }


    public function isAvailableForPeriod(int $resourceId, string $startDate, string $endDate, int $capacity): bool{
        $sql = "
            SELECT 1 FROM mesas m
            INNER JOIN recursos r ON r.id = m.recurso_id
            WHERE m.recurso_id = ? AND r.activo = 1 AND r.estado = 'disponible' AND r.capacidad >= ?
                AND NOT EXISTS (
                    SELECT 1
                    FROM reserva_recursos rr
                    INNER JOIN reservas res ON res.id = rr.reserva_id
                    WHERE rr.recurso_id = r.id
                        AND res.estado NOT IN ('cancelada', 'atendida', 'no_asistio')
                        AND res.fecha_entrada < ?
                        AND res.fecha_salida > ?
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM ocupacion_recursos obr
                    INNER JOIN ocupaciones o ON o.id = obr.ocupacion_id
                    WHERE obr.recurso_id = r.id
                        AND o.estado = 'en_uso'
                        AND o.fecha_inicio < ?
                        AND (
                            o.fecha_fin_estimada IS NULL
                            OR o.fecha_fin_estimada > ?
                        )
                )
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if(!$stmt)
            throw new \RuntimeException('Error preparando validación de disponibilidad: ' . $this->db->error);
        $stmt->bind_param('iissss',$resourceId, $capacity, $endDate, $startDate, $endDate, $startDate);

        if(!$stmt->execute()){
            $error = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Error validando disponibilidad: ' . $error);
        }

        $result = $stmt->get_result();
        $available = $result->num_rows > 0;
        $stmt->close();

        return $available;
    }


    public function lockResource(int $resourceId): void{
        $sql = "SELECT id FROM recursos WHERE id = ? FOR UPDATE";
        $stmt = $this->db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('Error preparando bloqueo del recurso: ' . $this->db->error);
        $stmt->bind_param('i', $resourceId);

        if(!$stmt->execute()){
            $error = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Error bloqueando el recurso: ' . $error);
        }

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        if(!$exists)
            throw new \InvalidArgumentException('La mesa seleccionada no existe.');
    }

}