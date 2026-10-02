<dialog id="miDialogoProductoCompuesto" class="midialog-md p-8">
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-3xl font-semibold m-0 text-neutral-800">Producto compuesto</h4>
            <button class="rounded-lg hover:bg-gray-100 transition">
                <i id="btnXCerrarModalProductoCompuesto" class="p-2 fa-solid fa-xmark text-gray-600 text-3xl"></i>
            </button>
        </div>
        <div class="flex justify-between">
            <p id="nombreProducto" class="mt-2 text-xl text-gray-600"></p>
            <span id="" class=" material-symbols-outlined cursor-pointer">print</span>
        </div>
        <hr class="my-4 border-t border-neutral-300">
        <!-- TABLA DE INSUMOS -->
        <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
            <table id="tablaDetalleInsumos"
                class="w-full text-left border-collapse">
                <thead
                    class="bg-indigo-100 text-indigo-800 uppercase text-base tracking-wide">
                    <tr>
                        <th class="px-5 py-3 border-b border-gray-200">Insumo</th>
                        <th class="px-5 py-3 border-b border-gray-200">Cantidad</th>
                        <th class="px-5 py-3 border-b border-gray-200">Unidad de medida</th>
                        <th class="px-5 py-3 border-b border-gray-200">Disponibilidad</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
                    <!-- Filas dinámicas -->
                </tbody>
            </table>
        </div>
    </dialog>