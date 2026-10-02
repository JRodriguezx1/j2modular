<!-- MODAL SELECCIÃƒâ€œN DE PRECIOS ADICIONALES -->
<dialog id="miDialogoPreciosAdicionales"
    class="rounded-2xl border border-gray-200  w-[96%] max-w-4xl p-8 bg-white  backdrop:bg-black/40 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out">


    <div class="accordion_inv relative">
        <input id="btn1" name="config" type="radio" checked>
        <input id="btn2" name="config" type="radio">
        <input id="btn3" name="config" type="radio">
        <?php if($user['perfil']<4):  ?>
            <input id="btn4" name="config" type="radio">
        <?php endif;  ?>
            <div class="precios-adicionales-tabs-wrap mb-7 flex">
                <div class="precios-adicionales-tabs btnsetup btnsetup-precios">
                    <label class="precios-adicionales-tab btn1" for="btn1">
                        <span class="material-symbols-outlined">payments</span>
                        <span>Precios</span>
                    </label>
                    <label class="precios-adicionales-tab btn2" for="btn2">
                        <span class="material-symbols-outlined">swap_vert</span>
                        <span>Variaci&oacute;n</span>
                    </label>
                    <label class="precios-adicionales-tab btn3" for="btn3">
                        <span class="material-symbols-outlined">edit_note</span>
                        <span>Anotaci&oacute;n</span>
                    </label>
                    <?php if($user['perfil']<4):  ?>
                        <label class="precios-adicionales-tab btn4" for="btn4">
                            <span class="material-symbols-outlined">percent</span>
                            <span>Comisi&oacute;n</span>
                        </label>
                    <?php endif;  ?>
                </div>
            </div>

            <!-- Encabezado del modal -->
            <div class="relative mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                <div class="flex items-center gap-4">
                    <!-- Icono -->
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 via-white to-indigo-50 border border-indigo-100 shadow-md">

                        <span
                            class="material-symbols-outlined text-[34px] text-indigo-600">
                            inventory_2
                        </span>
                    </div>

                    <!-- InformaciÃƒÂ³n -->
                    <div class="flex-1">
                        <!-- <p
                            class="uppercase tracking-[0.25em] text-xs font-bold text-indigo-600">
                            ConfiguraciÃƒÂ³n
                        </p> -->

                        <h3
                            class="mt-1 text-3xl font-bold text-slate-800">
                            Configurar producto
                        </h3>

                        <p
                            id="textCardProduct"
                            class="mt-2 text-lg text-slate-500 font-medium">
                        </p>
                    </div>
                </div>
                <button id="btnCerrarPreciosAdicionales" class="absolute top-4 right-4 h-10 w-10 rounded-full flex items-center justify-center text-slate-500  hover:bg-red-50  hover:text-red-600 transition">
                    <i class="fa-solid fa-xmark text-2xl text-slate-60 btnCerrarPreciosAdicionales"></i>
                </button>
            </div>
        

        <div class="contenedorsetup mb-4">
            
            <div class="contenido1 accordion_tab_content">
                <!-- Encabezado -->
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-slate-200">
                    <span class="material-symbols-outlined text-indigo-600 text-4xl">
                        payments
                    </span>

                    <div>
                        <h4 class="text-2xl font-bold text-slate-800">
                            Seleccionar precio
                        </h4>

                        <p class="text-slate-500 text-lg leading-7">
                            Elija uno de los precios disponibles o registre un precio libre.
                        </p>
                    </div>
                </div>

                <!-- Contenido -->
                <form id="formPreciosAdicioanles" class="space-y-5">
                    <!-- Lista de precios -->
                    <div id="listaPrecios" class="space-y-3 blockInputRadio"> </div>

                    <!-- Boton agregar precio -->
                    <?php if($conflocal['activar_precio_libre_al_facturar']->valor_final == 1): ?>
                        <div class="flex justify-center my-5">
                            <button
                                type="button"
                                id="btnMostrarNuevoPrecio"
                                class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-4 py-2.5 text-lg text-indigo-700 font-semibold shadow-sm transition hover:bg-indigo-50 hover:border-indigo-300">
                                <i id="iconPrecioLibre" class="fa-solid fa-plus"></i>
                                <span id="textoPrecioLibre">Definir precio manual</span>
                            </button>
                        </div>

                        <!-- Nuevo precio -->
                        <div id="nuevoPrecioContainer" class="overflow-hidden max-h-0 opacity-0 -translate-y-2 transition-all duration-500 ease-in-out">
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                                <!-- Encabezado -->
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="material-symbols-outlined text-emerald-600 text-3xl">
                                        attach_money
                                    </span>

                                    <div>
                                        <h5 class="text-2xl font-bold text-slate-800">Precio personalizado</h5>
                                        <p class="text-slate-500 text-lg leading-7">Registre un precio diferente para este producto.</p>
                                    </div>
                                </div>

                                <!-- Campo -->
                                <div class="max-w-sm mx-auto">
                                    <label for="precioLibre" class="block text-lg font-semibold text-slate-700 mb-3 text-center">Precio libre</label>
                                    <input
                                        id="precioLibre"
                                        type="text"
                                        name="precioLibre"
                                        placeholder="Ingrese el valor"
                                        class="bg-white
                                            border
                                            border-slate-300
                                            rounded-2xl
                                            block
                                            w-full
                                            h-14
                                            px-4
                                            text-center
                                            text-3xl
                                            font-bold
                                            text-emerald-700
                                            placeholder:text-slate-400
                                            placeholder:text-3xl
                                            focus:border-emerald-500
                                            focus:outline-none
                                            focus:ring-2
                                            focus:ring-emerald-200
                                            transition"
                                        oninput="formatearMoneda(this)">

                                    <p class="mt-3 text-center text-base text-slate-500 leading-6">
                                        Este valor reemplazar&aacute; el precio seleccionado.
                                    </p>
                                </div>
                            </div>

                            <!--
                            <div id="autorizacionSupervisor" class="mt-6 bg-indigo-50 border border-indigo-200 p-4 rounded-xl">
                                <h6 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-lock text-indigo-600"></i> AutorizaciÃƒÂ³n requerida
                                </h6>

                                <p class="text-gray-600 text-sm mb-3">
                                    Ingresa la clave del supervisor para aplicar el precio personalizado.
                                </p>

                                <input
                                    type="password"
                                    id="claveSupervisor"
                                    placeholder="Clave de supervisor"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 text-base focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                            -->

                            <!--
                            <div class="mt-6">
                                <button
                                    type="button"
                                    id="btnUsarPrecioPersonalizado"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-lg transition-all shadow-md hover:shadow-lg focus:ring-2 focus:ring-indigo-500 flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-check text-white text-lg"></i>

                                    Usar este precio

                                </button>
                            </div>
                            -->
                        </div>
                    <?php endif; ?>

                    <!-- Calculadora -->
                    <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <!-- Encabezado -->
                        <div class="flex items-center gap-3 mb-4">
                            <span class="material-symbols-outlined text-indigo-600 text-4xl">
                                calculate
                            </span>

                            <div>
                                <h5 class="text-2xl font-bold text-slate-800">
                                    Calculadora de cantidades
                                </h5>

                                <p class="text-slate-500 text-lg leading-7">
                                    Sume o reste cantidades antes de agregarlas al producto.
                                </p>
                            </div>
                        </div>

                        <!-- Entrada -->
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label
                                    for="inputCantidadCalculada"
                                    class="block text-base font-semibold text-slate-700 mb-2">
                                    Cantidad
                                </label>

                                <input
                                    id="inputCantidadCalculada"
                                    class="w-full bg-white border border-slate-300 rounded-2xl h-16 px-4 text-center text-2xl font-bold text-slate-800 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                    type="text"
                                    placeholder="0"
                                    value=""
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-2">
                                <button
                                    id="operationSum"
                                    type="button"
                                    class="w-14 h-14 rounded-xl border border-slate-300 bg-white hover:bg-indigo-50 hover:border-indigo-400 transition">

                                    <i class="fa-solid fa-plus text-xl text-indigo-600"></i>
                                </button>

                                <button
                                    id="operationLess"
                                    type="button"
                                    class="w-14 h-14 rounded-xl border border-slate-300 bg-white hover:bg-indigo-50 hover:border-indigo-400 transition">

                                    <i class="fa-solid fa-minus text-xl text-indigo-600"></i>
                                </button>

                                <button
                                    id="reset"
                                    type="button"
                                    class="w-14 h-14 rounded-xl border border-red-200 bg-white hover:bg-red-50 transition">

                                    <i class="fa-solid fa-power-off text-xl text-red-500"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Historial -->
                        <div class="mt-6">
                            <p class="text-base uppercase tracking-[0.20em] font-bold text-slate-500 text-center">
                                Operaciones
                            </p>

                            <p
                                id="lastOperation"
                                class="mt-3 text-center text-xl font-semibold text-slate-700 min-h-[32px]">
                                0
                            </p>
                        </div>

                        <!-- Resultado -->
                        <div class="mt-6 rounded-xl bg-emerald-50 border border-emerald-200 p-5">
                            <p class="text-center uppercase tracking-[0.20em] text-base font-bold text-emerald-700">
                                Resultado
                            </p>

                            <p
                                id="textCantidadCalculada"
                                class="mt-2 text-center text-6xl font-bold text-emerald-700">

                                0
                            </p>
                        </div>
                    </div>

                </form>
            </div>

            <div class="contenido2 accordion_tab_content">
                <div class="flex items-center gap-3 pb-5 mb-6 border-b border-slate-200">
                    <span class="material-symbols-outlined text-indigo-600 text-4xl">
                        inventory
                    </span>

                    <div>
                        <h4 class="text-2xl font-bold text-slate-800">
                            Configurar variaciones
                        </h4>

                        <p class="text-slate-500 text-lg leading-7">
                            Personalice los insumos incluidos y seleccione las variaciones del producto.
                        </p>
                    </div>
                </div>
                <!-- Lista de precios -->
                <div id="listaInsumos" class="space-y-3 blockInputRadio"> </div>
            </div>
            <div class="contenido3 accordion_tab_content">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 mb-6">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 text-4xl shadow-sm">
                            edit_note
                        </span>

                        <div>
                            <p class="text-base font-bold uppercase tracking-wide text-indigo-600">
                                Nota del producto
                            </p>
                            <h4 class="text-3xl font-bold text-slate-900 leading-tight">
                                Anotaci&oacute;n del producto
                            </h4>
                            <p class="mt-1 text-lg leading-6 text-slate-500">
                                Agregue una instrucci&oacute;n o detalle puntual para este producto.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200">
                        <span class="material-symbols-outlined flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 text-3xl">
                            sticky_note_2
                        </span>

                        <div>
                            <h5 class="text-2xl font-bold text-slate-900">
                                Detalle de la anotaci&oacute;n
                            </h5>
                            <p class="text-lg leading-6 text-slate-500">
                                Esta nota acompa&ntilde;ar&aacute; el producto en la venta actual.
                            </p>
                        </div>
                    </div>

                    <label for="anotacion" class="block mb-2 text-lg font-bold text-slate-700">
                        Anotaci&oacute;n
                    </label>
                    <textarea
                        id="anotacion"
                        rows="6"
                        placeholder="Ej. Sin cebolla, color rojo, entregar empacado, observaci&oacute;n especial..."
                        class="
                            w-full
                            min-h-[150px]
                            rounded-2xl
                            border
                            border-slate-300
                            bg-slate-50
                            px-5
                            py-4
                            text-lg
                            text-slate-800
                            leading-7
                            placeholder:text-slate-400
                            resize-y
                            outline-none
                            transition-all
                            duration-300
                            focus:border-indigo-500
                            focus:bg-white
                            focus:ring-2
                            focus:ring-indigo-200
                        "></textarea>

                    <div class="mt-4 flex min-h-[56px] items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-lg text-slate-600">
                        <span class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-xl text-indigo-600">
                            info
                        </span>
                        <p class="m-0 leading-6">
                            La anotaci&oacute;n solo se aplicar&aacute; a este producto durante la venta.
                        </p>
                    </div>
                </div>
            </div>
            <div class="contenido4 accordion_tab_content">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 mb-6">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 text-4xl shadow-sm">
                            percent
                        </span>

                        <div>
                            <p class="text-base font-bold uppercase tracking-wide text-indigo-600">
                                Comisi&oacute;n
                            </p>
                            <h4 class="text-3xl font-bold text-slate-900 leading-tight">
                                Comisi&oacute;n del producto
                            </h4>
                            <p class="mt-1 text-lg leading-6 text-slate-500">
                                Defina el porcentaje de comisi&oacute;n que recibir&aacute; el vendedor por este producto.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200">
                        <span class="material-symbols-outlined flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 text-3xl">
                            savings
                        </span>

                        <div>
                            <h5 class="text-2xl font-bold text-slate-900">
                                Porcentaje de comisi&oacute;n
                            </h5>
                            <p class="text-lg leading-6 text-slate-500">
                                Ingrese un valor entre 0 y 100.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(260px,0.75fr)]">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                            <label for="comisionproducto" class="block mb-3 text-lg font-bold text-slate-800">
                                Porcentaje
                            </label>

                            <div class="flex overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm transition-all duration-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-200">
                                <input
                                    id="comisionproducto"
                                    type="text"
                                    placeholder="0"
                                    value=""
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); if(parseFloat(this.value) > 100){this.value = 100;}"
                                    class="h-16 min-w-0 flex-1 border-0 bg-white px-5 text-center text-4xl font-bold text-slate-900 outline-none focus:ring-0"
                                    required
                                >

                                <div class="flex h-16 w-20 shrink-0 items-center justify-center border-l border-slate-200 bg-slate-50 text-3xl font-bold text-indigo-600">
                                    <i class="fa-solid fa-percent"></i>
                                </div>
                            </div>

                            <div class="mt-4 flex min-h-[56px] items-center gap-3 rounded-2xl border border-indigo-100 bg-white px-4 py-3 text-lg text-slate-600">
                                <span class="material-symbols-outlined flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xl text-indigo-600">
                                    info
                                </span>
                                <p class="m-0 leading-6">
                                    La comisi&oacute;n se calcular&aacute; autom&aacute;ticamente al realizar la venta.
                                </p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="material-symbols-outlined flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-2xl text-emerald-600">
                                    paid
                                </span>

                                <div>
                                    <h6 class="text-xl font-bold text-slate-900">
                                        Vista previa
                                    </h6>
                                    <p class="text-base text-slate-500">
                                        C&aacute;lculo estimado
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-3 text-lg">
                                <div class="flex items-center justify-between gap-4 rounded-xl bg-white px-4 py-3">
                                    <span class="text-slate-500">
                                        Venta
                                    </span>
                                    <span id="vistaPreviaVenta" class="font-bold text-slate-900">
                                        $0
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4 rounded-xl bg-white px-4 py-3">
                                    <span class="text-slate-500">
                                        Comisi&oacute;n
                                    </span>
                                    <span id="vistaPreviaPorcentaje" class="font-bold text-indigo-700">
                                        0%
                                    </span>
                                </div>

                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                                    <span class="block text-base font-bold uppercase tracking-wide text-emerald-700">
                                        Vendedor recibe
                                    </span>
                                    <span id="vistaPreviaComision" class="mt-1 block text-4xl font-bold text-emerald-700">
                                        $0
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones inferiores -->
        <div class="mt-4 border-t border-slate-200 pt-5" style="display:grid; grid-template-columns:minmax(0, 1fr) 150px 150px; gap:12px; align-items:center; width:100%;">
            <div></div>
            <button type="button" class="btn-md btn-turquoise" style="width:150px; min-height:48px; display:inline-flex; align-items:center; justify-content:center; padding:10px 16px;" value="Cancelar">Cancelar</button>
            <button id="aplicarOpcionesProducto" type="button" class="btn-md btn-indigo" style="width:150px; min-height:48px; display:inline-flex; align-items:center; justify-content:center; padding:10px 16px;" value="Seleccionar">Seleccionar</button>
        </div>
