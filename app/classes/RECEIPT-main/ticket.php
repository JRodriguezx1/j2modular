<?php

	# Incluyendo librerias necesarias #

use App\Models\parametrizacion\config_local;

    require __DIR__ ."/code128.php";
    //require __DIR__ ."/../../public/build/img/logoj2blanco.png";

    class ticketPOS{
        
        private $pdf;
        private $conflocal;
        private $fontSizeDatosFactura;
        private $fontSizeCliFactura;
        private $fontSizeProductsFactura;
        private $matrizFontSize =[];

        public function __construct(){
            $this->pdf = new PDF_Code128('P','mm',array(80,258));
            $this->pdf->SetMargins(4,10,4);
            $this->pdf->AddPage();
            $this->conflocal = config_local::getParamGlobal();
            $this->fontSizeDatosFactura = $this->conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final;
            $this->fontSizeCliFactura = $this->conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final;
            $this->fontSizeProductsFactura = $this->conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final;
            $this->matrizFontSize = [1=>['', 8], 2=>['B', 8], 3=>['', 9], 4=>['B', 9], 5=>['', 10], 6=>['B', 10], 7=>['', 12], 8=>['B', 12]];
        }


        public function generar($sucursal, $lineasencabezado, $factura, $facturaElectronica, $cliente, $direccion, $productos=[], object|null $emisor = null){

            $existe_archivo = !empty($sucursal->logo)&&file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/$sucursal->logo");
            if(!$existe_archivo) $sucursal->logo = "Logoj2negro.png";
            if(!$emisor)
                $this->pdf->Image(__DIR__ . '/../../../public/build/img/'.$sucursal->logo, 20, 5, 40, 28); // (ruta, x, y, ancho)
            $this->pdf->Ln(25);
            # Encabezado y datos de la empresa #
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($factura->nombrecompania??' - ')),0,'C',false);
            $this->pdf->SetFont('Arial','B',8);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($sucursal->nombre)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);
            if($lineasencabezado == [])
                $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: ".($sucursal->nit??' - ')),0,'C',false);
            //LINEA DE ENCABEZADO SEGUN EMPRESA O EMISOR
            foreach($lineasencabezado as $value)$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", $value),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Direccion: ".$sucursal->direccion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$sucursal->movil),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: ".$sucursal->email),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            //////////  FACTURA ELECTRONICA  ///////////
            if($facturaElectronica != null){
                $this->pdf->SetFont('Arial','B',9);
                $this->pdf->MultiCell(0,7,iconv("UTF-8", "ISO-8859-1","FACTURA ELECTRONICA DE VENTA"),0,'C',false);
            }

            $this->pdf->SetFont('Arial','',9);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: ".$factura->fechapago),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Forma de pago: ".$factura->tipoventa),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja: ".$factura->caja),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cajero: ".$factura->vendedor),0,'C',false);
            $this->pdf->SetFont('Arial','B',10);

            if($factura->estado == 'Guardado'){
                 $this->pdf->Ln(3);
            }else{
                $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Comprobante N°: ".$factura->num_orden)),0,'C',false);
            }
            //
            $factura->estado == 'Guardado'
                ?$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("COTIZACION")),0,'C',false)
                :$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("FACTURA: ".$factura->prefijo.''.$factura->num_consecutivo)),0,'C',false);
            
            $this->pdf->SetFont('Arial','',9);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $fs = $this->matrizFontSize[$this->fontSizeCliFactura];
            $this->pdf->SetFont('Arial',$fs[0],$fs[1]);
            
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: ".($cliente?->nombre??'').' '.$cliente?->apellido??''),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Documento: ".($cliente??null)?->identificacion??''),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".($cliente??null)?->telefono??''),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Dirección: ".$direccion->direccion.': '.$direccion->ciudad.' - '.$direccion->departamento),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);

            ////////////  ADQUIRIENTE  ////////////

            # Tabla de productos #
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Cant."),0,0,'C');
            $this->pdf->Cell(19,5,iconv("UTF-8", "ISO-8859-1","Precio"),0,0,'C');
            $this->pdf->Cell(15,5,iconv("UTF-8", "ISO-8859-1","Desc."),0,0,'C');
            $this->pdf->Cell(28,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');
            $this->pdf->SetFont('Arial','',10);

            $this->pdf->Ln(3);
            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);



            $fs = $this->matrizFontSize[$this->fontSizeProductsFactura];
            $this->pdf->SetFont('Arial',$fs[0],$fs[1]);
            /*----------  Detalles de la tabla  ----------*/
            foreach($productos as $value){
                $this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1", $value->nombreproducto),0,'C',false); //nombre producto
                $this->pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1", $value->cantidad),0,0,'C');  //cantidad
                $this->pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1",'$'.number_format($value->valorunidad, 2, ',', '.')),0,0,'C');  //precio unidad
                $this->pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1",$value->descuento),0,0,'C'); //descuento
                $this->pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1",'$'.number_format($value->total, 2, ',', '.')),0,0,'C'); //precio total
                $this->pdf->Ln(4);
            }
            //$this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Garantía de fábrica: 2 Meses"),0,'C',false);
            $this->pdf->Ln(7);
            /*----------  Fin Detalles de la tabla  ----------*/


            $this->pdf->SetFont('Arial','',10);
            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');

            $this->pdf->Ln(5);

            # Impuestos, descuentos & totales #
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","SUBTOTAL"),0,0,'C');
            $this->pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","+ $".number_format($factura->subtotal, 2, ',', '.')." COP"),0,0,'C');

            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Impuesto"),0,0,'C');
            $this->pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","+ $".number_format($factura->valorimpuestototal, 2, ',', '.')." COP"),0,0,'C');

            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Descuento"),0,0,'C');
            $this->pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","- $".number_format($factura->descuento, 2, ',', '.')." COP"),0,0,'C');

            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Tarifa envio"),0,0,'C');
            $this->pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","+ $".number_format($factura->valortarifa??0, 2, ',', '.')." COP"),0,0,'C');

            $this->pdf->Ln(5);

            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');

            $this->pdf->Ln(5);

            $this->pdf->Cell(16,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","TOTAL ".($factura->estado == 'Paga'?'A PAGAR':'COTIZACION:')),0,0,'C');
            $this->pdf->Cell(48,5,iconv("UTF-8", "ISO-8859-1","$".number_format($factura->total, 2, ',', '.')." COP"),0,0,'C');
            $this->pdf->SetFont('Arial','',10);

            if($factura->estado == 'Paga'){
                $this->pdf->Ln(5);
                $this->pdf->Cell(16,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
                $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","TOTAL PAGADO"),0,0,'C');
                $this->pdf->Cell(48,5,iconv("UTF-8", "ISO-8859-1","$".number_format($factura->total, 2, ',', '.')." COP"),0,0,'C');
                $this->pdf->Ln(5);
                $this->pdf->Cell(16,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
                $this->pdf->Cell(20,5,iconv("UTF-8", "ISO-8859-1","CAMBIO"),0,0,'C');
                $this->pdf->Cell(38,5,iconv("UTF-8", "ISO-8859-1","$0.00 COP"),0,0,'C');
                $this->pdf->Ln(5);
                $this->pdf->Cell(16,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
                $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","USTED AHORRA"),0,0,'C');
                $this->pdf->Cell(42,5,iconv("UTF-8", "ISO-8859-1","$0.00 COP"),0,0,'C');

                //////////////  MEDIO DE PAGO  ////////////
                $this->pdf->Ln(5);
                $this->pdf->SetFont('Arial','',8);
                foreach($factura->mediosdepago as $value){
                    $this->pdf->Ln(5);
                    $this->pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
                    $this->pdf->Cell(24,5,iconv("UTF-8", "ISO-8859-1", $value->mediopago),0,0,'C');
                    $this->pdf->Cell(24,5,iconv("UTF-8", "ISO-8859-1","$".number_format($value->valor, 2, ',', '.')." COP"),0,0,'C');
                }
            }

            $this->pdf->Ln(14);

            //////////////  OBSERVACIONES //////////////
            if($factura->observacion){
                $this->pdf->SetFont('Arial','',10);
                $this->pdf->Cell(0,4,iconv("UTF-8", "ISO-8859-1","OBSERVACION:"),0,'l',false);
                $this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1",$factura->observacion),0,'l',false);
                $this->pdf->Ln(8);
            }

            //////////////  RESOLUCION  ////////////
            if($facturaElectronica != null){
                $this->pdf->SetFont('Arial','',8);
                $this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Resolucion N° $facturaElectronica->resolucion, rango: desde {$facturaElectronica->consecutivo->rangoinicial} hasta {$facturaElectronica->consecutivo->rangofinal}, prefijo: $facturaElectronica->prefijo, vigencia: {$facturaElectronica->consecutivo->fechafin}"),0,'C',false);
                $this->pdf->Ln(5);
                $this->pdf->SetFont('Arial','',7);
                $this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","CUFE:$facturaElectronica->cufe"),0,'C',false);
                $this->pdf->Ln(5);
            }

            $this->pdf->SetFont('Arial','',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

            $this->pdf->SetFont('Arial','B',9);
            $this->pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

            $this->pdf->Ln(9);

            # Codigo de barras #
            $this->pdf->Code128(5,$this->pdf->GetY(),"COD000001V0001",70,20);
            $this->pdf->SetXY(0,$this->pdf->GetY()+21);
            $this->pdf->SetFont('Arial','',14);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001V0001"),0,'C',false);
            
            # Nombre del archivo PDF #
            $this->pdf->Output("I","factura $factura->prefijo $factura->num_consecutivo.pdf",true);
        }


        public function generarCredito($sucursal, $lineasencabezado, $credito, $usuario, $cliente, $direccion, $productos=[], $cuotas=[], object|null $identidadFiscal = null){
            $existe_archivo = !empty($sucursal->logo)&&file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/$sucursal->logo");
            if(!$existe_archivo) $sucursal->logo = "Logoj2negro.png";
            $this->pdf->Image(__DIR__ . '/../../../public/build/img/'.$sucursal->logo, 20, 5, 40, 28); // (ruta, x, y, ancho)
            $this->pdf->Ln(25);
            # Encabezado y datos de la empresa #
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($identidadFiscal?->nombre ?? $sucursal->negocio)),0,'C',false);
            $this->pdf->SetFont('Arial','B',8);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($sucursal->nombre)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: ".($identidadFiscal?->nit ?? $sucursal->nit)),0,'C',false);
            foreach($lineasencabezado as $value)$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", $value),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Direccion: ".$sucursal->direccion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$sucursal->movil),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: ".$sucursal->email),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->SetFont('Arial','',9);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: ".$credito->created_at),0,'C',false);
            //$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja: ".$credito->caja),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cajero: ".$usuario->nombre.' '.$usuario->apellido),0,'C',false);
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Credito N°: ".$credito->num_orden)),0,'C',false);
            //$this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("FACTURA: ".$factura->prefijo.''.$factura->num_consecutivo)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: ".$cliente?->nombre.' '.$cliente?->apellido??''),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Documento: ".$cliente->identificacion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$cliente->telefono),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Dirección: ".$direccion->direccion.': '.$direccion->ciudad.' - '.$direccion->departamento),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);

            ////////////  ADQUIRIENTE  ////////////

            # Tabla de productos #
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Cant."),0,0,'C');
            $this->pdf->Cell(19,5,iconv("UTF-8", "ISO-8859-1","Precio"),0,0,'C');
            $this->pdf->Cell(15,5,iconv("UTF-8", "ISO-8859-1","Desc."),0,0,'C');
            $this->pdf->Cell(28,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');
            $this->pdf->SetFont('Arial','',10);

            $this->pdf->Ln(3);
            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);



            /*----------  Detalles de la tabla  ----------*/
            foreach($productos as $value){
                $this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1", $value->nombreproducto),0,'C',false); //nombre producto
                $this->pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1", $value->cantidad),0,0,'C');  //cantidad
                $this->pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1",'$'.number_format($value->valorunidad, 2, ',', '.')),0,0,'C');  //precio unidad
                $this->pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1",$value->descuento),0,0,'C'); //descuento
                $this->pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1",'$'.number_format($value->total, 2, ',', '.')),0,0,'C'); //precio total
                $this->pdf->Ln(4);
            }
            //$this->pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Garantía de fábrica: 2 Meses"),0,'C',false);
            $this->pdf->Ln(7);
            /*----------  Fin Detalles de la tabla  ----------*/


            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            # Impuestos, descuentos & totales #
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","CREDITO TOTAL:"),0,0,'C');
            $this->pdf->Cell(38,5,iconv("UTF-8", "ISO-8859-1"," $".number_format($credito->capital, 2, ',', '.')." COP"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","Interes:"),0,0,'C');
            $this->pdf->Cell(38,5,iconv("UTF-8", "ISO-8859-1"," $".number_format($credito->valorinterestotal, 2, ',', '.')." COP"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","TOTAL ABONOS:"),0,0,'C');
            $this->pdf->Cell(38,5,iconv("UTF-8", "ISO-8859-1"," $".number_format($credito->abonodecuotas, 2, ',', '.')." COP"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","Descuento:"),0,0,'C');
            $this->pdf->Cell(38,5,iconv("UTF-8", "ISO-8859-1","- $".number_format($credito->descuento, 2, ',', '.')." COP"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(16,5,iconv("UTF-8", "ISO-8859-1","SALDO PENDIENTE: "),0,0,'C');
            $this->pdf->Cell(46,5,iconv("UTF-8", "ISO-8859-1","$".number_format($credito->saldopendiente, 2, ',', '.')." COP"),0,0,'C');

            $this->pdf->Ln(7);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","PAGOS"),0,'C',false);
            foreach($cuotas as $value)
                foreach($value->mediosdepago as $mp)
                    $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", $value->fechapagado.' '.$mp->mediopago.' $'.number_format($mp->valor??$value->valorpagado, 2, ',', '.')),0,'C',false);
            
            $this->pdf->Ln(12);


            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Nota: $credito->nota"),0,'C',false);
            $this->pdf->Ln(6);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

            $this->pdf->SetFont('Arial','B',9);
            $this->pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');
            # Nombre del archivo PDF #
            $this->pdf->Output("I","Credito $credito->num_orden.pdf",true);
        }


        public function generarComprobanteAbono($sucursal, $credito, $cuota, $cliente,  $productos=[], object|null $identidadFiscal = null){
            $existe_archivo = !empty($sucursal->logo)&&file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/$sucursal->logo");
            if(!$existe_archivo) $sucursal->logo = "Logoj2negro.png";
            $this->pdf->Image(__DIR__ . '/../../../public/build/img/'.$sucursal->logo, 20, 5, 40, 28); // (ruta, x, y, ancho)
            $this->pdf->Ln(25);
            # Encabezado y datos de la empresa #
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($identidadFiscal?->nombre  ?? $sucursal->negocio)),0,'C',false);
            $this->pdf->SetFont('Arial','B',8);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($sucursal->nombre)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: ".$identidadFiscal?->nit ?? $sucursal->nit),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Direccion: ".$sucursal->direccion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$sucursal->movil),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: ".$sucursal->email),0,'C',false);
            
            $this->pdf->SetFont('Arial','',9);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: ".$cliente->nombre),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Documento: ".$cliente->identificacion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$cliente->telefono),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);

            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Credito N°: ".$credito->num_orden)),0,'C',false);

            $this->pdf->SetFont('Arial','',9);
             $this->pdf->Ln(7);
             # Impuestos, descuentos & totales #
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", "$cuota->fechapagado  -  Abono N°: ".$cuota->numerocuota),0,'C',false);
            $this->pdf->Ln(5);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", "Valor pagado: ".$cuota->valorpagado),0,'C',false);
            $this->pdf->Ln(5);
            # Nombre del archivo PDF #
            $this->pdf->Output("I","Ticket_Nro_1.pdf",true);
        }


        public function generarComprobanteCompra($sucursal, $compra, $proveedor, $productos=[]){
            $existe_archivo = !empty($sucursal->logo)&&file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/$sucursal->logo");
            if(!$existe_archivo) $sucursal->logo = "Logoj2negro.png";
            $this->pdf->Image(__DIR__ . '/../../../public/build/img/'.$sucursal->logo, 20, 5, 40, 28); // (ruta, x, y, ancho)
            $this->pdf->Ln(25);
            # Encabezado y datos de la empresa #
             $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($sucursal->nombre)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: ".$sucursal->nit),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Direccion: ".$sucursal->direccion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$sucursal->movil),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: ".$sucursal->email),0,'C',false);
            
            $this->pdf->SetFont('Arial','',9);

            $this->pdf->Ln(1);
            $this->pdf->Line(4, $this->pdf->GetY(), 76, $this->pdf->GetY());
            $this->pdf->Ln(3);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Proveedor: ".$proveedor->nombre),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Documento: ".$proveedor->nit),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$proveedor->telefono),0,'C',false);

            $this->pdf->SetFont('Arial','B',9);
            $this->pdf->MultiCell(0,5, iconv("UTF-8","ISO-8859-1","COMPROBANTE DE COMPRA"),0,'C');

            $this->pdf->SetFont('Arial','',8);
            $this->pdf->Cell(0,5,"No: ".$compra->nfactura,0,1,'C');
            $this->pdf->Cell(0,5,"Fecha: ".$compra->fechacompra,0,1,'C');

            $this->pdf->Ln(2);
            $this->pdf->Line(4, $this->pdf->GetY(), 76, $this->pdf->GetY());
            $this->pdf->Ln(3);

            $this->pdf->SetFont('Arial','B',8);

            //PRODUCTOS
            // Encabezados
            $this->pdf->Cell(30,5,'Producto',0,0);
            $this->pdf->Cell(10,5,'Cant',0,0,'C');
            $this->pdf->Cell(15,5,'Precio',0,0,'R');
            $this->pdf->Cell(15,5,'Total',0,1,'R');

            $this->pdf->SetFont('Arial','',8);

            foreach($productos as $producto){

                $nombre = iconv("UTF-8","ISO-8859-1",$producto->nombreitem);
                // Nombre en varias líneas si es largo
                $this->pdf->MultiCell(30,4,$nombre,0);
                
                $y = $this->pdf->GetY() - 4;

                $this->pdf->SetXY(34, $y);
                $this->pdf->Cell(10,4,$producto->cantidad,0,0,'C');

                $this->pdf->Cell(15,4,number_format($producto->valorunidad,0),0,0,'R');

                $this->pdf->Cell(15,4,number_format($producto->valorcompra,0),0,1,'R');
            }

            //TOTALES
            $this->pdf->Ln(2);
            $this->pdf->Line(4, $this->pdf->GetY(), 76, $this->pdf->GetY());
            $this->pdf->Ln(3);

            $this->pdf->SetFont('Arial','B',9);

            $this->pdf->Cell(45,5,'TOTAL:',0,0,'R');
            $this->pdf->Cell(25,5,number_format($compra->valortotal,0),0,1,'R');

            # Nombre del archivo PDF #
            $this->pdf->Output("I","comprobante $compra->nfactura - $compra->id.pdf",true);

        }


        public function generarComprobantePagoComision($sucursal, $usuario, $pagoComision){
            $existe_archivo = !empty($sucursal->logo)&&file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/$sucursal->logo");
            if(!$existe_archivo) $sucursal->logo = "Logoj2negro.png";
            $this->pdf->Image(__DIR__ . '/../../../public/build/img/'.$sucursal->logo, 20, 5, 40, 28); // (ruta, x, y, ancho)
            $this->pdf->Ln(25);
            # Encabezado y datos de la empresa #
             $this->pdf->SetFont('Arial','B',10);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($sucursal->nombre)),0,'C',false);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: ".$sucursal->nit),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Direccion: ".$sucursal->direccion),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ".$sucursal->movil),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: ".$sucursal->email),0,'C',false);
            
            $this->pdf->SetFont('Arial','',9);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(5);

            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Empleado: ".$usuario->nombre.' '.$usuario->apellido),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Usuario: ".$usuario->nickname),0,'C',false);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cedula: ".$usuario->cedula),0,'C',false);

            $this->pdf->Ln(1);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $this->pdf->Ln(3);

            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Id del pago: ".$pagoComision->id)),0,'C',false);

            $this->pdf->SetFont('Arial','',9);
             $this->pdf->Ln(7);
             # Impuestos, descuentos & totales #
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", "Fecha de pago: ".$pagoComision->fechapago),0,'C',false);
            $this->pdf->Ln(5);
            $this->pdf->SetFont('Arial','',10);
            $this->pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1", "Comision pagado: $".number_format($pagoComision->valor??0, 0, ',', '.')." COP"),0,'C',false);

            $this->pdf->Ln(5);
            $this->pdf->SetFont('Arial','',8);
            $this->pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1", "Medio de pago: $pagoComision->mediopago"),0,0,'C');
                
            $this->pdf->Ln(5);
            # Nombre del archivo PDF #
            $this->pdf->Output("I","Ticket_Nro_1.pdf",true);
        }
    }