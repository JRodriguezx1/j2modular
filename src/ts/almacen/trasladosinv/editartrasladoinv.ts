(()=>{
    if(document.querySelector('.editartrasladoinv')){
        // EDITAR LOS PRODUCTOS A TRASLADOR A OTRA SEDE
        const btnAddItem = document.querySelector('#btnAddItem') as HTMLButtonElement;
        const tablaItems = document.querySelector('#tablaItems tbody');
        const btnUpdateTrasladoInv = document.querySelector('#btnUpdateTrasladoInv') as HTMLButtonElement;
        let carrito:{id: string, id_trasladoinv:string, fkproducto:string, idsubproducto_id:string, iditem:string, nombreitem:string, tipo:string, unidadmedida:string, cantidad: number, factor: number}[]=[];
        let filteredData: {id:string, text:string, tipo:string, sku:string, unidadmedida:string}[];   //tipo = 0 es producto simple,  1 = subproducto

        (async ()=>{
            try {
                const url = "/admin/api/totalitems"; //llamado a la API REST en el controlador almacencontrolador para treaer todas los productos simples y subproductos
                const respuesta = await fetch(url); 
                const resultado:{id:string, nombre:string, tipoproducto:string, sku:string, unidadmedida:string}[] = await respuesta.json(); 
                filteredData = resultado.map(item => ({ id: item.id, text: item.nombre, tipo: item.tipoproducto??'1', sku: item.sku, unidadmedida: item.unidadmedida }));
                activarselect2(filteredData);
            } catch (error) {
                console.log(error);
            }
        })();
        
        const parametrosURL = new URLSearchParams(window.location.search);
        const id = parametrosURL.get('id');
        if(id!=null && /^\d+$/.test(id) && Number(id) >= 1){
            (async ()=>{
                try {
                    const url = "/admin/api/idOrdenTrasladoSolicitudInv?id="+id; //llamado a la API REST y trae el detalle de la orden trasladar/solicitud desde trasladarinvcontrolador.php
                    const respuesta = await fetch(url); 
                    const resultado = await respuesta.json();
                    const detalleOrden = resultado.orden[0];
                    const itemsorden:{id:string, id_trasladoinv:string, fkproducto:string, idsubproducto_id:string, nombre:string, cantidad:number}[] = detalleOrden.detalletrasladoinv;
                    carrito = itemsorden.map(item =>{
                        const esProducto = item.fkproducto && !item.idsubproducto_id;
                        return {
                            id: item.id,
                            id_trasladoinv: item.id_trasladoinv,
                            fkproducto: item.fkproducto,
                            idsubproducto_id: item.idsubproducto_id,
                            iditem: esProducto?item.fkproducto:item.idsubproducto_id, 
                            nombreitem: item.nombre, 
                            tipo: esProducto?'0':'1', 
                            unidadmedida:'', 
                            cantidad: item.cantidad, 
                            factor: 1
                        }
                    });
                    //printDetalleOrden(detalleOrden);
                    carrito.forEach(c =>printItemTable(c.iditem, c.tipo, c.unidadmedida, c.cantidad, c.nombreitem));   
                } catch (error) {
                    console.log(error);
                }
            })();
        }
        
        

        function activarselect2(filteredData:{id:string, text:string, tipo:string, sku:string, unidadmedida:string}[]){
            ($('#articulo') as any).select2({ 
                width: '100%',
                data: filteredData,
                placeholder: "Selecciona un item",
                maximumSelectionLength: 1,
            });  
        }

        $("#articulo").on('change', (e)=>{
            let datos = ($('#articulo') as any).select2('data')[0];
            if(datos)(document.querySelector('#unidadmedida') as HTMLInputElement).value = datos.unidadmedida;
        });

        btnAddItem.addEventListener('click', (e)=>{
            let cantidad = parseFloat((document.querySelector('#cantidad') as HTMLInputElement).value);
            let datos = ($('#articulo') as any).select2('data')[0];
            console.log(datos);
            if(datos){
                const index = carrito.findIndex(x=>x.iditem==datos.id&&x.tipo==datos.tipo);
                if(index == -1){  //si el item seleccionado no existe en el carrito, agregarlo.
                    const itemselected = filteredData.find(x=>x.id==datos.id&&x.tipo==datos.tipo)!; 
                    const item:{id:string, id_trasladoinv:string, fkproducto:string, idsubproducto_id:string, iditem: string, nombreitem: string, tipo: string, unidadmedida: string, cantidad: number, factor: number} = {
                        id: '',
                        id_trasladoinv: id!,
                        fkproducto: itemselected.tipo=='0'?itemselected.id:'NULL', 
                        idsubproducto_id: itemselected.tipo=='1'?itemselected.id:'NULL',
                        iditem: itemselected?.id!,
                        nombreitem: itemselected.text,
                        tipo: itemselected.tipo,  ////tipo = 0 es producto simple,  1 = subproducto
                        unidadmedida: itemselected.unidadmedida,
                        cantidad: cantidad,
                        factor: 1,
                    }
                    carrito = [...carrito, item];
                    printItemTable(itemselected.id, itemselected.tipo, itemselected.unidadmedida, cantidad, itemselected.text);
                }else{
                    carrito[index].cantidad = cantidad;
                    const tr = document.querySelector(`[data-id="${carrito[index].iditem}"][data-tipo="${carrito[index].tipo}"]`)!;
                    tr.children[1].textContent = carrito[index].cantidad+'';
                }
            }
        });

        function printItemTable(id:string, tipo:string, unidadmedida:string, cantidad:number, nombre:string){
            let options = `<option data-factor="1" value="" >${unidadmedida}</option>`;
            /*if(tipo=='1'){   //si es un subproducto
                options = "";
                const subproductounidades = allConversionUnidades.filter(x => x.idsubproducto == id); 
                subproductounidades.forEach(u=>options+=`<option data-factor="${u.factorconversion}" value="${u.idsubproducto}" >${u.nombreunidaddestino}</option>`);
            }
            if(tipo=='0'){
                options = "";
                const productounidades = allConversionUnidades.filter(x => x.idproducto == id); 
                productounidades.forEach(u=>options+=`<option data-factor="${u.factorconversion}" value="${u.idproducto}" >${u.nombreunidaddestino}</option>`);
            }*/
            const tr = document.createElement('TR');
            tr.classList.add('itemselect');
            tr.dataset.id = `${id}`;
            tr.dataset.tipo = `${tipo}`;
            tr.insertAdjacentHTML('afterbegin', 
                `<td class="p-4">${nombre}</td>
                <td class="p-4">${cantidad}</td>
                <td class="p-4 flex items-center justify-center gap-2">
                    <button class="flex items-center gap-1 px-3 py-1 text-base text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">
                        👁 Ver
                    </button>
                    <button class="w-11 h-11 flex items-center justify-center text-green-600 border border-green-200 rounded-full hover:bg-green-50 text-xl">✅</button>
                    <button class="eliminarItem w-11 h-11 flex items-center justify-center text-rose-600 border border-rose-200 rounded-full hover:bg-rose-50 text-xl">❌</button>
                </td>`);
            tablaItems?.appendChild(tr);
        }


        /*function printDetalleOrden(detalleOrden:{id:string, sucursal_origen:string, sucursal_destino:string, tipo:string, fkusuario:string, nombreusuario:string, estado:string}){
            console.log(detalleOrden);

        }*/


        //////////////////////////////////// evento a la tabla de los productos seleccionados  ///////////////////////////////////
        tablaItems?.addEventListener('click', (e:Event)=>{
            const elementItem = (e.target as HTMLElement)?.closest('.itemselect'); //seleccionamos el tr de la tabla
            const iditem = (elementItem as HTMLElement).dataset.id!;
            const tipoitem = (elementItem as HTMLElement).dataset.tipo!;
        
            if((e.target as HTMLElement).classList.contains('eliminarItem')){ //se trae todoscon true menos el que coincida con iditem y tipoitem
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
                tablaItems?.querySelector(`TR[data-id="${iditem}"][data-tipo="${tipoitem}"]`)?.remove();
            }
            //itemCarrito.cantidad = itemCarrito.cantidadcomprado*itemCarrito.factor;
        });


        btnUpdateTrasladoInv.addEventListener('click', ()=>{
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Esta seguro de actualizar la orden de transferencia?',
                text: "Se procesara la orden con todos los productos para la sede destino",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (async ()=>{ 
                        const datos = new FormData();
                        datos.append('id_trasladoinv', id!);
                        datos.append('ids', JSON.stringify(carrito.map(x=>x.id)));
                        datos.append('nuevosproductos', JSON.stringify(carrito));
                        try {
                            const url = "/admin/api/editarOrdenTransferencia";  //api llamada a trasladosinvcontrolador
                            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                            const resultado = await respuesta.json();  
                            if(resultado.exito !== undefined){
                                setTimeout(() => {
                                    window.location.href = `/admin/almacen/trasladarinventario`;
                                }, 1600);
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