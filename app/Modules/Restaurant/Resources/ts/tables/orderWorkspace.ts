import { escapeCreateDOMHtml } from '../appFunction';
import { I_Categorias, getAllCategorias, listSimpleProducts } from './catalogoApi';
import {fetchActiveOccupation, getOccupationAccounts} from './occupationApi';
import type {ActiveOccupation, OccupationAccount} from './occupationApi';
import type { ProductPage } from './catalogoApi';
import {resetAccountPanel, showAccountPanel, showAccountPanelError} from './accountPanel';

let drawer: HTMLElement;
let overlay: HTMLElement;
//let content: HTMLElement;
let title: HTMLElement;
let requestVersion = 0;
let catalogRequestVersion = 0;
let hideOverlayTimer: number | undefined;
//let accounts: OccupationAccount[] = [];
//let selectedAccountId: number | null = null;
//variables de paginacion
let catalogCurrentPage = 0;
let catalogSelectedCategory: number | null = null;
let catalogHasMore = false;
let catalogLoading = false;
// Variables de búsqueda
let itemSearch = '';
let catalogSearchTimer: number | undefined;

const atencionMesaText = document.querySelector('#atencionMesaText') as HTMLSpanElement;


function setWorkspaceText(id: string, value: string): void{
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function openDrawer(): void{
    window.clearTimeout(hideOverlayTimer);
    overlay.classList.remove('hidden');
    drawer.inert = false;
    drawer.setAttribute('aria-hidden', 'false');
    requestAnimationFrame(() => drawer.classList.remove('translate-x-full'));
}

export function closeOrderWorkspace(): void{
    if(drawer.getAttribute('aria-hidden') === 'true') return;
    ++requestVersion;
    resetAccountPanel();
    // Quitar el foco si está dentro del drawer
    const activeElement = document.activeElement;
    if(activeElement instanceof HTMLElement && drawer.contains(activeElement))activeElement.blur();

    drawer.classList.add('translate-x-full');
    drawer.inert = true;
    drawer.setAttribute('aria-hidden', 'true');
    hideOverlayTimer = window.setTimeout(() => overlay.classList.add('hidden'), 300);
}

/*function renderWorkspace(occupation: ActiveOccupation): void {
    const selected = accounts.find(account => account.id === selectedAccountId) ?? null;
    const accountOptions = accounts.map(account => `
        <option value="${account.id}" ${account.id === selectedAccountId ? 'selected' : ''}>
            ${escapeCreateDOMHtml(account.name)}
        </option>
    `).join('');


    /*content.innerHTML = `
        <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
            Atención #${occupation.occupationId} · Estado: ${escapeCreateDOMHtml(occupation.status)}
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
                        ${selected ? `Estado: ${escapeCreateDOMHtml(selected.status)} · ${selected.invoiceId === null ? 'Sin orden todavía' : `Orden #${selected.invoiceId}`}` : ''}
                    </p>
                ` : '<p class="mt-5 text-sm text-slate-500">Esta atención no tiene cuentas.</p>'}
                <div class="mt-6 border-t border-slate-200 pt-5 text-sm text-slate-500">
                    El carrito y las acciones de venta se incorporarán en el siguiente paso.
                </div>
            </section>
        </div>`;
}*/

export async function openOrderWorkspace(resourceId: number, tableName: string): Promise<void>{
    if(!resourceId || !drawer)return;
    const currentRequest = ++requestVersion;
    //accounts = [];
    //selectedAccountId = null;
    title.textContent = tableName;
    atencionMesaText.classList.remove('bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-700');
    atencionMesaText.textContent = 'Consultando atención...';
    setWorkspaceText('atencionNoActiva', 'Atención por consultar');
    setWorkspaceText('atencionEstadoResumen', 'Atención por consultar');
    openDrawer();
    try{
        const occupation = await fetchActiveOccupation(resourceId);
        if (currentRequest !== requestVersion) return;
        atencionMesaText.classList.add('bg-emerald-50', 'text-emerald-700');
        atencionMesaText.textContent = 'En atención';
        setWorkspaceText('atencionNoActiva', `Atención #${occupation.occupationId}`);
        setWorkspaceText('atencionEstadoResumen', `Atención #${occupation.occupationId} · En uso`);
        const data = await getOccupationAccounts(occupation.occupationId);
        if (currentRequest !== requestVersion) return;
        void showAccountPanel(occupation, data.accounts);
        //accounts = data.accounts;
        //selectedAccountId = accounts.find(account => account.status === 'abierta')?.id ?? accounts[0]?.id ?? null;
        //atencionMesaText.classList.add('bg-emerald-50', 'text-emerald-700');
        //atencionMesaText.textContent = "En atención";
        //renderWorkspace(occupation);
    }catch(error){
        if (currentRequest !== requestVersion) return;
        const message = error instanceof Error ? error.message : 'No fue posible consultar la mesa.';
        atencionMesaText.classList.remove('bg-emerald-50', 'text-emerald-700');
        atencionMesaText.classList.add('bg-red-50', 'text-red-700');
        //atencionMesaText!.textContent = `${escapeCreateDOMHtml(message)}`;
        showAccountPanelError(message);
    }
}

export function initOrderWorkspace(): void {
    const drawerElement = document.getElementById('orderWorkspaceDrawer');
    const overlayElement = document.getElementById('orderWorkspaceOverlay');
    //const contentElement = document.getElementById('orderWorkspaceContent');
    const titleElement = document.getElementById('orderWorkspaceTitle');
    const btnLoadMore = document.querySelector<HTMLButtonElement>('#catalogLoadMore');
    if(!drawerElement || !overlayElement || !titleElement)return;

    cargaCategorias(); //carga de categorias

    drawer = drawerElement;
    overlay = overlayElement;
    //content = contentElement;
    title = titleElement;
    drawer.inert = true;

    drawer.addEventListener('click', event => {
        if ((event.target as HTMLElement).closest('[data-close-order-workspace]')) closeOrderWorkspace();
    });
    /*drawer.addEventListener('change', event => {
        const select = event.target as HTMLSelectElement;
        if (!select.matches('[data-order-workspace-account]')) return;
        //selectedAccountId = Number(select.value);
        //const selected = accounts.find(account => account.id === selectedAccountId);
        const info = drawer.querySelector<HTMLElement>('[data-order-workspace-account-info]');
        //if(selected && info)
        //    info.textContent = `Estado: ${selected.status} · ${selected.invoiceId === null ? 'Sin orden todavía' : `Orden #${selected.invoiceId}`}`;
    });*/
    overlay.addEventListener('click', closeOrderWorkspace);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') closeOrderWorkspace();
    });

    //evento al input de busqueda
    const searchItem = document.querySelector<HTMLInputElement>('#searchItem');
    searchItem?.addEventListener('input', () => {
        const termino = searchItem.value.trim();
        if(termino === itemSearch)return;
        itemSearch = termino;
         window.clearTimeout(catalogSearchTimer);
        // Invalidar las respuestas anteriores inmediatamente
        ++catalogRequestVersion;
        // Impedir cargar más mientras cambia la búsqueda
        catalogLoading = true;
        //const btnLoadMore = document.querySelector<HTMLButtonElement>('#catalogLoadMore');
        if(btnLoadMore)btnLoadMore.disabled = true;
    
        catalogSearchTimer = window.setTimeout(() => {
            void precargarProductos(catalogSelectedCategory);
        }, 350);
    });

    btnLoadMore?.addEventListener('click', () => { //boton de cargar mas productos.
        void cargarMasProductos();
    });

    void precargarProductos(); //carga de productos
}


