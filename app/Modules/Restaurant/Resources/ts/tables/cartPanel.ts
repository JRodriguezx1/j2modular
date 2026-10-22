import type {CartItem} from './cart';
import {escapeCreateDOMHtml, setElementText} from '../appFunction';

// Estos formateadores solo preparan los números para mostrarlos.
const currencyFormatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
});

const quantityFormatter = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 2 });

// Recibe los productos pendientes y actualiza su sección en la vista.
// Esta función no modifica el carrito.
export function renderCartPanel(items: CartItem[]): void{
    const container = document.getElementById('accountPanelPendingProducts');
    if(!container)return;

    // Contamos líneas: un producto con cantidad 3 sigue siendo una línea.
    const lines = items.length;
    setElementText('accountPanelPendingCount', `${lines} ${lines === 1 ? 'ítem' : 'ítems'} sin guardar`);
    // El cálculo del importe pendiente se incorporará después.

    // Así evitamos mostrar $0 cuando ya existen productos.
    setElementText('accountPanelPendingAmount', lines === 0 ? currencyFormatter.format(0) : 'Por calcular');
    // Si el carrito está vacío, mostramos el mensaje inicial.
    if(lines === 0){
        container.innerHTML = `<p class="text-sm text-slate-500">No hay productos nuevos.</p>`;
        return;
    }

    // Generamos una fila por cada producto pendiente.
    container.innerHTML = items.map(item => {
        // Escapamos el nombre porque se insertará dentro de HTML.
        const name = escapeCreateDOMHtml(item.name);
        const quantity = quantityFormatter.format(item.quantity);
        const unitPrice = currencyFormatter.format(item.unitPrice);
        const precioTotalItem = currencyFormatter.format(item.quantity * item.unitPrice);

        /*return `
            <div class="border-b border-slate-100 py-3">
                <div class="flex items-start gap-3">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-sm font-bold text-amber-700">${quantity}</span>
                    <div class="min-w-0 flex-1">
                        <p class="m-0 text-base font-semibold text-slate-900">${name}</p>
                        <p class="mt-0.5 mb-0 text-sm text-slate-500">${unitPrice} c/u</p>
                        <span class="mt-1 inline-block text-lg font-medium text-amber-700">Pendiente de guardar</span>
                    </div>
                    <span class="text-lg font-semibold text-slate-900">$${totalItem.toLocaleString()}</span>
                </div>
            </div>`;*/
            return `
                <div data-cart-product-id="${item.productId}" class="rounded-xl border border-slate-200 bg-white p-4">
                    <!-- Encabezado -->
                    <div class="flex items-center justify-between gap-3">
                        <p class="nombreproducto text-xl font-semibold text-slate-800 leading-7 break-words">${name}</p>
                        <button data-cart-action="remove" class="eliminarProducto w-9 h-9 rounded-lg border border-red-200 bg-red-50 text-red-500 transition-all duration-300 hover:bg-red-600 hover:text-white  hover:border-red-600 hover:shadow-md">
                            <i class="fa-solid fa-trash-can text-base"></i>
                        </button>
                    </div>

                    <!-- Detalles -->
                    <div class="mt-1 flex items-end justify-between gap-3">
                        <!-- Cantidad -->
                        <div class="flex flex-col gap-1">
                            <span class="text-xl font-bold uppercase text-slate-500">Cantidad</span>
                            <div class="flex items-center gap-1">
                                <button data-cart-action="decrease" type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="menos material-symbols-outlined text-base">remove</span></button>
                                <input
                                    data-cart-action="quantity"
                                    inputmode="decimal"
                                    type="text"
                                    class="inputcantidad w-16 max-w-[12ch] h-9 px-2 rounded-lg border border-slate-300 text-center font-medium text-xl outline-none focus:border-indigo-500"
                                    value="${quantity}"
                                >
                                <button data-cart-action="increase" type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="mas material-symbols-outlined text-base">add</span></button>
                            </div>
                        </div>
                        <!-- Precios -->
                        <div class="flex flex-col items-end gap-1 text-right">
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-bold uppercase text-slate-500">Unitario:</span>
                                <span class="text-xl font-medium text-slate-500">${unitPrice}</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-bold uppercase text-slate-500">Total</span>
                                <span class="text-xl font-bold text-slate-900">$${precioTotalItem}</span>
                            </div>
                        </div>
                    </div>
                </div>`;
        
    }).join('');
}