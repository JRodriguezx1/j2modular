<?php

namespace App\Controllers;

use App\classes\Traits\DocumentTrait;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\usuarios; //namespace\clase hija
use App\Models\ActiveRecord;
use App\Models\caja\factmediospago;
use App\Models\clientes\departments;
use App\Models\clientes\municipalities;
use App\Models\configuraciones\notacreditoinvoice;
use App\Models\parametrizacion\config_local;
use App\Models\configuraciones\tipofacturador;
use App\Models\felectronicas\adquirientes;
use App\Models\felectronicas\diancompanias;
use App\Models\felectronicas\facturas_electronicas;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\Rules\FacturaElectronicaRules;
use App\services\facturaElectronicaService;

use stdClass;

class apidiancontrolador{

  use DocumentTrait;

  //////////////---------   API   ----------///////////////////
  public static function citiesXdepartments(){
    //session_start();
    isadmin();
    if(!tienePermiso('Habilitar modulo de configuracion')&&userPerfil()>=3)return;
    $alertas = [];
    $id = $_GET['id'];
    if(!is_numeric($id)){
        $alertas['error'][] = "Hubo un error el id del departamento no es valido";
        echo json_encode($alertas);
        return;
    }
    $idsucursal = id_sucursal();
    $dapartments = departments::all();
    $conflocal = config_local::getParamGlobal();

    $municipios = municipalities::idregistros('department_id', $id);
    echo json_encode($municipios);
  }


  public static function crearCompanyJ2(){
    //session_start();
    isadmin();
    $alertas = [];
    if(userPerfil()>1){
      $alertas['error'][] = "No tienes permisos";
      return;
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }

    $compañia = json_decode(file_get_contents('php://input'), true);
    $identification_number = $compañia['identification_number'];
    $companyExist = diancompanias::find('identification_number', $identification_number);
    if($companyExist){
      $companyExist->compara_objetobd_post($compañia);
      $c = $companyExist->actualizar();
      if($c){
        $alertas['exito'][] = "Compañia guardada localmente";
        $alertas['id'] = $companyExist->id;
      }else{
        $alertas['error'][] = "No se guardo la configuracion de la compañia";
      }
    }else{
      $diancompanias = new diancompanias($compañia);
      $diancompanias->estado = 1;
      $r = $diancompanias->crear_guardar();
      if($r[0]){
        $alertas['exito'][] = "Compañia guardada localmente";
        $alertas['id'] = $r[1];
      }else{
        $alertas['error'][] = "No se guardo la configuracion de la compañia";
      }
    }
    echo json_encode($alertas);
  }


  public static function getCompaniesAll(){
    isadmin();
    $compañias = diancompanias::all();
    echo json_encode($compañias);
  }

  public static function eliminarCompanyLocal(){
    //session_start();
    isadmin();
    $alertas = [];
    $id = $_GET['id'];
    if(!is_numeric($id)){
        $alertas['error'][] = "Hubo un error, el id de la compañia no es valido";
        echo json_encode($alertas);
        return;
    }
    $compañia = diancompanias::find('identification_number', $id);
    if($compañia){
      $r = $compañia->eliminar_registro();
      if($r){
        $alertas['exito'][] = "Compañia eliminada";
      }else{
        $alertas['error'][] = "No fue posible eliminar compañia";
      }
    }
    echo json_encode($alertas);
  }


