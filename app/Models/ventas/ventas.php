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

    
    /////////// METODOS USADOS POR RESTAURANT /////////////
    public static function insertarLineaOrdenAbierta(array $linea): int{
        $columnas = [
            'idfactura', 'idproducto', 'tipoproducto', 'tipoproduccion',
            'rendimientoestandar', 'nombreproducto', 'foto', 'costo',
            'valorunidad', 'cantidad', 'promediostock', 'percentcomision',
            'valorcomision', 'subtotal', 'base', 'impuesto', 'valorimp',
            'descuento', 'total'
        ];

        $valores = [];
        foreach($columnas as $columna)$valores[] = $linea[$columna];

        $sql = 'INSERT INTO ventas (' . implode(', ', $columnas) . ') VALUES (' . implode(', ', array_fill(0, count($columnas), '?')) . ')';
        $db = self::getDB();
        $stmt = $db->prepare($sql);
        try{
            $stmt->execute($valores); // PHP 8.1+
            return (int)$db->insert_id;
        }finally{
            $stmt->close();
        }
    }


    public static function listarConsumosOrden(int $ordenId, int $sucursalId): array{
        if ($ordenId <= 0 || $sucursalId <= 0)
            throw new \InvalidArgumentException('La orden o la sucursal no es válida.');
        $db = self::getDB();
        if (!$db instanceof \mysqli)
            throw new \RuntimeException('No hay conexión disponible para consultar los consumos.');

        $sql = "SELECT v.id, v.idfactura, v.idproducto, v.nombreproducto,
                    v.cantidad, v.valorunidad, v.subtotal, v.base,
                    v.impuesto, v.valorimp, v.descuento, v.total, v.anotacion
                FROM ventas AS v
                INNER JOIN facturas AS f ON f.id = v.idfactura
                WHERE v.idfactura = ? AND f.id_sucursal = ? AND f.estado = 'Abierta'
                ORDER BY v.id ASC";
        $stmt = $db->prepare($sql);
        try {
            $stmt->bind_param('ii', $ordenId, $sucursalId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } finally {
            $stmt->close();
        }
    }

}