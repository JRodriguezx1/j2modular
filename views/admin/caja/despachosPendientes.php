<div class="box pendienteDespacho despachos-pendientes">
  <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
      <div class="flex items-center gap-4">
        <a href="/admin/caja" class="inline-grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-indigo-700 text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200" title="Volver a caja">
          <svg class="h-6 w-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
          </svg>
          <span class="sr-only">Atr&aacute;s</span>
        </a>
        <div>
          <p class="mb-1 text-xs font-extrabold uppercase tracking-[.18em] text-indigo-600">Caja</p>
          <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Ordenes pendientes de despacho</h1>
          <p class="mt-1 text-base font-medium text-slate-500">Consulte y abra las ordenes pendientes por despachar.</p>
        </div>
      </div>

      <div class="inline-flex items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-5 py-3 text-indigo-700">
        <span class="inline-grid h-10 w-10 place-items-center rounded-xl bg-white text-indigo-700 shadow-sm">
          <i class="fa-solid fa-truck-fast text-xl"></i>
        </span>
        <div>
          <p class="m-0 text-[11px] font-extrabold uppercase tracking-[.16em]">Pendientes</p>
          <p class="m-0 text-2xl font-extrabold leading-none"><?php echo count($despachosPendientes ?? []); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="mb-4 flex items-center gap-3 border-b border-slate-200 pb-4">
      <span class="inline-grid h-11 w-11 place-items-center rounded-xl bg-indigo-50 text-indigo-700">
        <i class="fa-solid fa-list-check text-lg"></i>
      </span>
      <div>
        <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Lista de despachos</h2>
        <p class="m-0 text-base font-medium leading-relaxed text-slate-500">Ordenes con entrega pendiente o listas para abrir.</p>
      </div>
    </div>

    <table id="tabladespachosPendientes" class="display responsive nowrap tabla despachos-table" width="100%">
      <thead>
        <tr>
          <th class="all dtr-control">Fecha</th>
          <th class="all">Orden</th>
          <th>Factura</th>
          <th>Cliente</th>
          <th>Zona</th>
          <th>Vendedor</th>
          <th>Observacion</th>
          <th>Estado</th>
          <th>Total</th>
          <th class="accionesth">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($despachosPendientes as $index => $value): ?>
        <tr>
          <td class="dtr-control font-normal text-slate-700"><div class="w-36 whitespace-normal leading-tight"><?php echo $value->fechacreacion; ?></div></td>
          <td class="text-right font-normal text-slate-700"><div class="w-16 whitespace-normal"><?php echo $value->num_orden;?></div></td>
          <td class="font-normal text-slate-700"><?php echo $value->prefijo.''.$value->num_consecutivo;?></td>
          <td class="font-normal text-slate-700"><div class="w-48 whitespace-normal leading-tight"><?php echo $value->cliente ?: 'N/A';?></div></td>
          <td class="font-normal text-slate-700"><div class="min-w-48 whitespace-normal leading-tight"><?php echo $value->direccion ?: '-Seleccionar-';?></div></td>
          <td class="font-normal text-slate-700"><div class="w-44 whitespace-normal leading-tight"><?php echo $value->vendedor;?></div></td>
          <td class="font-normal text-slate-700"><div class="min-w-40 whitespace-normal leading-tight"><?php echo $value->observacion;?></div></td>
          <td>
            <div data-estado="<?php echo $value->estado;?>" id="<?php echo $value->id;?>" class="max-w-full flex flex-wrap gap-2 justify-center">
              <span class="inline-flex min-w-28 items-center justify-center rounded-lg px-6 py-2 text-lg font-semibold text-white <?php echo $value->estado=='Paga'?'bg-emerald-500':'bg-indigo-700';?>"><?php echo $value->estado;?></span>
            </div>
          </td>
          <td class="text-right font-semibold text-slate-900">$<?php echo number_format($value->total, '0', ',', '.');?></td>
          <td class="accionestd">
            <div class="acciones-btns flex items-center justify-center" id="<?php echo $value->id;?>">
              <a class="inline-flex min-h-11 items-center justify-center rounded-lg bg-teal-500 px-7 text-lg font-semibold text-white shadow-sm transition hover:bg-teal-600 focus:outline-none focus:ring-4 focus:ring-teal-100" href="/admin/caja/ordenresumen?id=<?php echo $value->id;?>">Abrir</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
  .despachos-pendientes .dataTables_wrapper .dataTables_length,
  .despachos-pendientes .dataTables_wrapper .dataTables_filter {
    margin-bottom: 0.75rem;
    font-size: 1.24rem;
    color: #0f172a;
  }

  .despachos-pendientes .dataTables_wrapper .dataTables_length select,
  .despachos-pendientes .dataTables_wrapper .dataTables_filter input {
    min-height: 3.45rem;
    border-radius: 0.5rem;
    border: 1px solid #cbd5e1;
    padding: 0.6rem 1rem;
    font-size: 1.28rem;
  }

  .despachos-table thead th {
    vertical-align: middle !important;
    background: #f8fafc;
    color: #16243a;
    font-size: 1.24rem;
    font-weight: 800;
    padding-top: 1.1rem !important;
    padding-bottom: 1.1rem !important;
  }

  .despachos-table tbody td {
    vertical-align: middle !important;
    font-size: 1.28rem;
    padding-top: 1.1rem !important;
    padding-bottom: 1.1rem !important;
  }

  .despachos-pendientes .dataTables_info,
  .despachos-pendientes .dataTables_paginate {
    margin-top: 1rem;
    font-size: 1.28rem;
  }

  @media (max-width: 768px) {
    .despachos-pendientes {
      padding-bottom: 5.5rem;
    }

    .despachos-pendientes > div {
      max-width: 100%;
      overflow: hidden;
    }

    .despachos-pendientes .dataTables_wrapper {
      max-width: 100% !important;
      overflow-x: hidden !important;
      padding-bottom: 0.75rem;
    }

    .despachos-pendientes .dataTables_length,
    .despachos-pendientes .dataTables_filter,
    .despachos-pendientes .dataTables_length label,
    .despachos-pendientes .dataTables_filter label {
      display: flex !important;
      align-items: stretch !important;
      flex-direction: column;
      gap: .35rem;
      float: none !important;
      width: 100% !important;
      max-width: 100% !important;
      text-align: center !important;
    }

    .despachos-pendientes .dataTables_length select,
    .despachos-pendientes .dataTables_filter input {
      box-sizing: border-box !important;
      display: block !important;
      margin: 0 auto !important;
      max-width: 100% !important;
      min-width: 0 !important;
      width: 100% !important;
    }

    .despachos-table {
      width: 100% !important;
      min-width: 0 !important;
      border-collapse: collapse !important;
      background: #fff;
    }

    .despachos-table thead {
      display: table-header-group !important;
    }

    .despachos-table thead th {
      display: table-cell !important;
      padding: .75rem .65rem !important;
      font-size: 1rem !important;
      text-align: right;
    }

    .despachos-table thead th:nth-child(n+3) {
      display: none !important;
    }

    .despachos-table tbody tr {
      display: table-row !important;
      background: transparent !important;
    }

    .despachos-table tbody tr:not(.child) > td {
      display: table-cell !important;
      padding: .85rem .65rem !important;
      border: 1px solid #e2e8f0 !important;
      color: #0f172a;
      font-size: 1rem !important;
      line-height: 1.3;
      text-align: right !important;
      white-space: nowrap !important;
    }

    .despachos-table tbody tr:not(.child) > td:nth-child(n+3) {
      display: none !important;
    }

    .despachos-table tbody tr:not(.child) > td:first-child {
      cursor: pointer;
      padding-left: 5.2rem !important;
      position: relative;
      white-space: normal !important;
    }

    .despachos-table tbody tr:not(.child) > td:first-child div,
    .despachos-table tbody tr:not(.child) > td:nth-child(2) div {
      width: auto !important;
      min-width: 0 !important;
    }

    .despachos-table tbody tr:not(.child) > td:first-child::before {
      align-items: center;
      background: #eef2ff;
      border-radius: 999px;
      color: #4f46e5;
      content: "Ver  \25BE" !important;
      display: inline-flex !important;
      font-size: .74rem;
      font-weight: 800;
      height: 2rem;
      justify-content: center;
      left: .45rem;
      line-height: 1;
      position: absolute;
      text-transform: uppercase;
      top: 50%;
      transform: translateY(-50%);
      width: 4.2rem;
    }

    .despachos-table tbody tr.parent:not(.child) > td:first-child {
      padding-left: 6.9rem !important;
    }

    .despachos-table tbody tr.parent:not(.child) > td:first-child::before {
      background: #ccfbf1;
      color: #0f766e;
      content: "Ocultar  \25B4" !important;
      width: 6.1rem;
    }

    .despachos-table tbody tr.child td.child {
      display: table-cell !important;
      padding: .8rem 1rem 1rem !important;
      text-align: center !important;
      white-space: normal !important;
      background: #f8fafc !important;
    }

    .despachos-table tbody tr.child ul.dtr-details {
      display: inline-block !important;
      width: auto !important;
      min-width: 72%;
      margin: 0 auto !important;
      text-align: left;
    }

    .despachos-table tbody tr.child ul.dtr-details > li {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .8rem;
      padding: .55rem 0;
      border-bottom: 1px solid #e2e8f0;
      font-size: 1rem;
    }

    .despachos-table tbody tr.child span.dtr-title {
      min-width: 7rem;
      color: #1e293b;
      font-weight: 800;
      text-align: right;
    }

    .despachos-table tbody tr.child span.dtr-data {
      color: #0f172a;
      text-align: left;
    }
  }
</style>
