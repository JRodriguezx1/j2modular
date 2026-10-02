(()=>{
    if(document.querySelector('.almacen')){
        const btntrasladoinvnetario = document.querySelector('#btntrasladoinvnetario') as HTMLInputElement;
        const btnMasOpciones = document.querySelector('#btnMasOpciones') as HTMLInputElement;
        const btnviewProductsBajoStock = document.querySelector('#btnviewProductsBajoStock') as HTMLButtonElement;
        const miDialogoTrasladoInvnetario = document.querySelector('#miDialogoTrasladoInvnetario') as any;
        const miDialogoMasOpciones = document.querySelector('#miDialogoMasOpciones') as any;
        const reinciarinv = document.querySelector('#reinciarinv') as HTMLButtonElement;
        const miDialogoBajoStock = document.querySelector('#miDialogoBajoStock') as HTMLDialogElement;
        const miDialogoStock = document.querySelector('#miDialogoStock') as any;
        const modalStock = document.querySelector('#modalStock') as HTMLElement;
        const selectStockRapidoUndmedida = document.querySelector('#selectStockRapidoUndmedida') as HTMLSelectElement;
        let amount = document.querySelector('#cantidadStockRapido') as HTMLInputElement; //input cantidad de modal de stock rapido
        let stockAux = document.querySelector('#stockAux') as HTMLInputElement; //input cantidad aux de modal de stock rapido

        const miDialogoIngresarProduccion = document.querySelector('#miDialogoIngresarProduccion') as any;
        const selectitemAproducir = document.querySelector('#itemAproducir') as HTMLSelectElement;
        const btnsproduccion = document.querySelectorAll<HTMLButtonElement>('.btnproduccion');  //btns ingresar descontar ajustar produccion
        const selectIngresarProduccionUnidadmedida = document.querySelector('#selectIngresarProduccionUnidadmedida') as HTMLSelectElement;
        const amountProduccion = document.querySelector('#stockIngresarProduccion') as HTMLInputElement;  //input cantidad cuando se ingresa orden de produccion
        let cantidadactual = 0, indiceFila=0, endponit:string='', factorC = 0, idUnidMedida:string, tipoelemento:string = '', idelemento:string = '', tablaStockRapido:HTMLElement, tablaInventarioSedes:HTMLTableElement;
    
        const sedes = document.querySelector('#sedes');
        const tablaBajoStock = document.querySelector('#tablaBajoStock tbody') as HTMLBodyElement;

        interface datosProducto { stock?: string, sku?:string, precio_compra: string, precio_venta: string }

        type conversionunidadesapi = {
            id:string,
            idproducto:string,
            idsubproducto: string,
            idunidadmedidabase: string,
            idunidadmedidadestino: string,
            nombreunidadbase: string,
            nombreunidaddestino: string,
            factorconversion: string,
          };

          type productosXsucursal = {
            productoid:string,
            subproductoid: string,
            nombreproducto:string,
            sucursalid: string,
            sucursal: string,
            stock: string,
          };

        let allproductsXsucursal:productosXsucursal[]=[], allConversionUnidades:conversionunidadesapi[] = [];
        (async ()=>{
            try {
                const url = "/admin/api/allConversionesUnidades"; //llamado a la API REST en el controlador almacencontrolador para treaer todas las conversiones de unidades
                const respuesta = await fetch(url); 
                allConversionUnidades = await respuesta.json();
            } catch (error) {
                console.log(error);
            }
        })();

        //apertura de la ventana modal para las opcionesde traslado de inventario
        btntrasladoinvnetario.addEventListener('click', ()=>{
            miDialogoTrasladoInvnetario.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        });

        //apertura de la ventana modal para las opcionesde traslado de inventario
        btnMasOpciones.addEventListener('click', ()=>{
            miDialogoMasOpciones.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        });


        btnviewProductsBajoStock.addEventListener('click', ()=>{
            getItemsBajoStock();
            miDialogoBajoStock.showModal();
            document.addEventListener("click", cerrarDialogoExterno); 
        });

        async function getItemsBajoStock(){
            try {
                const url = "/admin/api/getItemsBajoStock";
                const respuesta = await fetch(url); 
                const resultado:{productoid:string, nombre:string, idunidadmedida:string, sku:string, stock:string, stockminimo:string, tipoproducto:string, tipoproduccion:string, tipoitem:string, unidadmedida:string, visible:string}[] = await respuesta.json();
                Object.assign(configdatatablesstockbajo, {
                    data: resultado,
                    columns: [
                        {title: 'ID', data: 'productoid'},
                        {title: 'NOMBRE', data: 'nombre'},
                        {
                            title: 'TIPO', 
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: (data: any, type: any, row: any) => row.tipoitem
                        },
                        {title: 'STOCK ACTUAL', data: 'stock'},
                        {title: 'STOCK MIN', data: 'stockminimo'},
                    ],
                });
                ($('#tablaBajoStock') as any).DataTable(configdatatablesstockbajo); 
            } catch (error) {
                console.log(error);
            }
        }

        reinciarinv.addEventListener('click', (e)=>{
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea reiniciar el inventario a cero?',
                text: "Todos los productos e insumos del inventario se reiniciaran a cero.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
                    (async ()=>{
                        try {
                            const url = "/admin/api/reiniciarinv"; //llamado a la API REST y se trae las direcciones segun cliente elegido
                            const respuesta = await fetch(url, {method: 'POST'});
                            const resultado = await respuesta.json();
                            (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
                            if(resultado.exito !== undefined){ 
                                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                                setTimeout(() => {
                                window.location.reload();  
                                }, 1270);
                            }else{
                                msjalertToast('error', '¡Error!', resultado.error[0]);
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();
                }
            });
        });


        sedes?.addEventListener('change', (e:Event)=>{
            if((e.target as HTMLInputElement).checked){
                (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
                (async ()=>{
                    try {
                        const url = "/admin/api/getStockproductosXsucursal"; //llamado a la API REST en el controlador almacencontrolador para treaer todos los productos con su respectiva sucursal y stock
                        const respuesta = await fetch(url); 
                        allproductsXsucursal = await respuesta.json();
                        printstockXsucursal();
                        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
                    } catch (error) {
                        console.log(error);
                    }
                })();
            }
        });


        /////////////////////////////////////  SOTCK RAPIDO ////////////////////////////////////////
        /*document.querySelector('#stockproducto')?.addEventListener('click', (e)=>{
            if((e.target as HTMLInputElement).checked){
                 tablaStockRapido = ($('#tablaStockRapido') as any).DataTable(configdatatables);
            }
        })*/
        tablaStockRapido = ($('#tablaStockRapido') as any).DataTable(configdatatablesstockrapidoinv);

        document.querySelector('#tablaStockRapido')?.addEventListener("click", (e:Event)=>{ //evento click sobre toda la tabla
            let options:string = '';
            const target = e.target as HTMLElement;

           if(target.closest('.btnsproducto')){
                idelemento = target.closest('.btnsproducto')!.id;
                tipoelemento = '0'; //si es producto
                cantidadactual = Number((target.closest('.btnsproducto') as HTMLElement).dataset.stock);
                document.querySelector('#nombreItemstockrapido')!.textContent = (target.closest('.btnsproducto') as HTMLElement).dataset.nombre!;
            }
            if(target.closest('.btnssubproducto')){
                idelemento = target.closest('.btnssubproducto')!.id;
                tipoelemento = '1'; //si es un subproducto
                cantidadactual = Number((target.closest('.btnssubproducto') as HTMLElement).dataset.stock);
                document.querySelector('#nombreItemstockrapido')!.textContent = (target.closest('.btnssubproducto') as HTMLElement).dataset.nombre!;
            }

            while(selectStockRapidoUndmedida.firstChild)selectStockRapidoUndmedida.removeChild(selectStockRapidoUndmedida.firstChild);
            if(tipoelemento == '0'){
                const productounidades = allConversionUnidades.filter(x => x.idproducto == idelemento); 
                productounidades.forEach(u=>{
                    options+=`<option data-factor="${u.factorconversion}" value="${u.idproducto}" >${u.nombreunidaddestino}</option>`;
                    idUnidMedida = u.idunidadmedidabase;
                });
                selectStockRapidoUndmedida.insertAdjacentHTML('afterbegin', options);
            }
            if(tipoelemento == '1'){
                const subproductounidades = allConversionUnidades.filter(x => x.idsubproducto == idelemento); 
                subproductounidades.forEach(u=>{
                    options+=`<option data-factor="${u.factorconversion}" value="${u.idsubproducto}" >${u.nombreunidaddestino}</option>`;
                    idUnidMedida = u.idunidadmedidabase;
                });
                selectStockRapidoUndmedida.insertAdjacentHTML('afterbegin', options);
            }
            
            if((e.target as HTMLElement)?.classList.contains("descontarStock")||(e.target as HTMLElement).parentElement?.classList.contains("descontarStock"))descontarStock(e);
            if((e.target as HTMLElement)?.classList.contains("aumentarStock")||(e.target as HTMLElement).parentElement?.classList.contains("aumentarStock"))aumentarStock(e);
            if((e.target as HTMLElement)?.classList.contains("ajustarStock")||(e.target as HTMLElement).parentElement?.classList.contains("ajustarStock"))ajustarStock(e);
        });

        //EDITAR STOCK
        function descontarStock(e:Event){
            modalStock.textContent = "Descontar cantidad a inventario"
            endponit = "descontarstock";
            indiceFila = (tablaStockRapido as any).row((e.target as HTMLElement).closest('tr')).index();
            miDialogoStock.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        }

        function aumentarStock(e:Event){
            modalStock.textContent = "Ingresar cantidad a inventario"
            endponit = "aumentarstock";
            indiceFila = (tablaStockRapido as any).row((e.target as HTMLElement).closest('tr')).index();
            miDialogoStock.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        }

        function ajustarStock(e:Event){
            modalStock.textContent = "Ajustar cantidad a inventario"
            endponit = "ajustarstock";
            indiceFila = (tablaStockRapido as any).row((e.target as HTMLElement).closest('tr')).index();
            miDialogoStock.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        }


        document.querySelector('#formStock')?.addEventListener('submit', (e)=>{
            e.preventDefault();
            factorC = Number(selectStockRapidoUndmedida.options[selectStockRapidoUndmedida.selectedIndex].dataset.factor);
            let info = (tablaStockRapido as any).page.info(), AmountItem:number = factorC*Number(amount.value), promediostock = 1, stockAuxiliar = Number(stockAux.value);
            
            if(endponit == "descontarstock")cantidadactual = cantidadactual-AmountItem;
            if(endponit == "aumentarstock")cantidadactual = cantidadactual+AmountItem;
            if(endponit == "ajustarstock")cantidadactual = AmountItem;

            /*idUnidMedida === '1'
                ?
                (promediostock = cantidadactual>0?(stockAuxiliar/cantidadactual):0)
                :
                (promediostock = stockAuxiliar>0?(cantidadactual/stockAuxiliar):0);*/
            promediostock = stockAuxiliar>0?(AmountItem/stockAuxiliar):0;

            (async ()=>{
                const datos = new FormData();
                datos.append('tipoitem', tipoelemento);
                datos.append('iditem', idelemento);
                datos.append('cantidad', AmountItem.toString());
                datos.append('stockaux', stockAuxiliar.toString());
                datos.append('promediostock', promediostock.toString());
                try{
                    const url = "/admin/api/"+endponit;  //asocia el producto con el sub producto en la tabla productos_sub
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                        msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                        ////// reset form ///////
                        (document.querySelector('#formStock') as HTMLFormElement)?.reset();
                        //actualizar fila
                        //(tablaStockRapido as any).cell(indiceFila, 3).data(cantidadactual).draw();
                        //(tablaStockRapido as any).page(info.page).draw('page'); //me mantiene la pagina actual
                        //actualiza tabla stock rapido
                        actualizarStockRapido([resultado.item[0]], tipoelemento);
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                    miDialogoStock.close();
                    document.removeEventListener("click", cerrarDialogoExterno);
                }catch(error){
                    console.log(error);
                }
            })();//cierre de async()
        });

    //////////////////////////  UTILIDAD PRODUCTO  ///////////////////////////
    ($('#utilidadProductos') as any).DataTable(configdatatablesstockrapido);  


    //////////////////////////// SOTCK POR SUCURSAL //////////////////////
    function printstockXsucursal(){
        const sucursales = [...new Set(allproductsXsucursal.map(d => d.sucursal))]; //lista unica de sucursales
        
        // 2. Reorganizar por producto
        const productos: Record<string, { nombreproducto: string; stocks: Record<string, string> }> = {};
        allproductsXsucursal.forEach(d => {
            if(d.productoid){
                if (!productos['p'+d.productoid]) { //si el obj no es creado se crea con el nombre del producto pero el stock vacio
                    productos['p'+d.productoid] = { nombreproducto: d.nombreproducto, stocks: {} };
                }
                productos['p'+d.productoid].stocks[d.sucursal] = d.stock; //aqui se llena el stock, que es un obj y sus propiedades son las sedes
            }
            if(d.subproductoid){
                if (!productos['s'+d.subproductoid]) { //si el obj no es creado se crea con el nombre del producto pero el stock vacio
                    productos['s'+d.subproductoid] = { nombreproducto: 'S-'+d.nombreproducto, stocks: {} };
                }
                productos['s'+d.subproductoid].stocks[d.sucursal] = d.stock; //aqui se llena el stock, que es un obj y sus propiedades son las sedes
            }
        });

        // 3. Columnas dinámicas
        const columnas = [{ title: "Producto", data: "nombreproducto" }];
        sucursales.forEach(s => {
            columnas.push({ title: s, data: s });
        });

        // 4. Dataset
        const dataset = Object.values(productos).map(prod => {
            const fila: any = { nombreproducto: prod.nombreproducto };
            sucursales.forEach(s => {
                fila[s] = prod.stocks[s] ?? 0; //fila es un obj, se crea la propieadad con el nombre de la sucursal y se iguala al objeto stock copn propiedad sucursal.
            });
            return fila;
        });

        tablaInventarioSedes = ($('#tablaInventarioSedes') as any).DataTable({
            data: dataset,
            columns: columnas,
            layout: {
                topStart: {
                    buttons: [
                    {extend: 'copyHtml5', text: 'Copia'}, 
                    {extend: 'excelHtml5', title: 'InventarioXsede'}, 
                    {extend: 'pdfHtml5', title: 'InventarioXsede'}, 
                    {extend: 'print', title: 'InventarioXsede', text: 'Imprimir'},
                    'colvis'
                    ],
                    pageLength: 'pageLength'
                }
            },
            pageLength: 25,
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true,
            responsive: true,
            deferRender: true,
            //retrieve: true,
            processing: true,
            language: {
                search: 'Busqueda',
                emptyTable: 'No Hay datos disponibles',
                zeroRecords:    "No se encontraron registros coincidentes",
                lengthMenu: '_MENU_ Entradas por pagina',
                info: 'Mostrando 1 de _MAX_ registros',
                infoEmpty: 'No hay entradas a mostrar',
                infoFiltered: ' (filtrado desde _MAX_ registros)',
                paginate: {"first": "<<", "last": ">>", "next": ">", "previous": "<"}
            },
            destroy: true, // importante si recargas la tabla
        });

    }


    ///////////////////////////// INGRESAR ORDEN DE PRODUCCION /////////////////////////////////
        
        //SELECT2 DE itemAproducir
        ($('#itemAproducir') as any).select2({
            maximumSelectionLength: 1,
        });

        ////// evento al select del producto a producir ////// 
        $("#itemAproducir").on('change', (e)=>{
            const idproducto = (e.target as HTMLOptionElement).value;
            const selectedOption = $("#itemAproducir option:selected");
            if(idproducto){
                const prodund = allConversionUnidades.filter(x =>x.idproducto == idproducto);
                mostrarSelectUnidades(prodund);
                let producto: datosProducto; //inicializamos el objeto de tipo interfaz 
                producto = {
                    stock: selectedOption.data('stock'),
                    precio_compra: selectedOption.data('precio_compra'),
                    precio_venta: selectedOption.data('precio_venta')
                }
                mostrarInfoItem(producto);
            }
        });

        /////////// CARGAR LAS UNIDADES DE MEDIDA EN EL SELECT unidadmedida  ////////////
        function mostrarSelectUnidades(prodund:conversionunidadesapi[]):void{
            while(selectIngresarProduccionUnidadmedida?.firstChild)selectIngresarProduccionUnidadmedida.removeChild(selectIngresarProduccionUnidadmedida?.firstChild);
            prodund.forEach(und =>{
              const option = document.createElement('option');
              option.textContent = und.nombreunidaddestino;
              option.value = und.id;  //und.id  =  es el id de la tabla conversionunidades
              option.dataset.factor = und.factorconversion;
              selectIngresarProduccionUnidadmedida.appendChild(option);
            });
        }

        btnsproduccion.forEach((btnproduccion, index) =>{
            btnproduccion.addEventListener('click', (e)=>{
               if(selectitemAproducir.value){    
                    document.querySelector('#nombreItemAProducir')!.textContent = selectitemAproducir.options[selectitemAproducir.selectedIndex].textContent;
                    if(index === 0){
                        endponit = "aumentarstock";
                        document.querySelector('#modalIngresarProduccion')!.textContent = "Ingresar produccion a inventario";
                    }
                    if(index === 1){
                        endponit = "descontarstock";
                        document.querySelector('#modalIngresarProduccion')!.textContent = "Descontar catnidad de inventario";
                    }
                    if(index === 2){
                        endponit = "ajustarstock";
                        document.querySelector('#modalIngresarProduccion')!.textContent = "Ajustar cantidad de inventario";
                    }
                    miDialogoIngresarProduccion.showModal();
                    document.addEventListener("click", cerrarDialogoExterno);
                }
            });
        });


        document.querySelector('#formIngresarProduccion')?.addEventListener('submit', (e)=>{
            e.preventDefault();
            factorC = Number(selectIngresarProduccionUnidadmedida.options[selectIngresarProduccionUnidadmedida.selectedIndex].dataset.factor);
            let AmountItemProduccion:number = factorC*Number(amountProduccion.value);

            (async ()=>{
                const datos = new FormData();
                datos.append('tipoitem', '0'); //0 = producto,  1 = subproducto
                datos.append('iditem', selectitemAproducir.value);
                datos.append('cantidad', AmountItemProduccion.toString());
                datos.append('construccion', '1');  //se indica al backend que es construccion y se debe descontar sus insumos
                try{
                    const url = "/admin/api/"+endponit;  //endpoint = aumentarstock para produccion, asocia el producto con el sub producto en la tabla productos_sub
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                        msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                        ////// reset form ///////
                        (document.querySelector('#formIngresarProduccion') as HTMLFormElement)?.reset();
                        //mostrar contaidad actual
                        mostrarInfoItem(resultado.item[0]); //producto
                        //actualizar tabla stock rapido
                        if(resultado.insumos !== undefined)actualizarStockRapido(resultado.insumos[0], '1'); //subproductos
                        //actualizar el selecto de los productos/insumos a producir
                        $("#itemAproducir option:selected").data('stock', resultado.item[0].stock);
                        
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                    miDialogoIngresarProduccion.close();
                    document.removeEventListener("click", cerrarDialogoExterno);
                }catch(error){
                    console.log(error);
                }
            })();//cierre de async()
        });


        function mostrarInfoItem(producto:{stock?:string, precio_compra:string, precio_venta:string}){
            document.querySelector('#stock')!.textContent = Number(producto.stock).toLocaleString();
            document.querySelector('#costoProduccion')!.textContent = "$"+Number(producto.precio_compra).toLocaleString();
            document.querySelector('#precioVenta')!.textContent = "$"+Number(producto.precio_venta).toLocaleString();
        }


        function actualizarStockRapido(insumosProductos:{id:string, productoid:string, subproductoid:string, sucursalid:string, stock:string, stockminimo:string, stockaux?:string}[], tipoItem:string){
            insumosProductos.forEach(element => {
                (tablaStockRapido as any).rows().every(function (this: any){
                    const rowNode = this.node() as HTMLTableRowElement;
                    let id_element = element.subproductoid, idAttr = rowNode.getAttribute('data-idsubproducto');
                    if(tipoItem == '0'){
                        idAttr = rowNode.getAttribute('data-idproducto');
                        id_element = element.productoid;
                    }
                    if (idAttr && idAttr === id_element){
                        this.cell(this.index(), 3).data(`<div class="text-center px-3 py-4 rounded-lg">${element.stock}</div>`);
                        this.cell(this.index(), 5).data(`${element.stockaux}`);

                        // Obtén el <td> real
                        const td = (tablaStockRapido as any).cell(this.index(), 3).node() as HTMLTableCellElement;
                        if(Number(element.stock) <= Number(element.stockminimo)){
                            td.firstElementChild?.classList.remove('bg-cyan-50', 'text-cyan-600');
                            td.firstElementChild?.classList.add('bg-red-50', 'text-red-800');
                        }else{
                            td.firstElementChild?.classList.remove('bg-red-50', 'text-red-800');
                            td.firstElementChild?.classList.add('bg-cyan-50', 'text-cyan-600');
                        }
                    } 
                });
            })
        }


        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
            if (f === miDialogoTrasladoInvnetario || f === miDialogoMasOpciones || f === miDialogoStock || f === miDialogoIngresarProduccion || f===miDialogoBajoStock || (f as HTMLInputElement).value === 'salir' || (f as HTMLElement).id == 'btnXCerrarTrasladoInvnetario' || (f as HTMLElement).id == 'btnXCerrarMasOpciones' || (f as HTMLElement).id == 'btnCerrarTotalBajoStock') {
                miDialogoStock.close();
                miDialogoIngresarProduccion.close();
                miDialogoTrasladoInvnetario.close();
                miDialogoMasOpciones.close();
                miDialogoBajoStock.close();
                document.removeEventListener("click", cerrarDialogoExterno);
            }
        }


        ///////////////////////////// GENERAR CODIGOS DE BARRAS /////////////////////////////////
        let productoCodeBar: datosProducto|undefined;
        let imagenBarcode: string | undefined;

        ($('#itemCodeBar') as any).select2({
            width: '100%',
            maximumSelectionLength: 1,
        });

        ////// evento al select del producto a producir ////// 
        $("#itemCodeBar").on('change', (e)=>{
            const idproducto = (e.target as HTMLSelectElement).value;
            const selectedOption = $("#itemCodeBar option:selected");
            if(idproducto){
                productoCodeBar = {
                    sku: selectedOption.data('sku'),
                    precio_compra: selectedOption.data('precio_compra'),
                    precio_venta: selectedOption.data('precio_venta'),
                };
                (document.querySelector('#codigoBarras') as HTMLInputElement).value = productoCodeBar.sku??'';
            }
        });

        ///////btn para generar codigo de barras
        document.querySelector('#formGenerarCodeBar')?.addEventListener('submit', (e)=>{
            e.preventDefault();
            
            if(!productoCodeBar || !productoCodeBar.sku){
                alert('Producto no seleccionado o no tiene codigo de barras');
                return;
            }

            const printPrecio = document.querySelector('#precioCodeBar') as HTMLSelectElement;

            const canvas = document.querySelector('#barcode') as HTMLCanvasElement;
            const options:any = {
                bcid: 'code128',       // tipo de código
                text: productoCodeBar.sku+'',     // valor (SKU o código)
                scale: 3,
                height: 10,
                includetext: false,
                textsize: 8,
                textyoffset: 2,
                textxalign: 'center'
            }

            if(printPrecio.value == '1'){
                 options.alttext = '$'+Number(productoCodeBar.precio_venta).toLocaleString();
            }else{
                delete options.alttext;
            }

            bwipjs.toCanvas(canvas, options);
            imagenBarcode = canvas.toDataURL("image/png");

        });
        

        document.querySelector('#btnImprimir')?.addEventListener('click', () => {
            if(!imagenBarcode){
                alert('Primero genere el código de barras');
                return;
            }
            const ventana = window.open('', '_blank');
            if(ventana){
                ventana?.document.write(`
                    <html>
                    <head>
                        <title>Etiqueta</title>
                        <style>
                            @page {
                                size: 60mm 40mm;
                                margin: 0;
                            }
                            body{
                                margin:0;
                                
                            }

                            .etiqueta{
                                text-align:center;
                                width:60mm;
                                height:40mm;
                            }

                            img{
                                width:100%;
                            }
                        </style>
                    </head>

                    <body>
                        <div class="etiqueta">
                            <img src="${imagenBarcode}" />
                        </div>
                    </body>
                    </html>
                `);
                ventana.document.close();
                ventana.onload = ()=>{
                    ventana?.focus();
                    ventana?.print();
                    setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana después de unos segundos
                };
            }
        });


    }

})();
