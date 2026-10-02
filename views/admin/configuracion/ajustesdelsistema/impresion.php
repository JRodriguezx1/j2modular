<div class="contenido5 accordion_tab_content mt-6 min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
    <div class="px-4 py-2 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50">
        <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
        <p class="text-slate-900 font-bold m-0 text-lg">Impresion</p>
    </div>
    <div class="grid grid-cols-1 gap-4 gap-y-10 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">
        <!-- Mostrar nombre del negocio en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar nombre del negocio en factura</label>
            <label for="nombrenegociosi" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="nombrenegociosi" type="radio" name="mostrar_nombre_del_negocio_en_factura" value="1" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="nombrenegociono" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="nombrenegociono" type="radio" name="mostrar_nombre_del_negocio_en_factura" value="0" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Mensaje final (Footer) -->
        <div>
            <label class="block text-xl font-semibold text-gray-900 mb-1">Mensaje final en factura</label>
            <textarea class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 h-40 block w-full p-2.5     text-xl focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500" rows="3" placeholder="Escribe el mensaje que al pie de la factura" neme="mensaje_final_en_factura"></textarea>
        </div>
        <!-- Titulo para el nombre del documento -->
        <div>
            <label for="nombre_impuesto" class="block text-xl font-semibold text-gray-900 mb-1">
                Titulo para el nombre del documento
            </label>
            <input 
                type="text" 
                id="titulo_para_el_nombre_del_documento" 
                name="titulo_para_el_nombre_del_documento" 
                placeholder="Ejemplo: NIT"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500"
            >
        </div>

        <!-- Habilitar subtotal en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Habilitar subtotal en factura</label>
            <label for="subtotal_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="subtotal_factura_si" type="radio" name="habilitar_subtotal_en_factura" value="1" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="subtotal_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 cursor-pointer hover:border-indigo-500">
                <input id="subtotal_factura_no" type="radio" name="habilitar_subtotal_en_factura" value="0" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Mostrar número de factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar número de factura</label>
            <label for="numero_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="numero_factura_si" type="radio" name="mostrar_numero_de_factura" value="1" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="numero_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="numero_factura_no" type="radio" name="mostrar_numero_de_factura" value="0" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Mostrar nombre del documento en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar nombre del documento en factura</label>
            <label for="nom_doc_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="nom_doc_factura_si" type="radio" name="mostrar_nombre_del_documento_en_factura" value="1" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="nom_doc_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="nom_doc_factura_no" type="radio" name="mostrar_nombre_del_documento_en_factura" value="0" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Mostrar observación en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar observación en factura</label>
            <label for="observacion_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="observacion_factura_si" type="radio" name="mostrar_observacion_en_factura" value="1" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="observacion_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="observacion_factura_no" type="radio" name="mostrar_observacion_en_factura" value="0" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>

        <!-- Mostrar cambio de efectivo en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar cambio de efectivo en factura</label>
            <label for="cambioefectivo_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="cambioefectivo_factura_si" type="radio" name="mostrar_cambio_de_efectivo_en_factura" value="1" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="cambioefectivo_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="cambioefectivo_factura_no" type="radio" name="mostrar_cambio_de_efectivo_en_factura" value="0" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Espaciado de papel en factura -->
        <div>
        <label for="espaciado_de_papel" class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">
            Espaciado de papel
        </label>
        <span class="block mb-1 text-sm text-gray-500">
            Ingrese el espaciado del papel (Ejemplo: POS 58 mm -- 22 / POS 80 mm -- 38 mm)
        </span>
        <input 
            type="number" 
            id="espaciado_de_papel" 
            name="espaciado_de_papel" 
            placeholder="Ingrese el espacio del papel"
            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
        >
        </div>
        <!-- Mostrar método de pago en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar método de pago en factura</label>
            <label for="met_pago_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="met_pago_factura_si" type="radio" name="mostrar_metodo_de_pago_en_factura" value="1" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="met_pago_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="met_pago_factura_no" type="radio" name="mostrar_metodo_de_pago_en_factura" value="0" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Mostrar logo en factura -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar logo en factura</label>
            <label for="logo_factura_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="logo_factura_si" type="radio" name="mostrar_logo_en_factura" value="1" class="hidden peer" checked>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="logo_factura_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="logo_factura_no" type="radio" name="mostrar_logo_en_factura" value="0" class="hidden peer">
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>
        <!-- Titulo para el nombre del documento -->
        <div>
            <label for="nombre_del_impuesto" class="block text-xl font-semibold text-gray-900 mb-1">
                Nombre del impuesto
            </label>
            <input 
                type="text" 
                id="nombre_del_impuesto"
                name="nombre_del_impuesto" 
                placeholder="Ejemplo: impoconsumo"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <!-- Titulo para el nombre del cliente -->
        <div>
            <label for="titulo_para_el_nombre_del_cliente" class="block text-xl font-semibold text-gray-900 mb-1">
                Titulo para el nombre del cliente
            </label>
            <input 
                type="text" 
                id="titulo_para_el_nombre_del_cliente" 
                name="titulo_para_el_nombre_del_cliente" 
                placeholder="Ejemplo: Razón Social"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <!-- Simbolo móneda -->
        <div>
            <label for="simbolo_moneda" class="block text-xl font-semibold text-gray-900 mb-1">
                Símbolo moneda
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el símbolo de la moneda que usará el sistema
            </span>
            <select 
                id="simbolo_moneda" 
                name="simbolo_moneda"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
                <option value="$">Peso colombiano (COP) – $</option>
                <option value="USD">$ – Dólar estadounidense</option>
                <option value="€">€ – Euro</option>
                <!-- <option value="£">£ – Libra esterlina</option>
                <option value="¥">¥ – Yen japonés</option>
                <option value="₿">₿ – Bitcoin</option> -->
            </select>
        </div>

        <!-- Mostrar ventas por usuario en print cierre de caja -->
        <div class="flex flex-col gap-2">
            <label class="block text-xl font-semibold text-gray-900 mb-1 mt-5 lg:mt-0">Mostrar ventas por usuario en impresion/ws cierre de caja</label>
            <label for="mostrar_ventasXusuario_en_impresion/ws_cierrecaja_si" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="mostrar_ventasXusuario_en_impresion/ws_cierrecaja_si" type="radio" name="mostrar_ventas_por_usuario_en_impresion/ws_cierrecaja" value="1" class="hidden peer" <?php echo $conflocal['mostrar_ventas_por_usuario_en_impresion/ws_cierrecaja']->valor_final == 1?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">Si</span>
            </label>

            <label for="mostrar_ventasXusuario_en_impresion/ws_cierrecaja_no" class="flex items-center ps-4 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg cursor-pointer select-none w-full p-2.5 h-14 text-xl focus:border-indigo-600 focus:outline-none focus:ring-1 hover:border-indigo-500">
                <input id="mostrar_ventasXusuario_en_impresion/ws_cierrecaja_no" type="radio" name="mostrar_ventas_por_usuario_en_impresion/ws_cierrecaja" value="0" class="hidden peer" <?php echo $conflocal['mostrar_ventas_por_usuario_en_impresion/ws_cierrecaja']->valor_final == 0?'checked':'';?>>
                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                <span class="ms-3 text-xl font-medium text-gray-900">No</span>
            </label>
        </div>


        <!-- Impresion de tamaño de fuente de los datos de factura -->
        <div>
            <label for="medidaFuenteDatosFactura" class="block text-xl font-semibold text-gray-900 mb-1">
                Tamaño de fuenta para los datos de factura
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el tamaño de fuente
            </span>
            <select
                id="medidaFuenteDatosFactura"
                name="medida_de_fuente_de_los_datos_de_factura"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
                <option value="1" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 1?'selected':''; ?>>T1 normal</option>
                <option value="2" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 2?'selected':''; ?>>T1 bold</option>
                <option value="3" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 3?'selected':''; ?>>T2 normal</option>
                <option value="4" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 4?'selected':''; ?>>T2 bold</option>
                <option value="5" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 5?'selected':''; ?>>T3 normal</option>
                <option value="6" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 6?'selected':''; ?>>T3 bold</option>
                <option value="7" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 7?'selected':''; ?>>T4 normal</option>
                <option value="8" <?php echo $conflocal['medida_de_fuente_de_los_datos_de_factura']->valor_final == 8?'selected':''; ?>>T4 bold</option>
            </select>
        </div>


        <!-- Impresion de tamaño de fuente en factura apartado cliente -->
        <div>
            <label for="medidaFuenteSeccionClienteFactura" class="block text-xl font-semibold text-gray-900 mb-1">
                Tamaño de fuenta seccion cliente en factura
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el tamaño de fuente
            </span>
            <select
                id="medidaFuenteSeccionClienteFactura"
                name="medida_de_fuente_del_apartado_cliente_en_factura"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
                <option value="1" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 1?'selected':''; ?>>T1 normal</option>
                <option value="2" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 2?'selected':''; ?>>T1 bold</option>
                <option value="3" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 3?'selected':''; ?>>T2 normal</option>
                <option value="4" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 4?'selected':''; ?>>T2 bold</option>
                <option value="5" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 5?'selected':''; ?>>T3 normal</option>
                <option value="6" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 6?'selected':''; ?>>T3 bold</option>
                <option value="7" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 7?'selected':''; ?>>T4 normal</option>
                <option value="8" <?php echo $conflocal['medida_de_fuente_del_apartado_cliente_en_factura']->valor_final == 8?'selected':''; ?>>T4 bold</option>
            </select>
        </div>


        <!-- Impresion de tamaño de fuente los productos en factura -->
        <div>
            <label for="medidaFuenteProductosFactura" class="block text-xl font-semibold text-gray-900 mb-1">
                Tamaño de fuenta para los productos en factura
            </label>
            <span class="block mb-1 text-sm text-gray-500">
                Seleccione el tamaño de fuente
            </span>
            <select
                id="medidaFuenteProductosFactura"
                name="medida_de_fuente_de_los_productos_en_factura"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
                <option value="1" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 1?'selected':''; ?>>T1 normal</option>
                <option value="2" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 2?'selected':''; ?>>T1 bold</option>
                <option value="3" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 3?'selected':''; ?>>T2 normal</option>
                <option value="4" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 4?'selected':''; ?>>T2 bold</option>
                <option value="5" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 5?'selected':''; ?>>T3 normal</option>
                <option value="6" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 6?'selected':''; ?>>T3 bold</option>
                <option value="7" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 7?'selected':''; ?>>T4 normal</option>
                <option value="8" <?php echo $conflocal['medida_de_fuente_de_los_productos_en_factura']->valor_final == 8?'selected':''; ?>>T4 bold</option>
            </select>
        </div>


    </div>
</div>
