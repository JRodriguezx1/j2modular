import { setElementText } from '../appFunction';
import {getAccountSummary} from './accountApi';
import type {AccountAmount, AccountConsumption, AccountSummary} from './accountApi';
import type {ActiveOccupation, OccupationAccount} from './occupationApi';

// Cada resultado explica por qué podemos o no activar un carrito.
export type AccountPanelResult =| {status: 'ready', account: OccupationAccount} | {status: 'empty'} | {status: 'selection-required'} | {status: 'closed'} | {status: 'error'} | {status: 'cancelled'};

let requestVersion = 0;
let activeRequest: AbortController | null = null;

const currencyFormatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
});
const quantityFormatter = new Intl.NumberFormat('es-CO', {maximumFractionDigits: 2});

function setStatus(message: string): void{
    const status = document.getElementById('accountPanelStatus');
    if(!status)return;
    status.textContent = message;
    status.classList.toggle('hidden', message === '');
}

function setEmptyMessage(containerId: string, message: string): void{
    const container = document.getElementById(containerId);
    if(!container)return;
    const paragraph = document.createElement('p');
    paragraph.className = 'text-sm text-slate-500';
    paragraph.textContent = message;
    container.replaceChildren(paragraph);
}

function formatCurrency(value: AccountAmount): string{
    const amount = Number(value);
    return Number.isFinite(amount) ? currencyFormatter.format(amount) : '—';
}

function formatQuantity(value: AccountAmount): string{
    const quantity = Number(value);
    return Number.isFinite(quantity) ? quantityFormatter.format(quantity) : '—';
}

function renderConsumptions(consumptions: AccountConsumption[]): void{
    if(consumptions.length === 0){
        setEmptyMessage('accountPanelConsumptions', 'Sin productos registrados.');
        return;
    }

    const container = document.getElementById('accountPanelConsumptions');
    const template = document.getElementById('accountConsumptionTemplate');
    if (!container || !(template instanceof HTMLTemplateElement))
        throw new Error('No se encontró la vista de productos registrados.');

    const fragment = document.createDocumentFragment();

    for(const consumption of consumptions){
        const row = template.content.firstElementChild?.cloneNode(true);
        if(!(row instanceof HTMLElement))
            throw new Error('La vista de productos registrados no es válida.');
        const quantity = row.querySelector<HTMLElement>('[data-consumption-quantity]');
        const name = row.querySelector<HTMLElement>('[data-consumption-name]');
        const unitPrice = row.querySelector<HTMLElement>('[data-consumption-unit-price]');
        const total = row.querySelector<HTMLElement>('[data-consumption-total]');
        if(!quantity || !name || !unitPrice || !total)
            throw new Error('La vista de productos registrados está incompleta.');

        quantity.textContent = formatQuantity(consumption.cantidad);
        name.textContent = consumption.nombreproducto;
        unitPrice.textContent = `${formatCurrency(consumption.valorunidad)} c/u`;
        total.textContent = formatCurrency(consumption.total);
        fragment.append(row);
    }
    container.replaceChildren(fragment);
}

function renderSummary(summary: AccountSummary): void{
    setElementText('accountPanelName', summary.name);
    const units = formatQuantity(summary.totales.totalunidades);
    setElementText('accountPanelRegisteredCount', `${units} ${units === '1' ? 'producto' : 'productos'}`);
    renderConsumptions(summary.consumos); //imprimir productos

    // El precio del POS incluye el impuesto; el total ya es el importe guardado.
    const registeredTotal = formatCurrency(summary.totales.total);
    setElementText('accountPanelRegisteredAmount', registeredTotal);
    setElementText('accountPanelDiscount', formatCurrency(summary.totales.descuento));
    setElementText('accountPanelTax', `${formatCurrency(summary.totales.valorimpuestototal)} incluidos`);
    setElementText('accountPanelTotal', registeredTotal);
    setElementText('accountPanelPayTotal', registeredTotal);
    //setStatus(summary.consumos.length === 0 ? 'Cuenta abierta sin productos registrados.' : '');
}

export function resetAccountPanel(): void{
    ++requestVersion;
    activeRequest?.abort();  // Cancelar la petición anterior controller?.abort();
    activeRequest = null;
    document.getElementById('accountPanel')?.setAttribute('aria-busy', 'false');
    setElementText('atencionNoActiva', 'Atención por consultar');
    setElementText('atencionEstadoResumen', 'Mesa sin atencion');
    setElementText('atencionEstadoCuenta', 'Listo para ordenar');
    setElementText('accountPanelOccupation', 'Atención por consultar');
    setElementText('accountPanelName', 'Cuenta por consultar');
    setElementText('accountPanelClientName', 'Consumidor final');
    setElementText('accountPanelRegisteredCount', '— ítems');  //conteo de items registrados
    setEmptyMessage('accountPanelConsumptions', 'Los productos registrados aparecerán aquí.');
    setElementText('accountPanelPendingCount', '0 item sin guardar');  //conteo de productos nuevos
    setElementText('accountPanelRegisteredAmount', '—');  //monto de productos registrados
    setElementText('accountPanelPendingAmount', formatCurrency(0)); //monto de productos nuevos
    setElementText('accountPanelDiscount', '—');
    setElementText('accountPanelTax', '—');
    setElementText('accountPanelTotal', '—');
    setElementText('accountPanelPayTotal', '—');
    setEmptyMessage('accountPanelPendingProducts', 'No hay productos nuevos.');
    //setStatus('Cuenta por consultar.');
}


