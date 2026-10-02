
(()=>{
    if(document.querySelector('.compras')){
        const proveedor = document.querySelector('#proveedor') as HTMLSelectElement;
        //const impuesto = document.querySelector('#inputimpuesto') as HTMLSelectElement;
        const inputValorimp = document.querySelector('#valorimp') as HTMLInputElement;
        const nfactura = document.querySelector('#nfactura') as HTMLInputElement;
        const fecha = document.querySelector('#fecha') as HTMLDataElement;
        const origenPago = document.querySelector('#origenPago') as HTMLSelectElement;
        const origenCaja = document.querySelector('#origenCaja') as HTMLSelectElement;
        const origenBanco = document.querySelector('#origenBanco') as HTMLSelectElement;
        const formapago = document.querySelector('#formapago') as HTMLSelectElement;
        const observacion = document.querySelector('#observacion') as HTMLInputElement;
        const btnvaciar = document.querySelector('#btnvaciar');
        const miDialogoVaciar = document.querySelector('#miDialogoVaciar') as any;
        const miDialogoRegistrarcompra = document.querySelector('#miDialogoRegistrarcompra') as any;
        const tablaCompras = document.querySelector('#tablaCompras tbody');

        const constImp: {[key:string]: number} = {};
        constImp['excluido'] = 0;
        constImp['0'] = 0;  //exento de iva, tarifa 0%
        constImp['5'] = 0.0476190476190476; //iva, tarifa al 5%,  Bienes/servicios al 5
        constImp['8'] = 0.0740740740740741; //inc, tarifa al 8%,  impuesto nacional al consumo
        constImp['16'] = 0.1379310344827586; //iva, tarifa al 16%,  contratos firmados con el estado antes de ley 1819
        constImp['19'] = 0.1596638655462185; //iva, tarifa al 19%,  tarifa general

        type conversionunidadesapi = {
        id:string,
        idproducto: string,
        idsubproducto: string,
        idunidadmedidabase: string,
        idunidadmedidadestino: string,
        nombreunidadbase: string,
        nombreunidaddestino: string,
        factorconversion: string,
        //idservicios:{idempleado:string, idservicio:string}[]
        };

        let carrito:{iditem:string, idpx:string, idsx:string, tipocosto:string, tipo: string, nombreitem:string, unidad:string, stock: number, cantidad: number, cantidadcomprado: number, factor: number, impuesto: number, valorunidad: number, subtotal: number, precio_compra: number, valorcompra: number}[]=[];
        let tax:number = 0, percentImp:number = 0, barcode = "", allConversionUnidades:conversionunidadesapi[] = [];
        let filteredData: {id:string, text:string, tipo:string, sku:string, unidadmedida:string}[];   //tipo = 0 es producto simple,  1 = subproducto

        function actualizarContadorProductos(): void {
            const badge = document.querySelector('#contadorProductos');
            if(!badge) return;
            const cantidad = carrito.length;
            badge.textContent = cantidad === 1? '1 producto': `${cantidad} productos`;
        }

        (async ()=>{
            try {
                const url = "/admin/api/allConversionesUnidades"; //llamado a la API REST en el controlador almacencontrolador para treaer todas las conversiones de unidades
                const respuesta = await fetch(url); 
                allConversionUnidades = await respuesta.json();
            } catch (error) {
                console.log(error);
            }
        })();

        (async ()=>{
            try {
                const url = "/admin/api/totalitems"; //llamado a la API REST en el controlador almacencontrolador para treaer todas los productos simples y subproductos
                const respuesta = await fetch(url); 
                const resultado:{id:string, nombre:string, tipoproducto:string, tipoproduccion: string, sku:string, unidadmedida:string}[] = await respuesta.json();
                console.log(resultado);
                filteredData = resultado.filter(item => item.tipoproduccion=='0'||item.tipoproduccion==null).map(item => ({ id: item.id, text: item.nombre, tipo: item.tipoproducto??'1', sku: item.sku, unidadmedida: item.unidadmedida }));
                activarselect2(filteredData);
            } catch (error) {
                console.log(error);
            }
        })();


        //impuesto?.addEventListener('input', ()=>resumen());
        inputValorimp.addEventListener('input', (e)=>resumen());

        function activarselect2(filteredData:{id:string, text:string, tipo:string, sku:string, unidadmedida:string}[]){
            ($('#articulo') as any).select2({ 
                data: filteredData,
                placeholder: "Selecciona un item",
                maximumSelectionLength: 1,
                /*
                templateResult: function (data:{id:string, text:string, tipo:string}) {
                    // Personalizar cómo se muestra cada opción en el dropdown
                    if (!data.id) { return data.text; }  // Si no hay id, solo mostrar el texto
                    const html = `
                        <div class="custom-option">
                            <span class="item-name">${data.text}</span> 
                            <span class="item-type">${data.tipo}</span>  <!-- Mostrar tipo adicional -->
                        </div>`;
                    return $(html);  // Devolver el HTML personalizado
                }*/
            });
        }


        //////////  ELEGIR ORIGEN DEL PAGO DE LA COMPRA, CAJA O BANCO ///////////
        origenPago.addEventListener('change', (e:Event)=>{
            if((e.target as HTMLOptionElement).value == '0'){ //0 = origen caja
                document.querySelector('#divBanco')?.classList.add('hidden');
                document.querySelector('#divBanco')?.removeAttribute("required");
            }else{  //1 = origen banco
                document.querySelector('#divBanco')?.classList.remove('hidden');
                document.querySelector('#divBanco')?.setAttribute("required", "");
            }
        });


        window.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Control')(document.querySelector('#btnScanner') as HTMLSelectElement).focus();
            if (e.key === 'Enter') {
                if (barcode.length >= 3) {
                    e.preventDefault();
                    e.stopImmediatePropagation(); // <--- CRITICO: Detiene a Select2 y a cualquier botón
                    searchBarCode(barcode);
                    barcode = "";
                    return false;
                }
                barcode = "";
            } else {
                barcode += e.key;
            }
        }, true);


        function searchBarCode(barcode:string){
            const itemselected = filteredData.find(x=>x.sku==barcode);
            if(itemselected != undefined){
                let productSelected = carrito.find(x=>x.iditem==itemselected.id&&x.tipo==itemselected.tipo);
                if(productSelected != undefined){
                    productSelected.cantidadcomprado  += 1;
                    productSelected.cantidad = productSelected.cantidadcomprado*productSelected.factor;
                    productSelected.stock = productSelected.cantidad;
                    //aumentar la cantodad en los inputs
                    const fila = document.querySelector<HTMLInputElement>(`TR[data-id="${productSelected.iditem}"][data-tipo="${productSelected.tipo}"] .inputcantidad`);
                    if(fila)fila.value = productSelected.stock+'';
                }else{ //itme no esta en carrito
                    agregarProducto(itemselected);
                }
            }
        }


        ////// EVENTO AL SELECT ARTICULOS O ITEMS PARA SELECCIONAR EL ITEM Y AÑADIR AL CARRITO ////// 
        $("#articulo").on('change', (e)=>{
            let datos = ($('#articulo') as any).select2('data')[0];
            if(datos)agregarProducto(datos);
            $("#articulo").val('null');
        });


        function agregarProducto(datos:any) {
            const index = carrito.findIndex(x=>x.iditem==datos.id&&x.tipo==datos.tipo);
            if(index == -1){  //si el item seleccionado no existe en el carrito, agregarlo.
                const itemselected = filteredData.find(x=>x.id==datos.id&&x.tipo==datos.tipo)!; //products es el arreglo de todos los productos traido por api
                const item:{iditem: string, idpx: string, idsx: string, tipocosto:string, tipo: string, nombreitem: string, unidad: string, stock: number, cantidad: number, cantidadcomprado: number, factor: number, impuesto: number, valorunidad: number, subtotal: number, precio_compra:number, valorcompra: number} = {
                    iditem: itemselected?.id!,
                    idpx: itemselected.tipo=='0'?itemselected.id:'NULL',
                    idsx: itemselected.tipo=='1'?itemselected.id:'NULL',
                    tipocosto: '1',
                    tipo: itemselected.tipo,  ////tipo = 0 es producto simple,  1 = producto principal compuesto o subproducto
                    nombreitem: itemselected.text,
                    unidad: itemselected.unidadmedida,
                    stock: 1,
                    cantidad: 1,
                    cantidadcomprado: 1,
                    factor: 1,
                    impuesto: 0,
                    valorunidad: 0,
                    subtotal: 0,
                    precio_compra: 0,
                    valorcompra: 0,
                }
                carrito = [...carrito, item];
                actualizarContadorProductos();
                if(carrito.length === 1)document.querySelector('#filaVacia')?.remove();
                printItemTable(datos.id, datos.tipo, datos.unidadmedida);
            }
        }


        function printItemTable(id:string, tipo:string, unidadmedida:string){
            let options = `<option data-factor="1" value="" >${unidadmedida}</option>`;
            const unItem = filteredData.find(x=>x.id==id&&x.tipo==tipo)!;
            if(tipo=='1'){   //si es un subproducto
                options = "";
                const subproductounidades = allConversionUnidades.filter(x => x.idsubproducto == id); 
                subproductounidades.forEach(u=>options+=`<option data-factor="${u.factorconversion}" value="${u.idsubproducto}" >${u.nombreunidaddestino}</option>`);
            }
            if(tipo=='0'){
                options = "";
                const productounidades = allConversionUnidades.filter(x => x.idproducto == id); 
                productounidades.forEach(u=>options+=`<option data-factor="${u.factorconversion}" value="${u.idproducto}" >${u.nombreunidaddestino}</option>`);
            }
            const tr = document.createElement('TR');
            tr.classList.add('itemselect');
            tr.dataset.id = `${id}`;
            tr.dataset.tipo = `${tipo}`;
            tr.insertAdjacentHTML('afterbegin', `<td class="!px-0 !py-2 text-xl text-gray-500 leading-5">${unItem?.text}</td> 
            <td class="!p-2 text-xl text-gray-500 leading-5">
                <select class="formulario__select selectunidad">
                    ${options}  
                </select>
            </td>
            <td class="!px-0 !py-2"><div class="flex"><button type="button"><span class="menos material-symbols-outlined">remove</span></button><input type="text" class="inputcantidad w-20 px-2 text-center" value="1" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"><button type="button"><span class="mas material-symbols-outlined">add</span></button></div></td>
            <td class="!p-2 text-xl text-gray-500 leading-5"><input type="text" placeholder="Ingresa Valor Total Producto" class="inputcompra text-base sm:text-lg md:text-xl lg:text-2xl placeholder:text-sm sm:placeholder:text-base md:placeholder:text-lg lg:placeholder:text-xl bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white h-14 focus:outline-none focus:ring-1" value="" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||0)"></td>
            <td class="accionestd"><div class="acciones-btns"><button class="btn-md btn-red eliminarItem"><i class="fa-solid fa-trash-can"></i></button></div></td>`);
            tablaCompras?.appendChild(tr);
        }
        

        //////////////////////////////////// evento a la tabla de los productos seleccionados  ///////////////////////////////////
        tablaCompras?.addEventListener('click', (e:Event)=>{
            const elementItem = (e.target as HTMLElement)?.closest('.itemselect'); //seleccionamos el tr de la tabla
            const iditem = (elementItem as HTMLElement).dataset.id!;
            const tipoitem = (elementItem as HTMLElement).dataset.tipo!;

            const itemCarrito = carrito.find(x=>x.iditem==iditem&&x.tipo==tipoitem)!;

            if((e.target as HTMLElement).classList.contains('selectunidad')){
                if((e.target as HTMLElement).dataset.event != "eventSelect"){
                    e.target?.addEventListener('change', (e:Event)=>{
                        const unit = e.target as HTMLSelectElement;
                        itemCarrito.factor = Number(unit.options[unit.selectedIndex].dataset.factor);
                    });
                    (e.target as HTMLElement).dataset.event = "eventSelect"; //se marca al input que ya tiene evento añadido
                }
            }
            
            if((e.target as HTMLElement).classList.contains('menos')){
                itemCarrito.cantidadcomprado--;
                if(itemCarrito.cantidadcomprado<0)itemCarrito.cantidadcomprado = 0;
            }

            if((e.target as HTMLElement).classList.contains('inputcantidad')){
                if((e.target as HTMLElement).dataset.event != "eventInput"){
                    e.target?.addEventListener('input', (e:Event)=>{
                        itemCarrito.cantidadcomprado = Number((e.target as HTMLInputElement).value);
                        itemCarrito.cantidad = itemCarrito.cantidadcomprado*itemCarrito.factor;
                        itemCarrito.stock = itemCarrito.cantidad;
                    });
                    (e.target as HTMLElement).dataset.event = "eventInput"; //se marca al input que ya tiene evento añadido
                }
            }

            if((e.target as HTMLElement).classList.contains('mas'))itemCarrito.cantidadcomprado++;
            
            //////// INPUT CANTIDAD DE LA TABLA DE ITEMS DE COMPRA, SE ACTUALIZA CADA VEZ QUE SE DE CLICK EN MENOS O MAS
            (tablaCompras?.querySelector(`TR[data-id="${iditem}"][data-tipo="${tipoitem}"] .inputcantidad`) as HTMLInputElement).value = itemCarrito?.cantidadcomprado+'';

            /////// EVENTO AL INPUT VALOR DE COMPRA DE LA TABLA DE ITEMS DE COMPRA  ///////
            if((e.target as HTMLElement).classList.contains('inputcompra')){
                if((e.target as HTMLElement).dataset.event != "eventInput"){
                    e.target?.addEventListener('input', (e:Event)=>{
                        itemCarrito.valorcompra = Number((e.target as HTMLInputElement).value);
                        itemCarrito.precio_compra = itemCarrito.valorcompra/itemCarrito.cantidad;
                        resumen();
                    });
                    (e.target as HTMLElement).dataset.event = "eventInput"; //se marca al input que ya tiene evento añadido
                }
            }

            if((e.target as HTMLElement).classList.contains('eliminarItem') || (e.target as HTMLElement).tagName == "I"){
                carrito = carrito.filter(x=> {
                    if(x.iditem==iditem){
                        if(x.tipo==tipoitem){
                            return false;
                        }else{
                            return true;
                        }
                    }else{
                        return true;
                    }
                });
                tablaCompras?.querySelector(`TR[data-id="${iditem}"][data-tipo="${tipoitem}"]`)?.remove();
                actualizarContadorProductos();
                resumen();
            }

            itemCarrito.cantidad = itemCarrito.cantidadcomprado*itemCarrito.factor;
            itemCarrito.stock = itemCarrito.cantidad;
        });


        btnvaciar?.addEventListener('click', ()=>{
            if(carrito.length){
              miDialogoVaciar.showModal();
              document.addEventListener("click", cerrarDialogoExterno);
            }
        });

        document.querySelector('#formComprar')?.addEventListener('submit', e=>{
            e.preventDefault();
            miDialogoRegistrarcompra.showModal();
            (document.querySelector('#numOrden') as HTMLSpanElement).textContent = nfactura.value;
            document.addEventListener("click", cerrarDialogoExterno);
        });

  
        async function procesarpedido(estado:string){
            const datos = new FormData();
            const valorCarrito = carrito.reduce((total, item)=>item.valorcompra+total, 0);
            datos.append('idproveedor', proveedor.options[proveedor.selectedIndex].value);
            //datos.append('idformapago', formapago.options[formapago.selectedIndex].value);
            datos.append('idorigencaja', origenCaja.options[origenCaja.selectedIndex].value);
            datos.append('idorigenbanco', origenBanco.options[origenBanco.selectedIndex].value);
            datos.append('nombreproveedor', proveedor.options[proveedor.selectedIndex].textContent!);
            //datos.append('formapago', formapago.options[formapago.selectedIndex].text);
            datos.append('nfactura', nfactura.value);
            datos.append('impuesto', (percentImp.toFixed(2))+'');
            datos.append('valorimp', tax+'');
            datos.append('base', valorCarrito+'');
            datos.append('origenpago', origenPago.options[origenPago.selectedIndex].value); //0 = si el origen viene de la caja,  1 = banco
            datos.append('nombreorigenpago', origenPago.options[origenPago.selectedIndex].textContent+''); //caja o banco
            datos.append('nombreorigencaja', origenCaja.options[origenCaja.selectedIndex].textContent+'');
            datos.append('nombreorigenbanco', origenBanco.options[origenBanco.selectedIndex].value?origenBanco.options[origenBanco.selectedIndex].textContent+'':'');
            datos.append('cantidaditems', carrito.reduce((total, item)=>item.cantidad+total, 0)+'');
            datos.append('observacion', observacion.value);
            datos.append('estado', estado);
            datos.append('subtotal', valorCarrito+'');
            datos.append('valortotal', (valorCarrito+tax)+'');
            datos.append('fechacompra', fecha.value);
            datos.append('carrito', JSON.stringify(carrito));  ////arreglo de objetos de producto simple y subproducto
            datos.append('subID', JSON.stringify(carrito.map(item=>{if(item.idsx){return item.idsx}}))); //envia solo los id de los subproductos
            try {
                const url = "/admin/api/registrarCompra";  //va al controlador ventascontrolador
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json();
                if(resultado.exito !== undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    //imprimir detalle de compra
                    if((document.querySelector('#imprimirComprobanteCompra') as HTMLInputElement).checked){
                        printComprobanteCompra(resultado.idcompra);
                    }
                    /////// reinciar campos de la compra
                    vaciarcompra();
                    (document.querySelector('#formComprar') as HTMLFormElement)?.reset();
                }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                }
            } catch (error) {
                console.log(error);
            }
        }


        function printComprobanteCompra(idcompra:string){
            setTimeout(() => {
                window.open("/admin/printComprobanteCompraPDF?id=" + idcompra, "_blank");  //llama a printcontrolador
            }, 1000);
        }

        function validarValorCompra():boolean{
            return carrito.some(item=>Number.isNaN(item.valorcompra)||item.valorcompra==0);
        }

        function resumen():void{
            const valorCarrito = carrito.reduce((total, item)=>item.valorcompra+total, 0);
            tax = Number(inputValorimp.value);
            percentImp = valorCarrito == 0 ? 0 :(100*tax)/valorCarrito;
            document.querySelector('#subTotal')!.textContent = '$ '+(valorCarrito.toLocaleString());
            document.querySelector('#impuesto')!.textContent = (percentImp.toFixed(2))+'%  -  $'+tax.toLocaleString();
            document.querySelector('#total')!.textContent = '$ '+((valorCarrito+tax).toLocaleString());
        }

        function vaciarcompra():void{
            carrito.length = 0;
            while(tablaCompras?.firstChild)tablaCompras.removeChild(tablaCompras?.firstChild);
            document.querySelector('#impuesto')!.textContent = '% 0';
            document.querySelector('#total')!.textContent = '$ 0';
        }

        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
            if (f === miDialogoVaciar || f === miDialogoRegistrarcompra || (f as HTMLInputElement).closest('.novaciar') || (f as HTMLInputElement).closest('.sivaciar') || (f as HTMLInputElement).closest('.nocomprar') || (f as HTMLInputElement).closest('.sicomprar')) {
              miDialogoVaciar.close();
              miDialogoRegistrarcompra.close();
              document.removeEventListener("click", cerrarDialogoExterno);
              if((f as HTMLInputElement).closest('.sicomprar')){
                if(!validarValorCompra()){
                    procesarpedido('paga');
                }else{
                    msjalertToast('error', '¡Error!', 'Verifica el valor de compra de la lista de productos');
                }
              }
              if((f as HTMLInputElement).closest('.sivaciar'))vaciarcompra();
            }
        }
        
    }

})();