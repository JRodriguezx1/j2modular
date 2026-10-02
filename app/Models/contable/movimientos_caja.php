<?php

namespace App\Models\contable;

class movimientos_caja 
{
    public ?int $id;
    public int $id_sucursal;
    public int $fk_tipo_movimientocaja;
    public int $fk_tipo_documento;
    public ?int $id_documento;  //id de la factura, id de la cuota etc
    public int $fk_tipo_tercero;
    public ?int $id_tercero; //id del cliente, o deun proveedor o banco
    public int $fk_caja;
    public int $fk_usuario;
    public string $naturaleza;
    public string $numero_documento;  //POS15, FE1015
    public int $num_orden;
    public float $valor;
    public string $referencia;
    public string $fecha;
    public string $fecha_anulacion;
    public string $concepto;
    public string $observacion;
    public int $estado;

    public function __construct(array $datos = []) 
    {
    $this->id                       = $datos['id'] ?? null;
    $this->id_sucursal              = $datos['id_sucursal'] ?? id_sucursal();
    $this->fk_tipo_movimientocaja   = $datos['fk_tipo_movimientocaja'] ?? '';
    $this->fk_tipo_documento        = $datos['fk_tipo_documento'] ?? '';
    $this->id_documento             = $datos['id_documento'] ?? '';
    $this->fk_tipo_tercero          = $datos['fk_tipo_tercero'] ?? '';
    $this->id_tercero               = $datos['id_tercero'] ?? '';
    $this->fk_caja                  = $datos['fk_caja'] ?? '';
    $this->fk_usuario               = $datos['fk_usuario'] ?? '';
    $this->naturaleza               = $datos['naturaleza'] ?? 'I';
    $this->numero_documento         = $datos['numero_documento'] ?? '';
    $this->num_orden                = $datos['num_orden'] ?? '';
    $this->valor                    = $datos['valor'] ?? 0;
    $this->referencia               = $datos['referencia'] ?? '';
    $this->fecha                    = $datos['fecha'] ?? date('Y-m-d H:i:s');
    $this->fecha_anulacion          = $datos['fecha_anulacion'] ?? '';
    $this->concepto                 = $datos['concepto'] ?? 'PAGO DE CONTADO A FACTURA';
    $this->observacion              = $datos['observacion'] ?? '';
    $this->estado                   = (int)($datos['estado'] ?? 1);
    }

    
}