  //guardar resolucion invoice cuando se consulta o se descarga de la Dian, de forma local.
  public static function guardarResolutionJ2(){
    //session_start();
    isadmin();
    $alertas = [];
    
    if(userPerfil()>1){
      $alertas['error'][] = "No tienes permisos";
      return $alertas;
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }

    $resolution = json_decode(file_get_contents('php://input'), true);
    $existeResolution = consecutivos::uniquewhereArray(['resolucion'=>$resolution['ResolutionNumber'], 'prefijo'=>$resolution['Prefix']]);
    if($existeResolution){
      $existeResolution->estado = 1;
      $alertas['exito'][] = "Resolucion descargada en sistema.";
      $existeResolution->nombretipofacturador = tipofacturador::find('id', $existeResolution->idtipofacturador);
      $alertas['facturador'] = $existeResolution;
    }else{
      $facturador = new consecutivos([
                                      'id_sucursalid'=>id_sucursal(), 
                                      'idcompania' => $resolution['idcompany'],
                                      'idtipofacturador'=>1, 
                                      'nombre'=>'Electronica '.$resolution['Prefix'], 
                                      'rangoinicial'=>$resolution['FromNumber'],
                                      'rangofinal'=>$resolution['ToNumber'],
                                      'siguientevalor'=>1,
                                      'fechainicio'=>$resolution['ValidDateFrom'],
                                      'fechafin'=>$resolution['ValidDateTo'],
                                      'resolucion'=>$resolution['ResolutionNumber'],
                                      'prefijo'=>$resolution['Prefix'],
                                      'mostrarresolucion'=>1,
                                      'mostrarimpuestodiscriminado'=>0,
                                      'electronica'=>1,
                                      'estado'=>1
                                    ]);
      //debuguear($facturador);
      $r = $facturador->crear_guardar();
      if($r[0]){
        $alertas['exito'][] = "Resolucion descargada en sistema.";
        $facturador->nombretipofacturador = tipofacturador::find('id', $facturador->idtipofacturador);
        $facturador->id = $r[1];
        $alertas['facturador'] = $facturador;
      }else{
        $alertas['error'][] = "Error al descargar resolucion, intentalo de nuevo";
      }
    }

    echo json_encode($alertas);
  }


  //guardar resolucion de nota credito invoice, de forma local.
  public static function guardarNCInvoiceJ2(){
    //session_start();
    isadmin();
    $alertas = [];

    if(userPerfil()>1){
      $alertas['error'][] = "No tienes permisos";
      return $alertas;
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }

    $resolution = json_decode(file_get_contents('php://input'), true);
    $existeResolution = notacreditoinvoice::uniquewhereArray(['id_compania'=>$resolution['idcompany'], 'prefix'=>$resolution['prefix']]);
    if($existeResolution){
      $existeResolution->estado = 1;
      $alertas['exito'][] = "Resolucion de NC ya disponible en sistema.";
    }else{
      $facturadornc = new notacreditoinvoice([
                                      'idsucursal_id_fk'=>id_sucursal(), 
                                      'id_compania' => $resolution['idcompany'],
                                      'type_document_id'=>$resolution['type_document_id'], 
                                      'prefix'=>$resolution['prefix'], 
                                      'resolution'=>'',
                                      'nextnumber'=>1,
                                      'resolution_date'=>date('Y-m-d'),
                                      'technical_key'=>$resolution['technical_key'], //identification_number
                                      'fromNC'=>$resolution['from'],
                                      'toNC'=>$resolution['to'],
                                      'date_from'=>'',
                                      'date_to'=>'',
                                      'estado'=>1
                                    ]);
      //debuguear($facturadornc);
      $r = $facturadornc->crear_guardar();
      if($r[0]){
        $alertas['exito'][] = "Resolucion descargada en sistema.";
      }else{
        $alertas['error'][] = "Error al descargar resolucion, intentalo de nuevo";
      }
    }
    echo json_encode($alertas);
  }


