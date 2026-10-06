<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;
use App\Modules\Restaurant\Domain\Entities\Occupation;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;

class MySqlOccupationRepository implements OccupationRepository{

    public function __construct(private mysqli $db)
    {}

    public function findCurrentByResourceId(int $resourceId): ?Occupation {

        $sql = "
            SELECT
                o.id,
                o.reserva_id,
                o.cliente_id,
                o.tipo,
                o.fecha_inicio,
                o.fecha_fin_estimada,
                o.fecha_fin_real,
                o.estado,
                o.observaciones
            FROM ocupaciones AS o

            INNER JOIN ocupacion_recursos AS obr
                ON obr.ocupacion_id = o.id

            WHERE obr.recurso_id = ?
              AND o.estado = 'en_uso'

            ORDER BY o.fecha_inicio DESC

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)throw new \RuntimeException('Error preparando consulta de ocupacion: ' . $this->db->error);

        $stmt->bind_param('i', $resourceId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        if(!$row)return null;

        return new Occupation(
            id: (int) $row['id'],
            reservationId: $row['reserva_id'] !== null ? (int) $row['reserva_id'] : null,
            clientId: $row['cliente_id'] !== null ? (int) $row['cliente_id'] : null,
            type: $row['tipo'],
            startDate: $row['fecha_inicio'],
            estimatedEndDate: $row['fecha_fin_estimada'],
            actualEndDate: $row['fecha_fin_real'],
            status: $row['estado'],
            observations: $row['observaciones']
        );
    }

    public function findCurrentByResourceIds(array $resourceIds): array {

        if(empty($resourceIds))return [];

        $resourceIds = array_map('intval', $resourceIds);
        $placeholders = implode(',', array_fill(0, count($resourceIds), '?'));

        $sql = "
            SELECT
                o.id,
                o.reserva_id,
                o.cliente_id,
                o.tipo,
                o.fecha_inicio,
                o.fecha_fin_estimada,
                o.fecha_fin_real,
                o.estado,
                o.observaciones,
                obr.recurso_id

            FROM ocupaciones AS o

            INNER JOIN ocupacion_recursos AS obr
                ON obr.ocupacion_id = o.id

            WHERE o.estado = 'en_uso'
            AND obr.recurso_id IN ($placeholders)

            ORDER BY o.fecha_inicio DESC
        ";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('Error preparando consulta de ocupaciones: '. $this->db->error);

        $types = str_repeat('i', count($resourceIds));

        $stmt->bind_param($types, ...$resourceIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $occupations = [];

        while ($row = $result->fetch_assoc()) {

            $resourceId = (int) $row['recurso_id'];

            /*
            * Como ordenamos por fecha_inicio DESC,
            * conservamos la ocupación más reciente.
            */
            if (isset($occupations[$resourceId])) {
                continue;
            }

            $occupations[$resourceId] = new Occupation(
                id: (int) $row['id'],
                reservationId: $row['reserva_id'] !== null ? (int) $row['reserva_id'] : null,
                clientId: $row['cliente_id'] !== null ? (int) $row['cliente_id'] : null,
                type: $row['tipo'],
                startDate: $row['fecha_inicio'],
                estimatedEndDate: $row['fecha_fin_estimada'],
                actualEndDate: $row['fecha_fin_real'],
                status: $row['estado'],
                observations: $row['observaciones']
            );
        }

        $stmt->close();
        return $occupations;
    }


    public function create(
    ?int $reservationId,
    ?int $clientId,
    string $type,
    string $startDate,
    ?string $estimatedEndDate,
    ?string $observations
): int {

    $sql = "
        INSERT INTO ocupaciones (
            reserva_id,
            cliente_id,
            tipo,
            fecha_inicio,
            fecha_fin_estimada,
            estado,
            observaciones
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            'en_uso',
            ?
        )
    ";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new \RuntimeException(
            'Error preparando creación de ocupación: '
            . $this->db->error
        );
    }

    $stmt->bind_param(
        'iissss',
        $reservationId,
        $clientId,
        $type,
        $startDate,
        $estimatedEndDate,
        $observations
    );

    if (!$stmt->execute()) {

        $error = $stmt->error;

        $stmt->close();

        throw new \RuntimeException(
            'Error creando la ocupación: '
            . $error
        );
    }

    $occupationId =
        (int) $this->db->insert_id;

    $stmt->close();

    return $occupationId;
}


public function attachResource(
    int $occupationId,
    int $resourceId,
    float $baseValue = 0
): void {

    $sql = "
        INSERT INTO ocupacion_recursos (
            ocupacion_id,
            recurso_id,
            valor_base
        )
        VALUES (?, ?, ?)
    ";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        throw new \RuntimeException(
            'Error preparando recurso de ocupación: '
            . $this->db->error
        );
    }

    $stmt->bind_param(
        'iid',
        $occupationId,
        $resourceId,
        $baseValue
    );

    if (!$stmt->execute()) {

        $error = $stmt->error;

        $stmt->close();

        throw new \RuntimeException(
            'Error vinculando recurso a la ocupación: '
            . $error
        );
    }

    $stmt->close();
}
    
}