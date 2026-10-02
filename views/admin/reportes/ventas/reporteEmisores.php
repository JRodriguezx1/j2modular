<div class="box reporteEmisores relative">
  <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>
  <a href="/admin/reportes" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  
  <h4 class="text-gray-600 mb-8 mt-4">Reporte de emisores</h4>
  <!-- Descripción rango de fecha -->
  <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
    <span class="text-lg">📅</span>
    <span class="text-sm md:text-base font-medium">Mostrando información del período:
      <span id="fecha1" class="font-semibold text-lg"> - </span>  al  <span id="fecha2" class="font-semibold text-lg"> - </span>
    </span>
  </div>
  
  <!--<div class="flex flex-col gap-4">-->

    <!-- Input y botón consultar 
    <div class="flex items-center gap-3 mb-4">
      <input 
        id="item"
        type="text" 
        name="item" 
        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-xl focus:border-indigo-600 block w-80 p-3 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500"
        placeholder="Seleccionar item"
        multiple="multiple"
      />
    </div>-->
    <!-- Tabs -->

    <div class="w-full overflow-x-auto lg:overflow-visible mb-6">
      <div class="flex w-max lg:w-auto lg:inline-flex rounded-2xl shadow-md border border-gray-300">
          <button class="tab-btn shrink-0 px-5 py-3 text-sm md:text-base font-medium bg-indigo-600 text-white transition"
            data-tab="ingresos">
            Ingresos
          </button>

          <button class="tab-btn shrink-0 px-5 py-3 text-sm md:text-base font-medium bg-white text-gray-600 border-l border-gray-300 hover:bg-indigo-50 hover:text-indigo-600 transition"
            data-tab="ventasXemisor">
            ventas Emisor
          </button>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
    <!-- Grupo de botones -->
      <div class="inline-flex rounded-2xl shadow-md overflow-hidden border border-gray-300 self-start">
        <button id="btnmesactual" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
          Mes actual
        </button>
        <button id="btnmesanterior" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 border-l border-gray-300 transition">
          Mes anterior
        </button>
        <button id="btnhoy" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 border-l border-gray-300 transition">
          Hoy
        </button>
        <button id="btnayer" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 border-l border-gray-300 transition">
          Ayer
        </button>
      </div>
      <!-- Input y botón consultar -->
      <div class="flex items-center gap-3 mb-6">
        <input 
          type="text" 
          name="datetimes" 
          class="bg-gray-50 border border-gray-300 text-gray-900 rounded-xl focus:border-indigo-600 block w-60 lg:w-80 p-3 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500"
          placeholder="Seleccionar fecha"
        />
        <button id="consultarFechaPersonalizada" class="px-6 py-3 text-base font-medium bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md transition">
          Consultar
        </button>
      </div>
    </div>

    <!-- Tab content -->
  <div id="tab-content">

    <div id="ingresos" class="tab-pane">
      <table id="tablaReporteEmisores" class="display responsive nowrap tabla" width="100%">
          <thead>
            <tr>
                <th>Emisor</th>
                <th>N° Ventas</th>
                <th>Subtotal</th>
                <th>Base Gravable</th>
                <th>Impuesto</th>
                <th>Descuento</th>
                <th>Total</th>
                <th>Ingresos</th>
            </tr>
          </thead>
        <tbody class="text-xl"></tbody>
      </table>
    </div>

    <!-- ventas por emisor -->
    <div id="ventasXemisor" class="tab-pane hidden">

      <h3 class="text-lg font-semibold mb-4">💳 Ventas por emisor</h3>

      <div class="flex items-center gap-3 mb-4">
        <select id="selectEmisor" class=" w-96"  multiple="multiple" required>
          <?php foreach($emisores as $value):   ?>
            <option value="<?php echo $value->id;?>" ><?php echo $value->nombre;?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <table id="tablaventasXemisor" class="display responsive nowrap tabla" width="100%">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="">Orden</th>
            <th class="">Fecha Pago</th>
            <th class="">N° Factura</th>
            <th class="">Tipo</th>
            <th class="">Tipo Venta</th>
            <th class="">Abrir</th>
            <th class="">Subtotal</th>
            <th class="">B. Gravable</th>
            <th class="">Imp</th>
            <th class="">Descuento</th>
            <th class="">Total</th>
            <th class="">Vendedor</th>
            <th class="">Caja</th>
          </tr>
        </thead>
        <tbody class="text-xl"></tbody>
      </table>
    </div>

  </div>
  
</div>


<script>
  const tabs = document.querySelectorAll('.tab-btn');
  const panes = document.querySelectorAll('.tab-pane');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Resetear todas las pestañas a estado inactivo
      tabs.forEach(t => {
        t.classList.remove('bg-indigo-600','text-white');
        t.classList.add('bg-white','text-gray-600');
      });

      // Ocultar todos los paneles
      panes.forEach(p => p.classList.add('hidden'));
      // Activar la pestaña clickeada
      tab.classList.add('bg-indigo-600','text-white');
      tab.classList.remove('bg-white','text-gray-600');
      // Mostrar el contenido correspondiente
      document.getElementById(tab.dataset.tab).classList.remove('hidden');
    });
  });
</script>