<?php

namespace App\Modules\POS\Services;

use App\Models\parametrizacion\config_local;
use App\Models\clientes\departments;
use App\Repositories\clientes\clientesRepository;

class GetDatosService{

    //consulta los parametros locales de la sucursal
    public function getParametros(): array{
        $conflocal = config_local::getParamCaja();
        return $conflocal;
    }

    public function getClientes(): array{
        $clientes = (new clientesRepository())->all();
        return $clientes;
    }

    public function getDepartments(): array{
        $departments = departments::all();
        return $departments;
    }

}