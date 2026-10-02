type Align = 'left' | 'center' | 'right';
const canvas = document.createElement('canvas');

class EscPosBuilder {

    protected content = '';
    protected bytes: number[] = [];

    protected write(...bytes: number[]) {
        this.bytes.push(...bytes);
    }

   // Comando Reset: Limpia formatos previos
    reset() {
        // ESC @  version bytes
        this.write(0x1B, 0x40);

        this.content += '\x1B\x40';
        this.setAlign('left');
        // Bold OFF
        this.boldOff();
        // Tamaño normal
        this.normalSize();
    }

    text(text: string = '') {
        const encoder = new TextEncoder();
        const encoded = encoder.encode(text);
        this.bytes.push(...encoded);
        this.write(0x0A); // salto línea

        //version string
        this.content += text + '\n';
    }

    // Agregamos Tamaño de texto (Doble ancho y alto)
    bigText(text: string, bold = false) {
        if(bold)this.boldOn();
        this.doubleSize();
        this.text(text);
        this.normalSize();
        if(bold)this.boldOff();
    }

    line() {
        this.text('-'.repeat(42));
    }

    // 1. Forzar que el centro sea una operación aislada y limpia
    center(text: string) {
        this.setAlign('center');         // Activar centro
        this.text(text);                 // Escribir texto y saltar (imprime el buffer)
        this.setAlign('left');           // Volver a izquierda INMEDIATAMENTE
    }

    setAlign(mode: 'left' | 'center' | 'right') {
        const map = {
            left: 0,
            center: 1,
            right: 2
        };
        // ESC a n  version bytes
        this.write(0x1B, 0x61, map[mode]);
        // ESC a n  version string
        this.content += `\x1B\x61${String.fromCharCode(map[mode])}`;
    }

    boldOn(){
        this.content += '\x1B\x45\x01';  //version string
        // ESC E 1
        this.write(0x1B, 0x45, 0x01);  //version bytes
    }

    boldOff(){
        this.content += '\x1B\x45\x00'; //version string
         // ESC E 0
        this.write(0x1B, 0x45, 0x00);  //version bytes
    }

    bold(text: string) {
        this.boldOn();
        this.text(text);
        this.boldOff();
    }

    normalSize(){
        this.content += '\x1D\x21\x00';  //version string
        // GS ! 0
        this.write(0x1D, 0x21, 0x00);  //version bytes
    }

    doubleHeight() {
        this.content += '\x1D\x21\x01';
        this.write(0x1D, 0x21, 0x01);
    }

    doubleWidth() {
        this.content += '\x1D\x21\x10';
        this.write(0x1D, 0x21, 0x10);
    }

    doubleSize() {
        // GS ! 17
        // doble ancho + doble alto
        this.content += '\x1D\x21\x11';
        this.write(0x1D, 0x21, 0x11);
    }

    feed(lines: number = 3) {
        this.content += '\n'.repeat(lines); //version string
        for(let i = 0; i < lines; i++)this.write(0x0A);  //version bytes
    }

    cut(feed:number = 5) {
        this.feed(feed);
        this.content += '\x1D\x56\x00';  //version string
        // GS V 0
        this.write(0x1D, 0x56, 0x00);  //version bytes
    }

    row(left: string, right: string, width = 42) {
        const spaces = width - left.length - right.length;
        this.text(left+' '.repeat(Math.max(1, spaces))+right);
    }

    wrap(text: string, width = 42): string[] {
        const words = text.split(' ');
        const lines: string[] = [];
        let current = '';
        for(const word of words) {
            if((current + word).length > width) {
                lines.push(current.trim());
                current = '';
            }
            current += word + ' ';
        }
        if(current.trim())lines.push(current.trim());
        return lines;
    }

    column(text: string, width: number, align: 'left' | 'center' | 'right' = 'left'){
        // evitar overflow
        if(text.length > width) {
            text = text.substring(0, width);
        }
        switch(align){
            case 'left':
                return text.padEnd(width, ' ');
            case 'right':
                return text.padStart(width, ' ');
            case 'center':
                const totalSpaces = width - text.length;
                const left = Math.floor(totalSpaces / 2);
                const right = totalSpaces - left;
                return (' '.repeat(left)+text +' '.repeat(right));
        }
    }

    row3(col1: string, col2: string, col3: string, align: Record<string, Align> = {col1: 'left', col2: 'right', col3: 'right'}, whidth:number[] = [8, 16, 18]){
        const line = this.column(col1, whidth[0], align.col1) + this.column(col2, whidth[1], align.col2) + this.column(col3, whidth[2], align.col3);
        this.text(line);
    }


