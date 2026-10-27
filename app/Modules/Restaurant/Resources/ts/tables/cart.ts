import type {SimpleProduct} from './catalogoApi';

// Un producto pendiente de guardar. El precio conserva el formato del backend.
export interface CartItem {
    productId: number,
    name: string,
    unitPrice: number, // Precio con impuesto incluido.
    quantity: number,
    taxRate: number|null, // Porcentaje: 19, 5, 0... o null.
}

// Cada llamada crea un carrito independiente, que después vincularemos a una cuenta.
export function createCart(){
    let items: CartItem[] = [];

    function validateQuantity(quantity: number): void{
        if(!Number.isFinite(quantity) || quantity < 0)throw new Error('La cantidad debe ser un número mayor que cero.');
    }


    /*function editarCantidad(productId: number, quantity: number){
        validateQuantity(quantity);
        const item = items.find(item => item.productId === productId);
        if(!item)throw new Error('El producto no está en el carrito.');
        item.quantity = quantity;
    }*/

    // Si el producto ya está pendiente, aumentamos su cantidad.
    function addProduct(product: SimpleProduct, quantity: number = 1): void{
        validateQuantity(quantity);
        const price = Number(product.precioVenta);
        const taxRate = product.impuesto;
        // Si hay porcentaje, debe ser un entero entre 0 y 100.
        if(taxRate !== null && (!Number.isInteger(taxRate) || taxRate < 0 || taxRate > 100))
            throw new Error('El impuesto del producto no es válido.');
        if(!Number.isInteger(product.id) || product.id <= 0)
            throw new Error('El producto no es válido.');
        if(!Number.isFinite(price) || price < 0)
            throw new Error('El precio del producto no es válido.');

        const item = items.find(item => item.productId === product.id);
        if(item){
            const newQuantity = item.quantity + quantity;
            validateQuantity(newQuantity);
            item.quantity = newQuantity;
            return;
        }

        items.push({
            productId: product.id,
            name: product.nombre,
            unitPrice: price,
            quantity,
            taxRate
        });
    }

    // Reemplaza la cantidad.
    function updateQuantity(productId: number, quantity: number): CartItem{
        validateQuantity(quantity);
        const item = items.find(item => item.productId === productId);
        if(!item)throw new Error('El producto no está en el carrito.');
        item.quantity = quantity;
        return item;
    }

    function removeProduct(productId: number): void{
        items = items.filter(item => item.productId !== productId);
    }

    // Devolvemos copias para que la vista no modifique el carrito accidentalmente.
    function getItems(): CartItem[]{
        return items.map(item => ({...item}));
    }

    function clear(): void{
        items = [];
    }

    return {addProduct, updateQuantity, removeProduct, getItems, clear};
}
