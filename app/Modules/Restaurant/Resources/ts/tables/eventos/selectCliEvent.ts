import { createCli } from './../cli';
const btnAddCliente = document.querySelector('#addcliente') as HTMLElement;
const miDialogoAddCliente = document.querySelector('#miDialogoAddCliente') as HTMLDialogElement;
const selectCliente = document.querySelector('#selectCliente') as HTMLSelectElement;
const tel = document.querySelector('#telefono')! as HTMLInputElement;
const doc = document.querySelector('#identificacion')! as HTMLInputElement;
const dirEntrega = document.querySelector('#direccionEntrega')! as HTMLSelectElement;
const direccionPreferidaPorCliente = new Map<string, string>();


type Cli = ReturnType<typeof createCli>;

//evento al boton de seleccionar cliente
export function registeraddclienteEvents(getClient: () => Cli|null, getContextVersion: () => number): void{

    let clientRequestVersion = 0;

    function iniciarOperacionCliente() {
        const versionConsulta = ++clientRequestVersion;
        // Capturamos el objeto y el contexto ANTES de consultar.
        const clientePedido = getClient();
        const versionContexto = getContextVersion();
        if(!clientePedido)return null;
        return {
            clientePedido,
            // Esta función se comprobará después del await.
            sigueVigente: () =>
                versionConsulta === clientRequestVersion &&
                versionContexto === getContextVersion() &&
                getClient() === clientePedido
        };
    }


    btnAddCliente?.addEventListener('click', (e)=>{
        miDialogoAddCliente.showModal();
        ($('#selectCliente') as any).select2({
            dropdownParent: $('#miDialogoAddCliente'),
            placeholder: "Seleccionar el cliente",
            maximumSelectionLength: 1
        });
        
    });

    //////////// evento al btn submit del formulario add nuevo cliente //////////////
    document.querySelector('#formAddCliente')?.addEventListener('submit', e=>{
        e.preventDefault();
        // Capturamos el pedido antes de comenzar a guardar.
        const operacion = iniciarOperacionCliente();
        if (!operacion) return;
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
                if(!operacion.sigueVigente())return;
                if(resultado.exito !== undefined){
                //msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                const clienteGuardado = resultado.nextID || selectCliente.value;
                if(clienteGuardado && direccionSeleccionada)direccionPreferidaPorCliente.set(clienteGuardado, direccionSeleccionada);
                if(selectCliente.value=='')addClienteSelect(resultado.nextID);
                if(selectCliente.value!=='')$('#selectCliente').val(clienteGuardado).trigger('change');
                //POS.limpiarformdialog();
                }else{
                //msjalertToast('error', '¡Error!', resultado.error[0]);
                }
            } catch (error) {
                console.log(error);
            }
        })();
        miDialogoAddCliente.close();
    
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
    $("#selectCliente").on('change', (e:any)=>{
        const idcli = (e.target as HTMLInputElement).value;
        const operacion = iniciarOperacionCliente();
        if(!operacion)return;

        // Una deselección explícita limpia memoria y presentación.
        // También invalida cualquier consulta anterior.
        if(!idcli){
            operacion.clientePedido.clear();
            renderCli(operacion.clientePedido.getCli());
            return;
        }

        if(idcli){
            (async ()=>{
                try{
                    const url = "/admin/api/clientes/direccionesXcliente?id="+idcli; //llamado a la API REST y se trae el cliente y sus direcciones direccionescontrolador, tambien traer sus precios personalizados
                    const respuesta = await fetch(url);
                    if (!respuesta.ok)throw new Error('No fue posible consultar el cliente.');
                    const resultado = await respuesta.json() as Cliente;

                    // Mientras esperábamos, pudo cambiar el cliente o la mesa.
                    // Una respuesta vieja no debe modificar memoria ni pantalla.
                    if(!operacion.sigueVigente())return;

                    // Primero actualizamos el objeto del pedido original.
                    operacion.clientePedido.addCli(resultado.id, resultado);
                    // Después dibujamos usando el estado guardado.
                    renderCli(operacion.clientePedido.getCli());
                    
                    //mapear los precios personalizados del cliente con arreglo de los productos
                    //actualizarPreciosCliente(resultado.preciospersonalizados);
                    //POS.gestionRedmir.actualizarPuntosDOM(`${resultado.nombre} ${resultado.apellido}`, resultado.puntos);
                }catch(error){
                    console.log(error);
                    if(operacion.sigueVigente())console.error('Error al consultar el cliente:', error);
                }
            })();
        }/*else{
            (document.querySelector('#accountPanelClientName') as HTMLParagraphElement).textContent = 'Seleccionar cliente';
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = 'SIN CLIENTE';
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-green-100', 'text-green-600');
            (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-gray-100', 'text-gray-700');
            //actualizar array de products y DOM
            /*for(const prod of POS.products){
                const item = POS.hackerList.get('id', prod.id)[0];
                prod.precio_venta = prod.precio_original;
                if(item)item.elm.querySelector('.precioVenta').textContent = '$'+Number(prod.precio_venta).toLocaleString();
            }*/
            //actualizar el carrito de ventas con los valores originales
            /*if(POS.carrito)
                POS.carrito.forEach((cr:CarritoItem)=>{
                    cr.valorunidad = POS.products.find((p:productsapi)=>p.id==cr.idproducto)?.precio_venta??cr.valorunidad;
                    cr.subtotal = cr.valorunidad * cr.stock!;
                    cr.total = cr.subtotal;
                    cr.valorcomision = (cr.subtotal*cr.percentcomision)/100;
                    cr.base = 1/(1+(Number(cr.impuesto)/100))*cr.subtotal;
                    cr.valorimp = cr.subtotal - cr.base;
                });*/
            //POS.valorCarritoTotal();
        //}
    });


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
        //POS.printTarifaEnvio();
        //POS.valorCarritoTotal();
    });

}


