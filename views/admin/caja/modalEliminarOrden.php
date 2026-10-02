<!-- MODAL PARA ELIMINAR LA ORDEN-->
<dialog class="midialog-sm orden-delete-dialog !p-0 sm:!max-w-[54rem]" id="miDialogoEliminarOrden">
    <div class="relative shrink-0 bg-gradient-to-br from-red-600 via-rose-600 to-indigo-600 px-8 pb-8 pt-7 text-white">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,.16)_0%,rgba(255,255,255,0)_48%)]"></div>

        <div class="relative flex items-start gap-5">
            <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-white/18 ring-1 ring-white/25">
                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
            </div>

            <div class="min-w-0 text-left">
                <p class="mb-1 text-base font-black uppercase tracking-[.24em] text-white/80">
                    Accion irreversible
                </p>

                <h2 class="m-0 text-3xl font-black leading-tight sm:text-4xl">
                    Eliminar orden de venta
                </h2>

                <p class="m-0 mt-2 text-lg font-medium leading-7 text-white/85">
                    Esta accion no se puede deshacer. Confirma cada dato antes de continuar.
                </p>
            </div>
        </div>
    </div>

    <div class="orden-delete-dialog__body overflow-y-auto overscroll-contain px-8 pt-6">
        <div id="divmsjalerta1"></div>

        <div class="mb-4 space-y-5">
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/80 p-5">
                <div class="mb-4 flex items-center gap-3 text-left">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-boxes-stacked text-xl"></i>
                    </span>

                    <div>
                        <h3 class="m-0 text-xl font-black text-slate-900">
                            Inventario
                        </h3>
                        <p class="m-0 mt-1 text-base font-medium text-slate-500">
                            Define si los productos regresan al stock.
                        </p>
                    </div>
                </div>

                <p class="mb-4 text-center text-lg font-semibold text-slate-700">
                    &iquest;Desea devolver los productos al inventario?
                </p>

                <div class="grid grid-cols-2 rounded-2xl border border-indigo-200 bg-white p-1.5 shadow-sm">
                    <label class="flex cursor-pointer">
                        <input type="radio" name="devolverinventario" value="1" class="peer hidden"/>
                        <span class="flex h-14 w-full items-center justify-center rounded-xl px-6 text-lg font-black text-slate-600 transition duration-200 ease-in-out peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-600/20">
                            Si
                        </span>
                    </label>

                    <label class="flex cursor-pointer">
                        <input type="radio" name="devolverinventario" value="0" class="peer hidden" checked />
                        <span class="flex h-14 w-full items-center justify-center rounded-xl px-6 text-lg font-black text-slate-600 transition duration-200 ease-in-out peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-600/20">
                            No
                        </span>
                    </label>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-5">
                <div class="mb-3 flex items-center gap-3 text-left">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-pen-line text-lg"></i>
                    </span>

                    <label
                        for="observacionEliminacion"
                        class="m-0 block text-xl font-black text-slate-800">
                        Motivo de la eliminaci&oacute;n
                    </label>
                </div>

                <textarea
                    id="observacionEliminacion"
                    class="min-h-[11rem] w-full resize-y rounded-2xl border border-slate-300 bg-white p-4 text-lg font-medium leading-7 text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                    name="observacionEliminacion"
                    placeholder="Indique el motivo por el cual se elimina la orden..."
                    rows="4"></textarea>
            </div>

            <div class="rounded-2xl border border-red-100 bg-red-50 p-5">
                <div class="mb-3 flex items-center gap-3 text-left">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-red-600 shadow-sm">
                        <i class="fa-solid fa-lock text-lg"></i>
                    </span>

                    <label
                        for="inputEliminarClave"
                        class="m-0 block text-xl font-black text-red-700">
                        Confirmaci&oacute;n de seguridad
                    </label>
                </div>

                <p class="mb-4 text-left text-base font-medium leading-6 text-slate-500">
                    Ingrese la clave para confirmar la eliminaci&oacute;n de la orden.
                </p>

                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl bg-red-50 text-red-500">
                        <i class="fa-solid fa-key text-base"></i>
                    </span>

                    <input
                        id="inputEliminarClave"
                        type="password"
                        min="0"
                        class="block h-16 w-full rounded-2xl border border-red-200 bg-white py-3 pl-16 pr-4 text-xl font-semibold text-slate-900 placeholder:font-medium placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-100"
                        placeholder="Ingrese la clave"
                        required>
                </div>
            </div>
        </div>

        <div id="productsInv" class="orden-delete-dialog__products mt-6 hidden rounded-2xl border border-slate-200 shadow-sm animate-fadeIn">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h3 class="text-xl font-semibold text-slate-700">
                    Productos a devolver
                </h3>
            </div>

            <table class="w-full text-left text-xl text-gray-500 rtl:text-right">
                <thead class="bg-slate-50 uppercase text-slate-700">
                    <tr>
                        <th scope="col" class="px-6 py-4">
                            Nombre producto
                        </th>

                        <th scope="col" class="px-6 py-4 text-center">
                            Qty
                        </th>

                        <th scope="col" class="px-6 py-4 text-center">
                            Devolver
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($productos as $index=>$value): ?>
                        <tr class="border-t border-slate-100 bg-white transition-colors hover:bg-slate-50">
                            <td class="px-6 py-3 font-medium text-slate-700">
                                <?php echo $value->nombreproducto??'';?>
                            </td>

                            <td class="px-6 py-3 text-center font-semibold text-indigo-600">
                                <?php echo $value->cantidad??0;?>
                            </td>

                            <td class="px-6 py-3" data-qty="<?php echo $value->cantidad??0;?>">
                                <input
                                    id="<?php echo $value->id;?>"
                                    data-idproducto="<?php echo $value->idproducto;?>"
                                    data-nombre="<?php echo $value->nombreproducto??'';?>"
                                    data-tipoproducto="<?php echo $value->tipoproducto;?>"
                                    data-tipoproduccion="<?php echo $value->tipoproduccion;?>"
                                    data-rendimientoestandar="<?php echo $value->rendimientoestandar;?>"
                                    data-promediostock="<?php echo $value->promediostock;?>"
                                    class="inputInv block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-lg text-gray-700 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    type="text"
                                    name=""
                                    value="<?php echo number_format($value->cantidad??0, 2, ',', '.');?>"
                                    oninput="formatearMoneda(this)"
                                    required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="orden-delete-dialog__footer sticky bottom-0 -mx-8 mt-6 flex flex-col-reverse gap-3 border-t border-gray-200 bg-white/95 px-8 pb-1 pt-5 backdrop-blur sm:flex-row sm:justify-end">
            <button
                type="button"
                class="noeliminar inline-flex h-16 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-xl font-black text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 sm:w-[15rem]">
                Cancelar
            </button>

            <button
                type="button"
                class="sieliminar inline-flex h-16 items-center justify-center rounded-xl bg-red-600 px-6 text-xl font-black text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700 sm:w-[15rem]">
                <i class="fa-solid fa-trash mr-2"></i>
                Eliminar
            </button>
        </div>
    </div>
</dialog>
