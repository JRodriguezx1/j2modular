<?php
  $totalCreditos = count($creditos);
  $creditosAbiertos = 0;
  $saldoPendienteTotal = 0;

  foreach($creditos as $creditoResumen){
    if((int)$creditoResumen->idestadocreditos === 2){
      $creditosAbiertos++;
    }
    $saldoPendienteTotal += (float)$creditoResumen->saldopendiente;
  }
?>

<div class="creditos w-full p-3 text-slate-900 sm:p-0 [@media(max-width:991px)]:pb-[calc(10rem+env(safe-area-inset-bottom))]">
  <?php include __DIR__. "/../../templates/alertas.php"; ?>

  <div class="mx-auto grid max-w-[150rem] gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50/60 via-white to-white p-4 shadow-sm sm:p-6">
    <section class="flex flex-col items-stretch gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
      <div class="min-w-0">
        <p class="mb-1 text-base font-extrabold uppercase text-indigo-600">Cartera</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Gestion de creditos</h1>
        <p class="mt-1 text-lg leading-snug text-slate-500">Administra separados, creditos activos, saldos pendientes y reportes desde una vista mas clara.</p>
      </div>
      <div class="flex justify-start gap-4 lg:justify-end">
        <article class="flex flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4">
          <span class="material-symbols-outlined inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-3xl text-white">credit_score</span>
          <div>
            <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900"><?php echo $totalCreditos; ?></strong>
            <small class="mt-1 block text-base font-bold text-slate-500">registros</small>
          </div>
        </article>
        <article class="flex flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4">
          <span class="material-symbols-outlined inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 text-3xl text-white">account_balance_wallet</span>
          <div>
            <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900">$<?php echo number_format($saldoPendienteTotal, '0', ',', '.'); ?></strong>
            <small class="mt-1 block text-base font-bold text-slate-500">saldo pendiente</small>
          </div>
        </article>
      </div>
    </section>

    <section class="flex flex-col items-stretch gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:flex-row sm:flex-wrap sm:items-center">
      <a class="btnDialog btnDialog_primary" href="/admin/creditos/separado">
        <span class="material-symbols-outlined text-2xl">add_2</span>
        Crear separado
      </a>
      <a class="btnDialog btnDialog_secondary" href="/admin/reportes/creditos/cuotas-creditos">
        <span class="material-symbols-outlined text-2xl">list_alt</span>
        Reporte cuotas
      </a>
      <a class="btnDialog btnDialog_light" href="/admin/reportes/creditos/creditos-finalizados">
        <span class="material-symbols-outlined text-2xl">task_alt</span>
        Creditos finalizados
      </a>
      <a class="btnDialog btnDialog_light" href="/admin/reportes/creditos/creditos-anulados">
        <span class="material-symbols-outlined text-2xl">folder_off</span>
        Creditos anulados
      </a>
    </section>

    <div id="divmsjalerta"></div>

    <section class="datatable-card config-table-card overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6 [@media(max-width:991px)]:[&_.dt-paging]:!p-3">
      <div class="flex flex-col items-start justify-between gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center">
        <div>
          <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Historial de creditos</h2>
          <p class="mt-1 text-base text-slate-500">Consulta cliente, valores, estado y acciones disponibles por credito.</p>
        </div>
        <div class="flex items-center gap-4">
          <button id="btnInterSucursal" class="btnDialog btnDialog_primary" type="button" value="salir">Inter Sucursal</button>
          <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-2 text-base font-extrabold text-indigo-600"><?php echo $creditosAbiertos; ?> abiertos</span>
        </div>
      </div>

      <table class="display responsive nowrap tabla datatable-table" width="100%" id="tablaCreditos">
        <thead>
            <tr>
                <th>id</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>CC</th>
                <th>Cliente</th>
                <th>Credito</th>
                <th>Interes</th>
                <th>Total Credito</th>
                <th>Abono total</th>
                <th>Estado</th>
                <th class="accionesth">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($creditos as $value): ?>
                <?php
                  $estadoCredito = $value->idestadocreditos==1?'Finalizado':($value->idestadocreditos==2?'Abierto':'Anulado');
                  $estadoClase = $value->idestadocreditos==1?'success':($value->idestadocreditos==2?'warning':'danger');
                  $tipoCredito = $value->idtipofinanciacion==1?'Credito':'Separado';
                  $tipoClase = $value->idtipofinanciacion==1?'primary':'info';
                ?>
                <tr>
                    <td><?php echo $value->ID; ?></td>
                    <td><?php echo $value->fechainicio; ?></td>
                    <td><span class="table-badge table-badge--<?php echo $tipoClase; ?>"><?php echo $tipoCredito; ?></span></td>
                    <td><span class="table-badge table-badge--neutral"><?php echo $value->identificacion; ?></span></td>
                    <td>
                      <span class="table-entity">
                        <span class="table-entity__icon"><i class="fa-solid fa-user"></i></span>
                        <span><?php echo $value->nombre.' '.$value->apellido; ?></span>
                      </span>
                    </td> 
                    <td><strong class="table-amount">$<?php echo number_format($value->capital,'2', ',', '.'); ?></strong></td>
                    <td>$<?php echo number_format($value->valorinterestotal,'2', ',', '.'); ?></td>
                    <td>$<?php echo number_format($value->montototal,'2', ',', '.'); ?></td>
                    <td><span class="table-badge table-badge--info">$<?php echo number_format($value->montototal+$value->abonoinicial-$value->saldopendiente,'2', ',', '.'); ?></span></td>
                    <td><span class="table-status table-status--<?php echo $estadoClase; ?>"><?php echo $estadoCredito; ?></span></td>
                    <td class="accionestd">
                        <div class="acciones-btns" id="<?php echo $value->ID;?>">
                            <a class="table-action table-action--view" href="/admin/creditos/detallecredito?id=<?php echo $value->ID;?>" title="Ver detalle del credito"><i class="fa-solid fa-chart-simple"></i></a>
                            <?php if($value->idtipofinanciacion==2&&$value->idestadocreditos==2): ?>
                                <?php if(tienePermiso('Anular separados')&&userPerfil()>3 || userPerfil()<4){ ?>
                                    <button class="table-action table-action--danger anularCredito" title="Anular credito"><i class="fa-solid fa-trash-can"></i></button>
                            <?php } endif; ?>
                            <span id="<?php echo $value->ID;?>" class="table-action table-action--print printPOSSeparado material-symbols-outlined" title="Imprimir separado">print</span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </section>


    <dialog id="miDialogoBuscarIntersucursal" class="detalledialog_xs" aria-labelledby="modalCreditoInterSucursal" aria-describedby="ayudaCreditoInterSucursal">
      <div class="p-6 flex justify-between items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
        <div class="flex items-center gap-4">
          <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100"><i class="fa-solid fa-envelope-open-text"></i></span>
          <div>
            <p class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Crédito</p>
            <h4 id="modalCreditoInterSucursal" class="text-slate-900 text-3xl leading-6 font-bold">Créditos inter sucursales</h4>
            <small class="mt-1 text-lg leading-snug text-slate-500">Consulta créditos abiertos de otras sucursales.</small>
          </div>
        </div>
        <button class="btndialog__close btnXCerrarInterSucursal" type="button" aria-label="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="p-8">
        <div class="form-field">
          <label for="creditoInterSucursal">Buscar crédito</label>
          <div class="form-input">
            <span><i class="fa-solid fa-hand-holding-dollar"></i></span>
            <input id="creditoInterSucursal" type="search" placeholder="ID, nombre o identificación" name="creditoInterSucursal" autocomplete="off" maxlength="80" aria-describedby="ayudaCreditoInterSucursal">
          </div>
          <small id="ayudaCreditoInterSucursal" class="mt-2 block text-base leading-5 text-slate-500">El ID puede tener cualquier longitud. Para nombre o identificación, ingresa mínimo 4 caracteres.</small>
        </div>

        <div id="listaCreditosIntersucursales" class="mt-5 grid max-h-[55vh] gap-3 overflow-y-auto pr-1" role="status" aria-live="polite" aria-atomic="false">
          <p class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-center text-base font-semibold text-slate-500">Escribe el ID del crédito, el nombre del cliente o su identificación.</p>
        </div>
      </div>
    </dialog>

  </div>


  <script>
    const getParam = <?= json_encode($conflocal) ?>;
  </script>

</div>
