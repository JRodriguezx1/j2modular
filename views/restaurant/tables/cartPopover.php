<div id="cart-popover" class="fixed z-40 w-72 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg" hidden>
    <!-- Descuento -->
    <button
        id="btnModalDescuento"
        type="button"
        data-action="discount"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-amber-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-100">
            <i class="fa-solid fa-percent"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-slate-700">Aplicar descuento</span>
            <span class="text-sm text-slate-500">Modificar precio</span>
        </span>
    </button>

    <!-- Cortesía -->
    <button
        id="btnModalCortesia"
        type="button"
        data-action="courtesy"
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

    <!-- Cancelar -->
    <button
        id="btnModalEliminarItem"
        type="button"
        data-action="cancel"
        class="group flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left transition-colors duration-150 hover:bg-rose-50"
    >
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500 group-hover:bg-rose-100">
           <i class="fa-regular fa-trash-can"></i>
        </span>
        <span class="flex flex-col gap-0.5">
            <span class="text-lg font-semibold text-rose-600">Cancelar producto</span>
            <span class="text-sm text-slate-500">Retirar de la cuenta</span>
        </span>
    </button>
</div>