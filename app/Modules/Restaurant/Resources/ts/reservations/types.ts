export interface RestaurantZone {
    id: number;
    name: string;
    active: boolean;
}

export interface AvailableTable {
    id: number;
    resourceId: number;
    name: string;
    capacity: number;
    zoneId: number | null;
    shape: string | null;
}

export interface AvailableTablesResponse {
    success: boolean;
    tables?: AvailableTable[];
    message?: string;
}

export interface ReservationsPageData {
    zones: RestaurantZone[];
}

export interface Customer {
    id: number;
    fullName: string;
    identification: string | null;
    phone: string | null;
    email: string | null;
}

export interface CustomersResponse {
    success: boolean;
    customers?: Customer[];
    message?: string;
}

export interface CreateReservationData {
    clientId: number;
    numberOfPeople: number;
    startDate: string;
    endDate: string;
    resourceId: number | null;
    observations: string | null;
}

export interface CreateReservationResponse {
    success: boolean;
    reservationId?: number;
    message?: string;
}

////////// interfaz para el detalle de la reserva
export interface ReservationResource {
    id: number;
    name: string;
    capacity: number;
    zoneId: number | null;
    zoneName: string | null;
    shape: string | null;
}

export interface ReservationDetail {
    id: number;
    clientId: number;
    clientName: string;
    numberOfPeople: number;
    startDate: string;
    endDate: string;
    status: string;
    observations: string | null;
    resources: ReservationResource[];
}

export interface ReservationDetailResponse {
    success: boolean;
    reservation?: ReservationDetail;
    message?: string;
}


//detalle de factura
interface insumo {
    id:string, 
    id_producto:string, 
    id_subproducto:string, 
    id_unidadmedida:string, 
    insumoprocesado:string, 
    cantidadsubproducto:string, 
    costo:string, 
    precio_compra:string, 
    sku:string, 
    stock:string, 
    unidadmedida:string,
    nombre:string, 
    grupos_insumos:{id:string, nombre:string, minimo:string, maximo:string, tipo:string}|null, 
    seleccionado:string, 
    permite_aumentar:string,
    precio_extra:string
}
interface MedioPago {
    id: string;
    mediopago: string;
    estado: string;
    valor?: number;
    nick?: string;
}
interface ConsumidorFinal {
    identification_number: string,
    name: string,
    phone: string | null,
    address: string | null,
    email: string | null,
    municipality_id: string | null
}
interface Cliente {
    id: string;
    nombre: string;
    apellido: string;
    tipodocumento: string;
    identificacion: string;
    telefono: string;
    email: string;
    fecha_nacimiento: string | null;
    total_compras: string | null;
    ultima_compra: string | null;
    totaldebe: string | null;
    limitecredito: string | null;
    data1: string | null;
    created_at: string;
}
interface CarritoItem {
    id: string;
    idproducto: string;
    tipoproducto: string;
    tipoproduccion: string;
    idcategoria?: string;
    foto: string;
    nombreproducto: string;
    rendimientoestandar: string;
    costo: string;
    valorunidad: number;
    cantidad: number;
    stock?: number;
    promediostock: number;
    prioridadcomision: string;
    percentcomision: number; 
    valorcomision: number;
    subtotal: number;
    base: number;
    impuesto: string;
    valorimp: number;
    descuento: number;
    total: number;
    insumos:insumo[];
}
interface Resolution {
    consecutivoremplazo: string | null;
    electronica: string | null;
    estado: string;
    fechafin: string | null;
    fechainicio: string | null;
    id: string;
    id_sucursalid: string;
    idcompania: string;
    idnegocio: string;
    idtipofacturador: string;
    mostrarimpuestodiscriminado: string;
    mostrarresolucion: string;
    nombre: string;
    prefijo: string;
    rangofinal: string;
    rangoinicial: string;
    resolucion: string;
    siguientevalor: number;
    tokenfacturaelectronica: string | null;
}
export interface DataInvoice {
    host: string;
    negocio: string;
    sucursal:string;
    nit: string;
    direccion: string;
    telefono: string;
    email: string;
    www: string;
    logo: string;
    num_orden: number;
    tipoFactura: string;
    textFactura: string;
    prefijo: string;
    consecutivo: string;
    fechaPago: string;
    caja: string;
    vendedor: string;
    consumidorFinal: ConsumidorFinal;
    cliente: Cliente;
    items: CarritoItem[];
    mediospago: MedioPago[];
    tipoventa: string;
    subtotal: string;
    base: string;
    valorimpuestototal: string;
    descuento: string;
    total: string;
    observacion: string;
    resolucion: Resolution;
    cufe?: string;
    link?: string;
}