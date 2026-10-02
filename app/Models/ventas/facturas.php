<?php
namespace App\Models\ventas;

class facturas extends \App\Models\ActiveRecord {
    protected static $tabla = 'facturas';
    protected static $columnasDB = ['id', 'idemisor', 'id_sucursal', 'idcliente', 'idvendedor', 'idrepartidorcobrador', 'idcaja', 'idconsecutivo', 'iddireccion', 'idtarifazona', 'idcierrecaja', 'idcanaldeventa', 'num_orden', 'prefijo', 'num_consecutivo', 'nombrecompania', 'nit', 'datosrut', 'cliente', 'vendedor', 'caja', 'tipofacturador', 'propina', 'direccion', 'tarifazona', 'totalunidades', 'recibido', 'cambio', 'transaccion', 'tipoventa', 'cotizacion', 'remision', 'estado', 'cambioaventa', 'ref_creditoid', 'referencia', 'abono', 'abonofinal', 'porcentgananciauser', 'valorgananciauser', 'subtotal', 'base', 'valorimpuestototal', 'dctox100', 'descuento', 'total', 'observacion', 'observacioneliminacion', 'departamento', 'ciudad', 'entrega', 'entregado', 'valortarifa', 'puntos_generados', 'puntos_descontados', 'fechaentrega', 'fechacreacion', 'fechapago', 'fechaanulacion', 'habilitada', 'opc1', 'opc2'];
    private static $arrayMetodosPago = ['Efectivo', 'Daviplata', 'Nequi', 'TD', 'TC', 'QR', 'TB'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->idemisor = $args['idemisor'] ?? '';
        $this->id_sucursal = $args['id_sucursal'] ?? '';
        $this->idcliente = $args['idcliente'] ?? 1;
        $this->idvendedor = $args['idvendedor'] ?? 1; //tabla usuarios
        $this->idrepartidorcobrador = $args['idrepartidorcobrador'] ?? '';
        $this->idcaja = $args['idcaja'] ?? 1;
        $this->idconsecutivo = $args['idconsecutivo'] ?? 1;
        $this->iddireccion = $args['iddireccion'] ?? 1;
        $this->idtarifazona = $args['idtarifazona'] ?? 1;
        $this->idcierrecaja = $args['idcierrecaja'] ?? '';
        $this->idcanaldeventa = $args['idcanaldeventa'] ?? 1;
        $this->num_orden = $args['num_orden'] ?? '';
        $this->prefijo = $args['prefijo'] ?? '';
        $this->num_consecutivo = $args['num_consecutivo'] ?? '';
        $this->nombrecompania = $args['nombrecompania'] ?? '';
        $this->nit = $args['nit'] ?? '';
        $this->datosrut = $args['datosrut'] ?? '';
        $this->cliente = $args['cliente'] ?? '';  //nombre del cliente
        $this->vendedor = $args['vendedor'] ?? '';  //nombre del vendedor
        $this->caja = $args['caja'] ?? '';   //nombre de la caja
        $this->tipofacturador = $args['tipofacturador'] ?? '';  //nombre del tipo de facturador
        $this->propina = $args['propina'] ?? 0;
        $this->direccion = $args['direccion'] ?? '';  //nombre de la direccion
        $this->tarifazona = $args['tarifazona'] ?? '';  //nombre de la tarifa
        $this->totalunidades = $args['totalunidades'] ?? 0;
        $this->recibido = $args['recibido'] ?? 0;
        $this->cambio = $args['cambio'] ?? 0;
        $this->transaccion = $args['transaccion'] ?? 0;
        $this->tipoventa = $args['tipoventa'] ?? 'Contado'; //si es de contado o credito
        $this->cotizacion = $args['cotizacion'] ?? 0; //1 = cotizacion
        $this->remision = $args['remision'] ?? 0;
        $this->estado = $args['estado'] ?? 'Paga';
        $this->cambioaventa = $args['cambioaventa']??0;  //1 = se pasa de cotizacion a venta
        $this->ref_creditoid = $args['ref_creditoid']??'';
        $this->referencia = $args['referencia']??'';
        $this->abono = $args['abono']??0;
        $this->abonofinal = $args['abonofinal']??0;
        $this->porcentgananciauser = $args['porcentgananciauser']??0;
        $this->valorgananciauser = $args['valorgananciauser']??0;
        $this->subtotal = $args['subtotal'] ?? 0;
        $this->base = $args['base'] ?? 0;
        $this->valorimpuestototal = $args['valorimpuestototal'] ?? 0;
        $this->dctox100 = $args['dctox100'] ?? 0;
        $this->descuento = $args['descuento'] ?? 0;
        $this->total = $args['total'] ?? 0;
        $this->observacion = $args['observacion'] ?? '';
        $this->observacioneliminacion = $args['observacioneliminacion'] ?? '';
        $this->departamento = $args['departamento'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->entrega = $args['entrega'] ?? 'Presencial';
        $this->entregado = $args['entregado'] ?? 1;
        $this->valortarifa = $args['valortarifa'] ?? 0;
        $this->puntos_generados = $args['puntos_generados'] ?? 0;
        $this->puntos_descontados = $args['puntos_descontados'] ?? 0;
        $this->fechaentrega = $this->entregado == 1?date('Y-m-d H:i:s'):'';
        $this->fechacreacion = $args['fechacreacion'] ?? date('Y-m-d H:i:s');
        $this->fechapago = $this->estado=='Paga'?date('Y-m-d H:i:s'):'';
        $this->fechaanulacion = $args['fechaanulacion']??null;
        $this->habilitada = $args['habilitada']??0;
        $this->opc1 = $args['opc1'] ?? '';
        $this->opc2 = $args['opc2'] ?? '';
    }

    // Validación para facturas nuevos
    public function validar_nueva_factura():array {
        if(!$this->idcliente)self::$alertas['error'][] = 'El cliente es Obligatorio';
        if(!$this->idvendedor)self::$alertas['error'][] = 'El vendedor es obligatorio';
        return self::$alertas;
    }


    public static function zDiarioTotalVentas($cajas, $consecutivos, $idsucursal, $fechaini="", $fechafin=""){
        $sql = "SELECT SUM(subtotal) as subtotalventa, SUM(base) as base, SUM(valorimpuestototal) as valorimpuestototal, SUM(total) as totalventa,
                SUM(CASE WHEN consecutivos.idtipofacturador = 1 THEN 1 ELSE 0 END) AS ELECTRONICAS, /*cuantas facturas electronicas hay*/
                SUM(CASE WHEN consecutivos.idtipofacturador = 2 THEN 1 ELSE 0 END) AS POS,          /*cuantas facturas POS hay*/
                SUM(CASE WHEN consecutivos.idtipofacturador = 1 THEN total ELSE 0 END) AS total_ELECTRONICAS,
                SUM(CASE WHEN consecutivos.idtipofacturador = 2 THEN total ELSE 0 END) AS total_POS
                FROM facturas JOIN consecutivos ON facturas.idconsecutivo = consecutivos.id WHERE idcaja IN(";
        
        $sql .= $cajas.") AND idconsecutivo IN(".$consecutivos.") AND facturas.estado = 'Paga' AND id_sucursal = $idsucursal AND fechapago BETWEEN '$fechaini' AND '$fechafin';";
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        $resultado->free();
        return $total;
    }

    public static function zDiarioMediosPago($cajas, $consecutivos, $idsucursal, $fechaini="", $fechafin=""){
        $sql = "SELECT mediospago.id, mediospago.mediopago as nombre, SUM(factmediospago.valor) as valor
                FROM mediospago JOIN factmediospago ON mediospago.id = factmediospago.idmediopago
                JOIN facturas ON factmediospago.id_factura = facturas.id WHERE idcaja IN(";
        
        $sql .= $cajas.") AND idconsecutivo IN(".$consecutivos.") AND facturas.estado = 'Paga' AND id_sucursal = $idsucursal AND fechapago BETWEEN '$fechaini' AND '$fechafin' GROUP BY mediospago.mediopago;";
        $resultado = self::$db->query($sql);
        $array = [];
        while($row = $resultado->fetch_assoc())$array[] = $row;
        $resultado->free();
        return $array;
    }

    // este medoto se usa en controlador de ventas para calcular el siguiente numero de orden
    public static function calcularNumOrden(int $idsucursal): int {
        $sql = "SELECT COALESCE(MAX(num_orden), 0)+1 AS next_num FROM facturas WHERE id_sucursal = $idsucursal;";
        $resultado = self::$db->query($sql);
        $r = $resultado->fetch_assoc();
        $resultado->free();
        return array_shift($r);
    }


    //metodo usado para la graficas de index reportes
    public static function ventasGraficaMensual(int $idsucursal){
        $sql = "SELECT DATE_FORMAT(fechapago, '%Y-%m') AS periodo, SUM(total) AS total_venta
                FROM facturas
                WHERE fechapago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND estado = 'Paga' AND id_sucursal = $idsucursal
                GROUP BY periodo
                ORDER BY periodo;";

        $resultado = self::$db->query($sql);
        $array = [];
        while($row = $resultado->fetch_object())$array[] = $row;
        $resultado->free();
        return $array;
    }

    //metodo usado para la graficas de index reportes
    public static function ventasGraficaDiario($idsucursal){
        $sql = "SELECT DAY(fechapago) AS dia, SUM(total) AS total_venta FROM facturas 
                WHERE fechapago >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                AND fechapago <  DATE_ADD(DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01'), INTERVAL 1 MONTH) AND estado = 'Paga' AND id_sucursal = $idsucursal
                GROUP BY DAY(fechapago) ORDER BY dia;";
        $resultado = self::$db->query($sql);
        $array = [];
        while($row = $resultado->fetch_object())$array[] = $row;
        $resultado->free();
        return $array;
    }


    //metodo usado para consultar las facturas con sus medios de pago
    public static function facturasConMediosPago($colum, $array = [], $filter=[]){
        $sql = "SELECT f.*, JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', mp.id,
                        'cierrecajaid', sm.cierrecajaid,
                        'id_factura', sm.id_factura,
                        'idmediopago', sm.idmediopago,
                        'valor', sm.valor,
                        'mediopago', mp.mediopago,
                        'estado', mp.estado,
                        'nick', mp.nick
                    )
                ) AS mediosdepago, cj.nombre as nombrecaja FROM facturas f
                LEFT JOIN factmediospago sm ON sm.id_factura = f.id
                LEFT JOIN mediospago mp ON mp.id = sm.idmediopago
                LEFT JOIN caja cj ON f.idcaja = cj.id
                WHERE f.$colum IN(";

                foreach($array as $key => $value){
                    if(array_key_last($array) == $key){
                        if($filter[2]<=3){ // mostrar todas las facturas para soporte, supervisor, administrador
                            $sql.= "{$value}) AND $filter[0] = $filter[1] GROUP BY f.id;";
                        }else{ //las facturas del asesor logueado
                            $sql.= "{$value}) AND $filter[0] = $filter[1] AND f.idvendedor = $filter[3] GROUP BY f.id;";
                        }
                    }else{
                        $sql.= "{$value}, ";
                    }
                }
        $resultado = self::$db->query($sql);
        $array = [];
        while($row = $resultado->fetch_object())$array[] = $row;
        $resultado->free();
        return $array;
    }


    public static function despachosPendientes(int $idsucursal):?array{
        $sql = "SELECT * FROM facturas f WHERE (f.estado = 'Paga' OR f.estado = 'Remision') AND (f.entrega = 'Domicilio' OR f.entrega = 'Presencial') AND f.entregado = 0 AND id_sucursal = $idsucursal;";
        $result = self::camposJoinObj($sql);
        return $result;
    }
}
