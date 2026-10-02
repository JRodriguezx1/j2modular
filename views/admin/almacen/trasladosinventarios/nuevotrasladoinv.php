<div class="nuevotrasladoinv box !pb-16">
  <!-- Botón atrás -->
  <a href="/admin/almacen/trasladarinventario" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>

  <div class="p-6 rounded-2xl">
    <!-- Barra de progreso -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <div class="step-circle w-10 h-10 rounded-full bg-indigo-600 text-white font-semibold flex items-center justify-center">1</div>
        <span class="text-sm font-medium text-gray-700">Datos</span>
      </div>
      <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
      <div class="flex items-center gap-2">
        <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">2</div>
        <span class="text-sm font-medium text-gray-500">Productos</span>
      </div>
      <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
      <div class="flex items-center gap-2">
        <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">3</div>
        <span class="text-sm font-medium text-gray-500">Resumen</span>
      </div>
    </div>

    <!-- Paso 1 -->
    <h4 class="text-gray-600 mt-6 font-bold uppercase">Traslado de Inventario</h4>
    <p class="text-gray-600 mb-5 mt-5">Registra el envío de productos hacia otras sedes</p>
    <section class="space-y-4 step-section step-1">
      <h2 class="text-xl font-semibold mb-4 text-gray-800 pt-6">Datos del traslado</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6">
        <div>
          <label class="block text-lg font-medium mb-1 text-gray-600">Sede Origen</label>
          <select id="sucursalorigen" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
              <option value="<?php echo $sucursalorigen->id;?>"><?php echo $sucursalorigen->nombre;?></option>
          </select>
        </div>
        <div>
          <label class="block text-lg font-medium mb-1 text-gray-600">Sede Destino</label>
          <select id="sucursaldestino" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
              <?php foreach($sucursales as $value): 
                  if($value->id != id_sucursal()): ?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->nombre; ?></option>
              <?php endif; endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-lg font-medium mb-1 text-gray-600">Fecha de Envío</label>
          <input id="fecha" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" />
        </div>
        <div>
          <label class="block text-lg font-medium mb-1 text-gray-600">Observaciones</label>
          <textarea id="observaciones" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-32 text-xl focus:outline-none focus:ring-1" placeholder="Ejemplo: envío de reposición de stock..."></textarea>
        </div>
      </div>
    </section>

    <!-- Paso 2 -->
    <section class="space-y-4 mt-8 step-section step-2 hidden">
      <h2 class="text-xl font-semibold mb-4 text-gray-800 pt-6">Productos a enviar</h2>
      <div class="flex flex-col md:flex-row gap-2">
        <select id="articulo" name="" class="w-full" multiple="multiple">
          <?php foreach($totalitems as $value): ?>
            <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
          <?php endforeach; ?>  
        </select>
        <input
            id="cantidad"
            type="text" 
            class="w-full md:w-1/4 focus:border-indigo-600 h-14 text-xl focus:outline-none focus:ring-1 border rounded-lg p-2.5 bg-gray-50 border-gray-300 text-gray-900"
            placeholder="Cant."
            value="1"
            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '0';"
        />
        <input id="unidadmedida" type="text" class="w-full md:w-1/4 focus:border-indigo-600 h-14 text-xl focus:outline-none focus:ring-1 border rounded-lg p-2.5 bg-gray-50 border-gray-300 text-gray-900" value="" readonly>
        <button id="btnAddItem" class="px-4 h-14 py-2 w-full md:w-1/4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Agregar</button>
      </div>

      <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table id="tablaItems" class="w-full text-left border-collapse">
          <thead class="bg-gray-50 text-gray-600 text-base font-semibold uppercase tracking-wide">
            <tr>
              <th class="text-left p-4">Producto</th>
              <th class="text-left p-4">Cantidad</th>
              <th class="text-center p-4">Acciones</th>
            </tr>
          </thead>
          <tbody class="[&>tr]:border-b [&>tr]:border-gray-200 [&>tr:last-child]:border-none [&>tr>td]:p-5 [&>tr>td]:text-[0.95rem] [&>tr>td]:uppercase [&>tr>td]:text-gray-600 [&>tr]:hover:bg-gray-100 [&>tr]:transition-all [&>tr]:duration-150 [&>tr]:ease-in-out [&>tr]:h-16">
            
          </tbody>
        </table>
      </div>
    </section>

    <!-- Paso 3 -->
    <section class="space-y-4 mt-8 step-section step-3 hidden">
      <h2 class="text-xl font-semibold mb-4 text-gray-800">Resumen del traslado</h2>

      <!-- Contenedor principal -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-3">
          <i class="fa-solid fa-circle-info text-indigo-600"></i>
          Detalles del traslado
        </h3>

        <!-- Información general -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Sede origen -->
          <div class="flex items-center gap-4 p-5 rounded-xl border border-gray-100 bg-gray-50 hover:shadow transition">
            <div class="w-12 h-12 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full">
              <i class="fa-solid fa-building text-xl"></i>
            </div>
            <div>
              <p class="text-sm text-gray-500 uppercase font-medium">Sede origen</p>
              <p id="resumen-sede-origen" class="text-xl font-bold text-indigo-600"><?php echo $sucursalorigen->nombre;?></p>
            </div>
          </div>

          <!-- Sede destino -->
          <div class="flex items-center gap-4 p-5 rounded-xl border border-gray-100 bg-gray-50 hover:shadow transition">
            <div class="w-12 h-12 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full">
              <i class="fa-solid fa-truck text-xl"></i>
            </div>
            <div>
              <p class="text-sm text-gray-500 uppercase font-medium">Sede destino</p>
              <p id="resumenSedeDestino" class="text-xl font-bold text-indigo-600">Sucursal destino</p>
            </div>
          </div>

          <!-- Fecha solicitud -->
          <div class="flex items-center gap-4 p-5 rounded-xl border border-gray-100 bg-gray-50 hover:shadow transition">
            <div class="w-12 h-12 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full">
              <i class="fa-solid fa-calendar-days text-xl"></i>
            </div>
            <div>
              <p class="text-sm text-gray-500 uppercase font-medium">Fecha solicitud</p>
              <p id="resumenFecha" class="text-lg font-semibold text-gray-800">--</p>
            </div>
          </div>

          <!-- Observaciones -->
          <div class="flex items-start gap-4 p-5 rounded-xl border border-gray-100 bg-gray-50 hover:shadow transition">
            <div class="w-12 h-12 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full">
              <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm text-gray-500 uppercase font-medium">Observaciones</p>
              <p id="resumenObservaciones" class="text-base text-gray-800 mt-1 break-words leading-relaxed">
                --
              </p>
            </div>
          </div>
        </div>

        <!-- Tabla de productos -->
        <div class="mt-8 overflow-x-auto bg-gray-50 border border-gray-200 rounded-xl shadow">
          <table id="tablaproductosresumen" class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 text-base font-semibold uppercase tracking-wide">
              <tr>
                <th class="p-4">Producto</th>
                <th class="p-4">Cantidad</th>
                <th class="p-4">UND</th>
              </tr>
            </thead>
            <tbody class="[&>tr]:border-b [&>tr]:border-gray-200 [&>tr:last-child]:border-none [&>tr>td]:p-5 [&>tr>td]:text-[0.95rem] [&>tr>td]:uppercase [&>tr>td]:text-gray-600 [&>tr]:hover:bg-gray-100 [&>tr]:transition-all [&>tr]:duration-150 [&>tr]:ease-in-out [&>tr]:h-16">
              <tr>
                <td class="p-3 text-sm text-gray-600">Shampoo Profesional</td>
                <td>25</td>
                <td>UND</td>
              </tr>
              <tr>
                <td>Aceite Capilar</td>
                <td>10</td>
                <td>UND</td>
              </tr>
              <tr>
                <td>Cera para cabello</td>
                <td>15</td>
                <td>UND</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>


    <!-- Navegación -->
    <div class="flex justify-between mt-6">
      <button id="btnAnteriorTrasladoInv" class="px-6 py-4 border rounded-lg hover:bg-gray-100">Anterior</button>
      <button id="btnSiguienteTrasladoInv" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Siguiente</button>
    </div>
  </div>
</div>


