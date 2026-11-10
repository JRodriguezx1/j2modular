import { ajustarAnchoCantidad, mostrarFeedbackCarrito, msjalertToast, setElementText } from "../../appFunction";
import { createCart } from "../cart";
import { SimpleProduct } from "../catalogoApi";
import { calculateCartTotals } from '../cartTotals';
import { renderCartTotals } from '../cartPanel';
import type { AccountConsumption } from '../accountApi';

const productsRegistrados = document.getElementById('accountPanelConsumptions');
const popover = document.querySelector<HTMLDivElement>("#cart-popover");
const btnsdevolverinv = document.querySelectorAll<HTMLInputElement>('input[name="devolverinventario"]'); //radio buttom
const miDialogoDescuento = document.querySelector('#miDialogoDescuento') as HTMLDialogElement;
const miDialogoEliminarItem = document.querySelector('#miDialogoEliminarItem') as HTMLDialogElement;

let activeItemId: string|number|null = null;
let activePop: HTMLElement | null = null;
let rowId:number|null = null;
// Permite comprobar que seguimos trabajando con el mismo pedido.
let rowOrder: ActiveOrder | null = null;

type Cart = ReturnType<typeof createCart>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;

interface ActiveOrder {
    resourceId: number,
    occupationId: number|null,
    accountId: number|null,
    cartKey: CartKey,
    cart: Cart,
    itemsregistrados: AccountConsumption[]
}

interface itemsEventsDependencies {
    otrosProducts: HTMLButtonElement,
    productosGrid: HTMLElement,
    cartContainer: HTMLElement,
    catalogProducts: Map<number, SimpleProduct>,
    ActiveOrderE: () => ActiveOrder|null,  //propiedad que contiene una funcion sin parametro, devuelve ActiveOrder | null
    renderCartPanelE: (items: ReturnType<Cart['getItems']>) => void,
}

