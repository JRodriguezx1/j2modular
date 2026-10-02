<?php

namespace App\services\caja;

use App\Models\caja\factmediospago;
use App\Models\clientes\clientes;
use App\Models\clientes\direcciones;
use App\Models\configuraciones\emisores;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\mediospago;
use App\Models\configuraciones\tarifas;
use App\Models\configuraciones\usuarios;
use App\Models\felectronicas\adquirientes;
use App\Models\felectronicas\facturas_electronicas;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;

/**
 * Prepara los datos de documentos e impresiones del módulo de caja.
 *
 * No conoce Router, sesiones, parámetros GET ni vistas. Cada consulta exige la
 * sucursal activa para evitar imprimir facturas o cierres de otra sucursal.
 */
final class CajaDocumentosService
{
    /**
     * Prepara una factura tamaño carta junto con sus medios de pago.
     *
     * Consumidor actual: cajacontrolador::printfacturacarta(), ruta
     * GET /printfacturacarta?id={factura}, abierta desde caja.ts y
     * ordenresumen.ts.
     */
    public function prepararFacturaCarta(int $facturaId, int $sucursalId): ?array{
        $datos = $this->obtenerDetalleVenta($facturaId, $sucursalId);
        if(!$datos)return null;

        $medios = [];
        foreach(factmediospago::idregistros('id_factura', $facturaId) as $pago){
            $medio = mediospago::find('id', (int)$pago->idmediopago);
            if($medio)$medios[] = $medio;
        }
        return $datos + ['mediospago'=>$medios];
    }

    /**
     * Prepara la cotización para impresión.
     *
     * Consumidor actual: cajacontrolador::printcotizacion(), ruta
     * GET /printcotizacion?id={factura}, abierta desde ordenresumen.ts.
     */
    public function prepararCotizacion(int $facturaId, int $sucursalId): ?array{
        return $this->obtenerDetalleVenta($facturaId, $sucursalId);
    }

    /**
     * Construye el DTO consumido por las impresoras POS.
     *
     * Consumidor actual: cajacontrolador::getInvoice(), ruta
     * GET /admin/api/getInvoice?id={factura}, llamada desde caja.ts y
     * detallecierrecaja.ts. El servicio no conoce GET ni genera la respuesta
     * JSON; únicamente valida la sucursal y devuelve datos serializables.
     */
    public function prepararInvoiceParaImpresion(int $facturaId, int $sucursalId): ?array{
        $datos = $this->obtenerDetalleVenta($facturaId, $sucursalId);
        if(!$datos)return null;

        $factura = $datos['factura'];
        $consecutivo = consecutivos::uniquewhereArray(['id'=>(int)$factura->idconsecutivo, 'id_sucursalid'=>$sucursalId]);
        if(!$consecutivo)return null;

        $customer = null;
        $facturaElectronica = null;
        if((int)$consecutivo->idtipofacturador === 1){
            $facturaElectronica = facturas_electronicas::uniquewhereArray(['id_sucursalidfk'=>$sucursalId, 'id_facturaid'=>$facturaId]);
            if($facturaElectronica){
                $customer = adquirientes::find('id', (int)$facturaElectronica->id_adquiriente);
            }
        }

        $mediosPago = $this->mapearMediosPago($facturaId);
        $items = $this->mapearProductos($datos['productos']);
        $cliente = $datos['cliente'];
        $sucursal = $datos['sucursal'];

        $resultado = [
            'host'=>$sucursal->host,
            'negocio'=>(string)$factura->nombrecompania,
            'sucursal'=>$sucursal->nombre,
            'nit'=>(string)$factura->nit,
            'direccion'=>$sucursal->direccion,
            'telefono'=>$sucursal->telefono,
            'email'=>$sucursal->email,
            'www'=>$sucursal->www,
            'logo'=>$sucursal->logo,
            'num_orden'=>(int)$factura->num_orden,
            'tipoFactura'=>$consecutivo->idtipofacturador,
            'textFactura'=>(int)$consecutivo->idtipofacturador === 1
                ? 'FACTURA ELECTRONICA DE VENTA'
                : 'COMPROBANTE DE VENTA',
            'prefijo'=>$factura->prefijo,
            'consecutivo'=>$factura->num_consecutivo,
            'fechaPago'=>$factura->fechapago,
            'caja'=>$factura->caja,
            'vendedor'=>$factura->vendedor,
            'consumidorFinal'=>[
                'identification_number'=>$customer?->identification_number ?? '222222222222',
                'name'=>$customer?->business_name ?? 'Consumidor Final',
                'phone'=>$customer?->phone ?? null,
                'address'=>$customer?->address ?? null,
                'email'=>$customer?->email ?? null,
                'municipality_id'=>$customer?->municipality_id ?? null
            ],
            'cliente'=>[
                'id'=>(string)($cliente->id ?? ''),
                'nombre'=>$cliente->nombre ?? $factura->cliente,
                'apellido'=>$cliente->apellido ?? '',
                'tipodocumento'=>(string)($cliente->tipodocumento ?? ''),
                'identificacion'=>$cliente->identificacion ?? '',
                'telefono'=>$cliente->telefono ?? '',
                'email'=>$cliente->email ?? '',
                'fecha_nacimiento'=>$cliente->fecha_nacimiento ?? '',
                'total_compras'=>$cliente->total_compras ?? '',
                'ultima_compra'=>$cliente->ultima_compra ?? '',
                'totaldebe'=>$cliente->totaldebe ?? '',
                'limitecredito'=>$cliente->limitecredito ?? '',
                'data1'=>$cliente->data1 ?? '',
                'created_at'=>$cliente->created_at ?? ''
            ],
            'items'=>$items,
            'mediospago'=>$mediosPago,
            'tipoventa'=>$factura->tipoventa,
            'subtotal'=>(string)$factura->subtotal,
            'base'=>(string)$factura->base,
            'valorimpuestototal'=>(string)$factura->valorimpuestototal,
            'descuento'=>(string)$factura->descuento,
            'total'=>(string)$factura->total,
            'observacion'=>(string)$factura->observacion,
            'resolucion'=>$consecutivo
        ];

        if($facturaElectronica){
            $resultado['cufe'] = $facturaElectronica->cufe;
            $resultado['link'] = $facturaElectronica->link;
        }
        return $resultado;
    }

