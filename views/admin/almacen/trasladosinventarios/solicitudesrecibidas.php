<div class="box solicitudesrecibidas p-10 !pb-20 rounded-lg mb-4">
    <div>
        <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
        </a>
        <div class="flex flex-wrap gap-5 mt-6">
        <!-- Traslado de inventario -->
            <a href="/admin/almacen/trasladarinventario"
                class="grid place-items-center w-[106px] h-46 bg-gray-50 rounded-2xl border border-gray-200 shadow-md hover:shadow-lg hover:bg-gray-100 transition p-4">
                <span class="material-symbols-outlined text-5xl text-indigo-600 mb-2">sync_alt</span>
                <p class="text-gray-700 font-semibold text-center text-sm uppercase tracking-wide m-0">
                Trasladar<br>inventario
                </p>
            </a>

            <!-- Solicitud de inventario -->
            <a href="/admin/almacen/solicitarinventario"
                class="grid place-items-center w-[106px] h-46 bg-gray-50 rounded-2xl border border-gray-200 shadow-md hover:shadow-lg hover:bg-gray-100 transition p-4">
                <span class="material-symbols-outlined text-5xl text-indigo-600 mb-2">inventory_2</span>
                <p class="text-gray-700 font-semibold text-center text-sm uppercase tracking-wide m-0">
                Solicitar<br>inventario
                </p>
            </a>
        </div>

        <h4 class="text-gray-600 mt-10 font-bold uppercase">Solicitudes recibidas</h4>
        <?php include __DIR__."/../../../templates/alertas.php"; ?>
    </div>
    <div class="p-6 ">
        <!-- Encabezado -->
        <!-- <h1 class="text-2xl font-bold text-gray-800 mb-2">Solicitudes recibidas</h1> -->
        <p class="text-gray-600 mb-6 mt-0">Gestiona las solicitudes recibidas de productos y materia prima de otras sedes.</p>

        <!-- Indicadores rápidos -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
            <!-- Pendientes -->
            <div class="grid grid-cols-2 items-center bg-indigo-50 rounded-2xl shadow-md p-6 flex-col justify-center hover:scale-105 hover:shadow-lg transition">
                <!-- <div class="bg-indigo-200 text-indigo-700 rounded-full w-12 h-12 flex items-center justify-center text-2xl mb-3">⏳</div> -->
                <div class="text-xl justify-self-start font-semibold text-indigo-600 uppercase tracking-wide">Pendientes</div>
                <div class="justify-self-end text-4xl font-extrabold text-indigo-700"><?php echo $pendientes??0;?></div>
            </div>

            <!-- Aprobadas -->
            <div class="grid grid-cols-2 items-center bg-emerald-50 rounded-2xl shadow-md p-6 flex-col justify-center hover:scale-105 hover:shadow-lg transition">
                <!-- <div class="bg-emerald-200 text-emerald-700 rounded-full w-12 h-12 flex items-center justify-center text-2xl mb-3">✅</div> -->
                <div class="text-xl justify-self-start font-semibold text-emerald-600 uppercase tracking-wide">En transito</div>
                <div class="justify-self-end text-4xl font-extrabold text-emerald-700"><?php echo $entransito??0;?></div>
            </div>

            <!-- Rechazadas -->
            <div class="grid grid-cols-2 items-center bg-rose-50 rounded-2xl shadow-md p-6 justify-center hover:scale-105 hover:shadow-lg transition">
                <!-- <div class="bg-rose-200 text-rose-700 rounded-full w-12 h-12 flex items-center justify-center text-2xl mb-3">❌</div> -->
                <div class="text-xl justify-self-start font-semibold text-rose-600 uppercase tracking-wide">Rechazadas</div>
                <div class="justify-self-end text-4xl font-extrabold text-rose-700"><?php echo $rechazadas??0;?></div>
            </div>

            <!-- Entregadas -->
            <div class="grid grid-cols-2 items-center bg-sky-50 rounded-2xl shadow-md p-6 justify-center hover:scale-105 hover:shadow-lg transition">
                <!-- <div class="bg-sky-200 text-sky-700 rounded-full w-12 h-12 flex items-center justify-center text-2xl mb-3">📦</div> -->
                <div class="text-xl justify-self-start font-semibold text-sky-600 uppercase tracking-wide">Entregadas</div>
                <div class="justify-self-end text-4xl font-extrabold text-sky-700"><?php echo $entregadas??0;?></div>
            </div>
        </div> <!-- End Indicadores rápidos -->

        <!-- Filtros -->
        <div class="flex gap-3 mb-6 w-full flex-col md:flex-row p-2">
            <input type="text" placeholder="Buscar por # o producto..." class="bg-gray-50 w-full border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block md:w-1/2 p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
            
            <select id="filtroSucursal" class="bg-gray-50 w-full border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block md:w-1/4 p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                <option value="">Todas las sedes</option>
                <?php foreach($sucursales as $value): ?>
                    <option value="<?php echo $value->nombre;?>"><?php echo $value->nombre;?></option>
                <?php endforeach; ?>
            </select>

            <select id="filtroEstados" class="bg-gray-50 border w-full border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block md:w-1/4 p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="entransito">En transito</option>
                <option value="rechazado">Rechazado</option>
                <option value="entregada">Entregado</option>
            </select>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table id="tablaTraslados" class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-base font-semibold uppercase tracking-wide">
                    <tr>
                        <th class="p-4"># Solicitud</th>
                        <th class="p-4">Sede origen</th>
                        <th class="p-4">Usuario</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-lg divide-y divide-gray-100">
                    <?php foreach($solicitudesrecividas as $value): ?>
                        <tr class="hover:bg-gray-50 transition trasladosolicitudinv">
                            <td class="p-4 font-medium text-gray-900"><?php echo $value->id;?></td>
                            <td class="p-4"><?php echo $value->sucursalorigen;?></td>
                            <td class="p-4"><?php echo $value->usuario;?></td>
                            <td class="p-4"><?php echo $value->created_at;?></td>
                            <td class="p-4"><?php echo $value->tipo=='Salida'?'Ingreso':'Solicitud';?></td>
                            <td class="p-4">
                                <span class="px-3 py-1 text-base font-semibold bg-indigo-100 rounded-full <?php echo $value->estado=='pendiente'?'bg-indigo-100 text-indigo-600':($value->estado=='entransito'?'bg-yellow-100 text-yellow-700':($value->estado=='entregada'?'bg-sky-50 text-sky-600':'bg-rose-50 text-rose-600'));?>">
                                    <?php echo $value->estado;?>
                                </span>
                            </td>
                            <td id="<?php echo $value->id;?>" class="p-4 flex items-center justify-center gap-2">
                                <button class="detalle flex items-center gap-1 px-3 py-1 text-base text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">
                                    👁 Ver
                                </button>
                                <button class="enviar w-11 h-11 flex items-center justify-center text-green-600 border border-green-200 rounded-full hover:bg-green-50 text-xl">✅</button>
                                <button class="cancelar w-11 h-11 flex items-center justify-center text-rose-600 border border-rose-200 rounded-full hover:bg-rose-50 text-xl">❌</button>
                            </td>
                        </tr>
                    <?php endforeach; ?> 
                </tbody>
            </table>
        </div>
    </div>

