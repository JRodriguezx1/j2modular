<?php

namespace App\Models\contracts;

interface mediosPagoContract{
    public function getPagoDestino():string;
    public function getEntityClass():string;
    public function crear_varios_reg_arrayobj(array $instance = []):array;
}