    /**
     * Prepara el comprobante imprimible de un cierre de caja.
     *
     * Consumidores actuales: cajacontrolador::printdetallecierre(), ruta
     * GET /printdetallecierre?id={cierre}, y el adaptador temporal
     * cajaService::printdetallecierre().
     */
    public function prepararDetalleCierre(int $cierreId, int $sucursalId): ?array{
        $datos = (new CajaConsultasService())->obtenerCierreParaImpresion($cierreId, $sucursalId);
        if(!$datos)return null;

        $sucursal = sucursales::find('id', $sucursalId);
        if(!$sucursal)return null;
        return $datos + [
            'sucursal'=>$sucursal,
            'lineasencabezado'=>explode("\n", (string)($sucursal->datosencabezados ?? ''))
        ];
    }

    /**
     * Construye el detalle común de factura y cotización.
     *
     * También mantiene la compatibilidad temporal con
     * cajaService::detalleVenta(). La factura se consulta por ID y sucursal;
     * clientes, vendedores o direcciones faltantes se representan con objetos
     * vacíos para que una inconsistencia histórica no rompa la impresión.
     */
    public function obtenerDetalleVenta(int $facturaId, int $sucursalId): ?array{
        $factura = facturas::uniquewhereArray(['id'=>$facturaId, 'id_sucursal'=>$sucursalId]);
        $sucursal = sucursales::find('id', $sucursalId);
        if(!$factura || !$sucursal)return null;

        $mediosPago = $this->mapearMediosPago($facturaId);
        $productos = ventas::idregistros('idfactura', $facturaId);
        $cliente = clientes::find('id', (int)$factura->idcliente) ?? new clientes();
        $direccion = direcciones::uniquewhereArray(['id'=>(int)$factura->iddireccion, 'idcliente'=>(int)$factura->idcliente]);
        if(!$direccion)$direccion = new direcciones(['idcliente'=>(int)$factura->idcliente]);

        $tarifa = tarifas::find('id', (int)$direccion->idtarifa) ?? new tarifas();
        $vendedor = usuarios::find('id', (int)$factura->idvendedor) ?? (object)['nombre'=>'', 'apellido'=>''];
        $lineasencabezado = explode("\n", (string)($factura->datosrut ?? ''));
        $emisor = null;

        if((int)$factura->idemisor > 0)
            $emisor = emisores::uniquewhereArray(['id'=>(int)$factura->idemisor, 'idsucursal'=>$sucursalId]);

        return compact('factura','productos', 'cliente', 'direccion', 'tarifa', 'vendedor', 'lineasencabezado', 'sucursal', 'emisor', 'mediosPago');
    }

    /** Convierte las relaciones de pago al contrato esperado por DataInvoice. */
    private function mapearMediosPago(int $facturaId): array{
        $resultado = [];
        foreach(factmediospago::idregistros('id_factura', $facturaId) as $pago){
            $medio = mediospago::find('id', (int)$pago->idmediopago);
            if(!$medio)continue;
            $resultado[] = [
                'id'=>(string)$pago->idmediopago,
                'mediopago'=>$medio->mediopago,
                'estado'=>(string)$medio->estado,
                'valor'=>(float)$pago->valor,
                'nick'=>$medio->nick
            ];
        }
        return $resultado;
    }

    /** Convierte las líneas vendidas al contrato esperado por DataInvoice. */
    private function mapearProductos(array $productos): array{
        return array_map(static function(object $producto): array {
            $item = [
                'id'=>(string)$producto->id,
                'idproducto'=>(string)$producto->idproducto,
                'tipoproducto'=>(string)$producto->tipoproducto,
                'tipoproduccion'=>(string)$producto->tipoproduccion,
                'foto'=>$producto->foto,
                'nombreproducto'=>$producto->nombreproducto,
                'rendimientoestandar'=>(string)$producto->rendimientoestandar,
                'costo'=>(string)$producto->costo,
                'valorunidad'=>(float)$producto->valorunidad,
                'cantidad'=>(float)$producto->cantidad,
                'percentcomision'=>(float)$producto->percentcomision,
                'valorcomision'=>(float)$producto->valorcomision,
                'subtotal'=>(float)$producto->subtotal,
                'base'=>(float)$producto->base,
                'impuesto'=>(string)$producto->impuesto,
                'valorimp'=>(float)$producto->valorimp,
                'descuento'=>(float)$producto->descuento,
                'total'=>(float)$producto->total
            ];
            if(isset($producto->idcategoria))$item['idcategoria'] = (string)$producto->idcategoria;
            return $item;
        }, $productos);
    }
}
