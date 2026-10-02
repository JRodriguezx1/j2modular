<?php

namespace App\Models\inventario;

class stockproductossucursal extends \App\Models\ActiveRecord{
    protected static $tabla = 'stockproductossucursal';
    protected static $columnasDB = ['id', 'productoid', 'sucursalid', 'stock', 'stockminimo', 'stockaux', 'habilitarventa', 'promediostock'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->productoid = $args['productoid']??'';
        $this->sucursalid = $args['sucursalid']??'';
        $this->stock = !empty($args['stock'])?$args['stock']:0;
        $this->stockminimo = !empty($args['stockminimo']) ? $args['stockminimo'] : 1;
        $this->stockaux = !empty($args['stockaux'])?$args['stockaux']:0;
        $this->promediostock = !empty($args['promediostock'])?$args['promediostock']:0;
        $this->habilitarventa = $args['habilitarventa']??1;
        $this->created_at = $args['created_at']??'';
    }

    public static function indicadoresAllProductsXSucursal(int $idsucursal = 1):array|NULL{
      $query="SELECT p.nombre, p.impuesto, p.tipoproducto, p.tipoproduccion, p.sku, sps.productoid, sps.stock, sps.stockminimo, sps.stockaux, p.precio_compra, p.precio_venta, p.idunidadmedida, und.nombre as unidadmedida, p.fecha_ingreso, p.visible, c.nombre as categoria, t.valorinv, t.cantidadreferencias, t.cantidadproductos, t.bajostock,t.productosagotados
              FROM stockproductossucursal sps JOIN productos p ON sps.productoid = p.id 
              JOIN categorias c ON p.idcategoria = c.id JOIN unidadesmedida und ON p.idunidadmedida = und.id
	            JOIN (SELECT 
                ROUND(SUM(CASE WHEN (p.tipoproducto = 0) OR (p.tipoproducto = 1 AND p.tipoproduccion = 1) THEN sps.stock * p.precio_compra ELSE 0 END), 2) AS valorinv, 
                COUNT(p.id) AS cantidadreferencias, 
    	          SUM(CASE WHEN ((p.tipoproducto = 0) OR (p.tipoproducto = 1 AND p.tipoproduccion = 1)) THEN sps.stock ELSE 0 END) AS cantidadproductos,
    	          SUM(CASE WHEN ((p.tipoproducto = 0) OR (p.tipoproducto = 1 AND p.tipoproduccion = 1)) AND sps.stock <= sps.stockminimo THEN 1 ELSE 0 END) AS bajostock,
    	          SUM(CASE WHEN ((p.tipoproducto = 0) OR (p.tipoproducto = 1 AND p.tipoproduccion = 1)) AND sps.stock = 0 THEN 1 ELSE 0 END) AS productosagotados
    	          FROM ".self::$tabla." sps JOIN productos p ON sps.productoid = p.id WHERE sps.sucursalid = $idsucursal AND p.visible = 1
              ) AS t WHERE sps.sucursalid = $idsucursal AND p.visible = 1;";
      $array = self::camposJoinObj($query);
      return $array;
    }

    public static function getStockproductosXsucursal():array|NULL{
      $sql = "SELECT p.id AS productoid, p.nombre AS nombreproducto, s.id AS sucursalid, s.nombre AS sucursal, sps.stock
              FROM stockproductossucursal sps
              INNER JOIN productos p ON sps.productoid = p.id
              INNER JOIN sucursales s ON sps.sucursalid = s.id
              WHERE p.tipoproducto = 0 OR (p.tipoproducto = 1 AND p.tipoproduccion = 1)
              ORDER BY p.id, s.id;";
      $array = self::camposJoinObj($sql);
      return $array;
    }
    

    //////////////  ITEMS EN BAJO STOCK  /////////////////
    ///////////////// llamada por api en /admin/api/getItemsBajoStock en almacen.ts
    public static function getProductosBajoStock(int $idsucursal = 1){
      $query="SELECT p.nombre, p.tipoproducto, p.tipoproduccion, p.sku, sps.productoid, sps.stock, sps.stockminimo, p.idunidadmedida, und.nombre as unidadmedida, p.visible
              FROM stockproductossucursal sps JOIN productos p ON sps.productoid = p.id 
              JOIN unidadesmedida und ON p.idunidadmedida = und.id
              WHERE ( p.tipoproducto = 0 OR (p.tipoproducto = 1 AND p.tipoproduccion = 1) ) AND sps.sucursalid = $idsucursal AND p.visible = 1 AND ( sps.stock = 0 OR sps.stock <= sps.stockminimo );";
      $array = self::camposJoinObj($query);
      return $array;
    }

}
