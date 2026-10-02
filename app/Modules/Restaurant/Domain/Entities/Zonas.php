<?php

namespace App\Modules\Restaurant\Domain\Entities;

class Zonas{
    public function __construct(private int $id, private string $nombre, private bool $activo)
    {}

    public function getId():int{
        return $this->id;
    }

    public function getName():string{
        return $this->nombre;
    }

    public function isActive():bool{
        return $this->activo;
    }

}