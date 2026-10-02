<div class="editartrasladoinv box !pb-28">

  <!-- Botón Atrás -->
  <a href="/admin/almacen/trasladarinventario"
    class="text-white bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
    <svg class="w-6 h-6 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M1 5h12m0 0L9 1m4 4L9 9" />
    </svg>
    <span class="sr-only">Atrás</span>
  </a>

  <!-- Contenido principal -->
  <section class="space-y-6 mt-8">

    <!-- Información del traslado con emojis -->
    <p class="text-gray-500">Orden #: <span id="numOrden"><?php echo $ordentraslado->id;?></span></p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="flex justify-between items-center p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <div>
          <p class="text-sm text-gray-500 ">Sede origen</p>
          <p id="sedeorigen" class="text-lg font-semibold text-gray-900 ">
            <?php echo $ordentraslado->sucursal_origen;?>
          </p>
        </div>
        <span class="text-3xl">🏭</span>
      </div>

      <div class="flex justify-between items-center p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <div>
          <p class="text-sm text-gray-500 ">Sede destino</p>
          <p id="sededestino" class="text-lg font-semibold text-gray-900 ">
            <?php echo $ordentraslado->sucursal_destino;?>
          </p>
        </div>
        <span class="text-3xl">🚚</span>
      </div>

      <div class="flex justify-between items-center p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <div>
          <p class="text-sm text-gray-500 ">Tipo</p>
          <p id="tipo" class="text-lg font-semibold text-gray-900 ">
            <?php echo $ordentraslado->tipo;?>
          </p>
        </div>
        <span class="text-3xl">📋</span>
      </div>

      <div class="flex justify-between items-center p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <div>
          <p class="text-sm text-gray-500 ">Estado</p>
          <p id="estado" class="text-lg font-semibold text-gray-900 ">
            <?php echo $ordentraslado->estado;?>
          </p>
        </div>
        <span class="text-3xl">✅</span>
      </div>

      <div class="flex justify-between items-center p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <div>
          <p class="text-sm text-gray-500 ">Usuario</p>
          <p id="usuario" class="text-lg font-semibold text-gray-900 ">
            <?php echo $ordentraslado->nombreusuario;?>
          </p>
        </div>
        <span class="text-3xl">👤</span>
      </div>

      <div class="p-4 bg-indigo-50  border border-indigo-200  rounded-xl">
        <textarea id="observacion" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-32 text-base focus:outline-none focus:ring-1 focus:ring-indigo-600"><?php echo $ordentraslado->observacion;?></textarea>
      </div>

    </div>

    <!-- Sección productos -->
    <h2 class="text-xl font-semibold mb-4 text-gray-800  pt-4 border-b border-gray-200  pb-2">
      Productos a enviar
    </h2>

    <div class="flex flex-col md:flex-row gap-2">
      <select id="articulo" name=""
        class="w-full rounded-lg border border-gray-300 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600    h-14 text-base px-2"
        multiple="multiple">
        <?php foreach($totalitems as $value): ?>
          <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
        <?php endforeach; ?>  
      </select>

      <input id="cantidad" type="text"
        class="w-full md:w-1/4 border rounded-lg p-2.5 bg-gray-50 border-gray-300 text-gray-900    focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 h-14 text-base text-center"
        placeholder="Cant." value="1"
        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '0';">

      <input id="unidadmedida" type="text"
        class="w-full md:w-1/4 border rounded-lg p-2.5 bg-gray-50 border-gray-300 text-gray-900    focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 h-14 text-base"
        placeholder="Unidad" readonly>

      <button id="btnAddItem"
        class="px-4 h-14 w-full md:w-1/4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition">
        Agregar
      </button>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto bg-white  rounded-xl shadow border border-gray-200 ">
      <table id="tablaItems" class="w-full text-left border-collapse text-[15px]">
        <thead class="bg-indigo-100  text-indigo-800  uppercase text-sm tracking-wide">
          <tr>
            <th class="p-4 border-b border-gray-200 ">Producto</th>
            <th class="p-4 border-b border-gray-200 ">Cantidad</th>
            <th class="p-4 border-b border-gray-200 ">Unidad de medida</th>
            <th class="p-4 border-b border-gray-200  text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100  text-base">
          <!-- Filas dinámicas -->
        </tbody>
      </table>
    </div>

    <!-- Botón actualizar -->
    <div class="flex justify-end mt-6">
      <button id="btnUpdateTrasladoInv"
        class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-base transition">
        Actualizar
      </button>
    </div>
  </section>
</div>
