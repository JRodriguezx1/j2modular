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
        const totalItem = item.quantity*item.unitPrice;

        return `
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
            </div>`;
    }).join('');
}