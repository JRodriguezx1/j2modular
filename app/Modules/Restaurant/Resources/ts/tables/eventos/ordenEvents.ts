import { createCart } from "../cart";
import type { AccountConsumption, AccountTotals } from '../accountApi';
import { msjalertToast } from "../../appFunction";


const miDialogoDescuento = document.querySelector('#miDialogoDescuento') as HTMLDialogElement;
const formDescuento = miDialogoDescuento.querySelector<HTMLFormElement>('#formDescuento');
const detalleDescuento = miDialogoDescuento.querySelector<HTMLElement>('#descuentoLineaDetalle');
const tiposDescuento = miDialogoDescuento.querySelectorAll<HTMLInputElement>('input[name="tipodescuento"]');
const inputDescuento = document.querySelector('#inputDescuento') as HTMLInputElement;
const inputDescuentoClave = document.querySelector('#inputDescuentoClave') as HTMLInputElement;
const btnModalDescuentoCuenta = document.querySelector('#btnModalDescuentoCuenta') as HTMLButtonElement;
const miDialogoConfirmar = document.querySelector('#miDialogoConfirmar') as HTMLDialogElement;
const btnModalCortesiaCuenta = document.querySelector('#btnModalCortesiaCuenta') as HTMLButtonElement;
const confirmar = document.querySelector('.confirmar') as HTMLButtonElement;
const inputConfirmarClave = document.querySelector('#inputConfirmarClave') as HTMLInputElement;
const miDialogoEliminarItem = document.querySelector('#miDialogoEliminarItem') as HTMLDialogElement;
const sieliminar = document.querySelector('.sieliminar') as HTMLButtonElement;
const btnModalAnularAtencion = document.querySelector('#btnModalAnularAtencion') as HTMLButtonElement;

const masOpciones = document.querySelector('#masOpciones') as HTMLButtonElement;
const popover = document.querySelector<HTMLDivElement>("#opciones-popover");

let activePop: HTMLElement|null = null;
// Permite comprobar que seguimos trabajando con el mismo pedido.
let rowOrder: ActiveOrder|null = null;
let subtotal = 0;

type Cart = ReturnType<typeof createCart>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;

interface ActiveOrder {
    resourceId: number,
    occupationId: number|null,
    accountId: number|null,
    cartKey: CartKey,
    cart: Cart,
    itemsregistrados: AccountConsumption[],
    totales: AccountTotals|null,
    summaryPending: boolean
}

interface ordenEventsDependencies {
    ActiveOrderE: () => ActiveOrder|null,  //propiedad que contiene una funcion sin parametro, devuelve ActiveOrder | null
    renderCartPanelE: (items: ReturnType<Cart['getItems']>) => void,
    setSavingOrder: (saving: boolean) => void;
    refreshConfirmedOrderCart: () => Promise<void>;
}

