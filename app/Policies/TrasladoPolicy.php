<?php

namespace App\Policies;

use App\Models\inventario\traslado_inv;
use DomainException;

/**
 * Reglas de autorizacion y direccion fisica para traslados de inventario.
 */
final class TrasladoPolicy{

    private const TIPO_SOLICITUD = 'Solicitud';
    private const TIPO_SALIDA = 'Salida';
    private const ESTADO_PENDIENTE = 'pendiente';
    private const ESTADO_EN_TRANSITO = 'entransito';

    public function esTipoValido(string $tipo): bool{
        return in_array($tipo, [self::TIPO_SOLICITUD, self::TIPO_SALIDA], true);
    }

    public function esParticipante(traslado_inv $orden, int $sucursalId): bool{
        if($sucursalId <= 0)return false;
        return $sucursalId === (int)$orden->id_sucursalorigen || $sucursalId === (int)$orden->id_sucursaldestino;
    }

    public function obtenerSucursalDespacho(traslado_inv $orden): int{
        $this->validarTipo($orden);
        return $orden->tipo === self::TIPO_SALIDA ? (int)$orden->id_sucursalorigen : (int)$orden->id_sucursaldestino;
    }

    public function obtenerSucursalRecepcion(traslado_inv $orden): int{
        $this->validarTipo($orden);
        return $orden->tipo === self::TIPO_SALIDA ? (int)$orden->id_sucursaldestino : (int)$orden->id_sucursalorigen;
    }

    public function puedeEditar(traslado_inv $orden, int $sucursalId): bool{
        return $this->esTipoValido((string)$orden->tipo)
            && $orden->estado === self::ESTADO_PENDIENTE
            && $sucursalId > 0
            && $sucursalId === (int)$orden->id_sucursalorigen;
    }

    public function puedeCancelar(traslado_inv $orden, int $sucursalId): bool{
        return $this->puedeEditar($orden, $sucursalId);
    }

    public function puedeRechazar(traslado_inv $orden, int $sucursalId): bool{
        return $this->esTipoValido((string)$orden->tipo)
            && $orden->estado === self::ESTADO_PENDIENTE
            && $sucursalId > 0
            && $sucursalId === (int)$orden->id_sucursaldestino;
    }

    public function puedeDespachar(traslado_inv $orden, int $sucursalId): bool{
        if(!$this->esTipoValido((string)$orden->tipo)
            || $orden->estado !== self::ESTADO_PENDIENTE
            || $sucursalId <= 0
        )return false;
        return $sucursalId === $this->obtenerSucursalDespacho($orden);
    }

    public function puedeRecibir(traslado_inv $orden, int $sucursalId): bool{
        if(!$this->esTipoValido((string)$orden->tipo)
            || $orden->estado !== self::ESTADO_EN_TRANSITO
            || $sucursalId <= 0
        )return false;
        return $sucursalId === $this->obtenerSucursalRecepcion($orden);
    }

    private function validarTipo(traslado_inv $orden): void{
        if(!$this->esTipoValido((string)$orden->tipo))throw new DomainException('El tipo de traslado no es valido.');
    }
    
}