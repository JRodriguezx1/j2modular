import type {SimpleProduct} from './catalogoApi';

// Un producto pendiente de guardar. El precio conserva el formato del backend.
export interface CartItem {
    productId: number;
    name: string;
    unitPrice: number;
    quantity: number;
}

// Cada llamada crea un carrito independiente, que después vincularemos a una cuenta.
export function createCart(){
    let items: CartItem[] = [];

    function validateQuantity(quantity: number): void{
        if(!Number.isFinite(quantity) || quantity <= 0)throw new Error('La cantidad debe ser un número mayor que cero.');
    }

    // Si el producto ya está pendiente, aumentamos su cantidad.
    function addProduct(product: SimpleProduct, quantity: number = 1): void{
        validateQuantity(quantity);

        const price = Number(product.precioVenta);
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
            quantity
        });
    }

    // Reemplaza la cantidad. Para quitar un producto usamos removeProduct().
    function updateQuantity(productId: number, quantity: number): void{
        validateQuantity(quantity);
        const item = items.find(item => item.productId === productId);
        if(!item)throw new Error('El producto no está en el carrito.');
        item.quantity = quantity;
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
