import type { ReservationDetail, ReservationDetailResponse} from './types';


async function fetchReservation(reservationId: number): Promise<ReservationDetail> {
    const response = await fetch(`/restaurant/api/reservations/detail?id=${reservationId}`, {
            headers: { Accept: 'application/json' }
        }
    );

    const result = await response.json() as ReservationDetailResponse;
    if(!response.ok || !result.success || !result.reservation)
        throw new Error(result.message ?? 'No fue posible consultar la reserva.');

    return result.reservation;
}


export function initReservationDetail(): void {
    const drawer = document.querySelector<HTMLElement>('#reservationDetailDrawer');
    const overlay = document.querySelector<HTMLElement>('#reservationDetailOverlay');
    if(!drawer || !overlay)return;
    const closeButtons = drawer.querySelectorAll<HTMLElement>('[data-reservation-detail-close]');

    document.addEventListener('click', async event => {
        const target = event.target as HTMLElement;
        const trigger = target.closest<HTMLElement>('[data-reservation-id]');
        if(!trigger)return;
        const reservationId = Number(trigger.dataset.reservationId);
        if(!reservationId)return;
        await openReservation(reservationId);
    });


    closeButtons.forEach(button => { button.addEventListener('click', closeDrawer);});
    overlay.addEventListener('click', closeDrawer);

    async function openReservation(reservationId: number): Promise<void> {
        openDrawer();
        renderLoading();
        try{
            const reservation = await fetchReservation(reservationId);
            renderReservation(reservation);
            renderActions(reservation);
        }catch(error){
            closeDrawer();
            Swal.fire({
                icon: 'error',
                title: 'No se pudo cargar la reserva',
                text: error instanceof Error ? error.message : 'Ocurrió un error inesperado.'
            });
        }
    }


    const openDrawer = ():void =>{
        overlay.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
    }

    function closeDrawer(): void{
        overlay?.classList.add('hidden');
        drawer?.classList.add('translate-x-full');
    }

    const renderLoading = (): void =>{
        const content = drawer.querySelector<HTMLElement>('#reservationDetailContent');
        if(!content)return;
        content.innerHTML = `
            <div class="flex items-center justify-center py-16">
                <span class="material-symbols-outlined animate-spin">
                    progress_activity
                </span>
                <span class="ml-2 text-sm text-slate-500">
                    Cargando reserva...
                </span>
            </div>
        `;
    }

    const renderReservation = (reservation: ReservationDetail):void =>{
        const content = drawer.querySelector<HTMLElement>('#reservationDetailContent');
        if(!content)return;

        const resources = reservation.resources.length > 0
                ? reservation.resources.map(resource => `
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-semibold text-slate-800">
                                ${escapeHtml(resource.name)}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                ${escapeHtml(resource.zoneName ?? 'Sin zona')}
                                · Capacidad ${resource.capacity}
                            </div>
                        </div>`).join('')
                : `
                    <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-500">
                        Sin mesa asignada
                    </div>`;


        content.innerHTML = `
            <div class="space-y-6">
                <div>
                    <div class="text-xs font-medium uppercase text-slate-400">Cliente</div>
                    <div class="mt-1 text-lg font-semibold">${escapeHtml(reservation.clientName)}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-slate-400">Personas</div>
                        <div class="font-medium"> ${reservation.numberOfPeople}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Estado</div>
                        <div class="font-medium capitalize">${escapeHtml(reservation.status)}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Horario</div>
                    <div class="mt-1 font-medium">
                        ${formatDateTime(reservation.startDate)} — ${formatTime(reservation.endDate)}
                    </div>
                </div>

                <div>
                    <div class="mb-2 text-xs text-slate-400">Mesa</div>
                    <div class="space-y-2">${resources}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-400">Observaciones</div>
                    <div class="mt-1 text-sm text-slate-700">
                        ${escapeHtml(reservation.observations ?? 'Sin observaciones')}
                    </div>
                </div>
            </div>`;
    }


    function renderActions(reservation: ReservationDetail): void{
        const actions = drawer?.querySelector<HTMLElement>('#reservationDetailActions');
        if(!actions)return;

        actions.innerHTML = '';
        if(reservation.status === 'pendiente'){
            actions.innerHTML = `
            <div class="space-y-2">
                <button
                    type="button"
                    data-confirm-reservation="${reservation.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Confirmar reserva
                </button>
                <button
                    type="button"
                    data-cancel-reservation="${reservation.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Cancelar reserva
                </button>
            </div>`;
            actions.classList.remove('hidden');
            return;
        }

        if (reservation.status === 'confirmada') {
            actions.innerHTML = `
                <button
                    type="button"
                    data-cancel-reservation="${reservation.id}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50"
                >
                <span class="material-symbols-outlined text-[20px]">cancel</span>
                    Cancelar reserva
                </button>`;
            actions.classList.remove('hidden');
            return;
        }

        actions.classList.add('hidden');
    }


    drawer.addEventListener('click', async event => {
        const target = event.target as HTMLElement;
        const buttonconfirm = target.closest<HTMLButtonElement>('[data-confirm-reservation]');
        const buttonCancel = target.closest<HTMLButtonElement>('[data-cancel-reservation]');
        let reservationId: number = 0;
        if(!buttonconfirm){
            if(!buttonCancel){
                return;
            }else{
                reservationId = Number(buttonCancel.dataset.confirmReservation);
            }
        }else{
            reservationId = Number(buttonconfirm.dataset.confirmReservation);
        }

        if(!reservationId)return;
        if(buttonconfirm)await confirmReservation(reservationId, buttonconfirm);
        if(buttonCancel)await cancelReservation( reservationId, buttonCancel);
    });


    async function confirmReservation(reservationId: number, button: HTMLButtonElement): Promise<void>{
        const confirmation = await Swal.fire({
                                icon: 'question',
                                title: '¿Confirmar reserva?',
                                text: 'La reserva cambiará a estado confirmada.',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, confirmar',
                                cancelButtonText: 'Cancelar'
                            });

        if(!confirmation.isConfirmed)return;
        button.disabled = true;
        const originalContent = button.innerHTML;
        button.innerHTML = `
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Confirmando...`;

        try {
            await confirmReservationRequest(reservationId);
            await Swal.fire({
                icon: 'success',
                title: 'Reserva confirmada',
                text: 'La reserva fue confirmada correctamente.',
                timer: 1200,
                showConfirmButton: false
            });

            /*
            * Volvemos a consultar la reserva.
            *
            * No cambiamos simplemente el estado
            * en memoria. Backend sigue siendo
            * nuestra fuente de verdad.
            */
            const reservation = await fetchReservation(reservationId);
            renderReservation(reservation);
            renderActions(reservation);
        }catch(error){
            Swal.fire({
                icon: 'error',
                title: 'No se pudo confirmar',
                text: error instanceof Error ? error.message : 'Ocurrió un error inesperado.'
            });
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    }


    async function confirmReservationRequest(reservationId: number): Promise<void>{
        const formData = new FormData();
        formData.append('reservationId', String(reservationId));

        const response = await fetch('/restaurant/api/reservations/confirm',
                            {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    Accept: 'application/json'
                                }
                            }
                        );

        const result = await response.json() as {success: boolean; message?: string;};
        if(!response.ok || !result.success)
            throw new Error(result.message ?? 'No fue posible confirmar la reserva.');
    }


    async function cancelReservation(reservationId: number, button: HTMLButtonElement): Promise<void>{
        const confirmation = await Swal.fire({
                icon: 'warning',
                title: '¿Cancelar reserva?',
                text: 'La mesa asignada quedará disponible nuevamente para este horario.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar reserva',
                cancelButtonText: 'Volver'
        });

        if(!confirmation.isConfirmed)return;
        button.disabled = true;
        const originalContent = button.innerHTML;

        button.innerHTML = `
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Cancelando...`;
        try {
            await cancelReservationRequest(reservationId);
            await Swal.fire({
                icon: 'success',
                title: 'Reserva cancelada',
                text: 'La reserva fue cancelada correctamente.',
                timer: 1200,
                showConfirmButton: false
            });

            const reservation = await fetchReservation(reservationId);
            renderReservation(reservation);
            renderActions(reservation);
        }catch(error){
            await Swal.fire({
                icon: 'error',
                title: 'No se pudo cancelar',
                text: error instanceof Error ? error.message : 'Ocurrió un error inesperado.'
            });
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    }


    async function cancelReservationRequest(reservationId: number): Promise<void>{
        const formData = new FormData();
        formData.append('reservationId', String(reservationId));
        const response = await fetch('/restaurant/api/reservations/cancel',
                            {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    Accept: 'application/json'
                                }
                            }
                        );
        const result = await response.json() as {success: boolean;  message?: string; };
        if(!response.ok || !result.success)
            throw new Error( result.message ?? 'No fue posible cancelar la reserva.'); 
    }


    function formatDateTime(value: string): string{
        const date = new Date(value.replace(' ', 'T'));
        return date.toLocaleString('es', {dateStyle: 'medium', timeStyle: 'short'});
    }

    function formatTime(value: string): string{
        const date = new Date(value.replace(' ', 'T'));
        return date.toLocaleTimeString('es', {hour: '2-digit', minute: '2-digit'});
    }

    function escapeHtml(value: string): string{
        const element = document.createElement('div');
        element.textContent = value;
        return element.innerHTML;
    }

}