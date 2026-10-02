<?php

namespace App\Models\inventario;

class stockinsumossucursal extends \App\Models\ActiveRecord{
    protected static $tabla = 'stockinsumossucursal';
    protected static $columnasDB = ['id', 'subproductoid', 'sucursalid', 'stock', 'stockminimo', 'stockaux', 'promediostock'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->subproductoid = $args['subproductoid']??'';
        $this->sucursalid = $args['sucursalid']??'';
        $this->stock = $args['stock']??0;
        $this->stockminimo = $args['stockminimo']??0;
        $this->stockaux = !empty($args['stockaux'])?$args['stockaux']:0;
        $this->promediostock = !empty($args['promediostock'])?$args['promediostock']:0;
        $this->created_at = $args['created_at']??'';
    }

    public static function indicadoresAllSubproductsXSucursal(int $idsucursal = 1):array|NULL{
      $query="SELECT sp.nombre, sis.subproductoid, sis.stock, sis.stockminimo, sis.stockaux, sp.precio_compra, sp.unidadmedida, sp.fecha_ingreso, 
      SUM(sis.stock*sp.precio_compra) OVER () AS valorinv, 
      COUNT(sp.id) OVER () AS cantidadreferencias, 
      SUM(sis.stock) OVER () AS cantidadproductos,
      SUM(CASE WHEN sis.stock <= sis.stockminimo THEN 1 ELSE 0 END) OVER () AS bajostock,
      SUM(CASE WHEN sis.stock = 0 THEN 1 ELSE 0 END) OVER () AS productosagotados
      FROM ".self::$tabla." sis JOIN subproductos sp ON sis.subproductoid = sp.id WHERE sis.sucursalid = $idsucursal;";
      $array = self::camposJoinObj($query);
      return $array;
    }

    public static function getStockinsumosXsucursal():array|NULL{
      $sql = "SELECT sp.id AS subproductoid, sp.nombre AS nombreproducto, s.id AS sucursalid, s.nombre AS sucursal, sis.stock
              FROM stockinsumossucursal sis
              INNER JOIN subproductos sp ON sis.subproductoid = sp.id
              INNER JOIN sucursales s ON sis.sucursalid = s.id
              ORDER BY sp.id, s.id;";
      $array = self::camposJoinObj($sql);
      return $array;
    }

    public static function getInsumosBajoStock(int $idsucursal = 1):array|NULL{
      $query="SELECT sp.id AS subproductoid, sp.nombre, sp.sku, sis.stock, sis.stockminimo,
              sp.id_unidadmedida AS idunidadmedida, sp.unidadmedida, 1 AS visible
              FROM ".self::$tabla." sis JOIN subproductos sp ON sis.subproductoid = sp.id
              WHERE sis.sucursalid = $idsucursal AND sis.stock <= sis.stockminimo;";
      return self::camposJoinObj($query);
    }

}