    qr(data: string, size = 6, errorLevel: 'L' | 'M' | 'Q' | 'H' = 'M') { 
        const errorMap = { L: 48, M: 49, Q: 50, H: 51 }; 
        const store_len = data.length + 3; 
        const pL = store_len % 256; 
        const pH = Math.floor(store_len / 256); 
        //ENCODE DATA
        const encoder = new TextEncoder(); 
        const encoded = encoder.encode(data); 
        /* ========================================= 
        TAMAÑO QR 
        ========================================= 
        */ 
        // GS ( k 3 0 49 67 n 
        // tamaño módulo 
        this.content +='\x1D\x28\x6B\x03\x00\x31\x43'+String.fromCharCode(size); 
        this.write(0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x43, size); //version bytes 
        /* ========================================= 
        NIVEL ERROR ========================================= 
        */ 
        // GS ( k 3 0 49 69 n 
        this.content +='\x1D\x28\x6B\x03\x00\x31\x45'+String.fromCharCode(errorMap[errorLevel]); 
        this.write(0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x45, errorMap[errorLevel]); //version bytes 
        /* ========================================= 
        ALMACENAR DATA 
        ========================================= 
        */ 
        this.content += '\x1D\x28\x6B' + String.fromCharCode(pL) + String.fromCharCode(pH) + '\x31\x50\x30' + data; 
        this.write(0x1D, 0x28, 0x6B, pL, pH, 0x31, 0x50, 0x30); //version bytes 
        this.bytes.push(...encoded); 
        /* ========================================= 
        IMPRIMIR QR 
        ========================================= 
        */ 
       this.content += '\x1D\x28\x6B\x03\x00\x31\x51\x30'; 
       this.write(0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x51, 0x30); 
    }
    

    canvasToEscPos(canvas: HTMLCanvasElement) {
        const ctx = canvas.getContext('2d')!;
        const width = canvas.width;
        const height = canvas.height;
        const image = ctx.getImageData(0, 0, width, height);
        const data = image.data;
        const bytes = [];
        bytes.push(0x1D, 0x76, 0x30, 0x00, width / 8, 0x00, height & 0xff, (height >> 8) & 0xff);
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x += 8) {
                let byte = 0;
                for (let bit = 0; bit < 8; bit++) {
                    const px = x + bit;
                    if (px >= width) continue;
                    const i = (y * width + px) * 4;
                    const r = data[i];
                    const g = data[i + 1];
                    const b = data[i + 2];
                    const gray = r * 0.299 + g * 0.587 + b * 0.114;
                    if (gray < 160) byte |= (1 << (7 - bit));
                }
                bytes.push(byte);
            }
        }
        return bytes;
    }


    async qrRaster(text:string, correction:string = 'M'){
        return new Promise<number[]>((resolve, reject)=>{
             const self = this; // Guardamos la referencia
            QRCode.toCanvas(
                canvas,
                text,
                {
                    width: 256,
                    margin: 1,
                    errorCorrectionLevel: correction
                },
                function (error:any) {
                    if (error) {
                        console.error(error);
                        reject(error);
                        return;
                    }
                    console.log('QR generado');
                    const qrBytes = self.canvasToEscPos(canvas);
                    resolve(qrBytes);
                }
            );
        });
    }


    async image(src: string, options: { width?: number; align?: 'left' | 'center' | 'right'; } = {}){
        const {
            width = 256,
            align = 'center'
        } = options;

        return new Promise<void>((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => {
                // calcular alto proporcional
                const ratio = img.height / img.width;
                const height = Math.floor(width * ratio);
                // canvas temporal
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(width / 8) * 8;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                if(!ctx){
                    reject('No canvas context');
                    return;
                }
                // fondo blanco
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                // mejora contraste
                ctx.filter = 'grayscale(100%) contrast(180%)';
                // dibujar imagen
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                // alineación
                this.setAlign(align);
                // convertir a ESC/POS
                const imgBytes = this.canvasToEscPos(canvas);
                // agregar bytes
                this.bytes.push(...imgBytes);
                this.feed(1);
                this.setAlign('left');
                resolve();
            };
            img.onerror = reject;
            img.src = src;
        });
    }


    build(buildBytes:boolean): string | Uint8Array  {
        if(buildBytes){
            return new Uint8Array(this.bytes);
        }else{
            return this.content;
        }
    }
}


class InvoiceTicketBuilder extends EscPosBuilder {

    private invoice: DataInvoice;
    constructor(invoice: DataInvoice) {
        super();
        this.reset();
        this.invoice = invoice;
    }

    async generate(buildBytes:boolean):Promise<string | Uint8Array> {
        await this.header();
        if(this.invoice.tipoFactura === '1')this.customer(); //solo factura electronica
        this.datosFactura();
        this.cliente();
        this.items();
        this.totals();
        this.payments();
        if(this.invoice.tipoFactura === '1')this.infoResolution();  //solo factura electronica
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
        this.feed(1);
    }

