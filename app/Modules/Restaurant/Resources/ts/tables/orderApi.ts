import type {CartItem} from './cart';
import type {AccountTotals} from './accountApi';

export interface FirstOrderData {
    resourceId: number;
    occupationId: number;
    accountId: number;
    orderId: number;
    lineIds: number[];
    totales: AccountTotals;
}

interface FirstOrderResponse {
    success: boolean;
    data?: FirstOrderData;
    message?: string;
}

export interface AdditionalOrderData {
    accountId: number;
    orderId: number;
    lineIds: number[];
    totales: AccountTotals;
}

interface AdditionalOrderResponse {
    success: boolean;
    data?: AdditionalOrderData;
    message?: string;
}

interface CliUpOrderData {
    accountId: number,
    occupationId: number,
    orderId: number,
    clientId: number|null,
}

interface CliUpOrderResponse {
    success: boolean,
    data?: CliUpOrderData,
    message?: string,
}

interface CancelLineData {
    accountId: number;
    occupationId: number;
    orderId: number;
    lineId: number;
    alreadyCancelled: boolean;
    totales: AccountTotals;
}


export async function saveFirstTableOrder(resourceId: number, customerId: number|null, cartItems: CartItem[]): Promise<FirstOrderData>{
    if(!Number.isSafeInteger(resourceId) || resourceId <= 0)
        throw new Error('La mesa no es válida.');
    
    if(customerId !== null && (!Number.isSafeInteger(customerId) || customerId <= 0))
        throw new Error('El cliente seleccionado no es válido.');
    
    const items = prepareOrderItems(cartItems);
    const form = new FormData();
    form.set('resourceId', String(resourceId));
    form.set('items', JSON.stringify(items));
    if(customerId !== null)form.set('customerId', String(customerId));
    
    const response = await fetch('/restaurant/api/tables/crearOrdenComanda', {
        method: 'POST',
        headers: {Accept: 'application/json'},
        body: form,
    });

    const result = await response.json() as FirstOrderResponse;

    if(!result || !response.ok || !result.success)
        throw new Error(result?.message ?? 'No fue posible confirmar el guardado. Revisa la mesa antes de reintentar.');
    // Comprobamos los identificadores antes de cambiar el contexto local.
    const data = result.data;
    if(!data || data.resourceId !== resourceId || ![data.occupationId, data.accountId, data.orderId].every(id=>Number.isSafeInteger(id) && id>0)
         || !Array.isArray(data.lineIds) || !data.totales){
        throw new Error('La respuesta del guardado está incompleta. Revisa la mesa antes de reintentar.');
    }

    return data;
}


// Agregar únicamente los pendientes a una cuenta existente.
export async function saveProductsToAccount(accountId: number, clientId:number|null|undefined, cartItems: CartItem[]): Promise<AdditionalOrderData>{
    if(!Number.isSafeInteger(accountId) || accountId <= 0)
        throw new Error('La cuenta no es válida.');

    // undefined y null tienen significados válidos y distintos.
    if(clientId !== undefined && clientId !== null && (!Number.isSafeInteger(clientId) || clientId <= 0))
        throw new Error('El cliente seleccionado no es válido.');

    const items = prepareOrderItems(cartItems);
    const form = new FormData();
    form.set('accountId', String(accountId));
    // undefined: no enviar el campo.
    // null: enviar vacío para retirar.
    // número: enviar el cliente que se quiere asignar.
    if(clientId !== undefined)form.set('clientId', clientId === null ? '' : String(clientId));
    form.set('items', JSON.stringify(items));

    const response = await fetch('/restaurant/api/accounts/products/addProducts', //llama controlador OccupationAccountController::addProducts
        {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: form
        }
    );

    const result = await response.json() as AdditionalOrderResponse;

    if(!result || !response.ok || result.success !== true)
        throw new Error(result?.message ?? 'No fue posible confirmar el guardado del lote.' );

    const data = result.data;
    // Validar la respuesta antes de limpiar los productos pendientes.
    if(!data || data.accountId!==accountId || !Number.isSafeInteger(data.orderId) || data.orderId<=0 || !Array.isArray(data.lineIds) ||
        data.lineIds.length!==items.length || !data.lineIds.every(id=>Number.isSafeInteger(id)&&id>0) || !data.totales || typeof data.totales!=='object' || Array.isArray(data.totales)){
        throw new Error('La respuesta del guardado está incompleta. Revisa la cuenta antes de reintentar.');
    }

    return data;
}



