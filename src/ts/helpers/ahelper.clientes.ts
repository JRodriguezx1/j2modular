(()=>{
  if(!document.querySelector('.ventas')&&!document.querySelector('.modorapido'))return;

    const POS = (window as any).POS;

    const btnAddCliente = document.querySelector('#addcliente') as HTMLElement;
    const btnAddDir = document.querySelector('#adddir') as HTMLElement;
    const miDialogoAddCliente = document.querySelector('#miDialogoAddCliente') as any;
    //const miDialogoAddDir = document.querySelector('#miDialogoAddDir') as any;
    const selectCliente = document.querySelector('#selectCliente') as HTMLSelectElement;
    const tel = document.querySelector('#telefono')! as HTMLInputElement;
    const doc = document.querySelector('#identificacion')! as HTMLInputElement;
    const dirEntrega = document.querySelector('#direccionEntrega')! as HTMLSelectElement;
    const direccionPreferidaPorCliente = new Map<string, string>();

  const gestionClientes = {
    selectCliente,
    dirEntrega,
    miDialogoAddCliente,
    //miDialogoAddDir,

    clientes(){
        
        //////////// evento al boton añadir cliente nuevo //////////////
        btnAddCliente?.addEventListener('click', (e)=>{
            miDialogoAddCliente.showModal();
            ($('#selectCliente') as any).select2({
                dropdownParent: $('#miDialogoAddCliente'),
                placeholder: "Seleccionar el cliente",
                maximumSelectionLength: 1
            });
            document.addEventListener("click", POS.cerrarDialogoExterno);
        });
        //////////// evento al boton añadir nueva direccion //////////////
        /*btnAddDir?.addEventListener('click', (e)=>{
            miDialogoAddDir.showModal();
            document.addEventListener("click", POS.cerrarDialogoExterno);
        });*/
        //////////// evento al btn submit del formulario add nuevo cliente //////////////
        document.querySelector('#formAddCliente')?.addEventListener('submit', e=>{
            e.preventDefault();
            (async ()=>{
                const direccionSeleccionada = dirEntrega.value;
                const datos = new FormData();
                datos.append('idcliente', selectCliente?.value??null); //id del cliente
                datos.append('nombre', (document.querySelector('#nombreclientenuevo') as HTMLInputElement).value);
                datos.append('apellido', (document.querySelector('#clientenuevoapellido') as HTMLInputElement).value || '  ');
                datos.append('tipodocumento', (document.querySelector('#tipodocumento') as HTMLInputElement).value);
                datos.append('telefono', (document.querySelector('#telefono') as HTMLInputElement).value);
                datos.append('iddireccion', direccionSeleccionada);
                //datos.append('idtarifa', (document.querySelector('#clientenuevotarifa') as HTMLSelectElement).value);
                datos.append('tipodocumento', (document.querySelector('#tipodocumento') as HTMLInputElement).value);
                datos.append('identificacion', (document.querySelector('#identificacion') as HTMLInputElement).value);
                datos.append('email', (document.querySelector('#clientenuevoemail') as HTMLInputElement).value);
                datos.append('departamento', (document.querySelector('#departamento') as HTMLInputElement).value||'No definido');
                datos.append('ciudad', (document.querySelector('#ciudad') as HTMLInputElement).value || 'No definido');
                datos.append('direccion', (document.querySelector('#clientenuevodireccion') as HTMLInputElement).value);
                try {
                    let url = "/admin/api/apiCrearCliente";
                    if(selectCliente.value!=='')url = "/admin/api/actualizarCliente";

                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    const clienteGuardado = resultado.nextID || selectCliente.value;
                    if(clienteGuardado && direccionSeleccionada)direccionPreferidaPorCliente.set(clienteGuardado, direccionSeleccionada);
                    if(selectCliente.value=='')addClienteSelect(resultado.nextID);
                    if(selectCliente.value!=='')$('#selectCliente').val(clienteGuardado).trigger('change');
                    //POS.limpiarformdialog();
                    }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
            miDialogoAddCliente.close();
            document.removeEventListener("click", POS.cerrarDialogoExterno);
        });
        /////////////// añadir cliente al select despues de crearse ///////////////////
        function addClienteSelect(clienteID:string): void{
            const option = document.createElement('option');
            option.textContent = (document.querySelector('#nombreclientenuevo') as HTMLInputElement).value + " " + (document.querySelector('#clientenuevoapellido') as HTMLInputElement).value;
            option.value = clienteID;
            option.dataset.tipodocumento = (document.querySelector('#tipodocumento') as HTMLInputElement).value;
            option.dataset.identidad = (document.querySelector('#identificacion') as HTMLInputElement).value;
            selectCliente?.appendChild(option);
            $('#selectCliente').val(clienteID).trigger('change'); // seleccionar el cliente nuevo, en el select y dispara evento
        }
        
        //evento 'cambio' al selecionar cliente, tambien se ejecuta cuando se crea cliente nuevo//
        $("#selectCliente").on('change', (e)=>{
            const idcli = (e.target as HTMLInputElement).value;
            if(idcli){
                (async ()=>{
                    try {
                        const url = "/admin/api/clientes/direccionesXcliente?id="+idcli; //llamado a la API REST y se trae el cliente y sus direcciones direccionescontrolador, tambien traer sus precios personalizados
                        const respuesta = await fetch(url); 
                        const resultado = await respuesta.json(); 
                        (document.querySelector('#nombreclientenuevo') as HTMLInputElement).value = resultado.nombre;
                        (document.querySelector('#clientenuevoapellido') as HTMLInputElement).value = resultado.apellido;
                        (document.querySelector('#telefono') as HTMLInputElement).value =  resultado.telefono;
                        addDireccionSelect(resultado);
                        (document.querySelector('#tipodocumento') as HTMLSelectElement).value = resultado.tipodocumento;
                        (document.querySelector('#identificacion') as HTMLInputElement).value = resultado.identificacion;
                        (document.querySelector('#clientenuevoemail') as HTMLInputElement).value = resultado.email;
                        (document.querySelector('#resumenCliente') as HTMLParagraphElement).textContent = resultado.nombre+' '+resultado.apellido;
                        (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = 'SELECCIONADO';
                        (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-gray-100', 'text-gray-700');
                        (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-green-100', 'text-green-600');
                        //mapear los precios personalizados del cliente con arreglo de los productos
                        actualizarPreciosCliente(resultado.preciospersonalizados);
                        console.log(resultado);
                        POS.gestionRedmir.actualizarPuntosDOM(`${resultado.nombre} ${resultado.apellido}`, resultado.puntos);
                    } catch (error) {
                        console.log(error);
                    }
                })();   
            }else{
                (document.querySelector('#resumenCliente') as HTMLParagraphElement).textContent = 'Seleccionar cliente';
                (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = 'SIN CLIENTE';
                (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-green-100', 'text-green-600');
                (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-gray-100', 'text-gray-700');
                //actualizar array de products y DOM
                for(const prod of POS.products){
                    const item = POS.hackerList.get('id', prod.id)[0];
                    prod.precio_venta = prod.precio_original;
                    if(item)item.elm.querySelector('.precioVenta').textContent = '$'+Number(prod.precio_venta).toLocaleString();
                }
                //actualizar el carrito de ventas con los valores originales
                if(POS.carrito)
                    POS.carrito.forEach((cr:CarritoItem)=>{
                        cr.valorunidad = POS.products.find((p:productsapi)=>p.id==cr.idproducto)?.precio_venta??cr.valorunidad;
                        cr.subtotal = cr.valorunidad * cr.stock!;
                        cr.total = cr.subtotal;
                        cr.valorcomision = (cr.subtotal*cr.percentcomision)/100;
                        cr.base = 1/(1+(Number(cr.impuesto)/100))*cr.subtotal;
                        cr.valorimp = cr.subtotal - cr.base;
                    });
                POS.valorCarritoTotal();
            }
        });

        ////// añade direccion al select de direcciones, cuando se selecciona o se agrega un cliente o si se agrega un nueva direccion/////
        function addDireccionSelect<T extends {id:string, nombre:string, apellido:string, telefono:string, identificacion:string, email:string, direcciones:{id:string, idcliente:string, idtarifa:string, tarifa:{id:string, idcliente:string, nombre:string, valor:string}, direccion:string, departamento:string, ciudad:string}[]}>(addrs: T):void{
            while(dirEntrega?.firstChild)dirEntrega.removeChild(dirEntrega?.firstChild);
            const setTarifas = new Set();
            POS.tarifas.length = 0;
            const direccionPreferida = direccionPreferidaPorCliente.get(addrs.id);
            addrs.direcciones.forEach(dir =>{
                const option = document.createElement('option');
                option.textContent = dir.direccion;
                option.value = dir.id;
                option.dataset.idcliente = dir.idcliente;
                option.dataset.idtarifa = dir.idtarifa;
                option.dataset.ciudad = dir.ciudad;
                dirEntrega.appendChild(option);
                dir.tarifa.idcliente = dir.idcliente;
                if(!setTarifas.has(dir.tarifa.id)){
                POS.tarifas = [...POS.tarifas, dir.tarifa];
                setTarifas.add(dir.tarifa.id);
                }
            });
            if(direccionPreferida && addrs.direcciones.some(dir=>dir.id === direccionPreferida)){
                dirEntrega.value = direccionPreferida;
            }
            setTarifas.clear();
            POS.printTarifaEnvio();
            POS.valorCarritoTotal();
            const direccionActual = addrs.direcciones.find(dir=>dir.id === dirEntrega.value) || addrs.direcciones[0];
            (document.querySelector('#departamento') as HTMLInputElement).value = direccionActual?.departamento??'No especificado';
            (document.querySelector('#ciudad') as HTMLInputElement).value = direccionActual?.ciudad??'No especificado';
        }


        function actualizarPreciosCliente(preciosDelCliente:{id:string, idcliente:string, idproducto:string, precioxcliente:string}[]){
            //resetar o eliminar precios personalizados de clientes anteriores
            const mapaPrecios = new Map( preciosDelCliente.map(p => [p.idproducto, p.precioxcliente]) );
            POS.products.forEach((prod:productsapi) => {
                if(mapaPrecios.has(prod.id)){
                    prod.precio_venta = mapaPrecios.get(prod.id)!;
                }else{
                    prod.precio_venta = prod.precio_original!;
                }
            });

            for(const prod of mapaPrecios.entries()){
                const item = POS.hackerList.get('id', prod[0])[0];
                if(item)item.elm.querySelector('.precioVenta').textContent = '$'+Number(prod[1]).toLocaleString();
            }
        }


        ///////// Evento al input de telefono para buscar cliente por telefono ////////////
        tel?.addEventListener('input', (e:Event)=>{
            const contact = (e.target as HTMLInputElement).value.trim();
            if (contact.length < 6) return;
            const cli = clientesDB.find(c => c.telefono === contact);
            console.log(cli);
            if(cli!=undefined)$('#selectCliente').val(cli.id).trigger('change'); 
        });


        ///////// Evento al input de identificacion para buscar cliente por documento ////////////
        doc?.addEventListener('input', (e:Event)=>{
            const iD = (e.target as HTMLInputElement).value.trim();
            if (iD.length < 5) return;
            const cli = clientesDB.find(c => c.identificacion === iD);
            console.log(cli);
            if(cli!=undefined)$('#selectCliente').val(cli.id).trigger('change'); 
        });
        

        ///////// Evento al select de direcciones ////////////
        dirEntrega?.addEventListener('change', (e)=>{
            const select = (e.target as HTMLSelectElement);
            if(selectCliente.value && select.value)direccionPreferidaPorCliente.set(selectCliente.value, select.value);
            const x:string = select.options[select.selectedIndex].dataset.ciudad||'';
            (document.querySelector('#ciudad') as HTMLInputElement).value = x;
            POS.printTarifaEnvio();
            POS.valorCarritoTotal();
        });

        ////////////////// evento al btn submit del formulario add direccion //////////////////////
        /*document.querySelector('#formAddDir')?.addEventListener('submit', e=>{
            e.preventDefault();
            (async ()=>{
                const datos = new FormData();
                datos.append('idcliente', selectCliente.value);
                datos.append('departamento', (document.querySelector('#adddepartamento') as HTMLInputElement).value);
                datos.append('ciudad', (document.querySelector('#addciudad') as HTMLInputElement).value);
                datos.append('direccion', (document.querySelector('#adddireccion') as HTMLInputElement).value);
                datos.append('idtarifa', (document.querySelector('#tarifa') as HTMLSelectElement).value);
                try {
                    const url = "/admin/api/addDireccionCliente";  //direccionescontrolador
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    addDireccionSelect(resultado.direcciones);
                    }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
            //miDialogoAddDir.close();
            //document.removeEventListener("click", POS.cerrarDialogoExterno);
        });*/
        
    },

    resaltarSelectorCliente():void{
      if(!btnAddCliente)return;
      btnAddCliente.classList.remove('cliente-required-pulse');
      void btnAddCliente.offsetWidth;
      btnAddCliente.classList.add('cliente-required-pulse');
      btnAddCliente.scrollIntoView({behavior: 'smooth', block: 'center'});
      setTimeout(()=>btnAddCliente.classList.remove('cliente-required-pulse'), 3600);
    }
    
  };

  (window as any).POS.gestionClientes = gestionClientes;


})();
