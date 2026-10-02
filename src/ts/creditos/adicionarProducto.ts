(()=>{
    if(document.querySelector('.adicionarProducto')){
        
        const btnAddItem = document.querySelector('#btnAddItem') as HTMLButtonElement;
        const tablaItems = document.querySelector('#tablaItems tbody');
        const btnUpdateCreditoSeparado = document.querySelector('#btnUpdateCreditoSeparado') as HTMLButtonElement;

        interface i_itemDetalle {
            id:string, //id del registro de la tabla productosseparados
            idcredito:string,
            idproducto: string,
            idunidadmedida:string,
            tipoproducto:string,
            tipoproduccion:string,
            promediostock:number,
            rendimientoestandar?:string,
            factor?:number,
            nombreproducto:string,
            unidadmedida:string,
            preciopersonalizado:string,
            sku:string,
            stockminimo?:string,

            capital?:number,            //DATOS DEL CREDITO
            abonoinicial?:number,      //abono inicial en el credito
            saldopendiente?:number,
            cantidadcuotas?:number,
            interes?:string,           // 1 = si interes en el credito, 0 = no interes en el credito
            interestotal?:number,      //interes total dlecredito en porcentaje
            valorinterestotal?:number, //valor del interes total de credito
            montototal?:number,
            descuentocredito?:number,  //descuneto general en el credito
            
            costo:number,
            valorunidad:number,
            descuento:number,  //se calcula para productos nuevos agregados
            cantidad:number,   //se calcula para productos nuevos agregados
            stock?:number,
            base:number,       //se calcula para productos nuevos agregados
            impuesto:string,
            valorimp:number,   //se calcula para productos nuevos agregados
            subtotal:number,   //se calcula para productos nuevos agregados
            total:number,      //se calcula para productos nuevos agregados
            insumos?:insumo[];
        }

        /*interface i_allProducts {
            id:string,  //id del producto
            idunidadmedida:string, 
            nombre:string,
            impuesto:string,
            tipoproducto:string, 
            tipoproduccion:string, 
            sku:string, 
            unidadmedida:string,
            promediostock:number,
            preciopersonalizado:string,
            rendimientoestandar:string, 
            stockminimo:string,
            precio_compra:number,
            precio_venta:number
            habilitarventa:string, 
            visible:string,
        }*/

        interface Item {
            id_impuesto: number,
            facturaid: number,
            basegravable: number,
            valorimpuesto: number
        }


        let factimpuestos:Item[] = [];
        let carrito:i_itemDetalle[]=[];
        let allproducts:productsapi[] = [];
        let filteredData: {id:string, text:string, tipo:string, tipoproducto:string, tipoproduccion:string, sku:string, unidadmedida:string, precio:string, promediostock:string}[];   //tipoproducto = 0 es producto simple,  1 = compuesto,  si no viene es subproducto, tipo=0 es producto(simple o compuesto), tipo=1 es subproducto
        const valorTotal = {subtotal: 0, base: 0, valorimpuestototal: 0, dctox100: 0, descuento: 0, total: 0}; //datos global de la venta
        const dataCredit = {capital:0, abonoinicial:0, saldopendiente:0, cantidadcuotas:0, interes:'0', interestotal:0, valorinterestotal:0, montototal:0, descuentocredito: 0};

        const constImp: {[key:string]: number} = {};
        constImp['excluido'] = 0;
        constImp['0'] = 0;  //exento de iva, tarifa 0%
        constImp['5'] = 0.0476190476190476; //iva, tarifa al 5%,  Bienes/servicios al 5
        constImp['8'] = 0.0740740740740741; //inc, tarifa al 8%,  impuesto nacional al consumo
        constImp['16'] = 0.1379310344827586; //iva, tarifa al 16%,  contratos firmados con el estado antes de ley 1819
        constImp['19'] = 0.1596638655462185; //iva, tarifa al 19%,  tarifa general

        (async ()=>{
            try {
                const url = "/admin/api/allproducts"; //llamado a la API REST en el controlador almacencontrolador para treaer todas los productos simples y compuestos
                const respuesta = await fetch(url);
                const resultado:productsapi[] = await respuesta.json();
                filteredData = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1').map(item => ({ id: item.id, text: item.nombre, tipo:item.tipoproducto??'1', tipoproducto: item.tipoproducto, tipoproduccion: item.tipoproduccion, sku: item.sku, unidadmedida: item.unidadmedida, precio: item.precio_venta+'', promediostock: item.promediostock??'1' }));
                activarselect2();
                allproducts = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1');
            } catch (error) {
                console.log(error);
            }
        })();
        
        const parametrosURL = new URLSearchParams(window.location.search);
        const idcreditoURL = parametrosURL.get('id');
        if(idcreditoURL!=null && /^\d+$/.test(idcreditoURL) && Number(idcreditoURL) >= 1){
            (async ()=>{
                try {
                    const url = "/admin/api/detalleProductosCredito?id="+idcreditoURL; //llamado a la API REST y trae el detalle de los productos de credito/separado
                    const respuesta = await fetch(url); 
                    const resultado:i_itemDetalle[] = await respuesta.json();
                    console.log(resultado);
                    carrito = resultado.map(item =>{
                        
                        return {
                            id: item.id,
                            idcredito: item.idcredito,
                            idproducto:item.idproducto,
                            idunidadmedida: item.idunidadmedida,
                            tipoproducto: item.tipoproducto, 
                            tipoproduccion: item.tipoproduccion,
                            promediostock: Number(item.promediostock),
                            rendimientoestandar: item.rendimientoestandar,
                            nombreproducto: item.nombreproducto, 
                            unidadmedida: item.unidadmedida,
                            preciopersonalizado: item.preciopersonalizado,
                            sku: item.sku,
                            costo: Number(item.costo),
                            valorunidad: Number(item.valorunidad),
                            descuento: Number(item.descuento), //descuento del producto
                            cantidad: Number(item.cantidad),
                            stock: Number(item.cantidad),
                            base: Number(item.base),
                            impuesto: item.impuesto,
                            valorimp: Number(item.valorimp),
                            subtotal: Number(item.subtotal),
                            total: Number(item.total),
                            factor: 1,
                        }
                    });
                    carrito.forEach((c, i) =>printItemTable(c.unidadmedida, c.cantidad, c.nombreproducto, c.valorunidad, c.total, i));
                    //obtener datos del credito

                    dataCredit.capital = Number(resultado[0].capital);
                    dataCredit.abonoinicial = Number(resultado[0].abonoinicial!);
                    dataCredit.saldopendiente = Number(resultado[0].saldopendiente);
                    dataCredit.cantidadcuotas = Number(resultado[0].cantidadcuotas);
                    dataCredit.interes = resultado[0].interes!;
                    dataCredit.interestotal = resultado[0].interestotal!;
                    dataCredit.valorinterestotal = Number(resultado[0].valorinterestotal!);
                    dataCredit.montototal = Number(resultado[0].montototal);
                    dataCredit.descuentocredito = Number(resultado[0].descuentocredito!);
                    
                } catch (error) {
                    console.log(error);
                }
            })();
        }
        
        

        function activarselect2(){
            ($('#articulo') as any).select2({ 
                width: '100%',
                data: filteredData,
                placeholder: "",
                maximumSelectionLength: 1,
                dropdownCssClass: 'select2-theme-dropdown',
            });  
        }

        $("#articulo").on('change', (e)=>{
            let datos = ($('#articulo') as any).select2('data')[0];
            if(datos)(document.querySelector('#unidadmedida') as HTMLInputElement).value = datos.unidadmedida;
        });

        function limpiarSelectorArticulo():void{
            ($('#articulo') as any).val(null).trigger('change');
            (document.querySelector('#unidadmedida') as HTMLInputElement).value = '';
            (document.querySelector('#cantidad') as HTMLInputElement).value = '1';
        }

        btnAddItem.addEventListener('click', (e)=>{
            let cantidad = parseFloat((document.querySelector('#cantidad') as HTMLInputElement).value);
            let datos = ($('#articulo') as any).select2('data')[0];
            if(!datos)return;
            const itemselected = allproducts.find(x=>x.id == datos.id);
            if(itemselected){
                const productoConfigurado = structuredClone(itemselected);
                //filtrarInsumos(productoConfigurado);
                const index = carrito.findIndex(x=>x.idproducto==datos.id /*&& x.valorunidad == Number(datos.precio) && mismaConfiguracion(x, productoConfigurado!)*/);
                if(index == -1){  //si el item seleccionado no existe en el carrito, agregarlo.

                    const productototal = Number(productoConfigurado.precio_venta)*cantidad;
                    const productovalorimp = productototal*constImp[productoConfigurado.impuesto??'0']; //si producto.impuesto es null toma el valor de cero

                    const item:i_itemDetalle = {
                        id: '',
                        idcredito: idcreditoURL!,
                        idproducto:productoConfigurado.id,
                        idunidadmedida: productoConfigurado.idunidadmedida,
                        //tipo: itemselected.tipo,  ////tipo = 0 es producto (simple o compuesto produccion),  1 = subproducto
                        tipoproducto: productoConfigurado.tipoproducto, 
                        tipoproduccion: productoConfigurado.tipoproduccion,
                        promediostock: Number(productoConfigurado.promediostock),
                        rendimientoestandar: productoConfigurado.rendimientoestandar,
                        nombreproducto: productoConfigurado.nombre, 
                        unidadmedida: productoConfigurado.unidadmedida,
                        preciopersonalizado: '0',
                        sku: productoConfigurado.sku,
                        costo: Number(productoConfigurado.precio_compra),
                        valorunidad: Number(productoConfigurado.precio_venta),
                        descuento: 0,
                        cantidad: cantidad,
                        stock: cantidad,
                        base: productototal-productovalorimp,
                        impuesto: productoConfigurado.impuesto,
                        valorimp: productovalorimp,
                        subtotal: productototal,
                        total: productototal,
                        factor: 1,
                    }
                    carrito = [...carrito, item];
                    const indice = carrito.length - 1;
                    printItemTable(productoConfigurado.unidadmedida, cantidad, productoConfigurado.nombre, Number(productoConfigurado.precio_venta), item.total, indice);
                    valorCarritoTotal();
                }else{
                    carrito[index].cantidad = cantidad;
                    carrito[index].stock = cantidad;
                    carrito[index].subtotal = (carrito[index].valorunidad)*carrito[index].cantidad;
                    carrito[index].total = carrito[index].subtotal;
                    //calculo del impuesto y base por producto en el carrito deventas
                    carrito[index].valorimp = parseFloat((carrito[index].total*constImp[carrito[index].impuesto??0]).toFixed(3));
                    carrito[index].base = parseFloat((carrito[index].total-carrito[index].valorimp).toFixed(3));
                    //calcular valores
                    const tr = document.querySelector(`[data-index="${index}"]`)!;
                    tr.children[1].textContent = carrito[index].cantidad+'';
                    valorCarritoTotal();
                }
                limpiarSelectorArticulo();
            }
        });


        /*function mismaConfiguracion(productCarrito: any, producto2: productsapi):boolean {
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
        }*/

        function printItemTable(unidadmedida:string, cantidad:number, nombre:string, valorunidad:number, total:number, indice:number){
            const tr = document.createElement('TR');
            tr.classList.add('itemselect');
            tr.dataset.index = `${indice}`;
            tr.insertAdjacentHTML('afterbegin', 
                `<td class="font-semibold text-slate-900">${nombre}</td>
                <td>${cantidad}</td>
                <td class="font-extrabold text-cyan-700">${unidadmedida}</td>
                <td class="font-extrabold">$ ${valorunidad.toLocaleString()}</td>
                <td class="font-extrabold text-emerald-700">$ ${total.toLocaleString()}</td>
                <td class="flex min-w-[12rem] items-center justify-center gap-[.7rem] whitespace-nowrap">
                    <button class="inline-flex h-[3.6rem] min-w-16 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-[.9rem] text-[1.18rem] font-extrabold text-indigo-600 transition hover:-translate-y-px">
                        👁 Ver
                    </button>
                    <button class="eliminarItem inline-flex h-[3.6rem] w-16 items-center justify-center rounded-lg border border-red-500 bg-red-500 p-0 text-white transition hover:-translate-y-px hover:bg-red-600 hover:shadow-md"><i class="fa-solid fa-trash-can"></i></button>
                </td>`);
            tablaItems?.appendChild(tr);
        }


        function valorCarritoTotal(){
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
            });

            //Valor del impuesto total de todos los productos, es decir de la factura;
            let valorTotalImp:number = 0;
            for(let valorImp of mapImpuesto.values())valorTotalImp += valorImp;
            
            valorTotal.valorimpuestototal = parseFloat(valorTotalImp.toFixed(3));  //valor del impuesto total factura de todos los productos
            valorTotal.subtotal = carrito.reduce((total, x)=>x.total+total, 0);
            valorTotal.base = valorTotal.subtotal - valorTotal.valorimpuestototal;  //valor de la base total factura de todos los productos
            valorTotal.total = valorTotal.subtotal+dataCredit.valorinterestotal-dataCredit.descuentocredito-dataCredit.abonoinicial;
            //console.log(valorTotal);
            //console.log(factimpuestos);
            document.querySelector('#subTotal')!.textContent = '$'+valorTotal.subtotal.toLocaleString();
            (document.querySelector('#impuesto') as HTMLElement).textContent = '$'+valorTotalImp.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.querySelector('#total')!.textContent = '$ '+(valorTotal.total).toLocaleString(); //+ interes - descuneto - abonoinicial
            
        }


        //////////////////////////////////// evento a la tabla de los productos seleccionados  ///////////////////////////////////
        tablaItems?.addEventListener('click', (e:Event)=>{
            const elementItem = (e.target as HTMLElement)?.closest('.itemselect'); //seleccionamos el tr de la tabla
            const indexcarrito = Number((elementItem as HTMLElement).dataset.index!);
        
            if((e.target as HTMLElement).classList.contains('eliminarItem')){ //se trae todoscon true menos el que coincida con iditem y tipoitem
                carrito.splice(indexcarrito, 1);
                while(tablaItems.firstChild)tablaItems.removeChild(tablaItems.firstChild);
                carrito.forEach((item, i) =>printItemTable(item.unidadmedida, item.cantidad, item.nombreproducto, item.valorunidad, item.total, i));
                //tablaItems?.querySelector(`TR[data-id="${iditem}"]`)?.remove();
            }
            valorCarritoTotal();
            //itemCarrito.cantidad = itemCarrito.cantidadcomprado*itemCarrito.factor;
        });


        btnUpdateCreditoSeparado.addEventListener('click', ()=>{
            //validar que el montototal no sea menor a lo abonado hasta la fecha
            if(valorTotal.total< (dataCredit.montototal-dataCredit.saldopendiente)){
                Swal.fire('El valor del credito es inferior de lo que se ha abonado.', '', 'error')
                return;
            }

            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Esta seguro de actualizar la orden?',
                text: "Se procesara la orden con todos los productos del separado",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (async ()=>{ 
                        const datos = new FormData();
                        datos.append('idcredito', idcreditoURL!);
                        datos.append('ids', JSON.stringify(carrito.map(x=>x.id)));
                        datos.append('nuevosproductos', JSON.stringify(carrito));

                        datos.append('factimpuestos', JSON.stringify(factimpuestos));
                        datos.append('capital', valorTotal.subtotal+'');
                        datos.append('saldopendiente', (dataCredit.saldopendiente+valorTotal.total-dataCredit.montototal)+'');
                        datos.append('montocuota', (Math.ceil((valorTotal.total/dataCredit.cantidadcuotas)*100)/100)+'');
                        datos.append('montototal', valorTotal.total+'');
                        datos.append('totalunidades', carrito.reduce((total, x)=>x.cantidad+total, 0)+'');
                        
                        datos.append('base', valorTotal.base.toFixed(3));  //base global del credito
                        datos.append('valorimpuestototal', valorTotal.valorimpuestototal+''); //valor total del impuesto dle credito. 
                        datos.append('dctox100', valorTotal.dctox100+'');
                        datos.append('descuento', dataCredit.descuentocredito+''); //descuento global de credito
                    
                        try {
                            const url = "/admin/api/editarOrdenCreditoSeparado";  //api llamada a trasladosinvcontrolador
                            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                            const resultado = await respuesta.json();  
                            if(resultado.exito !== undefined){
                                /*setTimeout(() => {
                                    window.location.href = `/admin/almacen/trasladarinventario`;
                                }, 1600);*/
                                Swal.fire(resultado.exito[0], '', 'success') 
                            }else{
                                Swal.fire(resultado.error[0], '', 'error')
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();//cierre de async()
                }
            });
        });

    }
})();
