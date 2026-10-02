<?php 

namespace App\services;

use App\Models\configuraciones\consecutivos;
use App\Models\felectronicas\adquirientes;
use App\Models\felectronicas\diancompanias;
use App\Models\felectronicas\facturas_electronicas;
use App\Models\ventas\facturas;
use stdClass;

//**SERVICIO DE FACTURA ELECTRONICA, ADQUIRIENTES Y NOTA CREDITO - DIAN

class facturaElectronicaService {

    public static function createUpDateAdquiriente(array $datosadquiriente): array{
       $alertas = []; 
       $existeAdquiriente = adquirientes::find('identification_number', $datosadquiriente['identification_number']);
      if($existeAdquiriente){
        $existeAdquiriente->compara_objetobd_post($datosadquiriente);
        $r = $existeAdquiriente->actualizar();
        $alertas['tipo'] = "actualizar";
        $alertas['response'] = $r;
        $alertas['obj'] = $existeAdquiriente;
        $alertas['id'] = $existeAdquiriente->id;
      }else{
        $adquiriente = new adquirientes($datosadquiriente);
        $r = $adquiriente->crear_guardar();
        $alertas['tipo'] = "crear";
        $alertas['response'] = $r[0];
        $alertas['obj'] = $adquiriente;
        $alertas['obj']->id = $r[1];
        $alertas['id'] = $r[1];
      }
      return $alertas;
    }

    //metodo usado en crearFacturaPOSaElectronica de apicdiancontrolador
    public static function reciclarFacturaElectronica($value){

      $previasAceptadas  = facturas_electronicas::whereArray(['id_sucursalidfk'=>id_sucursal(), 'id_estadoelectronica'=>2, 'id_facturaid'=>$value->id_facturaid], 'DESC');
      if(!empty($previasAceptadas )){
        $last  = reset($previasAceptadas );
        $facturageneral = facturas::find('id', $last ->id_facturaid); //obtener la factura POS anterior
        $facturageneral->idconsecutivo =  $last ->consecutivo_id;
        $facturageneral->prefijo =  $last ->prefijo;
        $facturageneral->num_consecutivo =  $last ->numero;
        $facturageneral->actualizar();
      }else{
        // no hay factura electronica previa, tomar el prefojo y numero POS que tenia originalmente
      }
    }

    //metodo usado en crearFacturaPOSaElectronica de apicdiancontrolador
    public static function actualizarFacturaConsecutivo($factura, $consecutivo, $numConsecutivo){
      $factura->num_consecutivo = $numConsecutivo;
      $factura->prefijo = $consecutivo->prefijo;
      $factura->idconsecutivo = $consecutivo->id;
      $r = $factura->actualizar();
      $c = $consecutivo->actualizar();
    }

    //actualizar las fechas de envio
    public static function actualizarFechaEnvioInvoice(stdClass $json_envio):string{
      date_default_timezone_set('America/Bogota');
      $json_envio->date = date('Y-m-d');
      $json_envio->time = date('H:i:s');
      $json_envio->payment_form->payment_due_date = date('Y-m-d');
      $json_envioDateUP = json_encode($json_envio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
      return $json_envioDateUP;
    }

    //actualizar datos de la resolucion de la FE
    public static function actualizarResolutionFE(array $data):string{
      date_default_timezone_set('America/Bogota');
      $idfe = $data['idfe'];
      $idresolution = $data['idresolution'];
      $consecutivo = $data['consecutivo'];

      $resolucion = consecutivos::uniquewhereArray(['id'=>$idfe, 'id_sucursalid'=>id_sucursal()]);
      $company = diancompanias::find('id', $resolucion->idcompania);
      $facturaElectronica = facturas_electronicas::uniquewhereArray(['id'=>$idfe, 'id_sucursalidfk'=>id_sucursal()]);
      $json_envio = json_decode($facturaElectronica->json_envio);
      $facturaElectronica->consecutivo_id = $idresolution;
      $facturaElectronica->numero = $consecutivo;
      $facturaElectronica->num_factura = $resolucion->prefijo.'-'.$consecutivo;
      $facturaElectronica->prefijo = $resolucion->prefijo;
      $facturaElectronica->resolucion = $resolucion->resolucion;
      $facturaElectronica->token_electronica = $company->token;
      
      $json_envio->prefix = $facturaElectronica->prefijo;
      $json_envio->number = $facturaElectronica->numero;
      $json_envio->resolution_number = $facturaElectronica->resolucion;
      $json_envio->date = date('Y-m-d');
      $json_envio->time = date('H:i:s');
      $json_envio->payment_form->payment_due_date = date('Y-m-d');
      $facturaElectronica->json_envio = json_encode($json_envio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
      $facturaElectronica->actualizar();
      return $facturaElectronica->num_factura;
    }
}