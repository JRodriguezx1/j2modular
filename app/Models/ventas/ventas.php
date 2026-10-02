<?php
namespace App\Models\ventas;

class ventas extends \App\Models\ActiveRecord {
    protected static $tabla = 'ventas';
    protected static $columnasDB = ['id', 'idfactura', 'idproducto', 'tipoproducto', 'tipoproduccion', 'rendimientoestandar', 'nombreproducto', 'foto', 'anotacion', 'costo', 'valorunidad', 'cantidad', 'promediostock', 'percentcomision', 'valorcomision', 'subtotal', 'base', 'impuesto', 'valorimp', 'descuento', 'total', 'dato1', 'dato2'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idfactura = $args['idfactura'] ?? '';
        $this->idproducto = $args['idproducto'] ?? '';
        $this->tipoproducto = $args['tipoproducto'] ?? '';
        $this->tipoproduccion = $args['tipoproduccion']??0; // 0 = inmediato,  1 = construccion solo aplica para productos compuesto
        $this->rendimientoestandar = $args['rendimientoestandar']??1;
        $this->nombreproducto = $args['nombreproducto'] ?? '';
        $this->foto = $args['foto'] ?? '';
        $this->anotacion = $args['anotacion'] ?? '';
        $this->costo = $args['costo'] ?? '';
        $this->valorunidad = $args['valorunidad'] ?? '';
        $this->cantidad = $args['cantidad'] ?? '';
        $this->promediostock = $args['promediostock'] ?? 0;
        $this->percentcomision = $args['percentcomision'] ?? 0;
        $this->valorcomision = $args['valorcomision'] ?? 0;
        $this->subtotal = $args['subtotal'] ?? 0;
        $this->base = $args['base'] ?? 0;
        $this->impuesto = $args['impuesto'] ?? '0';
        $this->valorimp = $args['valorimp'] ?? '0';
        $this->descuento = $args['descuento'] ?? '';
        $this->total = $args['total'] ?? '';
        $this->dato1 = $args['dato1 '] ?? '';
        $this->dato2 = $args['dato2'] ?? '';
    }

    // Validación para venta nueva
    public function validar_nueva_venta():array {
        if(!$this->idfactura)self::$alertas['error'][] = 'La factura es obligatoria';
        
        if(!$this->idproducto)self::$alertas['error'][] = 'El producto es obligatorio';
        
        if(!$this->cantidad)self::$alertas['error'][] = 'La cantidad es obligatoria';

        if(strlen($this->cantidad)>9999)self::$alertas['error'][] = 'Maxima cantidad 9999';

        return self::$alertas;
    }
}
