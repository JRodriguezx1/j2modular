<main class="costosProductosPorProveedor flex h-[92vh] min-h-0 flex-col gap-4 overflow-hidden">
    <div class="bg-white rounded-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between p-4">
            <div>
                <div class="mb-2 flex items-center gap-2 text-base font-medium text-slate-500"><span>Inventario</span><span>/</span><span class="text-indigo-600">Costos por proveedor</span></div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Costos por proveedor</h1>
                <p class="mt-1 text-lg text-slate-500">Compara los costos de compra y encuentra el mejor proveedor de cada producto.</p>
            </div>
            <button id="btnAdd" type="button" class="btnDialog btnDialog_primary">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Actualizar costo
            </button>
        </div>

        <section aria-label="Indicadores" class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3 px-8 pb-4">
            <div class="flex items-center gap-3 rounded-xl bg-indigo-100 p-4">
                <div class="rounded-lg bg-indigo-200 p-3 text-indigo-700">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <p class="mb-2 mt-0 text-lg font-medium text-indigo-700">Articulos</p>
                    <h3 id="statProducts" class="text-2xl font-bold text-indigo-800"> - </h3>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-emerald-100 p-4">
                <div class="rounded-lg bg-emerald-200 p-3 text-emerald-700">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <p class="mb-2 mt-0 text-lg font-medium text-emerald-700">Con varios proveedores</p>
                    <h3 id="statMultiple" class="text-2xl font-bold text-emerald-800">0</h3>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-orange-100 p-4">
                <div class="rounded-lg bg-orange-200 p-3 text-orange-700">
                    <i class="fa-brands fa-google-drive"></i>
                </div>
                <div>
                    <p class="mb-2 mt-0 text-lg font-medium text-orange-700">Costos registrados</p>
                    <h3 id="statQuotes" class="text-2xl font-bold text-orange-800">0</h3>
                </div>
            </div>
        </section>
    </div>

    <section class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-label="Listado de productos">
      <div class="flex flex-col gap-3 border-b border-slate-200 p-4 md:flex-row md:items-center">
        <div class="relative min-w-0 flex-1">
          <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
          <input id="search" type="search" placeholder="Buscar por nombre, código o proveedor..." aria-label="Buscar productos" class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-lg outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
        </div>
        <select id="filter" aria-label="Filtrar items" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-lg text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
          <option value="all">Todos los items</option>
          <option value="multiple">Con varios proveedores</option>
          <option value="single">Con un proveedor</option>
          <option value="none">Sin proveedor</option>
        </select>
      </div>
      <div class="min-h-0 flex-1 overflow-auto">
        <table class="display responsive nowrap tabla datatable-table dataTable" width="100%">
          <thead>
            <tr>
                <th scope="col" class="!text-center">Producto</th>
                <th scope="col" class="!text-center">Proveedores</th>
                <th scope="col" class="!text-center">Mejor proveedor</th>
                <!--<th scope="col" class="!text-center">Menor costo</th>-->
                <th scope="col" class="!text-center">Ver</th>
            </tr>
          </thead>
          <tbody id="productsBody"></tbody>
        </table>
      </div>
      <div class="border-t border-slate-200 px-5 py-3 text-base text-slate-500"><span id="counter"></span></div>
    </section>
  </main>

  <!-- Modal de edición (demo local, sin persistencia en servidor) -->
  <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
      <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 id="modalTitle" class="text-xl font-bold text-slate-900">Actualizar costo</h2>
            <p class="mt-1 text-base text-slate-500">Selecciona el producto, proveedor y costo.</p>
        </div>
        <button id="closeModal" aria-label="Cerrar" type="button" class="rounded-md p-1 text-slate-500 hover:bg-slate-100">✕</button>
      </div>
      <form id="costForm" class="space-y-4">
        <label class="block">
            <span class="mb-1.5 block text-lg font-medium text-slate-600">Producto</span>
            <select id="productSelect" multiple required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base focus:border-indigo-500 focus:outline-none"></select>
        </label>
        <label class="block">
            <span class="mb-1.5 block text-lg font-medium text-slate-600">Proveedor</span>
            <select id="supplierSelect" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base focus:border-indigo-500 focus:outline-none"></select>
        </label>
        <label class="block">
            <span class="mb-1.5 block text-lg font-medium text-slate-600">Costo unitario (COP)</span>
            <input id="costInput" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base focus:border-indigo-500 focus:outline-none" />
        </label>
        <div class="flex justify-end gap-2 pt-2">
            <button id="cancelModal" type="button" class="btnDialog btnDialog_light">Cancelar</button>
            <button id="registrarCosto" type="submit" class="btnDialog btnDialog_primary">Guardar costo</button>
        </div>
      </form>
    </div>
  </div>
