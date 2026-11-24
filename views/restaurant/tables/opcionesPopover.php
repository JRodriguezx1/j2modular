<div id="opciones-popover" class="fixed z-40 w-72 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg" hidden>
    <!-- Descuento -->
    <button
        id="btnModalDescuentoCuenta"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-amber-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-100">
            <i class="fa-solid fa-percent"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">descuento</span>
            <span class="text-sm text-slate-500">Modificar precio</span>
        </span>
    </button>

    <!-- Cortesía -->
    <button
        id="btnModalCortesiaCuenta"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-indigo-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100">
            <i class="fa-solid fa-gift"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">Cortesía</span>
            <span class="text-sm text-slate-500">Sin costo para el cliente</span>
        </span>
    </button>

    <!-- Anular cambiar mesa -->
    <button
        id="btnModalCambiarMesa"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-emerald-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 group-hover:bg-emerald-100">
           <i class="fa-solid fa-dice-d6"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">Cambiar mesa</span>
            <span class="text-sm text-slate-500">Cambiar de mesa</span>
        </span>
    </button>

    <!-- Anular atencion -->
    <button
        id="btnModalAnularAtencion"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-rose-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500 group-hover:bg-rose-100">
           <i class="fa-regular fa-trash-can"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-rose-600">Anular</span>
            <span class="text-sm text-slate-500">Anular la atencion</span>
        </span>
    </button>

    <!-- Precuenta -->
    <button
        id="btnImprimirPrecuenta"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-cyan-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-500 group-hover:bg-cyan-100">
           <i class="fa-solid fa-file-lines"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">Precuenta</span>
            <span class="text-sm text-slate-500">Imprime la precuenta</span>
        </span>
    </button>

    <!-- Ver detalle -->
    <button
        id="btnVerDetalle"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-indigo-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 group-hover:bg-indigo-100">
           <i class="fa-brands fa-instalod"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">Detalle</span>
            <span class="text-sm text-slate-500">Detalle de la orden</span>
        </span>
    </button>

    <!-- comanda -->
    <button
        id="btnImprimirComanda"
        type="button"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-cyan-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-500 group-hover:bg-cyan-100">
           <i class="fa-solid fa-kitchen-set"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-cyan-600">Comanda</span>
            <span class="text-sm text-slate-500">Imprimir comanda</span>
        </span>
    </button>
</div>