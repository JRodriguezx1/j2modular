<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Repositories\AccountRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;

class CreateAdditionalAccount{

    public function __construct(private AccountRepository $accountRepository, private OccupationRepository $occupationRepository)
    {}

    public function execute(int $occupationId, string $name): int{
        if($occupationId <= 0)
            throw new InvalidArgumentException('La ocupación indicada no es válida.');
        $name = trim($name);

        if($name === '')
            throw new InvalidArgumentException('Debes indicar el nombre de la cuenta.');

        if(mb_strlen($name) > 100)
            throw new InvalidArgumentException('El nombre de la cuenta no puede superar los 100 caracteres.');
        $occupation = $this->occupationRepository->findById($occupationId);

        if($occupation === null)
            throw new InvalidArgumentException('La ocupación no existe.');
        
        if($occupation->getStatus() !== 'en_uso')
            throw new InvalidArgumentException('Solo se pueden crear cuentas en ocupaciones activas.');

        return $this->accountRepository->create(occupationId: $occupationId, name: $name);
    }
    
}