</div>



    <script>
        const btnPrecioLibre = document.getElementById('btnMostrarNuevoPrecio');
        const contenedorPrecioLibre = document.getElementById('nuevoPrecioContainer');
        const inputPrecioLibre = document.getElementById('precioLibre');

        const textoPrecioLibre = document.getElementById('textoPrecioLibre');
        const iconPrecioLibre = document.getElementById('iconPrecioLibre');

        btnPrecioLibre?.addEventListener('click', () => {
            const abierto = contenedorPrecioLibre.classList.contains('max-h-[600px]');

            if (abierto) {

                contenedorPrecioLibre.classList.remove(
                    'max-h-[600px]',
                    'opacity-100',
                    'translate-y-0'
                );

                contenedorPrecioLibre.classList.add(
                    'max-h-0',
                    'opacity-0',
                    '-translate-y-2'
                );

                textoPrecioLibre.textContent = 'Definir precio manual';

                iconPrecioLibre.classList.remove('fa-minus');
                iconPrecioLibre.classList.add('fa-plus');

            } else {

                contenedorPrecioLibre.classList.remove(
                    'max-h-0',
                    'opacity-0',
                    '-translate-y-2'
                );

                contenedorPrecioLibre.classList.add(
                    'max-h-[600px]',
                    'opacity-100',
                    'translate-y-0'
                );

                textoPrecioLibre.textContent = 'Ocultar precio manual';

                iconPrecioLibre.classList.remove('fa-plus');
                iconPrecioLibre.classList.add('fa-minus');

                setTimeout(() => {
                    inputPrecioLibre.focus();
                }, 300);
            }
        });
    </script>
</dialog>
