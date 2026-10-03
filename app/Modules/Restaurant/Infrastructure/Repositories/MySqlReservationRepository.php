<?php

namespace App\Modules\Restaurant\Infrastructure\Repositories;

use mysqli;
use App\Modules\Restaurant\Domain\Entities\Reservation;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

class MySqlReservationRepository implements ReservationRepository{

    public function __construct(private mysqli $db)
    {}

    /**
     * @return Reservation[]
     */
    public function findUpcomingForToday(int $limit = 5): array {
        $limit = max(1, $limit);

        /*
         * 1. Consultamos únicamente las reservas.
         *
         * No hacemos JOIN con reserva_recursos aquí porque una
         * reserva puede tener varios recursos y el LIMIT terminaría
         * limitando filas en lugar de reservas.
         */
        $sql = "
            SELECT
                r.id,
                r.cliente_id,
                r.numero_personas,
                r.fecha_entrada,
                r.fecha_salida,
                r.estado,
                r.observaciones,

                CONCAT_WS(' ', c.nombre, c.apellido) AS cliente_nombre

            FROM reservas AS r
            INNER JOIN clientes AS c ON c.id = r.cliente_id
            WHERE r.fecha_entrada >= CURDATE()
                AND r.fecha_entrada < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                AND r.fecha_entrada >= NOW()
                AND r.estado NOT IN ('cancelada', 'atendida', 'no_asistio')
            ORDER BY r.fecha_entrada ASC
            LIMIT ?";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('Error preparando consulta de reservas: '  . $this->db->error);
        
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservationRows = [];

        while($row = $result->fetch_assoc()){
            $reservationRows[] = $row;
        }

        $stmt->close();
        if(empty($reservationRows))return [];

        /*
         * 2. Extraemos los IDs de las reservas encontradas.
         */
        $reservationIds = array_map(fn(array $row): int => (int) $row['id'], $reservationRows);

        /*
         * 3. Consultamos TODOS los recursos correspondientes
         * a esas reservas en una sola consulta.
         */
        $resourcesByReservation = $this->findResourcesByReservationIds($reservationIds);

        /*
         * 4. Construimos las entidades.
         */
        $reservations = [];

        foreach ($reservationRows as $row) {
            $reservationId = (int) $row['id'];
            $reservations[] = new Reservation(
                id: $reservationId,
                clientId: (int) $row['cliente_id'],
                numberOfPeople: $row['numero_personas'] !== null ? (int) $row['numero_personas'] : null,
                startDate: $row['fecha_entrada'],
                endDate: $row['fecha_salida'],
                status: $row['estado'],
                observations: $row['observaciones'],
                clientName: trim($row['cliente_nombre']),
                resources:$resourcesByReservation[$reservationId] ?? []
            );
        }

        return $reservations;
    }


    public function countRelevantForToday(): int{
        $sql = "
            SELECT COUNT(*) AS total
            FROM reservas AS r
            WHERE r.fecha_entrada >= CURDATE()
                AND r.fecha_entrada < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                AND r.fecha_entrada >= NOW()
                AND r.estado NOT IN ('cancelada', 'atendida', 'no_asistio')";

        $result = $this->db->query($sql);
        if(!$result)throw new \RuntimeException('Error consultando total de reservas: ' . $this->db->error);
        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }


    /**
     * @param int[] $reservationIds
     *
     * @return array<int, array<int, array{id:int, name:string}>>
     */
    private function findResourcesByReservationIds(array $reservationIds): array {
        if(empty($reservationIds))return [];
        $reservationIds = array_map('intval', $reservationIds);
        $placeholders = implode( ',', array_fill(0, count($reservationIds), '?'));

        $sql = "
            SELECT rr.reserva_id, rec.id AS recurso_id, rec.nombre AS recurso_nombre
            FROM reserva_recursos AS rr
            INNER JOIN recursos AS rec ON rec.id = rr.recurso_id
            WHERE rr.reserva_id IN ($placeholders)
            ORDER BY rec.nombre ASC";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)throw new \RuntimeException('Error preparando consulta de recursos de reservas: ' . $this->db->error);

        $types = str_repeat('i', count($reservationIds));
        $stmt->bind_param($types, ...$reservationIds);
        $stmt->execute();
        $result = $stmt->get_result();
        $resourcesByReservation = [];

        while($row = $result->fetch_assoc()){
            $reservationId = (int) $row['reserva_id'];
            $resourcesByReservation[$reservationId][] = ['id' => (int) $row['recurso_id'], 'name' => $row['recurso_nombre']];
        }

        $stmt->close();
        return $resourcesByReservation;
    }


