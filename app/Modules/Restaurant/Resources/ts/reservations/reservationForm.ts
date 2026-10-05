import type {CreateReservationData, CreateReservationResponse} from './types';

async function createReservation(data: CreateReservationData): Promise<CreateReservationResponse>{
    const formData = new FormData();
    formData.append('clientId', String(data.clientId));
    formData.append('numberOfPeople', String(data.numberOfPeople));
    formData.append('startDate', data.startDate);
    formData.append('endDate', data.endDate);
    if(data.resourceId !== null)
        formData.append('resourceId', String(data.resourceId));
    if(data.observations)
        formData.append('observations', data.observations);

    const response = await fetch('/restaurant/api/reservations', {
                            method: 'POST',
                            body: formData,
                            headers: { Accept: 'application/json'}
                        }
                    );

    const result = await response.json() as CreateReservationResponse;
    if(!response.ok)throw new Error(result.message ?? 'No fue posible crear la reserva.');
    return result;
}


export function initReservationForm(): void {
    const form = document.querySelector<HTMLFormElement>('#reservationForm');
    if(!form)return;

    const clientId = form.querySelector<HTMLInputElement>('#reservationClientId');
    const date = form.querySelector<HTMLInputElement>('#reservationDate');
    const startTime = form.querySelector<HTMLInputElement>('#reservationStartTime');
    const endTime = form.querySelector<HTMLInputElement>('#reservationEndTime');
    const people = form.querySelector<HTMLInputElement>('#reservationPeople');
    const resourceId = form.querySelector<HTMLInputElement>('#reservationResourceId');
    const observations = form.querySelector<HTMLTextAreaElement>('#reservationObservations');
    const submitButton = form.querySelector<HTMLButtonElement>('[type="submit"]');

    if(!clientId || !date || !startTime || !endTime || !people || !resourceId || !submitButton)return;

    form.addEventListener('submit', async event => {
        console.log(123);
            event.preventDefault();
            const selectedClientId = Number(clientId.value);
            const numberOfPeople = Number(people.value);

            if(!selectedClientId){
                showError('Debes seleccionar un cliente.');
                return;
            }

            if(!date.value || !startTime.value || !endTime.value){
                showError('Debes seleccionar fecha y horario.');
                return;
            }

            const data: CreateReservationData = {
                clientId: selectedClientId,
                numberOfPeople,
                startDate: buildDateTime(date.value, startTime.value),
                endDate: buildDateTime(date.value, endTime.value),
                resourceId: resourceId.value ? Number(resourceId.value) : null,
                observations: observations?.value.trim() || null
            };

            await submitReservation(data, submitButton);
        }
    );

    async function submitReservation(data: CreateReservationData, button: HTMLButtonElement): Promise<void> {
        setSubmitting(button, true);
        try {
            const result = await createReservation(data);
            showSuccess( result.message ?? 'Reserva creada correctamente.');
            /*
             * Por ahora recargamos la página.
             *
             * Esto garantiza que timeline,
             * contadores y demás datos queden
             * sincronizados con backend.
             */
            window.setTimeout(() => { window.location.reload(); }, 700);
        }catch(error){
            showError(error instanceof Error ? error.message : 'No fue posible crear la reserva.');
        }finally{
            setSubmitting(button, false);
        }
    }


    function buildDateTime(selectedDate: string, selectedTime: string): string{
        return `${selectedDate} ${selectedTime}:00`;
    }

    function setSubmitting(button: HTMLButtonElement, submitting: boolean): void {
        button.disabled = submitting;
        if(submitting){
            button.dataset.originalText = button.textContent ?? '';

            button.innerHTML = `
                <span class="material-symbols-outlined animate-spin text-[18px]">
                    progress_activity
                </span>
                Guardando...`;

            return;
        }
        button.textContent = button.dataset.originalText ?? 'Guardar reserva';
    }


    function showError( message: string): void {
        if(typeof Swal !== 'undefined'){
            Swal.fire({
                icon: 'error',
                title: 'No se pudo crear la reserva',
                text: message,
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        console.error(message);
    }


    function showSuccess( message: string): void {
        if(typeof Swal !== 'undefined'){
            Swal.fire({
                icon: 'success',
                title: 'Reserva creada',
                text: message,
                showConfirmButton: false,
                timer: 1200
            });
            return;
        }
        console.log(message);
    }

}