export function showAccountPanelError(message: string): void{
    resetAccountPanel();
    setElementText('atencionEstadoCuenta', 'Cuenta no disponible');
    //setStatus(message);
    setEmptyMessage('accountPanelConsumptions', 'No fue posible consultar los productos registrados.');
}

//llamada desdes orderWorkspace.ts
export async function showAccountPanel(occupation: ActiveOccupation, accounts: OccupationAccount[]): Promise<AccountPanelResult>{
    resetAccountPanel();
    const currentRequest = requestVersion;
    setElementText('accountPanelOccupation', `Atención #${occupation.occupationId}`);
    setElementText('accountPanelClientName', (occupation.cliente.nombre+' '+occupation.cliente.apellido)?.trim() || (occupation.cliente.id === null ? 'Seleccionar cliente' : 'Seleccionar cliente'));

    if(accounts.length === 0)return {status: 'empty'};

    const openAccounts = accounts.filter(account => account.occupationId === occupation.occupationId && account.status === 'abierta');
    if(openAccounts.length === 0){ // Existen cuentas, pero ninguna está abierta.
        setElementText('atencionEstadoCuenta', 'Sin cuenta abierta');
        //setStatus('Las cuentas de esta atención están cerradas o canceladas.');
        setEmptyMessage('accountPanelConsumptions', 'Sin cuenta para consultar productos.');
        return {status: 'closed'};
    }
    if(openAccounts.length > 1){
        setElementText('atencionEstadoCuenta', 'Varias cuentas abiertas');
        //setStatus('Esta atención tiene varias cuentas abiertas. La selección de cuenta está pendiente.');
        setEmptyMessage('accountPanelConsumptions', 'Selecciona una cuenta cuando esta opción esté disponible.');
        return {status: 'selection-required'};
    }

    const account = openAccounts[0];  //obtenemos la primera cuenta o cuenta general
    setElementText('atencionEstadoCuenta', 'Cuenta abierta');
    setElementText('accountPanelName', account.name);
    //setStatus('Consultando productos y total de la cuenta...');
    setEmptyMessage('accountPanelConsumptions', 'Cargando productos registrados...');
    document.getElementById('accountPanel')?.setAttribute('aria-busy', 'true');
    
    const controller = new AbortController();  // Crear un controlador nuevo
    activeRequest = controller;
    try{
        const summary = await getAccountSummary(account.id, controller.signal);
        if(currentRequest !== requestVersion)return {status: 'cancelled'};
        if(summary.accountId !== account.id || summary.occupationId !== occupation.occupationId)
            throw new Error('El resumen recibido no corresponde a esta cuenta.');
        renderSummary(summary);
        return {status: 'ready', account}; //retornamos la primera cuenta "general".
    }catch(error){
        if(currentRequest !== requestVersion || controller.signal.aborted)return {status: 'cancelled'};
        //setStatus(error instanceof Error ? error.message : 'No fue posible consultar la cuenta.');
        setEmptyMessage('accountPanelConsumptions', 'No fue posible consultar los productos registrados.');
    }finally{
        if(currentRequest === requestVersion){
            activeRequest = null;
            document.getElementById('accountPanel')?.setAttribute('aria-busy', 'false');
        }
    }
    return {status: 'error'};
}


// Prepara la presentación de un pedido que todavía no tiene cuenta.
// No crea registros en MySQL.
export function showTemporaryAccountPanel(occupation: ActiveOccupation | null): void{
    resetAccountPanel();
    setElementText('atencionNoActiva', 'Sin atención registrada');
    setElementText('atencionEstadoResumen', 'Mesa sin atencion');
    setElementText('atencionEstadoCuenta', 'Listo para ordenar');
    setElementText('accountPanelOccupation', occupation ? `Atención #${occupation.occupationId}` : 'Sin atención registrada');
    setElementText('accountPanelName', 'Cuenta por crear');
    let nameClient = "";
    if(occupation && occupation.client?.nombre)
        nameClient = occupation.client?.nombre + ' ' + occupation.client?.apellido;
    
    setElementText('accountPanelClientName', nameClient.trim() || 'Seleccionar cliente');
    (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-green-100', 'text-green-600');
    (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-gray-100', 'text-gray-700');
    setElementText('accountPanelRegisteredCount', '0 ítems');
    setElementText('accountPanelRegisteredAmount', formatCurrency(0));
    setEmptyMessage('accountPanelConsumptions', 'Sin productos registrados.');
    // El carrito pendiente se mostrará cuando implementemos su renderizado.
}