import { ajustarAnchoCantidad, setElementText } from "../../appFunction";
import { createCart } from "../cart";
import { SimpleProduct } from "../catalogoApi";
import { calculateCartTotals } from '../cartTotals';
import { renderCartTotals } from '../cartPanel';

type Cart = ReturnType<typeof createCart>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;

interface ActiveOrder {
    resourceId: number;
    occupationId: number|null;
    accountId: number|null;
    cartKey: CartKey;
    cart: Cart;
}

interface itemsEventsDependencies {
    productosGrid: HTMLElement,
    cartContainer: HTMLElement,
    catalogProducts: Map<number, SimpleProduct>,
    ActiveOrderE: () => ActiveOrder|null,  //propiedad que contiene una funcion sin parametro, devuelve ActiveOrder | null
    renderCartPanelE: (items: ReturnType<Cart['getItems']>) => void,
}

//evento de los productos del catalogo
export function registerProductEvents({productosGrid, cartContainer, catalogProducts, ActiveOrderE, renderCartPanelE}: itemsEventsDependencies): void{
    //evento de los productos del catalogo
    productosGrid?.addEventListener('click', event => {
        console.log(catalogProducts.size);
        const target = event.target;
        if(!(target instanceof Element))return;

        const article = target.closest<HTMLElement>('article[data-product-id]');
        if(!article || !productosGrid.contains(article))return;
        
        const activeOrder = ActiveOrderE();
        // La cuenta debe haber terminado de cargar correctamente.
        if(activeOrder === null){
            setElementText('catalogProductStatus', 'El pedido aún no está disponible. Revisa el estado del panel.');
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
            console.table(activeOrder.cart.getItems());
        }catch(error){
            console.error('Error al modificar el carrito:', error);
        }
    });


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
      if(inputVal < 0){
        val = '1';
      };
      input.value = val;
      const item = cart.updateQuantity(productId, inputVal);
      // Actualizamos el importe de esta fila usando el cálculo común.
      const lineTotal = calculateCartTotals([item]).total;
      //actualizar solo fila
      row.querySelector('.precioTotalItem')!.textContent = "$"+lineTotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
      // Actualizamos el resumen sin reconstruir los inputs.
      renderCartTotals(cart.getItems());
      ajustarAnchoCantidad(input, item.quantity);
      console.table(activeOrder.cart.getItems());
    });
}