    private customer() { //cliente factura electronica
        this.center(`Cliente: ${this.invoice.consumidorFinal.name}`);
        this.center(`NIT: ${this.invoice.consumidorFinal.identification_number}`);
    }

    private datosFactura(){
        this.boldOn();
        this.center(`${this.invoice.textFactura}`);
        this.boldOff();
        this.bold(`Factura: ${this.invoice.prefijo.trim()}${this.invoice.consecutivo.trim()}`);
        this.text(`Fecha pago: ${this.invoice.fechaPago.trim()}`);
        this.text(`Forma de pago: ${this.invoice.tipoventa.trim()}`);
        this.text(`Caja: ${this.invoice.caja.trim()}`);
        this.text(`Vendedor: ${this.invoice.vendedor.trim()}`);
        this.line();
    }

    private cliente(){
        this.feed(1);
        this.center(`Cliente: ${this.invoice.cliente.nombre.trim()} ${this.invoice.cliente.apellido.trim()}`);
        this.center(`Documento: ${this.invoice.cliente.identificacion?.trim()??''}`);
        this.center(`Telefono: ${this.invoice.cliente.telefono.trim()}`);
        this.center(`Direccion: ${this.invoice.cliente.data1?.trim()||' - '}`);
        this.feed(1);
    }

    private items() {
        this.line();
        this.feed(1);
        this.boldOn();
        this.row3('CANT', 'UND', 'TOTAL', {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        this.boldOff();
        for(const item of this.invoice.items) {
            for(const line of this.wrap(item.nombreproducto.trim()))this.center(line);
            //this.row(`${item.cantidad}  x  $${item.valorunidad.toLocaleString()}`, '$'+item.total.toLocaleString());
            this.row3(item.stock?.toString()||item.cantidad.toString(), '$'+item.valorunidad.toLocaleString(), '$'+item.total.toLocaleString(), {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        }
    }

    private totals() {
        this.line();
        this.feed(1);
        this.setAlign('right');
        this.text(`SUBTOTAL: $${Number(this.invoice.subtotal.trim()).toLocaleString()}`);
        this.text(`IMPUESTO: $${Number(this.invoice.valorimpuestototal.trim()).toLocaleString()}`);
        this.text(`DESCUENTO: $${Number(this.invoice.descuento.trim()).toLocaleString()}`);
        this.bigText(`TOTAL: $${Number(this.invoice.total.trim()).toLocaleString()}`, true);
    }

    private payments() {
        this.feed(2);
        this.doubleHeight();
        for(const pago of this.invoice.mediospago) {
            //this.text(`${pago.mediopago}: $${pago.valor?.toLocaleString()}`);
            this.row3(' ', pago.mediopago+':', ` $${pago.valor?.toLocaleString()}`, {col1:'right', col2:'right', col3:'left'}, [10, 17, 15]);
        }
        this.normalSize();
        this.feed(1);
        this.setAlign('left');
        this.text(`Observacion: ${this.invoice.observacion}`);
        this.line();
        this.feed(2);
    }

    private infoResolution(){
        this.setAlign('left');
        this.text(`Resolucion: ${this.invoice.resolucion.resolucion}, Rango desde: ${this.invoice.resolucion.rangoinicial} hasta ${this.invoice.resolucion.rangofinal}`);
        this.text(`CUFE: ${this.invoice.cufe || 'fe9c733f32770f5fcc4ef954f9ef663c54c752e6c07bdc144bb00627faadf9f648818da3e96ef8293547140fb1970d22'}`);
        this.feed(2);
    }

    private async footer() {
        this.setAlign('center');
        //this.qr(this.invoice.link || 'www.j2softwarepos.com/', 8, 'M');
        const qrBytes = await this.qrRaster(this.invoice.link || this.invoice.www, 'M');
        this.bytes.push(...qrBytes);
        this.feed(2);
        this.boldOn();
        this.center('GRACIAS POR SU COMPRA');
        this.boldOff();
        this.feed(2);
        this.center(`© ${new Date().getFullYear()} J2 Software POS Multisucursal`);
        this.center('www.j2softwarepos.com');
    }
    //copy /b ticket.bin "\\localhost\\CAJA"
}



class InvoiceTicketBuilder2 extends EscPosBuilder {

    private invoice: DataInvoice;
    constructor(invoice: DataInvoice) {
        super();
        this.reset();
        this.invoice = invoice;
    }

    async generate(buildBytes:boolean):Promise<string | Uint8Array> {
        await this.header();
        if(this.invoice.tipoFactura === '1')this.customer(); //solo factura electronica
        this.datosFactura();
        this.cliente();
        this.items();
        this.totals();
        this.payments();
        if(this.invoice.tipoFactura === '1')this.infoResolution();  //solo factura electronica
        await this.footer();
        this.cut(1);
        return this.build(buildBytes);
    }

    private async header() {
        await this.image(`/build/img/${this.invoice.logo}`, {
            width: 220,
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
        //this.feed(1);
    }

    private customer() { //cliente factura electronica
        this.center(`Cliente: ${this.invoice.consumidorFinal.name}`);
        this.center(`NIT: ${this.invoice.consumidorFinal.identification_number}`);
    }

    private datosFactura(){
        this.boldOn();
        this.center(`${this.invoice.textFactura}`);
        this.boldOff();
        this.bold(`Factura: ${this.invoice.prefijo.trim()}${this.invoice.consecutivo.trim()}`);
        this.text(`Fecha pago: ${this.invoice.fechaPago.trim()}`);
        this.text(`Forma de pago: ${this.invoice.tipoventa.trim()}`);
        //this.text(`Caja: ${this.invoice.caja.trim()}`);
        this.text(`Vendedor: ${this.invoice.vendedor.trim()}`);
        this.line();
    }

    private cliente(){
        //this.feed(1);
        this.center(`Cliente: ${this.invoice.cliente.nombre.trim()} ${this.invoice.cliente.apellido.trim()}`);
        this.center(`Documento: ${this.invoice.cliente.identificacion?.trim()??''}`);
        this.center(`Telefono: ${this.invoice.cliente.telefono.trim()}`);
        this.center(`Direccion: ${this.invoice.cliente.data1?.trim()||' - '}`);
        //this.feed(1);
    }

    private items() {
        this.line();
        //this.feed(1);
        this.boldOn();
        this.row3('CANT', 'UND', 'TOTAL', {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        this.boldOff();
        for(const item of this.invoice.items) {
            for(const line of this.wrap(item.nombreproducto.trim()))this.center(line);
            //this.row(`${item.cantidad}  x  $${item.valorunidad.toLocaleString()}`, '$'+item.total.toLocaleString());
            this.row3(item.stock?.toString()||item.cantidad.toString(), '$'+item.valorunidad.toLocaleString(), '$'+item.total.toLocaleString(), {col1:'left', col2:'right', col3:'right'}, [8, 16, 18]);
        }
    }

    private totals() {
        this.line();
        this.feed(1);
        this.setAlign('right');
        this.text(`SUBTOTAL: $${Number(this.invoice.subtotal.trim()).toLocaleString()}`);
        this.text(`IMPUESTO: $${Number(this.invoice.valorimpuestototal.trim()).toLocaleString()}`);
        this.text(`DESCUENTO: $${Number(this.invoice.descuento.trim()).toLocaleString()}`);
        this.bigText(`TOTAL: $${Number(this.invoice.total.trim()).toLocaleString()}`, true);
    }

    private payments() {
        this.feed(1);
        //this.doubleHeight();
        this.normalSize();
        for(const pago of this.invoice.mediospago) {
            //this.text(`${pago.mediopago}: $${pago.valor?.toLocaleString()}`);
            this.row3(' ', pago.mediopago+':', ` $${pago.valor?.toLocaleString()}`, {col1:'right', col2:'right', col3:'left'}, [10, 17, 15]);
        }
        //this.normalSize();
        this.feed(1);
        this.setAlign('left');
        this.text(`Observacion: ${this.invoice.observacion}`);
        this.line();
        //this.feed(2);
    }

    private infoResolution(){
        this.setAlign('left');
        this.text(`Resolucion: ${this.invoice.resolucion.resolucion}, Rango desde: ${this.invoice.resolucion.rangoinicial} hasta ${this.invoice.resolucion.rangofinal}`);
        this.text(`CUFE: ${this.invoice.cufe || 'fe9c733f32770f5fcc4ef954f9ef663c54c752e6c07bdc144bb00627faadf9f648818da3e96ef8293547140fb1970d22'}`);
        this.feed(2);
    }

    private async footer() {
        this.setAlign('center');
        //this.qr(this.invoice.link || 'www.j2softwarepos.com/', 8, 'M');
        const qrBytes = await this.qrRaster(this.invoice.link || this.invoice.www, 'M');
        this.bytes.push(...qrBytes);
        this.feed(1);
        this.boldOn();
        this.center('GRACIAS POR SU COMPRA');
        this.boldOff();
        this.feed(1);
        this.center(`© ${new Date().getFullYear()} J2 Software POS Multisucursal`);
        this.center('www.j2softwarepos.com');
    }
    //copy /b ticket.bin "\\localhost\\CAJA"
}



