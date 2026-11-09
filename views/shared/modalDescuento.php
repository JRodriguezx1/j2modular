<!-- MODAL PARA AGREGAR DESCUENTO -->
<dialog id="miDialogoDescuento" class="midialog-xs w-[92vw] max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/50">
    <div class="px-7 py-6">
        <div class="mb-5 border-b border-slate-200 pb-5 text-center">
        <div class="mx-auto mb-3 grid h-16 w-16 place-items-center rounded-2xl bg-indigo-100 text-indigo-700">
            <i class="fa-solid fa-percent text-3xl"></i>
        </div>

        <h4 class="text-4xl font-bold leading-tight text-slate-900">Aplicar descuento</h4>
        <p class="mx-auto mt-2 max-w-md text-lg leading-relaxed text-slate-500">Aplica un descuento al subtotal del pedido.</p>
        </div>

        <form id="formDescuento" class="text-center">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <div class="mb-6">
            <h5 class="mb-3 block text-lg font-semibold text-slate-700">Tipo de descuento</h5>

            <div class="inline-flex max-w-full rounded-xl border-[3px] border-indigo-600 bg-white p-1 select-none">
                <label class="flex cursor-pointer">
                <input type="radio" name="tipodescuento" value="valor" class="peer hidden" checked />
                <span class="rounded-lg px-7 py-3 text-lg font-semibold text-slate-700 transition-all duration-200 peer-checked:bg-indigo-600 peer-checked:text-white">
                    Valor
                </span>
                </label>

                <label class="flex cursor-pointer">
                <input type="radio" name="tipodescuento" value="porcentaje" class="peer hidden" />
                <span class="rounded-lg px-7 py-3 text-lg font-semibold text-slate-700 transition-all duration-200 peer-checked:bg-indigo-600 peer-checked:text-white">
                    Porcentaje
                </span>
                </label>
            </div>
            </div>

            <div class="space-y-6">
            <div>
                <label for="inputDescuento" class="mb-2 block text-lg font-semibold text-slate-700">Descuento</label>
                <input
                id="inputDescuento"
                type="number"
                min="0"
                name="descuento"
                data-descuento=""
                class="miles block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                required>
            </div>

            <div>
                <label for="inputDescuentoClave" class="mb-2 block text-lg font-semibold leading-snug text-slate-700">Clave de autorizaci&oacute;n</label>
                <input
                id="inputDescuentoClave"
                type="password"
                name="descuentoclave"
                class="miles block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-100">

                <div id="divmsjalertaClaveDcto"></div>
            </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" class="btn-md btn-turquoise salir w-full !px-6 !py-5 !text-2xl">Salir</button>
            <button id="btnCrearAddDir" type="submit" class="btn-md btn-indigo crearAddDir w-full !px-6 !py-5 !text-2xl">Aplicar</button>
        </div>
        </form>
    </div>
</dialog>