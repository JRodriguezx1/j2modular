<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Domain\Entities\Occupation;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;

//GetCurrentOccupation responde: ¿Cuál es la ocupación activa de este recurso?
class GetCurrentOccupation{

    public function __construct(private OccupationRepository $occupationRepository)
    {}

    public function execute( int $resourceId): Occupation{
        if($resourceId <= 0)
            throw new InvalidArgumentException('El recurso indicado no es válido.');
        $occupation = $this->occupationRepository->findCurrentByResourceId($resourceId);

        if(!$occupation)throw new InvalidArgumentException('La mesa no tiene una atención activa.');
        return $occupation;
    }
}