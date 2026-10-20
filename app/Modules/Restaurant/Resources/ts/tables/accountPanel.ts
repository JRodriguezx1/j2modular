import {getAccountSummary} from './accountApi';
import type {AccountAmount, AccountConsumption, AccountSummary} from './accountApi';
import type {ActiveOccupation, OccupationAccount} from './occupationApi';

let requestVersion = 0;
let activeRequest: AbortController | null = null;

const currencyFormatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
});
const quantityFormatter = new Intl.NumberFormat('es-CO', {maximumFractionDigits: 2});

function setText(id: string, value: string): void{
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function setStatus(message: string): void{
    const status = document.getElementById('accountPanelStatus');
    if (!status) return;
    status.textContent = message;
    status.classList.toggle('hidden', message === '');
}

function setEmptyMessage(containerId: string, message: string): void{
    const container = document.getElementById(containerId);
    if (!container) return;
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
    if (consumptions.length === 0) {
        setEmptyMessage('accountPanelConsumptions', 'Sin productos registrados.');
        return;
    }

    const container = document.getElementById('accountPanelConsumptions');
    const template = document.getElementById('accountConsumptionTemplate');
    if (!container || !(template instanceof HTMLTemplateElement))
        throw new Error('No se encontró la vista de productos registrados.');

    const fragment = document.createDocumentFragment();

    for (const consumption of consumptions) {
        const row = template.content.firstElementChild?.cloneNode(true);
        if (!(row instanceof HTMLElement))
            throw new Error('La vista de productos registrados no es válida.');

        const quantity = row.querySelector<HTMLElement>('[data-consumption-quantity]');
        const name = row.querySelector<HTMLElement>('[data-consumption-name]');
        const unitPrice = row.querySelector<HTMLElement>('[data-consumption-unit-price]');
        const total = row.querySelector<HTMLElement>('[data-consumption-total]');
        if (!quantity || !name || !unitPrice || !total)
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
    setText('accountPanelName', summary.name);
    const units = formatQuantity(summary.totales.totalunidades);
    setText('accountPanelProductCount', `${units} ${units === '1' ? 'producto' : 'productos'}`);
    const lines = summary.consumos.length;
    setText('accountPanelRegisteredCount', `${lines} ${lines === 1 ? 'ítem' : 'ítems'}`);
    renderConsumptions(summary.consumos);

    // El precio del POS incluye el impuesto; el total ya es el importe guardado.
    const registeredTotal = formatCurrency(summary.totales.total);
    setText('accountPanelRegisteredAmount', registeredTotal);
    setText('accountPanelDiscount', formatCurrency(summary.totales.descuento));
    setText('accountPanelTax', `${formatCurrency(summary.totales.valorimpuestototal)} incluidos`);
    setText('accountPanelTotal', registeredTotal);
    setText('accountPanelPayTotal', registeredTotal);
    setStatus(summary.consumos.length === 0 ? 'Cuenta abierta sin productos registrados.' : '');
}

export function resetAccountPanel(): void {
    ++requestVersion;
    activeRequest?.abort();
    activeRequest = null;
    document.getElementById('accountPanel')?.setAttribute('aria-busy', 'false');
    setText('atencionEstadoCuenta', 'Cuenta por consultar');
    setText('accountPanelOccupation', 'Atención por consultar');
    setText('accountPanelName', 'Cuenta por consultar');
    setText('accountPanelClientName', 'Por consultar');
    setText('accountPanelProductCount', '— productos');
    setText('accountPanelRegisteredCount', '— ítems');
    setText('accountPanelRegisteredAmount', '—');
    setText('accountPanelPendingCount', '0 sin guardar');
    setText('accountPanelPendingAmount', formatCurrency(0));
    setText('accountPanelDiscount', '—');
    setText('accountPanelTax', '—');
    setText('accountPanelTotal', '—');
    setText('accountPanelPayTotal', '—');
    setEmptyMessage('accountPanelConsumptions', 'Los productos registrados aparecerán aquí.');
    setEmptyMessage('accountPanelPendingProducts', 'No hay productos nuevos.');
    setStatus('Cuenta por consultar.');
}


export function showAccountPanelError(message: string): void{
    resetAccountPanel();
    setText('atencionEstadoCuenta', 'Cuenta no disponible');
    setStatus(message);
    setEmptyMessage('accountPanelConsumptions', 'No fue posible consultar los productos registrados.');
}


export async function showAccountPanel(occupation: ActiveOccupation, accounts: OccupationAccount[]): Promise<void> {
    resetAccountPanel();
    const currentRequest = requestVersion;
    setText('accountPanelOccupation', `Atención #${occupation.occupationId}`);
    setText('accountPanelClientName', occupation.clientName?.trim() || (occupation.clientId === null ? 'Consumidor final' : 'Cliente por consultar'));

    const openAccounts = accounts.filter(account =>
        account.occupationId === occupation.occupationId && account.status === 'abierta');
    if(openAccounts.length === 0){
        setText('atencionEstadoCuenta', 'Sin cuenta abierta');
        setStatus('Esta atención aún no tiene una cuenta abierta.');
        setEmptyMessage('accountPanelConsumptions', 'Sin cuenta para consultar productos.');
        return;
    }
    if(openAccounts.length > 1){
        setText('atencionEstadoCuenta', 'Varias cuentas abiertas');
        setStatus('Esta atención tiene varias cuentas abiertas. La selección de cuenta está pendiente.');
        setEmptyMessage('accountPanelConsumptions', 'Selecciona una cuenta cuando esta opción esté disponible.');
        return;
    }

    const account = openAccounts[0];
    setText('atencionEstadoCuenta', 'Cuenta abierta');
    setText('accountPanelName', account.name);
    setStatus('Consultando productos y total de la cuenta...');
    setEmptyMessage('accountPanelConsumptions', 'Cargando productos registrados...');
    document.getElementById('accountPanel')?.setAttribute('aria-busy', 'true');
    const controller = new AbortController();
    activeRequest = controller;

    try{
        const summary = await getAccountSummary(account.id, controller.signal);
        if(currentRequest !== requestVersion)return;
        if(summary.accountId !== account.id || summary.occupationId !== occupation.occupationId)
            throw new Error('El resumen recibido no corresponde a esta cuenta.');
        renderSummary(summary);
    }catch(error){
        if(currentRequest !== requestVersion || controller.signal.aborted)return;
        setStatus(error instanceof Error ? error.message : 'No fue posible consultar la cuenta.');
        setEmptyMessage('accountPanelConsumptions', 'No fue posible consultar los productos registrados.');
    }finally{
        if(currentRequest === requestVersion) {
            activeRequest = null;
            document.getElementById('accountPanel')?.setAttribute('aria-busy', 'false');
        }
    }
}