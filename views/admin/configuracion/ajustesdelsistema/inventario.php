<div class="configinventario contenido2 accordion_tab_content mt-6 min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
                 
                        <div class="px-4 py-2 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50">
                            <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
                            <p class="text-slate-900 font-bold m-0 text-lg">Inventario</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">
                        
                        
                        <div class="flex flex-col gap-2">
                            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Tipo costo inventario</label>
                            <label for="costopromedio" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                                <input id="costopromedio" type="radio" name="tipo_costo_inventario" class="hidden peer" value="costo_promedio">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                                <span class="ms-3 text-xl font-medium text-gray-900">Costo promedio</span>
                            </label>

                            <label for="ultimocosto" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                                <input id="ultimocosto" type="radio" name="tipo_costo_inventario" class="hidden peer" value="ultimo_costo" checked>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                                <span class="ms-3 text-xl font-medium text-gray-900">Último costo</span>
                            </label>
                        </div> 
        
                        
                        
                    </div>
                </div> <!-- fin INVENTARIO -->