    public function findCurrentByResourceIds(array $resourceIds, int $minutesBefore = 30): array {
        if(empty($resourceIds))return [];
        $resourceIds = array_map('intval', $resourceIds);

        $minutesBefore = max(0, $minutesBefore);
        $placeholders = implode(',', array_fill(0, count($resourceIds), '?'));

        $sql = "
            SELECT
                r.id,
                r.cliente_id,
                r.numero_personas,
                r.fecha_entrada,
                r.fecha_salida,
                r.estado,
                r.observaciones,
                CONCAT_WS(' ', c.nombre, c.apellido) AS cliente_nombre,
                rr.recurso_id
            FROM reservas AS r

            INNER JOIN clientes AS c ON c.id = r.cliente_id
            INNER JOIN reserva_recursos AS rr ON rr.reserva_id = r.id

            WHERE rr.recurso_id IN ($placeholders)
                AND r.estado NOT IN ('cancelada', 'atendida', 'no_asistio')
                AND NOW() >= DATE_SUB(r.fecha_entrada, INTERVAL ? MINUTE)
                AND NOW() < r.fecha_salida
            ORDER BY r.fecha_entrada ASC";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)throw new \RuntimeException('Error preparando consulta de reservas actuales: ' . $this->db->error);
        
        $types = str_repeat('i', count($resourceIds)) . 'i';
        $params = [...$resourceIds, $minutesBefore];
        $stmt->bind_param($types,  ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservationRows = [];
        $reservationIds = [];
        while ($row = $result->fetch_assoc()) {
            $resourceId = (int) $row['recurso_id'];

            /*
            * Si por alguna inconsistencia existen varias reservas
            * activas para el mismo recurso, nos quedamos con
            * la más próxima por fecha_entrada.
            */
            if(isset($reservationRows[$resourceId]))continue;

            $reservationRows[$resourceId] = $row;
            $reservationIds[] = (int) $row['id'];
        }

        $stmt->close();
        if(empty($reservationRows))return [];
        

        /*
        * Recuperamos todos los recursos de las reservas encontradas.
        */
        $reservationIds = array_values(array_unique($reservationIds));
        $resourcesByReservation = $this->findResourcesByReservationIds($reservationIds);
        $reservations = [];

        foreach($reservationRows as $resourceId => $row){
            $reservationId = (int) $row['id'];
            $reservations[$resourceId] =
                new Reservation(
                    id: $reservationId,
                    clientId: (int) $row['cliente_id'],
                    numberOfPeople: $row['numero_personas'] !== null ? (int) $row['numero_personas'] : null,
                    startDate: $row['fecha_entrada'],
                    endDate: $row['fecha_salida'],
                    status: $row['estado'],
                    observations: $row['observaciones'],
                    clientName: trim($row['cliente_nombre']),
                    resources: $resourcesByReservation[$reservationId] ?? []
                );
        }

        return $reservations;
    }


    public function findByDate(string $date): array{
        $sql = "
            SELECT
                r.id,
                r.cliente_id,
                r.numero_personas,
                r.fecha_entrada,
                r.fecha_salida,
                r.estado,
                r.observaciones,
                CONCAT_WS(' ', c.nombre, c.apellido) AS cliente_nombre
            FROM reservas AS r
            INNER JOIN clientes AS c ON c.id = r.cliente_id
            WHERE r.fecha_entrada >= ? AND r.fecha_entrada < DATE_ADD( ?, INTERVAL 1 DAY)
            ORDER BY r.fecha_entrada ASC, r.id ASC";

        $stmt = $this->db->prepare($sql);

        if(!$stmt)throw new \RuntimeException('Error preparando consulta de reservas por fecha: ' . $this->db->error);

        $stmt->bind_param('ss', $date, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $stmt->close();
        if(empty($rows))return [];

        /*
        * Recuperamos todos los recursos de las reservas
        * con una sola consulta.
        */
        $reservationIds = array_map(fn(array $row): int => (int) $row['id'], $rows);
        $resourcesByReservation = $this->findResourcesByReservationIds($reservationIds);

        $reservations = [];
        foreach($rows as $row){
            $reservationId = (int) $row['id'];
            $reservations[] = new Reservation(
                id: $reservationId,
                clientId: (int) $row['cliente_id'],
                numberOfPeople: $row['numero_personas'] !== null ? (int) $row['numero_personas'] : null,
                startDate: $row['fecha_entrada'],
                endDate: $row['fecha_salida'],
                status: $row['estado'],
                observations: $row['observaciones'],
                clientName:  trim($row['cliente_nombre']),
                resources: $resourcesByReservation[$reservationId] ?? []
            );
        }

        return $reservations;
    }


    public function create(int $clientId, int $numberOfPeople, string $startDate, string $endDate, string $status, ?string $observations): int {
        $sql = "INSERT INTO reservas (cliente_id, numero_personas, fecha_entrada, fecha_salida, estado, observaciones) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if(!$stmt)
            throw new \RuntimeException('Error preparando creación de reserva: ' . $this->db->error);
        $stmt->bind_param('iissss', $clientId, $numberOfPeople, $startDate, $endDate, $status, $observations);

        if(!$stmt->execute()){
            $error = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Error creando reserva: ' . $error);
        }

        $reservationId = (int) $this->db->insert_id;
        $stmt->close();
        return $reservationId;
    }


    public function attachResource(int $reservationId, int $resourceId, float $price = 0): void {
        $sql = "INSERT INTO reserva_recursos (reserva_id, recurso_id, precio) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('Error preparando asignación de recurso: ' . $this->db->error);
        $stmt->bind_param('iid', $reservationId, $resourceId, $price);

        if(!$stmt->execute()){
            $error = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Error asignando recurso a la reserva: ' . $error);
        }
        $stmt->close();
    }

}