  public static function filterAdquirientes(){
    //session_start();
    isadmin();
    $alertas = [];
    $adquirientes = adquirientes::all();
    echo json_encode($adquirientes);
  }

  
  public static function guardarAdquiriente(){
    //session_start();
    isadmin();
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }
    $datosadquiriente = json_decode(file_get_contents('php://input'), true);
    $resAdq = facturaElectronicaService::createUpDateAdquiriente($datosadquiriente);
    echo json_encode($resAdq);
    return;
  }


  //Metodo usado en ventas.sendinvoice.ts para enviar una factura electronica desde ventas.ts
  public static function sendInvoice(){
    //session_start();
    isadmin();
    $alertas = [];
    
    $url = "https://apidianj2.com/api/ubl2.1/invoice"; 
    ///////////    enviar FE     /////////////
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }
    $idfactura = json_decode(file_get_contents('php://input'), true);
    $factura = facturas::find('id', $idfactura['id']);
    $allFacturasDian = facturas_electronicas::whereArray(['id_sucursalidfk'=>id_sucursal(), 'consecutivo_id'=>$factura->idconsecutivo, 'id_facturaid' => $idfactura['id'], 'nota_credito'=>0]);
    //obtener la factura electronica que esta pendiente de enviar..
    $filtrados = array_filter($allFacturasDian, function($obj) { return $obj->id_estadoelectronica != 2 && $obj->id_estadoelectronica != 4;});
    $facturaDian = reset($filtrados); //reset devuelve el primer elemento del arreglo
    
    if($facturaDian && $factura->estado == 'Paga' && $facturaDian->id_estadoelectronica != 2 && $facturaDian->id_estadoelectronica != 4){
      //si es una factura pendiente o error, obtener el json y actualizar las fechas de envio
      $json_envioDateUP = facturaElectronicaService::actualizarFechaEnvioInvoice(json_decode($facturaDian->json_envio));
      $facturaDian->json_envio = $json_envioDateUP;
      $res = self::sendInvoiceDian($facturaDian->json_envio, $url, $facturaDian->token_electronica);

      if(!$res['success']){
        $alertas['error'][] = $res['error'];
        $alertas['cufe'] = "fe9c733f32770f5fcc4ef954f9ef663c54c752e6c07bdc144bb00627faadf9f648818da3e96ef8293547140fb1970d22";
        $alertas['link'] = "https://catalogo-vpfe.dian.gov.co/User/SearchDocument";
        echo json_encode($alertas);
        return;
      }
    
      //actualizar respuesta de la dian en la tabla facturas_electronicas
      if($res['success'] && buscarClaveArray($res, 'IsValid')=='true'){
        $arrayFile = explode('.', buscarClaveArray($res, 'urlinvoicexml'));
        $facturaDian->id_estadoelectronica = 2; //aceptada
        $facturaDian->cufe = buscarClaveArray($res, 'cufe');
        $facturaDian->qr = buscarClaveArray($res, 'QRStr');
        $facturaDian->filename = "$facturaDian->nitcompany/$arrayFile[0]";
        $facturaDian->link =  $facturaDian->qr;
        $mensaje = $res["response"]["ResponseDian"]["Envelope"]["Body"]["SendBillSyncResponse"]["SendBillSyncResult"];
        $error = $mensaje["ErrorMessage"]["string"];
        if(!is_array($error))$error = [$error]; // convertir string en array
        $facturaDian->respuesta_factura = join(' // ', $error).', IsValid = '.$mensaje["IsValid"].', StatusDescription = '.$mensaje["StatusDescription"].', StatusMessage = '.$mensaje["StatusMessage"];
        $facturaDian->fecha_ultimo_intento = date('Y-m-d H:i:s');
        $r = $facturaDian->actualizar();
        $alertas['exito'][] = "Factura electronica procesadamente exitosamente.";
        $alertas['cufe'] = $facturaDian->cufe;
        $alertas['link'] = $facturaDian->link;
        echo json_encode($alertas);
        return;
      }else{
        $facturaDian->id_estadoelectronica = 3; //error
        $facturaDian->cufe = '';
        $facturaDian->qr = '';
        $facturaDian->link =  '';
        $mensaje = $res["response"]["ResponseDian"]["Envelope"]["Body"]["SendBillSyncResponse"]["SendBillSyncResult"];
        $error = $mensaje["ErrorMessage"]["string"];
        if(!is_array($error))$error = [$error]; // convertir string en array
        $facturaDian->respuesta_factura = join(' // ', $error).', IsValid = '.$mensaje["IsValid"].', StatusDescription = '.$mensaje["StatusDescription"].', StatusMessage = '.$mensaje["StatusMessage"];
        $facturaDian->fecha_ultimo_intento = date('Y-m-d H:i:s');
        $r = $facturaDian->actualizar();
        $alertas['error'][] = "Error al enviar la factura electronica. $facturaDian->respuesta_factura";
        echo json_encode($alertas);
        return;
      }
    }else{
      $alertas['error'][] = "Error factura no se encuentra como pendiente de enviar a Dian o no esta paga.";
      echo json_encode($alertas);
      return;
    }
  }
  
  
  public static function sendNc(){
    //session_start();
    isadmin();
    $alertas = [];
    $getDB = facturas_electronicas::getDB();
    $url = "https://apidianj2.com/api/ubl2.1/credit-note";
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }
    $datos = json_decode(file_get_contents('php://input'), true);
    $facturaDian = facturas_electronicas::uniquewhereArray(['id'=>$datos['id'], 'id_sucursalidfk'=>id_sucursal(), 'id_facturaid'=>$datos['idfactura']]);
    $companyExist = diancompanias::find('identification_number', $facturaDian->nitcompany);
    if($companyExist){
      
      $resolInvoiceNc = notacreditoinvoice::find('id_compania', $companyExist->id);
      
      if($datos['consecutivo']!=''&&is_numeric($datos['consecutivo'])) //consecutivo personalizado
        $resolInvoiceNc->nextnumber = $datos['consecutivo'];
     
      if($facturaDian&&$facturaDian->id_estadoelectronica == 2&&($facturaDian->nota_credito==0 || $facturaDian->id_estadonota != 2)){ //la factura electronica debe estar en estado aceptado por la Dian 
        $jsonenvio = json_decode($facturaDian->json_envio);
        $jsonNcDian = self::createNcElectronic($jsonenvio, $facturaDian->numero, $facturaDian->prefijo, $facturaDian->cufe, $facturaDian->fecha_factura, $resolInvoiceNc);
        $res = self::sendInvoiceDian($jsonNcDian, $url, $facturaDian->token_electronica);
        if(!$res['success']){
          $alertas['error'][] = $res['error'];
          //...
          echo json_encode($alertas);
          return;
        }

        //debuguear($res);
        if($res['success'] && buscarClaveArray($res, 'IsValid')=='true'){
          // actualizar consecutivo en la tabla de notacreditoinvoice
          $getDB->begin_transaction();
          try {
            $arrayFile = explode('.', buscarClaveArray($res, 'urlinvoicexml'));
            $facturaDian->id_estadonota = 2;
            //$facturaDian->nota_credito = 1; //que es nc
            $facturaDian->prefixnc = $resolInvoiceNc->prefix;
            $facturaDian->num_nota = $resolInvoiceNc->nextnumber;
            $facturaDian->cufe_nota = buscarClaveArray($res, 'cude');
            $facturaDian->qrnc = buscarClaveArray($res, 'QRStr');
            $facturaDian->linknc =  $facturaDian->qrnc;
            $facturaDian->filenamenc = "$facturaDian->nitcompany/$arrayFile[0]";
            $facturaDian->fecha_nota = date('Y-m-d H:i:s');
            $facturaDian->json_envionc = $jsonNcDian;
            $mensaje = $res["response"]["ResponseDian"]["Envelope"]["Body"]["SendBillSyncResponse"]["SendBillSyncResult"];
            $error = $mensaje["ErrorMessage"]["string"];
            if(!is_array($error))$error = [$error]; // convertir string en array
            $facturaDian->respuesta_nota = join(' // ', $error).', IsValid = '.$mensaje["IsValid"].', StatusDescription = '.$mensaje["StatusDescription"].', StatusMessage = '.$mensaje["StatusMessage"];
            //$r = $facturaDian->actualizar();

            if($datos['consecutivo']==''&&!is_numeric($datos['consecutivo'])&&($facturaDian->nota_credito == 0)){ //si no es consecutivo personalizado
              $resolInvoiceNc->nextnumber += 1;
              $resolInvoiceNc->actualizar();
            }
            
            $facturaDian->nota_credito = 1; //que es nc
            $r = $facturaDian->actualizar();

            $alertas['exito'][] = "Nota credito procesadamente exitosamente.";
            $alertas['notacredito'] = $facturaDian;
            $getDB->commit();
            echo json_encode($alertas);
            return;
          } catch (\Throwable $th) {
            $getDB->rollback();
            $alertas['error'][] = "Error en base de datos al generar la nota credito. ".$th->getMessage();
            $alertas['notacredito'] = $facturaDian;
            echo json_encode($alertas);
            return;
          }
        }else{
          $facturaDian->id_estadonota = 3; //error
          //$facturaDian->nota_credito = 1; //que es nc
          $facturaDian->prefixnc = $resolInvoiceNc->prefix;
          $facturaDian->num_nota = $resolInvoiceNc->nextnumber;
          $facturaDian->fecha_nota = date('Y-m-d H:i:s');
          $facturaDian->json_envionc = $jsonNcDian;
          $mensaje = $res["response"]["ResponseDian"]["Envelope"]["Body"]["SendBillSyncResponse"]["SendBillSyncResult"];
          $error = $mensaje["ErrorMessage"]["string"];
          if(!is_array($error))$error = [$error]; // convertir string en array
          $facturaDian->respuesta_nota = join(' // ', $error).', IsValid = '.$mensaje["IsValid"].', StatusDescription = '.$mensaje["StatusDescription"].', StatusMessage = '.$mensaje["StatusMessage"];
          //$r = $facturaDian->actualizar();

          if($datos['consecutivo']==''&&!is_numeric($datos['consecutivo'])&&($facturaDian->nota_credito == 0)){
            $resolInvoiceNc->nextnumber += 1;
            $resolInvoiceNc->actualizar();
          }

          $facturaDian->nota_credito = 1; //que es nc
          $r = $facturaDian->actualizar();
          
          $alertas['error'][] = "Error al generar nota credito. $facturaDian->respuesta_nota";
          $alertas['notacredito'] = $facturaDian;
          echo json_encode($alertas);
          return;
        }
      }else{
        $alertas['error'][] = "Error, la factura electronica no esta aceptada por la Dian o ya se genero nota credito.";
        echo json_encode($alertas);
        return;
      }
    }else{
      $alertas['error'][] = "Error, no se puede generar nota credito, compañia no existe";
      echo json_encode($alertas);
      return;
    }
  }


  //METODO LLAMADO DESDE VISTA DETALLEINVOICE BTN + NUEVA FACTURA
  public static function crearFacturaPOSaElectronica(){
    //session_start();
    isadmin();
    $alertas = [];
    $existeInvoice = null;

    // -------------------------------
    // Datos iniciales
    // -------------------------------
    $idfactura = $_POST['idfactura']; //id de la factura general
    $idconsecutivo = $_POST['idResolution']; //id de la resolucion o consecutivo, viene del front ya por sucursal filtrada
    $numConsecutivoManual = $_POST['numConsecutivoManual'];
    
    $factura = facturas::find('id', $idfactura);
    $productos = ventas::idregistros('idfactura', $factura->id);
    $datosAdquiriente = json_decode(json_encode(adquirientes::find('id', 1)));
    $mediospago = factmediospago::idregistros('id_factura', $factura->id);

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      // -------------------------------
      // Consecutivo DIAN
      // -------------------------------
      $consecutivo = consecutivos::findForUpdate('id', $idconsecutivo);
      $numConsecutivo = $numConsecutivoManual!==''&&is_numeric($numConsecutivoManual)?$numConsecutivoManual:$consecutivo->siguientevalor;
                        
      if($numConsecutivoManual===''||!is_numeric($numConsecutivoManual))
        $consecutivo->siguientevalor++;
      
      // -------------------------------
      // Buscar FE existente con este consecutivo
      // -------------------------------
      $validationFE = FacturaElectronicaRules::validarSiPuedeGenerarFE($factura, $idconsecutivo, $numConsecutivo);
      if(!$validationFE['success']){
        $alertas['error'][] =  $validationFE['message'];
        echo json_encode($alertas);
        return;
      }
      // SI ESTÁ ANULADA → RECICLAR
      if($validationFE['reuse']){ // si una facturaelectronica es = 4, eliminada o anulada
        // Si pertenece a otra factura POS, reasignar datos de la original
          if($validationFE['fe']->id_facturaid != $factura->id){  //$validationFE['fe'] es el registro de factura electronica que coincide con el numero e id de resolucion y esta eliminada
            facturaElectronicaService::reciclarFacturaElectronica($validationFE['fe']);
          }
          // Reusar FE
          $validationFE['fe']->id_facturaid = $factura->id;
          $validationFE['fe']->id_estadoelectronica=1;
          $existeInvoice = $validationFE['fe'];
      }

      // -------------------------------
      // Si se encontró FE reciclable
      // -------------------------------
      if($existeInvoice){
        facturaElectronicaService::actualizarFacturaConsecutivo($factura, $consecutivo, $numConsecutivo);
        $existeInvoice->actualizar();
        $alertas['exito'][] = "Factura electronica regenerada con exito.";
        $alertas['facturaelectronica'] = $existeInvoice;
        echo json_encode($alertas);
        return;
      }

      // -------------------------------
      // Crear nueva FE
      // -------------------------------
      facturaElectronicaService::actualizarFacturaConsecutivo($factura, $consecutivo, $numConsecutivo);
      $rfe = self::createInvoiceElectronic($productos, $datosAdquiriente, $consecutivo->id, $idfactura, $factura->num_consecutivo, $mediospago, $factura->descuento, $factura->valortarifa, '');
      if($rfe[0]){
        $alertas['exito'][] = "Factura electronica creada exitosamente.";
        $fe = new stdClass();
        $fe->id = $rfe[1];
        $fe->id_estadoelectronica = 1;
        $fe->numero = $factura->num_consecutivo;
        $fe->num_factura = $factura->prefijo.'-'.$factura->num_consecutivo;
        $fe->prefijo = $factura->prefijo;
        $fe->id_facturaid = $factura->id;
        $fe->id_adquiriente = 1;
        $fe->id_estadonota = 1;
        $fe->nota_credito = 0;
        $fe->prefixnc = '';
        $fe->num_nota = '';
        $alertas['facturaelectronica'] = $fe;
        echo json_encode($alertas);
        return;
      }else{
        $alertas['error'][] = "Error al convertir la factura a factura electronica.";
        echo json_encode($alertas);
        return;
      }
      
    }
  }


  //asigna adquiriente a la ultima factura electronica.
  public static function asignarAdquirienteAFactura(){
    //session_start();
    isadmin();
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }
    $datosadquiriente = json_decode(file_get_contents('php://input'), true);
    //obtener ultima factura electronica creada
    $facturaDian = facturas_electronicas::uniquewhereArray(['id'=>$datosadquiriente['idfe'], 'id_sucursalidfk'=>id_sucursal(), 'id_facturaid'=>$datosadquiriente['idfactura']]);
    if($facturaDian){
      $resAdq = facturaElectronicaService::createUpDateAdquiriente($datosadquiriente);
      $facturaDian->id_adquiriente = $resAdq['id'];
      $r = $facturaDian->actualizar();
      if($r){
        $alertas['exito'][] = 'Adquiriente asignado correctamente';
        $alertas['tipo'] = $resAdq['tipo'];
        $alertas['obj'] = $resAdq['obj'];
      }else{
        $alertas['error'][] = 'No se pudo asignar adquiriente a la factura electronica';
      }
    }else{
      $alertas['error'][] = 'Factura electronica no existe';
    }
    echo json_encode($alertas);
    return;
  }


  public static function eliminarFacturaElectronica(){
    //session_start();
    isadmin();
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }
    $idfe = json_decode(file_get_contents('php://input'), true);
    $idfe = $idfe['id'];
    $facturaElectronica = facturas_electronicas::uniquewhereArray(['id'=>$idfe, 'id_sucursalidfk'=>id_sucursal()]);
    $facturaElectronica->id_estadoelectronica = 4;
    $r = $facturaElectronica->actualizar();
    if($r){
      facturaElectronicaService::reciclarFacturaElectronica($facturaElectronica);
      $alertas['exito'][] = 'Factura electronica eliminada';
    }
    echo json_encode($alertas);
    return;
  }


  public static function editarResolutionFE():void{
    isadmin();
    //header('Content-Type: application/json');
    $alertas = [];
    $getDB = facturas_electronicas::getDB();

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      http_response_code(405); // Método no permitido
      echo json_encode(['error' => 'Método no permitido']);
      exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON inválido']);
        exit;
    }
    $getDB->begin_transaction();
    try {
      $alertas['prefijoNum'] = facturaElectronicaService::actualizarResolutionFE($data);
      $getDB->commit();
      $alertas['exito'][] = 'Cambio de datos de resolucion de factura electronica.';
    } catch (\Throwable $th) {
      $getDB->rollback();
      $alertas['error'][] = "Error al actualizar los datos de la resolucion de la factura electronica. ".$th->getMessage();
    }
    echo json_encode($alertas);
    return;
  }
  

}
