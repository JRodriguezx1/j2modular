<?php 

namespace App\services;

use App\Models\configuraciones\caja;
use App\Models\configuraciones\notificacionesws;
use App\Models\configuraciones\usuarios;
use App\Models\inventario\detalletrasladoinv;
use App\Models\sucursales;
use GreenApi\RestApi\GreenApiClient;
use stdClass;

//GREEN_API_INSTANCE=7107567532
//GREEN_API_TOKEN=f00f6093de07418bbf0ab6331862fed231478914797c46a5a7

class whatsAppService{

    private GreenApiClient $client;
    private string $msg = "";
    private array $configLocalParam;
    
    public function __construct($configLocal = [])
    {
        $this->configLocalParam = $configLocal;
        $this->client = new GreenApiClient(
                            $_ENV['GREEN_API_INSTANCE'],
                            $_ENV['GREEN_API_TOKEN']
                        );
    }


    public function getContacts():stdClass{
        return $this->client->serviceMethods->getContacts();
    }


    public function sendMessage(string $text):stdClass|null{
        $number = notificacionesws::find('sucursal_idfk', id_sucursal());
        if(!$number){
            return null;
        }
        $result = $this->client->sending->sendMessage($number->chatid, $text);
        return $result;
    }
    


    public function crearContactoWS($array):array{
        $contactWS = notificacionesws::whereArray(['sucursal_idfk'=>id_sucursal()]);
        $array['charid'] = "";
        if(count($contactWS)>0){
            $alertas['error'][] = "Solo un contacto por sucursal";
            return $alertas;
        }

        if($array['tipo'] == 'grupo'){
            $contacts = $this->getContacts();
            if($contacts->code !== 200 || count($contacts->data) == 0){
                $alertas['error'][] = "Error, grupo no encontrado";
                return $alertas;
            }
            foreach($contacts->data as $value){
                if($value->type === "group" && $value->name === $array['nombre']){
                    $array['chatid'] = $value->id;
                    break;
                }
            }
            if(!isset($array['chatid'])){
                $alertas['error'][] = "Error, grupo no encontrado";
                return $alertas;
            }
        }

        $ws = new notificacionesws($array);
        $alertas = $ws->validar();
        if(!empty($alertas))return $alertas;
        $getDB = notificacionesws::getDB();
        $getDB->begin_transaction();
        try {
            $r = $ws->crear_guardar();  //si falla no ejecuta el commit ni el return.
            $getDB->commit();
            return $r;
        } catch (\Throwable $th) {
            $getDB->rollback();
            throw $th;
        }
    }


    public function eliminarContacto($id):bool{
        $contactWS = notificacionesws::find('id', $id);
        $getDB = notificacionesws::getDB();
        $getDB->begin_transaction();
        try {
             $r = $contactWS->eliminar_registro();
            $getDB->commit();
            return $r;
        } catch (\Throwable $th) {
            $getDB->rollback();
            throw $th;
        }
    }


