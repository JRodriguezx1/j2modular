
interface CarritoItemC {
    idfactura?: string,  //para creditos
    idproducto?: string,
    nombreproducto: string,
    cantidad: string,
    costo: string,
    valorunidad: string,
    impuesto: string,
    base: string,
    valorimp: string,
    subtotal: string,
    total: string,
}

interface cuota {
    id_credito: string,
    cajaid: string,
    cierrecaja_id: string,
    valorpagado: string,
    numerocuota: string,
    fechapagado: string,
}

interface clienteCredito {
    nombre: string,
    apellido: string,
    tipodocumento: string,
    identificacion: string,
    telefono: string,
    totaldebe: string,
    email: string
}

interface direccionCliente{
    ciudad: string,
    direccion: string,
}

interface usuarioVendedor{
    nombre: string,
    apellido: string,
    cedula: string,
    movil: string,
    nickname: string
}

interface creditoSeparado{
    negocio: string,
    sucursal:string,
    nit: string,
    direccion: string,
    telefono: string,
    email: string,
    www: string,
    logo: string,
    num_orden: string,
    id: string,
    fechainicio: string,
    fechafin: string,
    idestadocreditos: string,
    capital: string,
    descuento: string,
    interestotal: string,
    interesxcuota: string,
    valorinterestotal: string,
    montototal: string,
    nombrecliente: string,
    nota: string,
    saldopendiente: string,
    abonodecuotas: string,
    valorimpuestototal: string,
    productos: CarritoItemC[],
    cuotas: cuota[],
    cliente: clienteCredito,
    direccionCliente?: direccionCliente,
    usuario?: usuarioVendedor
}
