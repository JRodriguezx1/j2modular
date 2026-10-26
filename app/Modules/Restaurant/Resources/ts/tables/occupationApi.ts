export interface ActiveOccupation {
    occupationId: number;
    resourceId: number;
    reservationId: number | null;
    client?: Cliente;
    type: string;
    startDate: string;
    estimatedEndDate: string | null;
    status: string;
    numberOfPeople: number | null;
    observations: string | null;
}

export interface OccupationAccount {
    id: number;
    occupationId: number;
    invoiceId: number | null;
    name: string;
    status: string;
}

export interface OccupationAccountsData {
    occupationId: number;
    occupationStatus: string;
    accounts: OccupationAccount[];
}

interface ActiveOccupationResponse {
    success: boolean;
    occupation?: ActiveOccupation|null;
    message?: string;
}

interface OccupationAccountsResponse {
    success: boolean;
    data?: OccupationAccountsData;
    message?: string;
}

//consultar la atencion/mesa
export async function fetchActiveOccupation(resourceId: number): Promise<ActiveOccupation|null> {
    const url = `/restaurant/api/occupations/active?resourceId=${encodeURIComponent(resourceId)}`; //RestaurantController -> getActiveOccupation
    const response = await fetch(url, {
        headers: { Accept: 'application/json' }
    });
    const result = await response.json() as ActiveOccupationResponse;
    if(!response.ok || !result.success)
        throw new Error(result.message ?? 'No fue posible consultar la atención.');
    // Si falta el campo, no podemos asumir que la mesa está sin ocupación.
    if(result.occupation === undefined)
        throw new Error('La respuesta de la atención está incompleta.');
    return result.occupation;
}

//consultar las cuentas asociadas a la ocupacion
export async function getOccupationAccounts(occupationId: number): Promise<OccupationAccountsData> {
    const url = `/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(occupationId)}`;
    const response = await fetch(url, { headers: { Accept: 'application/json' } });
    const result = await response.json() as OccupationAccountsResponse;
    if (!response.ok || !result.success || !result.data)
        throw new Error(result.message ?? 'No fue posible consultar las cuentas.');
    return result.data;  /*retorna OccupationAccountsData = {
                                occupationId: number;
                                occupationStatus: string;
                                accounts: OccupationAccount[];
                            }*/
}
