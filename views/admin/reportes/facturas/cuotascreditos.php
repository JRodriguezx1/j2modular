<div class="box cuotasCreditos w-full overflow-x-hidden sm:pb-12 [@media(max-width:991px)]:!pb-[calc(10rem+env(safe-area-inset-bottom))]">
  <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>

  <div class="mx-auto grid min-w-0 max-w-screen-2xl gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50 via-white to-white p-4 shadow-sm sm:p-6">
    <section class="grid min-w-0 grid-cols-1 items-center gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-4 sm:p-6 md:grid-cols-[auto_minmax(0,1fr)] xl:grid-cols-[auto_minmax(0,1fr)_auto]">
      <a href="/admin/creditos" class="inline-flex size-16 items-center justify-center rounded-lg bg-indigo-600 text-2xl text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700 hover:text-white" aria-label="Volver a creditos">
        <i class="fa-solid fa-arrow-left"></i>
      </a>

      <div class="min-w-0">
        <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Cartera</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Reporte de cuotas</h1>
        <span class="mt-1 block text-lg leading-snug text-slate-500">Consulta pagos de creditos y separados por rango de fechas.</span>
      </div>

      <div class="flex min-w-0 flex-wrap gap-4 md:col-span-2 xl:col-span-1 xl:justify-end" aria-label="Resumen del reporte">
        <article class="flex min-w-72 flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 xl:flex-none">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-3xl text-white"><i class="fa-solid fa-receipt"></i></span>
          <div>
            <strong id="cuotasCreditosTotalRegistros" class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900">0</strong>
            <small class="mt-1 block text-base font-bold text-slate-500">cuotas registradas</small>
          </div>
        </article>
        <article class="flex min-w-72 flex-1 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 xl:flex-none">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-cyan-600 text-3xl text-white"><i class="fa-solid fa-wallet"></i></span>
          <div>
            <strong id="cuotasCreditosTotalValor" class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900">$0</strong>
            <small class="mt-1 block text-base font-bold text-slate-500">valor pagado</small>
          </div>
        </article>
      </div>
    </section>

    <section class="grid min-w-0 gap-5 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
      <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-calendar-days"></i></span>
        <div>
          <h2 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Periodo de consulta</h2>
          <p class="mb-0 mt-1 text-lg text-slate-500">Elige un atajo o define un rango personalizado.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:flex lg:flex-wrap">
        <button id="btnmesactual" class="inline-flex min-h-16 items-center justify-center gap-2 rounded-lg border border-indigo-600 bg-indigo-600 px-5 text-lg font-extrabold text-white shadow-md shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700" type="button">
          <i class="fa-regular fa-calendar-check"></i>
          Mes actual
        </button>
        <button id="btnmesanterior" class="inline-flex min-h-16 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-lg font-extrabold text-slate-700 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" type="button">
          <i class="fa-solid fa-calendar-minus text-indigo-600"></i>
          Mes anterior
        </button>
        <button id="btnhoy" class="inline-flex min-h-16 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-lg font-extrabold text-slate-700 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" type="button">
          <i class="fa-regular fa-sun text-indigo-600"></i>
          Hoy
        </button>
        <button id="btnayer" class="inline-flex min-h-16 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 text-lg font-extrabold text-slate-700 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" type="button">
          <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
          Ayer
        </button>
      </div>

      <div class="grid min-w-0 items-end gap-3 lg:grid-cols-[minmax(0,48rem)_auto]">
        <div class="form-field">
          <label for="cuotasCreditosRango">Rango personalizado</label>
          <div class="form-input">
            <span><i class="fa-solid fa-calendar"></i></span>
            <input
              id="cuotasCreditosRango"
              type="text"
              name="datetimes"
              placeholder="Seleccionar fecha"
              autocomplete="off"
              inputmode="none"
              readonly
            >
          </div>
        </div>
        <button id="consultarFechaPersonalizada" class="inline-flex min-h-16 w-full items-center justify-center gap-2 rounded-lg border border-cyan-300 bg-cyan-50 px-5 text-lg font-extrabold text-cyan-700 transition hover:-translate-y-0.5 hover:bg-cyan-100 lg:w-auto" type="button">
          <i class="fa-solid fa-magnifying-glass-chart"></i>
          Consultar
        </button>
      </div>
    </section>

    <section class="datatable-card config-table-card min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6 [@media(max-width:991px)]:[&_.dt-paging]:!p-3">
      <div class="flex min-w-0 flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Cuotas de creditos/separados</h2>
          <p class="mb-0 mt-1 text-lg text-slate-500">Detalle de pagos, medio utilizado y estado del credito.</p>
        </div>
        <span id="cuotasCreditosPeriodo" class="inline-flex max-w-full self-start rounded-full bg-indigo-50 px-3 py-2 text-base font-extrabold text-indigo-600 sm:shrink-0">Sin periodo consultado</span>
      </div>

      <div class="min-w-0 overflow-x-auto">
        <table id="tablaCuotasCreditos" class="display responsive nowrap tabla datatable-table" width="100%">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Cliente</th>
              <th>Credito</th>
              <th>No. cuota</th>
              <th>Valor</th>
              <th>Medio de pago</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </div>
</div>
