<div class="box p-10 rounded-lg trasladosRetiros mb-20 md:mb-0">

    <!-- BOTÓN ATRÁS -->
    <a href="/admin/caja" class="text-white bg-indigo-700 hover:bg-indigo-800 rounded-lg p-4 inline-flex items-center">
        <svg class="w-6 h-6 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-width="2" d="M1 5h12M9 1l4 4-4 4"/>
        </svg>
    </a>

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10 mt-4">
        <h2 class="text-4xl font-bold text-gray-800 flex items-center gap-3">
            💸 Gestión de dinero
        </h2>

        <button id="btnNuevoMovimiento"
            class="bg-indigo-600 text-white px-6 py-4 text-xl rounded-xl shadow-md hover:bg-indigo-700 transition flex items-center gap-2">
            ➕ Nuevo movimiento
        </button>
    </div>

    <!-- ===== RESUMEN ===== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        <!-- TOTAL -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-500 to-indigo-400 text-white p-6 rounded-2xl shadow-md hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg text-indigo-100">Total disponible</p>
                    <h2 class="text-4xl font-bold">$3.300.000</h2>
                    <p class="text-indigo-200">Suma de todas las cuentas</p>
                </div>
                <span class="text-5xl">💰</span>
            </div>

            <div class="mt-4 text-indigo-100 text-lg">
                ✔ Estado financiero saludable
            </div>
        </div>

        <!-- BANCOS -->
        <div class="bg-white p-6 rounded-2xl shadow-md border hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-lg">Total bancos</p>
                    <h2 class="text-3xl font-bold text-indigo-700">$2.500.000</h2>
                    <p class="text-gray-400">Saldo en bancos</p>
                </div>
                <span class="text-5xl">🏦</span>
            </div>

            <div class="mt-4 text-gray-500 text-lg">
                Movimientos: <b class="text-gray-800">35</b>
            </div>
        </div>

        <!-- CAJA -->
        <div class="bg-white p-6 rounded-2xl shadow-md border hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-lg">Caja</p>
                    <h2 class="text-3xl font-bold text-green-600">$800.000</h2>
                    <p class="text-gray-400">Disponible en caja</p>
                </div>
                <span class="text-5xl">💵</span>
            </div>

            <div class="mt-4 text-gray-500 text-lg">
                Movimientos: <b class="text-gray-800">20</b>
            </div>
        </div>

    </div>

    <!-- ===== CUENTAS / BANCOS ===== -->
    <div class="bg-white rounded-2xl shadow border mb-12">

        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-700">Cuentas / Bancos</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-700 text-lg uppercase">
                    <tr>
                        <th class="p-5">Cuenta</th>
                        <th class="p-5">Tipo</th>
                        <th class="p-5">Saldo</th>
                        <th class="p-5">Movimientos</th>
                        <th class="p-5">Retiros</th>
                    </tr>
                </thead>

                <tbody class="text-gray-800 text-lg divide-y">

                    <tr class="hover:bg-gray-50">
                        <td class="p-5">Banco Bogotá</td>
                        <td class="p-5">Banco</td>
                        <td class="p-5 font-bold text-indigo-600">$2.500.000</td>
                        <td class="p-5">35</td>
                        <td class="p-5 text-rose-500">10</td>
                    </tr>

                    <tr class="hover:bg-gray-50">
                        <td class="p-5">Caja principal</td>
                        <td class="p-5">Caja</td>
                        <td class="p-5 font-bold text-green-600">$800.000</td>
                        <td class="p-5">20</td>
                        <td class="p-5 text-rose-500">5</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== HISTORIAL ===== -->
    <h3 class="text-xl text-gray-600 font-bold mb-6 uppercase">Historial de movimientos</h3>

    <!-- FILTROS -->
    <div class="bg-white p-5 rounded-2xl shadow-md border mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <input type="date" id="filtroDesde"
                class="bg-gray-50 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 w-full p-3 h-14 text-lg outline-none">

            <input type="date" id="filtroHasta"
                class="bg-gray-50 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 w-full p-3 h-14 text-lg outline-none">

            <select id="filtroTipo"
                class="bg-gray-50 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 w-full p-3 h-14 text-lg outline-none">
                <option value="">Todos</option>
                <option value="Traslado">Traslado</option>
                <option value="Retiro">Retiro</option>
            </select>

            <select id="filtroCuenta"
                class="bg-gray-50 border border-gray-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 w-full p-3 h-14 text-lg outline-none">
                <option value="">Todas</option>
                <option value="Caja">Caja</option>
                <option value="Banco">Banco</option>
            </select>

        </div>
    </div>

    <!-- TABLA -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow border">

        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-700 text-lg uppercase">
                <tr>
                    <th class="p-5">Fecha</th>
                    <th class="p-5">Tipo</th>
                    <th class="p-5">Origen</th>
                    <th class="p-5">Destino</th>
                    <th class="p-5">Valor</th>
                    <th class="p-5">Detalle</th>
                </tr>
            </thead>

            <tbody id="tablaMovimientos" class="text-gray-800 text-lg divide-y">

                <tr data-fecha="2025-04-10" data-tipo="Traslado" data-cuenta="Caja">
                    <td class="p-5">2025-04-10</td>
                    <td class="p-5"><span class="px-4 py-1 bg-indigo-100 text-indigo-600 rounded-full">Traslado</span></td>
                    <td class="p-5">Caja</td>
                    <td class="p-5">Banco</td>
                    <td class="p-5 font-bold text-xl">$200.000</td>
                    <td class="p-5 text-gray-500">Consignación</td>
                </tr>

                <tr data-fecha="2025-04-09" data-tipo="Retiro" data-cuenta="Banco">
                    <td class="p-5">2025-04-09</td>
                    <td class="p-5"><span class="px-4 py-1 bg-rose-100 text-rose-600 rounded-full">Retiro</span></td>
                    <td class="p-5">Banco</td>
                    <td class="p-5">—</td>
                    <td class="p-5 font-bold text-xl">$100.000</td>
                    <td class="p-5 text-gray-500">Pago proveedor</td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