//evento del boton mas opciones
export function registerOrdenEvents({ActiveOrderE, renderCartPanelE, setSavingOrder, refreshConfirmedOrderCart}: ordenEventsDependencies): void{

    //////////  EVENTO DE LAS ACCIONES DEL BTN MAS..  ///////////////
    masOpciones?.addEventListener('click', (e:MouseEvent)=>{
        const target = e.target;
        if(!(target instanceof Element))return;
        const btn = target.closest<HTMLElement>('#masOpciones');
        if(!btn)return;
        
        const order  = ActiveOrderE();
        if(order  === null || order.accountId === null){
            msjalertToast('warning', 'Verificar', 'La cuenta no esta disponible.');
            return;
        }

        if(activePop === btn){
            closeOpcionesPopover();
            return;
        }

        rowOrder = order;
        openPopover(btn);
    });


    // EVENTO AL BOTON PARA ABRIR MODAL DE DESCUENTO
    btnModalDescuentoCuenta.addEventListener('click', ()=>{
        const order = ActiveOrderE();
        //Evitar usar una selección que pertenecía a otro contexto.
        if(order === null || order.accountId === null || order !== rowOrder){
            closeOpcionesPopover();
            msjalertToast('warning', 'Verificar', 'Selecciona nuevamente el producto de la cuenta.');
            return;
        }
        
        if(!formDescuento || !detalleDescuento)return;
        // Cada apertura vuelve al modo Valor y limpia los datos de la línea anterior.
        formDescuento.reset();
        const mensaje = formDescuento.querySelector<HTMLElement>('#divmsjalertaClaveDcto');
        if(mensaje)mensaje.textContent = '';
        
        miDialogoDescuento.dataset.accountId = String(order.accountId);
        subtotal = Number(order.totales?.subtotal);
        detalleDescuento.textContent = `Aplicar Descuento · Subtotal: $${subtotal.toLocaleString('es-CO')}`;
        // El descuento vigente se puede reemplazar hasta el subtotal de esta línea.
        inputDescuento.value = '';
        inputDescuento.max = String(subtotal);
        closeOpcionesPopover();
        miDialogoDescuento.showModal();
        inputDescuento.focus();
        inputDescuento.select();
    });

    /////////////////////  logica del descuento  //////////////////////////
    tiposDescuento.forEach(radio => {
        radio.addEventListener('change', () => {
            // Porcentaje admite hasta 100; Valor admite hasta el subtotal registrado.
            inputDescuento.max = radio.value === 'porcentaje' ? '100' : String(subtotal);
            inputDescuento.value = '';
            inputDescuento.focus();
        });
    });

    /////////////////////   EVENTO AL BOTON APLICAR DESCUENTO   /////////////////////
    formDescuento?.addEventListener('submit', async event=>{
        event.preventDefault();
        // Buscar el botón también cuando el formulario se envía con Enter.
        const boton = formDescuento.querySelector<HTMLButtonElement>('#btnAplicarDcto');
        if(!boton || boton.disabled || !miDialogoDescuento.open)return;
        const order = ActiveOrderE();
        const accountId = Number(miDialogoDescuento.dataset.accountId);

        if(!order || order.accountId !== accountId){
            miDialogoDescuento.close();
            msjalertToast('warning', 'Verificar', 'Selecciona nuevamente el consumo.');
            return;
        }

        if(!formDescuento.reportValidity())return;

        const type = new FormData(formDescuento).get('tipodescuento');
        if(type !== 'valor' && type !== 'porcentaje')return;

        const mensaje = formDescuento.querySelector<HTMLElement>('#divmsjalertaClaveDcto');
        if(mensaje)mensaje.textContent = '';
        boton.disabled = true;
        setSavingOrder(true);
        let guardado = false;
        try{
            // Los importes anteriores necesitan confirmarse nuevamente.
            order.summaryPending = true;
            //await aplicarDescuentoCuenta(accountId, type, inputDescuento.value, inputDescuentoClave.value);
            guardado = true;
            inputDescuentoClave.value = '';
            miDialogoDescuento.close();
            // Actualiza consumos, descuento y totales, incluidos los pendientes del carrito.
            await refreshConfirmedOrderCart();
            msjalertToast('success', 'Cuenta actualizada', 'El descuento quedó guardado.');
        }catch(error){
            if(guardado){
                msjalertToast('warning', 'Descuento guardado', 'Vuelve a abrir la mesa para actualizar el resumen.');
            }else{
                const detalle = error instanceof Error ? error.message : 'Revisa la cuenta antes de reintentar.';
                if(mensaje)mensaje.textContent = detalle;
            }
        }finally{
            boton.disabled = false;
            setSavingOrder(false);
        }
    });



    ///////////   EVENTO AL BOTON PARA ABRIR MODAL DE CORTESIA    ////////////
    btnModalCortesiaCuenta.addEventListener('click', ()=>{
        const order = ActiveOrderE();
        //Evitar usar una selección que pertenecía a otro contexto.
        if(order === null || order.accountId === null || order !== rowOrder){
            closeOpcionesPopover();
            msjalertToast('warning', 'Verificar', 'Selecciona nuevamente el producto de la cuenta.');
            return;
        }

        const textConfirmacion = miDialogoConfirmar.querySelector<HTMLElement>('#textConfirmacion');
        const textDescripcion = miDialogoConfirmar.querySelector<HTMLElement>('#textDescripcion');
        if(!textConfirmacion || !textDescripcion)return;
        textConfirmacion.textContent = 'Aplicar cortesia';
        textDescripcion.textContent = 'El importe a cobrar por esta cuenta quedará en cero.';
        // Limpiar los datos de una apertura anterior.
        inputConfirmarClave.value = ''
        // Guardar la línea y la operación que el usuario va a confirmar.
        miDialogoConfirmar.dataset.accountId = String(order.accountId);
        closeOpcionesPopover();
        miDialogoConfirmar.showModal();
        
    });

    confirmar?.addEventListener('click', async ()=>{
        if(confirmar.disabled || !miDialogoConfirmar.open)return;
        const order = ActiveOrderE();
        const accountId = Number(miDialogoConfirmar.dataset.accountId);

        if(!order || order.accountId !== accountId){
            miDialogoConfirmar.close();
            msjalertToast('warning', 'Verificar', 'Selecciona nuevamente la cuenta.');
            return;
        }

        // Bloquear antes del primer await para evitar un segundo clic.
        confirmar.disabled = true;
        setSavingOrder(true);
        let confirmada = false;
        try{
            order.summaryPending = true;
            //await aplicarCortesiaLinea(accountId, inputConfirmarClave.value);
            confirmada = true;
            miDialogoConfirmar.close();
            inputConfirmarClave.value = '';
            await refreshConfirmedOrderCart();
            msjalertToast('success', 'Cuenta actualizada', 'La cortesía quedó aplicada.');
        }catch(error){
            miDialogoConfirmar.close();
            if(confirmada){
                msjalertToast('warning', 'Cortesia actualizada', 'Vuelve a abrir la mesa para actualizar los consumos.');
            }else{
                const detalle = error instanceof Error ? error.message : 'No se recibió confirmación.';
                msjalertToast('warning', 'Revisar cuenta', `${detalle} Revisa la cuenta antes de volver a confirmar.`);
            }
        }finally{
            setSavingOrder(false);
            confirmar.disabled = false;
        }
    });


    // EVENTO AL BOTON PARA ABRIR MODAL DE ANULAR LINEAS O ITEMS
        btnModalAnularAtencion.addEventListener('click', ()=>{
            const order = ActiveOrderE();
            //Evitar usar una selección que pertenecía a otro contexto.
            if(order === null || order.accountId === null || order !== rowOrder){
                closeOpcionesPopover();
                msjalertToast('warning', 'Verificar', 'Selecciona nuevamente el producto de la cuenta.');
                return;
            }
    
            const nameProduct = miDialogoEliminarItem.querySelector<HTMLElement>('#productoNombreId');
            const quantityInput = miDialogoEliminarItem.querySelector<HTMLInputElement>('#productoQtyId');
            if(!nameProduct || !quantityInput)return;
            
            //nameProduct.textContent = item.nombreproducto;
            //quantityInput.value = String(quantity);
            //quantityInput.max = String(quantity);
            // Conservar los identificadores del producto mostrado en el modal.
            //miDialogoEliminarItem.dataset.lineId = String(item.id);
            miDialogoEliminarItem.dataset.accountId = String(order.accountId);
            // Evitar conservar el motivo y la clave de una apertura anterior.
            const motivo = miDialogoEliminarItem.querySelector<HTMLTextAreaElement>('#observacionEliminacion');
            const clave = miDialogoEliminarItem.querySelector<HTMLInputElement>('#inputEliminarClave');
            if(motivo)motivo.value = '';
            if(clave)clave.value = '';
    
            closeOpcionesPopover();
            miDialogoEliminarItem.showModal();
            quantityInput.focus();
            quantityInput.select();
        });
    
    
        sieliminar?.addEventListener('click', async ()=>{
            if(sieliminar.disabled || !miDialogoEliminarItem.open)return;
    
            const order = ActiveOrderE();
            const accountId = Number(miDialogoEliminarItem.dataset.accountId);
    
            if(!order || order.accountId !== accountId){
                miDialogoEliminarItem.close();
                msjalertToast('warning', 'Verificar', 'Selecciona nuevamente la cuenta.');
                return;
            }
    
            const quantityInput = miDialogoEliminarItem.querySelector<HTMLInputElement>('#productoQtyId');
            const reasonInput = miDialogoEliminarItem.querySelector<HTMLTextAreaElement>('#observacionEliminacion');
            if(!quantityInput || !reasonInput)return;
            
            const reason = reasonInput.value.trim();
            
            reasonInput.setCustomValidity(reason.length > 0 && Array.from(reason).length <= 255 ? '' : 'Escribe un motivo de entre 1 y 255 caracteres.');
            if(!reasonInput.reportValidity())return;
    
            // Bloquear antes del primer await para evitar un segundo clic.
            sieliminar.disabled = true;
            setSavingOrder(true);
    
            let confirmada = false;
            try{
                order.summaryPending = true;
                //const result = await eliminarItem(accountId, lineId, quantity, reason);
                confirmada = true;
                miDialogoEliminarItem.close();
                // Una sola consulta trae las cantidades vigentes y los totales.
                await refreshConfirmedOrderCart();
                //const mensaje = result.alreadyCancelled ? 'La línea ya estaba anulada.' : 'La anulación quedó registrada.';
                //msjalertToast('success', 'Cuenta actualizada', mensaje);
            }catch(error){
                miDialogoEliminarItem.close();
                if(confirmada){
                    msjalertToast('warning', 'Anulación registrada', 'Vuelve a abrir la mesa para actualizar los consumos.');
                }else{
                    const detalle = error instanceof Error ? error.message : 'No se recibió confirmación.';
                    msjalertToast('warning', 'Revisar cuenta', `${detalle} Revisa la cuenta antes de volver a confirmar.`);
                }
            }finally{
                setSavingOrder(false);
                sieliminar.disabled = false;
            }
    
        });

}



export const closeOpcionesPopover = (): void => {
    if(popover)popover.hidden = true;
    activePop?.setAttribute("aria-expanded", "false");
    activePop = null;
    rowOrder = null;
};

const openPopover = (element: HTMLElement): void => {
    const rect = element.getBoundingClientRect();

    if(!popover)return;
    if(activePop && activePop !== element)activePop.setAttribute("aria-expanded", "false");

    activePop = element;
    element.setAttribute("aria-expanded", "true");
    popover.hidden = false;

    const width = popover.offsetWidth;
    const height = popover.offsetHeight;
    const margin = 8;

    // Ajustar horizontalmente a la pantalla
    const left = Math.max(margin, Math.min(rect.right - width, window.innerWidth - width - margin));
    // Abrir hacia abajo o arriba según espacio
    const top = rect.bottom + height + margin <= window.innerHeight ? rect.bottom - 24 : rect.top - height - 4;
    popover.style.left = `${left}px`;
    popover.style.top = `${Math.max(margin, top)}px`;
};