export function renderCli(cliente:Cliente){
    (document.querySelector('#formAddCliente') as HTMLFormElement)?.reset();
    $('#selectCliente').val('');
    (document.querySelector('#nombreclientenuevo') as HTMLInputElement).value = cliente.nombre;
    (document.querySelector('#clientenuevoapellido') as HTMLInputElement).value = cliente.apellido;
    (document.querySelector('#telefono') as HTMLInputElement).value =  cliente.telefono;
    addDireccionSelect(cliente);
    (document.querySelector('#tipodocumento') as HTMLSelectElement).value = cliente.tipodocumento;
    (document.querySelector('#identificacion') as HTMLInputElement).value = cliente.identificacion;
    (document.querySelector('#clientenuevoemail') as HTMLInputElement).value = cliente.email;
    (document.querySelector('#accountPanelClientName') as HTMLParagraphElement).textContent = cliente.id?cliente.nombre+' '+cliente.apellido:'Seleccionar cliente';
    (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = cliente.id?'SELECCIONADO':'SIN CLIENTE';
    (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove(cliente.id?'bg-gray-100':'bg-green-100', cliente.id?'text-gray-700':'text-green-600');
    (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add(cliente.id?'bg-green-100':'bg-gray-100', cliente.id?'text-green-600':'text-gray-700');
   
}


////// añade direccion al select de direcciones, cuando se selecciona o se agrega un cliente o si se agrega un nueva direccion/////
    function addDireccionSelect(addrs:Cliente):void{
        while(dirEntrega?.firstChild)dirEntrega.removeChild(dirEntrega?.firstChild);
        const setTarifas = new Set();
        //POS.tarifas.length = 0;
        const direccionPreferida = direccionPreferidaPorCliente.get(addrs.id);
        addrs.direcciones?.forEach(dir =>{
            const option = document.createElement('option');
            option.textContent = dir.direccion;
            option.value = dir.id;
            option.dataset.idcliente = dir.idcliente;
            option.dataset.idtarifa = dir.idtarifa;
            option.dataset.ciudad = dir.ciudad;
            dirEntrega.appendChild(option);
            //dir.tarifa.idcliente = dir.idcliente;
            //if(!setTarifas.has(dir.tarifa.id)){
            //POS.tarifas = [...POS.tarifas, dir.tarifa];
            //setTarifas.add(dir.tarifa.id);
            //}
        });
        if(direccionPreferida && addrs.direcciones?.some(dir=>dir.id === direccionPreferida)){
            dirEntrega.value = direccionPreferida;
        }
        setTarifas.clear();
        //POS.printTarifaEnvio();
        //POS.valorCarritoTotal();
        const direccionActual = addrs.direcciones?.find(dir=>dir.id === dirEntrega.value) || addrs.direcciones?.[0];
        (document.querySelector('#departamento') as HTMLInputElement).value = direccionActual?.departamento??'No especificado';
        (document.querySelector('#ciudad') as HTMLInputElement).value = direccionActual?.ciudad??'No especificado';
    }
