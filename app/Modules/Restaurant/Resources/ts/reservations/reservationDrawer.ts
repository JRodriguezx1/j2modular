import type {AvailableTable, ReservationsPageData} from './types';
import {findAvailableTables} from './availability';

export function initReservationDrawer(pageData: ReservationsPageData):void{

    const btnOpen = document.querySelector<HTMLButtonElement>('#btnNewReservation');
    const btnClose = document.querySelector<HTMLButtonElement>('#btnCloseReservationDrawer');
    const btnCancel = document.querySelector<HTMLButtonElement>('#btnCancelReservation');
    const drawer = document.querySelector<HTMLElement>('#reservationDrawer');
    const overlay = document.querySelector<HTMLElement>('#reservationDrawerOverlay');
    const dateInput = document.querySelector<HTMLInputElement>('#reservationDate');
    const startTimeInput = document.querySelector<HTMLInputElement>('#reservationStartTime');
    const endTimeInput = document.querySelector<HTMLInputElement>('#reservationEndTime');
    const peopleInput = document.querySelector<HTMLInputElement>('#reservationPeople');
    const peopleValue = document.querySelector<HTMLElement>('#reservationPeopleValue');
    const btnIncrease = document.querySelector<HTMLButtonElement>('#btnIncreasePeople');
    const btnDecrease = document.querySelector<HTMLButtonElement>('#btnDecreasePeople');
    const tablesContainer = document.querySelector<HTMLElement>('#availableTables');
    const tablesCounter = document.querySelector<HTMLElement>('#availableTablesCounter');
    const resourceInput = document.querySelector<HTMLInputElement>('#reservationResourceId');

    if(!btnOpen || !drawer || !overlay || !dateInput || !startTimeInput || !endTimeInput || !peopleInput || !peopleValue || !tablesContainer || !tablesCounter || !resourceInput)
        return;

    function openDrawer(): void {
        overlay?.classList.remove('hidden');
        requestAnimationFrame(() => { drawer?.classList.remove('translate-x-full'); });
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer(): void{
        drawer?.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        window.setTimeout(() => {
            overlay?.classList.add('hidden');
        }, 300);
    }


    const updatePeople = (value: number): void => {
        const people = Math.max(1, value);
        peopleInput.value = String(people);
        peopleValue.textContent = String(people);
        loadAvailability();
    }


    /**/

    function buildDateTime(date: string, time: string): string{
        return `${date} ${time}:00`;
    }


    const loadAvailability = async():Promise<void> => {
        const date = dateInput?.value;
        const startTime = startTimeInput?.value;
        const endTime = endTimeInput?.value;
        const numberOfPeople = Number(peopleInput?.value);

        /*
         * Una disponibilidad anterior deja
         * de ser válida cuando cambia algún
         * criterio.
         */
        resourceInput.value = '';

        if(!date || !startTime || !endTime || numberOfPeople < 1){
            renderInitialState();
            return;
        }

        renderLoading();
        try{
            const tables = await findAvailableTables({
                    startDate: buildDateTime(date, startTime),
                    endDate: buildDateTime(date, endTime),
                    numberOfPeople
            });
            renderTables(tables);
        }catch (error){
            if(error instanceof DOMException && error.name === 'AbortError')return;
            renderError(error instanceof Error ? error.message : 'No fue posible consultar disponibilidad.');
        }
    }


    /** */

    const renderInitialState = ():void=>{
        tablesCounter.textContent = '';
        tablesContainer.innerHTML = `
            <div class="flex min-h-[70px] flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-slate-400">table_restaurant</span>
                <p class="mt-1 text-base text-slate-400">Selecciona fecha y horario.</p>
            </div>`;
    }


    const renderLoading = ():void =>{
        tablesCounter.textContent = 'Consultando...';
        tablesContainer.innerHTML = `
            <div class="flex min-h-[70px] items-center justify-center gap-2 text-xs text-slate-400">
                <span class="material-symbols-outlined animate-spin text-3xl">progress_activity</span>
                Buscando mesas disponibles...
            </div>`;
    }


    const renderError = (message: string):void =>{
        tablesCounter.textContent = '';
        tablesContainer.innerHTML = '';
        const wrapper = document.createElement('div');

        wrapper.className =
            'flex min-h-[70px] items-center ' +
            'justify-center gap-2 text-center ' +
            'text-base text-rose-500';

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined text-[18px]';
        icon.textContent = 'error';
        const text = document.createElement('span');
        /*
         * textContent evita inyectar el mensaje
         * recibido como HTML.
         */
        text.textContent = message;
        wrapper.append(icon, text);
        tablesContainer?.append(wrapper);
    }


    /** */
    function getZoneName(zoneId: number | null): string{
        if(zoneId === null)return 'Sin zona';
        const zone = pageData.zones.find(item => Number(item.id) === Number(zoneId));
        return zone?.name ?? 'Sin zona';
    }


    const renderTables = (tables: AvailableTable[]):void =>{
        tablesContainer.innerHTML = '';
        resourceInput.value = '';
        tablesCounter.textContent = `${tables.length} ${tables.length === 1 ? 'disponible' : 'disponibles'}`;

        if(tables.length === 0){
            const message = document.createElement('div');
            message.className = 'flex min-h-[80px] flex-col ' + 'items-center justify-center text-center';
            message.innerHTML = `
                <span class="material-symbols-outlined text-[22px] text-amber-400">event_busy</span>
                <p class="mt-1 text-xs font-medium text-slate-600">No hay mesas disponibles</p>
                <p class="mt-0.5 text-[11px] text-slate-400">Puedes guardar la reserva sin asignar mesa.</p>`;

            tablesContainer.append(message);

            return;
        }

        const grouped = new Map<string, AvailableTable[]>();

        for(const table of tables){
            const zoneName = getZoneName( table.zoneId);
            const current = grouped.get(zoneName) ?? [];
            current.push(table);
            grouped.set(zoneName, current);
        }

        for(const [zoneName, zoneTables] of grouped){
            renderZone(zoneName, zoneTables);
        }
    }

    /** */
    function renderZone(zoneName: string, tables: AvailableTable[]): void {
        const section = document.createElement('div');
        section.className = 'mb-4 last:mb-0';
        const title = document.createElement('p');
        title.className = 'mb-2 text-xl font-bold ' + 'uppercase tracking-[0.12em] ' + 'text-slate-500';
        title.textContent = zoneName;
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2';
        for(const table of tables){
            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.resourceId = String(table.resourceId);
            button.className = 'rounded-xl border border-slate-200 ' + 'bg-white px-3 py-2.5 text-left ' + 'transition hover:border-indigo-300 ' + 'hover:bg-indigo-50/40';
            const name = document.createElement('span');
            name.className = 'block truncate text-lg ' + 'font-bold text-slate-700';
            name.textContent = table.name;
            const capacity = document.createElement('span');
            capacity.className = 'mt-0.5 block text-xl ' + 'text-slate-400';
            capacity.textContent = `${table.capacity} personas`;
            button.append(name, capacity);
            button.addEventListener('click', () => { selectTable(button, table.resourceId);});

            grid.append(button);
        }

        section.append(title, grid);
        tablesContainer?.append(section);
    }


    /** */
    const selectTable = (selectedButton: HTMLButtonElement, resourceId: number):void =>{
        tablesContainer.querySelectorAll<HTMLButtonElement>('button[data-resource-id]').forEach(button => {
                button.classList.remove('border-indigo-500', 'bg-indigo-50', 'ring-1', 'ring-indigo-500');
        });

        selectedButton.classList.add('border-indigo-500', 'bg-indigo-50', 'ring-1', 'ring-indigo-500');
        resourceInput.value = String(resourceId);
    }


    /** */
    btnOpen.addEventListener('click', openDrawer);
    btnClose?.addEventListener('click', closeDrawer);
    btnCancel?.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    btnIncrease?.addEventListener('click',() => {
        updatePeople(Number(peopleInput.value) + 1);
    });

    btnDecrease?.addEventListener('click', () => {
        updatePeople(Number(peopleInput.value) - 1);
    });

    dateInput.addEventListener('change', loadAvailability);
    startTimeInput.addEventListener('change', loadAvailability);
    endTimeInput.addEventListener('change', loadAvailability);

    document.addEventListener('keydown', event => {
            if(event.key === 'Escape' && !overlay.classList.contains('hidden'))closeDrawer();
    });

}