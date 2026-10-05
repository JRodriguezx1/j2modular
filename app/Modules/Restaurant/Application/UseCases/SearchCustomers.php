<?php

namespace App\Modules\Restaurant\Application\UseCases;

use App\Modules\Restaurant\Domain\Repositories\CustomerRepository;

class SearchCustomers
{
    public function __construct(private CustomerRepository $customerRepository)
    {}


    public function execute(string $term, int $limit = 10): array{
        $term = trim($term);
        if(mb_strlen($term) < 2)return [];
        $limit = max(1, min($limit, 20));
        return $this->customerRepository->search($term, $limit);
    }
    
}