<!-- MODAL DETALLE TRASLADO / SOLICITUD -->
<dialog id="miDialogoDetalleTrasladoSolicitud"
    class="rounded-2xl border border-gray-200 w-[95%] max-w-4xl p-8 bg-white backdrop:bg-black/40">

    <!-- Encabezado -->
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 id="modalDetalleTrasladoSolicitud"
            class="text-2xl font-bold text-indigo-700">
            📦 Detalle Traslado / Solicitud de Mercancía
        </h4>
        <button id="btnXCerrarDetalleTrasladoSolicitud"
            class="p-2 rounded-lg hover:bg-gray-100 transition">
            <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
        </button>
    </div>

    <!-- Contenido -->
    <div id="contenidodetalle" class="space-y-6">
        <!-- Cards informativos -->
        <p class="text-gray-500">Orden #: <span id="numOrden"></span></p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex justify-between items-center p-4 h-24 rounded-xl bg-indigo-50 border border-indigo-200">
                <div>
                    <p class="text-sm text-gray-500">Origen</p>
                    <p id="sedeorigen" class="text-lg font-semibold text-gray-900">—</p>
                </div>
                <div class="text-3xl">🚚</div>
            </div>

            <div class="flex justify-between items-center p-4 h-24 rounded-xl bg-indigo-50 border border-indigo-200">
                <div>
                    <p class="text-sm text-gray-500">Destino</p>
                    <p id="sededestino" class="text-lg font-semibold text-gray-900">—</p>
                </div>
                <div class="text-3xl">🏬</div>
            </div>

            <div class="flex justify-between items-center p-4 h-24 rounded-xl bg-indigo-50 border border-indigo-200">
                <div>
                    <p class="text-sm text-gray-500">Tipo</p>
                    <p id="tipo" class="text-lg font-semibold text-gray-900">—</p>
                </div>
                <div class="text-3xl">📋</div>
            </div>
        </div>
        <!-- Observaciones -->
        <div>
            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
            <textarea 
                id="observaciones" 
                rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-32 text-xl focus:outline-none focus:ring-1"
                placeholder="Agrega tus observaciones aquí...">
            </textarea>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
            <table id="tabladetalleorden"
                class="min-w-full text-base text-left text-gray-800">
                <thead
                    class="bg-indigo-100 text-indigo-800 uppercase text-sm tracking-wide">
                    <tr>
                        <th class="px-5 py-3 border-b border-gray-200">Producto</th>
                        <th class="px-5 py-3 border-b border-gray-200">Cantidad</th>
                        <th class="px-5 py-3 border-b border-gray-200">Unidad de medida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Filas dinámicas -->
                </tbody>
            </table>
        </div>
    </div>
</dialog>

</div>