<?php

namespace App\Modules\Restaurant\Domain\Repositories;

use App\Modules\Restaurant\Domain\Entities\Customer;

interface CustomerRepository{
    /**
     * @return Customer[]
     */
    public function search(string $term, int $limit = 10): array;
    
    public function exists(
    int $customerId
): bool;
}