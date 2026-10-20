export type AccountAmount = string|number;

export interface AccountConsumption{
    id: number;
    idfactura: number;
    idproducto: number;
    nombreproducto: string;
    cantidad: AccountAmount;
    valorunidad: AccountAmount;
    subtotal: AccountAmount;
    base: AccountAmount;
    impuesto: AccountAmount;
    valorimp: AccountAmount;
    descuento: AccountAmount;
    total: AccountAmount;
    anotacion: string|null;
}

export interface AccountTotals{
    totalunidades: AccountAmount;
    subtotal: AccountAmount;
    base: AccountAmount;
    valorimpuestototal: AccountAmount;
    descuento: AccountAmount;
    total: AccountAmount;
}

export interface AccountSummary{
    accountId: number;
    occupationId: number;
    name: string;
    orderId: number|null;
    consumos: AccountConsumption[];
    totales: AccountTotals;
}

interface AccountSummaryResponse{
    success: boolean;
    data?: AccountSummary;
    message?: string;
}

// Consulta los productos registrados y los totales de una cuenta abierta.
export async function getAccountSummary(accountId: number, signal?: AbortSignal): Promise<AccountSummary>{
    if (!Number.isInteger(accountId) || accountId <= 0)
        throw new Error('El ID de la cuenta no es válido.');

    const url = `/restaurant/api/accounts/summary?accountId=${encodeURIComponent(String(accountId))}`;
    const response = await fetch(url, { headers: { Accept: 'application/json' }, signal });
    const result = await response.json() as AccountSummaryResponse;

    if (!response.ok || !result.success || !result.data)
        throw new Error(result.message ?? 'No fue posible consultar la cuenta.');
    if (!Array.isArray(result.data.consumos) || !result.data.totales ||
        typeof result.data.totales !== 'object' || Array.isArray(result.data.totales))
        throw new Error('La respuesta de la cuenta no tiene el formato esperado.');

    return result.data;
}