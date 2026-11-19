<dialog 
    id="miDialogoConfirmar"
    class="rounded-2xl border border-slate-200 w-[94%] max-w-xl p-0 bg-white backdrop:bg-black/45 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out overflow-hidden">

    <div class="px-8 pt-8 pb-6 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 shadow-sm">
            <i class="fa-solid fa-folder-plus text-3xl"></i>
        </div>
        <p class="mb-2 text-3xl font-bold text-slate-900">Confirmar operacion</p>
        <p id="textConfirmacion" class="mb-0 block text-lg font-semibold leading-snug text-slate-700">-</p>
        <p id="textDescripcion" class="mt-0 mx-auto max-w-md text-lg text-slate-600 leading-relaxed">-</p>

        <div>
            <label for="inputConfirmarClave" class="mb-2 block text-lg font-semibold leading-snug text-slate-700">Clave de autorizaci&oacute;n</label>
            <input
                id="inputConfirmarClave"
                type="password"
                name="confirmarClave"
                class="miles block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-100"
            >
            <div id="divmsjalertaConfirmarCortesia"></div>
        </div>
    </div>

    <div class="border-t border-slate-200 bg-slate-50 px-8 py-6">
        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="cancelar inline-flex min-h-[5.2rem] items-center justify-center gap-3 rounded-lg bg-teal-500 px-5 text-xl font-semibold text-white shadow-sm transition hover:bg-teal-600 focus:outline-none focus:ring-4 focus:ring-teal-200">
                <i class="fa-solid fa-file-lines"></i>
                Cancelar
            </button>
            <button type="button" class="confirmar inline-flex min-h-[5.2rem] items-center justify-center gap-3 rounded-lg bg-indigo-700 px-5 text-xl font-semibold text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                <i class="fa-solid fa-truck-ramp-box"></i>
                Confirmar
            </button>
        </div>
    </div>

</dialog>