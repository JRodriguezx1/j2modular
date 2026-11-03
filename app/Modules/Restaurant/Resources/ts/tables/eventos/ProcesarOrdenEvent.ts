import { msjalertToast, setElementText } from "../../appFunction";
import { createCart } from "../cart";
import { createCli } from "../cli";
import { createCliDian } from "../cliDian";
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
    cliDian: ReturnType<typeof createCliDian>
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
                    msjalertToast('success', 'Exito!', 'Pedido recuperado correctamente.');
                }catch(error){
                    msjalertToast('warning', 'Pedido recuperado', 'Pero no se pudo actualizar el carrito.');
                }finally{
                    setSavingOrder(false);
                }
            return;
        }
      
        if(order.accountId !== null){
            await saveAdditionalOrder(); // La cuenta ya existe: corresponde al guardado adicional.
            return;
        }
        if(order.occupationId !== null){
            msjalertToast('warning', 'Verficar', 'Esta atención todavía no tiene una cuenta asignada.');
            return;
        }
        await saveFirstOrder(order);
    });


    async function saveFirstOrder(order: ActiveOrder): Promise<void>{

        if(isSavingOrder() || hasPendingConfirmation(order.resourceId))return;
        if(order.occupationId !== null || order.accountId !== null)return;

        const items = order.cart.getItems();

        setSavingOrder(true); // Bloquear el panel e invalidar consultas anteriores.
        // Capturar la versión después de invalidarlas.
        const contextVersion = getContextVersion();
        let saved = false;

        try{
            // Capturar y validar el cliente que se enviará.
            const customerId = order.cli.getClientId();
            const result = await saveFirstTableOrder(order.resourceId, customerId, items);
            saved = true;
            // La factura se creó con este cliente, incluso si es null.
            order.cli.confirmSavedClient(customerId);
            pendingConfirmations.set(order.resourceId, {order, result});
            recoverConfirmedOrder(order.resourceId);
            // Recuperar memoria no requiere que el panel siga abierto.
            // Dibujar sí requiere que siga siendo el mismo contexto.
            if(getContextVersion() !== contextVersion || getActiveOrder() !== order)return;
            await refreshConfirmedOrderCart();
            msjalertToast('success', 'Exito!', 'Pedido guardado correctamente.');
        }catch(error){
            msjalertToast('warning', saved ? 'El pedido se guardó':'Verificar pedido', saved ? 'falta actualizar el contexto o el resumen.' : 'No fue posible confirmar el guardado.');
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
            msjalertToast('warning', 'No hay items', 'No hay productos pendientes para guardar.');
            return;
        }

        const accountId = order.accountId;
        // Bloquear nuevos envíos y modificaciones mientras guardamos.
        setSavingOrder(true);
        const contextVersion = getContextVersion();
        let saved = false;
        try{
            // Capturar la intención de cambio antes de enviar.
            const clientChange = order.cli.getClientChange();
            await saveProductsToAccount(accountId, clientChange, items);
            saved = true;
            // Actualizar la referencia solo si enviamos un cambio de cliente.
            if(clientChange !== undefined)
                order.cli.confirmSavedClient(clientChange);
            order.cart.clear(); // La cuenta y su clave ya existen: no hay que migrarlas. Limpiar.
            msjalertToast('success', 'Exito!', 'Pedido actualizado correctamente.');
            // No dibujar sobre otro contexto.
            if(getActiveOrder() !== order || getContextVersion() !== contextVersion)return;
            await refreshConfirmedOrderCart(); // Mostrar los productos registrados y sus nuevos totales.
        }catch(error){
            msjalertToast('warning', saved ? 'Los productos se guardaron':'Verificar cuenta', saved ? 'No se pudo actualizar el resumen.' : 'No fue posible confirmar el guardado.');
        }finally{
            setSavingOrder(false);
        }
    }


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