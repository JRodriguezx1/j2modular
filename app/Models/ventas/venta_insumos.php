<?php

namespace App\Models\ventas;

class venta_insumos extends \App\Models\ActiveRecord{
    protected static $tabla = 'venta_insumos';
    protected static $columnasDB = ['id', 'venta_id', 'subproducto_id', 'grupo_insumo_id', 'nombre_insumo', 'cantidad_configurada', 'cantidad_consumida', 'stockaux_consumido', 'es_fijo', 'tipo_variacion'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->venta_id = $args['venta_id']??'';
        $this->subproducto_id = $args['subproducto_id']??'';
        $this->grupo_insumo_id = $args['grupo_insumo_id']??'';
        $this->nombre_insumo = $args['nombre_insumo']??'';
        $this->cantidad_configurada = $args['cantidad_configurada']??0;
        $this->cantidad_consumida = $args['cantidad_consumida']??0;
        $this->stockaux_consumido = $args['stockaux_consumido']??0;
        $this->es_fijo = $args['es_fijo']??0;
        $this->tipo_variacion = $args['tipo_variacion']??NULL;
        $this->created_at = $args['created_at']??'';
    }

    public function validar():array
    {
        if(!$this->venta_id)self::$alertas['error'][] = "Error, el detalle de venta es obligatorio";
        if(strlen($this->nombre_insumo)>99)self::$alertas['error'][] = "El nombre de insumo es muy extenso > 99 caracteres";
        return self::$alertas;
    }

    /**
     * Recupera el detalle historico junto con los datos del grupo necesarios
     * para reconstruir el carrito de una cotizacion.
     */
    public static function detallesPorVentas(array $idsVenta):array
    {
        $idsVenta = array_values(array_unique(array_filter(
            array_map('intval', $idsVenta),
            static fn(int $id):bool => $id > 0
        )));
        if(empty($idsVenta))return [];

        $ids = implode(', ', $idsVenta);
        $sql = "SELECT vi.*,
                       gi.nombre AS nombre_grupo,
                       gi.minimo AS minimo_grupo,
                       gi.maximo AS maximo_grupo
                FROM venta_insumos vi
                LEFT JOIN grupos_insumos gi ON gi.id = vi.grupo_insumo_id
                WHERE vi.venta_id IN ($ids)
                ORDER BY vi.venta_id, vi.id;";

        return self::camposJoinObj($sql);
    }

}
