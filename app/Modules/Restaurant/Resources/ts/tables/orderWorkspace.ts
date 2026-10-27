import { cerrarDialogoExterno, escapeCreateDOMHtml, setElementText } from '../appFunction';
import {createCart} from './cart';
import { I_Categorias, getAllCategorias, listSimpleProducts } from './catalogoApi';
import {fetchActiveOccupation, getOccupationAccounts} from './occupationApi';
import type { ProductPage, SimpleProduct } from './catalogoApi';
import {resetAccountPanel, showAccountPanel, showAccountPanelError, showTemporaryAccountPanel} from './accountPanel';
import {renderCartPanel} from './cartPanel';
import { registerProductEvents } from './eventos/productsEvents';
import { registeraddclienteEvents, renderCli } from './eventos/selectCliEvent';
import { createCli } from './cli';

let drawer: HTMLElement;
let overlay: HTMLElement;
let title: HTMLElement;
let requestVersion = 0;
let catalogRequestVersion = 0;
let hideOverlayTimer: number | undefined;
//variables de paginacion
let catalogCurrentPage = 0;                           // Última página cargada correctamente
let catalogSelectedCategory: number | null = null;    // Categoría seleccionada actualmente
let catalogHasMore = false;                           // Indica si existen más productos
let catalogLoading = false;                           // Evita solicitudes duplicadas
// Variables de búsqueda
let itemSearch = '';
let catalogSearchTimer: number | undefined;

// Obtiene el tipo del objeto que devuelve createCart().
type Cart = ReturnType<typeof createCart>;
type Cli = ReturnType<typeof createCli>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;
const carts = new Map<CartKey, Cart>();
const clientes = new Map<CartKey, Cli>();

// Representa el pedido que estamos preparando en el panel.
let activeOrder: {
    resourceId: number;
    occupationId: number | null;
    accountId: number | null;
    cartKey: CartKey;
    cart: Cart;
    cli: Cli,
} | null = null;


const catalogProducts = new Map<number, SimpleProduct>();

const productosGrid = document.querySelector<HTMLDivElement>('#catalogProductGrid');
const cartContainer = document.getElementById('accountPanelPendingProducts');


// Selecciona el carrito correspondiente al contexto confirmado.
function activateOrder(resourceId: number, occupationId: number|null, accountId: number|null, client:Cliente|null): void{
    let key: CartKey;
    if(accountId !== null){
        key = `account:${accountId}`;
    }else if(occupationId !== null){
        key = `occupation:${occupationId}`;
    }else{
        key = `table:${resourceId}`;
    }

    // Recuperamos el carrito anterior o creamos uno vacío.
    let cart = carts.get(key);
    if(!cart){
        cart = createCart();
        carts.set(key, cart);  //si no hay ocupacion key = table:5
    }

    //recuperamos cliente
    let cli = clientes.get(key);
    if(!cli){
        cli = createCli();
        if(client && client?.id!=='')cli.addCli('', client);
        clientes.set(key, cli);
    }

    activeOrder = {resourceId, occupationId, accountId, cartKey: key, cart, cli};
    renderCartPanel(cart.getItems());  //Muestra el carrito recién creado. Así reaparecen los pendientes al abrir la mesa.
    renderCli(cli.getCli());
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
    activeOrder = null;
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
    activeOrder = null; //Mientras consultamos, no hay un destino habilitado para productos.
    resetAccountPanel();
    title.textContent = tableName;
    openDrawer();
    try{
        const occupation = await fetchActiveOccupation(resourceId); //consulta ocupacion activa

        if(currentRequest !== requestVersion) return;
        // Caso 1: no hay ocupación. Permitimos preparar un borrador.
        if(occupation === null){
            showTemporaryAccountPanel(null);
            activateOrder(resourceId, null, null, null);  //cunado no hay cuenta abierta ni ocupacion activeOrder.cartKey = table:idrecurso = table:4
            return;
        }

        if(occupation.resourceId !== resourceId || occupation.status !== 'en_uso')
            throw new Error('La atención recibida no está activa para esta mesa.');
        
        setElementText('atencionNoActiva', `Atención #${occupation.occupationId}`);
        setElementText('atencionEstadoResumen', `Atención #${occupation.occupationId} · En uso`);
                                                                                                                             //arreglo de cuentas
        const data = await getOccupationAccounts(occupation.occupationId); //{occupationId: number, occupationStatus:string, accounts:[{id:number, occupationId:number, invoiceId:number|null, name:string, status:string}, {}]}
        if(currentRequest !== requestVersion)return;
        // La atención pudo cambiar mientras consultábamos.
        if(data.occupationId !== occupation.occupationId || data.occupationStatus !== 'en_uso' || data.accounts.some(account =>account.occupationId !== occupation.occupationId))
            throw new Error('Las cuentas recibidas no corresponden a una atención activa.');

        //Esperamos a que el panel consulte el resumen y nos devuelva la cuenta.
        const result = await showAccountPanel(occupation, data.accounts); //llamada al panel izquierdo para detalle de cuenta y pasamos arreglo de cuentas.
        // Mientras esperábamos, el usuario pudo cerrar el panel o cambiar de mesa.
        if(currentRequest !== requestVersion)return;

       // Caso 2: existe una cuenta abierta y su resumen fue consultado.
        if(result.status === 'ready'){
            activateOrder(resourceId, occupation.occupationId, result.account.id, occupation.client);
            return;
        }

        // Caso 3: existe la ocupación, pero todavía no tiene ninguna cuenta.
        if(result.status === 'empty'){
            showTemporaryAccountPanel(occupation);
            activateOrder(resourceId, occupation.occupationId, null, occupation.client);
            return;
        }
    }catch(error){
        console.log(error);
        if(currentRequest !== requestVersion)return;
        activeOrder = null;
        showAccountPanelError(error instanceof Error ? error.message : 'No fue posible consultar la mesa.');
    }
}

