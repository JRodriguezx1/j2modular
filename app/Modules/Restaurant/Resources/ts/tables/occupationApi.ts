export interface ActiveOccupation {
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
    occupation?: ActiveOccupation;
    message?: string;
}

interface OccupationAccountsResponse {
    success: boolean;
    data?: OccupationAccountsData;
    message?: string;
}

//consultar la atencion/mesa
export async function fetchActiveOccupation(resourceId: number): Promise<ActiveOccupation> {
    const response = await fetch(`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(resourceId)}`, {
        headers: { Accept: 'application/json' }
    });
    const result = await response.json() as ActiveOccupationResponse;
    if (!response.ok || !result.success || !result.occupation)
        throw new Error(result.message ?? 'No fue posible consultar la atención.');
    return result.occupation;
}

//consultar las cuentas asociadas a la ocupacion
export async function getOccupationAccounts(occupationId: number): Promise<OccupationAccountsData> {
    const response = await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(occupationId)}`, {
        headers: { Accept: 'application/json' }
    });
    const result = await response.json() as OccupationAccountsResponse;
    if (!response.ok || !result.success || !result.data)
        throw new Error(result.message ?? 'No fue posible consultar las cuentas.');
    return result.data;
}
