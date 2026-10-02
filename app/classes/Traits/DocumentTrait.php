<?php

namespace App\Classes\Traits;

use App\Models\configuraciones\consecutivos;
use App\Models\felectronicas\diancompanias;
use App\Models\felectronicas\facturas_electronicas;
use stdClass;

trait DocumentTrait
{

    //metodo llamado desde ventascontrolador cuando se hace la venta, para guardar la FE de manera local
    protected static function createInvoiceElectronic(array $carrito, stdClass $datosAdquiriente, $idconsecutivo, $idfactura, $numconsecutivo, array $mediospago, float $descgeneral, float $cargo, string $observacion):array
    {
        date_default_timezone_set('America/Bogota');
        $invoice_lines = [];
        $tax_summary = []; // para agrupar impuestos
        $line_extension_total = 0;
        $tax_exclusive_amount = 0;
        $tax_inclusive_total = 0;
        $tax_total = 0;
        $metodoPago = 10;  //contado
        $stateEmail = false;

        if(isset($datosAdquiriente->email) && $datosAdquiriente?->email!='')$stateEmail=true;
        
        foreach($mediospago as $value)
            if($value->idmediopago != 1){
                $metodoPago = 47;
                break;
            }


        $consecutivo = consecutivos::find('id', $idconsecutivo);
        $compañia = diancompanias::find('id', $consecutivo->idcompania);

        //solo si es tipo de facturador ELECTRONICO
        if($consecutivo->estado && $consecutivo->idtipofacturador == 1){
            $customer = [
                "identification_number" => $datosAdquiriente->identification_number??"222222222222",  //obligatorio
                "dv" => $datosAdquiriente->dv??null,
                "name" => $datosAdquiriente->business_name??"Consumidor Final",  //obligatorio
                "phone" => $datosAdquiriente->phone??null,
                "address" => $datosAdquiriente->address??null,
                "email" => $datosAdquiriente->email??null,
                "type_document_identification_id" => $datosAdquiriente->type_document_identification_id??3,
                "type_organization_id" => $datosAdquiriente->type_organization_id??null,
                "tax_id" => $datosAdquiriente->tax_id??null,
                "type_liability_id" => $datosAdquiriente->type_liability_id??null,
                "type_regime_id" => $datosAdquiriente->type_regime_id??null,
                "municipality_id" => $datosAdquiriente->municipality_id??null
            ];


            foreach($carrito as $index => $value){
                $invoice_lines[$index] = [
                    "type_item_identification_id" => "4", // tipo estándar
                    "code" => $value->idproducto,
                    "description" => $value->nombreproducto,
                    "invoiced_quantity" => strval($value->cantidad),
                    "unit_measure_id" => "70", // unidad por defecto: unidad
                    "line_extension_amount" => number_format($value->base, 2, '.', ''),
                    "free_of_charge_indicator" => false,
                    "price_amount" => number_format($value->base/$value->cantidad, 2, '.', ''),
                    "base_quantity" => strval($value->cantidad)
                ];

                if(is_numeric($value->impuesto)&&$value->impuesto>=0){
                    $invoice_lines[$index]["tax_totals"] = [
                        [
                            "tax_id" => $value->impuesto==8?4:1, // 1 = IVA code 01   -   4 = INC code 04
                            "tax_amount" => number_format($value->valorimp, 2, '.', ''),
                            "taxable_amount" => number_format($value->base, 2, '.', ''),
                            "percent" => $value->impuesto
                        ],
                    ];

                    // Agrupar impuestos por porcentaje
                    $percent = $value->impuesto;
                    if (!isset($tax_summary[$percent])) {
                        $tax_summary[$percent] = [
                            "tax_id" => $value->impuesto==8?4:1, // 1 = IVA code 01   -   4 = INC code 04
                            "percent" => $percent,
                            "taxable_amount" => 0,
                            "tax_amount" => 0
                        ];
                    }
                    $tax_summary[$percent]["taxable_amount"] += $value->base;
                    $tax_summary[$percent]["tax_amount"] += $value->valorimp;
                    $tax_exclusive_amount += $value->base;
                }   


                // Acumulados
                $line_extension_total += $value->base;
                $tax_total += $value->valorimp;
                $tax_inclusive_total += $value->total;
            }

            // Convertir el resumen de impuestos en arreglo
            $tax_totals = array_values(array_map(function($t) {  //tax_summary = ["8" =>[], "19"=>[],...]
                return [
                    "tax_id" => $t["tax_id"],
                    "tax_amount" => number_format($t["tax_amount"], 2, '.', ''),
                    "percent" => $t["percent"],
                    "taxable_amount" => number_format($t["taxable_amount"], 2, '.', '')
                ];
            }, $tax_summary));

            //CARGO GENERAL ADICIONAL
            $cargos = [
                "charge_id" => 1,
                "charge_indicator" => true,
                "allowance_charge_reason" => "CARGO GENERAL ADICIONAL",
                "amount" => number_format($cargo, 2, '.', ''),
                "base_amount"  => number_format($tax_inclusive_total, 2, '.', '')                
            ];
            //DESCUENTO GENERAL
            $descuentos = [
                "discount_id" => 1,
                "charge_indicator" => false,
                "allowance_charge_reason" => "DESCUENTO GENERAL",
                "amount" => number_format($descgeneral, 2, '.', ''),
                "base_amount"  => number_format($tax_inclusive_total, 2, '.', '')                
            ];
            $allowance_charges = [];
            if($cargo>0)$allowance_charges[] = $cargos;
            if($descgeneral>0) $allowance_charges[] = $descuentos;

            // Totales monetarios
            $legal_monetary_totals = [
                "line_extension_amount" => number_format($line_extension_total, 2, '.', ''),  //sin impuesto
                "tax_exclusive_amount" => number_format(empty($tax_totals)?0:$tax_exclusive_amount, 2, '.', ''),  //sin impuesto, ó '0' si es excluido de impuesto
                "tax_inclusive_amount" => number_format($tax_inclusive_total, 2, '.', ''),  //con impuesto
                "allowance_total_amount" => number_format($descgeneral, 2, '.', ''),
                "charge_total_amount" => number_format($cargo, 2, '.', ''),
                "payable_amount" => number_format($tax_inclusive_total+$cargo-$descgeneral, 2, '.', '')
            ];

            // Armar la factura final
            $factura = [
                "prefix" => $consecutivo->prefijo,
                "number" => $numconsecutivo, 
                "type_document_id" => "1",
                "date" => date('Y-m-d'),
                "time" => date('H:i:s'),
                "resolution_number" => $consecutivo->resolucion,
                "sendmail" => $stateEmail,
                "notes" => "Factura Electroncia de venta. ".$observacion,
                "payment_form" => [
                    "payment_form_id" => "1",  //1 = contado,  2  = credito
                    "payment_method_id" => $metodoPago,  //efectivo, transferencia etc
                    "payment_due_date" => date('Y-m-d'),
                    "duration_measure" => "0"
                ],
                "customer" => $customer,
                "invoice_lines" => $invoice_lines,
                //"tax_totals" => $tax_totals,
                //"allowance_charges" => $allowance_charges,
                "legal_monetary_totals" => $legal_monetary_totals
            ];

            if(!empty($tax_totals))$factura["tax_totals"] = $tax_totals;
            if($descgeneral>0 || $cargo>0)$factura["allowance_charges"] = $allowance_charges;


            // generar json de la factura Dian
            $jsonDian = json_encode($factura, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            //guardar en tabla facturas_electronicas
            $facturasElectronicas = new facturas_electronicas([
                'id_sucursalidfk'=>id_sucursal(),
                'id_estadoelectronica'=>1, //pendiente
                'consecutivo_id'=>$consecutivo->id,
                'id_facturaid'=>$idfactura,  //id del pedido o orden generado en la tabla factura
                'id_adquiriente'=>$datosAdquiriente->id??1,
                'id_estadonota'=>1,
                'numero'=>$numconsecutivo,
                'num_factura'=>$consecutivo->prefijo.'-'.$numconsecutivo,
                'prefijo'=>$consecutivo->prefijo,
                'resolucion'=>$consecutivo->resolucion,
                'token_electronica'=>$compañia->token,  //token de la compañia
                'cufe'=>'',
                'qr'=>'',
                'nitcompany'=>$compañia->identification_number,
                'fecha_factura'=>date('Y-m-d H:i:s'),  //fecha de recepcion por la Dian
                'identificacion'=>$customer['identification_number'],
                'nombre'=>$customer['name'],
                'email'=>$customer['email'],
                'link'=>'',
                'nota_credito'=>0,  //0 = no es una nota credito,  1 = si es nota credito
                'prefixnc' => '',
                'num_nota'=>'',
                'cufe_nota'=>'',
                'qrnc' => '',
                'linknc' => '',
                'filenamenc' => '',
                'fecha_nota'=>'',
                'is_auto'=> 1,   //cuando se envia de manera automatica es 1, si la factua se envia de manera manual es 0.
                'json_envio'=>$jsonDian,
                'json_envionc' => '',
                'respuesta_factura'=>'',
                'respuesta_nota'=>'',
                'intentos_de_envio'=>1,
                'fecha_ultimo_intento'=>date('Y-m-d H:i:s')
            ]);

            $rfe = $facturasElectronicas->crear_guardar();
            return $rfe;
        }else{  //resolucion desactivada en sistema
            return [false];
        }
    } //fin metodo crear factura electronica


    protected static function createNcElectronic(stdClass $jsonenvio, $number, $prefix, $cufe, $fecha, $resolInvoiceNc):string
    {
        date_default_timezone_set('America/Bogota');
        $stateEmail = false;
        $billing_reference = [
            "number" => "$prefix$number", //"FE2082",
            "uuid" => $cufe,
            "issue_date" => substr($fecha, 0, 10) //"2025-07-01"
        ];

        $customer = $jsonenvio->customer;
        $credit_note_lines = $jsonenvio->invoice_lines;
        $tax_totals = $jsonenvio->tax_totals;
        $legal_monetary_totals = $jsonenvio->legal_monetary_totals;

        if(isset($customer->email))$stateEmail = true;

        // Armar la nota credito final
        $notaCredito = [
            "billing_reference" => $billing_reference,
            "discrepancyresponsecode" => 2,
            "discrepancyresponsedescription" => "Nota credito total a factura",
            "notes" => "Nota credito",
            "prefix" => $resolInvoiceNc->prefix,   //variable resol nc esta en la api
            "number" => $resolInvoiceNc->nextnumber,        //variable resol nc esta en la api
            "type_document_id" => 4,
            "date" => date('Y-m-d'),   //fecha actual de la generacion de la NC
            "time" => date('H:i:s'),
            "type_operation_id" => 12,
            "sendmail" => $stateEmail,
            "sendmailtome" => false,
            "head_note" => "Este documento es una nota crédito con referencia generada automáticamente.",
            "foot_note" => "Gracias por su atención. Cualquier duda comuníquese con servicio al cliente.",
            "customer" => $customer,
            "credit_note_lines" => $credit_note_lines,
            "tax_totals" => $tax_totals,
            "legal_monetary_totals" => $legal_monetary_totals
        ];

        if(property_exists($jsonenvio, 'allowance_charges'))$notaCredito["allowance_charges"] = $jsonenvio->allowance_charges;
        // generar json de la nota credito Dian
        $jsonNcDian = json_encode($notaCredito, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $jsonNcDian;
    }
    

    protected static function sendInvoiceDian(string $jsonenvio, $url, $token):array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonenvio);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
         // SOLUCIÓN TEMPORAL - Solo desarrollo
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Verificar si hubo error de cURL
        if($response === false || curl_errno($ch)){
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            return ['success' => false, 'error' => "Error de conexión: ($errno): $error"];
        }
        curl_close($ch);
        
        $decoded = json_decode($response, true);
        if($httpcode >= 200 && $httpcode < 300)return [ 'success' => true, 'status' => $httpcode, 'response' => $decoded, ];
        
        // Error HTTP
        return [
            'success' => false,
            'status' => $httpcode,
            'error' => $decoded['message'] ?? 'Error en la API',
            'response' => $decoded ?: $response,
        ];
    }



}
