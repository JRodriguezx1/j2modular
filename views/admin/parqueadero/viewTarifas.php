<dialog id="miDialogoViewTarifa" class="midialog-sm p-8">
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 class="text-3xl font-semibold m-0 text-neutral-800">TARIFAS</h4>
        <button class="rounded-lg hover:bg-gray-100 transition">
            <i id="btnXCerrarModalViewTarifa" class="p-2 fa-solid fa-xmark text-gray-600 text-3xl"></i>
        </button>
    </div>
    <!-- LISTA DE TARIFAS -->
    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
        <table id="tablaListaTarifas"
            class="w-full text-left border-collapse">
            <thead
                class="bg-indigo-100 text-indigo-800 uppercase text-base tracking-wide">
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200">Nombre</th>
                    <th class="px-5 py-3 border-b border-gray-200">Tarifa hora</th>
                    <th class="px-5 py-3 border-b border-gray-200">Tarifa Dia</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
                <!-- Filas dinámicas -->
            </tbody>
        </table>
    </div>
</dialog>