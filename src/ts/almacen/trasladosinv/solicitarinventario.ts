(()=>{

    if(document.querySelector('.solicitarinvnetario')){
        // PROCESO SOLICITUD DE INVENTARIO A OTRAS SEDES
        const btnAnterior = document.getElementById("btnAnteriorSolicitudInv") as HTMLButtonElement;
        const btnSiguiente = document.getElementById("btnSiguienteSolicitudInv") as HTMLButtonElement;
        const steps = document.querySelectorAll<HTMLDivElement>(".step-circle");
        const stepSections = document.querySelectorAll<HTMLElement>(".step-section");
        const btnAddItem = document.querySelector('#btnAddItem') as HTMLButtonElement;
        const tablaItems = document.querySelector('#tablaItems tbody');
        const resumenSedeDestino = document.querySelector('#resumenSedeDestino') as HTMLParagraphElement;
        const resumenFecha = document.querySelector('#resumenFecha') as HTMLParagraphElement;
        const resumenObservaciones = document.querySelector('#resumenObservaciones') as HTMLParagraphElement;
        let carrito:{iditem:string, idpx:string, idsx:string, tipo: string, nombreitem:string, unidad:string, cantidad: number, factor: number, impuesto: number, valorunidad: number, subtotal: number, precio_compra: number, valorcompra: number}[]=[];
        let filteredData: {id:string, text:string, tipo:string, sku:string, unidadmedida:string}[];   //tipo = 0 es producto simple,  1 = subproducto
        let currentStep = 1;
        const totalSteps = 3;

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


        //establecer fecha por defecto
        const inputFecha = document.getElementById('fecha') as HTMLInputElement;
            const hoy = new Date();
            const yyyy = hoy.getFullYear();
            const mm = String(hoy.getMonth() + 1).padStart(2, '0'); // Mes con 2 dígitos
            const dd = String(hoy.getDate()).padStart(2, '0');      // Día con 2 dígitos
            inputFecha.value = `${yyyy}-${mm}-${dd}`;

        function actualizarProgreso() {
            // Actualizar barra de progreso
            steps.forEach((step, index) => {
                const stepNumber = index + 1;
                if (stepNumber < currentStep) {
                step.className =
                    "step-circle w-10 h-10 rounded-full bg-green-500 text-white font-semibold flex items-center justify-center";
                } else if (stepNumber === currentStep) {
                step.className =
                    "step-circle w-10 h-10 rounded-full bg-indigo-600 text-white font-semibold flex items-center justify-center";
                } else {
                step.className =
                    "step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center";
                }
            });

            // Mostrar solo la sección actual
            stepSections.forEach((section, index) => {
                if (index + 1 === currentStep) {
                section.style.display = "block";
                } else {
                section.style.display = "none";
                }
            });

            // Deshabilitar/activar botones si es necesario
            btnAnterior.disabled = currentStep === 1;
            //btnSiguiente.disabled = currentStep === totalSteps;
        }

        // Inicializar vista
        actualizarProgreso();

        // Eventos de navegación
        btnSiguiente.addEventListener("click", () => {
            if (currentStep < totalSteps) {
                currentStep++;
                if(currentStep===3 && carrito.length===0){
                    currentStep--;
                    return;
                }
                if(currentStep===3 && carrito.length>0)resumen();
                actualizarProgreso();
            }else{
                generarorden();
            }
            
        });

        btnAnterior.addEventListener("click", () => {
            if (currentStep > 1) {
                currentStep--;
                actualizarProgreso();
            }
        });


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
                    const itemselected = filteredData.find(x=>x.id==datos.id&&x.tipo==datos.tipo)!; //products es el arreglo de todos los productos traido por api
                    const item:{iditem: string, idpx: string, idsx: string, tipo: string, nombreitem: string, unidad: string, cantidad: number, factor: number, impuesto: number, valorunidad: number, subtotal: number, precio_compra:number, valorcompra: number} = {
                        iditem: itemselected?.id!,
                        idpx: itemselected.tipo=='0'?itemselected.id:'NULL',
                        idsx: itemselected.tipo=='1'?itemselected.id:'NULL',
                        tipo: itemselected.tipo,  ////tipo = 0 es producto simple,  1 = subproducto
                        nombreitem: itemselected.text,
                        unidad: itemselected.unidadmedida,
                        cantidad: cantidad,
                        factor: 1,
                        impuesto: 0,
                        valorunidad: 0,
                        subtotal: 0,
                        precio_compra: 0,
                        valorcompra: 0,
                    }
                    carrito = [...carrito, item];
                    printItemTable(datos.id, datos.tipo, datos.unidadmedida, cantidad);
                }else{
                    carrito[index].cantidad = cantidad;
                    const tr = document.querySelector(`[data-id="${carrito[index].iditem}"][data-tipo="${carrito[index].tipo}"]`)!;
                    tr.children[1].textContent = carrito[index].cantidad+'';
                }
            }
        });

        function printItemTable(id:string, tipo:string, unidadmedida:string, cantidad:number){
            let options = `<option data-factor="1" value="" >${unidadmedida}</option>`;
            const unItem = filteredData.find(x=>x.id==id&&x.tipo==tipo)!;
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
                `<td class="p-4">${unItem?.text}</td>
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


        //////////////////////////////////// evento a la tabla de los productos seleccionados  ///////////////////////////////////
        tablaItems?.addEventListener('click', (e:Event)=>{
            const elementItem = (e.target as HTMLElement)?.closest('.itemselect'); //seleccionamos el tr de la tabla
            const iditem = (elementItem as HTMLElement).dataset.id!;
            const tipoitem = (elementItem as HTMLElement).dataset.tipo!;

            const itemCarrito = carrito.find(x=>x.iditem==iditem&&x.tipo==tipoitem)!;
            
        
            if((e.target as HTMLElement).classList.contains('eliminarItem')){
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
                //resumen();
            }

            //itemCarrito.cantidad = itemCarrito.cantidadcomprado*itemCarrito.factor;
        });

        function resumen(){
            const tablaproductosresumen = document.querySelector('#tablaproductosresumen tbody');
            while(tablaproductosresumen?.firstChild)tablaproductosresumen.removeChild(tablaproductosresumen.firstChild);
            resumenSedeDestino.textContent = (document.querySelector('#sucursaldestino') as HTMLSelectElement).textContent;
            resumenFecha.textContent = inputFecha.value;
            resumenObservaciones.textContent = (document.querySelector('#observaciones') as HTMLTextAreaElement).value;

            carrito.forEach(z=>{
                const tr = document.createElement('tr');
                const tdnombre = document.createElement('td');
                tdnombre.textContent = z.nombreitem;
                tr?.appendChild(tdnombre);
                const tdcantidad = document.createElement('td');
                tdcantidad.textContent = z.cantidad+'';
                tr?.appendChild(tdcantidad);
                const tdund = document.createElement('td');
                tdund.textContent = z.unidad;
                tr?.appendChild(tdund);
                tablaproductosresumen?.appendChild(tr);
            });
        }

        function generarorden(){
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Esta seguro de generar orden de solicitud?',
                text: "Se procesara la orden con todos los productos en la sede destino",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    const idsucursalorigen = document.querySelector('#sucursalorigen') as HTMLSelectElement;
                    const sucursaldestino = document.querySelector('#sucursaldestino') as HTMLSelectElement;
                    (async ()=>{ 
                        const datos = new FormData();
                        datos.append('idsucursalorigen', idsucursalorigen.value);
                        datos.append('idsucursaldestino', sucursaldestino.value);
                        datos.append('productos', JSON.stringify(carrito));
                        try {
                            const url = "/admin/api/apisolicitarinventario";  //api llamada a trasladosinvcontrolador.php
                            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                            const resultado = await respuesta.json();  
                            if(resultado.exito !== undefined){
                                setTimeout(() => {
                                    window.location.href = `/admin/almacen/solicitudesrecibidas`;
                                }, 600);
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
        }

    }
})();