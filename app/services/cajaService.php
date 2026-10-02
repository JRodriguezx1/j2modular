<?php 

namespace App\services;

use App\Models\caja\cierrescajas;
use App\Models\caja\factmediospago;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\emisores;
use App\Models\factimpuestos;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Repositories\contable\movimientos_cajaRepository;
use App\Repositories\creditos\creditosRepository;
use App\Repositories\creditos\cuotasRepository;
use App\services\caja\CajaDocumentosService;
use App\services\caja\CajaOrdenesService;

class cajaService {

    /**
     * Adaptador temporal para la impresión del cierre.
     *
     * El controlador y la ruta conservan su contrato actual; la consulta fue
     * centralizada en CajaDocumentosService y CajaConsultasService para evitar
     * implementaciones distintas del mismo resumen.
     */
    public static function printdetallecierre(int $id):?array{
        return (new CajaDocumentosService())->prepararDetalleCierre($id, id_sucursal());
    }
    /**
     * Adaptador temporal del detalle compartido por factura y cotización.
     * Los consumidores nuevos deben usar CajaDocumentosService directamente.
     */
    public static function detalleVenta(int $id):?array{
        return (new CajaDocumentosService())->obtenerDetalleVenta($id, id_sucursal());
    }


    /**
     * Adaptador temporal para consumidores internos todavía no migrados.
     * El caso de uso y su transacción pertenecen a CajaOrdenesService.
     */
    public static function despacharOrden(int $id):array{
        return (new CajaOrdenesService())->despacharOrden($id, id_sucursal());
    }