<!-- ===== MODAL (INTACTO) ===== -->
<dialog id="modalMovimiento"
    class="rounded-2xl border border-gray-200 w-[95%] max-w-3xl p-8 bg-white backdrop:bg-black/40 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out">

    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
            💸 Nuevo movimiento
        </h4>

        <button
            class="p-2 rounded-lg hover:bg-gray-100 transition"
            onclick="document.getElementById('modalMovimiento').close()">
            <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
        </button>
    </div>

    <div id="divAlertaMovimiento"></div>

    <form id="formMovimiento" class="space-y-6">

        <div>
            <label class="text-lg font-medium text-gray-700">Tipo de transacción</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-14 text-lg focus:outline-none focus:ring-1">
                <option disabled selected>-Seleccionar-</option>
                <option>Traslado</option>
                <option>Retiro</option>
                <option>Ingreso</option>
            </select>
        </div>

        <div>
            <label class="text-lg font-medium text-gray-700">Cuenta origen</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-14 text-lg focus:outline-none focus:ring-1">
                <option disabled selected>-Seleccionar-</option>
                <option>Caja principal</option>
                <option>Banco Bogotá</option>
            </select>
        </div>

        <div>
            <label class="text-lg font-medium text-gray-700">Cuenta destino</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-14 text-lg focus:outline-none focus:ring-1">
                <option disabled selected>-Seleccionar-</option>
                <option>Caja principal</option>
                <option>Banco Bogotá</option>
            </select>
        </div>

        <div>
            <label class="text-lg font-medium text-gray-700">Valor</label>
            <input type="text"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-14 text-lg focus:outline-none focus:ring-1"
                placeholder="Ingresa el valor">
        </div>

        <div>
            <label class="text-lg font-medium text-gray-700">Fecha</label>
            <input type="date"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-14 text-lg focus:outline-none focus:ring-1">
        </div>

        <div>
            <label class="text-lg font-medium text-gray-700">Detalle</label>
            <textarea
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 mt-2 h-40 text-lg focus:outline-none focus:ring-1"
                placeholder="Detalle del movimiento"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button"
                class="px-6 py-3 bg-gray-200 rounded-lg"
                onclick="document.getElementById('modalMovimiento').close()">
                Cancelar
            </button>

            <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg">
                Guardar
            </button>
        </div>

    </form>
</dialog>

<script>
const modal = document.getElementById("modalMovimiento");
document.getElementById("btnNuevoMovimiento").onclick = () => modal.showModal();

// FILTROS
const filas = document.querySelectorAll("#tablaMovimientos tr");

document.querySelectorAll("#filtroDesde, #filtroHasta, #filtroTipo, #filtroCuenta")
.forEach(f => f.addEventListener("change", filtrarTabla));

function filtrarTabla(){
    const desde = filtroDesde.value;
    const hasta = filtroHasta.value;
    const tipo = filtroTipo.value;
    const cuenta = filtroCuenta.value;

    filas.forEach(fila => {
        const f = fila.dataset.fecha;
        const t = fila.dataset.tipo;
        const c = fila.dataset.cuenta;

        let show = true;

        if(desde && f < desde) show = false;
        if(hasta && f > hasta) show = false;
        if(tipo && tipo !== t) show = false;
        if(cuenta && cuenta !== c) show = false;

        fila.style.display = show ? "" : "none";
    });
}
</script>