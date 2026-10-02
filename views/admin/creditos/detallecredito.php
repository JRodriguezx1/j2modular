<div class="detallecredito min-h-[calc(100vh-7rem)] w-full bg-gradient-to-b from-indigo-50/60 via-slate-50 to-slate-100 p-3 text-slate-900 sm:p-6">
  <?php include __DIR__. "/../../templates/alertas.php"; ?>

  <input id="idcredito" class="hidden" type="text" name="id_credito" value="<?php echo $credito->id;?>">
  
  <div class="relative mx-auto grid max-w-[150rem] gap-6 rounded-lg border border-slate-200 bg-white p-4 shadow-lg sm:p-6">
    <!-- Título principal -->
    <a href="/admin/creditos" class="absolute left-8 top-8 z-[2] inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 p-0 text-white shadow-lg transition hover:-translate-y-0.5 hover:text-white sm:left-14 sm:top-16">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h2 class="flex min-h-36 items-center gap-3 rounded-lg border border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 py-6 pl-24 pr-6 text-3xl font-extrabold leading-tight text-slate-900 sm:min-h-40 sm:pl-28 sm:text-4xl">
      Detalles del <?php echo $credito->idtipofinanciacion==1?'Crédito':'Separado'; ?>
      <span class="ml-auto hidden shrink-0 rounded-lg border border-slate-200 bg-white/90 px-4 py-3 text-base font-extrabold uppercase text-slate-500 md:inline-flex">Vista de cartera</span>
    </h2>

    <div id="divmsjalerta"></div>
    <!-- Información general del crédito -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">🧾 Factura</h3>
        <p class="m-0 text-xl font-bold leading-tight text-slate-900"><?php echo $factura!=null?$factura->prefijo.' - '.$factura->num_consecutivo:'';?></p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">💰 Credito</h3>
        <p id="creditoText" class="m-0 text-xl font-bold leading-tight text-slate-900">$ <?php echo number_format($credito->capital,'2', ',', '.'); ?></p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">💸 Abono Inicial</h3>
        <p id="abonoInicialText" class="m-0 text-xl font-bold leading-tight text-slate-900">$ <?php echo number_format($credito->abonoinicial,'2', ',', '.');?></p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">💷 Interes Total</h3>
        <p id="interesText" class="m-0 text-xl font-bold leading-tight text-slate-900">$ <?php echo number_format($credito->valorinterestotal,'2', ',', '.');?></p>
      </div>
    </div>

    <!-- Detalles financieros -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">💲 Credito Total</h3>
        <p id="creditoTotalText" class="m-0 text-xl font-bold leading-tight text-slate-900">$ <?php echo number_format($credito->montototal,'2', ',', '.');?></p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">📅 Fecha Emisión</h3>
        <p class="m-0 text-xl font-bold leading-tight text-slate-900"><?php echo $credito->fechainicio;?></p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">🔢 Plazo</h3>
        <p class="m-0 text-xl font-bold leading-tight text-slate-900"><?php echo ($credito->numcuota??0).' / '.$credito->cantidadcuotas;?> Cuotas</p>
      </div>

      <div class="grid min-h-28 gap-2 overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
        <h3 class="m-0 text-base font-extrabold uppercase leading-tight text-indigo-600">📆 Fecha Vencimiento</h3>
        <p class="m-0 text-xl font-bold leading-tight text-slate-900"> - </p>
      </div>
    </div>

    <!-- Estado actual -->
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="mb-4 flex items-center gap-3 border-b border-slate-200 pb-4 text-2xl font-extrabold text-slate-900">📊 Estado del Crédito - <button class="btnDialog btnDialog_primary"><?php echo $sucursaldelcredito->nombre;?></button></h3>
      <div class="flex flex-wrap items-center gap-4">
        <div class="flex flex-wrap items-center gap-4">
          <span class="inline-flex min-h-11 items-center rounded-full px-3 py-1 text-base font-extrabold <?php echo $credito->idestadocreditos==1?'bg-cyan-100 text-blue-600':($credito->idestadocreditos==2?'bg-green-100 text-green-700':'bg-red-100 text-red-700'); ?>">
            <?php echo $credito->idestadocreditos==1?'Finalizado':($credito->idestadocreditos==2?'En curso':'Anulado'); ?>
          </span>
          <span class="text-xl font-semibold text-slate-500">Saldo pendiente: <strong id="saldopendientetext" class="font-bold text-slate-900">$<?php echo number_format($credito->saldopendiente,'2', ',', '.'); ?></strong></span>
        </div>
        <div class="flex flex-wrap items-center gap-4">
          <span class="text-xl font-semibold text-slate-500">Cliente: <strong class="font-bold text-slate-900"><?php echo $cliente->nombre.' '.$cliente->apellido; ?></strong><p class="m-0 text-base font-normal">Documento: <?php echo $cliente->identificacion;?></p></span>
        </div>
        <div class="flex flex-wrap items-center gap-4">
          <span class="flex items-center gap-2 text-lg font-semibold text-slate-500">Productos: <span class="btn-xs inline-flex min-h-11 items-center rounded-full px-3 py-1 text-base font-extrabold <?php echo $credito->productoentregado==0?'btn-light':'btn-lima';?>"><?php echo $credito->productoentregado==0?'Pendiente':'Entregado';?></span></span>
        </div>
        <?php if($credito->idestadocreditos == 2 && $credito->idtipofinanciacion == 2):
                if(tienePermiso('Editar separados activos')&&userPerfil()>3 || userPerfil()<4){
        ?>
          <div><a href="/admin/creditos/adicionarProducto?id=<?php echo $credito->id;?>" class="inline-flex size-14 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-blue-700 text-xl font-black text-white hover:text-white">+</a></div>
        <?php } endif;?>
      </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex flex-col items-stretch gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
      <?php if($credito->idestadocreditos == 2):
                if($credito->id_fksucursal == id_sucursal() && (tienePermiso('Editar separados activos')&&userPerfil()>3 || userPerfil()<4)){
      ?>

        <button id="ajustarCredito" class="nuevobtn nuevobtn_light w-full sm:w-auto">🔄 Ajustar Credito</button>
      
      <?php } endif; ?>
      <button id="btnDetalleProductos" class="nuevobtn nuevobtn_blueintense w-full sm:w-auto">📄 Productos</button>
      <button id="btnAbonar" class="nuevobtn nuevobtn_indigo w-full sm:w-auto">➕ Abonar</button>
      <button id="btnPagarTodo" class="nuevobtn nuevobtn_turquoise w-full sm:w-auto">✅ Pagar Todo</button>
      <button class="nuevobtn nuevobtn_gray w-full sm:w-auto">⬅️ Volver</button>
    </div>

    <!-- Historial de abonos -->
    <div class="datatable-card config-table-card overflow-hidden rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <h3 class="mb-5 border-b border-slate-200 pb-4 text-2xl font-extrabold text-slate-900">📚 Historial de Abonos</h3>
      <table id="tablacuotas">
        <thead>
          <tr>
            <th>N° de Cuota</th>
            <th>Fecha</th>
            <th>Valor cuota</th>
            <th>Valor pagado</th>
            <th>Medio pago</th>
            <th>Recaudo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cuotas as $value): ?>
            <tr>
              <td><?php echo $value->numerocuota;?></td>
              <td><?php echo $value->fechapagado;?></td>
              <td>$<?php echo number_format($value->montocuota, '2', ',', '.');?></td>
              <td>$<?php echo number_format($value->valorpagado, '2', ',', '.');?></td>
              <td>
                <?php foreach($value->mediosdepago as $idx => $element): ?>
                <button id="<?php echo $value->id;?>" data-totalpagado="<?php echo $value->valorpagado;?>" data-idcredito="<?php echo $value->id_credito;?>" data-idmediopago="<?php echo $element->idmediopago;?>" data-mediopagado="<?php echo $element->valor;?>" class="mediosdepago"><?php echo $element->mediopago;?></button>
                <?php endforeach; ?>
              </td>
              <td><div class="text-indigo-600 text-xl bg-indigo-50 rounded-lg pt-2 font-semibold"><?php echo $value->nombresucursal;?></div></td>
              <td>
                <div id="<?php echo $value->id;?>" class="flex justify-center gap-4">
                  <button class="anularAbono" title="Eliminar abono">X</button>
                  <button class="printPOSAbono material-symbols-outlined">print</button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          
        </tbody>
      </table>
    </div>
  </div>


  <!-- MODAL DETALLE PRODUCTO-->
  <dialog id="miDialogoDetalleProducto" class="detalledialog_md">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 sm:items-center sm:p-5">
      <div class="flex min-w-0 items-start gap-4 sm:items-center">
        <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-2xl text-white sm:size-16">
          <i class="fa-solid fa-boxes-stacked"></i>
        </span>
        <div>
          <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Productos</p>
          <h4 id="modalDetalleProducto" class="m-0 text-xl font-extrabold text-slate-900 sm:text-2xl">Detalle producto</h4>
          <span class="text-sm font-semibold text-slate-500 sm:text-base"><?php echo count($productos); ?> articulos asociados al <?php echo $credito->idtipofinanciacion==1?'credito':'separado'; ?>.</span>
        </div>
      </div>
      <button type="button" class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-xl text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 sm:size-14" aria-label="Cerrar detalle de producto">
          <i id="btnXCerrarModalDetalleProducto" class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div id="divmsjalerta1"></div>
    <!-- TABLA DE PRODUCTOS -->
    <div class="p-4 sm:p-5">
    <div class="overflow-visible rounded-lg border border-slate-200 bg-white p-3 sm:overflow-x-auto sm:bg-slate-50">
        <table id="tabladetalleProducto"
            class="detalle-producto-dialog__table w-full min-w-0 border-separate border-spacing-0 sm:min-w-[58rem]">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unidad de medida</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas -->
                <?php foreach($productos as $value): ?>
                  <tr>
                    <td data-label="Producto">
                      <span class="detalle-producto-dialog__product"><?php echo $value->nombreproducto; ?></span>
                    </td>
                    <td data-label="Cantidad">
                      <span class="detalle-producto-dialog__quantity"><?php echo $value->cantidad; ?></span>
                    </td>
                    <td data-label="Unidad">
                      <span class="detalle-producto-dialog__unit"><?php echo 'Unidades'; ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
  </dialog><!--fin modal detalle producto-->


  <!-- MODAL CAMBIO MEDIO DE PAGO -->
    <dialog id="cambioMedioPago"
        class="midialog-xs p-12">
        <!-- Encabezado -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">💳 Cambio medio de pago</h4>
            <button type="button" id="btnCerrarCambioMedioPago"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                onclick="document.getElementById('cambioMedioPago').close()">
                <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
            </button>
        </div>

        <div id="divmsjalerta2"></div>

        <form id="formCambioMedioPago" class="formulario space-y-6" action="/admin/caja/cambioMedioPago" method="POST">

            <div class="text-center">
                <label id="numCuota" class="text-gray-700 text-2xl font-medium block mb-2">
                    Credito N° :
                </label>
                <p id="textMP" class="text-gray-600 text-3xl font-light m-0 mb-2"></p>
                <span id="totalPagado" class="text-gray-800 text-2xl font-semibold block mb-4">$ </span>
            </div>

            <!-- Medios de pago -->
            <div class="formulario__campo">
                <label class="formulario__label" for="selectMediopago">Medio de pago</label>
                <select id="selectMediopago" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="mediopagoid" required>
                    <?php foreach($mediospago as $value):  ?>
                          <option value="<?php echo $value->id;?>" ><?php echo $value->mediopago;?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botones -->
            <div class="text-right pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" class="btn-md btn-turquoise !py-4 !px-6 !w-[136px]" value="Cancelar">Cancelar</button>
                <input id="btnEnviarCambioMedioPago" class="btn-md btn-indigo !py-4 !px-6 !w-[136px]" type="submit" value="Aplicar">
            </div>
        </form>
    </dialog>

  <?php include __DIR__ . "/abonoinicial.php"; ?>
  <?php include __DIR__ . "/abonototal.php"; ?>
  <?php include __DIR__ . "/ajustarcredito.php"; ?>

  <script>
    const getParam = <?= json_encode($conflocal) ?>;
  </script>

</div>
