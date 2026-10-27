<!-- Overlay -->
<div id="orderWorkspaceOverlay" class="fixed inset-0 z-[60] hidden bg-slate-950/50 backdrop-blur-[2px]"></div>

<!-- Drawer -->
<aside
  id="orderWorkspaceDrawer"
  role="dialog"
  aria-modal="true"
  aria-labelledby="orderWorkspaceTitle"
  aria-hidden="true"
  class="fixed right-0 top-0 z-[70] flex h-full w-full translate-x-full flex-col bg-slate-50 shadow-2xl transition-transform duration-300 tlg:max-w-screen-md 2xlg:max-w-screen-lg 3xlg:max-w-screen-xl">

  <!-- ENCABEZADO -->
  <header class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4">
    <div class="min-w-0">
      <div class="">
        <p class="text-lg font-semibold uppercase tracking-widest text-slate-500 m-0 mb-3">Operación de mesa</p>
      </div>

      <div class="mt-1 flex flex-wrap items-center gap-3">
        <h2 id="orderWorkspaceTitle" class="text-2xl font-bold tracking-tight text-slate-900">Mesa</h2>
        <span id="atencionNoActiva" class="rounded-lg bg-indigo-50 px-2.5 py-1 text-base font-semibold text-indigo-700">Atención por consultar</span>
      </div>

    </div>

    <button
      type="button"
      data-close-order-workspace
      aria-label="Cerrar operación de mesa"
      class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-100">
      <span class="material-symbols-outlined" aria-hidden="true">close</span>
    </button>
  </header>

  <!-- CONTENIDO -->
  <div id="orderWorkspaceContent" class="min-h-0 flex-1 overflow-y-auto p-3 sm:p-5 lg:overflow-hidden">

    <div class="flex h-full min-h-0 flex-col gap-4">
      <!-- INFORMACIÓN DE OCUPACIÓN -->
      <div id="atencionEstado" class="flex shrink-0 flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
            <span class="material-symbols-outlined">table_restaurant</span>
          </div>
          <div>
            <p class="m-0 text-lg font-semibold text-slate-900">Estado de mesa</p>
            <p id="atencionEstadoResumen" class="m-0 text-sm text-slate-500">Atención por consultar</p>
          </div>
        </div>
        <span id="atencionEstadoCuenta" class="rounded-full bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">Cuenta por consultar</span>
      </div>

      <!-- DOS COLUMNAS -->
      <div class="grid min-h-0 flex-1 gap-4 md:grid-cols-[minmax(0,1fr)_340px]">
        <!-- COLUMNA IZQUIERDA: CATÁLOGO -->
        <section class="flex min-h-[450px] min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white">
          <!-- Cabecera catálogo -->
          <div class="shrink-0 border-b border-slate-100 p-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-slate-900">Catálogo de productos</h3>
                <p class="mt-0.5 text-base text-slate-500">Selecciona productos para la mesa</p>
              </div>
              <span id="catalogProductCount" class="rounded-lg bg-slate-100 px-2.5 py-1 text-base font-medium text-slate-600">Cargando...</span>
            </div>

            <!-- Buscar -->
            <div class="relative mt-4">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
              <input
                id="searchItem"
                type="search"
                placeholder="Buscar por nombre o código..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100">
            </div>

            <!-- Categorías -->
            <div id="divCategorias" class="mt-3 flex gap-2 overflow-x-auto pb-1 text-base"></div>
          </div>

          <!-- Productos con scroll independiente -->
          <div id="catalogoPaginador" class="min-h-0 flex-1 overflow-y-auto p-4">
            <div class="mb-3 flex items-center justify-between">
              <p id="catalogProductStatus" class="text-base font-medium text-slate-500">Cargando productos...</p>
              <button type="button" class="flex items-center gap-1 text-base font-medium text-slate-600">
                <span class="material-symbols-outlined text-2xl">tune</span>
                Filtros
              </button>
            </div>

            <div id="catalogProductGrid" class="grid grid-cols-1 gap-3 xxs:grid-cols-2 xs:grid-cols-3 xsm:grid-cols-4 xmd:grid-cols-3 2xlg:grid-cols-4">

              <!-- PRODUCTO 1 -->
              <!--<article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                <div class="relative h-32 bg-slate-100">
                  <img
                    src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&amp;fit=crop&amp;w=600&amp;q=80"
                    alt="Hamburguesa clásica"
                    class="h-full w-full object-cover"
                  >
                  <span class="absolute left-2 top-2 rounded-md bg-white/95 px-2 py-1 text-[10px] font-semibold text-slate-700 shadow-sm">Popular</span>
                </div>
                <div class="p-3">
                  <h4 class="text-base font-semibold text-slate-900">Hamburguesa clásica</h4>
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-lg font-bold text-slate-900">$24.000</span>
                    <button
                      type="button"
                      aria-label="Agregar hamburguesa clásica"
                      class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-600 hover:text-white">
                      <span class="material-symbols-outlined">add</span>
                    </button>
                  </div>
                </div>
              </article>-->

              <!-- PRODUCTO 2 -->
              <!--<article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                <div class="h-32 bg-slate-100">
                  <img
                    src="https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&amp;fit=crop&amp;w=600&amp;q=80"
                    alt="Pizza margarita"
                    class="h-full w-full object-cover"
                  >
                </div>
                <div class="p-3">
                  <h4 class="text-base font-semibold text-slate-900">Pizza margarita</h4>
                  
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-lg font-bold text-slate-900">$32.000</span>
                    <button
                      type="button"
                      aria-label="Agregar pizza margarita"
                      class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white">
                      <span class="material-symbols-outlined">add</span>
                    </button>
                  </div>
                </div>
              </article>-->

              <!-- PRODUCTO 3 -->
              <!--<article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                <div class="h-32 bg-slate-100">
                  <img
                    src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&amp;fit=crop&amp;w=600&amp;q=80"
                    alt="Ensalada fresca"
                    class="h-full w-full object-cover">
                </div>
                <div class="p-3">
                  <h4 class="text-base font-semibold text-slate-900">Ensalada fresca</h4>
                  
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-lg font-bold text-slate-900">$18.000</span>
                    <button
                      type="button"
                      aria-label="Agregar ensalada fresca"
                      class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white">
                      <span class="material-symbols-outlined">add</span>
                    </button>
                  </div>
                </div>
              </article>-->

              <!-- PRODUCTO 4 -->
              <!--<article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                <div class="h-32 bg-slate-100">
                  <img
                    src="https://images.unsplash.com/photo-1513558161293-cdaf765edfd4?auto=format&amp;fit=crop&amp;w=600&amp;q=80"
                    alt="Limonada natural"
                    class="h-full w-full object-cover"
                  >
                </div>
                <div class="p-3">
                  <h4 class="text-base font-semibold text-slate-900">Limonada natural</h4>
                  
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-lg font-bold text-slate-900">$8.000</span>
                    <button
                      type="button"
                      aria-label="Agregar limonada natural"
                      class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white">
                      <span class="material-symbols-outlined">add</span>
                    </button>
                  </div>
                </div>
              </article>-->

            </div>

            <div class="mt-5 text-center">
              <button
                id="catalogLoadMore"
                type="button"
                aria-controls="catalogProductGrid"
                class="hidden rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-wait disabled:opacity-60">
                Cargar más
              </button>
            </div>
          </div>
        </section>

        <!-- COLUMNA DERECHA: CUENTA -->
        <section id="accountPanel" aria-busy="false" class="flex min-h-[520px] min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white">
          <!-- Cabecera cuenta -->
          <div class="shrink-0 border-b border-slate-100 p-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-slate-900">Cuenta de la mesa</h3>
                <p id="accountPanelOccupation" class="mt-0.5 text-base text-slate-500">Atención por consultar</p>
                <p id="accountPanelName" class="mt-0.5 text-sm text-slate-500">Cuenta por consultar</p>
              </div>
              
            </div>

            <!-- Cliente -->
            <div id="addcliente" class="mt-4 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 cursor-pointer hover:border-indigo-500 transition-all">
              <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white text-slate-500">
                <span class="material-symbols-outlined text-indigo-600">person</span>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-gray-500 m-0 uppercase font-semibold">Cliente</p>
                <p id="accountPanelClientName" class="m-0 text-xl font-semibold text-gray-900 leading-tight truncate">Seleccionar cliente</p>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <span id="badgeEstado" class="text-sm font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-700">SIN CLIENTE</span>
                <span class="material-symbols-outlined text-gray-400 text-xl">chevron_right</span>
              </div>
            </div>
          </div>

          <!-- Productos de la cuenta -->
          <div class="min-h-0 flex-1 overflow-y-auto p-4">
            <!-- Productos registrados -->
            <div class="mb-4">
              <div class="mb-3 flex items-center justify-between">
                <h4 class="text-xl font-bold uppercase tracking-wide text-slate-500">Productos registrados</h4>
                <span id="accountPanelRegisteredCount" class="text-lg font-medium text-slate-500">— ítems</span>
              </div>

              <div id="accountPanelConsumptions">
                <p class="text-sm text-slate-500"></p>
              </div>

              <template id="accountConsumptionTemplate">
                <div class="border-b border-slate-100 py-3">
                  <div class="flex items-start gap-3">
                    <span data-consumption-quantity class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600"></span>
                    <div class="min-w-0 flex-1">
                      <p data-consumption-name class="m-0 text-base font-semibold text-slate-900"></p>
                      <p data-consumption-unit-price class="mt-0.5 mb-0 text-sm text-slate-500"></p>
                      <span class="mt-1 inline-block text-lg font-medium text-emerald-600">Registrado</span>
                    </div>
                    <span data-consumption-total class="text-lg font-semibold text-slate-900"></span>
                  </div>
                </div>
              </template>
            </div>

            <!-- Carrito pendiente -->
            <div>
              <div class="mb-3 flex items-center justify-between">
                <h4 class="text-sm font-bold uppercase tracking-wide text-slate-500">Nuevos productos</h4>
                <span id="accountPanelPendingCount" class="rounded-full bg-amber-50 px-2 py-0.5 text-lg font-medium text-amber-700">0 sin guardar</span>
              </div>

              <div id="accountPanelPendingProducts" class="space-y-2">
                <p class="text-sm text-slate-500">No hay productos nuevos.</p>
              </div>
            </div>
          </div>

          <!-- Resumen fijo abajo -->
          <div class="shrink-0 border-t border-slate-200 bg-slate-50 p-4">

            <div class="space-y-2 text-lg">
              <div class="flex justify-between text-slate-600">
                <span>Productos registrados</span>
                <span id="accountPanelRegisteredAmount" class="font-semibold">—</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>Nuevos productos</span>
                <span id="accountPanelPendingAmount" class="font-semibold">$0</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>Descuento</span>
                <span id="accountPanelDiscount" class="font-semibold">—</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>Impuestos (incluidos)</span>
                <span id="accountPanelTax" class="font-semibold">—</span>
              </div>
            </div>

            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
              <span class="text-xl font-bold text-slate-800">Total: </span>
              <span id="accountPanelTotal" class="text-2xl font-extrabold tracking-tight text-slate-900">—</span>
            </div>

            <!-- Acciones -->
            <div class="mt-4 grid grid-cols-2 gap-2">
              <button
                type="button"
                disabled
                class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-base font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50">
                <span class="material-symbols-outlined text-lg">save</span>
                Guardar pedido
              </button>

              <button
                type="button"
                disabled
                class="flex items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-base font-semibold text-indigo-700 hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-50">
                <span class="material-symbols-outlined text-lg">skillet</span>
                Enviar a cocina
              </button>
            </div>

            <button
              type="button"
              disabled
              class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
              <span class="material-symbols-outlined text-xl">payments</span>
              Cobrar cuenta
              <span id="accountPanelPayTotal" class="ml-auto text-xl font-semibold">—</span>
            </button>
          </div>
        </section>

      </div>
    </div>
  </div>
</aside>

  <!-- MODAL PARA CREAR CLIENTE-->
  <?php include __DIR__. "/../../admin/ventas/modalCreateAddCli.php"; ?>
  <!-- MODAL PARA GUARDAR EL PEDIDO-->
  <?php include __DIR__. "/../../admin/ventas/modalguardarpedido.php"; ?>
  <!--///////////////////// Modal procesar el pago boton facturar /////////////////////////-->
  <?php include __DIR__. "/../../admin/ventas/modalprocesarpago.php"; ?>
  <!-- MODAL DATOS DEL ADQUIRIENTE -->
  <?php include __DIR__. "/../../admin/ventas/modaladquiriente.php"; ?>