async function cargaCategorias(){
    const categorias:I_Categorias[] = await getAllCategorias();
    const divCategorias = document.querySelector('#divCategorias') as HTMLDivElement;

    divCategorias.innerHTML = `
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${categorias.map(c => `
            <button
                type="button"
                data-categoria-id="${escapeCreateDOMHtml(c.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${escapeCreateDOMHtml(c.nombre)}
            </button>
        `).join('')}
    `;

    //evento a los btns de la categoria
    divCategorias.addEventListener('click', event => {
        const button = (event.target as HTMLElement).closest<HTMLButtonElement>('button[data-categoria-id]');
        if(!button || !divCategorias.contains(button))return;

        const rawId = button.dataset.categoriaId;
        const categoriaId = rawId === 'all' ? null : Number(rawId);

        if(categoriaId !== null && (!Number.isInteger(categoriaId) || categoriaId <= 0))return;

        divCategorias.querySelectorAll<HTMLButtonElement>('button[data-categoria-id]')
            .forEach(item => { //detectar boton activado para resaltar
                const active = item === button;
                item.setAttribute('aria-pressed', String(active));
                item.classList.toggle('border-indigo-600', active);
                item.classList.toggle('bg-indigo-600', active);
                item.classList.toggle('text-white', active);
                item.classList.toggle('border-slate-200', !active);
                item.classList.toggle('bg-white', !active);
                item.classList.toggle('text-slate-600', !active);
                item.classList.toggle('hover:bg-slate-50', !active);
            });

        void precargarProductos(categoriaId);
    });
    /*
    let cateogira:string = "";
    categorias.forEach(c => {
        cateogira += `
            <button
                id="idCategoria_${c.id}"
                type="button" 
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${escapeCreateDOMHtml(c.nombre)}
            </button>`;

    });
    divCategorias.innerHTML = cateogira;*/
}


