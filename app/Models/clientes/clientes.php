<?php
namespace App\Models\clientes;

class clientes extends \App\Models\ActiveRecord {
    protected static $tabla = 'clientes';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'tipodocumento', 'identificacion', 'telefono', 'email', 'fecha_nacimiento', 'total_compras', 'ultima_compra', 'totaldebe', 'limitecredito', 'puntos', 'data1'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = !empty($args['apellido'])?$args['apellido']:' ';
        $this->tipodocumento = $args['tipodocumento'] ?? '3';
        $this->identificacion = $args['identificacion'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->fecha_nacimiento = $args['fecha_nacimiento'] ?? '';
        $this->total_compras = $args['total_compras'] ?? 0;
        $this->ultima_compra = $args['ultima_compra'] ?? '';
        $this->totaldebe = $args['totaldebe'] ?? 0;
        $this->limitecredito = $args['limitecredito'] ?? 100000;
        $this->puntos = $args['puntos'] ?? 0;
        $this->data1 = $args['data1'] ?? '';
        $this->created_at = $args['created_at']??'';
    }

    /** Columnas controladas que los repositorios pueden persistir. */
    public function toArray():array
    {
        return [
            'nombre'=>$this->nombre,
            'apellido'=>$this->apellido,
            'tipodocumento'=>$this->tipodocumento,
            'identificacion'=>$this->identificacion,
            'telefono'=>$this->telefono,
            'email'=>$this->email,
            'fecha_nacimiento'=>$this->fecha_nacimiento,
            'total_compras'=>$this->total_compras,
            'ultima_compra'=>$this->ultima_compra,
            'totaldebe'=>$this->totaldebe,
            'limitecredito'=>$this->limitecredito,
            'data1'=>$this->data1,
        ];
    }

    // Validación para clientes nuevos
    public function validar_nuevo_cliente():array {
        if(!$this->nombre)self::$alertas['error'][] = 'El Nombre del cliente es Obligatorio';
        
        if(strlen($this->nombre)>45)self::$alertas['error'][] = 'Has excecido el limite de caracteres';
        
        if(strlen($this->apellido)>32)self::$alertas['error'][] = 'El apellido del cliente no debe ir vacio o ser mayor a 32 digitos';
        
        //if(!$this->identificacion)self::$alertas['error'][] = 'La identificacion del cliente es Obligatorio';

        //if(strlen($this->identificacion)<7 || strlen($this->identificacion)>11)self::$alertas['error'][] = 'La identificacion no debe ser menor a 7 digitos o mayor a 11 digitos';
        
        if(!$this->telefono)self::$alertas['error'][] = 'El telefono del cliente es Obligatorio';

        if(strlen($this->telefono) >30)self::$alertas['error'][] = 'El dato del telefono no puede superar los 30 caracteres';

        if($this->email)if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) self::$alertas['error'][] = 'Email no válido';

       // if(strlen($this->direccion)>74)self::$alertas['error'][] = 'Direccion muy larga';

        return self::$alertas;
    }
    

    public static function indicadoresVentasXcliente(int $idcliente, int $idsucursal = 1):object|NULL{
        $query = "SELECT
                    COUNT(f.id) AS cantidad_ventas,
                    SUM(f.total) AS total_ventas_cliente,
                    AVG(f.total) AS ticket_promedio
                FROM facturas f
                WHERE f.fechapago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND f.id_sucursal = $idsucursal AND f.idcliente = $idcliente AND f.estado = 'Paga';";
        $array = self::camposJoinObj($query);
        return array_shift($array);
    }

    public static function comprasXMesXCliente(int $idcliente, int $idsucursal = 1):array|NULL{
        $query = "SELECT
                    DATE_FORMAT(f.fechapago, '%Y-%m') as periodo,
                    SUM(f.total) AS ventas_totales
                FROM facturas f
                WHERE f.fechapago >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND f.id_sucursal = $idsucursal AND f.idcliente = $idcliente AND f.estado = 'Paga'
                GROUP BY DATE_FORMAT(f.fechapago, '%Y-%m')
                ORDER BY periodo";
        $array = self::camposJoinObj($query);
        return $array;
    }

    public static function ventasXCategoriasXCliente(int $idcliente, int $idsucursal = 1):array|NULL{
        $query = "SELECT
                    p.idcategoria,
                    c.nombre AS categoria,
                    SUM(v.cantidad) AS unidades_vendidas,
                    SUM(v.total) AS venta_total_categoria
                FROM facturas f
                JOIN ventas v ON v.idfactura = f.id
                JOIN productos p ON p.id = v.idproducto
                LEFT JOIN categorias c ON c.id = p.idcategoria
                WHERE f.id_sucursal = $idsucursal AND f.idcliente = $idcliente AND f.estado = 'Paga'
                GROUP BY p.idcategoria, c.nombre
                ORDER BY venta_total_categoria DESC;";
        $array = self::camposJoinObj($query);
        return $array;
    }

    public static function direccionesANDTarifas(int $id):array{
        $query = "SELECT 
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', d.id,
                        'idcliente', d.idcliente,
                        'idtarifa', d.idtarifa,
                        'direccion', d.direccion,
                        'departamento', d.departamento,
                        'ciudad', d.ciudad,
                        'tarifa', IF(t.id IS NOT NULL,
                            JSON_OBJECT(
                                'id', t.id,
                                'nombre', t.nombre,
                                'valor', t.valor
                            ),
                            NULL
                        )
                    )
                ) AS direcciones
                FROM direcciones d
                LEFT JOIN tarifas t ON t.id = d.idtarifa
                WHERE d.idcliente = $id";

        $array = self::$db->query($query);
        $result = $array->fetch_assoc();
        return json_decode($result['direcciones']);
    }
    
}