    public function sendtextDetalleCierreCaja($id):stdClass|null{
        $data = cajaService::printdetallecierre($id);
        $sucursal = sucursales::find('id', id_sucursal());

        // 🔹 Encabezado
        $estado = $data['ultimocierre']->estado == 1 ? 'CIERRE DE CAJA' : 'CIERRE PARCIAL';
        $this->msg .= "🔹*$estado*\n\n";
        $this->msg .= "Sucursal: " . (($sucursal ?? null)->nombre ?? '') . "\n";
        $this->msg .= "Caja: {$data['ultimocierre']->nombrecaja}\n";
        $this->msg .= "Cajero: {$data['ultimocierre']->nombreusuario}\n";
        $this->msg .= "Inicio: {$data['ultimocierre']->fechainicio}\n";
        $this->msg .= "*Fin: {$data['ultimocierre']->fechacierre}*\n\n";
        // 🔹 Cuadre de caja
        $base = $data['ultimocierre']->basecaja ?? 0;
        $ventas = $data['ultimocierre']->ventasenefectivo ?? 0;
        $abonos = $data['ultimocierre']->abonosenefectivo ?? 0;
        $gastos = $data['ultimocierre']->gastoscaja ?? 0;
        $domicilios = $data['ultimocierre']->domicilios ?? 0;
        $dineroCaja = $base + $ventas + $abonos - $gastos;
        $realCaja = $dineroCaja - $domicilios;

        $this->msg .= "*CUADRE DE CAJA*\n";
        $this->msg .= "Efectivo Inicial: + $" . number_format($base, 0, ',', '.') . "\n";
        $this->msg .= "Ventas en Efectivo: + $" . number_format($ventas, 0, ',', '.') . "\n";
        $this->msg .= "Abonos: + $" . number_format($abonos, 0, ',', '.') . "\n";
        $this->msg .= "Gastos: - $" . number_format($gastos, 0, ',', '.') . "\n";
        $this->msg .= "*Dinero en caja: $" . number_format($dineroCaja, 0, ',', '.') . "*\n";
        $this->msg .= "Domicilios: - $" . number_format($domicilios, 0, ',', '.') . "\n";
        $this->msg .= "*Real en caja: $" . number_format($realCaja, 0, ',', '.') . "*\n\n";
        // 🔹 Medios de pago
        $this->msg .= "💳 *MEDIOS DE PAGO*\n";
        foreach ($data['discriminarmediospagos'] as $value) {
            $this->msg .= "{$value['mediopago']}: $" . number_format($value['valor'], 0, ',', '.') . "\n";
        }
        // 🔹 Datos de venta
        $this->msg .= "\n*DATOS DE VENTA*\n";
        $this->msg .= "Ventas de contado: + $" . number_format(($data['ultimocierre']->ingresoventas ?? 0), 0, ',', '.') . "\n";
        $this->msg .= "Ventas a credito: + $" . number_format($data['ultimocierre']->creditocapital??0, 0, ',', '.') . "\n";
        $this->msg .= "Total Descuentos:  $" . number_format($data['ultimocierre']->totaldescuentos??0, 0, ',', '.') . "\n";
        $this->msg .= "💰*Ingreso total de ventas: = $" . number_format(($data['ultimocierre']->ingresoventas??0)+($data['ultimocierre']->creditocapital??0), 0, ',', '.') . "*\n";
        $this->msg .= "Total abonos: + $" . number_format($data['ultimocierre']->abonostotales??0, 0, ',', '.') . "\n";
        $this->msg .= "💵*Ingreso de caja del dia:  $" . number_format(($data['ultimocierre']->ingresoventas??0)+($data['ultimocierre']->abonostotales??0), 0, ',', '.') . "*\n";
        $this->msg .= "Total gastos de caja: - $" . number_format($data['ultimocierre']->gastoscaja??0, 0, ',', '.') . "\n";
        $this->msg .= "Total Domicilios: - $" . number_format($data['ultimocierre']->domicilios??0, 0, ',', '.') . "\n";
        $this->msg .= "📌*Utilidad del dia: = $" . number_format(($data['ultimocierre']->ingresoventas??0)+($data['ultimocierre']->creditocapital??0)-($data['ultimocierre']->domicilios??0)-($data['ultimocierre']->gastoscaja??0), 0, ',', '.') . "*\n";
        $this->msg .= "Base grabable: = $" . number_format($data['ultimocierre']->basegravable??0, 0, ',', '.') . "\n";
        $this->msg .= "Impuesto Total: - $" . number_format($data['ultimocierre']->valorimpuestototal??0, 0, ',', '.') . "\n";
        $this->msg .= "Gastos otros/bancarios: - $" . number_format($data['ultimocierre']->gastosbanco??0, 0, ',', '.') . "\n\n";
        /*// 🔹 Detalle impuesto
        $this->msg .= "*DETALLE IMPUESTO*\n";
        // 🔹 Tipo de facturas
        $this->msg .= "*TIPO DE FACTURAS*\n";*/
        // 🔹 Sobrante y faltante
        $this->msg .= "📊*SOBRANTE Y FALTANTE*\n";
        foreach ($data['sobrantefaltante'] as $value) {
            $this->msg .= "Medio de pago: *{$value->nombremediopago}*" . "\n";
            $this->msg .= "Sisitema: $" . number_format($value->valorsistema, 0, ',', '.') . " - Declarado: $" . number_format($value->valordeclarado??0, 0, ',', '.') . " => " . number_format($value->valordeclarado-$value->valorsistema, 0, ',', '.') . "\n";
        }

        // 🔹 Ventas por usuario
        if($this->configLocalParam['mostrar_ventas_por_usuario_en_impresion/ws_cierrecaja']->valor_final == 1){
            $this->msg .= "\n*VENTAS POR USUARIO*\n";
            foreach ($data['ventasxusuarios'] as $index => $value) {
                $comision = "comision";
                if(array_key_last($data['ventasxusuarios']) == $index)$comision = "comision_negocio";
                $this->msg .= "*{$value['Nombre']}*" . "\n";
                $this->msg .= $value['N_ventas'] . " Ventas: $" . $value['ventas'] . " Comision: $" . number_format($value[$comision], 0, ',', '.') . "\n";
            }
        }

        // 🔹 Pie de pagina
        $this->msg .= "\n*J2 SOFTWARE POS*\n";
        $this->msg .= "www.j2softwarepos.com\n";
        $r = $this->sendMessage($this->msg);
        return $r;
    }
    

