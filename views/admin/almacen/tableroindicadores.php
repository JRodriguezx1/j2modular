<div class="pt-3 bg-gray-50 sm:pt-20">
  
  <!-- Título -->
  <div class="max-w-4xl mx-auto text-center shadow-md px-6 py-3">
    <h2 class="text-3xl font-extrabold leading-9 sm:text-4xl sm:leading-10 text-indigo-600 uppercase">
      Tablero de indicadores
    </h2>
    <p class="mt-3 text-xl leading-7 text-gray-600 sm:mt-4 mb-0">
      Información de los principales indicadores de inventario del almacén.
    </p>
  </div>

  <!-- Contenido principal -->
  <div class="pb-12 mt-10 bg-gray-50 sm:pb-16">
    <div class="max-w-6xl mx-auto">
      <dl class="bg-white rounded-lg shadow-lg sm:grid sm:grid-cols-3">
        
        <!-- Valor inventario -->
        <div class="flex flex-col p-6 text-center border-b border-gray-100 sm:border-0 sm:border-r">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500 " id="item-1">
            Valor inventario
          </dt>
          <dd id="starsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600" aria-describedby="item-1">
            $<?php echo $valorInv;?>
          </dd>
        </div>

        <!-- Stock total -->
        <div class="flex flex-col p-6 text-center border-t border-b border-gray-100 sm:border-0 sm:border-l sm:border-r">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
            Stock total productos de venta
          </dt>
          <dd id="downloadsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600">
            <?php echo number_format($cantidadProductos??0, "0", ",", ".");?>
          </dd>
        </div>

        <!-- Cantidad referencias -->
        <div class="flex flex-col p-6 text-center border-t border-gray-100 sm:border-0 sm:border-l">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
            <p class="mb-0">Cantidad de referencias</p>
            <span class="text-base text-gray-400">(productos e insumos)</span>
          </dt>
          <dd id="sponsorsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600">
            <?php echo number_format($cantidadReferencias??0, "0", ",", ".");?>
          </dd>
        </div>

        <!-- Categorías -->
        <div class="flex flex-col p-6 text-center border-t border-gray-100 sm:border-0 sm:border-l">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
            Categorías
          </dt>
          <dd id="sponsorsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600">
            <?php echo $cantidadCategorias;?>
          </dd>
        </div>

        <!-- Bajo stock -->
        <div class="flex flex-col p-6 text-center border-t border-gray-100 sm:border-0 sm:border-l">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
            Items con bajo stock
          </dt>
          <dd id="sponsorsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600">
              <button id="btnviewProductsBajoStock" class="rounded-xl border-2 border-blue-100 shadow-md px-6 py-2"><?php echo number_format($bajoStock??0, "0", ",", ".");?></button>
          </dd>
        </div>

        <!-- Agotados -->
        <div class="flex flex-col p-6 text-center border-t border-gray-100 sm:border-0 sm:border-l">
          <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
            Productos agotados
          </dt>
          <dd id="sponsorsCount" class="order-1 text-5xl font-extrabold leading-none text-indigo-600">
            <?php echo number_format($productosAgotados??0, "0", ",", ".");?>
          </dd>
        </div>

      </dl>
    </div>
  </div>
</div>

<dialog id="miDialogoBajoStock" class="midialog-lg p-12">
    <div class="flex justify-between items-center mb-4">
        <h4 id="modalTotalBajoStock" class="font-semibold text-gray-700 mb-4">Items con bajo stock</h4>
        <button class="rounded-lg bg-indigo-500 hover:bg-indigo-700 transition"><i id="btnCerrarTotalBajoStock" class="fa-solid fa-xmark px-4 py-2 text-3xl text-white"></i></button>
    </div>
    <div id="divmsjalerta"></div>
    <!-- TABLA DE INSUMOS -->
    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
        <table id="tablaBajoStock"
            class="w-full text-left border-collapse">
            <thead
                class="bg-indigo-100 text-indigo-800 uppercase text-base tracking-wide">
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200">Id</th>
                    <th class="px-5 py-3 border-b border-gray-200">Nombre</th>
                    <th class="px-5 py-3 border-b border-gray-200">Tipo</th>
                    <th class="px-5 py-3 border-b border-gray-200">Stock Actual</th>
                    <th class="px-5 py-3 border-b border-gray-200">Stock Min</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
                <!-- Filas dinámicas -->
            </tbody>
        </table>
    </div>
</dialog>
