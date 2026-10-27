import type { CartItem } from './cart';

// Importes de los productos pendientes.
// Por ahora no aplicamos descuentos ni cargos adicionales.
export interface CartTotals {
    base: number;  // Importe sin el impuesto.
    tax: number;   // Impuesto incluido en los precios.
    total: number; // Importe que se cobra.
}

// Redondea un número a la cantidad de decimales indicada.
function round(value: number, decimals: number): number {
    const factor = 10 ** decimals;
    return Math.round((value + Number.EPSILON) * factor) / factor;
}

// Calcula desde los ítems actuales.
// No modifica cantidades, precios ni impuestos del carrito.
export function calculateCartTotals(items: readonly CartItem[]): CartTotals{
    let base = 0;
    let tax = 0;
    let total = 0;

    for(const item of items){
        // Para la operación matemática, null equivale a tasa cero.
        // item.taxRate conserva su valor original.
        const rate = item.taxRate ?? 0;
        const lineTotal = round(item.unitPrice*item.quantity, 2);
        // Extraemos el impuesto incluido; no lo sumamos otra vez.
        // Ejemplo: 119 × 19 / 119 = 19 de impuesto.
        const lineTax = round( lineTotal * rate / (100 + rate), 3);
        // La base es lo que queda al separar el impuesto.
        const lineBase = round(lineTotal - lineTax, 3);
        // Acumulamos los importes ya redondeados de cada línea.
        base += lineBase;
        tax += lineTax;
        total += lineTotal;
    }

    // Redondeamos las sumas para reducir residuos decimales
    // de las operaciones con números de JavaScript.
    return {base: round(base, 3), tax: round(tax, 3), total: round(total, 2)};
}