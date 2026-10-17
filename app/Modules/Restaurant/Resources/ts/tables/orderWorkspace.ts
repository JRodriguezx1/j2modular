import {fetchActiveOccupation, getOccupationAccounts} from './occupationApi';
import type {ActiveOccupation, OccupationAccount} from './occupationApi';

let drawer: HTMLElement;
let overlay: HTMLElement;
let content: HTMLElement;
let title: HTMLElement;
let requestVersion = 0;
let hideOverlayTimer: number | undefined;
let accounts: OccupationAccount[] = [];
let selectedAccountId: number | null = null;

function escapeHtml(value: string): string {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}

function openDrawer(): void {
    window.clearTimeout(hideOverlayTimer);
    overlay.classList.remove('hidden');
    drawer.inert = false;
    requestAnimationFrame(() => drawer.classList.remove('translate-x-full'));
    drawer.setAttribute('aria-hidden', 'false');
}

export function closeOrderWorkspace(): void {
    if (drawer.getAttribute('aria-hidden') === 'true') return;
    ++requestVersion;
    drawer.classList.add('translate-x-full');
    drawer.setAttribute('aria-hidden', 'true');
    drawer.inert = true;
    hideOverlayTimer = window.setTimeout(() => overlay.classList.add('hidden'), 300);
}

function renderWorkspace(occupation: ActiveOccupation): void {
    const selected = accounts.find(account => account.id === selectedAccountId) ?? null;
    const accountOptions = accounts.map(account => `
        <option value="${account.id}" ${account.id === selectedAccountId ? 'selected' : ''}>
            ${escapeHtml(account.name)}
        </option>
    `).join('');


    /*content.innerHTML = `
        <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
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
                ${accounts.length > 0 ? `
                    <label for="orderWorkspaceAccount" class="mt-5 block text-sm font-medium text-slate-700">Cuenta de destino</label>
                    <select id="orderWorkspaceAccount" data-order-workspace-account
                        class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                        ${accountOptions}
                    </select>
                    <p data-order-workspace-account-info class="mt-3 text-sm text-slate-500">
                        ${selected ? `Estado: ${escapeHtml(selected.status)} · ${selected.invoiceId === null ? 'Sin orden todavía' : `Orden #${selected.invoiceId}`}` : ''}
                    </p>
                ` : '<p class="mt-5 text-sm text-slate-500">Esta atención no tiene cuentas.</p>'}
                <div class="mt-6 border-t border-slate-200 pt-5 text-sm text-slate-500">
                    El carrito y las acciones de venta se incorporarán en el siguiente paso.
                </div>
            </section>
        </div>`;*/
}

export async function openOrderWorkspace(resourceId: number, tableName: string): Promise<void> {
    if (!resourceId || !drawer) return;
    const currentRequest = ++requestVersion;
    accounts = [];
    selectedAccountId = null;
    title.textContent = tableName;
    //content.innerHTML = '<p class="text-sm text-slate-500">Cargando atención y cuentas...</p>';
    openDrawer();

    try{
        const occupation = await fetchActiveOccupation(resourceId);
        if (currentRequest !== requestVersion) return;
        const data = await getOccupationAccounts(occupation.occupationId);
        if (currentRequest !== requestVersion) return;
        accounts = data.accounts;
        selectedAccountId = accounts.find(account => account.status === 'abierta')?.id ?? accounts[0]?.id ?? null;
        document.querySelector('#atencionNoActiva')!.textContent = "";
        renderWorkspace(occupation);
    }catch(error){
        if (currentRequest !== requestVersion) return;
        const message = error instanceof Error ? error.message : 'No fue posible consultar la mesa.';
        //content.innerHTML = `<p class="rounded-xl bg-red-50 p-4 text-sm text-red-700">${escapeHtml(message)}</p>`;
        document.querySelector('#atencionNoActiva')!.textContent = `${escapeHtml(message)}`;
    }
}

export function initOrderWorkspace(): void {
    const drawerElement = document.getElementById('orderWorkspaceDrawer');
    const overlayElement = document.getElementById('orderWorkspaceOverlay');
    const contentElement = document.getElementById('orderWorkspaceContent');
    const titleElement = document.getElementById('orderWorkspaceTitle');
    if (!drawerElement || !overlayElement || !contentElement || !titleElement) return;

    drawer = drawerElement;
    overlay = overlayElement;
    content = contentElement;
    title = titleElement;
    drawer.inert = true;

    drawer.addEventListener('click', event => {
        if ((event.target as HTMLElement).closest('[data-close-order-workspace]')) closeOrderWorkspace();
    });
    drawer.addEventListener('change', event => {
        const select = event.target as HTMLSelectElement;
        if (!select.matches('[data-order-workspace-account]')) return;
        selectedAccountId = Number(select.value);
        const selected = accounts.find(account => account.id === selectedAccountId);
        const info = drawer.querySelector<HTMLElement>('[data-order-workspace-account-info]');
        if (selected && info)
            info.textContent = `Estado: ${selected.status} · ${selected.invoiceId === null ? 'Sin orden todavía' : `Orden #${selected.invoiceId}`}`;
    });
    overlay.addEventListener('click', closeOrderWorkspace);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') closeOrderWorkspace();
    });
}
