<?php

namespace App\Modules\Restaurant\Providers;

use App\Core\Container\Container;
use App\Modules\Restaurant\Domain\Repositories\RestaurantTableRepository;
use App\Modules\Restaurant\Domain\Repositories\ZoneRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlTableRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlZoneRepository;
use App\Modules\Restaurant\Domain\Repositories\OccupationRepository;
use App\Modules\Restaurant\Domain\Repositories\ReservationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlOccupationRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlReservationRepository;
use App\Modules\Restaurant\Domain\Repositories\CustomerRepository;
use App\Modules\Restaurant\Infrastructure\Repositories\MySqlCustomerRepository;

class RestaurantServiceProvider{

    public function register(Container $container): void{
        $container->bind(RestaurantTableRepository::class, MySqlTableRepository::class); //interfaz e implementacion de la interfaz
        $container->bind(ZoneRepository::class, MySqlZoneRepository::class);
        $container->bind(OccupationRepository::class, MySqlOccupationRepository::class);
        $container->bind(ReservationRepository::class, MySqlReservationRepository::class);
        $container->bind(CustomerRepository::class, MySqlCustomerRepository::class);
    }

}