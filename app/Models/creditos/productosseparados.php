<?php
namespace App\Models\creditos;

class productosseparados {
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idcredito = $args['idcredito'] ?? '';
        $this->idproducto = $args['idproducto'] ?? '';
        $this->tipoproducto = $args['tipoproducto'] ?? '';
        $this->tipoproduccion = $args['tipoproduccion']??0; // 0 = inmediato,  1 = construccion solo aplica para productos compuesto
        $this->promediostock = $args['promediostock']??0;
        $this->rendimientoestandar = $args['rendimientoestandar']??1;
        $this->nombreproducto = $args['nombreproducto'] ?? '';
        $this->foto = $args['foto'] ?? '';
        $this->costo = $args['costo'] ?? 0;
        $this->valorunidad = $args['valorunidad'] ?? 0;
        $this->cantidad = $args['cantidad'] ?? '';
        $this->subtotal = $args['subtotal'] ?? 0;
        $this->base = $args['base'] ?? 0;
        $this->impuesto = $args['impuesto'] ?? '0';
        $this->valorimp = $args['valorimp'] ?? '0';
        $this->descuento = $args['descuento'] ?? '';
        $this->total = $args['total'] ?? '';
        $this->created_at = $args['created_at']??'';
    }

    // Validación para venta nueva
    public function validar_nueva_venta():array {
        $alertas = [];
        if(!$this->idcredito)$alertas['error'][] = 'La factura es obligatoria';
        
        if(!$this->idproducto)$alertas['error'][] = 'El producto es obligatorio';
        
        if(!$this->cantidad)$alertas['error'][] = 'La cantidad es obligatoria';

        if(strlen($this->cantidad)>9999)$alertas['error'][] = 'Maxima cantidad 9999';

        return $alertas;
    }


    public function toArray():array {
        return [
            //'id' => $this->id, 
            'idcredito' => $this->idcredito, 
            'idproducto' => $this->idproducto, 
            'tipoproducto' => $this->tipoproducto, 
            'tipoproduccion' => $this->tipoproduccion, 
            'promediostock' => $this->promediostock,
            'rendimientoestandar' => $this->rendimientoestandar, 
            'nombreproducto' => $this->nombreproducto, 
            'foto' => $this->foto, 
            'costo' => $this->costo, 
            'valorunidad' => $this->valorunidad, 
            'cantidad' => $this->cantidad, 
            'subtotal' => $this->subtotal, 
            'base' => $this->base, 
            'impuesto' => $this->impuesto, 
            'valorimp' => $this->valorimp, 
            'descuento' => $this->descuento, 
            'total' => $this->total,
            //'created_at' => $this->created_at
        ];
    }

}