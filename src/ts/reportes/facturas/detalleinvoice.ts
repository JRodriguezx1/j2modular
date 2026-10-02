(():void=>{

  if(document.querySelector('.detalleinvoice')){
    const POS = (window as any).POS;
    const btnEnvarDian = document.querySelector('#btnEnvarDian') as HTMLButtonElement;
    const btnModalNotaCredito = document.querySelector('#btnModalNotaCredito') as HTMLButtonElement;
    const btnNuevaFactura = document.querySelector('#btnNuevaFactura') as HTMLButtonElement;
    const tipoConsecutivo = document.querySelectorAll<HTMLButtonElement>('input[name="tipoConsecutivo"]');
    const selectSetConsecutivo = document.querySelector('#selectSetConsecutivo') as HTMLSelectElement;
    const miDialogoNC = document.querySelector('#miDialogoNC') as any;

    const facturarA = document.querySelector('#facturarA') as HTMLButtonElement;  //btn para asignar adquiriente
    const miDialogoFacturarA = document.querySelector('#miDialogoFacturarA') as any;
    const modalNuevaFactura = document.querySelector('#modalNuevaFactura') as HTMLDialogElement;
    const modalEditarResolution = document.querySelector('#modalEditarResolution') as HTMLDialogElement;
    const formFacturarA = document.querySelector('#formFacturarA') as HTMLFormElement;
    const documentinput = document.querySelector('#identification_number') as HTMLInputElement;
    const selectDepartments = document.querySelector('#department_id') as HTMLSelectElement;
    const selectdCities = document.querySelector('#municipality_id') as HTMLSelectElement;
    let isElectronica = false, idfeEditarResolution:string;
    let customers:adquirientes[] = [];
    let customersfiltrados:adquirientes[] = [];


    interface document {
      id:string,
      id_estadoelectronica:string,
      consecutivo_id:string,
      id_facturaid:string,
      id_adquiriente:string,
      id_estadonota:string,
      numero:string,
      num_factura:string,
      prefijo:string
      filename:string,
      identificacion:string
      nombre?:string,
      email?:string,
      link:string,
      nota_credito:string,
      prefixnc:string,
      num_nota:string,
      linknc:string,
      filenamenc:string,
      fecha_factura:string,
      fecha_nota:string
    }

    interface adquirientes {
      id?:string,
      type_document_identification_id:string,
      identification_number:string,
      business_name:string,
      email:string,
      address:string,
      municipality_id?:string,
      department_id?:string,
      ciudad_nombre:string
      type_organization_id:string,
      type_regime_id:string,
      phone:string
    }

    type municipalities = { id:string, department_id:string, name:string, code:string };
    let cities:municipalities[]=[];


    (async ()=>{
      const url = `/admin/api/filterAdquirientes`; 
      const respuesta = await fetch(url);
      const resultado = await respuesta.json();
      customers = resultado;
    })();


    //buscar adquiriente con el numero de indentificacion que esta en el modal de adquiriente
    documentinput.addEventListener('input', buscarAquiriente);

    function buscarAquiriente(e:Event){
        const busqueda:string = (e.target as HTMLInputElement).value;
        if(busqueda.length > 3 && /^\d+$/.test(busqueda)){
            //const expresionregular = new RegExp(busqueda, "i");
            customersfiltrados = customers.filter(document => { 
                if(document.identification_number.trim() === busqueda.trim()){
                    return document;
                }
            });   
        }else{
            customersfiltrados = [];
        }
        mostrarAdquiriente();
    }

    function mostrarAdquiriente(){
      if(customersfiltrados.length>0){
        $('#type_document_identification_id').val(customersfiltrados[0].type_document_identification_id);
        (document.querySelector('#identification_number') as HTMLInputElement).value = customersfiltrados[0].identification_number;
        (document.querySelector('#business_name') as HTMLInputElement).value = customersfiltrados[0].business_name;
        (document.querySelector('#email') as HTMLInputElement).value = customersfiltrados[0].email;
        (document.querySelector('#address') as HTMLInputElement).value = customersfiltrados[0].address;
        if(customersfiltrados[0].department_id){
          $('#department_id').val(customersfiltrados[0].department_id);
          imprimirCiudades(customersfiltrados[0].department_id);
        }
        //$('#municipality_id').val(customersfiltrados[0].municipality_id);
        $('#type_organization_id').val(customersfiltrados[0].type_organization_id);
        $('#type_regime_id').val(customersfiltrados[0].type_regime_id);
        (document.querySelector('#phone') as HTMLInputElement).value = customersfiltrados[0].phone;
      }else{
        $('#type_document_identification_id').val("");
        (document.querySelector('#business_name') as HTMLInputElement).value = '';
        (document.querySelector('#email') as HTMLInputElement).value = '';
        (document.querySelector('#address') as HTMLInputElement).value = '';
        $('#department_id').val("");
        $('#municipality_id').val("");
        $('#type_organization_id').val("");
        $('#type_regime_id').val("");
        (document.querySelector('#phone') as HTMLInputElement).value = "";
      }
    }



    /////       Obtener municipio segun departamento        ///////
    selectDepartments?.addEventListener('change', (e:Event)=>{
      const x:HTMLOptionElement = (e.target as HTMLOptionElement);
      imprimirCiudades(x.value);
    });

    function imprimirCiudades(x:string){
      (async ()=>{
        try {
          const url = "/admin/api/citiesXdepartments?id="+x; //llamado a la API REST y se trae las cities segun cliente elegido
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          if(resultado.error){
            Swal.fire(resultado.error[0], '', 'error')
          }else{
            cities = resultado;
            addCitiesToSelect(cities);
          }
        } catch (error) {
            console.log(error);
        }
      })();
    }
    
    function addCitiesToSelect<T extends {id:string, department_id:string, name:string, code:string}>(addrs: T[]):void{
      while(selectdCities?.firstChild)selectdCities.removeChild(selectdCities?.firstChild);
      addrs.forEach(x =>{
        const option = document.createElement('option');
        option.textContent = x.name;
        option.value = x.id;
        option.dataset.code = x.code;
        option.dataset.department_id = x.department_id;
        selectdCities.appendChild(option);
      });
      if(customersfiltrados.length>0&&customersfiltrados[0].municipality_id)$('#municipality_id').val(customersfiltrados[0].municipality_id);
    }

    ///////////////////// Enviar a DIAN //////////////////////
    btnEnvarDian.addEventListener('click', async ()=>{
      const idfactura:string = (document.querySelector('#idfactura') as HTMLInputElement).value;
      const idfe:string = (document.querySelector('#idfe') as HTMLInputElement).value;
      const idfeState:string = (document.querySelector('#idfeState') as HTMLInputElement).value;
      (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
      if(idfeState != '2'){
        const resDian = await POS.sendInvoiceAPI.sendInvoice(idfactura);
        const filaDoc = document.querySelector(`#detalleDocumentos tr[data-idfe="${idfe}"]`);
        if(resDian.exito !== undefined){
          document.querySelector('#estadoFactura')!.textContent = 'Aceptada DIAN';
          document.querySelector('#estadoFactura')?.classList.remove('bg-slate-100', 'text-gray-700', 'bg-yellow-100', 'text-yellow-700');
          document.querySelector('#estadoFactura')?.classList.add('bg-green-500', 'text-white');
          msjalertToast('success', '¡Éxito!', resDian.exito[0]);
          if(filaDoc)filaDoc.children[3].children[1].innerHTML = `<a class="btn-xs btn-lima" href="${resDian.link}" target="_blank"> Aceptada </a>`;
        }else{
          document.querySelector('#estadoFactura')!.textContent = 'Error';
          document.querySelector('#estadoFactura')?.classList.remove('bg-slate-100', 'g-green-500', 'text-gray-700', 'bg-yellow-100', 'text-yellow-700');
          document.querySelector('#estadoFactura')?.classList.add('bg-red-500', 'text-white');
          msjalertToast('error', '¡Error!', resDian.error[0]);
          if(filaDoc)filaDoc.children[3].children[1].innerHTML = `<a class="btn-xs btn-red" href="/" target="_blank"> Error </a>`;
        }
      }else{
        msjalertToast('error', '¡Error!', 'Factura electronica aceptada por la Dian, no se puede volver a enviar');
      }
      (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
    });


    ///////////////////// btn nueva factura //////////////////////
    btnNuevaFactura.addEventListener('click', ()=>{
      modalNuevaFactura.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    ///////////////////// evento al btn facturar A - adquiriente /////////////////////
    facturarA.addEventListener('click', (e:Event)=>{
      miDialogoFacturarA.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    //tipoConsecutivo es para generar nueva factura electronica
    tipoConsecutivo.forEach(radio=>{
      radio.addEventListener('change', (e:Event)=>{
        const targetDom = e.target as HTMLInputElement;
        if(targetDom.value == 'manual'){
          document.querySelector('#consecutivoManual')?.classList.remove('hidden');
          document.querySelector('#consecutivoManual')?.setAttribute("required", "");
        }else{
          document.querySelector('#consecutivoManual')?.classList.add('hidden');
          document.querySelector('#consecutivoManual')?.removeAttribute("required");
          (document.querySelector('#consecutivoManual') as HTMLInputElement).value = '';
        }
      })
    });

    /////////// Generar nueva facutura /////////
    document.querySelector('#formNuevaFactura')?.addEventListener('submit', async e=>{
      e.preventDefault();
      const idResolution = (document.querySelector('#selectResolucion') as HTMLInputElement).value;
      const datos = new FormData();
      datos.append('idfactura', (document.querySelector('#idfactura') as HTMLInputElement).value);
      datos.append('idResolution', idResolution);
      datos.append('numConsecutivoManual', (document.querySelector('#consecutivoManual') as HTMLButtonElement)?.value || '');
      modalNuevaFactura.close();
      document.removeEventListener("click", cerrarDialogoExterno);
      (async ()=>{
        try {
            const url = "/admin/api/crearFacturaPOSaElectronica";  //api llamada en parametroscontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              (document.querySelector('#idfe') as HTMLInputElement).value = resultado.facturaelectronica.id;
              (document.querySelector('#idfeState') as HTMLInputElement).value = resultado.facturaelectronica.id_estadoelectronica;
              document.querySelector('#prefixNumber')!.textContent = `Factura #${resultado.facturaelectronica.num_factura}`;
              document.querySelector('#estadoFactura')!.textContent = 'Pendiente DIAN';
              document.querySelector('#estadoFactura')?.classList.remove('bg-slate-100', 'text-gray-700', 'bg-green-500', 'text-white');
              document.querySelector('#estadoFactura')?.classList.add('bg-yellow-100', 'text-yellow-700');
              isElectronica = true;  //condicion para que el adquiriente se aguarde y se asocie al documento
              printDocumentInTable(resultado.facturaelectronica);
              insertInModalNC(resultado.facturaelectronica);
              customersfiltrados = customers.filter(x=>x.id == resultado.facturaelectronica.id_adquiriente);
              mostrarAdquiriente();
              document.querySelector('#nombreAdquiriente')!.textContent = customersfiltrados[0].business_name;
              document.querySelector('#identificacionAdquiriente')!.textContent = customersfiltrados[0].identification_number;
              document.querySelector('#emailAdquiriente')!.textContent = customersfiltrados[0].email;
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
    });

    function printDocumentInTable(facturaelectronica:document){
      document.querySelector('#detalleDocumentos')?.insertAdjacentHTML('beforeend', `
        <tr data-idfe="${facturaelectronica.id}">
          <td class="py-3 px-4">${facturaelectronica.id}</td>
          <td class="text-center">N° ${facturaelectronica.id_facturaid}</td> 
          <td class="text-center">${facturaelectronica.num_factura}${facturaelectronica.id_estadonota == '2' ? ` / ${facturaelectronica.prefixnc} - ${facturaelectronica.num_nota}` : ''}</td>
          <td class="text-center">
            <div class="btn-xs ${facturaelectronica.id_estadoelectronica == '2' ?'btn-lima':(facturaelectronica.id_estadoelectronica == '1'?'btn-blue':'btn-red')}"> ${facturaelectronica.id_estadoelectronica == '2'?'Aceptada':(facturaelectronica.id_estadoelectronica == '1'?'Pendiente':'Error')} </div>
            <div class="btn-xs ${facturaelectronica.id_estadonota == '2' ?'btn-orange':(facturaelectronica.id_estadonota == '1'?'btn-blue':'btn-red')}"> ${facturaelectronica.id_estadonota == '2' && facturaelectronica.nota_credito == '1' ?'Aceptada NC':(facturaelectronica.id_estadonota == '1' && facturaelectronica.nota_credito == '1'?'Pendiente NC':facturaelectronica.id_estadonota == '3' && facturaelectronica.nota_credito == '1'?'Error NC':'')} </div>
          </td>
          <td class="text-center">${facturaelectronica.id_estadoelectronica != '2' ?'<span class="cursor-pointer material-symbols-outlined eliminarFactura">delete</span>':''}</td>
        </tr>`
      );
    }

    function insertInModalNC(facturaelectronica:document){
      const selectInvoice = document.querySelector('#selectInvoice') as HTMLSelectElement;
      const option = document.createElement('option');
      option.value = facturaelectronica.id;
      option.textContent = facturaelectronica.num_factura;
      selectInvoice.appendChild(option);
    }
    
    /////////// Guardar adquiriente /////////
    formFacturarA.addEventListener('submit', (e:Event)=>{
      e.preventDefault();
      const data = new FormData(formFacturarA);
      const datosAdquiriente: Record<string, FormDataEntryValue> = Object.fromEntries(data.entries());
      
      miDialogoFacturarA.close();
      document.removeEventListener("click", cerrarDialogoExterno);
      
      const identification = datosAdquiriente.identification_number as string;
      const requiereEmail = identification && identification!='222222222222';
      // Validar email solo si identification_number es distinto de 222222222222 y no vacío
      if (requiereEmail && !validarEmail(datosAdquiriente.email as string)) {
          Swal.fire("Correo incorrecto", "Debe ingresar un email válido o enviar a consumidor final o generico: 222222222222", "error");
          return; // detiene el proceso
      }
      if(isElectronica){
        document.querySelector('#nombreAdquiriente')!.textContent = datosAdquiriente.business_name as string;
        document.querySelector('#identificacionAdquiriente')!.textContent = datosAdquiriente.identification_number as string;
        document.querySelector('#emailAdquiriente')!.textContent = datosAdquiriente.email as string;
        //guarda y asigna adquiriente en DB.
        asignarAdquirienteAFactura(datosAdquiriente);
      }else{
        msjalertToast('error', '¡Error!', 'Se debe generar nueva factura electronica para asignarle un adquiriente.');
      }
    });

    function validarEmail(correo: string): boolean {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(correo);
    }


    //asigna adquiriente a la ultima factura electronica.
    async function asignarAdquirienteAFactura(datosAdquiriente: Record<string, FormDataEntryValue>){
      datosAdquiriente.idfactura = (document.querySelector('#idfactura') as HTMLInputElement).value;  ////id de la factura general
      datosAdquiriente.idfe = (document.querySelector('#idfe') as HTMLInputElement).value;
      try {
            const url = "/admin/api/asignarAdquirienteAFactura";
            const respuesta = await fetch(url, {
                                      method: 'POST',
                                      headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                      body: JSON.stringify(datosAdquiriente)
                                    }); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              document.querySelector('#nombreAdquiriente')!.textContent = datosAdquiriente.business_name as string;
              document.querySelector('#identificacionAdquiriente')!.textContent = datosAdquiriente.identification_number as string;
              document.querySelector('#emailAdquiriente')!.textContent = datosAdquiriente.email as string;
              if(resultado.tipo == "crear"){
                customers = [...customers, resultado.obj];
              }else{
                /// actualizar el arregle del adquiriente o customers ///
                customers.forEach(a=>{if(a.identification_number == resultado.obj.identification_number)a = Object.assign(a, resultado.obj);});
              }
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
      } catch (error) {
          console.log(error);
      }
    }


    ///////////////////-------    Btn NC     --------///////////////////
    btnModalNotaCredito.addEventListener('click', ()=>{
        limpiarformdialog();
        miDialogoNC.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });

    ///////// Habilita el campo de consecutivo personalizado
    selectSetConsecutivo?.addEventListener('change', (e:Event)=>{
      const targetDom = e.target as HTMLSelectElement;
      const habilitaconsecutivo = document.querySelector('.habilitaconsecutivo') as HTMLElement;
      if(targetDom.value == '0'){  //  Siguiente consecutivo automatico
        habilitaconsecutivo.style.display = "none";
        document.querySelector('#consecutivoPersonalizado')?.removeAttribute("required");
        (document.querySelector('#consecutivoPersonalizado') as HTMLInputElement).value = '';
      }
      else{  //consecutivo personalizado
        habilitaconsecutivo.style.display = "flex";
        document.querySelector('#consecutivoPersonalizado')?.setAttribute("required", "");
      }
    });

    document.querySelector('#formNC')?.addEventListener('submit', async e=>{
        e.preventDefault();
        const idfactura:string = (document.querySelector('#idfactura') as HTMLInputElement).value;  //id de la factura general
        const idInvoice:string = (document.querySelector('#selectInvoice') as HTMLSelectElement).value;
        const idfeState:string = (document.querySelector('#idfeState') as HTMLInputElement).value;
        const consecutivo = (document.querySelector('#consecutivoPersonalizado') as HTMLInputElement).value;
        miDialogoNC.close();
        document.removeEventListener("click", cerrarDialogoExterno);
        if(idfeState == '2'){
          try {
              const url = "/admin/api/sendNc"; //llamado a la API REST apidiancontrolador.php
              const respuesta = await fetch(url, {
                  method: 'POST', 
                  headers: { "Accept": "application/json", "Content-Type": "application/json" },
                  body: JSON.stringify({id: idInvoice, idfactura, consecutivo})  //si consecutivo es vacio '', es siguiente consecutivo
              });
              const responseDian = await respuesta.json(); 
              console.log(responseDian);
              const filaDoc = document.querySelector(`#detalleDocumentos tr[data-idfe="${idInvoice}"]`);
              if(filaDoc)filaDoc.children[2].textContent = filaDoc.children[2].textContent+' / '+responseDian.notacredito.prefixnc+' - '+responseDian.notacredito.num_nota;
              if(responseDian.exito !== undefined){
                msjalertToast('success', '¡Éxito!', responseDian.exito[0]);
                //actualizar tabla de detalle de documentos electronicos
                if(filaDoc){
                  const enlace = filaDoc.children[3].children[1] as HTMLAnchorElement;
                  enlace.innerHTML = `Aceptada NC`;
                  enlace.classList.remove('btn-blue', 'btn-red');
                  enlace.classList.add('btn-orange');
                  enlace.href = responseDian.notacredito.linknc;
                }
                //quitar el documento del select de la modal NC
                eliminarOptionSelectNC(idInvoice);
              }else{
                msjalertToast('error', '¡Error!', responseDian.error[0]);
                //actualizar tabla de detalle de documentos electronicos
                if(filaDoc){
                  const enlace = filaDoc.children[3].children[1] as HTMLAnchorElement;
                  enlace.innerHTML = `Error NC`;
                  enlace.classList.remove('btn-blue', 'btn-orange');
                  enlace.classList.add('btn-red');
                  enlace.href = responseDian.notacredito.linknc;
                }
              }
          } catch (error) {
              console.log(error);
          }
        }else{
          msjalertToast('error', '¡Error!', 'Error, la factura electronica no esta aceptada por la Dian o ya se genero nota credito.');
        }
    });

    //eliminar option 'documento electronico' del select de la modal NC
    function eliminarOptionSelectNC(idInvoice:string){
      const selectInvoice = document.querySelector('#selectInvoice') as HTMLSelectElement;
      const optionsToRemove = selectInvoice.querySelector(`option[value="${idInvoice}"]`);
      if(optionsToRemove)optionsToRemove.remove();
    }


    /////////  ENVENTO A LA TABLA DE DETALLE DE DOCUMENTOS  ///////////
    document.querySelector('#detalleDocumentos')?.addEventListener('click', e=>{
      const evento = e.target;
      const tr:string|undefined = (evento as HTMLSpanElement).closest('tr')?.dataset.idfe;
      if((evento as HTMLSpanElement).classList.contains('eliminarFactura'))
        if(tr != undefined)eliminarFacturaElectronica(tr);
      if((evento as HTMLSpanElement).classList.contains('editarResolution'))
        if(tr != undefined){
          idfeEditarResolution = tr;
          modalEditarResolution.showModal();
          document.addEventListener("click", cerrarDialogoExterno);
        }
    });

    document.querySelector('#formEditarResolution')?.addEventListener('submit', async e=>{
        e.preventDefault();
        try {
        const url = "/admin/api/editarResolutionFE"; //llamado a la API REST apidiancontrolador.php
        const respuesta = await fetch(url, {
            method: 'POST', 
            headers: { "Accept": "application/json", "Content-Type": "application/json" },
            body: JSON.stringify({
                                    idfe: idfeEditarResolution,
                                    idresolution: (document.querySelector('#selectEditarResolucion') as HTMLInputElement).value,
                                    consecutivo: (document.querySelector('#consecutivoEditarResolution') as HTMLInputElement).value
                                  })
        });
        const resultado = await respuesta.json();
        if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            const filaDoc = document.querySelector(`#detalleDocumentos tr[data-idfe="${idfeEditarResolution}"]`) as HTMLTableRowElement;
            filaDoc.children[2].textContent = resultado.prefijoNum;
             document.querySelector('#prefixNumber')!.textContent = `Factura #${resultado.prefijoNum}`;
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
        console.log(error);
      }
    });

    async function eliminarFacturaElectronica(idfe:string){
      try {
          const url = "/admin/api/eliminarFacturaElectronica"; //llamado a la API REST apidiancontrolador.php
          const respuesta = await fetch(url, {
              method: 'POST', 
              headers: { "Accept": "application/json", "Content-Type": "application/json" },
              body: JSON.stringify({id: idfe})
          });
          const resultado = await respuesta.json(); 
          console.log(resultado);
          const filaDoc = document.querySelector(`#detalleDocumentos tr[data-idfe="${idfe}"]`);
          if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            if(filaDoc)filaDoc.remove();
            eliminarOptionSelectNC(idfe);
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
    }

    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if( f === miDialogoNC || f === modalNuevaFactura || f === miDialogoFacturarA || f === modalEditarResolution || (f as HTMLInputElement).value === 'Salir' || (f as HTMLInputElement).value === 'Cancelar') {
        miDialogoNC.close();
        modalNuevaFactura.close();
        miDialogoFacturarA.close();
        modalEditarResolution.close();
        document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateCompañia') as HTMLFormElement)?.reset();
    }

  }

})();