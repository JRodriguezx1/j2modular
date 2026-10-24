<?php

namespace App\Modules\Restaurant\Application\Ports;

interface GetDatosPort{
    
    public function getParam(): array;
    public function getClientes(): array;

}