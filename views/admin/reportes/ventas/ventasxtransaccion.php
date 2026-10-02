<div class="box ventasxtransaccion">
  <a href="/admin/reportes" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  
  <h4 class="text-gray-600 mb-8 mt-4">Ventas por transaccion</h4>
  
  <div class="inline-flex rounded-2xl shadow-md overflow-hidden border border-gray-300">
    <button id="btnanual" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
      Anual
    </button>
    <button id="btnmensual" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 border-l border-gray-300 transition">
      Mensual
    </button>
    <button id="btndiario" class="px-6 py-3 text-base font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 border-l border-gray-300 transition">
      Diario
    </button>
  </div>


  <div class="mt-4">
    <table id="tablaTransaccioneXVenta" class="display responsive nowrap tabla" width="100%"></table>
  </div>

  <dialog id="miDialogoMes" class="p-0 rounded-2xl shadow-2xl max-w-xl w-full">
    <div class="p-12">
      <!-- Título -->
      <h4 class="text-3xl font-bold text-gray-800 text-center uppercase tracking-wide">
        Aplicar cambios
      </h4>

      <!-- Formulario -->
      <form id="formMes" class="mt-10 text-center">
        <p class="text-gray-600 text-2xl mb-10">Seleccionar año</p>

        <!-- Input -->
        <input 
          id="inputselectaño" 
          type="number" 
          name="año" 
          min="2000" 
          max="2100" 
          step="1" 
          value="2025" 
          required
          class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-1/2 p-2.5     h-14 text-xl focus:outline-none focus:ring-1 m-auto"
        >

        <!-- Botones -->
        <div class="flex justify-center gap-8 mt-12">
          <button 
            type="button" 
            class="btn-md btn-turquoise !py-4 px-6 text-xl min-w-[130px] salir rounded-xl shadow-md transition"
          >
            Salir
          </button>
          <button 
            id="btnaplicaraño" 
            type="submit" 
            class="btn-md btn-indigo !py-4 px-6 text-xl min-w-[130px] crearAddDir rounded-xl shadow-md transition"
          >
            Aplicar
          </button>
        </div>
      </form>
    </div>
  </dialog>



  <dialog id="miDialogoDiario" class="midialog-xs p-12">
    <h4 class=" text-gray-700 font-semibold">Aplicar cambios</h4>
    <form id="formDiario" class=" border-b border-gray-900/10 pb-6 text-center">
        <p class="mt-2 text-xl text-gray-600">Seleccionar mes.</p>
        <input id="inputselectmesyaño" type="month" name="mes" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" required>
        <div class="grid grid-cols-2 gap-3 mt-6">
            <button type="button" class="btn-md btn-turquoise !py-4 px-6 text-xl min-w-[130px] salir">Salir</button>
            <button id="btnaplicarmes" type="submit"class="btn-md btn-indigo !py-4 px-6 text-xl min-w-[130px] crearAddDir">Aplicar</button>
        </div>
    </form>
  </dialog>

</div>