<!-- VISTA: DETALLE DE COMPRA -->
<section class="box detallecompra p-10 !pb-20 rounded-lg mb-4">

  <!-- Botón atrás -->
  <a href="/admin/reportes/compras"
    class="text-white bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2 mb-6 transition">
    <svg class="w-6 h-6 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M1 5h12m0 0L9 1m4 4L9 9" />
    </svg>
    <span class="sr-only">Atrás</span>
  </a>

  <!-- Título principal -->
  <h4 class="text-gray-600 mb-4 mt-0 font-bold uppercase tracking-wide">
    Detalle de compra
  </h4>

  <!-- Descripción -->
  <p class="text-gray-600 mb-8">
    Visualiza la información general y el detalle de los productos adquiridos en esta compra.
  </p>

  <!-- Bloque resumen -->
  <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <div class="bg-indigo-50 rounded-2xl shadow-md p-4 flex justify-between items-center hover:scale-105 hover:shadow-lg transition">
      <div>
        <p class="text-gray-700 text-base font-semibold">Usuario</p>
        <h3 class="text-xl font-extrabold text-indigo-700 leading-tight"><?php echo $usuario->nombre.' '.$usuario->apellido??'';?></h3>
      </div>
      <i class="fa-solid fa-user text-indigo-500 text-3xl"></i>
    </div>

    <div class="bg-indigo-50 rounded-2xl shadow-md p-4 flex justify-between items-center hover:scale-105 hover:shadow-lg transition">
      <div>
        <p class="text-gray-700 text-base font-semibold">Proveedor</p>
        <h3 class="text-xl font-extrabold text-indigo-700 leading-tight"><?php echo $proveedor->nombre;?></h3>
      </div>
      <i class="fa-solid fa-store text-indigo-500 text-3xl"></i>
    </div>

    <div class="bg-indigo-50 rounded-2xl shadow-md p-4 flex justify-between items-center hover:scale-105 hover:shadow-lg transition">
      <div>
        <p class="text-gray-700 text-base font-semibold">N° Factura</p>
        <h3 class="text-xl font-extrabold text-indigo-700 leading-tight"><?php echo $compra->nfactura;?></h3>
      </div>
      <i class="fa-solid fa-file-invoice text-indigo-500 text-3xl"></i>
    </div>
  </section>

  <!-- Bloque información detallada -->
<div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">

  <h5 class="text-gray-600 font-semibold uppercase mb-5 border-b border-gray-100 pb-3 flex items-center gap-2">
    <i class="fa-solid fa-circle-info text-indigo-500 text-lg"></i>
    Información general
  </h5>

  <!-- Información principal -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700 mb-6">
    <div class="flex flex-col bg-indigo-50 rounded-xl p-3 space-y-4">
      <span class="text-base text-gray-500 font-semibold">Forma de pago</span>
      <span class="text-indigo-700 text-xl font-extrabold"><?php echo $compra->formapago;?></span>
    </div>

    <div class="flex flex-col bg-indigo-50 rounded-xl p-3 space-y-4">
      <span class="text-base text-gray-500 font-semibold">Fecha de compra</span>
      <span class="text-indigo-700 text-xl font-extrabold"><?php echo $compra->fechacompra;?></span>
    </div>

    <div class="flex flex-col bg-indigo-50 rounded-xl p-3 space-y-4">
      <span class="text-base text-gray-500 font-semibold">Valor total</span>
      <span class="text-indigo-700 text-xl font-extrabold">$<?php echo number_format($compra->valortotal, '2', ',', '.');?></span>
    </div>
  </div>

  <!-- Origen del dinero -->
  <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_2fr] gap-4 text-gray-700 mb-6">
    <!-- Caja principal -->
    <div class="flex flex-col bg-green-50 rounded-xl p-3 space-y-4">
      <span class="text-base text-gray-500 font-semibold">Origen del dinero</span>
      <span class="text-green-700 text-xl font-extrabold"><?php echo $caja->nombre;?></span>
    </div>

    <!-- Tipo de origen -->
    <div class="flex flex-col bg-green-50 rounded-xl p-3 space-y-4">
      <span class="text-base text-gray-500 font-semibold">Tipo de origen</span>
      <span class="text-green-700 text-xl font-extrabold">Efectivo</span>
    </div>

    <!-- Observación -->
    <div class="bg-green-50 rounded-xl p-4">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-note-sticky text-green-500 text-xl"></i>
        <p class="font-semibold text-gray-700">Observación</p>
      </div>
      <p class="text-gray-600 leading-relaxed mt-2">
        <?php echo $compra->observacion;?>
      </p>
    </div>
  </div>
</div>

  <!-- Tabla de productos -->
  <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
    <table class="w-full text-left border-collapse">
      <thead class="bg-gray-50 text-gray-600 text-base font-semibold uppercase tracking-wide">
        <tr>
          <th class="p-4">#</th>
          <th class="p-4">Producto</th>
          <th class="p-4">Cantidad</th>
          <th class="p-4">Unidad</th>
          <th class="p-4">Precio Unitario</th>
          <th class="p-4">Subtotal</th>
        </tr>
      </thead>
      <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
        <?php foreach($detallecompra as $index => $value): ?>
        <tr class="hover:bg-gray-50 transition">
          <td class="px-6 py-3"><?php echo $index+1;?></td>
          <td class="px-6 py-3"><?php echo $value->nombreitem;?></td>
          <td class="px-6 py-3"><?php echo $value->cantidad;?></td>
          <td class="px-6 py-3"><?php echo $value->unidad;?></td>
          <td class="px-6 py-3"><?php echo $value->valorunidad;?></td>
          <td class="px-6 py-3"><?php echo $value->valorcompra;?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Totales de compra -->
  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 mt-8 w-full md:w-1/2 ml-auto">
    <h5 class="text-gray-600 font-semibold mb-4 uppercase">Resumen de compra</h5>

    <div class="space-y-3 text-gray-700 text-base">
      <div class="flex justify-between">
        <span>Subtotal:</span>
        <span class="font-semibold">$<?php echo number_format($compra->valortotal, '2', ',', '.');?></span>
      </div>

      <div class="flex justify-between">
        <span>IVA (<?php echo number_format($compra->impuesto??0, '2', ',', '.');?>%):</span>
        <span class="font-semibold">$<?php echo number_format($compra->valorimp, '2', ',', '.');?></span>
      </div>

      <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between text-lg font-bold text-gray-800">
        <span>Total:</span>
        <span class="text-indigo-600">$<?php echo number_format($compra->valortotal, '2', ',', '.');?></span>
      </div>
    </div>
  </div>

  <!-- Botón imprimir -->
  <div class="flex justify-end mt-8">
    <button
      id="btnPrintCompra"
      class="flex items-center gap-2 bg-indigo-600 text-white font-medium px-6 py-3 rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition">
      <i class="fa fa-print"></i>
      Imprimir
    </button>
  </div>

</section>