//evento de los productos del catalogo
export function registerProductEvents({otrosProducts, productosGrid, cartContainer, catalogProducts, ActiveOrderE, renderCartPanelE}: itemsEventsDependencies): void{
    //evento de los productos del catalogo
    productosGrid?.addEventListener('click', event => {
        const target = event.target;
        if(!(target instanceof Element))return;
        const article = target.closest<HTMLElement>('article[data-product-id]');
        if(!article || !productosGrid.contains(article))return;
        
        const activeOrder = ActiveOrderE();
        if(activeOrder === null){
            msjalertToast('warning', 'Advertencia', 'El pedido aún no está disponible. Revisa el estado del panel.');
            return;
        }

        // Recuperamos el producto usando el ID guardado en el article.
        const productId = Number(article.dataset.productId);
        const product = catalogProducts.get(productId);
        if(!product)return;

        try{
            // Agrega una unidad. Si ya existe, aumenta su cantidad.
            activeOrder.cart.addProduct(product);
            // Volvemos a dibujar la lista usando los datos actualizados.
            renderCartPanelE(activeOrder.cart.getItems()); //calcula los totales y muestra los totales en cartPanel.ts
            article.classList.remove('producto-agregado-feedback');
            article.classList.add('producto-agregado-feedback');
            setTimeout(()=>article.classList.remove('producto-agregado-feedback'), 900);
            mostrarFeedbackCarrito(product.nombre, 1);
            // Temporal: permite comprobar el resultado en la consola.
            console.log('Cuenta activa:', activeOrder.accountId);
            console.log('Cuenta en MySQL:', activeOrder.accountId);
            console.table(activeOrder.cart.getItems());
        }catch(error){
            setElementText('catalogProductStatus', error instanceof Error ? error.message : 'No fue posible agregar el producto.');
        }
    });


    //Evento a los items del carrito   
    cartContainer?.addEventListener('click', event => {
        const target = event.target;
        if(!(target instanceof Element))return;

        const button = target.closest<HTMLButtonElement>('button[data-cart-action]');
        if(!button || !cartContainer.contains(button))return;
        const activeOrder = ActiveOrderE();
        if(activeOrder === null)return;
        const row = button.closest<HTMLElement>('[data-cart-product-id]');
        if (!row) return;

        const productId = Number(row.dataset.cartProductId);
        if(!Number.isSafeInteger(productId) || productId <= 0)return;

        const action = button.dataset.cartAction;
        const cart = activeOrder.cart;
        try {
            switch(action){
                case 'increase': {
                    if(productId === 1){
                        const item = cart.getItems().find(item => item.productId === productId);
                        if(!item)return;
                        if(item.quantity < 1)return;
                        cart.updateQuantity(productId, Math.round((item.quantity + 1) * 100) / 100);
                        break;
                    }
                    const product = catalogProducts.get(productId);
                    if(!product)return;
                    cart.addProduct(product);
                    break;
                }
                case 'decrease': {
                    const item = cart.getItems().find(item => item.productId === productId);
                    if(!item)return;
                    if(item.quantity <= 1)return;
                    cart.updateQuantity(productId, Math.round((item.quantity - 1) * 100) / 100);
                    break;
                }
                case 'remove': {
                    cart.removeProduct(productId);
                    break;
                }
                default: return;
            }

            renderCartPanelE(cart.getItems()); //calcula los totales y muestra los totales en cartPanel.ts
        }catch(error){
            console.error('Error al modificar el carrito:', error);
        }
    });


    //evento al contenedor de los inputs del carrito de cada item
    cartContainer?.addEventListener('input', e=>{
      const activeOrder = ActiveOrderE();
      if(activeOrder === null)return;
      const cart = activeOrder.cart;
      const input = e.target as HTMLInputElement;
      if (!input.classList.contains('inputcantidad')) return;
      //const fila = input?.closest('.productselect') as HTMLTableRowElement;
      //indexcarrito = Number(fila.dataset.indexcarrito!);

      const row = input.closest<HTMLElement>('[data-cart-product-id]');
      if (!row) return;

      const productId = Number(row.dataset.cartProductId);
      if(!Number.isSafeInteger(productId) || productId <= 0)return;
    
      let val = input.value;
      val = val.replace(/[^0-9.]/g, '');
      const partes = val.split('.');
      if(partes.length > 2)val = partes[0]+'.'+partes.slice(1).join('');
      if (val.startsWith('.'))val = '1';
      if (val === '' || isNaN(parseFloat(val))) val = '';

      const inputVal = Number(val);
      if(inputVal < 0)val = '1';
      input.value = val;
      const item = cart.updateQuantity(productId, inputVal);
      // Actualizamos el importe de esta fila usando el cálculo común.
      const lineTotal = calculateCartTotals([item]).total;
      //actualizar solo fila
      row.querySelector('.precioTotalItem')!.textContent = "$"+lineTotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
      // Actualizamos el resumen sin reconstruir los inputs.
      renderCartTotals(cart.getItems());
      ajustarAnchoCantidad(input, item.quantity);
    });


    //   OTROS PRODUCTOS   //
    const miDialogoOtrosProductos = document.querySelector('#miDialogoOtrosProductos') as any;
    let otrosproductos:{id:number, nombre:string, cantidad:number, valorunidad:number, total:number}={
      id: 0,
      nombre: '',
      cantidad: 0,
      valorunidad: 0,
      total: 0
    }
    otrosProducts.addEventListener('click', ()=> miDialogoOtrosProductos.showModal() );

    /////////////////////Evento al formulario de agregar otros productos //////////////////////
    document.querySelector('#formOtrosProductos')?.addEventListener('submit', (e:Event)=>{
        e.preventDefault();
        const formelements = (e.target as HTMLFormElement).elements;
        const impuesto = (formelements.namedItem('porcentaje_de_impuesto') as HTMLSelectElement).value;
        const cantidadotros = Number((formelements.namedItem('cantidadotros') as HTMLInputElement).value);
        const preciootros = Number((formelements.namedItem('preciootros') as HTMLInputElement).value);
        
        otrosproductos!.id += -1 ;
        otrosproductos!.nombre = (formelements.namedItem('nombreotros') as HTMLInputElement).value;
        otrosproductos!.cantidad = cantidadotros;
        otrosproductos!.valorunidad = preciootros/cantidadotros;
        otrosproductos!.total = preciootros;

        const activeOrder = ActiveOrderE();
        if(activeOrder === null)return;

        const product:SimpleProduct = {
            id: 1,
            categoriaId: 1,
            nombre: otrosproductos!.nombre,
            foto: '',
            impuesto: impuesto==''?null:Number(impuesto), //impuesto en %,
            tipoproducto: -1,
            tipoproduccion: -1,
            sku: '-1',
            rendimientoestandar: '1',
            precioVenta: otrosproductos.valorunidad,
            percentcomision: '0',
            prioridadcomision: 0
        }

        const carrito = activeOrder.cart;
        carrito.addProduct(product, otrosproductos.cantidad);
        mostrarFeedbackCarrito(otrosproductos.nombre ?? 'Producto personalizado', otrosproductos.cantidad ?? 1);
        miDialogoOtrosProductos.close();
        (e.target as HTMLFormElement).reset();
        renderCartPanelE(activeOrder.cart.getItems());
    });


    //////////  EVENTO A LOS PRODUCTOS REGISTRADOS  ///////////////
    productsRegistrados?.addEventListener('click', (e:MouseEvent)=>{
        const target = e.target;
        if(!(target instanceof Element))return;
        const row = target.closest<HTMLElement>('div[data-idregistro]');
        if(!row)return;
        
        const order  = ActiveOrderE();
        if(order  === null || order.accountId === null){
            msjalertToast('warning', 'Verificar', 'La cuenta no esta disponible.');
            return;
        }

        const lineId = Number(row.dataset.idregistro);
        if(!Number.isSafeInteger(lineId) || lineId <= 0)return;

        // Buscar por ID de ventas, no por ID del producto.
        const item = order.itemsregistrados.find(item => Number(item.id) === lineId);
        if(!item){
            msjalertToast('warning','Verificar', 'El consumo ya no está disponible. Actualiza la cuenta.');
            return;
        }

        if(activePop === row){
            closePopover();
            return;
        }
        rowId = lineId;
        rowOrder = order;
        openPopover(row, lineId);
    });


    const btnModalDescuento = document.querySelector('#btnModalDescuento') as HTMLButtonElement;
    btnModalDescuento.addEventListener('click', ()=>{
        console.log(rowId);
        miDialogoDescuento.showModal();
    });

    const btnModalEliminarItem = document.querySelector('#btnModalEliminarItem') as HTMLButtonElement;
    btnModalEliminarItem.addEventListener('click', ()=>{
        const order = ActiveOrderE();
        //Evitar usar una selección que pertenecía a otro contexto.
        if(order === null || order.accountId === null || order !== rowOrder || rowId === null){
            closePopover();
            msjalertToast('warning', 'Verificar', 'Selecciona nuevamente el producto de la cuenta.');
            return;
        }

        const item = order.itemsregistrados.find(item => Number(item.id) === rowId);
        if(!item){ //se puede anular la condicion
            closePopover();
            msjalertToast('warning', 'Verificar', 'El consumo ya no está disponible.');
            return;
        }

        const quantity = Number(item.cantidad);
        if(!Number.isFinite(quantity) || quantity <= 0){
            msjalertToast('warning', 'Verificar', 'La cantidad registrada no es válida.');
            return;
        }

        const nameProduct = miDialogoEliminarItem.querySelector<HTMLElement>('#productoNombreId');
        const quantityInput = miDialogoEliminarItem.querySelector<HTMLInputElement>('#productoQtyId');
        if(!nameProduct || !quantityInput)return;
        
        nameProduct.textContent = item.nombreproducto;
        quantityInput.value = String(quantity);
        quantityInput.max = String(quantity);
        // Conservar los identificadores del producto mostrado en el modal.
        miDialogoEliminarItem.dataset.lineId = String(item.id);
        miDialogoEliminarItem.dataset.accountId = String(order.accountId);
        // Evitar conservar el motivo y la clave de una apertura anterior.
        const motivo = miDialogoEliminarItem.querySelector<HTMLTextAreaElement>('#observacionEliminacion');
        const clave = miDialogoEliminarItem.querySelector<HTMLInputElement>('#inputEliminarClave');
        if(motivo)motivo.value = '';
        if(clave)clave.value = '';

        closePopover();
        miDialogoEliminarItem.showModal();
        quantityInput.focus();
        quantityInput.select();
    });


}


export const closePopover = (): void => {
    if(popover)popover.hidden = true;
    activePop?.setAttribute("aria-expanded", "false");
    activePop = null;
    activeItemId = null;
    // Limpiar también la selección asociada al popover.
    rowId = null;
    rowOrder = null;
};


const openPopover = (registroDiv: HTMLElement, registroId: string|number): void => {
    const rect = registroDiv.getBoundingClientRect();

    if(!popover)return;
    if(activePop && activePop !== registroDiv)activePop.setAttribute("aria-expanded", "false");

    activePop = registroDiv;
    activeItemId = registroId;
    registroDiv.setAttribute("aria-expanded", "true");

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