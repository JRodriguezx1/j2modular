interface ActiveOccupation {
    occupationId: number;
    resourceId: number;
    reservationId: number | null;
    clientId: number | null;
    clientName: string | null;
    type: string;
    startDate: string;
    estimatedEndDate: string | null;
    status: string;
    numberOfPeople: number | null;
    observations: string | null;
}

interface ActiveOccupationResponse {
    success: boolean;
    occupation?: ActiveOccupation;
    message?: string;
}


async function fetchActiveOccupation(resourceId: number): Promise<ActiveOccupation>{
    const response = await fetch( `/restaurant/api/occupations/active?resourceId=${resourceId}`,
            { 
                headers: {Accept: 'application/json'} 
            }
        );

    const result = await response.json() as ActiveOccupationResponse;
    if(!response.ok || !result.success || !result.occupation)
        throw new Error(result.message ?? 'No fue posible consultar la atención.');
    return result.occupation;
}


let drawer: HTMLElement;
let overlay: HTMLElement;
let content: HTMLElement;
let actions: HTMLElement;


function openDrawer(): void{
    overlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        drawer.classList.remove('translate-x-full');
    });
    drawer.setAttribute('aria-hidden', 'false');
}


function closeDrawer(): void{
    drawer.classList.add('translate-x-full');
    drawer.setAttribute('aria-hidden','true');
    window.setTimeout(() => {overlay.classList.add('hidden');}, 300);
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

function escapeHtml(value: string): string {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}


function renderOccupation(occupation: ActiveOccupation): void {
    const client = occupation.clientName ? escapeHtml(occupation.clientName) : 'Sin cliente asociado';
    const people = occupation.numberOfPeople !== null ? String(occupation.numberOfPeople) : '—';
    const reservation = occupation.reservationId !== null ? `#${occupation.reservationId}` : 'Atención directa';
    const observations = occupation.observations ? escapeHtml(occupation.observations) : 'Sin observaciones';
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

    document.addEventListener('click', async event => {
        const target = event.target as HTMLElement;
        const card = target.closest<HTMLElement>('[data-active-occupation]');
        if(!card)return;
    
        const resourceId = Number(card.dataset.resourceId);
        if(!resourceId)return;
        
        openDrawer();
        renderLoading();
        actions.classList.add('hidden');
        try {
            const occupation = await fetchActiveOccupation(resourceId);
            renderOccupation(occupation);
        } catch (error) {
            content.innerHTML = `
                <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    ${escapeHtml(error instanceof Error ? error.message : 'Ocurrió un error inesperado.')}
                </div>`;
        }
    });


    drawer.addEventListener('click', event => {
            const target = event.target as HTMLElement;
            if(target.closest('[data-close-active-occupation]'))closeDrawer();    
    });


    overlay.addEventListener('click', closeDrawer);


    document.addEventListener('keydown', event => {
        if(event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false')
            closeDrawer();  
    });
}