// Preparar el mismo formato para el primer guardado y los siguientes.
function prepareOrderItems(cartItems: CartItem[]){
    if(cartItems.length === 0)
        throw new Error('Agrega al menos un producto.');

    return cartItems.map(item => {
        if(!Number.isSafeInteger(item.productId) || item.productId <= 0 || !Number.isFinite(item.quantity) || item.quantity <= 0)
            throw new Error('Revisa los productos y sus cantidades.');
        
        const quantity = item.quantity.toFixed(2);
        //No redondear silenciosamente cantidades con más decimales. La tolerancia admite pequeñas diferencias de JavaScript.
        if(!/^(?:0|[1-9]\d{0,7})\.\d{2}$/.test(quantity) || Math.abs(item.quantity - Number(quantity)) > 1e-9 || Number(quantity) <= 0) 
            throw new Error('Las cantidades deben tener como máximo dos decimales y ocho dígitos enteros.');
        
        // Precios e impuestos se obtienen en el backend.
        return {productId: item.productId, name: item.name, unitPrice: item.unitPrice, taxRate: item.taxRate, quantity};
    });
}


// Actualiza únicamente el cliente de la ocupación y de su factura.
export async function UpdateAccountCustomer(accountId: number, clientId:number|null): Promise<CliUpOrderData>{
    if(!Number.isSafeInteger(accountId) || accountId <= 0)
        throw new Error('La cuenta no es válida.');

    // Solo admitimos null para retirar o un ID positivo para asignar.
    if(clientId !== null && (!Number.isSafeInteger(clientId) || clientId <= 0))
        throw new Error('El cliente seleccionado no es válido.');

    const form = new FormData();
    form.set('accountId', String(accountId));
    form.set('clientId', clientId === null ? '' : String(clientId));

    const response = await fetch('/restaurant/api/accounts/customer/UpdateAccountCustomer', //llama controlador OccupationAccountController::UpdateAccountCustomer
        {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: form
        }
    );

    const result = await response.json() as CliUpOrderResponse;

    if(!result || !response.ok || result.success !== true)
        throw new Error(result?.message ?? 'No fue posible actualizar el cliente.' );

    const data = result.data;
    // Confirmar que la respuesta corresponde al cambio solicitado.
    if(!data || data.accountId!==accountId || !Number.isSafeInteger(data.occupationId) || data.occupationId<=0 || !Number.isSafeInteger(data.orderId) ||  data.orderId<=0 || data.clientId!==clientId)
        throw new Error('La respuesta de actualización está incompleta o no corresponde.');

    return data;
}


// eliminar item de una linea o item registrado.
export async function eliminarItem(accountId: number, lineId:number, quantity: string, reason: string): Promise<CancelLineData>{
    if(!Number.isSafeInteger(accountId) || accountId <= 0)
        throw new Error('La cuenta no es válida.');

    // Solo admitimos null para retirar o un ID positivo para asignar.
    if(!Number.isSafeInteger(lineId) || lineId <= 0)
        throw new Error('La linea seleccionada no es valida.');


    const form = new FormData();
    form.set('accountId', String(accountId));
    form.set('lineId', String(lineId));
    form.set('quantity', quantity); // Conservar el decimal escrito en el modal.
    form.set('reason', reason.trim());

    const response = await fetch('/restaurant/api/accounts/lines/cancel', //llama controlador OccupationAccountController::UpdateAccountCustomer
        {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: form
        }
    );

    const result = await response.json() as {success:boolean, message?:string, data?:CancelLineData};

    if(!response.ok || result?.success !== true)
        throw new Error(result?.message ?? 'No fue posible eliminar el item.' );

    const data = result.data;
    if(!data || data.accountId!==accountId || !Number.isSafeInteger(data.occupationId) || data.occupationId<=0 || !Number.isSafeInteger(data.orderId) ||  data.orderId<=0 || data.lineId!==lineId)
        throw new Error('La respuesta de actualización está incompleta o no corresponde.');

    return data;
}