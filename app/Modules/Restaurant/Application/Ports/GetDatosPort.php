<?php

namespace App\Modules\Restaurant\Application\Ports;

interface GetDatosPort{
    
    public function getParam(): array;
    public function getClientes(): array;
    public function getDepartments(): array;
    public function getCajas(): array;
    public function getConsecutivos(): array;
    public function getMediosPago(): array;
    public function getCanalesVenta(): array;
}