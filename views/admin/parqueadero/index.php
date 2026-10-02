<div class="box parqueadero">
    <div>
        <h4 class="font-semibold text-slate-800 tracking-tight">Módulo de Parqueadero</h4>
        <p class="text-lg text-slate-500">Gestión de ingresos, salidas y tarifas en tiempo real.</p>
    </div>

</div>

<div class="p-6 bg-slate-50 min-h-screen">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 border-b border-slate-200 pb-4">
        <div>
            <button id="updateTarifa" class="btn-xs btn-indigo">Actualizar Tarifas</button>
            <button id="viewTarifa" class="btn-xs btn-turquoise">Ver Tarifas</button>
        </div>
        <div class="flex gap-4 mt-4 md:mt-0">
            <div class="bg-white p-3 rounded-lg shadow-sm border border-slate-200 flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-sm font-medium text-slate-700">Ocupados: <strong class="text-slate-900">14</strong></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 h-fit">
            <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                Registrar Ingreso
            </h2>
            
            <form action="?c=parqueo&a=guardar" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Placa / Patente</label>
                    <input type="text" name="placa" placeholder="ABC-123" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white uppercase text-center text-xl font-bold tracking-widest text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Tipo de Vehículo</label>
                    <select name="tipo_vehiculo_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-slate-700">
                        <option value="">Seleccione...</option>
                        <option value="1">Automóvil ($2.00 / hr)</option>
                        <option value="2">Motocicleta ($1.05 / hr)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Propietario</label>
                        <input type="text" name="propietario" placeholder="Opcional" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Teléfono</label>
                        <input type="text" name="telefono" placeholder="Opcional" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Generar Ticket de Entrada
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Vehículos en el Parqueadero</h2>
                <input type="text" placeholder="Buscar placa..." class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm w-48 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-indigo-300 transition-all flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block bg-indigo-50 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-medium mb-2">Automóvil</span>
                            <div class="bg-amber-100 border-2 border-amber-400 rounded-md px-3 py-1 font-mono text-xl font-bold text-slate-800 tracking-wider shadow-inner w-fit">
                                ABC-123
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block uppercase font-medium">Tiempo</span>
                            <span class="text-sm font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">2h 15m</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            Ingreso: <span class="font-medium text-slate-700">10:15 AM</span>
                        </div>
                        <button type="button" onclick="abrirModalCobro(1)"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 px-3 rounded-lg shadow-sm transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"/></svg>
                            Dar Salida
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL PARA CREAR/ACTUALIZAR TARIFA-->
<?php include __DIR__. "/miDialogoUpdateTarifa.php"; ?>
<!-- MODAL PARA VER TARIFA-->
<?php include __DIR__. "/viewTarifas.php"; ?>