export function initOrderWorkspace(): void{
    const drawerElement = document.getElementById('orderWorkspaceDrawer');
    const overlayElement = document.getElementById('orderWorkspaceOverlay');
    //const contentElement = document.getElementById('orderWorkspaceContent');
    const titleElement = document.getElementById('orderWorkspaceTitle');
    const btnLoadMore = document.querySelector<HTMLButtonElement>('#catalogLoadMore');
    if(!drawerElement || !overlayElement || !titleElement)return;

    cargaCategorias(); //carga de categorias

    drawer = drawerElement;
    overlay = overlayElement;
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

    document.addEventListener("click", cerrarDialogoExterno);
    
    document.addEventListener('keydown', event => {
        if(event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false')closeOrderWorkspace();
        //(document.querySelector('#miDialogoAddCliente') as HTMLDialogElement)?.close();
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
        catalogSearchTimer = window.setTimeout(() => { void precargarProductos(catalogSelectedCategory); }, 350);
    });

    btnLoadMore?.addEventListener('click', () => { //boton de cargar mas productos.
        void cargarMasProductos();
    });
    
    
    const getActiveOrder = () => activeOrder;  //funcion flecha getActiveOrder que no recibe parametros y devuelve activeOrder, es lo mismo que escribir function getActiveOrder(){return activeOrder;}
    if(productosGrid && cartContainer)
        registerProductEvents({productosGrid, cartContainer, catalogProducts, ActiveOrderE:getActiveOrder, renderCartPanelE:renderCartPanel});

    const getActiveClient = () => activeOrder?.cli ?? null;
    registeraddclienteEvents(getActiveClient, () => requestVersion);  //para abrir modal de clientes y seleccionar.

    void precargarProductos(); //carga de productos
}


// Catalogo de productos, filtros y paginador
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
    const count = document.querySelector<HTMLElement>('#catalogProductCount');
    const status = document.querySelector<HTMLElement>('#catalogProductStatus');
    const btnLoadMore = document.querySelector<HTMLButtonElement>('#catalogLoadMore');

    if(!productosGrid || !count || !status || !btnLoadMore)return;
    const currentRequest = ++catalogRequestVersion;

    catalogLoading = true;
    btnLoadMore.disabled = true;
    btnLoadMore.classList.add('hidden');

    if(reemplazar){
        // Al cambiar la búsqueda o categoría, quitamos el catálogo anterior.
        //catalogProducts.clear();
        productosGrid.replaceChildren();
        count.textContent = 'Cargando...';
        status.textContent = 'Cargando productos...';
    }else{
        status.textContent = 'Cargando más productos...';
    }

    try {
        const page: ProductPage = await listSimpleProducts(numeroPagina, categoriaId, itemSearch);
        if(currentRequest !== catalogRequestVersion)return;

        //Guardamos los datos que después utilizará el clic. Al cargar más, se conservan los productos de las páginas anteriores.
        for(const product of page.products)
            catalogProducts.set(product.id, { ...product, precioVenta: Number(product.precioVenta)}); //mapa idproducto con el producto
//console.log(catalogProducts);
        //count.textContent = `${page.products.length} en esta página`;
        //status.textContent = page.products.length > 0 ? `Mostrando página ${page.page}` : 'No hay productos vendibles en esta sucursal.';

        console.log(page.products);
        const html = page.products.map(product => {
            const name = escapeCreateDOMHtml(product.nombre);
            const photo = product.foto && product.foto !== 'null' && product.foto !== 'undefined' ? product.foto : null;
            const price = Number(product.precioVenta);
            const priceText = price.toLocaleString();

            return `
                <article 
                    data-product-id="${product.id}"
                    class="cursor-pointer group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
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
            productosGrid.innerHTML = html;
            catalogoPaginador?.scrollTo({ top: 0 });
        }else{
            productosGrid.insertAdjacentHTML('beforeend', html);
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