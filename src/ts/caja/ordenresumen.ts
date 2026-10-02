(():void=>{
    if(document.querySelector('.ordenresumen')){
      const POS = (window as any).POS;
      const miDialogoFacturar:any = document.querySelector("#miDialogoFacturar");
      const miDialogoEliminarOrden = document.querySelector('#miDialogoEliminarOrden') as any;
      const btnfacturar = document.querySelector<HTMLButtonElement>("#btnfacturar");
      const btneliminarorden = document.querySelector('#btneliminarorden') as HTMLButtonElement;
      const btnEmisor = document.querySelector('#btnEmisor') as HTMLButtonElement;
      const btnSelectVendedor = document.querySelector('#btnSelectVendedor') as HTMLButtonElement;
      const btnCaja = document.querySelector('#caja') as HTMLSelectElement;
      const btnTipoFacturador = document.querySelector('#facturador') as HTMLSelectElement;
      const mediospago = document.querySelectorAll('.mediopago');
      const btnsdevolverinv = document.querySelectorAll<HTMLInputElement>('input[name="devolverinventario"]'); //radio buttom
      const inputsInv = document.querySelectorAll<HTMLInputElement>('.inputInv');
      const printcarta = document.querySelector('#printcarta');
      const printcotizacion = document.querySelector('#printcotizacion');
      const btnDespachar = document.querySelector<HTMLButtonElement>("#btnDespachar");
      const btnMasOpciones = document.querySelector('#btnMasOpciones') as HTMLInputElement;
      const btnImprimirTirilla = document.querySelector('#btnImprimirTirilla') as HTMLInputElement;
      const btnOrdenEnvio = document.querySelector('#btnOrdenEnvio') as HTMLInputElement;
      const miDialogoMasOpciones = document.querySelector('#miDialogoMasOpciones') as any;
      const numOrden = document.querySelector('#numOrden');
      const referenciaFactura = document.querySelector('#referenciaFactura');
      const miDialogoRemision = document.querySelector('#miDialogoRemision') as HTMLDialogElement;
      const miDialogoSelectUser = document.querySelector('#miDialogoSelectUser') as HTMLDialogElement;
      const miDialogoSelectEmisor = document.querySelector('#miDialogoSelectEmisor') as HTMLDialogElement;
      const enviarEmail = document.querySelector('#enviarEmail') as HTMLButtonElement;
      const miDialogoEnviarEmailCliente = document.querySelector('#miDialogoEnviarEmailCliente') as any;
      //const miDialogoDespachar = document.querySelector('#miDialogoDespachar') as any;
      const miDialogoProductoCompuesto = document.querySelector('#miDialogoProductoCompuesto') as any;
      const selectEmisor = document.querySelector('#selectEmisor') as HTMLInputElement;
      const inputEliminarClave = document.querySelector('#inputEliminarClave') as HTMLInputElement;
      const tablaDetalleInsumos = document.querySelector('#tablaDetalleInsumos tbody') as HTMLBodyElement;

      const valorTotal = {subtotal: 0, impuesto: 0, dctox100: 0, descuento: 0, idtarifa: 0, valortarifa: 0, total: 0}; //datos global de la venta
      const mapMediospago = new Map();

      interface clavesApi {
        clave:string,
        valor_default:string|null,
        valor_final:string|null,
        valor_local:string|null
      };

      let claveEliminarOrden:clavesApi[];
      const idorden = (document.querySelector('#idorden') as HTMLElement).dataset.idorden;

      document.addEventListener("click", cerrarDialogoExterno);

      (async ()=>{
        try {
            const url = "/admin/api/getPasswords"; //llamado a la API REST
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json(); 
            claveEliminarOrden = resultado;
        } catch (error) {
            console.log(error);
        }
      })();

  
      valorTotal.subtotal = Number(document.querySelector('#subTotal')?.textContent);
      valorTotal.total = Number(document.querySelector('#total')?.textContent?.replace('$', '').trim().replace(/\./g, '').replace(',', '.'));
    
      btnCaja.addEventListener('change', (e:Event)=>selectFacturadorSegunCaja(e.target as HTMLSelectElement));
    
      function selectFacturadorSegunCaja(z:HTMLSelectElement){
        $('#facturador').val(z.options[z.selectedIndex].dataset.idfacturador??'1');
      }

      printcarta?.addEventListener('click', ()=>{
        //leer parametros de url
        const parametrosURL = new URLSearchParams(window.location.search);
        const id = parametrosURL.get('id');
        if(id==''||id==null||isNaN(Number(id)))return;
        const ventana = window.open('/printfacturacarta?id='+id, '_blank');
        if(ventana){
          ventana.onload = ()=>{
            ventana?.focus();
            ventana?.print();
            setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana después de unos segundos
          };
        }
      });

      printcotizacion?.addEventListener('click', ()=>{
        //leer parametros de url
        const parametrosURL = new URLSearchParams(window.location.search);
        const id = parametrosURL.get('id');
        if(id==''||id==null||isNaN(Number(id)))return;
        const ventana = window.open('/printcotizacion?id='+id, '_blank');
        if(ventana){
          ventana.onload = ()=>{
            ventana?.focus();
            ventana?.print();
            setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana después de unos segundos
          };
        }
      });

      btnfacturar?.addEventListener('click', ()=>{
        /*if(modalidadEntrega.textContent === ": Domicilio" && (selectCliente.value =='1' || selectCliente.value =='2' || !dirEntrega.value)){
          msjAlert('error', 'Cliente o direccion no seleccionado', (document.querySelector('#divmsjalerta1') as HTMLElement));
          return;
        }*/
        /*if(carrito.length){*/
          subirModalPagar();
          miDialogoFacturar.showModal();
        //}
      });
      

      btneliminarorden?.addEventListener('click', ()=>{
          miDialogoEliminarOrden.showModal();
      });


      enviarEmail?.addEventListener('click', ()=>{
        miDialogoEnviarEmailCliente.showModal();
      });


      document.querySelector('#formEnviarEmailCliente')?.addEventListener('submit', (e:Event)=>{
        e.preventDefault();
        if(idorden!=null && Number(idorden)>0){
          const datos = new FormData();
          datos.append('id', idorden);
          datos.append('email', (document.querySelector('#inputEmail') as HTMLInputElement).value);
          miDialogoEnviarEmailCliente.close();
          (async ()=>{
            try {
              const url = "/admin/api/sendOrdenEmailToCustemer";  //va al controlador cajacontrolador para enviar detalle de orden por email.
              const respuesta = await fetch(url, {method: 'POST', body: datos});
              const resultado = await respuesta.json();
              if(resultado.exito!=undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
            } catch (error) {
                console.log(error);
            }
          })();
        }
      });


      btnDespachar?.addEventListener('click', ()=>{
        if(Number.isNaN(idorden))return;
        Swal.fire({
          customClass: {
            popup: 'j2-confirm j2-confirm--dispatch',
            icon: 'j2-confirm__icon',
            title: 'j2-confirm__title',
            htmlContainer: 'j2-confirm__text',
            actions: 'j2-confirm__actions',
            confirmButton: 'j2-confirm__button j2-confirm__button--confirm',
            cancelButton: 'j2-confirm__button j2-confirm__button--cancel'
          },
          icon: 'question',
          title: 'Desea despachar la orden?',
          text: "La orden sera registrada como despachada y entregada.",
          showCancelButton: true,
          confirmButtonText: 'Despachar',
          cancelButtonText: 'Cancelar',
          buttonsStyling: false,
      }).then((result:any) => {
          if (result.isConfirmed) {
            (async ()=>{
              try {
                const url = "/admin/api/caja/despacharOrden?id="+idorden; //llamado a la API REST
                const respuesta = await fetch(url); 
                const resultado = await respuesta.json();
                if(resultado.exito !== undefined){
                  msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                  (document.querySelector('#textEstado') as HTMLParagraphElement).textContent = "Domicilio entregado";
                  btnDespachar.classList.add('!hidden');
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


      //apertura de la ventana modal para las opcionesde traslado de inventario
      btnMasOpciones.addEventListener('click', ()=>{
          miDialogoMasOpciones.showModal();
      });


      btnImprimirTirilla.addEventListener('click', ()=>{
        if(Number.isNaN(idorden))return;
        miDialogoMasOpciones.close();
        window.open("/admin/printPDFPOS?id=" + idorden, "_blank");  //controlador printcontrolador
      });


      btnOrdenEnvio?.addEventListener('click', ()=>{
        miDialogoRemision.showModal();
      });

      /////////  CAMBIAR EMISOR  /////////////
      btnEmisor.addEventListener('click', (e)=>{
        miDialogoSelectEmisor.showModal();
      });

      /////////  CAMBIAR USUARIO VENDEDOR Y COMISION  /////////////
      btnSelectVendedor.addEventListener('click', (e)=>{
        miDialogoSelectUser.showModal();
      });

      document.querySelector('#btnEditarCrearSelectUser')?.addEventListener('submit', (e:Event)=>{
        e.preventDefault();
        /*try {
            const url = `/admin/api/ventas/detalleProductoCompuesto?idproducto=${idproducto}&idfactura=${idventa}`; //llamado a la API REST ventascontrolador, detalle producto compuesto
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json();
            detalleInsumos(resultado);
          } catch (error) {
              console.log(error);
          }*/
      });


      selectEmisor.addEventListener('click', (e:Event)=>{
        const target = e.target as HTMLSelectElement;
        const opcion = document.querySelector(`#selectCaja option[data-emisor="${target.value}"]`) as HTMLOptionElement;
        if(opcion) {
          (document.querySelector('#selectCaja') as HTMLSelectElement).value = opcion.value;
        }
      });


      ////////////////////FORM PARA CAMBIAR DE EMISOR
      document.querySelector('#formUpdateSelectEmisor')?.addEventListener('submit', async (e:Event)=>{
        e.preventDefault();
        const inputcambiarEmisor = document.querySelector('#inputcambiarEmisor') as HTMLInputElement;
        const v:number = validarPassword('clave_para_cambiar_emisor_de_una_factura', 'divmsjalertaSelectEmisor', inputcambiarEmisor);
        if(!v || idorden==null || Number(idorden)<=0){
          return;
        }
        miDialogoSelectEmisor.close();
        inputcambiarEmisor.value = '';
        const datos = new FormData();
        datos.append('id', idorden);
        datos.append('idemisor', selectEmisor.value);
        datos.append('idcaja', (document.querySelector('#selectCaja') as HTMLSelectElement).value);
        try {
          const url = "/admin/api/caja/cambiarEmisor";  //va al controlador cajacontrolador para cambiar el emisor en tabla creditos y facturas.
          const respuesta = await fetch(url, {method: 'POST', body: datos});
          const resultado = await respuesta.json();
          if(resultado.exito!=undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            (document.querySelector('#nitEmisor') as HTMLSpanElement).textContent = 'NIT: '+resultado.emisor.nit;
            (document.querySelector('#nombreEmisor') as HTMLSpanElement).textContent = resultado.emisor.negocio?resultado.emisor.negocio:resultado.emisor.nombre;
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
        } catch (error) {
            console.log(error);
        }
      });


      ///////////////////// Logica botones devolver inventario ////////////////////////
      btnsdevolverinv.forEach(inv=>{ //evento a los radiobutton
        inv.addEventListener('change', (e:Event)=>{
          document.querySelector('#productsInv')?.classList.toggle('hidden');
          if((e.target as HTMLInputElement).value === "1"){
            document.querySelectorAll('.inputInv').forEach(x=>x.setAttribute('required', ''));
          }else{
            document.querySelectorAll('.inputInv').forEach(x=>x.removeAttribute('required'));
          }
        });
      });

      inputsInv.forEach(inputinv =>{
        inputinv.addEventListener('input', e=>{
          const qty = (e.target as HTMLInputElement);
          if(obtenerNumero(qty) != Number(qty.parentElement?.dataset.qty)){
            qty.classList.add('border-2', 'border-rose-600');
            if(!document.querySelector('.alerta'))
              msjAlert('error', 'Cantidad diferente a devolver a inventario', (document.querySelector('#divmsjalerta1') as HTMLElement));
          }else{
            qty.classList.remove('border-2', 'border-rose-600');
            document.querySelector('.alerta')?.remove();
          }
        });
      });

      /////////////////////  logica del modal de pago  //////////////////////////
      function subirModalPagar(){
        document.querySelector('#totalPagar')!.textContent = `${valorTotal.total.toLocaleString()}`;
        //como se puede cerrar el modal y aumentar los productos, hay calcular los inputs
        let totalotrosmedios = 0;
        mediospago.forEach((item, index)=>{
          if(index>0)totalotrosmedios += parseFloat((item as HTMLInputElement).value.trim().replace(/\./g, '').replace(',', '.'));
        });

        if(valorTotal.total<totalotrosmedios){
          totalotrosmedios = 0;
          mapMediospago.clear();
          $('.mediopago').val(0);
        }
        (document.querySelector('.Efectivo')! as HTMLInputElement).value =  `${(valorTotal.total-totalotrosmedios).toLocaleString()}`;
        mapMediospago.set('1', valorTotal.total-totalotrosmedios);
        if(valorTotal.total-totalotrosmedios == 0 && mapMediospago.has('1'))mapMediospago.delete('1');
        calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
      }
  
      //eventos a los inputs medios de pago
      mediospago.forEach(m=>{m.addEventListener('input', (e)=>{ calcularmediospago(e);});}); 

      function calcularmediospago(e:Event){
        let totalotrosmedios = 0;
        mediospago.forEach((item, index)=>{ //sumar todos los medios de pago menos el efectivo
          if(index>0)totalotrosmedios += parseFloat((item as HTMLInputElement).value.trim().replace(/\./g, '').replace(',', '.'));
        });
        if(totalotrosmedios<=valorTotal.total){
          mapMediospago.set('1', valorTotal.total-totalotrosmedios);
          if(valorTotal.total-totalotrosmedios == 0 && mapMediospago.has('1'))mapMediospago.delete('1');
          mapMediospago.set((e.target as HTMLInputElement).id, parseFloat((e.target as HTMLInputElement).value.trim().replace(/\./g, '').replace(',', '.')));
          if((e.target as HTMLInputElement).value == '0' && mapMediospago.has((e.target as HTMLInputElement).id))mapMediospago.delete((e.target as HTMLInputElement).id);
        }else{ //si la suma de los medios de pago superan el valor total, toma el ultimo input digitado y lo reestablece a su ultimo valor
          if(mapMediospago.has((e.target as HTMLInputElement).id)){
            (e.target as HTMLInputElement).value = mapMediospago.get((e.target as HTMLInputElement).id).toLocaleString();
          }else{
            (e.target as HTMLInputElement).value = '0';
          }
        }
        (mediospago[0] as HTMLInputElement).value = (mapMediospago.get('1')??0).toLocaleString();
        calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
      }

      /////////////////////  evento al input recibido  //////////////////////////
      document.querySelector<HTMLInputElement>('#recibio')?.addEventListener('input', (e)=>{
        calcularCambio((e.target as HTMLInputElement).value);
      });
      function calcularCambio(recibido:string):void{
        recibido = recibido.trim().replace(/\./g, '').replace(',', '.');
        if(Number(recibido)>mapMediospago.get('1')){
          (document.querySelector('#cambio') as HTMLElement).textContent = (Number(recibido)-mapMediospago.get('1')).toLocaleString()+'';
          return;
        }
        (document.querySelector('#cambio') as HTMLElement).textContent = '0';
      }


      ////////////////// evento al bton pagar del modal facturar //////////////////////
      document.querySelector('#formfacturarCotizacion')?.addEventListener('submit', e=>{
        e.preventDefault();
        procesarpedido('Paga');
      });

      async function procesarpedido(estado:string){ //////PROCESAR PAGO DE COTIZACION SiN CAMBIAR DATOS DE LOS PRODUCTOS//////
        const imprimir = document.querySelector('input[name="imprimir"]:checked') as HTMLInputElement;
        const datos = new FormData();
        datos.append('id', idorden!);
        datos.append('idemisor', btnCaja.selectedOptions[0].dataset.idemisor??'');
        //datos.append('idcliente', (document.querySelector('#selectCliente') as HTMLSelectElement).value);
        //datos.append('idvendedor', (document.querySelector('#vendedor') as HTMLInputElement).dataset.idvendedor!);
        datos.append('idcaja', btnCaja.value);
        datos.append('idconsecutivo', btnTipoFacturador.value);
        //datos.append('iddireccion', dirEntrega.value);
        //datos.append('idtarifazona', valorTotal.idtarifa+'');
        //datos.append('cliente', selectCliente.options[selectCliente.selectedIndex].textContent!);
        //datos.append('vendedor', (document.querySelector('#vendedor') as HTMLInputElement).value);
        datos.append('caja', (document.querySelector('#caja option:checked') as HTMLSelectElement).textContent!);
        datos.append('tipofacturador', btnTipoFacturador.options[btnTipoFacturador.selectedIndex].textContent!);
        //datos.append('direccion', dirEntrega.options[dirEntrega.selectedIndex].text);
        //datos.append('tarifazona', nombretarifa||'');
        //datos.append('carrito', JSON.stringify(carrito));
        //datos.append('totalunidades', totalunidades.textContent!);
        //datos.append('mediosPago', JSON.stringify(Object.fromEntries(mapMediospago)));
        datos.append('mediosPago', JSON.stringify(Array.from(mapMediospago, ([idmediopago, valor])=>({idmediopago, id_factura:0, valor}))));
        datos.append('recibido', document.querySelector<HTMLInputElement>('#recibio')!.value);
        datos.append('transaccion', '');
        datos.append('tipoventa', 'Contado');
        datos.append('estado', estado);
        datos.append('cambioaventa', '1');  //cambioaventa por defecto es 0
        //datos.append('subtotal', valorTotal.subtotal+'');
        //datos.append('impuesto', valorTotal.impuesto+'');
        //datos.append('dctox100',valorTotal.dctox100+'');
        //datos.append('descuento',valorTotal.descuento+'');
        //datos.append('total', valorTotal.total.toString());
        datos.append('observacion', document.querySelector<HTMLTextAreaElement>('#observacion')!.value);
        //datos.append('departamento', '');
        //datos.append('ciudad', (document.querySelector('#ciudadEntrega') as HTMLInputElement).value);
        //datos.append('entrega', modalidadEntrega.textContent!.replace(': ', ''));
        //datos.append('valortarifa', valorTotal.valortarifa+'');
        //datos.append('datosAdquiriente', JSON.stringify(POS.gestionarAdquiriente.datosAdquiriente));
        //datos.append('opc1', '');
        //datos.append('opc2', '');
        try {
            const url = "/admin/api/facturarCotizacion";  //va al controlador ventascontrolador
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              /////// reinciar modulo de ventas
              ordenpagada();
              miDialogoFacturar.close();
              if(resultado.idfactura && imprimir.value === '1')printTicketPOS(resultado.idfactura);
              if(btnTipoFacturador.options[btnTipoFacturador.selectedIndex].dataset.idtipofacturador == '1'){ 
                const resDian = await POS.sendInvoiceAPI.sendInvoice(resultado.idfactura); //llama a la funcion que esta en ts/ventas/ventas.sendinvoice.ts
                console.log(resDian);
              }
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      }
  
      function printTicketPOS(idfactura:string){
        setTimeout(() => {
          window.open("/admin/printPDFPOS?id=" + idfactura, "_blank");
        }, 1200);
      }

      function ordenpagada(){
        if(btnfacturar)btnfacturar.style.display = "none";
        (document.querySelector('#abrirOrden') as HTMLElement).style.display = "none";
        (document.querySelector('#estadoOrden') as HTMLElement).textContent = "Paga";
      }

      function cerrarDialogoExterno(event:Event) {
        const f = event.target as HTMLElement;
        if (f === miDialogoFacturar || f === miDialogoEliminarOrden || f === miDialogoEnviarEmailCliente || f === miDialogoMasOpciones || f === miDialogoRemision || f === miDialogoSelectEmisor || f === miDialogoSelectUser || f === miDialogoProductoCompuesto || f.id == 'btnXCerrarMasOpciones' || f.id == 'btnXCerrarRemision' || (f as HTMLInputElement).value === 'cancelar' || (f as HTMLInputElement).value === 'Salir' || f.closest('.noeliminar') || f.id == 'btnXCerrarModalProductoCompuesto' || f.id == 'btnXCerrarModalSelectUser' || f.id == 'btnXCerrarModalSelectEmisor') {
            miDialogoFacturar.close();
            miDialogoEliminarOrden.close();
            miDialogoEnviarEmailCliente.close();
            miDialogoMasOpciones.close();
            miDialogoRemision.close();
            miDialogoProductoCompuesto.close();
            miDialogoSelectUser.close();
            miDialogoSelectEmisor.close();
        }
      }

      //evento al boton confirmar para eliminar orden
      document.querySelector('.sieliminar')?.addEventListener('click', (event:Event)=>{
        const f = event.target;
        if((f as HTMLInputElement).closest('.sieliminar'))eliminarorden();
      });


      function eliminarorden():void{
        ///////*** crear arreglo de obj de los productos y sus cantidades ***///////
        type producto = {id:string, idventa:string, idproducto:string, nombre:string, tipoproducto:string, tipoproduccion:string, rendimientoestandar:string, cantidad: string , promediostock: string};
        var products:producto[] = [];

        const v:number = validarPassword('clave_para_eliminar_factura', 'divmsjalerta1', inputEliminarClave);
        if(!v)return;

        inputsInv.forEach(inputinv =>{
          const v = inputinv as HTMLInputElement;
          const n:number|null = obtenerNumero(v);
          if(n !== null)
            products = [...products, {id: v.id, idventa: v.id, idproducto: v.dataset.idproducto??'', nombre: v.dataset.nombre??'', tipoproducto: v.dataset.tipoproducto!, tipoproduccion: v.dataset.tipoproduccion!, rendimientoestandar: v.dataset.rendimientoestandar!, cantidad: n+'', promediostock: v.dataset.promediostock??'0'}];
        });

        (async ()=>{
          const btnConfirmar = document.querySelector<HTMLButtonElement>('.sieliminar');
          if(btnConfirmar){
            btnConfirmar.disabled = true;
            btnConfirmar.classList.add('opacity-70', 'cursor-not-allowed');
          }
          const datos = new FormData();
          datos.append('id', idorden!); //id de la factura
          datos.append('observacioneliminacion', (document.querySelector('#observacionEliminacion') as HTMLTextAreaElement).value);
          datos.append('inv', JSON.stringify(products));
          datos.append('devolverinv', (document.querySelector('input[name="devolverinventario"]:checked') as HTMLInputElement).value);
          //datos.append('domicilio', 0);
          try {
              const url = "/admin/api/eliminarOrden";  //api llamada en ventascontrolador.php
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();
              if(resultado.exito !== undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                miDialogoEliminarOrden.close();
                btneliminarorden.style.display = "none";
                enviarEmail.style.display = "none";
                (document.querySelector('#estadoOrden') as HTMLElement).textContent = "Eliminada";
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
              if(resultado.exito === undefined && btnConfirmar){
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('opacity-70', 'cursor-not-allowed');
              }
          } catch (error) {
              console.log(error);
              msjalertToast('error', 'Error', 'No se pudo eliminar la orden. Intenta nuevamente.');
              if(btnConfirmar){
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('opacity-70', 'cursor-not-allowed');
              }
          }
        })();
      }


      document.querySelector('#tablaDetalleProductos')?.addEventListener('click', async(e:Event)=>{
        while(tablaDetalleInsumos.firstChild)tablaDetalleInsumos.removeChild(tablaDetalleInsumos.firstChild);
        const target = e.target as HTMLElement;
        if(target.classList.contains('productoCompuesto')){
          const nombreProductoCompuesto = target.textContent;
          const parametrosURL = new URLSearchParams(window.location.search);
          const idventa = parametrosURL.get('id');
          if(idventa==''||idventa==null||isNaN(Number(idventa)))return;
          const idproducto = target.id;
          document.querySelector('#nombreProducto')!.textContent = nombreProductoCompuesto;
          miDialogoProductoCompuesto.showModal();
          
          try {
            const url = `/admin/api/ventas/detalleProductoCompuesto?idproducto=${idproducto}&idfactura=${idventa}`; //llamado a la API REST ventascontrolador, detalle producto compuesto
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json();
            detalleInsumos(resultado);
          } catch (error) {
              console.log(error);
          }
        }
      });


      function detalleInsumos(resultado:{cantidadcalculada:string, costo:string, disponibilidad:string, nombre:string, precio_compra:string, simbolo:string, sku:string, stockminimo:string, unidadmedida:string}[]){
        resultado.forEach(ins=>{
          const tr = document.createElement('tr') as HTMLTableRowElement;
          //tr.classList.add('productselect');
          //tr.dataset.idproducto = `${item.idproducto}`;
          tr.innerHTML = `<td class="text-center">${ins.nombre}</td>
                          <td class="text-center">${ins.cantidadcalculada}</td>
                          <td class="text-center">${ins.unidadmedida}</td>
                          <td class="text-center">${ins.disponibilidad} ${ins.simbolo}</td>`;
          tablaDetalleInsumos.prepend(tr);
        });
      }


      function validarPassword(llave:string, divAlert:string, input:HTMLInputElement):number{
        if(!claveEliminarOrden){
          msjAlert('error', 'Las claves de seguridad aun se estan cargando. Intenta nuevamente.', (document.querySelector('#'+divAlert) as HTMLElement));
          return 0;
        }
        const clave = claveEliminarOrden.find(c => c.clave==llave);
        if(clave?.valor_final!==null && input.value !== clave?.valor_final){
          msjAlert('error', 'El password es invalido', (document.querySelector('#'+divAlert) as HTMLElement));
          return 0;
        }
        return 1;
      }

    }
  
})();