    public function sendTextOrdenEliminada(object $factura, int $idcaja, $devolverInv, array $productos = []){
        $sucursal = sucursales::find('id', id_sucursal());
        $caja = caja::find('id', $idcaja);
        $usuario = usuarios::find('id', $_SESSION['id']);
        $fechaAnulacion = date('Y-m-d H:i:s');
        $this->msg = '';
        // 🔹 Encabezado
        $estado = $factura->tipoventa == 'Contado' ? 'VENTA DE CONTADO ANULADA' : 'VENTA DE CREDITO ANULADA';
        $this->msg .= "🔹*$estado*\n\n";
        $this->msg .= "Sucursal: " . (($sucursal ?? null)->nombre ?? '') . "\n";
        $this->msg .= "Caja: {$caja->nombre}\n";
        $this->msg .= "Usuario: $usuario->nombre $usuario->apellido\n";
        $this->msg .= "Fecha: ". ($factura->fechaanulacion??$fechaAnulacion) ."\n\n";
        // 🔹 Cuadre de caja
        $this->msg .= "*ORDEN ANULADA*\n";
        $this->msg .= "ID de la orden: " . $factura->id . "\n";
        $this->msg .= "Numero de orden: " . $factura->num_orden . "\n";
        $this->msg .= "📌Numero de factura: " . $factura->prefijo.$factura->num_consecutivo . "\n";
        $this->msg .= "💰*Valor: $" . number_format($factura->total??0, 0, ',', '.') . "*\n\n";
        $this->msg .= "DEVOLUCION A INVENTARIO: " . ($devolverInv==true?'Si':'No') . "\n\n";

        foreach ($productos as $value) {
            $nombre = $value->nombre ?? $value->nombreproducto ?? '';
            $cantidad = $value->stock ?? $value->cantidad ?? 0;
            $this->msg .= "id: {$value->id}, {$nombre}, Cant: " . number_format((float)$cantidad, 2, ',', '.') . "\n";
        }

        $this->msg .= "\n*OBOSERVACION:*\n";
        $this->msg .= "{$factura->observacioneliminacion}\n";

        // 🔹 Pie de pagina
        $this->msg .= "\n*J2 SOFTWARE POS*\n";
        $this->msg .= "www.j2softwarepos.com\n";
        $this->sendMessage($this->msg);
    }


    /**
     * Envia una sola alerta con los productos e insumos que acaban de cruzar
     * su stock minimo. Recibe la estructura creada por StockMinimoService.
     */
    public function productoBajoStock(array $items):stdClass|null{
        if(empty($items))return null;
        $productos = [];
        $insumos = [];
        foreach($items as $item){
            if(!is_object($item) || (int)($item->id ?? 0) <= 0)continue;
            if(($item->tipo ?? '') === 'Insumo'){
                $insumos[] = $item;
            }else{
                $productos[] = $item;
            }
        }

        // Si todos los elementos recibidos eran invalidos no se envia mensaje.
        if(empty($productos) && empty($insumos))return null;

        $sucursal = sucursales::find('id', id_sucursal());
        $this->msg = "🔹*ALERTA DE STOCK MINIMO*\n";
        $this->msg .= "Sucursal: " . (($sucursal ?? null)->nombre ?? '') . "\n";
        $this->msg .= "Fecha: " . date('Y-m-d H:i:s') . "\n";

        if(!empty($productos)){
            $this->msg .= "\n*PRODUCTOS*\n";
            $this->msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            foreach($productos as $producto)$this->agregarItemStockMinimo($producto);
        }

        if(!empty($insumos)){
            $this->msg .= "\n*INSUMOS*\n";
            $this->msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
            foreach($insumos as $insumo)$this->agregarItemStockMinimo($insumo);
        }

        $this->msg .= "\n*J2 SOFTWARE POS*\n";
        $this->msg .= "www.j2softwarepos.com\n";
        return $this->sendMessage($this->msg);
    }
    

