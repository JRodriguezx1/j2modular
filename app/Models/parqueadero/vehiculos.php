<?php

namespace App\Models\parqueadero;

class vehiculos
{
    public ?int $id;
    public int $tipo_vehiculo_id;
    public int $cliente_id;
    public string $placa;
    public ?string $propietario;
    public ?string $telefono;

    public function __construct(array $datos = []) 
    {
        $this->id = $datos['id'] ?? null;
        // Limpiamos y estandarizamos la placa (ej: "aaa-123" -> "AAA123")
        $this->tipo_vehiculo_id = $datos['tipo_vehiculo_id'] ?? 0;
        $this->cliente_id = $datos['cliente_id'] ?? null;
        $this->placa = strtoupper(str_replace([' ', '-'], '', $datos['placa'] ?? ''));
        $this->propietario = $datos['propietario'] ?? null;
        $this->telefono = $datos['telefono'] ?? null;
    }
}