<h1>En desarrollo</h1>

<div class="box rounded-lg p-10 !pb-20">
  <a href="/admin/almacen/trasladoinventario" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  <div class="p-8 dark:bg-neutral-900 rounded-2xl">
    <!-- Encabezado -->
    <h2 class="text-2xl font-bold mb-2">Solicitud #00123</h2>
    <p class="text-gray-600"><i class="fa-regular fa-calendar text-blue-500 mr-1"></i> Fecha: <span class="font-medium">12/09/2025</span></p>
    <p class="text-gray-600"><i class="fa-regular fa-user text-purple-500 mr-1"></i> Usuario: <span class="font-medium">Carlos Pérez</span></p>

    <div class="flex flex-wrap gap-4 mt-8">
      <!-- Origen -->
      <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg w-full md:w-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m12 8h-7m0 0l3-3m-3 3l3 3" />
        </svg>
        <div>
          <span class="text-gray-700 text-base">Origen:</span>
          <span class="font-semibold text-lg text-blue-600">Sede Norte</span>
        </div>
      </div>

      <!-- Destino -->
      <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 px-4 py-2 rounded-lg w-full md:w-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
        <div>
          <span class="text-gray-700 text-base">Destino:</span>
          <span class="font-semibold text-lg text-indigo-600">Sede Centro</span>
        </div>
      </div>

      <!-- Estado -->
      <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 px-4 py-2 rounded-lg w-full md:w-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
          <span class="text-gray-700 text-base">Estado:</span>
          <span class="font-semibold text-lg text-yellow-600">Pendiente</span>
        </div>
      </div>
    </div>

    <!-- Tabla para pantallas medianas en adelante -->
    <div class="mt-6 overflow-x-auto hidden sm:block">
      <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
        <thead class="bg-gray-100 dark:bg-neutral-800">
          <tr>
            <th class="px-6 py-3 text-left font-semibold text-gray-700">Producto</th>
            <th class="px-6 py-3 text-center font-semibold text-gray-700">Cantidad Solicitada</th>
            <th class="px-6 py-3 text-center font-semibold text-gray-700">Cantidad Aprobada</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr>
            <td class="px-6 py-3">Producto A</td>
            <td class="px-6 py-3 text-center">10</td>
            <td class="px-6 py-3 text-center">
              <input type="number" class="w-full px-2 py-1 text-center bg-gray-50 border border-gray-300 text-gray-900          rounded-lg focus:outline-none focus:border-2 focus:border-indigo-600 dark:bg-gray-600 dark:border-gray-500 dark:text-white h-14 text-xl" min="0" max="10" value="10" />
            </td>
          </tr>
          <tr>
            <td class="px-6 py-3">Producto B</td>
            <td class="px-6 py-3 text-center">5</td>
            <td class="px-6 py-3 text-center">
              <input type="number" class="w-full px-2 py-1 text-center bg-gray-50 border border-gray-300 text-gray-900          rounded-lg focus:outline-none focus:border-2 focus:border-indigo-600 dark:bg-gray-600 dark:border-gray-500 dark:text-white h-14 text-xl" min="0" max="5" value="5" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

<!-- Vista compacta en cards solo para móviles -->
<!-- Vista móvil -->
<div class="sm:hidden space-y-4 mt-4">
  <!-- Producto A -->
  <div class="p-3 border rounded-lg shadow-sm">
    <p class="flex items-center gap-2 font-semibold text-gray-800">
      <!-- Icono de caja -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M20 12H4m16 0a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v4a2 2 0 002 2m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
      </svg>
      Producto A
    </p>
    <p class="flex items-center gap-2 mt-2 text-gray-600">
      <!-- Icono solicitado -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>
      Solicitado: 10
    </p>
    <p class="flex items-center gap-2 mt-2 text-gray-600">
      <!-- Icono aprobado -->
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      Aprobado:
      <input type="number" class="w-24 px-2 py-1 text-center bg-gray-50 border border-gray-300 text-gray-900          rounded-lg focus:outline-none focus:border-2 focus:border-indigo-600 dark:bg-gray-600 dark:border-gray-500 dark:text-white h-14 text-xl" value="10" min="0" max="10" />
    </p>
  </div>

  <!-- Producto B -->
  <div class="p-3 border rounded-lg shadow-sm">
    <p class="flex items-center gap-2 font-semibold text-gray-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M20 12H4m16 0a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v4a2 2 0 002 2m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
      </svg>
      Producto B
    </p>
    <p class="flex items-center gap-2 mt-2 text-gray-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>
      Solicitado: 5
    </p>
    <p class="flex items-center gap-2 mt-2 text-gray-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      Aprobado:
      <input type="number" class="w-24 px-2 py-1 text-center bg-gray-50 border border-gray-300 text-gray-900          rounded-lg focus:outline-none focus:border-2 focus:border-indigo-600 dark:bg-gray-600 dark:border-gray-500 dark:text-white h-14 text-xl" value="5" min="0" max="5" />
    </p>
  </div>
</div>



    <!-- Botones alineados a la derecha -->
    <div class="flex justify-end gap-3 mt-4">
      <button class="flex items-center justify-center gap-2 bg-gray-200 text-gray-700 font-medium rounded-md mb-4 py-4 px-6 w-[136px] hover:bg-gray-300 transition">
        Cerrar
      </button>

      <button class="flex items-center justify-center gap-2 bg-indigo-500 text-white font-medium rounded-md mb-4 py-4 px-6 !w-[136px] hover:bg-indigo-600 transition">
        ✔ Aprobar
      </button>

      <button class="flex items-center justify-center gap-2 bg-red-500 text-white font-medium rounded-md mb-4 py-4 px-6 !w-[136px] hover:bg-red-600 transition">
        ✖ Rechazar
      </button>
    </div>
  </div>
</div>