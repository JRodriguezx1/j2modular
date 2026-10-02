<!-- MODAL PARA GUARDAR EL PEDIDO-->
<dialog id="miDialogoGuardar"
    class="rounded-2xl border border-slate-200 w-[94%] max-w-xl p-0 bg-white backdrop:bg-black/45 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out overflow-hidden">

    <div class="px-8 pt-8 pb-6 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 shadow-sm">
            <i class="fa-solid fa-folder-plus text-3xl"></i>
        </div>

        <p class="mb-2 text-3xl font-bold text-slate-900">
            Generar orden
        </p>

        <p class="mx-auto max-w-md text-lg text-slate-600 leading-relaxed">
            La orden No: <span class="font-semibold text-slate-900"><?php echo $num_orden;?></span> se registrar&aacute; en el sistema.
            Podr&aacute; volver a retomarla posteriormente.
        </p>
    </div>

    <div class="border-t border-slate-200 bg-slate-50 px-8 py-6">
        <div class="grid grid-cols-2 gap-4">
            <button class="cotizacion inline-flex min-h-[5.2rem] items-center justify-center gap-3 rounded-lg bg-teal-500 px-5 text-xl font-semibold text-white shadow-sm transition hover:bg-teal-600 focus:outline-none focus:ring-4 focus:ring-teal-200">
                <i class="fa-solid fa-file-lines"></i>
                Cotizaci&oacute;n
            </button>

            <button class="remision inline-flex min-h-[5.2rem] items-center justify-center gap-3 rounded-lg bg-indigo-700 px-5 text-xl font-semibold text-white shadow-sm transition hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                <i class="fa-solid fa-truck-ramp-box"></i>
                Remisi&oacute;n
            </button>
        </div>
    </div>

</dialog>