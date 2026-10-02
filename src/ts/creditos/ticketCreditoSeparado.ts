
class ticketCreditoSeparadoBuilder extends EscPosBuilder{
    private invoice: creditoSeparado;
    constructor(invoice: creditoSeparado) {
        super();
        this.reset();
        this.invoice = invoice;
    }

    async generate(buildBytes:boolean):Promise<string | Uint8Array> {
        await this.header();
        //if(this.invoice.tipoFactura === '1')this.customer(); //solo factura electronica
        this.datosCreditoSeparado();
        this.cliente();
        this.items();
        this.totals();
        this.payments();
        //if(this.invoice.tipoFactura === '1')this.infoResolution();  //solo factura electronica
        await this.footer();
        this.cut();
        return this.build(buildBytes);
    }

    private async header() {
        await this.image(`/build/img/${this.invoice.logo}`, {
            width: 256,
            align: 'center'
        });
        this.boldOn();
        this.center(this.invoice.negocio.toUpperCase());
        this.boldOff();
        this.feed(1);
        this.center(`NIT: ${this.invoice.nit.trim()}`);
        this.center(`SEDE: ${this.invoice.sucursal.trim().toUpperCase()}`);
        this.center(this.invoice.direccion.trim());
        this.center(`TEL: ${this.invoice.telefono.trim()}`);
        this.center(`Email: ${this.invoice.email.trim()}`);
        this.feed(1);
    }

    /*private customer() { //cliente factura electronica
        this.center(`Cliente: ${this.invoice.consumidorfinal.name}`);
        this.center(`NIT: ${this.invoice.consumidorfinal.identification_number}`);
    }*/

    private datosCreditoSeparado(){
        this.line();
        this.setAlign('center');
        this.text(`Fecha inicio: ${this.invoice.fechainicio.trim()}`);
        this.text(`Fcha de finalizacion: ${this.invoice.fechafin.trim()}`);
        this.text(`Vendedor: ${this.invoice.usuario?.nombre.trim()} ${this.invoice.usuario?.apellido.trim()}`);
        this.bold(`CREDITO No: ${this.invoice.num_orden.trim()}`);
        this.line();
    }

    private cliente(){
        this.feed(1);
        this.center(`Cliente: ${this.invoice.cliente.nombre.trim()} ${this.invoice.cliente.apellido.trim()}`);
        this.center(`Documento: ${this.invoice.cliente.identificacion?.trim()??''}`);
        this.center(`Telefono: ${this.invoice.cliente.telefono.trim()}`);
        this.center(`Direccion: ${this.invoice?.direccionCliente?.direccion?.trim()||' - '}`);
        this.feed(1);
    }

    private items() {
        this.feed(1);
        this.boldOn();
        this.row3('CANT', 'UND', 'TOTAL', {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        this.boldOff();
        for(const item of this.invoice.productos) {
            for(const line of this.wrap(item.nombreproducto.trim()))this.center(line);
            //this.row(`${item.cantidad}  x  $${item.valorunidad.toLocaleString()}`, '$'+item.total.toLocaleString());
            this.row3(item.cantidad.toString(), '$'+Number(item.valorunidad).toLocaleString(), '$'+Number(item.total).toLocaleString(), {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        }
    }

    private totals() {
        this.line();
        this.feed(1);
        this.setAlign('right');
        this.text(`CREDITO TOTAL: $${Number(this.invoice.capital.trim()).toLocaleString()}`);
        this.text(`Interes: $${Number(this.invoice.valorinterestotal.trim()).toLocaleString()}`);
        this.text(`TOTAL ABONOS: $${Number(this.invoice.abonodecuotas.trim()).toLocaleString()}`);
        this.text(`Descuento: $${Number(this.invoice.descuento.trim()).toLocaleString()}`);
        this.line();
        this.boldOn();
        this.center(`SALDO PENDIENTE: $${Number(this.invoice.saldopendiente.trim()).toLocaleString()}`);
        this.boldOff();
    }

    private payments() {
        this.feed(2);
        this.setAlign('center');
        this.text('PAGOS');
        for(const cuota of this.invoice.cuotas)
            this.text(`${cuota.fechapagado}: $${cuota.valorpagado?.toLocaleString()}`);
        
        this.normalSize();
        this.feed(1);
        this.setAlign('left');
        this.text(`Observacion: ${this.invoice.nota}`);
        this.feed(2);
    }

    /*private infoResolution(){
        this.setAlign('left');
        this.text(`Resolucion: ${this.invoice.resolucion.resolucion}, Rango desde: ${this.invoice.resolucion.rangoinicial} hasta ${this.invoice.resolucion.rangofinal}`);
        this.text(`CUFE: ${this.invoice.cufe || 'fe9c733f32770f5fcc4ef954f9ef663c54c752e6c07bdc144bb00627faadf9f648818da3e96ef8293547140fb1970d22'}`);
        this.feed(2);
    }*/

    private async footer() {
        this.setAlign('center');
        this.feed(2);
        this.text('***Para poder realizar un reclamo o devolución debe de presentar este ticket ***');
        this.boldOn();
        this.feed(1);
        this.center('GRACIAS POR SU COMPRA');
        this.boldOff();
        this.feed(2);
    }
}



//////////////////  DISEÑO DE TICKET PARA COMPROBANTE DE ABONOS  ///////////////////
class ticketAbonoBuilder extends EscPosBuilder{
    private invoice: creditoSeparado;
    constructor(invoice: creditoSeparado) {
        super();
        this.reset();
        this.invoice = invoice;
    }

    async generate(buildBytes:boolean):Promise<string | Uint8Array> {
        await this.header();
        this.cliente();
        this.totals();
        await this.footer();
        return this.build(buildBytes);
    }

    private async header() {
        await this.image(`/build/img/${this.invoice.logo}`, {
            width: 256,
            align: 'center'
        });
        this.boldOn();
        this.center(this.invoice.negocio.toUpperCase());
        this.boldOff();
        this.feed(1);
        this.center(`NIT: ${this.invoice.nit.trim()}`);
        this.center(`SEDE: ${this.invoice.sucursal.trim().toUpperCase()}`);
        this.center(this.invoice.direccion.trim());
        this.center(`TEL: ${this.invoice.telefono.trim()}`);
        this.center(`Email: ${this.invoice.email.trim()}`);
        this.feed(1);
        this.line();
    }

    private cliente(){
        this.feed(1);
        this.center(`Cliente: ${this.invoice.cliente.nombre.trim()} ${this.invoice.cliente.apellido.trim()}`);
        this.center(`Documento: ${this.invoice.cliente.identificacion?.trim()??''}`);
        this.center(`Telefono: ${this.invoice.cliente.telefono.trim()}`);
        this.center(`Direccion: ${this.invoice?.direccionCliente?.direccion?.trim()||' - '}`);
        this.feed(1);
    }

    private totals() {
        this.line();
        this.feed(1);
        this.setAlign('center');
        this.bold(`CREDITO No: ${this.invoice.num_orden.trim()}`);
        this.feed(2);
        for(const cuota of this.invoice.cuotas){
            this.text(`${cuota.fechapagado}  -  Abono No: ${cuota.numerocuota.trim()}`);
            this.text(`Valor pagado: $${cuota.valorpagado?.toLocaleString()}`);
        }
    }

    private async footer() {
        this.normalSize();
        this.cut();
    }

}