    public static function cambiarEmisor(array $data):array{
        $repoMovimientocaja = new movimientos_cajaRepository();
        $creditoRepo = new creditosRepository();
        $nuevoEmisor = null;
        $idfactura = $data['id'];
        $idemisor = $data['idemisor'];
        $idNewCaja = $data['idcaja'];
        $sucursal = sucursales::find('id', id_sucursal());
        if(!$sucursal)return ['error' => ['La sucursal no existe.']];
        $nuevaCaja = caja::uniquewhereArray(['id' => (int)$idNewCaja, 'idsucursalid' => $sucursal->id]);
        $factura = facturas::find('id', $idfactura);
        if(!$factura)return ['error'=>['No se encontro factura']];
        $credito = $creditoRepo->uniqueWhere(['factura_id'=>$factura->id]);
        if($factura->idcaja == $idNewCaja)return ['error'=>['Debes elegir un emisor distinto al inicial']];
        if(!$nuevaCaja)return ['error' => ['La caja seleccionada no existe.']];
        
        if(!empty($nuevaCaja->idemisor)){
            $nuevoEmisor = emisores::uniquewhereArray(['id' => (int)$nuevaCaja->idemisor, 'idsucursal' => $sucursal->id]);
            if(!$nuevoEmisor)return ['error' => ['El emisor de la caja no existe.']];
        }
        
        //actualizar valores de la caja actual
        $mediospago = factmediospago::uniquewhereArray(['id_factura'=>$factura->id, 'idmediopago'=>1])->valor??0; //me trae la factura que pago en efectivo
        $factMP = factmediospago::idregistros('id_factura', $factura->id);
        $cierrecajafactura = cierrescajas::find('id', $factura->idcierrecaja);
        $tempcierrecaja = clone $cierrecajafactura;

        //obtener las cuotas de la caja actual
        $cuotasRepo = new cuotasRepository();
        $cuotas =  $credito ? $cuotasRepo->where(['id_credito'=>$credito->id, 'cierrecaja_id'=>$cierrecajafactura->id]) : [];

        ///// ACTUALIZAR CAJA ACTUAL
        /////////// calcular cantidad de facturas y discriminar por tipo
        $cierrecajafactura->totalfacturaseliminadas += 1;
        if(consecutivos::uncampo('id', $factura->idconsecutivo, 'idtipofacturador')==1){
          $cierrecajafactura->facturaselectronicaselimnadas += 1;
          $cierrecajafactura->facturaselectronicas -= 1;
          $cierrecajafactura->valorfe -= $factura->total;
          $cierrecajafactura->descuentofe -= $factura->descuento;
        }else{
          $cierrecajafactura->facturasposeliminadas += 1;
          $cierrecajafactura->facturaspos -= 1;
          $cierrecajafactura->valorpos -= $factura->total;
          $cierrecajafactura->descuentopos += $factura->descuento;
        }

        ///////// calcular ventas en efectivo, total descuentos, total ingreso de ventas
        if($factura->tipoventa=='Contado'){
          $cierrecajafactura->ventasenefectivo -= $mediospago;
          $cierrecajafactura->ingresoventas -= $factura->total;
        }else{
          $cierrecajafactura->creditocapital -= $factura->total;
          $cierrecajafactura->creditos -= ($factura->total-$factura->abono);
        }

        $cierrecajafactura->domicilios -= $factura->valortarifa;
        $cierrecajafactura->totaldescuentos -= $factura->descuento;
        $cierrecajafactura->valorimpuestototal -= $factura->valorimpuestototal;
        $cierrecajafactura->basegravable -= $factura->base;


        $r1 = $cierrecajafactura->actualizar();
        if(!$r1)return ['error'=>['Error al actualizar el emisor en la factura en el cierre de caja actual']];
        
        ///descuenta los abonos de creditos por caja 
        if($factura->tipoventa=='Credito')$anularCredito = creditosService::descontarAbonosCreditosXCierresCaja($credito->id);  //me vuelve a actualizar el cierre de caja
        if(isset($anularCredito['error'])){
            $tempcierrecaja->actualizar();
            return ['error'=>['Error al actualizar los abonos del emisor de la factura en los cierre de caja']];
        }
        

        //ACTUALIZAR VALORES DE LA NUEVA CAJA
        $ultimocierre = cierrescajas::uniquewhereArray(['idcaja' => $idNewCaja, 'estado'=>0]);
        $ultimocierre->totalfacturas = $ultimocierre->totalfacturas + 1;  //total de facturas
        if(consecutivos::uncampo('id', $factura->idconsecutivo, 'idtipofacturador')==1){
            $ultimocierre->facturaselectronicas = $ultimocierre->facturaselectronicas + 1;  //total de facturas electronicas
            $ultimocierre->valorfe += $factura->total;
            $ultimocierre->descuentofe += $factura->descuento;
        }else{
            $ultimocierre->facturaspos = $ultimocierre->facturaspos + 1;   //total de facturas pos
            $ultimocierre->valorpos += $factura->total;
            $ultimocierre->descuentopos += $factura->descuento;
        }
        $ultimocierre->ventasenefectivo += $factura->tipoventa=='Contado'?$factura->total:0; 
        ///////// calcular ventas en efectivo, total descuentos, total ingreso de ventas
        //////// establecer el id del  nuevo cierre de caja para las factmediospago ////////////
        foreach($factMP as $obj){
            $obj->cierrecajaid = $ultimocierre->id;
            if($obj->idmediopago == 1)$ultimocierre->abonosenefectivo += ($credito?$obj->valor:0);
        }
        //////// establecer el id del nuevo cierre de caja para las cuotas
        foreach($cuotas as $cuota)
            $cuota->cierrecaja_id = $ultimocierre->id;

        $ultimocierre->creditocapital += $credito?->capital??0;
        $ultimocierre->creditos += ($credito?->capital??0)-($credito?->abonoinicial??0);  
        $ultimocierre->abonoscreditos += $credito?->abonodecuotas??0;
        $ultimocierre->abonostotales += $credito?->abonodecuotas??0;
        $ultimocierre->domicilios += $factura->valortarifa;
        
        $ultimocierre->ingresoventas += ($credito?0:$factura->total);
        $ultimocierre->totaldescuentos += $factura->descuento;
        $ultimocierre->realventas += $factura->total; 
        $ultimocierre->valorimpuestototal += $factura->valorimpuestototal;
        $ultimocierre->basegravable += $factura->base;

        //ACTUALIZAR FACTURA
        if ($nuevoEmisor) {
            $factura->idemisor = (int)$nuevoEmisor->id;
            $factura->nombrecompania = (string)$nuevoEmisor->nombre;
            $factura->nit = (string)$nuevoEmisor->nit;
            $factura->datosrut = (string)$nuevoEmisor->datosencabezados;
            $emisorRespuesta = $nuevoEmisor;
        } else {
            $factura->idemisor = null;
            $factura->nombrecompania = (string)$sucursal->negocio;
            $factura->nit = (string)$sucursal->nit;
            $factura->datosrut = (string)$sucursal->datosencabezados;
            $emisorRespuesta = $sucursal;
        }

        $factura->idcaja = $idNewCaja;
        $factura->idcierrecaja = $ultimocierre->id;
        $credito && $credito->idemisor = $factura->idemisor;

        $r2 = $factura->actualizar();
        if($r2){
            $ultimocierre->actualizar();
            if($factMP)factmediospago::updatemultiregobj($factMP, ['cierrecajaid']);
            if($cuotas)$cuotasRepo->updatemultiregobj($cuotas, ['cierrecaja_id']);
            if($credito)$creditoRepo->update($credito);
            
            //actualizar movimiento de caja
            $movCaja = $repoMovimientocaja->uniqueWhere(['fk_tipo_documento'=>1, 'id_documento'=>$factura->id]);
            $movCaja->fk_caja = $idNewCaja;
            $repoMovimientocaja->update($movCaja);
        }else{
            $tempcierrecaja->actualizar();
            return ['error'=>['Error al actualizar el emisor en la factura']];
        }
        return ['exito'=>['Emisor actualizado en factura'],  'emisor' => $emisorRespuesta];
    }


}
