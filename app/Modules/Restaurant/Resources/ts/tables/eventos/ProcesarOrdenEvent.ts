import { setElementText } from "../../appFunction";
import { createCart } from "../cart";
import { createCli } from "../cli";
import { saveFirstTableOrder, saveProductsToAccount } from "../orderApi";

const saveFirstOrderButton = document.getElementById('saveFirstOrderButton') as HTMLButtonElement;

type Cart = ReturnType<typeof createCart>;
type Cli = ReturnType<typeof createCli>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;

export interface OrderSaveResult {
    resourceId: number;
    occupationId: number;
    accountId: number;
    orderId: number;
}

interface ActiveOrder {
    resourceId: number,
    occupationId: number|null,
    accountId: number|null,
    cartKey: CartKey,
    cart: Cart,
    cli: Cli,
}



export function registrarProcesarOrdenEvent(
    getActiveOrder: () => ActiveOrder|null, 
    getContextVersion: () => number, 
    isSavingOrder: () => boolean, 
    setSavingOrder: (saving:boolean) => void,
    confirmFirstOrder: (order: ActiveOrder, result: OrderSaveResult) => void,
    refreshConfirmedOrderCart: () => Promise<void>){


        // Cada mesa conserva su propia confirmación.
    const pendingConfirmations = new Map<number, { order: ActiveOrder; result: OrderSaveResult; }>();

    function hasPendingConfirmation(resourceId: number): boolean{
        return pendingConfirmations.has(resourceId);
    }

    saveFirstOrderButton.addEventListener('click', async () => {
        // Evitar acciones mientras existe una petición en curso.
        if(isSavingOrder()) return;
        const order = getActiveOrder();
        if (!order) return;
        // Revisamos únicamente la confirmación de esta mesa.
        if(hasPendingConfirmation(order.resourceId)){
            setSavingOrder(true);
                try{
                    recoverConfirmedOrder(order.resourceId);
                    await refreshConfirmedOrderCart();
                    //////setElementText('orderWorkspaceSaveStatus', 'Pedido recuperado correctamente.');
                }catch(error){
                    console.error('No fue posible completar la recuperación del pedido.', error);
                    ///////setElementText('orderWorkspaceSaveStatus', 'Pedido recuperado, pero no se pudo actualizar el carrito.');
                }finally{
                    setSavingOrder(false);
                }
            return;
        }
        //void saveFirstOrder();  // Si no existe una confirmación pendiente, procesar el primer pedido normalmente.
      
        if(order.accountId !== null){
            await saveAdditionalOrder(); // La cuenta ya existe: corresponde al guardado adicional.
            return;
        }
        if(order.occupationId !== null){
            console.info('Esta atención todavía no tiene una cuenta asignada.');
            return;
        }
        await saveFirstOrder(order);
    });


    async function saveFirstOrder(order: ActiveOrder): Promise<void>{

        if(isSavingOrder() || hasPendingConfirmation(order.resourceId))return;
        if(order.occupationId !== null || order.accountId !== null)return;

        //const tableName = title.textContent ?? 'Mesa';
        const clientId = order.cli.getCli().id;
        const customerId = clientId === '' ? null : Number(clientId);
        const items = order.cart.getItems();

        setSavingOrder(true); // Bloquear el panel e invalidar consultas anteriores.
        // Capturar la versión después de invalidarlas.
        const contextVersion = getContextVersion();
        let saved = false;

        try{
            //////setElementText('orderWorkspaceSaveStatus', 'Guardando pedido...');
            const result = await saveFirstTableOrder(order.resourceId, customerId, items);
            saved = true;
            pendingConfirmations.set(order.resourceId, {order, result});
            recoverConfirmedOrder(order.resourceId);
            // Recuperar memoria no requiere que el panel siga abierto.
            // Dibujar sí requiere que siga siendo el mismo contexto.
            if(getContextVersion() !== contextVersion || getActiveOrder() !== order)return;
            
            await refreshConfirmedOrderCart();
            
                ///////setElementText('orderWorkspaceSaveStatus', 'Pedido guardado correctamente.');

            //Consulta nuevamente ocupación, cuenta y productos registrados.
            //await loadOrderWorkspace(order.resourceId, tableName);

        }catch(error){
            console.error(saved ? 'El pedido se guardó, pero falta actualizar el contexto o el resumen.' : 'No fue posible confirmar el guardado.', error);
        }finally{
            setSavingOrder(false);
        }
    }



    async function saveAdditionalOrder(): Promise<void>{
        const order = getActiveOrder();
        if(isSavingOrder() || order === null)return;
        if (hasPendingConfirmation(order.resourceId))return;
        // Solo aplica cuando ya existe una ocupación y una cuenta.
        if(order.occupationId === null || order.accountId === null)return;

        // Verificar que existan productos pendientes.
        const items = order.cart.getItems();
        if(items.length === 0){
            setElementText('orderWorkspaceSaveStatus', 'No hay productos pendientes para guardar.');
            return;
        }

        const accountId = order.accountId;
        // Bloquear nuevos envíos y modificaciones mientras guardamos.
        setSavingOrder(true);
        const contextVersion = getContextVersion();
        let saved = false;
        try{
            await saveProductsToAccount(accountId, items);
            saved = true;
            order.cart.clear(); // La cuenta y su clave ya existen: no hay que migrarlas. Limpiar.
            // No dibujar sobre otro contexto.
            if(getActiveOrder() !== order || getContextVersion() !== contextVersion)return;
            await refreshConfirmedOrderCart(); // Mostrar los productos registrados y sus nuevos totales.
        }catch(error){
            console.error(saved ? 'Los productos se guardaron, pero no se pudo actualizar el resumen.' : 'No fue posible confirmar el guardado. Revisa la cuenta.', error);
        }finally{
            setSavingOrder(false);
        }
    }


    //si falla confirmFirstOrder(), podamos volver a intentar la actualización local sin llamar nuevamente a saveFirstTableOrder().
    function recoverConfirmedOrder(resourceId: number): void{
        const pending = pendingConfirmations.get(resourceId);
        if(!pending)return;
        confirmFirstOrder(pending.order, pending.result);
        // Si confirmFirstOrder lanza un error, esta línea no se ejecuta:
        // la confirmación queda disponible para otro intento.
        pendingConfirmations.delete(resourceId);
    }

    return {hasPendingConfirmation, recoverConfirmedOrder};
}