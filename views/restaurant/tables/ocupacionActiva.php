<div id="activeOccupationOverlay" class="fixed inset-0 z-40 hidden bg-slate-950/30 backdrop-blur-[1px]"></div>

<aside
    id="activeOccupationDrawer"
    class="fixed right-0 top-0 z-50 flex h-full w-full max-w-md translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    aria-hidden="true"
>
    <header class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4">
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Mesa ocupada</p>
            <h2 class="text-lg font-semibold text-slate-900">Atención activa</h2>
        </div>
        <button
            type="button"
            data-close-active-occupation
            class="flex size-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
    </header>

    <div id="activeOccupationContent" class="min-h-0 flex-1 overflow-y-auto p-5"></div>
    <div id="activeOccupationActions" class=" hidden shrink-0 border-t border-slate-200 bg-white p-4"></div>
</aside>