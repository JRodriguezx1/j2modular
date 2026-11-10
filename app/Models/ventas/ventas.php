<?php
namespace App\Models\ventas;

class ventas extends \App\Models\ActiveRecord {
    protected static $tabla = 'ventas';
    protected static $columnasDB = ['id', 'idfactura', 'idventa_origen', 'idproducto', 'idusuarioanulada', 'tipoproducto', 'tipoproduccion', 'anulada', 'fecha_anulacion', 'motivo_anulacion', 'rendimientoestandar', 'nombreproducto', 'foto', 'anotacion', 'costo', 'valorunidad', 'cantidad', 'promediostock', 'percentcomision', 'valorcomision', 'subtotal', 'base', 'impuesto', 'valorimp', 'descuento', 'total', 'dato1', 'dato2'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idfactura = $args['idfactura'] ?? '';
        $this->idventa_origen = $args['idventa_origen'] ?? 'NULL';
        $this->idproducto = $args['idproducto'] ?? '';
        $this->idusuarioanulada = $args['idusuarioanulada'] ?? 'NULL';
        $this->tipoproducto = $args['tipoproducto'] ?? '';
        $this->tipoproduccion = $args['tipoproduccion']??0; // 0 = inmediato,  1 = construccion solo aplica para productos compuesto
        $this->anulada = $args['anulada']??0;
        $this->fecha_anulacion = $args['fecha_anulacion']??'NULL';
        $this->motivo_anulacion = $args['motivo_anulacion']??'NULL';
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
    // Requiere una transacción abierta y la factura previamente bloqueada.
    public static function bloquearLineaOrden(int $ordenId, int $lineaId): ?array{
        $db = self::getDB();
        // Consultamos también las anuladas para reconocer un segundo intento.
        $sql = "SELECT * FROM ventas WHERE id = ? AND idfactura = ? LIMIT 1 FOR UPDATE";
        $stmt = $db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('No fue posible preparar la consulta de la línea.');

        try{
            $stmt->bind_param('ii', $lineaId, $ordenId);
            if(!$stmt->execute())
                throw new \RuntimeException('No fue posible consultar la línea.');
            return $stmt->get_result()->fetch_assoc() ?: null;
        }finally{
            $stmt->close();
        }
    }


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
                WHERE v.idfactura = ? AND f.id_sucursal = ? AND f.estado = 'Abierta' AND (v.anulada IS NULL OR v.anulada = 0)
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


    // Marca una línea vigente sin borrar sus cantidades ni importes.
    // La factura y la línea deben estar bloqueadas previamente.
    public static function marcarLineaAnulada(int $ordenId, int $lineaId, int $usuarioId, string $motivo): void {
        $db = self::getDB();
        $sql = "UPDATE ventas SET anulada = 1, idusuarioanulada = ?, fecha_anulacion = NOW(), motivo_anulacion = ?
                WHERE id = ? AND idfactura = ? AND (anulada IS NULL OR anulada = 0)";
        $stmt = $db->prepare($sql);
        if(!$stmt)
            throw new \RuntimeException('No fue posible preparar la anulación.');

        try {
            $stmt->bind_param('isii', $usuarioId, $motivo, $lineaId, $ordenId);
            if(!$stmt->execute())
                throw new \RuntimeException('No fue posible anular la línea.');
            // Aquí sí esperamos una modificación: la línea debe pasar de vigente a anulada.
            if($stmt->affected_rows !== 1)
                throw new \RuntimeException('La línea ya no está disponible para anular.');
        } finally {
            $stmt->close();
        }
    }


    // Requiere una transacción abierta y la línea bloqueada previamente.
    public static function actualizarRestanteLineaOrden(int $ordenId, int $lineaId, array $restante): void {
        $db = self::getDB();
        $sql = "UPDATE ventas SET cantidad = ?, subtotal = ?, descuento = ?, total = ?, base = ?, valorimp = ?, valorcomision = ?
                WHERE id = ? AND idfactura = ? AND (anulada IS NULL OR anulada = 0)";
        $stmt = $db->prepare($sql);
        if(!$stmt)
            throw new \RuntimeException('No fue posible preparar la actualización de la línea.');

        try {
            // Mantener los decimales como texto, sin convertirlos a float.
            $guardado = $stmt->execute([$restante['cantidad'], $restante['subtotal'], $restante['descuento'], $restante['total'], $restante['base'], $restante['valorimp'], $restante['valorcomision'], $lineaId, $ordenId]);
            // En una anulación parcial la cantidad siempre cambia.
            if(!$guardado || $stmt->affected_rows!==1) 
                throw new \RuntimeException('No fue posible actualizar la cantidad restante.');
        } finally {
            $stmt->close();
        }
    }


    // Copia los datos originales y guarda los importes de la parte anulada.
    // Requiere la misma transacción y el bloqueo de la línea original.
    public static function insertarParteAnuladaOrden(int $ordenId, int $lineaId, array $anulada, int $usuarioId, string $motivo): int{
        $db = self::getDB();

        $sql = "INSERT INTO ventas (
                    idfactura, idventa_origen, idproducto,
                    idusuarioanulada, tipoproducto, tipoproduccion,
                    anulada, fecha_anulacion, motivo_anulacion,
                    rendimientoestandar, nombreproducto, foto, anotacion,
                    costo, valorunidad, cantidad, promediostock,
                    percentcomision, valorcomision, subtotal, base,
                    impuesto, valorimp, descuento, total, dato1, dato2
                )
                SELECT idfactura, id, idproducto, ?, tipoproducto, tipoproduccion, 1, NOW(), ?,
                    rendimientoestandar, nombreproducto, foto, anotacion,
                    costo, valorunidad, ?, promediostock,
                    percentcomision, ?, ?, ?, impuesto, ?, ?, ?, dato1, dato2
                FROM ventas
                WHERE id = ? AND idfactura = ? AND (anulada IS NULL OR anulada = 0)";
        $stmt = $db->prepare($sql);

        if(!$stmt)
            throw new \RuntimeException('No fue posible preparar el registro de la parte anulada.');

        try {
            $guardado = $stmt->execute([$usuarioId, $motivo, $anulada['cantidad'], $anulada['valorcomision'], $anulada['subtotal'], $anulada['base'], $anulada['valorimp'], $anulada['descuento'], $anulada['total'], $lineaId, $ordenId]);

            if(!$guardado || $stmt->affected_rows !== 1)
                throw new \RuntimeException('No fue posible guardar la parte anulada.');
            return (int)$db->insert_id;
        } finally {
            $stmt->close();
        }
    }

}