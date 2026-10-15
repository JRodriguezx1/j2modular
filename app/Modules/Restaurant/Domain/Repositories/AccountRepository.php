<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Account;

interface AccountRepository{

    public function findByOccupationId(int $occupationId): array;
    public function findById(int $accountId): ?Account;
    public function findByIdForUpdate(int $accountId): ?Account;
    public function linkOpenOrder(int $accountId, int $orderId): void;
    public function create(int $occupationId, string $name = 'General'): int;
    public function createMain(int $occupationId): int;
    public function findMainByOccupationId(int $occupationId): ?Account;  //consultar la cuenta principal:
}