async function cargarMasProductos(): Promise<void>{
    if(catalogLoading || !catalogHasMore)return;
    await cargarPaginaProductos(catalogCurrentPage+1, catalogSelectedCategory, false);
}


async function precargarProductos(categoriaId: number | null = null): Promise<void> {
    catalogSelectedCategory = categoriaId;
    catalogCurrentPage = 0;
    catalogHasMore = false;
    // Invalidar solicitudes anteriores
    ++catalogRequestVersion;
    await cargarPaginaProductos(1, categoriaId, true);
}

async function cargarPaginaProductos(numeroPagina: number, categoriaId: number | null, reemplazar: boolean): Promise<void>{
    const catalogoPaginador = document.querySelector<HTMLDivElement>('#catalogoPaginador');
    const grid = document.querySelector<HTMLDivElement>('#catalogProductGrid');
    const count = document.querySelector<HTMLElement>('#catalogProductCount');
    const status = document.querySelector<HTMLElement>('#catalogProductStatus');
    const btnLoadMore = document.querySelector<HTMLButtonElement>('#catalogLoadMore');

    if(!grid || !count || !status || !btnLoadMore)return;
    const currentRequest = ++catalogRequestVersion;

    catalogLoading = true;
    btnLoadMore.disabled = true;
    btnLoadMore.classList.add('hidden');

    if(reemplazar){
        grid.replaceChildren();
        count.textContent = 'Cargando...';
        status.textContent = 'Cargando productos...';
    }else{
        status.textContent = 'Cargando más productos...';
    }

    try {
        const page: ProductPage = await listSimpleProducts(numeroPagina, categoriaId, itemSearch);
        if(currentRequest !== catalogRequestVersion)return;

        //count.textContent = `${page.products.length} en esta página`;
        //status.textContent = page.products.length > 0 ? `Mostrando página ${page.page}` : 'No hay productos vendibles en esta sucursal.';

        const html = page.products.map(product => {
            const name = escapeCreateDOMHtml(product.nombre);
            const photo = product.foto && product.foto !== 'null' && product.foto !== 'undefined' ? product.foto : null;
            const price = Number(product.precioVenta);
            const priceText = price.toLocaleString();

            return `
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${photo!=null?'':'grid place-items-center'}">
                        ${photo!=null
                            ? `<img src="/build/img/${escapeCreateDOMHtml(photo)}" alt="${name}" loading="lazy" class="h-full w-full object-cover">`
                            : '<span class=" material-symbols-outlined text-5xl">restaurant</span>'
                        }
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${name}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${escapeCreateDOMHtml(priceText)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${name}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`;
        }).join('');

        if(reemplazar){
            grid.innerHTML = html;
            catalogoPaginador?.scrollTo({ top: 0 });
        }else{
            grid.insertAdjacentHTML('beforeend', html);
        }

        catalogCurrentPage = page.page;
        catalogHasMore = page.hasMore;

        /*const totalMostrados = grid.querySelectorAll('article').length;
        count.textContent = `${totalMostrados} productos mostrados`;
        status.textContent = totalMostrados > 0 ? `Mostrando ${totalMostrados} productos` : 'No hay productos vendibles en esta sucursal.';
        */
        //const totalMostrados = grid.children.length;
        count.textContent = `${page.products.length} en esta página`;
        status.textContent = page.products.length > 0 ? `Mostrando página ${page.page}` : 'No hay productos vendibles en esta sucursal.';
        
        btnLoadMore.classList.toggle('hidden', !catalogHasMore);
    }catch(error){
        if(currentRequest !== catalogRequestVersion)return;
        if(reemplazar)count.textContent = 'Sin cargar';
        status.textContent = error instanceof Error ? error.message : 'No fue posible cargar los productos.';
        // Permitir reintentar la página fallida
        if(!reemplazar)btnLoadMore.classList.remove('hidden');
    }finally{
        if (currentRequest === catalogRequestVersion) {
            catalogLoading = false;
            btnLoadMore.disabled = false;
        }
    }
}