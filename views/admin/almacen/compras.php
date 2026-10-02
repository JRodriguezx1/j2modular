<div class="box compras p-10 !pb-20 rounded-lg mb-4">
    <div>
        <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
        </a>
        <h4 class="text-gray-600 mb-10 mt-4">Compras</h4>
        <?php include __DIR__. "/../../templates/alertas.php"; ?>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
            <form id="formComprar" action="">
                <div class="border-b border-gray-900/10 pb-8 mb-3">
                
                    <p class="mt-2 text-xl text-gray-600">Ingreso Almacen.</p>

                    <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-8">

                        <div class="sm:col-span-3 tlg:col-span-2">
                            <label for="proveedor" class="block text-2xl font-medium text-gray-600">Proveedor</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="proveedor" name="proveedor" autocomplete="proveedor-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" required>
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($proveedores as $value): ?>
                                    <option value="<?php echo $value->id; ?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg> -->
                            </div>
                        </div>

                        <!-- Porcentaje de impuesto -->
                        <!--<div class="sm:col-span-3 md:col-span-3 tlg:col-span-2"> 
                            <label for="porcentaje_impuesto" class="block text-2xl font-medium text-gray-600">Impuesto</label>
                            -->
                            <!-- <span class="block mb-1 text-sm text-gray-500">
                                Seleccione el impuesto y tarifa correspondiente al negocio
                            </span> -->
                            <!--<select 
                                id="inputimpuesto" 
                                name="inputimpuestocompra"
                                class="bg-gray-50 border mt-2 border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1"
                            >
                                <span class="text-indigo-600 font-bold">
                                    <optgroup label="IVA">
                                </span>
                                <option value="0">Exento – 0%</option>
                                <option value="5">Bienes / Servicios al 5%</option>
                                <option value="16">Contratos antes Ley 1819 – 16%</option>
                                <option value="19">Tarifa general – 19%</option>
                                <option value="excluido">Excluido</option>
                                </optgroup>
                                <span class="text-indigo-600 font-bold">
                                    <optgroup class="text-indigo-60" label="INC">
                                </span>
                                <option value="8">Impuesto Nacional al Consumo – 8%</option>
                                </optgroup>
                            </select>
                        </div>-->

                        <div class="sm:col-span-2 md:col-span-2 tlg:col-span-2">
                            <label for="valorimp" class="block text-2xl font-medium text-gray-600">Impuesto Total</label>
                            <div class="mt-2">
                                <input 
                                    id="valorimp" 
                                    type="text" 
                                    name="valorimp" 
                                    autocomplete="family-name" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" 
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                    placeholder="EJ: 25100"
                                >
                            </div>
                        </div>

                        <div class="sm:col-span-2 md:col-span-2 tlg:col-span-2">
                            <label for="factura" class="block text-2xl font-medium text-gray-600">N° Factura</label>
                            <div class="mt-2">
                                <input id="nfactura" type="text" name="factura" autocomplete="family-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1">
                            </div>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3 tlg:col-span-2">
                            <label for="fecha" class="block text-2xl font-medium text-gray-600">Fecha</label>
                            <div class="mt-2">
                                <input id="fecha" type="date" name="fecha" autocomplete="family-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" required>
                            </div>
                        </div>

                        <div class="sm:col-span-2 md:col-span-2 tlg:col-span-2">
                            <label for="origenPago" class="block text-2xl font-medium text-gray-600">Origen</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="origenPago" name="origen" autocomplete="origen-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" required>
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <option value="0">Caja</option>
                                    <option value="1">Banco</option>
                                </select>
                            </div>
                        </div>

                        <div id="divCaja" class="sm:col-span-2 md:col-span-3 tlg:col-span-2">
                            <label for="origenCaja" class="block text-2xl font-medium text-gray-600">Caja</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="origenCaja" name="origencaja" autocomplete="origencaja-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" required>
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($cajas as $value): ?>
                                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div id="divBanco" class="sm:col-span-2 md:col-span-3 tlg:col-span-2 hidden">
                            <label for="origenBanco" class="block text-2xl font-medium text-gray-600">Banco</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="origenBanco" name="origenbanco" autocomplete="origenbanco-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1">
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($bancos as $value): ?>
                                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg> -->
                            </div>
                        </div>

                        <!--<div class="sm:col-span-2 md:col-span-3 tlg:col-span-2">
                            <label for="formapago" class="block text-2xl font-medium text-gray-600">Forma de pago</label>
                            <div class="mt-2 grid grid-cols-1">
                                <select id="formapago" name="formapago" autocomplete="formapago-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-12 text-xl focus:outline-none focus:ring-1" required>
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <option value="1">Contado</option>
                                    <option value="2">Credito 1 mes</option>
                                    <option value="3">Credito 2 mes</option>
                                    <option value="4">Credito 3 mes</option>
                                    <option value="5">Credito 4 mes</option>
                                    <option value="6">Credito 6 mes</option>
                                    <option value="7">Credito 10 mes</option>
                                    <option value="8">Credito 12 mes</option>
                                    <option value="9">Credito 15 mes</option>
                                    <option value="10">Credito 18 mes</option>
                                    <option value="11">Credito 24 mes</option>
                                </select>-->
                                <!-- <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg> -->
                            <!--</div>
                        </div>-->

                        <div class="sm:col-span-8">
                            <label for="observacion" class="block text-2xl font-medium text-gray-600">Observacion</label>
                            <div class="mt-2">
                            <input id="observacion" name="observacion" type="text" autocomplete="observacion ID" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 text-xl focus:outline-none focus:ring-1 h-12">
                            </div>
                        </div>
                    
                    </div>
                </div>

                <div class="mb-6 w-full lg:w-3/4">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fa-solid fa-barcode text-indigo-600 text-3xl"></i>
                            <div>
                                <h3 class="text-2xl font-bold text-indigo-700">
                                    Buscar y agregar productos
                                </h3>

                                <p class="text-base text-slate-500">
                                    Seleccione uno o varios productos para agregarlos a la compra.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" id="btnScanner" class="btn-xs btn-blue whitespace-nowrap">Ctl . Escáner</button>
                            <select
                                id="articulo"
                                name="articulo"
                                autocomplete="articulo-name"
                                class="bg-white border-2 border-indigo-100 shadow-sm text-gray-900 rounded-xl focus:!border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                multiple="multiple"
                            >

                                <?php foreach($totalitems as $value): ?>
                                    <option value="<?php echo $value->id;?>">
                                        <?php echo $value->nombre;?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h5 class="text-xl font-semibold text-slate-700">
                            Productos agregados
                        </h5>

                        <span
                            id="contadorProductos"
                            class="px-4 py-2 rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                            0 productos
                        </span>
                    </div>
                    <table class="tabla bg-white rounded-xl overflow-hidden" width="100%" id="tablaCompras">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>V. Compra</th>
                                <th class="accionesth text-red-500"><i class="fa-solid fa-x"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- productos seleccionados a comprar-->
                            <!--
                            <tr class="">
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Taladro percutor BLACK AND DECKER 550W 1/2</td>
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Metros cubicos</td> 
                                <td class="!px-0 !py-2"><div class="flex justify-center"><button><span class="menos material-symbols-outlined">remove</span></button><input type="text" class=" w-20 px-2 text-center" value="11" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"><button><span class="mas material-symbols-outlined">add</span></button></div></td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56000</td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56880</td>
                                <td class="accionestd"><div class="acciones-btns"><button class="btn-md btn-red eliminarEmpleado"><i class="fa-solid fa-trash-can"></i></button></div></td>
                            </tr>
                            <tr class="">
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Taladro percutor BLACK AND DECKER 550W 1/2</td>
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Metros cubicos</td>
                                <td class="!px-0 !py-2"><div class="flex justify-center"><button><span class="menos material-symbols-outlined">remove</span></button><input type="text" class=" w-20 px-2 text-center" value="11" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"><button><span class="mas material-symbols-outlined">add</span></button></div></td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56000</td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56880</td>
                                <td class="accionestd"><div class="acciones-btns"><button class="btn-md btn-red eliminarEmpleado"><i class="fa-solid fa-trash-can"></i></button></div></td>
                            </tr>
                            <tr class="">
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Atornillador Inalambrico Dewalt 12W de 1/2</td>
                                <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">Metros cubicos</td>
                                <td class="!px-0 !py-2"><div class="flex justify-center"><button><span class="menos material-symbols-outlined">remove</span></button><input type="text" class=" w-20 px-2 text-center" value="11" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"><button><span class="mas material-symbols-outlined">add</span></button></div></td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56000</td>
                                <td class="!p-2 text-xl text-gray-500 leading-5">56880</td>
                                <td class="accionestd"><div class="acciones-btns"><button class="btn-md btn-red eliminarEmpleado"><i class="fa-solid fa-trash-can"></i></button></div></td>
                            </tr>-->
                            <tr id="filaVacia">
                                <td colspan="5" class="py-16 text-center text-slate-400">

                                    <i class="fa-solid fa-box-open text-5xl mb-4 block"></i>

                                    <p class="text-xl">
                                        No hay productos agregados
                                    </p>

                                    <p class="text-base mt-2">
                                        Busca un producto para comenzar la compra
                                    </p>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div> <!-- FIn Apilamiento de productos -->

                <div class="text-right mt-6">
                    <button id="btnvaciar" class="btn-md btn-turquoise !py-4 !px-6 !w-[136px] vaciar" type="button" value="vaciar">Vaciar</button>
                    <input id="btnRegistrarCompra" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[136px] registrarCompra" type="submit" value="Registrar">
                </div>
            </form>

            <!-- INFORMACION FINAL DE LA COMPRA-->
            <div class="mt-2 flex justify-end">
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 min-w-[340px] shadow-sm">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-2xl">Sub Total</span>
                            <span id="subTotal" class="text-slate-700 text-2xl font-medium">$ 0</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-2xl">Impuesto</span>
                            <span id="impuesto" class="text-slate-700 text-2xl font-medium">0%</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-2xl">Descuento</span>
                            <span id="descuento" class="text-slate-700 text-2xl font-medium">$ 0</span>
                        </div>

                        <div class="border-t border-indigo-200 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-800 text-3xl font-bold">
                                    Total
                                </span>

                                <span
                                    id="total"
                                    class="text-indigo-700 text-5xl font-bold"
                                    style="font-family:'Tektur',serif;">
                                    $ 0
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA VACIAR EL CARRITO DE COMPRAS-->
    <dialog class="midialog-xs px-8 pb-8" id="miDialogoVaciar">
        <div>
            <p class="text-2xl font-semibold text-gray-500">Desea vaciar la lista de compra?</p>
        </div>
        <div class="flex justify-around border-t-gray-300 pt-4">
            <div class="sivaciar flex cursor-pointer transition-transform hover:scale-110 text-blue-500 font-semibold"><i class="fa-regular fa-pen-to-square"></i><p class="m-0 ml-1">Si</p></div>
            <div class="novaciar flex cursor-pointer transition-transform hover:scale-110 text-red-500 font-semibold"><i class="fa-regular fa-trash-can"></i><p class="m-0 ml-1">No</p></div>
        </div>
    </dialog>

     <!-- MODAL PARA REGISTRAR LA COMPRA-->
    <dialog class="midialog-xs px-8 pb-8" id="miDialogoRegistrarcompra">
        <div>
            <p class="text-3xl font-semibold text-gray-500">Desea registrar la compra?</p>
            <p class="text-xl text-gray-500">El pedido de compra No: <span id="numOrden"></span> se guardara en sistema.</p>
        </div>

        <div class=" flex-1 mb-4 sm:m-0">
            <label for="imprimirComprobanteCompra" class="flex flex-col items-center cursor-pointer">
                <span class="text-gray-600 mb-4 text-xl">Imprimir comprobante?</span>
                <input
                    id="imprimirComprobanteCompra" 
                    name="imprimirComprobanteCompra"
                    value="1" 
                    type="checkbox" 
                    class="sr-only peer"
                    <?php //echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?>
                    >
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                    <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                </div>
            </label>
        </div>

        <div id="" class="flex justify-around border-t-gray-300 pt-4">
            <div class="sicomprar flex cursor-pointer transition-transform hover:scale-110 text-blue-500 font-semibold"><i class="fa-regular fa-pen-to-square"></i><p class="m-0 ml-1">Si</p></div>
            <div class="nocomprar flex cursor-pointer transition-transform hover:scale-110 text-red-500 font-semibold"><i class="fa-regular fa-trash-can"></i><p class="m-0 ml-1">No</p></div>
        </div>
    </dialog>

</div>