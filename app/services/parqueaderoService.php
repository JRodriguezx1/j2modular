<?php 

namespace App\services;


use App\Models\configuraciones\usuarios;
use App\Models\parqueadero\tipo_vehiculo;
use App\Models\sucursales;
use App\Repositories\parqueadero\tipoVehiculosRepository;
use stdClass;


class parqueaderoService{

    private $repoTipoVehiculo;
    
    public function __construct()
    {
        $this->repoTipoVehiculo = new tipoVehiculosRepository();
    }



    public function createUpdateTarifa(array $data):array{
        return $this->repoTipoVehiculo->upsert(new tipo_vehiculo($data));
    }


    public function allTarifas():array{
        return $this->repoTipoVehiculo->allActive();
    }
    

    
}