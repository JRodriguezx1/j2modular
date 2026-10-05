import type {AvailableTable, AvailableTablesResponse} from './types';

export interface AvailabilityParams{
    startDate: string;
    endDate: string;
    numberOfPeople: number;
}

let controller: AbortController | null = null;

export async function findAvailableTables(params: AvailabilityParams): Promise<AvailableTable[]> { //usado por reservationDrawer.ts
    controller?.abort();
    controller = new AbortController();

    const data = new FormData();
    data.append('startDate', params.startDate);
    data.append('endDate', params.endDate);
    data.append('numberOfPeople', String(params.numberOfPeople));

    const response = await fetch(
            '/restaurant/api/reservations/available-tables',
            {
                method: 'POST',
                body: data,
                signal: controller.signal
            }
        );

    const result = await response.json() as AvailableTablesResponse;
    if(!response.ok)throw new Error(result.message ?? 'No fue posible consultar disponibilidad.');
    
    return result.tables ?? [];
}