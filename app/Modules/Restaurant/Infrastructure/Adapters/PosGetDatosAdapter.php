<?php

namespace App\Modules\Restaurant\Infrastructure\Adapters;

use App\Modules\POS\Services\GetDatosService;
use App\Modules\Restaurant\Application\Ports\GetDatosPort;

class PosGetDatosAdapter implements GetDatosPort{

    public function __construct(private GetDatosService $getDatosService)
    {}

    public function getParam(): array{
        return $this->getDatosService->getParametros();
    }

    public function getClientes(): array{
        return $this->getDatosService->getClientes();
    }

    public function getDepartments(): array{
        return $this->getDatosService->getDepartments();
    }

    public function getCajas(): array{
        return $this->getDatosService->getCajas();
    }

    public function getConsecutivos(): array{
        return $this->getDatosService->getConsecutivos();
    }

    public function getMediosPago(): array{
        return $this->getDatosService->getMediosPago();
    }

    public function getCanalesVenta(): array{
        return $this->getDatosService->getCanalesVenta();
    }
    
}