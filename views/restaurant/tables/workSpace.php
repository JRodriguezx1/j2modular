<div id="orderWorkspaceOverlay" class="fixed inset-0 z-[60] hidden bg-slate-950/40"></div>
<aside 
    id="orderWorkspaceDrawer"
    class="fixed right-0 top-0 z-[70] flex h-full w-full max-w-6xl translate-x-full flex-col bg-slate-50 shadow-2xl transition-transform duration-300"
    role="dialog" aria-modal="true" aria-labelledby="orderWorkspaceTitle" aria-hidden="true">
    <header class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Operación de mesa</p>
            <h2 id="orderWorkspaceTitle" class="text-xl font-semibold text-slate-900">Mesa <span id="atencionNoActiva"></span></h2>
        </div>
        <button type="button" data-close-order-workspace aria-label="Cerrar operación de mesa" class="flex size-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
    </header>
    <div id="orderWorkspaceContent" class="min-h-0 flex-1 overflow-y-auto p-5">
        
        <div id="atencionEstado" class="mb-5 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
            Atención #${occupation.occupationId} · Estado: ${escapeHtml(occupation.status)}
        </div>
        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            <section class="min-h-[320px] rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-lg font-semibold text-slate-900">Catálogo de productos</h3>
                <p class="mt-3 text-sm text-slate-500">
                    Aquí se incorporarán la búsqueda, las categorías y los filtros de productos.
                </p>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-lg font-semibold text-slate-900">Cuenta y carrito</h3>
                <!--${accounts.length > 0 ? `
                    <label for="orderWorkspaceAccount" class="mt-5 block text-sm font-medium text-slate-700">Cuenta de destino</label>
                    <select id="orderWorkspaceAccount" data-order-workspace-account class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                        ${accountOptions}
                    </select>
                    <p data-order-workspace-account-info class="mt-3 text-sm text-slate-500">
                        ${selected ? `Estado: ${escapeHtml(selected.status)} · ${selected.invoiceId === null ? 'Sin orden todavía' : `Orden #${selected.invoiceId}`}` : ''}
                    </p>
                ` : '<p class="mt-5 text-sm text-slate-500">Esta atención no tiene cuentas.</p>'}-->
                <div class="mt-6 border-t border-slate-200 pt-5 text-sm text-slate-500">
                    El carrito y las acciones de venta se incorporarán en el siguiente paso.
                </div>
            </section>
        </div>
        
    </div>
</aside>