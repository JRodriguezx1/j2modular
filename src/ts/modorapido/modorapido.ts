(()=>{
    if(document.querySelector('.modorapido')){

        const POS = (window as any).POS;

        interface Item {
            id_impuesto: number,
            facturaid: number,
            basegravable: number,
            valorimpuesto: number
        }


        const selectCliente = POS.gestionClientes.selectCliente;
        const dirEntrega = POS.gestionClientes.dirEntrega;
        const btnCaja = document.querySelector('#caja') as HTMLSelectElement; //select de la caja en el modal pagar
        const btnTipoFacturador = document.querySelector('#facturador') as HTMLSelectElement; //select del consecutivo o facturador en el modal de pago
        const btnfacturar = document.querySelector('#btnfacturar');
        const btnguardar = document.querySelector('#btnguardar');
        const btnPagar = document.getElementById('btnPagar') as HTMLInputElement;
        const miDialogoAddCliente = POS.gestionClientes.miDialogoAddCliente;
        const miDialogoGuardar = document.querySelector('#miDialogoGuardar') as any;
        const miDialogoFacturarA = POS.gestionarAdquiriente.miDialogoFacturarA;
        const miDialogoFacturar = document.querySelector('#miDialogoFacturar') as any;
        const totalUnidades = document.querySelector('#totalUnidades') as HTMLSpanElement;
        const tablaMR = document.getElementById('tablaVenta') as HTMLBodyElement;

        let factimpuestos:Item[] = [];
        let carrito:CarritoItem[]=[];
        let allproducts:productsapi[] = [];
        let filteredData: {id:string, text:string, tipo:string, tipoproducto:string, tipoproduccion:string, sku:string, unidadmedida:string, precio:string}[];   //tipoproducto = 0 es producto simple,  1 = compuesto,  si no viene es subproducto, tipo=0 es producto(simple o compuesto), tipo=1 es subproducto
        const valorTotal = {porcentgananciauser: 0, valorgananciauser: 0, subtotal: 0, base: 0, valorimpuestototal: 0, dctox100: 0, descuento: 0, idtarifa: 0, valortarifa: 0, total: 0}; //datos global de la venta
        let tarifas:{id:string, idcliente:string, nombre:string, valor:string}[] = [];
        //const dataCredit = {capital:0, abonoinicial:0, saldopendiente:0, cantidadcuotas:0, interes:'0', interestotal:0, valorinterestotal:0, montototal:0, descuentocredito: 0};

        const constImp: {[key:string]: number} = {};
        constImp['excluido'] = 0;
        constImp['0'] = 0;  //exento de iva, tarifa 0%
        constImp['5'] = 0.0476190476190476; //iva, tarifa al 5%,  Bienes/servicios al 5
        constImp['8'] = 0.0740740740740741; //inc, tarifa al 8%,  impuesto nacional al consumo
        constImp['16'] = 0.1379310344827586; //iva, tarifa al 16%,  contratos firmados con el estado antes de ley 1819
        constImp['19'] = 0.1596638655462185; //iva, tarifa al 19%,  tarifa general

        let  tipoventa:string="Contado";
        const mapMediospago = new Map();

        const mediosPagoDBMAP = new Map<string, string>(  //se usa para imprimir los medios de pago en el servidor de impresion
            mediosPagoDB.map(m => [m.id, m.mediopago]) //mediosPagoDB se declara en app.ts el cual viene del <script> en index.php que convierte el array de medios de pago de php a js.
        );

        POS.gestionClientes.clientes();  //inicializa modulo de clientes
        document.addEventListener("click", cerrarDialogoExterno);

        (async ()=>{
            try {
                const url = "/admin/api/allproducts"; //llamado a la API REST en el controlador almacencontrolador para treaer todas los productos simples y compuestos
                const respuesta = await fetch(url);
                const resultado:productsapi[] = await respuesta.json();
                filteredData = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1').map(item => ({ id: item.id, text: item.nombre, tipo:item.tipoproducto??'1', tipoproducto: item.tipoproducto, tipoproduccion: item.tipoproduccion, sku: item.sku, unidadmedida: item.unidadmedida, precio:item.precio_venta }));
                activarselect2();
                allproducts = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1');
            } catch (error) {
                console.log(error);
            }
        })();

        activarselect2();
        function activarselect2(){
            ($('#articulo') as any).select2({ 
                width: '100%',
                data: filteredData,
                placeholder: "Buscar producto...",
                maximumSelectionLength: 1,
            });  
        }


        let barcode = "";
        let lastKeyTime = Date.now();
        window.addEventListener('keydown', (e: KeyboardEvent) => {
            let estado = true;
            const currentTime = Date.now();
            const target = e.target as HTMLElement;
            const cobrar:boolean = miDialogoFacturar?.open;

            if (e.key === 'Control')(document.querySelector('#articulo') as HTMLSelectElement).focus();
            if (e.key === 'F8')(document.querySelector('#facturarA') as HTMLButtonElement).click();

            // 1. FILTRO DE ENTRADA mejorado
            // Si el foco está en un input que NO sea el de búsqueda de Select2, ignoramos.
            // El buscador de Select2 suele tener la clase 'select2-search__field'
            const isSelect2Input = $(target).hasClass('select2-search__field');
            const isBody = target.tagName === 'BODY';
            const isButton = target.tagName === 'BUTTON' || $(target).hasClass('btn');

            // Si está escribiendo en un input normal (ej: Notas) y NO es el de Select2, salimos
            if (!isSelect2Input && !isBody && !isButton && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
                if (currentTime - lastKeyTime < 43 && e.key === 'Enter'){
                    e.preventDefault();
                    if(target.classList.contains('inputcantidad')){
                        const inputcantidad = (target as HTMLInputElement);
                        inputcantidad.value = '1';
                        inputcantidad.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    return;
                }
                if(target.classList.contains('inputcantidad')&& e.key === 'Enter'){
                    e.preventDefault();
                    btnfacturar?.dispatchEvent( new Event('click'));
                }
            }

            // 2. LÓGICA DE VELOCIDAD
            if (currentTime - lastKeyTime > 43)barcode = ""; 
            lastKeyTime = currentTime;

            // 3. CAPTURA DEL ENTER
            if (e.key === 'Enter') {
                if (barcode.length >= 3) {
                    e.preventDefault();
                    e.stopImmediatePropagation(); // <--- CRITICO: Detiene a Select2 y a cualquier botón
                    searchBarCode(barcode);
                    barcode = "";
                    return false;
                }
                if(cobrar){
                    e.preventDefault();
                    btnPagar.click();
                }
                barcode = "";
            } else {
                // 4. ACUMULACIÓN
                barcode += e.key;
            }
        }, true); // <--- El "true" activa el modo CAPTURA


        function searchBarCode(barcode:string){
            const itemselected = allproducts.find(x=>x.sku==barcode);
            if(itemselected != undefined){
                const productoConfigurado = structuredClone(itemselected);
                filtrarInsumos(productoConfigurado);
                agregarProducto(itemselected.id, Number(itemselected.precio_venta), productoConfigurado);
            }
        }

        ////// EVENTO AL SELECT ARTICULOS O ITEMS PARA SELECCIONAR EL ITEM Y AÑADIR AL CARRITO ////// 
        $("#articulo").on('change', (e)=>{
            let datos = ($('#articulo') as any).select2('data')[0];
            if(datos){
                const itemselected = allproducts.find(x=>x.id == datos.id)!
                const productoConfigurado = structuredClone(itemselected);
                filtrarInsumos(productoConfigurado);
                agregarProducto(datos.id, datos.precio, productoConfigurado);
                $("#articulo").val('null').trigger('change');
            }
        });


        function agregarProducto(id:string, precio:number = 0, productoConfigurado:productsapi|null) {
            const index = carrito.findIndex(x=>x.idproducto==id && x.valorunidad == precio && mismaConfiguracion(x, productoConfigurado!));

            if(index == -1){  //si el item seleccionado no existe en el carrito, agregarlo.
                //const itemselected = allproducts.find(x=>x.id==id)!;
                const productototal = Number(productoConfigurado?.precio_venta);
                const productovalorimp = productototal*constImp[productoConfigurado?.impuesto??'0']; //si producto.impuesto es null toma el valor de cero
                
                //varia segun la prioridad de la comision
                if(productoConfigurado?.prioridadcomision === '0')  //si el porcentaje es por usuario
                    productoConfigurado.percentcomision = Number(percentComisionUser);
                const valorcomision:number = (productototal*productoConfigurado?.percentcomision!)/100;  

                const item:CarritoItem = {
                    id: '',
                    idproducto: productoConfigurado?.id!,
                    tipoproducto: productoConfigurado?.tipoproducto!,
                    tipoproduccion: productoConfigurado?.tipoproduccion!,
                    idcategoria: productoConfigurado?.idcategoria!,
                    nombreproducto: productoConfigurado?.nombre!,
                    rendimientoestandar: productoConfigurado?.rendimientoestandar!,
                    foto: productoConfigurado?.foto!,
                    costo: productoConfigurado?.precio_compra!,
                    valorunidad: Number(productoConfigurado?.precio_venta),
                    cantidad: 1,
                    stock: 1,
                    promediostock: Number(productoConfigurado?.promediostock),
                    prioridadcomision: productoConfigurado?.prioridadcomision!,
                    percentcomision: productoConfigurado?.percentcomision!,
                    valorcomision: valorcomision,
                    subtotal: productototal, //este es el subtotal del producto
                    base: productototal-productovalorimp,
                    impuesto: productoConfigurado?.impuesto!, //porcentaje de impuesto, es null si es excluido de iva
                    valorimp: productovalorimp,
                    descuento: 0,
                    total: productototal, //valorunidad x cantidad
                    insumos: productoConfigurado?.insumos??[]
                }
                carrito = [...carrito, item];
            }else{ //sumar cantidad cuando el item ya existe 
                sumarcantidad(carrito[index], carrito[index].cantidad+1);
            }
            
            printCarrito();
            valorCarritoTotal();
        }
        

        function mismaConfiguracion(productCarrito: any, producto2: productsapi):boolean {
            const mapaProduct2 = new Map(producto2.insumos.map((ins:any) => [ins.id_subproducto, ins]));
            // Deben ser el mismo producto
            if(productCarrito.idproducto !== producto2.id)return false;

            // Deben tener la misma cantidad de insumos
            if(productCarrito.insumos.length !== producto2.insumos.length)return false;

            for (const insumo1 of productCarrito.insumos) {
                //const insumo2 = producto2.insumos.find((x:any) => x.id_subproducto == insumo1.id_subproducto);
                const insumo2:any = mapaProduct2.get(insumo1.id_subproducto);
                if (!insumo2)return false;
                // Comparar selección
                if (Number(insumo1.seleccionado) !== Number(insumo2.seleccionado))
                    return false;
                // Comparar cantidad
                if (Number(insumo1.cantidadsubproducto) !== Number(insumo2.cantidadsubproducto))
                    return false;
            }
            return true;
        }


        tablaMR?.addEventListener('input', e=>{
            const input = e.target as HTMLInputElement;
            if (!input.classList.contains('inputcantidad')) return;
            const fila = input?.closest('.productselect') as HTMLTableRowElement;
            const indexcarrito = fila.dataset.indexcarrito!;
            const productoCarrito = carrito[Number(indexcarrito)];
            
            if(productoCarrito==undefined)return;
                
            let val = input.value;
            val = val.replace(/[^0-9.]/g, '');
            const partes = val.split('.');
            if(partes.length > 2)val = partes[0]+'.'+partes.slice(1).join('');
            if (val.startsWith('.'))val = '1';
            if (val === '' || isNaN(parseFloat(val))) val = '';

            input.value = val;
            let cantidad = Number(val);
            
            if(cantidad <= 0){
                cantidad = 0;
                productoCarrito.total = 0;
                //carrito = carrito.filter(x=>x.iditem != id);
            }
            fila.children[3].textContent = '$'+(productoCarrito.valorunidad*cantidad).toLocaleString();
            sumarcantidad(productoCarrito, cantidad);
            valorCarritoTotal();
        });

        function sumarcantidad(productoCarrito:CarritoItem, cantidad:number){
            productoCarrito.cantidad = cantidad;
            productoCarrito.stock = cantidad;
            productoCarrito.subtotal = (productoCarrito.valorunidad)*productoCarrito.cantidad;
            productoCarrito.total = productoCarrito.subtotal;
            productoCarrito.valorcomision = (productoCarrito.subtotal*productoCarrito.percentcomision)/100;
            //calculo del impuesto y base por producto en el carrito deventas
            productoCarrito.valorimp = parseFloat((productoCarrito.total*constImp[productoCarrito.impuesto??0]).toFixed(3));
            productoCarrito.base = parseFloat((productoCarrito.total-productoCarrito.valorimp).toFixed(3));
        }


        ////// EVENTO POR TECLADO DE LA TABLA MODO RAPIDO DE ITEMS SELECCIONADOS
        tablaMR.addEventListener('keydown', (e: KeyboardEvent) => {
            const target = e.target as HTMLElement;
            if (target.classList.contains('inputcantidad')) { //Delegation al inputcantidad de la tabla
                const input = target as HTMLInputElement;
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    input.value = String((Number(input.value) || 0) + 1);
                    beep(900);
                    flashCantidad(input, 'up');
                    input.dispatchEvent(new Event('input', { bubbles: true })); // 🔥 reutiliza lógica
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    input.value = String(Math.max(0, (Number(input.value) || 0) - 1));
                    beep(500);
                    flashCantidad(input, 'down');
                    input.dispatchEvent(new Event('input', { bubbles: true })); // 🔥 reutiliza lógica
                }
            }
        });


        document.querySelector('#btnScanner')?.addEventListener('click', ()=>
            (document.querySelector('#articulo') as HTMLSelectElement).focus()
        );


        /////////////////////// evento a la tabla de productos de venta (carrito) //////////////////////////
        tablaMR?.addEventListener('click', (e:Event)=>{
            if((e.target as HTMLElement).classList.contains('eliminarProducto') || (e.target as HTMLElement).tagName == "I"){
                const elementProduct = (e.target as HTMLElement)?.closest('.productselect');
                const indexcarrito = Number((elementProduct as HTMLElement).dataset.indexcarrito!);
                carrito.splice(indexcarrito, 1);
                printCarrito();
                valorCarritoTotal();
            }
        });


        function printCarrito(){
            while(tablaMR.firstChild)tablaMR.removeChild(tablaMR.firstChild);
            carrito.forEach((item, i)=>{
                const tr = document.createElement('tr') as HTMLTableRowElement;
                tr.classList.add('productselect');
                tr.dataset.indexcarrito = i+'';
                tr.innerHTML = `<td class="py-2 pl-4">${item.nombreproducto}</td>
                                <td class="py-2 text-center cursor-pointer cantidad"><input type="text" class="inputcantidad w-20 p-2 text-center" value="${item.cantidad}"></td>
                                <td class="py-2 text-right">$${item.valorunidad.toLocaleString()}</td>
                                <td class="py-2 text-right totalFila">$${item.total.toLocaleString()}</td>
                                <td class="text-center"><div class="btn-xs btn-red eliminarProducto"><i class="fa-solid fa-trash-can"></i></div></td>`;
                tablaMR.prepend(tr);
            });
        }

        function printTarifaEnvio():void{
            tarifas = POS.tarifas;
            const selectDir = dirEntrega.options[dirEntrega.selectedIndex];
        
        }
        
        ////////////////////// valores finales subtotal y total ////////////////////////
        function valorCarritoTotal(){
            valorTotal.valorgananciauser = 0;
            //calcular el impuesto discriminado por tarifa
            const idimpuesto: Record<string, number> = {'0': 1, '5': 2, '16': 3, '19': 4, 'excluido': 5, '8': 6 };
            const objbase:{'0':number, '5':number, '16':number, '19':number, 'excluido':number, '8':number} = {'0': 0, '5': 0, '16': 0, '19': 0, 'excluido':0, '8': 0};

            const mapImpuesto = new Map();
            carrito.forEach(x=>{
                if(x.impuesto){
                if(mapImpuesto.has(x.impuesto)){
                    const valor = mapImpuesto.get(x.impuesto) + x.total*constImp[x.impuesto];
                    mapImpuesto.set(x.impuesto, valor);
                }else{
                    mapImpuesto.set(x.impuesto, x.total*constImp[x.impuesto]);
                }
                }
                if(x.impuesto == null)x.impuesto = "excluido";
                objbase[x.impuesto as keyof typeof objbase] += x.base;
                const impValor = mapImpuesto.get(x.impuesto)??0;
                const index = factimpuestos.findIndex(Obj=>Obj.id_impuesto == idimpuesto[x.impuesto]);
                if(index!=-1){ //si existe remplazar obj
                    factimpuestos[index] = {id_impuesto:idimpuesto[x.impuesto], facturaid:0, basegravable:objbase[x.impuesto as keyof typeof objbase], valorimpuesto: impValor};
                }else{
                    factimpuestos = [...factimpuestos, {id_impuesto:idimpuesto[x.impuesto], facturaid:0, basegravable:objbase[x.impuesto as keyof typeof objbase], valorimpuesto: impValor}];
                }

                //calcular valor total de comision para el usuario
                valorTotal.valorgananciauser += x.valorcomision;
            });
            
            //Valor del impuesto total de todos los productos, es decir de la factura;
            let valorTotalImp:number = 0;
            for(let valorImp of mapImpuesto.values())valorTotalImp += valorImp;
            valorTotal.valorimpuestototal = parseFloat(valorTotalImp.toFixed(3));  //valor del impuesto total factura de todos los productos
            valorTotal.subtotal = carrito.reduce((total, x)=>x.total+total, 0);
            valorTotal.porcentgananciauser =  (100*valorTotal.valorgananciauser)/valorTotal.subtotal;
            valorTotal.base = valorTotal.subtotal - valorTotal.valorimpuestototal;  //valor de la base total factura de todos los productos
            valorTotal.total = valorTotal.subtotal + valorTotal.valortarifa - valorTotal.descuento;
            document.querySelector('#subTotal')!.textContent = '$'+valorTotal.subtotal.toLocaleString();
            (document.querySelector('#impuesto') as HTMLElement).textContent = '$'+valorTotalImp.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            //(document.querySelector('#valorTarifa') as HTMLElement).textContent = '$'+valorTotal.valortarifa.toLocaleString();
            document.querySelector('#total')!.textContent = '$ '+valorTotal.total.toLocaleString();
            // cantidad total de productos
            totalUnidades.textContent = carrito.reduce((total, producto)=>producto.cantidad+total, 0)+'';

            POS.carrito = carrito;
        }


        btnguardar?.addEventListener('click', ()=>{if(carrito.length)miDialogoGuardar.showModal();});
        btnfacturar?.addEventListener('click', ()=>{
            if(carrito.length){
                document.querySelector('.Efectivo')?.setAttribute('readonly', 'true');
                document.querySelector('#inputscreditos')?.classList.add('hidden');
                document.querySelector('#inputscreditos')?.classList.remove('flex');
                tipoventa = "Contado";
                POS.tipoventa = tipoventa;
                POS.gestionSubirModalPagar.subirModalPagar();
                (document.querySelector('#formfacturar #first') as HTMLInputElement).checked = true;
                miDialogoFacturar.showModal();
            }
        });


        ////////////////// evento al bton pagar del modal facturar //////////////////////
        document.querySelector('#formfacturar')?.addEventListener('submit', e=>{
            e.preventDefault();
            if(valorTotal.total <0 || valorTotal.subtotal <0){
                msjAlert('error', 'No se puede procesar pago con $0', (document.querySelector('#divmsjalertaprocesarpago') as HTMLElement));
                return;
            }

            //calcular si el totoal de los medios de pago es menor al abono inicial, abortar pago...
            let totalMediosPago:number = 0;
            for(let value of mapMediospago.values())totalMediosPago+=value;
            if(totalMediosPago<POS.gestionSubirModalPagar.valoresCredito.abonoinicial){
                POS.gestionSubirModalPagar.mostrarMensajeMetodosPago?.();
                return;
            }
        
            btnPagar.disabled = true;
            btnPagar.value = 'Procesando...';
            procesarpedido('Paga', '0');
        });

        async function procesarpedido(estado:string, ctz:string){
            const imprimir = document.querySelector('input[name="imprimir"]:checked') as HTMLInputElement;
            const valoresCredito = POS.gestionSubirModalPagar.valoresCredito;
            const datos = new FormData();
            //datos.append('id', datosfactura?.id??'');
            datos.append('idemisor', btnCaja.selectedOptions[0].dataset.idemisor??'');
            datos.append('idcliente', (document.querySelector('#selectCliente') as HTMLSelectElement).value || '1');
            datos.append('idvendedor', (document.querySelector('#vendedor') as HTMLParagraphElement).dataset.idvendedor!);
            datos.append('idcaja', btnCaja.value);
            datos.append('idconsecutivo', btnTipoFacturador.value);
            datos.append('iddireccion', dirEntrega.value);
            datos.append('idtarifazona', valorTotal.idtarifa+'');
            datos.append('idcanaldeventa', (document.querySelector('#canalVenta') as HTMLSelectElement)?.value??'1');
            datos.append('cliente', selectCliente.value==''?'N/A':selectCliente.options[selectCliente.selectedIndex].textContent!);
            datos.append('vendedor', (document.querySelector('#vendedor') as HTMLParagraphElement).textContent!);
            datos.append('caja', (document.querySelector('#caja option:checked') as HTMLSelectElement).textContent!);
            datos.append('tipofacturador', btnTipoFacturador.options[btnTipoFacturador.selectedIndex].textContent!);
            datos.append('direccion', dirEntrega.options[dirEntrega.selectedIndex]?.text??'');
            //datos.append('tarifazona', nombretarifa||'');
            datos.append('carrito', JSON.stringify(carrito.filter(x=>x.cantidad>0)));  //envio de todos los productos con sus cantidades
            datos.append('totalunidades', totalUnidades.textContent!);
            datos.append('mediosPago', JSON.stringify(Array.from(mapMediospago, ([idmediopago, valor])=>({idmediopago, id_factura:0, valor}))));
            datos.append('factimpuestos', JSON.stringify(factimpuestos));
            datos.append('recibido', document.querySelector<HTMLInputElement>('#recibio')!.value);
            datos.append('transaccion', '');
            datos.append('tipoventa', tipoventa);
            datos.append('valoresCredito', JSON.stringify(valoresCredito));
            datos.append('cotizacion', ctz);  //1= cotizacion, 0 = no cotizacion pagada.
            datos.append('remision', estado=='Remision'?'1':'0');
            datos.append('estado', estado);
            datos.append('porcentgananciauser', valorTotal.porcentgananciauser.toFixed(2));
            datos.append('valorgananciauser', valorTotal.valorgananciauser.toFixed(2));
            datos.append('subtotal', valorTotal.subtotal+'');
            datos.append('base', valorTotal.base.toFixed(3));
            datos.append('valorimpuestototal', valorTotal.valorimpuestototal+''); //valor total del impuesto. 
            datos.append('dctox100',valorTotal.dctox100+'');
            datos.append('descuento',valorTotal.descuento+'');
            datos.append('total', valorTotal.total.toString());
            datos.append('observacion', document.querySelector<HTMLTextAreaElement>('#observacion')!.value);
            datos.append('departamento', '');
            //datos.append('ciudad', (document.querySelector('#ciudad') as HTMLInputElement).value);
            datos.append('entrega', 'Presencial');
            datos.append('entregado', estado=='Paga'?'1':'0'); //si es remision no se ha entregado, si es pago y entrega a domicilio se define segun el checkbox de despachar, si es pago y entrega presencial se marca como entregado
            datos.append('valortarifa', valorTotal.valortarifa+'');
            datos.append('datosAdquiriente', JSON.stringify(POS.gestionarAdquiriente.datosAdquiriente));
            datos.append('opc1', '');
            datos.append('opc2', '');
            try {
                const url = "/admin/api/facturarModorapido";  //va al controlador ventascontrolador
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json();
                console.log(resultado);

                if(resultado.exito !== undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    btnPagar.disabled = false;
                    btnPagar.value = 'Pagar';
                    miDialogoFacturar.close();

                    if(estado == "Paga"){
                        resultado.dataInvoice.items = carrito.filter(x=>x.cantidad>0);
                        resultado.dataInvoice.mediospago = Array.from(mapMediospago, ([idmediopago, valor])=>({
                            idmediopago,
                            mediopago: mediosPagoDBMAP.get(idmediopago),
                            valor,
                        }));
                
                        //ENVIAR FACTURA A DIAN SI ES FACTURACION ELECTRONICA
                        if(btnTipoFacturador.options[btnTipoFacturador.selectedIndex].dataset.idtipofacturador == '1'){
                            const resDian = await POS.sendInvoiceAPI.sendInvoice(resultado.idfactura);
                            POS.gestionarAdquiriente.datosAdquiriente = {}; //reiniciar datos de adquiriente cada vez que se facture electronicamente
                            resultado.dataInvoice.cufe = resDian.cufe;
                            resultado.dataInvoice.link = resDian.link;
                            console.log(resDian);
                        }
                        //IMPRIMIR TICKET POS
                        if(resultado.idfactura && imprimir.value === '1')printTicketPOS(resultado.idfactura, resultado.dataInvoice);
                    }
                    vaciarventa();
                }else{
                    btnPagar.disabled = false;
                    btnPagar.value = 'Pagar';
                    const mensajeError = resultado.error?.[0]??'No fue posible procesar la solicitud';
                    msjalertToast('error', '¡Error!', mensajeError);
                }
            } catch (error) {
                console.log(error);
                btnPagar.disabled = false;
                btnPagar.value = 'Pagar';
                msjalertToast('error', '¡Error!', 'No fue posible comunicarse con el servidor');
            }
        }


        async function printTicketPOS(idfactura:string, datainvoice:DataInvoice){
            try {
                const url = "http://localhost:3100/api/printPOS/ticket1/CAJA"; //llamado a la API server print nodejs/ts
                const respuesta = await fetch(url, {
                method: 'POST',
                headers: { "Accept": "application/json", "Content-Type": "application/json" },
                body: JSON.stringify(datainvoice)
                });
                const resultado = await respuesta.json();
            } catch (error) {
                console.log(error);
            }

            setTimeout(() => {
                window.open("/admin/printPDFPOS?id=" + idfactura, "_blank");
            }, 1000);
        }


        function vaciarventa():void
        {
            //if(datosfactura?.id)datosfactura.id = '';
            (document.querySelector('#formFacturarA') as HTMLFormElement)?.reset();
            (document.querySelector('#formfacturar') as HTMLFormElement)?.reset();
            (document.querySelector('#formAddCliente') as HTMLFormElement).reset();
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = 'SIN CLIENTE';
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-green-100', 'text-green-600');
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-gray-100', 'text-gray-700');
            (document.querySelector('#resumenCliente') as HTMLParagraphElement).textContent = "Seleccionar cliente";
            mapMediospago.clear();
            $('.mediopago').val(0);
            carrito.length = 0;
            factimpuestos.length = 0;

            history.replaceState({}, "", "/admin/ventas/modorapido");  //modificar la URL sin recargar la página.
            while(tablaMR?.firstChild)tablaMR.removeChild(tablaMR?.firstChild);
            //(document.querySelector('#npedido') as HTMLInputElement).value = '';
            document.querySelector('#subTotal')!.textContent = '$'+0;
            document.querySelector('#impuesto')!.textContent = '$'+0;
            //(document.querySelector('#descuento') as HTMLElement).textContent = '$'+0;
            totalUnidades.textContent = '0';
            document.querySelector('#total')!.textContent = '$'+0;
            //$('#selectCliente').val('').trigger('change');   //aqui tambien se reinicia la elemento del valor de la tarifa
            for(const key in valorTotal)valorTotal[key as keyof typeof valorTotal] = 0; //reiniciar objeto
        }


        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
            if (f=== miDialogoAddCliente || f=== miDialogoFacturarA || f === miDialogoGuardar || f === miDialogoFacturar || (f as HTMLInputElement).value === 'Cancelar' || (f as HTMLInputElement).closest('.salir') || (f as HTMLInputElement).closest('.cotizacion') || (f as HTMLInputElement).closest('.remision')) {
                miDialogoFacturarA.close();
                miDialogoFacturar.close();
                miDialogoAddCliente.close();
                miDialogoGuardar.close();
                console.log(1);
                if((f as HTMLInputElement).closest('.cotizacion')){
                    console.log(3);
                    tipoventa = "";
                    procesarpedido('Guardado', '1');
                }
                if((f as HTMLInputElement).closest('.remision')){
                    tipoventa = "";
                    procesarpedido('Remision', '0');
                }
            }
        }


        POS.valorCarritoTotal = valorCarritoTotal;
        POS.valorTotal = valorTotal;
        POS.mapMediospago = mapMediospago;
        POS.tarifas = tarifas;
        POS.printTarifaEnvio = printTarifaEnvio;

    }

})();
