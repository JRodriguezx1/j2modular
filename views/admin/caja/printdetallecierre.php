<div class=" bg-white rounded-lg p-2 text-lg leading-snug text-black">
  <!-- Encabezado -->
  <div class="text-center border-b border-gray-200 pb-4">
    <h2 class="font-bold text-xl text-gray-900">
      <?php echo $ultimocierre->estado==1?'CIERRE DE CAJA':'CIERRE PARCIAL';?>
    </h2>
    <p class="text-black"><?php echo $ultimocierre->nombrecaja??'';?></p>
    <p class="mt-1 font-semibold text-gray-900"><?php echo ($sucursal??null)?->nombre??'';?></p>
    <p class="text-black"><?php echo ($sucursal??null)?->nit;?></p>
    <?php foreach($lineasencabezado as $value):?>
      <p><?php echo $value;?></p>
    <?php endforeach;?>
  </div>

  <!-- Información General -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Información General</h3>
    <div class="grid grid-cols-1 gap-1">
      <p><span class="font-semibold">Caja:</span> <?php echo $ultimocierre->nombrecaja??'';?></p>
      <p><span class="font-semibold">Cuadre de caja: ID</span> <?php echo $ultimocierre->id;?></p>
      <p><span class="font-semibold">Usuario:</span> <?php echo $ultimocierre->nombreusuario??''; ?></p>
      <p><span class="font-semibold">Inicio:</span> <?php echo $ultimocierre->fechainicio??''; ?></p>
      <p><span class="font-semibold">Fin:</span> <?php echo $ultimocierre->fechacierre??'';?></p>
      <p><span class="font-semibold">Reporte:</span> <?php date_default_timezone_set('America/Bogota'); echo date('d M Y H:i:s');?></p>
    </div>
  </div>

  <!-- Cuadre de Caja -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Cuadre de Caja</h3>
    <ul class="space-y-0.5">
      <li>Efectivo Inicial: <span class="font-semibold text-gray-900">+ $<?php echo number_format($ultimocierre->basecaja??0, "0", ",", ".");?></span></li>
      <li>Ventas en Efectivo: <span class="font-semibold text-gray-900">+ $<?php echo number_format($ultimocierre->ventasenefectivo??0, "0", ",", ".");?></span></li>
      <li>Abonos en efectivo: <span class="font-semibold text-gray-900">+ $<?php echo number_format($ultimocierre->abonosenefectivo??0, "0", ",", ".");?></span></li>
      <li>Gastos en Efectivo: <span class="font-semibold text-gray-900">- $<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></span></li>
      <li class="font-semibold text-gray-900">DINERO EN CAJA: $<?php echo number_format($ultimocierre->basecaja+$ultimocierre->ventasenefectivo+($ultimocierre->abonosenefectivo??0)-$ultimocierre->gastoscaja??0, "0", ",", ".");?></li>
      <li>Domicilios: $<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></li>
      <li class="font-semibold text-gray-900">REAL EN CAJA: $<?php echo number_format($ultimocierre->basecaja+$ultimocierre->ventasenefectivo+($ultimocierre->abonosenefectivo??0)-$ultimocierre->gastoscaja-$ultimocierre->domicilios??0, "0", ",", ".");?></li>
    </ul>
  </div>

  <!-- Medios de Pago -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Medios de Pago</h3>
    <ul>
      <?php foreach($discriminarmediospagos as $index => $value): ?>
        <li><?php echo $value['mediopago'];?>: <span class="font-semibold">$<?php echo number_format($value['valor'], "0", ",", ".");?></span></li> 
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Datos de Ventas -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Datos de Ventas</h3>
    <ul>
      <li>Ingreso Total de Ventas: + $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->totaldescuentos??0), "0", ",", ".");?></li>
      <li>Abonos totales: + $<?php echo number_format($ultimocierre->abonostotales??0, "0", ",", ".");?></li>
      <li>Total gastos de caja: - $<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></li>
      <li>Gastos otros/bancarios: - $<?php echo number_format($ultimocierre->gastosbanco??0, "0", ",", ".");?></li>
      <li>Total Descuentos: - $<?php echo number_format($ultimocierre->totaldescuentos??0, "0", ",", ".");?></li>
      <li>Total Domicilios: - $<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></li>
      <li class="font-semibold text-gray-900">GANANCIA HOY: = $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->abonostotales??0)-($ultimocierre->domicilios??0)-($ultimocierre->gastoscaja??0)-($ultimocierre->gastosbanco??0), "0", ",", ".");?></li>
      <li>Creditos <span class="font-semibold text-gray-900">+ $<?php echo number_format($ultimocierre->creditos??0, "0", ",", ".");?></span></li>
      <li class="font-semibold text-gray-900">Total de ventas: $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->creditos??0), "0", ",", ".");?></li>
      <li>Base grabable: = $<?php echo number_format($ultimocierre->basegravable??0, "0", ",", ".");?></li>
      <li>Impuesto Total: - $<?php echo number_format($ultimocierre->valorimpuestototal??0, "0", ",", ".");?></li>
    </ul>
  </div>

  <!-- Detalle Impuesto -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Detalle Impuesto</h3>
    <ul>
      <?php foreach($discriminarimpuesto as $index => $value): ?>
        <li>Tarifa: <?php echo $value['tarifa']!=null?$value['tarifa'].'%':'Excluido';?> — Base: $<?php echo number_format($value['basegravable'], '2', ',', '.');?> — Imp: $<?php echo number_format($value['valorimpuesto'], "2", ",", ".");?></li> 
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Tipo de Facturas -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Tipo de Facturas</h3>
    <p>Facturas electrónicas: <?php echo number_format($ultimocierre->facturaselectronicas??0, "0", ",", ".");?></p>
    <p>Facturas POS: <?php echo number_format($ultimocierre->facturaspos??0, "0", ",", ".");?></p>
  </div>

  <!-- Sobrante y Faltante -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Sobrante y Faltante</h3>
    <ul>
      <?php foreach($sobrantefaltante as $index => $value): ?>
        <li class="font-semibold text-gray-900">Medio de pago: <?php echo $value->nombremediopago;?></li>
        <li>Sistema: <?php echo number_format($value->valorsistema, "0", ",", ".");?> — Declarado: <?php echo number_format($value->valordeclarado, "0", ",", ".");?> = <?php echo number_format($value->valordeclarado-$value->valorsistema, "0", ",", ".");?></li>   
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Ventas Por Usuario -->
  <div class="mt-3">
    <h3 class="font-bold text-gray-900 mb-1.5">Ventas Por Usuario</h3>
    <ul>
      <?php foreach($ventasxusuarios as $index => $value): ?>
        <li>Usuario: <?php echo $value['Nombre']??'';?> — $<?php echo number_format($value['ventas']??'', '2', ',', '.');?> (<?php echo $value['N_ventas']??0;?>)</li> 
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Pie de página -->
  <div class="mt-5 text-center border-t border-gray-200 pt-3 text-base text-black leading-tight">
    <p>J2 SOFTWARE POS</p>
    <p>www.j2softwarepos.com</p>
  </div>
</div>
