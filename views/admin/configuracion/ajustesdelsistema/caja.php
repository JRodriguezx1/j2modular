<div class="configcaja contenido1 accordion_tab_content mt-6 min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6">
    <div class="px-4 py-2 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50">
        <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
        <p class="text-slate-900 font-bold m-0 text-lg">Caja</p>
    </div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">
        <div>
            <label class="block text-xl font-semibold text-gray-900 mb-1">Mensaje para factura</label>
            <textarea 
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 h-40 block w-full p-2.5     text-xl focus:outline-none focus:ring-1 hover:border-indigo-500" 
                rows="3"
                name="mensaje_para_factura"
                placeholder="Escribe el mensaje que aparecerá en la factura"><?php echo $conflocal['mensaje_para_factura']->valor_final; ?></textarea>
        </div>

        <!-- Imprimir factura automaticamente-->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Imprimir factura automaticamente</label>
            <label for="imprimirfacturasi" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="imprimirfacturasi" type="radio" name="imprimir_factura_automaticamente" class="hidden peer" value="1" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="imprimirfacturano" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="imprimirfacturano" type="radio" name="imprimir_factura_automaticamente" class="hidden peer" value="0" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 0?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Límite de descuento permitido -->
        <div class="flex flex-col gap-2">
            <label for="limite_de_descuento_permitido" class="block text-xl font-semibold text-gray-900 mb-1 mt-5">
                Límite de descuento permitido
            </label>
            <div class="flex items-center gap-3">
                <input 
                    type="text" 
                    id="limite_de_descuento_permitido" 
                    name="limite_de_descuento_permitido" 
                    min="0" max="100"
                    class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 
                        block w-28 p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
                    placeholder="0"
                    value="<?php echo $conflocal['limite_de_descuento_permitido']->valor_final; ?>"
                    oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()"
                >
                <span class="text-lg font-medium text-gray-700">%</span>
            </div>
            <p class="mt-1 text-sm text-gray-500">Indica el porcentaje máximo que se puede aplicar en descuentos.</p>
        </div>

        <!-- Permitir cierre de caja con pedidos sin facturar -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-medium font-semibold text-gray-900 mb-1 mt-5">Permitir cierre de caja con ordenes sin facturar</label>
            <!-- Opción Sí -->
            <label for="cierre-sin-facturar-si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="cierre-sin-facturar-si" type="radio" name="permitir_cierre_de_caja_con_ordenes_sin_pagar" class="hidden peer" value="1" <?php echo $conflocal['permitir_cierre_de_caja_con_ordenes_sin_pagar']->valor_final == 1?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900 ">Sí</span>
            </label>

            <!-- Opción No -->
            <label for="cierre-sin-facturar-no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="cierre-sin-facturar-no" type="radio" name="permitir_cierre_de_caja_con_ordenes_sin_pagar" class="hidden peer" value="0" <?php echo $conflocal['permitir_cierre_de_caja_con_ordenes_sin_pagar']->valor_final == 0?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900 ">No</span>
            </label>
        </div>
        
        <!-- Indicadores de caja -->
        <div>
            <label for="indicador_caja" class="block text-xl font-medium font-semibold text-gray-900 mb-1">
                Indicador de caja
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el indicador de cuadre de caja en efectivo
            </span>
            <select
                id="indicador_caja"
                name="indicador_caja"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
                <option value="1" <?php echo $conflocal['indicador_caja']->valor_final == 1?'selected':''; ?>>Efectivo total</option>
                <option value="2" <?php echo $conflocal['indicador_caja']->valor_final == 2?'selected':''; ?>>Efectivo total sin base inicial</option>
                <option value="3" <?php echo $conflocal['indicador_caja']->valor_final == 3?'selected':''; ?>>Efectivo total sin domicilios</option>
                <option value="4" <?php echo $conflocal['indicador_caja']->valor_final == 4?'selected':''; ?>>Efectivo total sin base y sin domicilios</option>
            </select>
        </div>

        <!-- Permitir venta de productos sin stock -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-medium font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Permitir venta de productos sin stock</label>
            <label for="productosinstocksi" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="productosinstocksi" type="radio" name="permitir_venta_de_productos_sin_stock" class="hidden peer" value="1" <?php echo $conflocal['permitir_venta_de_productos_sin_stock']->valor_final == 1?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="productosinstockno" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="productosinstockno" type="radio" name="permitir_venta_de_productos_sin_stock" class="hidden peer" value="0" <?php echo $conflocal['permitir_venta_de_productos_sin_stock']->valor_final == 0?'checked':'';?> >
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <div class="flex flex-col gap-2">
            <label for="tasa_de_interes_por_cuota" class="block text-xl font-medium font-semibold text-gray-900 mb-1 mt-5">
                Tasa de interes por cuota
            </label>
            <div class="flex items-center gap-3">
                <input 
                    type="text" 
                    id="tasa_de_interes_por_cuota" 
                    name="tasa_de_interes_por_cuota" 
                    min="0" max="100"
                    class=" keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 
                        block w-28 p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
                    placeholder="0"
                    value="<?php echo $conflocal['tasa_de_interes_por_cuota']->valor_final; ?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">Indica el porcentaje de interes que tendra cada cuota.</p>
        </div>

        <div class="flex flex-col gap-2">
            <label for="base_de_caja_automatico_constante" class="block text-xl font-medium font-semibold text-gray-900 mb-1 mt-5">
                Base de caja
            </label>
            <div class="flex items-center gap-3">
                <input 
                    type="text" 
                    id="base_de_caja_automatico_constante" 
                    name="base_de_caja_automatico_constante" 
                    class=" keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 
                        block w-32 p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
                    placeholder="0"
                    value="<?php echo $conflocal['base_de_caja_automatico_constante']->valor_final; ?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                >
            </div>
            <p class="mt-1 text-sm text-gray-500">Ingrso automatico de la base en caja principal</p>
        </div>
    </div>
</div> <!-- End Caja -->