    /** Agrega al mensaje el detalle uniforme de un producto o insumo. */
    private function agregarItemStockMinimo(object $item):void{
        $nombre = trim((string)($item->nombre ?? ''));
        $sku = trim((string)($item->sku ?? ''));
        $unidad = trim((string)($item->unidadmedida ?? ''));
        //$stockAnterior = number_format((float)($item->stock_anterior ?? 0), 2, ',', '.');
        $stockActual = number_format((float)($item->stock_actual ?? 0), 2, ',', '.');
        $stockMinimo = number_format((float)($item->stock_minimo ?? 0), 2, ',', '.');

        $this->msg .= "ID: " . (int)$item->id . " - " . ($nombre !== '' ? $nombre : 'Sin nombre') . "\n";
        if($sku !== '')$this->msg .= "SKU: {$sku}\n";
        //$this->msg .= "Stock: {$stockAnterior} → {$stockActual}" . ($unidad !== '' ? " {$unidad}" : '') . "\n";
        $this->msg .= "🚨 Stock actual: {$stockActual}" . ($unidad !== '' ? " {$unidad}" : '') . "\n";
        $this->msg .= "Minimo: {$stockMinimo}" . ($unidad !== '' ? " {$unidad}" : '') . "\n\n";
    }

    /** Conserva hasta cuatro decimales sin agregar ceros innecesarios. */
    /*private function formatearCantidadStock(float $cantidad):string{
        return rtrim(rtrim(number_format($cantidad, 4, ',', '.'), '0'), ',');
    }*/

    public function sendMsgTrasladoInvDespachado(object $trasladoinv, array $listaproductos){
        
        $usuario = usuarios::find('id', $trasladoinv->fkusuario);
        $sucursales = sucursales::IN_Where('id', [$trasladoinv->id_sucursalorigen, $trasladoinv->id_sucursaldestino], ['estado', 1]);
        $indexado = [];
        foreach($sucursales as $value)$indexado[$value->id] = $value;
        $sucursalorigen = $indexado[$trasladoinv->id_sucursalorigen];
        $sucursaldestino = $indexado[$trasladoinv->id_sucursaldestino];

        $this->msg = '';
        // 🔹 Encabezado
   
        $this->msg .= "🔹*TRASLADO DE INVENTARIO*\n\n";
        $this->msg .= "Sucursal origen: " . ($sucursalorigen->nombre??'') . "\n";
        $this->msg .= "Sucursal destino: " . ($sucursaldestino->nombre??'') . "\n";
        $this->msg .= "Usuario: $usuario->nombre $usuario->apellido\n";
        $this->msg .= "Fecha: ".$trasladoinv->created_at."\n\n";
        // 🔹 Informacion del despacho de mercancia
        $this->msg .= "*Informacion del despacho*\n";
        $this->msg .= "ID orden de traslado: " . $trasladoinv->id . "\n";
        $this->msg .= "📌Numero de orden: " . $trasladoinv->id . "\n";
        //$this->msg .= "💰*Valor: $" . number_format($trasladoinv->total??0, 0, ',', '.') . "*\n\n";
        $this->msg .= "\nPRODUCTOS DESPACHADOS: " . "\n\n";


        //CONSULTA DETALLADA DE LOS PRODUCTOS TRASLADADOS
        $sql = "SELECT td.id, td.id_trasladoinv, td.fkproducto, td.idsubproducto_id,
                    COALESCE(pund.nombre, spund.nombre) as unidadmedida,
                    COALESCE(p.nombre, sp.nombre) AS nombre, td.cantidad
                    FROM detalletrasladoinv td
                    LEFT JOIN productos p ON td.fkproducto = p.id
                    LEFT JOIN subproductos sp ON td.idsubproducto_id = sp.id
                    LEFT JOIN unidadesmedida pund ON p.idunidadmedida = pund.id
                    LEFT JOIN unidadesmedida spund ON sp.id_unidadmedida = spund.id
                    WHERE td.id_trasladoinv = $trasladoinv->id;";
            $detalletrasladoinv = detalletrasladoinv::camposJoinObj($sql);

        foreach ($detalletrasladoinv as $value) {
            $id = $value->fkproducto == NULL ? $value->idsubproducto_id : $value->fkproducto;
            $this->msg .= "id: {$id}, {$value->nombre}, Cant: " . number_format($value->cantidad??0, 0, ',', '.') ." ". $value->unidadmedida . "\n";
        }

        $this->msg .= "\n*OBOSERVACION*\n";
        $this->msg .= "{$trasladoinv->observacion}\n";

        // 🔹 Pie de pagina
        $this->msg .= "\n*J2 SOFTWARE POS*\n";
        $this->msg .= "www.j2softwarepos.com\n";
        $this->sendMessage($this->msg);
    }
}
