<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Reservation;

interface ReservationRepository{
    /**
     * @return Reservation[]
     */
    public function findUpcomingForToday(int $limit = 5): array;
    public function countRelevantForToday(): int;
    /**
     * Busca las reservas que afectan actualmente
     * a los recursos indicados.
     * @param int[] $resourceIds
     * @return array<int, Reservation>
     *         keyed by resourceId
     */
    public function findCurrentByResourceIds(array $resourceIds, int $minutesBefore = 30): array;
    public function findByDate(string $date): array;
    public function create(int $clientId, int $numberOfPeople, string $startDate, string $endDate, string $status, ?string $observations): int;
    public function attachResource(int $reservationId, int $resourceId, float $price = 0): void;
}