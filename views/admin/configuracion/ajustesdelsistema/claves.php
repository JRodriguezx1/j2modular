<div class="contenido3 accordion_tab_content mt-6 min-w-0 w-full space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">
    <div class="flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2">
        <span class="flex items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-gear" aria-hidden="true"></i></span>
        <p class="text-slate-900 font-bold m-0 text-lg">Claves</p>
    </div>
    <div class="grid grid-cols-1 gap-4 gap-y-10 lg:grid-cols-2 [&>div]:min-w-0 [&>div]:rounded-lg [&>div]:border [&>div]:border-gray-200 [&>div]:bg-gray-50 [&>div]:p-4 [&>div]:transition [&>div:hover]:border-indigo-200 [&>div:hover]:shadow-md">
        <div>
            <label for="clave_para_eliminar_factura" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para eliminar factura
            </label>
            <input 
                type="password" 
                id="clave_para_eliminar_factura" 
                name="clave_para_eliminar_factura" 
                placeholder="Ingrese la clave eliminar factura"
                value="<?php echo $conflocal['clave_para_eliminar_factura']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <div>
            <label for="clave_para_eliminar_un_gasto/base" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para eliminar un gasto/base
            </label>
            <input 
                type="password" 
                id="clave_para_eliminar_un_gasto/base" 
                name="clave_para_eliminar_un_gasto/base" 
                placeholder="Ingrese la clave eliminar gasto/base"
                value="<?php echo $conflocal['clave_para_eliminar_un_gasto/base']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <div>
            <label for="clave_para_agregar_descuento" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para agregar descuento
            </label>
            <input 
                type="password" 
                id="clave_para_agregar_descuento" 
                name="clave_para_agregar_descuento" 
                placeholder="Ingrese la clave para descuento"
                value="<?php echo $conflocal['clave_para_agregar_descuento']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <div>
            <label for="clave_para_abrir_cajón_monedero" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para abrir cajón monedero
            </label>
            <input 
                type="password" 
                id="clave_para_abrir_cajón_monedero" 
                name="clave_para_abrir_cajón_monedero" 
                placeholder="Ingrese la clave abrir cajón"
                value="<?php echo $conflocal['clave_para_abrir_cajón_monedero']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <div>
            <label for="clave_para_ajustar_credito" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para ajustar credito
            </label>
            <input 
                type="password" 
                id="clave_para_ajustar_credito" 
                name="clave_para_ajustar_credito" 
                placeholder="Ingrese la clave para ajustar credito"
                value="<?php echo $conflocal['clave_para_ajustar_credito']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
        <div>
            <label for="clave_para_cambiar_emisor_de_una_factura" class="block mb-2 text-xl font-semibold text-gray-900 ">
                Clave para cambiar emisor de una factura
            </label>
            <input 
                type="password" 
                id="clave_para_cambiar_emisor_de_una_factura" 
                name="clave_para_cambiar_emisor_de_una_factura" 
                placeholder="Ingrese la clave para ajustar credito"
                value="<?php echo $conflocal['clave_para_cambiar_emisor_de_una_factura']->valor_final;?>"
                class="keyinput bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1 hover:border-indigo-500"
            >
        </div>
    </div> 
</div> <!-- fin claves-->
