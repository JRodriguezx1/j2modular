<?php

namespace App\Modules\POS\Services;

use App\Models\parametrizacion\config_local;
use App\Models\clientes\departments;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\mediospago;
use App\Repositories\clientes\clientesRepository;
use App\Repositories\ventas\canalVentaRepository;

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

    public function getCajas(): array{
        $idsucursal =  filter_var(id_sucursal()??null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if($idsucursal === false)throw new \InvalidArgumentException('Sucursal no válida.');
        return caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
    }

    public function getConsecutivos(): array{
        $idsucursal =  filter_var(id_sucursal()??null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if($idsucursal === false)throw new \InvalidArgumentException('Sucursal no válida.');
        return consecutivos::whereArray(['id_sucursalid'=>$idsucursal, 'estado'=>1]);
    }

    public function getMediosPago(): array{
        return mediospago::whereArray(['estado'=>1]);
    }

    public function getCanalesVenta(): array{
        $canalesVentaRepo = new canalVentaRepository();
        return $canalesVentaRepo->all();
    }

}