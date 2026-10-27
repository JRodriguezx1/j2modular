export interface I_Categorias {
    id: string;
    nombre: string;
    codigo: string | null;
    totalproductos: string;
    foto: string | null;
    fechacreacion: string | null;
    visible: string;
}

export interface SimpleProduct {
    id: number,
    categoriaId: number|null,
    nombre: string,
    foto: string|null,
    impuesto: number|null,
    tipoproducto: number|null,
    tipoproduccion: number|null,
    sku: string|null,
    rendimientoestandar: string|null,
    precioVenta: string|number,
    percentcomision: string|null,
    prioridadcomision: number|null
}

export interface ProductPage {
    page: number;
    pageSize: number;
    hasMore: boolean;
    products: SimpleProduct[];
}

interface ProductCatalogResponse {
    success: boolean;
    data?: ProductPage;
    message?: string;
}


// Consultar las categorías visibles del inventario.
export async function getAllCategorias(): Promise<I_Categorias[]>{
    const url = `/admin/api/inventory/getAllcategorias`; //ApiInventoryController -> getAllcategorias
    const response = await fetch(url);
    if(!response.ok)
        throw new Error('No fue posible consultar las categorias.');
    const data: unknown = await response.json();
    if(!Array.isArray(data))
        throw new Error('La respuesta de categorías no tiene el formato esperado.');
    return data as I_Categorias[];
}

// Consultar las categorías visibles del inventario.
export async function listSimpleProducts(page = 1, categoriaId: number | null = null, search:string = ''): Promise<ProductPage>{
    const params = new URLSearchParams({page: String(page)});
    if(categoriaId!=null)params.set('categoriaId', String(categoriaId));
    if(search)params.set('search', search);

    const url = `/admin/api/inventory/listSimpleProducts?${params.toString()}`; //ApiInventoryController -> listSimpleProducts
    const response = await fetch(url);
    const result = await response.json() as ProductCatalogResponse;
    if(!response.ok || !result.success || !result.data || !Array.isArray(result.data.products))
        throw new Error('No fue posible consultar las productos.');
    return result.data;
}
