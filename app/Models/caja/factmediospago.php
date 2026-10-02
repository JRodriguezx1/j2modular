<?php

namespace App\Models\caja;

use App\Models\contracts\mediosPagoContract;

class factmediospago extends \App\Models\ActiveRecord{
    protected static $tabla = 'factmediospago';
    protected static $columnasDB = ['id', 'cierrecajaid', 'id_factura', 'idcuota', 'idmediopago', 'valor'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->cierrecajaid = $args['cierrecajaid']??'';
        $this->id_factura = $args['id_factura']??'';
        $this->idcuota = $args['idcuota']??'';
        $this->idmediopago = $args['idmediopago']??'';
        $this->valor = $args['valor']??'';
    }


    public function validar():array
    {
        if(!$this->idmediopago)self::$alertas['error'][] = "Error intenta nuevamnete";
        if(strlen($this->id_factura)>31)self::$alertas['error'][] = "Error intenta nuevamnete";
        return self::$alertas;
    }


    /** Obtiene y bloquea los medios de pago asociados a una cuota. */
    public static function obtenerPorCuotaParaActualizar(int $idcuota):array
    {
        if($idcuota <= 0)return [];
        return self::consultar_sql(
            "SELECT * FROM ".static::$tabla." WHERE idcuota = {$idcuota} FOR UPDATE"
        );
    }

    /**
     * Obtiene y bloquea los pagos de una factura durante un cambio de medios.
     * El orden estable por ID reduce inconsistencias entre solicitudes
     * concurrentes que intenten modificar la misma factura.
     */
    public static function obtenerPorFacturaParaActualizar(int $facturaId):array
    {
        if($facturaId <= 0)return [];
        return self::consultar_sql(
            "SELECT * FROM ".static::$tabla
            ." WHERE id_factura = {$facturaId} ORDER BY id ASC FOR UPDATE"
        );
    }
    
    public function pagoDestino(int $id):void{
        $this->id_factura = $id;
    }
}

?>
