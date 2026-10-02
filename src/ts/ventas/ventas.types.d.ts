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

type productsapi = {
      id:string,
      idcategoria: string,
      idunidadmedida: string,
      nombre: string,
      foto: string,
      habilitarventa: string,
      impuesto: string,  //porcentaje(%) de impuesto
      marca: string,
      tipoproducto: string, // 0 = simple,  1 = compuesto
      tipoproduccion: string, //0 = inmediato, 1 = construccion (aplica para productos compuestos)
      codigo: string,
      unidadmedida: string,
      descripcion: string,
      peso: string,
      medidas: string,
      color: string,
      funcion: string,
      uso:string,
      fabricante: string,
      garantia: string,
      stock: string,
      stockminimo: string,
      promediostock?:string,
      categoria: string,
      rendimientoestandar: string,
      sku:string,
      precio_compra: string,
      precio_venta: string,
      precio_original?: string,
      percentcomision: number,
      prioridadcomision: string,  //0 = usuario,  1 = producto
      fecha_ingreso: string,
      estado: string,
      visible: string,
      preciosadicionales:{id:string, idproducto:string, precio:string, estado:string, created_at:string}[],
      insumos: insumo[]
    };


//detalle de factura
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
interface DataInvoice {
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

interface divisa {
    id: number,
    nombre: string,
    codigo: string,
    simbolo: string
}
