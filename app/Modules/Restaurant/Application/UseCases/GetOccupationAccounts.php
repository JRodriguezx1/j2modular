<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;

class GetOccupationAccounts{

    public function __construct(private AccountRepository $accountRepository, private OccupationRepository $occupationRepository)
    {}

    public function execute(int $occupationId): array{
        if($occupationId <= 0)
            throw new InvalidArgumentException('La ocupación indicada no es válida.');
        

        $occupation = $this->occupationRepository->findById($occupationId);

        if($occupation === null)
            throw new InvalidArgumentException('La ocupación no existe.');
        

        $accounts = $this->accountRepository->findByOccupationId($occupationId);

        $result = [];

        foreach ($accounts as $account) {
            $result[] = [
                'id' => $account->getId(),
                'occupationId' => $account->getOccupationId(),
                'invoiceId' => $account->getInvoiceId(),
                'name' => $account->getName(),
                'status' => $account->getStatus()
            ];
        }

        return [
            'occupationId' => $occupation->getId(),
            'occupationStatus' => $occupation->getStatus(),
            'accounts' => $result
        ];
    }
}