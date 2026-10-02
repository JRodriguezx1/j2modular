<html>
	<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACTURA DE VENTA</title>
		<style rel="stylesheet" type="text/css">
			@media (min-width: 640px){.sm\:col-span-2{grid-column:span 2 / span 2}.sm\:block{display:block}.sm\:grid{display:grid}.sm\:grid-cols-5{grid-template-columns:repeat(5, minmax(0, 1fr))}.sm\:grid-cols-1{grid-template-columns:repeat(1, minmax(0, 1fr))}.sm\:justify-end{justify-content:flex-end}.sm\:gap-2{gap:0.5rem}.sm\:text-end{text-align:end}}
		</style>
	</head>
  
  <body style="background-color:#f3f4f6; padding:40px 0; font-family:'Segoe UI', Arial, sans-serif; color:#1f2937; margin:0;">

    <div style="max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.1);">

      <!-- Encabezado -->
      <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 30px; border-bottom:1px solid #e5e7eb;">
        <div style="text-align:left; line-height:1.1;">
          <h2 style="margin:0; font-size:18px; color:#111827; font-weight:700;"><?php echo $sucursal->negocio??'';?></h2>
          <p style="margin:2px 0; font-size:13px; color:#374151;"><?php echo $sucursal->nombre??'';?></p>
          <p style="margin:2px 0; font-size:13px; color:#374151;">Tel: <?php echo $cliente->telefono??'';?></p>
          <p style="margin:2px 0; font-size:13px; color:#374151;"><?php echo $sucursal->ciudad.' - '.$sucursal->departamento;?></p>
          <a href="mailto:contabilidad@innovatech.com.co" style="color:#4338ca; font-size:13px; text-decoration:none;"><?php echo $sucursal->email??'';?></a>
        </div>
        <div style="text-align:right;">
          <img src="cid:logo_id"
              alt="Logo del negocio"
              style="width:150px; height:auto; display:block; margin-left:auto;">
        </div>
      </div>

      <!-- Contenido -->
      <div style="padding:0 30px 30px 30px;">
        <h2 style="color:#111827; font-size:22px; margin-bottom:20px; text-align:center; letter-spacing:0.5px;">FACTURA DE VENTA</h2>

        <!-- 🧾 Detalles de la venta -->
        <div style="background-color:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:15px 20px; margin-bottom:25px;">
          <table style="width:100%; border-collapse:collapse; font-size:13px; line-height:1.1;">
            <tr>
              <td style="vertical-align:top; width:50%; padding-right:15px; border-right:1px solid #e5e7eb;">
                <h4 style="margin:0 0 8px; font-size:13px; color:#4338ca;">Datos del Cliente</h4>
                <p style="margin:0 0 4px;"><strong>Cliente:</strong><?php echo $cliente->nombre.' '.$cliente->apellido;?></p>
                <p style="margin:0 0 4px;"><strong>NIT/CC:</strong><?php echo $cliente->identificacion??'N/A';?></p>
                <p style="margin:0 0 4px;"><strong>Correo:</strong><?php echo $cliente->email??'N/A';?></p>
                <p style="margin:0 0 4px;"><strong>Teléfono:</strong><?php echo $cliente->telefono??'N/A';?></p>
                <p style="margin:0;"><strong>Dirección:</strong><?php echo $direccion->direccion.', '.$direccion->departamento.' - '.$direccion->ciudad;?></p>
              </td>
              <td style="vertical-align:top; width:50%; padding-left:15px;">
                <h4 style="margin:0 0 8px; font-size:13px; color:#4338ca;">Detalles de la orden</h4>
                <p style="margin:0 0 4px;"><strong>Orden N°:</strong><?php echo $factura->num_orden;?></p>
                <p style="margin:0 0 4px;"><strong>Vendedor:</strong><?php echo $vendedor->nombre.' '.$vendedor->apellido;?></p>
                <p style="margin:0 0 10px;"><strong>Medio de pago:</strong> Efectivo</p>
                <hr style="border:none; border-top:1px solid #e5e7eb; margin:10px 0;">
                <!-- 🕓 Nueva sección -->
                <h4 style="margin:10px 0 6px; font-size:13px; color:#4338ca;">Fecha y hora de Factura</h4>
                <p style="margin:0 0 4px;"><strong>Generación:</strong> <?php echo $factura->fechacreacion;?></p>
                <p style="margin:0 0 4px;"><strong>Expedición:</strong> <?php echo date('Y-m-d H:i:s');?></p>
              </td>

            </tr>
          </table>
        </div>

        <!-- 🧾 Tabla de productos -->
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:25px;">
          <thead>
            <tr style="background-color:#f9fafb;">
              <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Producto</th>
              <th style="text-align:center; padding:10px; border-bottom:1px solid #e5e7eb;">Cantidad</th>
              <th style="text-align:right; padding:10px; border-bottom:1px solid #e5e7eb;">Vr. Unitario</th>
              <th style="text-align:right; padding:10px; border-bottom:1px solid #e5e7eb; width:150px;">Vr. Total</th>
            </tr>
          </thead>
          <tbody>
            <!-- Aquí se insertan dinámicamente los productos -->
            <?php foreach($productos as $value): ?>
            <tr>
              <td style="padding:8px 10px; border-bottom:1px solid #f3f4f6;"><?php echo $value->nombreproducto;?></td>
              <td style="text-align:center; padding:8px 10px; border-bottom:1px solid #f3f4f6;"><?php echo $value->cantidad;?></td>
              <td style="text-align:right; padding:8px 10px; border-bottom:1px solid #f3f4f6;">$<?php echo number_format($value->valorunidad, '2', '.', ',');?></td>
              <td style="text-align:right; padding:8px 10px; border-bottom:1px solid #f3f4f6;">$<?php echo number_format($value->total, '2', '.', ',');?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Totales -->
        <div style="text-align:right; font-size:14px; margin-bottom:25px;">
          <p style="margin:0;"><strong>Subtotal:</strong> $<?php echo number_format($factura->base, '2', '.', ',');?></p>
          <p style="margin:2px 0;"><strong>IVA:</strong> $<?php echo number_format($factura->valorimpuestototal, '2', '.', ',');?></p>
          <p style="margin:2px 0 10px;"><strong>Total:</strong> <span style="font-size:16px; color:#4338ca;">$<?php echo number_format($factura->total, '2', '.', ',');?></span></p>
        </div>

        <!-- 🧾 Código QR de validación -->
        <div style="text-align:center; margin:30px 0;">
          <div style="display:inline-block; padding:15px 25px; border:1px solid #e5e7eb; border-radius:10px; background-color:#f9fafb;">
            <?php 
              // Ejemplo de variables dinámicas (pueden venir del backend)
              $cufe = "4A8D7F12345B9E00CFA9321ABCDEF12345678900"; 
              $url_dian = "https://www.dian.gov.co";
              $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=" . urlencode($url_dian . '?cufe=' . $cufe);
            ?>
            <img src="<?= $qr_url ?>" 
                alt="QR DIAN"
                style="width:110px; height:110px; display:block; margin:0 auto 10px auto; border-radius:6px;">
            <p style="margin:0; font-size:12px; color:#6b7280; line-height:1.5;">
              <strong style="color:#374151;">Código QR de validación DIAN</strong><br>
              Escanee para verificar la validez de esta factura electrónica.<br>
              <span style="font-size:11px; color:#9ca3af;">CUFE: <?= $cufe ?></span>
            </p>
          </div>
        </div>

        <!-- 📄 Pie de página compacto -->
        <hr style="margin:20px 0; border:none; border-top:1px solid #e5e7eb;">

        <div style="font-size:11.5px; color:#6b7280; text-align:center; line-height:1.1; padding:0 15px 15px;">
          <p style="margin:0 0 0px;">
            Representacion grafica de factura de venta generada por <strong style="color:#4338ca;"><?php echo $sucursal->negocio??'';?></strong> - NIT <?php echo $sucursal->nit??'';?>. Gracias por su compra. Contáctanos: <a href="mailto:<?php echo $sucursal->email??'';?>" style="color:#4338ca; text-decoration:none; margin:0 0 5px;"><?php echo $sucursal->email??'';?></a> | 
            Tel: <?php echo $sucursal->telefono??'';?>
          </p>

          <p style="margin:0 0 5px;"><?php echo $sucursal->direccion.', '.$sucursal->ciudad.' - '.$sucursal->departamento;?></p>
          <p style="margin:0 0 0px; font-size:11px; color:#9ca3af;">
            © <?php echo date('Y');?> <strong style="color:#4338ca;"><?php echo $sucursal->negocio??'';?></strong>. Todos los derechos reservados.
          </p>
          <a href="https://subtle-souffle-25465c.netlify.app/" style="margin:0 0 0px;; font-size:11px;">
            Elaborado y generado con <strong style="color:#4338ca;">J2 Software POS Multisucursal</strong>.
          </a>
        </div>
      </div>
    </div>

    <!-- 📱 Estilos responsive -->
    <style>
      @media only screen and (max-width: 600px) {
        .col-izq, .col-der {
          display:block !important;
          width:100% !important;
          padding:0 !important;
          border:none !important;
        }
        .col-der {
          margin-top:10px !important;
        }
      }
      </style>
  </body>
</html>
