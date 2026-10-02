<?php

namespace App\Models\parqueadero;

class parqueos
{
    public ?int $id;
    public int $vehiculo_id;
    public string $fechaingreso;
    public ?string $fechasalida;
    public ?float $horas;
    public ?float $valor;
    public string $estado;
    public ?string $observacion;

    // Atributos mapeados de la relación (opcionales)
    public ?float $tarifaHora;
    public ?float $tarifaDia;

    public function __construct(array $datos = []) 
    {
        $this->id = $datos['id'] ?? null;
        $this->vehiculo_id = $datos['vehiculo_id'] ?? 0;
        $this->fechaingreso = $datos['fecha_hora_ingreso'] ?? '';
        $this->fechasalida = $datos['fecha_hora_salida'] ?? null;
        $this->horas = isset($datos['horas']) ? (float)$datos['horas'] : null;
        $this->valor = isset($datos['valor']) ? (float)$datos['valor'] : null;
        $this->estado = $datos['estado'] ?? 'ACTIVO';
        $this->observacion = $datos['observacion'] ?? null;
        
        $this->tarifaHora = isset($datos['tarifa_hora']) ? (float)$datos['tarifa_hora'] : null;
        $this->tarifaDia = isset($datos['tarifa_dia']) ? (float)$datos['tarifa_dia'] : null;
    }

    
    /**
     * Lógica de negocio encapsulada en el Modelo.
     * El modelo calcula su propia tarifa si conoce las horas.
     */
    public function calcularLiquidacion(): void 
    {
        if ($this->estado !== 'ACTIVO') {
            throw new \Exception("El parqueo ya no está activo.");
        }

        $ingreso = new \DateTime($this->fechaingreso);
        $salida = new \DateTime(); // Ahora mismo
        
        $intervalo = $ingreso->diff($salida);
        $horasTotales = ($intervalo->days * 24) + $intervalo->h + ($intervalo->i / 60);
        
        $horasACobrar = ceil($horasTotales);
        if ($horasACobrar == 0) $horasACobrar = 1;

        // Asignamos los valores calculados al modelo
        $this->fechasalida = $salida->format('Y-m-d H:i:s');
        $this->horas = round($horasTotales, 2);
        $this->valor = $horasACobrar * $this->tarifaHora;
        $this->estado = 'FINALIZADO';
    }
}