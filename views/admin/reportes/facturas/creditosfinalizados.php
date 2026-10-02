<?php
  $totalRegistros = is_countable($creditosFinalizados) ? count($creditosFinalizados) : 0;
  $totalCredito = 0;
  $totalAbonado = 0;

  foreach($creditosFinalizados as $creditoFinalizado){
    $totalCredito += (float) $creditoFinalizado->montototal;
    $totalAbonado += (float) ($creditoFinalizado->montototal - $creditoFinalizado->saldopendiente);
  }
?>

<div class="box creditosFinalizados w-full overflow-x-hidden sm:pb-12 [@media(max-width:991px)]:!pb-[calc(10rem+env(safe-area-inset-bottom))]">
  <div class="mx-auto grid min-w-0 max-w-screen-2xl gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50 via-white to-white p-4 shadow-sm sm:p-6">
    <section class="grid min-w-0 grid-cols-1 items-center gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-4 sm:p-6 md:grid-cols-[auto_minmax(0,1fr)] xl:grid-cols-[auto_minmax(0,1fr)_auto]">
      <a href="/admin/creditos" class="inline-flex size-16 items-center justify-center rounded-lg bg-indigo-600 text-2xl text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:text-white" aria-label="Volver a creditos">
        <i class="fa-solid fa-arrow-left"></i>
      </a>

      <div class="min-w-0">
        <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Cartera</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Creditos finalizados</h1>
        <span class="mt-1 block text-lg leading-snug text-slate-500">Consulta creditos y separados que ya cerraron su ciclo de pago.</span>
      </div>

      <div class="flex min-w-0 flex-wrap gap-4 md:col-span-2 xl:col-span-1 xl:justify-end" aria-label="Resumen de creditos finalizados">
        <article class="flex min-w-72 flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 xl:flex-none">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-3xl text-white"><i class="fa-solid fa-circle-check"></i></span>
          <div>
            <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900"><?php echo number_format($totalRegistros, 0, ',', '.'); ?></strong>
            <small class="mt-1 block text-base font-bold text-slate-500">registros finalizados</small>
          </div>
        </article>
        <article class="flex min-w-72 flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 xl:flex-none">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-cyan-600 text-3xl text-white"><i class="fa-solid fa-wallet"></i></span>
          <div>
            <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900">$<?php echo number_format($totalAbonado, 0, ',', '.'); ?></strong>
            <small class="mt-1 block text-base font-bold text-slate-500">total abonado</small>
          </div>
        </article>
      </div>
    </section>

    <section class="datatable-card config-table-card min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6 [@media(max-width:991px)]:[&_.dt-paging]:!p-3">
      <div class="flex min-w-0 flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Historial finalizado</h2>
          <p class="mb-0 mt-1 text-lg text-slate-500">Detalle de cliente, valores, fechas y acceso rapido al credito.</p>
        </div>
        <span class="inline-flex max-w-full self-start rounded-full bg-cyan-50 px-3 py-2 text-base font-extrabold text-cyan-700 sm:shrink-0">$<?php echo number_format($totalCredito, 0, ',', '.'); ?> en cartera cerrada</span>
      </div>

      <div class="min-w-0 overflow-x-auto">
        <table id="tablaCreditosFinalizados" class="display responsive nowrap tabla datatable-table" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Fecha fin</th>
              <th>Tipo</th>
              <th>Cliente</th>
              <th>Credito</th>
              <th>Interes</th>
              <th>Total credito</th>
              <th>Abono total</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($creditosFinalizados as $value):?>
              <?php
                $tipo = $value->idtipofinanciacion == 1 ? 'Credito' : 'Separado';
                $estado = $value->idestadocreditos == 1 ? 'Finalizado' : ($value->idestadocreditos == 2 ? 'Abierto' : 'Anulado');
                $estadoClase = $value->idestadocreditos == 1 ? 'table-status--success' : ($value->idestadocreditos == 2 ? 'table-status--warning' : 'table-status--danger');
                $tipoClase = $value->idtipofinanciacion == 1 ? 'table-badge--primary' : 'table-badge--info';
                $cliente = trim($value->nombre.' '.$value->apellido);
                $abonoTotal = (float) ($value->montototal - $value->saldopendiente);
              ?>
              <tr>
                <td><span class="table-badge table-badge--neutral"><?php echo htmlspecialchars($value->ID); ?></span></td>
                <td><?php echo htmlspecialchars($value->fechainicio); ?></td>
                <td><?php echo htmlspecialchars($value->fechafin); ?></td>
                <td><span class="table-badge <?php echo $tipoClase; ?>"><?php echo $tipo; ?></span></td>
                <td>
                  <span class="table-entity">
                    <span class="table-entity__icon"><i class="fa-solid fa-user"></i></span>
                    <span><?php echo htmlspecialchars($cliente ?: 'Sin cliente'); ?></span>
                  </span>
                </td>
                <td><strong class="table-amount">$<?php echo number_format($value->capital, 2, ',', '.'); ?></strong></td>
                <td>$<?php echo number_format($value->valorinterestotal, 2, ',', '.'); ?></td>
                <td><strong>$<?php echo number_format($value->montototal, 2, ',', '.'); ?></strong></td>
                <td><span class="table-badge table-badge--info">$<?php echo number_format($abonoTotal, 2, ',', '.'); ?></span></td>
                <td>
                  <div class="inline-flex flex-wrap items-center gap-2">
                    <span class="table-status <?php echo $estadoClase; ?>"><?php echo $estado; ?></span>
                    <a class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-cyan-300 bg-white px-3 py-1 text-base font-extrabold text-cyan-700 transition hover:-translate-y-0.5 hover:bg-cyan-50 hover:text-cyan-700" href="/admin/creditos/detallecredito?id=<?php echo htmlspecialchars($value->ID); ?>" target="_blank" rel="noopener">
                      <i class="fa-solid fa-arrow-up-right-from-square"></i>
                      Abrir
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>
