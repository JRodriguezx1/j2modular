<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Account;

interface AccountRepository{

    public function findByOccupationId(int $occupationId): array;

    public function findById(int $accountId): ?Account;

    public function create(int $occupationId, string $name = 'General'): int;
    
}