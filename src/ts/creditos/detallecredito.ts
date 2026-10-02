(()=>{
  if(document.querySelector('.detallecredito')){


    const btnajustarCredito = document.querySelector('#ajustarCredito') as HTMLButtonElement;
    const btnDetalleProductos = document.querySelector('#btnDetalleProductos') as HTMLButtonElement;
    const btnAbonar = document.querySelector('#btnAbonar') as HTMLButtonElement;
    const btnEditarCrearAbono = document.querySelector('#btnEditarCrearAbono') as HTMLInputElement;
    const btnEditarCrearPagoTotal = document.querySelector('#btnEditarCrearPagoTotal') as HTMLInputElement;
    const btnPagarTodo = document.querySelector('#btnPagarTodo') as HTMLButtonElement;
    const miDialogoAjustarCredito = document.querySelector('#miDialogoAjustarCredito') as any;
    const miDialogoAbono = document.querySelector('#miDialogoAbono') as any;
    const miDialogoPagoTotal = document.querySelector('#miDialogoPagoTotal') as any;
    const miDialogoDetalleProducto = document.querySelector('#miDialogoDetalleProducto') as any;
    const modalcambioMedioPago:any = document.querySelector("#cambioMedioPago");
    //const pagarTodo = document.querySelector('#pagarTodo') as HTMLButtonElement;
    const totalPagado = document.querySelector('#totalPagado') as HTMLSpanElement;
    const numCuota = document.querySelector('#numCuota') as HTMLLabelElement;
    const selectMediopago = document.querySelector('#selectMediopago') as HTMLSelectElement;
    const inputPasswordAjustarCredito = document.querySelector('#inputPasswordAjustarCredito') as HTMLInputElement;
    let contentMP:HTMLButtonElement, idcuota:string = '0', idcredito:string = '0', totalpagado:string = '0', idmediopago:string = '0', mediopagado:string = '0';
    
    let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;
    let indiceFila=0, tablacuotas:HTMLElement;

    interface clavesApi {
      clave:string,
      valor_default:string|null,
      valor_final:string|null,
      valor_local:string|null
    };

    let password:clavesApi[];

    (async ()=>{
      try {
          const url = "/admin/api/getPasswords"; //llamado a la API REST
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          password = resultado;
      } catch (error) {
          console.log(error);
      }
    })();

    
    document.addEventListener("click", cerrarDialogoExterno);
     
    //////////////////  TABLA //////////////////////
    tablacuotas = ($('#tablacuotas') as any).DataTable(configdatatablesToolbar);
    modernizarToolbarDataTable('#tablacuotas');

    btnajustarCredito?.addEventListener('click', ():void=>{
      miDialogoAjustarCredito.showModal();
    });

    btnDetalleProductos?.addEventListener('click', ():void=>{
      miDialogoDetalleProducto.showModal();
    });

    //btn para abonar credito
    btnAbonar?.addEventListener('click', ():void=>{
      miDialogoAbono.showModal();
    });

    //btn para abonar credito
    btnPagarTodo?.addEventListener('click', ():void=>{
      miDialogoPagoTotal.showModal();
    });


    /*btnEditarCrearAbono.addEventListener('click', ()=>{
      btnEditarCrearAbono.disabled = true;
      (document.querySelector('#caja') as HTMLSelectElement).disabled = false;
      (document.querySelector('#formCrearUpdateAbono') as HTMLFormElement).submit();
    });
    btnEditarCrearPagoTotal.addEventListener('click', ()=>{
      btnEditarCrearPagoTotal.disabled = true;
      (document.querySelector('#PagoTotal_caja') as HTMLSelectElement).disabled = false;
      (document.querySelector('#formCrearUpdatePagoTotal') as HTMLFormElement).submit();
    });*/

    document.querySelector('#formCrearUpdateAbono')?.addEventListener('submit', e=>{
      e.preventDefault();
      const caja = document.querySelector('#caja') as HTMLSelectElement;
      const abono = document.querySelector('#abono') as HTMLInputElement;
      const mediopago = document.querySelector('#mediopago') as HTMLSelectElement;
      const detalle = document.querySelector('#detalle') as HTMLTextAreaElement;
      btnEditarCrearAbono.disabled = true;
      btnEditarCrearAbono.value = 'Procesando...';
      crearAbono(caja, abono, mediopago, detalle);
    });

    document.querySelector('#formCrearUpdatePagoTotal')?.addEventListener('submit', e=>{
      e.preventDefault();
      const PagoTotal_caja = document.querySelector('#PagoTotal_caja') as HTMLSelectElement;
      const PagoTotal_abono = document.querySelector('#PagoTotal_abono') as HTMLInputElement;
      const PagoTotal_mediopago = document.querySelector('#PagoTotal_mediopago') as HTMLSelectElement;
      const pagoTotal_detalle = document.querySelector('#pagoTotal_detalle') as HTMLTextAreaElement;
      btnEditarCrearPagoTotal.disabled = true;
      btnEditarCrearPagoTotal.value = 'Procesando...';
      crearAbono(PagoTotal_caja, PagoTotal_abono, PagoTotal_mediopago, pagoTotal_detalle);
    });

    async function crearAbono(caja: HTMLSelectElement, abono: HTMLInputElement, mediopagado: HTMLSelectElement, detalle: HTMLTextAreaElement):Promise<void>{
      const id:string = (document.querySelector('#idcredito') as HTMLInputElement).value;
      const inputAbono = obtenerNumero(abono)?.toString();
      const datos = new FormData();
      datos.append('id_credito', id);
      datos.append('cajaid', caja.value);
      datos.append('mediopagoid', mediopagado.value);
      datos.append('valorpagado', inputAbono??'');
      datos.append('detalle', detalle.value);
      try {
          const url = "/admin/api/creditos/registrarAbono";  //va al controlador creditoscontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();
          if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            document.querySelector('#saldopendientetext')!.textContent = '$ '+resultado.saldopendiente.toLocaleString();
            btnEditarCrearAbono.disabled = false;
            btnEditarCrearPagoTotal.disabled = false;
            btnEditarCrearAbono.value = 'Confirmar';
            btnEditarCrearPagoTotal.value = 'Confirmar';
            //registrar en tabla.
            (tablacuotas as any).row.add([
                resultado.cuota.numerocuota,
                resultado.cuota.fechapagado,
                '$'+resultado.cuota.montocuota.toLocaleString(),
                '$'+resultado.cuota.valorpagado.toLocaleString(),
                `<button
                  id="${resultado.idcuota}" 
                  data-totalpagado="${resultado.cuota.valorpagado}"
                  data-idcredito="${resultado.cuota.id_credito}"
                  data-idmediopago="${resultado.cuota.mediopagoid}"
                  data-mediopagado="${resultado.cuota.valorpagado}"
                  class="mediosdepago">
                  ${resultado.mediopago}
                </button>`,
                `<div class="text-indigo-600 text-xl bg-indigo-50 rounded-lg pt-2 font-semibold">
                    ${resultado.sucursalregistrado}
                </div>`,
                `<div id="${resultado.idcuota}" class="flex justify-center gap-4">
                    <button class="anularAbono" title="Eliminar abono">X</button>
                    <button class="printPOSAbono material-symbols-outlined">print</button>
                </div>`
            ]).draw(false); // draw(false) evita recargar toda la tabla
            if((document.querySelector('#imprimirComprobanteAbonoinicial') as HTMLInputElement).checked)
              window.setTimeout(()=>printPOSComprobanteAbono(resultado.idcuota), 450);
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
      miDialogoAbono.close();
      miDialogoPagoTotal.close();
    }
    

    let saldopendiente = Number((document.querySelector('#saldopendiente') as HTMLInputElement).value || '0');
    document.querySelector('#abonoTotalAntiguo')?.addEventListener("input", (e:Event)=>{
      const abonoTotalAntiguo = (e.target as HTMLInputElement);
      if(Number(abonoTotalAntiguo.value)>saldopendiente)abonoTotalAntiguo.value = '';
    });

    ////////////// Evento a la tabla cuotas ///////////////
    document.querySelector('#tablacuotas')?.addEventListener("click", (e:Event)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLButtonElement;
      if(target?.classList.contains("mediosdepago")||target.parentElement?.classList.contains("mediosdepago"))cambiomediopago(target);
      if(target?.classList.contains("anularAbono"))anularAbono(target);
      if(target?.classList.contains("printPOSAbono"))printPOSComprobanteAbono(target.parentElement?.id);
    });


    function cambiomediopago(target:HTMLButtonElement){
      document.querySelector('#textMP')!.textContent = 'Pago por '+target.textContent+': ';
      idcuota = target.id;
      totalpagado = target.dataset.totalpagado??'0';
      idcredito = target.dataset.idcredito??'0';
      idmediopago = target.dataset.idmediopago??'0';
      mediopagado = target.dataset.mediopagado??'0';
      contentMP = target;
      totalPagado.textContent = '$ '+Number(mediopagado).toLocaleString();
      numCuota.textContent = 'Credito N° : '+idcredito;
      selectMediopago.value = idmediopago;
      modalcambioMedioPago.showModal();
    }


    document.querySelector('#formCambioMedioPago')?.addEventListener('submit', e=>{
      e.preventDefault();
      actualizarMediosPago();
    });


    async function actualizarMediosPago(){
      const datos = new FormData();
      datos.append('id', idcuota);
      datos.append('idmediopago', idmediopago);
      datos.append('id_credito', idcredito);
      datos.append('idnuevomediopago', selectMediopago.value);
      datos.append('valor', mediopagado);
      try {
          const url = "/admin/api/cuota/cambioMedioPagoSeparado";  //va al controlador creditoscontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();
          if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            updateMP(resultado.mediosPagoUpdate);
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
      modalcambioMedioPago.close();
    }


    function updateMP(mediosPagoUpdate:{id:string, idcuota:string, mediopago_id?:string, idmediopago?:string, valor:string}){
      const {mediopago_id, idmediopago} = mediosPagoUpdate;
      contentMP.textContent = selectMediopago.options[selectMediopago.selectedIndex].textContent;
      contentMP.dataset.idmediopago = mediopago_id??idmediopago;
    }



    document.querySelector('#formAjustarCredito')?.addEventListener('submit', e=>{
      e.preventDefault();
      const v:number = validarPassword('divmsjalertaClaveAjustarCredito', inputPasswordAjustarCredito.value);
      if(!v)return;
      ajustarCreditoAntiguo();
    });

    function validarPassword(divAlertString:string, claveUsuario:string):number{
      const clave = password.find(c => c.clave=='clave_para_ajustar_credito');
      if(clave?.valor_final!==null && claveUsuario !== clave?.valor_final){
        msjAlert('error', 'El password es invalido', (document.querySelector('#'+divAlertString) as HTMLElement));
        return 0;
      }
      return 1;
    }


    async function ajustarCreditoAntiguo(){
      const id:string = (document.querySelector('#idcredito') as HTMLInputElement).value;
      const abonototalantiguo = (document.querySelector('#abonoTotalAntiguo') as HTMLInputElement).value||'0';
      const recargo = (document.querySelector('#recargo') as HTMLInputElement).value||'0';
      const datos = new FormData();
      datos.append('id', id);
      datos.append('recargo', recargo);
      datos.append('abonototalantiguo', abonototalantiguo);
      datos.append('fechainicio', (document.querySelector('#ajustarFechaInicio') as HTMLInputElement).value);
      try {
          const url = "/admin/api/ajustarCreditoAntiguo";  //va al controlador creditoscontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();
          if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            ajustarIndicadores(abonototalantiguo, recargo);
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
      miDialogoAjustarCredito.close();
    }


    function ajustarIndicadores(abonototalantiguo:string, recargo:string){
      const capital:number = Number((document.querySelector('#capital') as HTMLInputElement).value);
      const abonoinicial:number = Number((document.querySelector('#abonoinicial') as HTMLInputElement).value);
      const montototal:number = Number((document.querySelector('#montototal') as HTMLInputElement).value);
      const saldopendiente:number = Number((document.querySelector('#saldopendiente') as HTMLInputElement).value);
      document.querySelector('#abonoInicialText')!.textContent = '$ '+abonototalantiguo;
      document.querySelector('#interesText')!.textContent = '$ '+recargo;
      document.querySelector('#creditoTotalText')!.textContent = '$ '+(capital - abonoinicial + Number(recargo)).toLocaleString();
      document.querySelector('#saldopendientetext')!.textContent = '$ '+(capital+Number(recargo)-abonoinicial-Number(abonototalantiguo)).toLocaleString();
    }

    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if (f=== miDialogoAjustarCredito || f === miDialogoAbono || f === miDialogoDetalleProducto || f === modalcambioMedioPago || (f as HTMLInputElement).value === 'salir' || (f as HTMLInputElement).value === 'Cancelar' 
          || (f as HTMLElement).id == 'btnXCerrarModalDetalleProducto' || (f as HTMLElement).id == 'btnXCerrarModalAbono' || f === miDialogoPagoTotal
          || (f as HTMLElement).id == 'btnXCerrarModalPagoTotal' || (f as HTMLElement).id == 'btnXCerrarModalAjustarCredito' ) {
        miDialogoAbono.close();
        miDialogoPagoTotal.close();
        miDialogoDetalleProducto.close();
        modalcambioMedioPago.close();
        miDialogoAjustarCredito.close();
      }
    }


    function anularAbono(target: HTMLButtonElement){
      const idabono = target.parentElement?.id;
      const fila = target.closest('tr');
      if(idabono==undefined)return;
      const claveAnularAbono = password.find(c => c.clave === 'clave_para_anular_abono');
      const claveConfigurada = claveAnularAbono?.valor_final?.trim();
      Swal.fire({
          customClass: {
            popup: 'j2-confirm j2-confirm--danger',
            icon: 'j2-confirm__icon',
            title: 'j2-confirm__title',
            htmlContainer: 'j2-confirm__text',
            input: 'j2-confirm__input',
            actions: 'j2-confirm__actions',
            confirmButton: 'j2-confirm__button j2-confirm__button--danger',
            cancelButton: 'j2-confirm__button j2-confirm__button--cancel'
          },
          icon: 'warning',
          title: 'Anular abono registrado',
          html: '<strong>Esta accion reversara el abono seleccionado.</strong><br><span>El registro quedara anulado y se actualizara el historial del credito.</span>',
          input: 'password',
          inputPlaceholder: claveConfigurada ? 'Ingresa la clave de anulacion' : 'Clave de autorizacion (opcional)',
          inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Anular abono',
          cancelButtonText: 'Cancelar',
          reverseButtons: true,
          showLoaderOnConfirm: true,
          inputValidator: (value:string) => {
            if(!claveConfigurada) return;
            if(!value) return 'La clave de anulacion es obligatoria.';
            if(value !== claveConfigurada) return 'La clave de anulacion no corresponde.';
          },
          preConfirm: async()=>{
            try {
                const url = "/admin/api/creditos/anularAbono?id="+idabono;
                const respuesta = await fetch(url); 
                const resultado = await respuesta.json();

                if (!respuesta.ok || resultado.error) {
                    const mensajeError = resultado.error ? resultado.error[0] : 'No se pudo procesar la solicitud.';
                    throw new Error(mensajeError);
                }
                document.querySelector('#saldopendientetext')!.textContent = '$ '+resultado.saldopendiente.toLocaleString();
                return resultado;
            } catch (error:any) {
                  console.log(error);
                  Swal.showValidationMessage(error.message);
            }

          },
          allowOutsideClick: () => !Swal.isLoading()
      }).then((result:any) => {
          if (result.isConfirmed && result.value) {
              fila?.remove();
              Swal.fire({
                customClass: {
                  popup: 'j2-confirm j2-confirm--success',
                  icon: 'j2-confirm__icon',
                  title: 'j2-confirm__title',
                  htmlContainer: 'j2-confirm__text',
                  actions: 'j2-confirm__actions j2-confirm__actions--single',
                  confirmButton: 'j2-confirm__button j2-confirm__button--confirm'
                },
                icon: 'success',
                title: 'Abono anulado',
                html: '<strong>'+(result.value.exito?.[0] ?? 'El abono fue anulado correctamente.')+'</strong><br><span>El historial del credito quedo actualizado.</span>',
                confirmButtonText: 'Entendido'
              });
          }
      });
    }


    async function printPOSComprobanteAbono(idabono:string|undefined){
      if(idabono==undefined)return;
      try{
        const url = "/admin/api/creditos/getAbono?id="+idabono; //llamado a la API REST - creditocontrolador 
        const respuesta = await fetch(url); 
        const resultado = await respuesta.json();
        const isAndroid = /Android/i.test(navigator.userAgent);
        if(printerBT === '1'){
          const builder = new ticketAbonoBuilder(resultado);
          const ticket = await builder.generate(true); //true para version buffer bytes
          const base64 = bytesToBase64(ticket);
          if(isAndroid)window.location.href = `rawbt:base64,${base64}`;
          //descargar .bin a equipo
          /*const blob = new Blob([ticket], { type: 'application/octet-stream' });
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'ticket.bin';
          a.click();
          URL.revokeObjectURL(url);*/
        }
      }catch(error){
        console.log(error);
      }

      if(!isNaN(Number(idabono)))
        window.open("/admin/printPDFAbonoCredito?id=" + idabono, "_blank"); //controlador printcontrolador
    }
  }

})();
