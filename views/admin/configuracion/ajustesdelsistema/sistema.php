<div class="contenido8 accordion_tab_content min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
    <div class="px-4 py-2 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50">
        <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
        <p class="text-slate-900 font-bold m-0 text-lg">Sistema</p>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">

        <!-- convertir_a_pos_factura_no_impresa -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Convertir a POS factura no impresa</label>
            <label for="convertir_a_pos_factura_no_impresa_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1  cursor-pointer hover:border-indigo-500">
                <input id="convertir_a_pos_factura_no_impresa_si" type="radio" name="convertir_a_pos_factura_no_impresa" class="hidden peer" value="1" <?php //echo $conflocal['convertir_a_pos_factura_no_impresa']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="convertir_a_pos_factura_no_impresa_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="convertir_a_pos_factura_no_impresa_no" type="radio" name="convertir_a_pos_factura_no_impresa" class="hidden peer" value="0" <?php //echo $conflocal['convertir_a_pos_factura_no_impresa']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- activar modulo de venta rapido -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Activar el modo rapido de ventas</label>
            <label for="habilitar_venta_modo_rapido_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="habilitar_venta_modo_rapido_si" type="radio" name="habilitar_venta_modo_rapido" class="hidden peer" value="1" <?php echo $conflocal['habilitar_venta_modo_rapido']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="habilitar_venta_modo_rapido_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="habilitar_venta_modo_rapido_no" type="radio" name="habilitar_venta_modo_rapido" class="hidden peer" value="0" <?php echo $conflocal['habilitar_venta_modo_rapido']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- activar canal de venta -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Activar otros canales de venta</label>
            <label for="habilitar_canal_de_venta_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="habilitar_canal_de_venta_si" type="radio" name="habilitar_canal_de_venta" class="hidden peer" value="1" <?php echo $conflocal['habilitar_canal_de_venta']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="habilitar_canal_de_venta_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="habilitar_canal_de_venta_no" type="radio" name="habilitar_canal_de_venta" class="hidden peer" value="0" <?php echo $conflocal['habilitar_canal_de_venta']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>


        <!-- Impresora principal de CAJA para Android por BT -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Impresora principal de CAJA para Android por BT</label>
            <label for="impresora_principal_de_CAJA_para_Android_por_BT_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="impresora_principal_de_CAJA_para_Android_por_BT_si" type="radio" name="impresora_principal_de_CAJA_para_Android_por_BT" class="hidden peer" value="1" <?php echo $conflocal['impresora_principal_de_CAJA_para_Android_por_BT']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="impresora_principal_de_CAJA_para_Android_por_BT_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="impresora_principal_de_CAJA_para_Android_por_BT_no" type="radio" name="impresora_principal_de_CAJA_para_Android_por_BT" class="hidden peer" value="0" <?php echo $conflocal['impresora_principal_de_CAJA_para_Android_por_BT']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Activar calculadira de merma en modulo de ventas -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Activar calculadira de merma en modulo de ventas</label>
            <label for="activar_calculadira_de_merma_en_modulo_de_ventas_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="activar_calculadira_de_merma_en_modulo_de_ventas_si" type="radio" name="activar_calculadira_de_merma_en_modulo_de_ventas" class="hidden peer" value="1" <?php echo $conflocal['activar_calculadira_de_merma_en_modulo_de_ventas']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="activar_calculadira_de_merma_en_modulo_de_ventas_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="activar_calculadira_de_merma_en_modulo_de_ventas_no" type="radio" name="activar_calculadira_de_merma_en_modulo_de_ventas" class="hidden peer" value="0" <?php echo $conflocal['activar_calculadira_de_merma_en_modulo_de_ventas']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Obigatorio todos los campos al momento de crear clientes -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Obligatorio todos los campos al momento de crear cliente</label>
            <label for="obligatorio_todos_los_campos_al_crear_cliente_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="obligatorio_todos_los_campos_al_crear_cliente_si" type="radio" name="obligatorio_todos_los_campos_al_crear_cliente" class="hidden peer" value="1" <?php echo $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="obligatorio_todos_los_campos_al_crear_cliente_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="obligatorio_todos_los_campos_al_crear_cliente_no" type="radio" name="obligatorio_todos_los_campos_al_crear_cliente" class="hidden peer" value="0" <?php echo $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Activar precio libre -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Activar precio libre al facturar</label>
            <label for="activar_precio_libre_al_facturar_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="activar_precio_libre_al_facturar_si" type="radio" name="activar_precio_libre_al_facturar" class="hidden peer" value="1" <?php echo $conflocal['activar_precio_libre_al_facturar']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="activar_precio_libre_al_facturar_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="activar_precio_libre_al_facturar_no" type="radio" name="activar_precio_libre_al_facturar" class="hidden peer" value="0" <?php echo $conflocal['activar_precio_libre_al_facturar']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Activar precio libre -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Restringir caja facturadora a la caja inicial del credito</label>
            <label for="restringir_caja_facturadora_a_caja_inicial_del_credito_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="restringir_caja_facturadora_a_caja_inicial_del_credito_si" type="radio" name="restringir_caja_facturadora_a_caja_inicial_del_credito" class="hidden peer" value="1" <?php echo $conflocal['restringir_caja_facturadora_a_caja_inicial_del_credito']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="restringir_caja_facturadora_a_caja_inicial_del_credito_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="restringir_caja_facturadora_a_caja_inicial_del_credito_no" type="radio" name="restringir_caja_facturadora_a_caja_inicial_del_credito" class="hidden peer" value="0" <?php echo $conflocal['restringir_caja_facturadora_a_caja_inicial_del_credito']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <div class="flex flex-col gap-2">
            <label for="valor_por_punto" class="block text-xl font-semibold text-gray-900 mb-1 mt-5">
                Establecer valor por punto (Fidelizacion)
            </label>
            <div class="flex items-center gap-3">
                <input 
                    type="text"
                    id="valor_por_punto"
                    name="valor_por_punto"
                    class=" keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 
                        block w-32 p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
                    placeholder="0"
                    value="<?php echo $conflocal['valor_por_punto']->valor_final; ?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                >
            </div>
        </div>

        <!-- Mostrar tasa de cambio -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar tasa de cambio en modulo de ventas</label>
            <label for="mostrar_tasa_de_cambio_de_divisa_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1   cursor-pointer hover:border-indigo-500 ">
                <input id="mostrar_tasa_de_cambio_de_divisa_si" type="radio" name="mostrar_tasa_de_cambio_de_divisa" class="hidden peer" value="1" <?php echo $conflocal['mostrar_tasa_de_cambio_de_divisa']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="mostrar_tasa_de_cambio_de_divisa_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="mostrar_tasa_de_cambio_de_divisa_no" type="radio" name="mostrar_tasa_de_cambio_de_divisa" class="hidden peer" value="0" <?php echo $conflocal['mostrar_tasa_de_cambio_de_divisa']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
    </div>
    
</div> <!-- fin Sistema -->
