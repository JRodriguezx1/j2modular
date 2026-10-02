<!-- MODAL PARA ABONAR-->
  <dialog id="miDialogoDetalleComision" class="midialog-md p-12">
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 id="modalDetalleComision" class="font-semibold text-gray-700 mb-4">Detalle comisiones</h4>
        <button class="rounded-lg bg-indigo-500 hover:bg-indigo-700 transition">
            <i id="btnXCerrarModalDetalleComision" class="fa-solid fa-xmark px-4 py-2 text-3xl text-white"></i>
        </button>
    </div>
    <div id="divmsjalertaDetalleComision"></div>
    <div class="flex justify-between">
      <p id="numFactura" class="text-2xl uppercase">factura: -</p>
      <p id="valorVenta" class="text-2xl uppercase">venta: $0</p>
    </div>
    <div class="flex gap-4 mb-4">
      <div>
        <p class="m-0 text-slate-500 text-xl font-semibold">Caja: </p>
        <p class="m-0 text-slate-500 text-xl font-semibold">Fecha Pago: </p>
        <p class="m-0 text-slate-500 text-xl font-semibold">Vendedor: </p>
      </div>
      <div>
        <p id="nombreCaja" class="m-0 text-slate-500 text-xl"> - </p>
        <p id="fechaPago" class="m-0 text-slate-500 text-xl"> - </p>
        <p id="vendedor" class="m-0 text-slate-500 text-xl"> - </p>
      </div>
    </div>
    <!-- TABLA IMTEMS -->
    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
        <table id="tablaItemsComision"
            class="w-full text-left border-collapse">
            <thead class="bg-indigo-100 text-indigo-800 uppercase text-base tracking-wide">
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200">Id</th>
                    <th class="px-5 py-3 border-b border-gray-200">Nombre</th>
                    <th class="px-5 py-3 border-b border-gray-200">Cant</th>
                    <th class="px-5 py-3 border-b border-gray-200">Und</th>
                    <th class="px-5 py-3 border-b border-gray-200">Total</th>
                    <th class="px-5 py-3 border-b border-gray-200">Porcentaje</th>
                    <th class="px-5 py-3 border-b border-gray-200">Valor Comision.</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
                <!-- Filas dinámicas -->
            </tbody>
        </table>
    </div>
  </dialog>