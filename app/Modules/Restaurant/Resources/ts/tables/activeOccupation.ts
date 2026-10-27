import { escapeCreateDOMHtml } from '../appFunction';
import {fetchActiveOccupation, getOccupationAccounts} from './occupationApi';
import type {ActiveOccupation, OccupationAccount} from './occupationApi';


let drawer: HTMLElement;
let overlay: HTMLElement;
let content: HTMLElement;
let actions: HTMLElement;
let hideOverlayTimer: number | undefined;


function openDrawer(): void{
    window.clearTimeout(hideOverlayTimer);
    overlay.classList.remove('hidden');
    drawer.inert = false;
    requestAnimationFrame(() => {
        drawer.classList.remove('translate-x-full');
    });
    drawer.setAttribute('aria-hidden', 'false');
}


export function closeActiveOccupation(): void{
    if(drawer.getAttribute('aria-hidden') === 'true')return;
    ++requestVersion;
    drawer.classList.add('translate-x-full');
    drawer.setAttribute('aria-hidden','true');
    drawer.inert = true;
    hideOverlayTimer = window.setTimeout(() => {overlay.classList.add('hidden');}, 300);
}


function renderLoading(): void{
    content.innerHTML = `
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atención...</span>
            </div>
        </div>`;
}


function renderOccupation(occupation: ActiveOccupation): void {
    const client = occupation.client?.id ? escapeCreateDOMHtml(occupation.client.nombre) : 'Sin cliente asociado';
    const people = occupation.numberOfPeople !== null ? String(occupation.numberOfPeople) : '—';
    const reservation = occupation.reservationId !== null ? `#${occupation.reservationId}` : 'Atención directa';
    const observations = occupation.observations ? escapeCreateDOMHtml(occupation.observations) : 'Sin observaciones';
    content.innerHTML = `
        <div class="space-y-6">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600">
                        restaurant
                    </span>

                    <div>
                        <p class="text-xs font-medium uppercase text-emerald-700">
                            Estado
                        </p>
                        <p class="font-semibold text-emerald-900">
                            Atención en curso
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                ${detailRow('Cliente', client)}
                ${detailRow('Personas', people)}
                ${detailRow('Origen', reservation)}
                ${detailRow('Inicio', formatDateTime(occupation.startDate))}
                ${detailRow('Fin estimado', occupation.estimatedEndDate ? formatDateTime(occupation.estimatedEndDate) : '—')}
                ${detailRow('Observaciones', observations)}
            </div>
        </div>`;
}


function detailRow(label: string, value: string): string {
    return `
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${label}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${value}
            </p>
        </div>`;
}


function formatDateTime(value: string): string{
    const date = new Date(value.replace(' ', 'T'));
    if(Number.isNaN(date.getTime()))
        return value;
    return new Intl.DateTimeFormat('es', { dateStyle: 'medium', timeStyle: 'short'}).format(date);
}


function renderAccounts(accounts: OccupationAccount[]): void{
    const section = document.createElement('section');
    section.className = 'mt-8 border-t border-slate-200 pt-5';
    section.innerHTML = `
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${accounts.length === 0
            ? '<p class="text-sm text-slate-500">Esta atención no tiene cuentas.</p>'
            : accounts.map(account => `
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${escapeCreateDOMHtml(account.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${escapeCreateDOMHtml(account.status)} · ${account.invoiceId === null ? 'Sin orden todavía' : `Orden #${account.invoiceId}`}
                    </p>
                </article>
            `).join('')}`;
    content.appendChild(section);
}

function renderAccountsError(message: string): void{
    const error = document.createElement('p');
    error.className = 'mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700';
    error.textContent = message;
    content.appendChild(error);
}


let requestVersion = 0;

export function initActiveOccupation(): void{
    const drawerElement = document.getElementById('activeOccupationDrawer');
    const overlayElement = document.getElementById('activeOccupationOverlay');
    const contentElement = document.getElementById('activeOccupationContent');
    const actionsElement = document.getElementById('activeOccupationActions');

    if(!drawerElement || !overlayElement || !contentElement || !actionsElement)
        return;
    
    drawer = drawerElement;
    overlay = overlayElement;
    content = contentElement;
    actions = actionsElement;
    drawer.inert = true;

    drawer.addEventListener('click', event => {
        const target = event.target as HTMLElement;
        if(target.closest('[data-close-active-occupation]'))closeActiveOccupation();
    });

    overlay.addEventListener('click', closeActiveOccupation);

    document.addEventListener('keydown', event => {
        if(event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false')
            closeActiveOccupation();
    });
}


export async function openActiveOccupationDetails(resourceId: number): Promise<void>{
    if(!resourceId)return;
    const currentRequest = ++requestVersion;

    openDrawer();
    renderLoading();
    actions.classList.add('hidden');
    try{
        const occupation = await fetchActiveOccupation(resourceId);
        if(currentRequest !== requestVersion)return;
        if(occupation === null){
            content.textContent = 'La mesa no tiene una atención activa.';
            return;
        }
        renderOccupation(occupation);

        try{
            const data = await getOccupationAccounts(occupation.occupationId);
            if(currentRequest !== requestVersion)return;
            renderAccounts(data.accounts);
        }catch(error){
            if(currentRequest !== requestVersion)return;
            renderAccountsError(error instanceof Error ? error.message : 'No fue posible consultar las cuentas.');
        }

    }catch(error){
        if(currentRequest !== requestVersion)return;
        content.innerHTML = `
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${escapeCreateDOMHtml(error instanceof Error ? error.message : 'Ocurrió un error inesperado.')}
            </div>`;
    }
}
