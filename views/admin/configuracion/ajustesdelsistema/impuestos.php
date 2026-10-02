<div class="contenido6 accordion_tab_content mt-6 min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
    <div class="px-4 py-2 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50">
        <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
        <p class="text-slate-900 font-bold m-0 text-lg">Impuesto</p>
    </div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">
        <!-- Porcentaje de impuesto -->
        <div>
            <label for="porcentaje_de_impuesto" class="block text-xl font-semibold text-gray-900 mb-1">
                Tarifa del impuesto
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el impuesto y tarifa correspondiente al negocio
            </span>
            <select
                id="porcentaje_de_impuesto"
                name="porcentaje_de_impuesto"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500"
            >
                <span class="text-indigo-600 font-bold">
                    <optgroup label="IVA">
                </span>
                <option value="0" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == 0?'selected':'';?> >Exento – 0%</option>
                <option value="5" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == 5?'selected':'';?> >Bienes / Servicios al 5%</option>
                <option value="16" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == 16?'selected':'';?> >Contratos antes Ley 1819 – 16%</option>
                <option value="19" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == 19?'selected':'';?> >Tarifa general – 19%</option>
                <option value="" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == null?'selected':'';?> >Excluido de IVA</option> <!-- valor por defecto -->
                </optgroup>
                <span class="text-indigo-600 font-bold">
                    <optgroup class="text-indigo-60" label="INC">
                </span>
                <option value="8" <?php echo $conflocal['porcentaje_de_impuesto']->valor_final == 8?'selected':'';?>>Impuesto Nacional al Consumo – 8%</option>
                </optgroup>
            </select>
        </div>
        <!-- Mostrar impuesto en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar impuesto en factura</label>
            <label for="impuesto_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="impuesto_factura_si" type="radio" name="mostrar_impuesto_en_factura" class="hidden peer" value="1" <?php echo $conflocal['mostrar_impuesto_en_factura']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="impuesto_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="impuesto_factura_no" type="radio" name="mostrar_impuesto_en_factura" class="hidden peer" value="0" <?php echo $conflocal['mostrar_impuesto_en_factura']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Discriminar impuesto por producto -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Discriminar impuesto por producto</label>
            <label for="discriminar_imp_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="discriminar_imp_si" type="radio" name="discriminar_impuesto_por_producto" class="hidden peer" value="1" <?php echo $conflocal['discriminar_impuesto_por_producto']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="discriminar_imp_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="discriminar_imp_no" type="radio" name="discriminar_impuesto_por_producto" class="hidden peer" value="0" <?php echo $conflocal['discriminar_impuesto_por_producto']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Mostrar subtotales en factura sin impuesto -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar subtotales en factura sin impuesto</label>
            <label for="subtotal_sin_imp_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="subtotal_sin_imp_si" type="radio" name="mostrar_subtotales_en_factura_sin_impuesto" class="hidden peer" value="1" <?php echo $conflocal['mostrar_subtotales_en_factura_sin_impuesto']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="subtotal_sin_imp_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="subtotal_sin_imp_no" type="radio" name="mostrar_subtotales_en_factura_sin_impuesto" class="hidden peer" value="0" <?php echo $conflocal['mostrar_subtotales_en_factura_sin_impuesto']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
    </div>
</div> <!-- fin Impuesto -->
