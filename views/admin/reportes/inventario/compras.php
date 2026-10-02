<div class="box reportescompras relative">
  <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>
  <a href="/admin/reportes" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  
  <h4 class="text-gray-600 mb-8 mt-4">Compras</h4>
  <!-- Descripción rango de fecha -->
  <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
    <span class="text-lg">📅</span>
    <span class="text-sm md:text-base font-medium">Mostrando información del período:
      <span id="fecha1" class="font-semibold text-lg"> - </span>  -  <span id="fecha2" class="font-semibold text-lg"> - </span>
    </span>
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
    <div class="flex items-center gap-3">
      <input 
        type="text" 
        name="datetimes" 
        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-xl focus:border-indigo-600 block w-60 lg:w-80 p-3 text-base     focus:outline-none focus:ring-2 focus:ring-indigo-500"
        placeholder="Seleccionar fecha"
      />
      <button id="consultarFechaPersonalizada" class="px-6 py-3 text-base font-medium bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md transition">
        Consultar
      </button>
    </div>
  </div>


  <div class="mt-4">
    <table id="tablaReportesCompras" class="display responsive nowrap tabla" width="100%">
        <thead>
          <tr>
              <th>Nº</th>
              <th>Usuario</th>
              <th>Origen</th>
              <th>Proveedor</th>
              <th>N° Factura</th>
              <th>V. Total</th>
              <th>Fecha</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          
      </tbody>
    </table>
  </div>
</div>