<?php

namespace App\Modules\Restaurant\Application\UseCases;

use InvalidArgumentException;

use App\Modules\Restaurant\Application\DTO\ActiveOccupationDetail; //DTO.
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;

//GetActiveOccupationDetail responde: ¿Qué información necesita la interfaz para mostrar la atención actual de esta mesa?
class GetActiveOccupationDetail{

    public function __construct(private OccupationRepository $occupationRepository, private ReservationRepository $reservationRepository)
    {}

    public function execute(int $resourceId): ActiveOccupationDetail{
        if($resourceId <= 0)
            throw new InvalidArgumentException('El recurso indicado no es válido.');
        $occupation = $this->occupationRepository->findCurrentByResourceId($resourceId);
        if(!$occupation)
            throw new InvalidArgumentException('La mesa no tiene una atención activa.');

        $clientName = null;
        $numberOfPeople = null;
        /*
         * Si la ocupación nació de una reserva,
         * aprovechamos la información de esa reserva.
         */
        if($occupation->getReservationId() !== null){
            $reservation = $this->reservationRepository->findById($occupation->getReservationId());
            if($reservation){
                $clientName = $reservation->getClientName();
                $numberOfPeople = $reservation->getNumberOfPeople();
            }
        }

        return new ActiveOccupationDetail(
            occupationId: $occupation->getId(),
            resourceId: $resourceId,
            reservationId: $occupation->getReservationId(),
            clientId: $occupation->getClientId(),
            clientName: $clientName,
            type: $occupation->getType(),
            startDate: $occupation->getStartDate(),
            estimatedEndDate: $occupation->getEstimatedEndDate(),
            status: $occupation->getStatus(),
            numberOfPeople: $numberOfPeople,
            observations: $occupation->getObservations()
        );
    }

}