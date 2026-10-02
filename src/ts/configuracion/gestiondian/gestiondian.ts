(():void=>{
  if(document.querySelector('.gestionDian')){
    const POS = (window as any).POS;
    const btnCrearCompañia = document.querySelector('#btnCrearCompañia') as HTMLButtonElement;
    const btnObtenerresolucion = document.querySelector('#btnObtenerresolucion') as HTMLButtonElement;
    const BtnSetpruebas = document.querySelector('#BtnSetpruebas') as HTMLButtonElement;
    const btnDocumentos = document.querySelector('#btnDocumentos') as HTMLButtonElement;
    const formCrearUpdateCompañia = document.querySelector('#formCrearUpdateCompañia') as HTMLFormElement;
    const miDialogoAdquirirCompañia = POS.gestionAdquirirCompany.miDialogoAdquirirCompañia
    const miDialogoCompañia = document.querySelector('#miDialogoCompañia') as any;
    const miDialogoRecepcionDocumentos = document.querySelector('#miDialogoRecepcionDocumentos') as any;
    const miDialogoGetResolucion = POS.gestionarGetResolutions.miDialogoGetResolucion
    const miDialogosetpruebas = POS.gestionarSetPruebas.miDialogosetpruebas;
    const selectResolucioncompañia = POS.gestionarGetResolutions.selectResolucioncompañia;
    const selectSetCompañia = POS.gestionarSetPruebas.selectSetCompañia;

    interface configCompany {
      success:boolean,
      message:string,
      password:string,
      token:string,
      company: {
        address: string,
        country_id: number,
        created_at: string,
        dv: string,
        eqdocs_type_environment_id: number,
        id: number,
        identification_number: string,
        language_id: number,
        merchant_registration: string,
        municipality_id: number,
        payroll_type_environment_id: 2,
        phone: string,
        state: number,
        tax_id: number,
        type_currency_id: number,
        type_document_identification_id: number,
        type_environment_id: number,
        type_liability_id: number,
        type_operation_id: number,
        type_organization_id: number,
        type_regime_id: number,
        updated_at: string,
        user: {
          email: string,
          name: string,
          updated_at: string,
          created_at: string
        }
      }
    }


    //Resolucion a almacenar en API
    interface resolconfig {
      idcompany?:string,
      type_document_id:string, 
      prefix:string, 
      resolution?:string, 
      resolution_date?:string, 
      technical_key?:string,   //para la nota credito invoice se usa como identification_number de la compañia
      from:string,
      to:string, 
      generated_to_date?:string, 
      date_from?:string, 
      date_to?:string
    }


    //  Obtener compañias
    async function getCompañiasLocal<T>():Promise<T[]> {
      try {
          const url = "/admin/api/getCompaniesAll"; //llamado a la API REST y se trae las cities segun cliente elegido
          const respuesta = await fetch(url); 
          const resultado:T[]= await respuesta.json(); 
          return resultado;
        } catch (error) {
            console.log(error);
            return [];
        }
    }
    
    let companiesAll:companiesDian[];

    (async()=>{
      companiesAll = await getCompañiasLocal<{id:string, identification_number:string, business_name:string, idsoftware:string, token:string}>();
      POS.companiesAll = companiesAll; //exponer globalmente
    })();


    ///////////////////-------    crear compañia     --------///////////////////
    btnCrearCompañia.addEventListener('click', ()=>{
        limpiarformdialog();
        document.querySelector('#modalCompañia')!.textContent = "Crear compañia";
        (document.querySelector('#btnEditarCrearCompañia') as HTMLInputElement).value = "Crear";
        miDialogoCompañia.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });

    btnObtenerresolucion.addEventListener('click', ()=>{
        limpiarformdialog();
        miDialogoGetResolucion.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });

    BtnSetpruebas.addEventListener('click', async ()=>{
        limpiarformdialog();
        //const r = await getCompañiasLocal();

        miDialogosetpruebas.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });

    btnDocumentos?.addEventListener('click', ()=>{
        (document.querySelector('#formRecepcionDocumentos') as HTMLFormElement)?.reset();
        miDialogoRecepcionDocumentos?.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });

    document.querySelector('#formRecepcionDocumentos')?.addEventListener('submit', (e:Event)=>{
      e.preventDefault();
      msjalertToast('info', 'Formulario preparado', 'El cargue de documentos queda listo para conectar al guardado.');
    });


    formCrearUpdateCompañia.addEventListener('submit', async(e:Event)=>{
      e.preventDefault();
      const datos = new FormData(formCrearUpdateCompañia);
      const archivoP12 = datos.get('certificate') as File;
      const datoscompañia = Object.fromEntries(datos.entries());
      datoscompañia.merchant_registration = "0000000-00";

      const nit = datoscompañia.identification_number as string;
      if(!/^\d+$/.test(nit)){
        alert('Numero del Nit no es valido');
        return;
      }
      
      if(archivoP12 && archivoP12.size>0){
        if(!archivoP12.name.toLocaleLowerCase().endsWith('.p12')){
          alert('Por favor seleccione un archivo formato .p12 valido');
          return;
        }
        try {
          const base64String = await base64(archivoP12);
          datoscompañia.certificadop12base64 = base64String;
          crearCompanyAPI(datoscompañia);
        } catch (error) {
          alert('Error durante el procesamiento del certificado .p12');
          return;
        }
      }
    });


    async function crearCompanyAPI(datoscompañia: Record<string, FormDataEntryValue>){
      const { identification_number, certificadop12base64, password, idsoftware, pinsoftware } = datoscompañia;
        const dv = getDgv(Number(identification_number));
        try{
            const url = `https://apidianj2.com/api/ubl2.1/config/${identification_number}/${dv}`; //llamado a la API REST Dianlaravel
            const respuesta = await fetch(url,  {
                                                  method: 'POST',
                                                  headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                                  body: JSON.stringify(datoscompañia)
                                                });
            const resultado = await respuesta.json();
            if(resultado.success){
              miDialogoCompañia.close();
              document.removeEventListener("click", cerrarDialogoExterno);
              
              const cert = await configCertificado(resultado.token, certificadop12base64+'', password+'');
              if(cert == undefined){
                //eliminar usuario de la api
                sendDeleteCompany('', identification_number+'', resultado.token);
                msjalertToast('error', '¡Error!', 'Error en la obtencion del certificado digital.');
                return;
              }
              const soft = await configSoftware(resultado.token, idsoftware+'', pinsoftware+'');
              if(soft == undefined){
                //eliminar usuario de la api
                sendDeleteCompany('', identification_number+'', resultado.token);
                msjalertToast('error', '¡Error!', 'Error en la configuracion del software.');
                return;
              }
              const resolprueba:resolconfig = {
                type_document_id:'1', 
                prefix:'SETP', 
                resolution:'18760000001', 
                resolution_date:'2019-01-19', 
                technical_key: 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c', 
                from: '990000000', 
                to: '995000000', 
                generated_to_date:'0', 
                date_from:'2019-01-19', 
                date_to: '2030-01-19'
              };
              const resol = await crearResolucion(resolprueba, resultado.token);
              if(resol == undefined){
                //eliminar usuario de la api
                sendDeleteCompany('', identification_number+'', resultado.token);
                msjalertToast('error', '¡Error!', 'Error en la configuracion de la resolucion.');
                return;
              }
              /////    crear resolucion para NC    ///////
              const extprefix = (datoscompañia.business_name as string).match(/[a-zA-Z]/g)!;
              const a:string = extprefix[0];
              const b:string = extprefix[extprefix.length-1];
              const resolNC:resolconfig = {
                type_document_id:'4', 
                prefix:'NC'+a+b, 
                from: '1', 
                to: '99999999', 
              };
              
              const resResolNC = await crearResolucion(resolNC, resultado.token); //crear resolucion nota credito invoice en la api
              if(resResolNC == undefined){
                //eliminar usuario de la api
                sendDeleteCompany('', identification_number+'', resultado.token);
                msjalertToast('error', '¡Error!', 'Error en la configuracion de la resolucion de NC.');
                return;
              }

              const idcompany = await crearCompanyJ2(datoscompañia, resultado.token);
              //crear resolucion nota credito invoice de forma local
              if(idcompany!=undefined){
                const resResolNCJ2 = crearResolucionNCJ2(resolNC, datoscompañia.identification_number+'', idcompany);
              }
      
            }else{
              msjalertToast('error', '¡Error!', 'No se pudo crear la compañia de facturacion.');
            }
          } catch (error) {
              console.log(error);
          }
    }


    ///////    CREAR CERTIFICADO EN LA API DIAN    ///////
    async function configCertificado(token:string, certificado:string, password:string):Promise<boolean|undefined> {
      try{
          const url = "https://apidianj2.com/api/ubl2.1/config/certificate"; //llamado a la API REST Dianlaravel
          const respuesta = await fetch(url, {
                                                method: 'PUT',
                                                headers: {
                                                  "Accept": "application/json",
                                                  "Content-Type": "application/json",
                                                  "Authorization": "Bearer "+token
                                                },
                                                body: JSON.stringify({"certificate": certificado, "password":password})
                                              });
          const resultado = await respuesta.json();
          return resultado.success;
        } catch (error) {
            console.log(error);
            return false;
        }
    }

    
    ///////    CREAR SOFTWARE EN LA API DIAN    ////////
    async function configSoftware(token:string, idsoftware:string, pinsoftware:string):Promise<boolean|undefined>
    {
      try {
        const url = "https://apidianj2.com/api/ubl2.1/config/software"; //llamado a la API REST Dianlaravel
        const respuesta = await fetch(url, {
                                              method: 'PUT',
                                              headers: {
                                                "Accept": "application/json",
                                                "Content-Type": "application/json",
                                                "Authorization": "Bearer "+token
                                              },
                                              body: JSON.stringify({"id": idsoftware, "pin":pinsoftware})
                                            });
        const resultado = await respuesta.json();
        return resultado.success;
      } catch (error) {
        console.log(error);
        return false;
      }
    }


    ///////    CREAR RESOLUCIONES EN LA API    ////////
    async function crearResolucion(resolition:resolconfig, token:string)
    {
      try {
        const url = "https://apidianj2.com/api/ubl2.1/config/resolution"; //llamado a la API REST Dian-laravel
        const respuesta = await fetch(url, {
                                              method: 'PUT',
                                              headers: {
                                                "Accept": "application/json",
                                                "Content-Type": "application/json",
                                                "Authorization": "Bearer "+token
                                              },
                                              body: JSON.stringify(resolition)
                                            });
        const resultado = await respuesta.json();
        return resultado.success;
      } catch (error) {
        console.log(error);
        return false;
      }
      
    }


    //////// CREAR RESOLUCION NC PARA INVOICE EN J2 LOCALMENTE ////////////
    async function crearResolucionNCJ2(resolNC:resolconfig, identification_number:string, idcompany:string){
      resolNC.technical_key = identification_number;
      resolNC.idcompany = idcompany;
      try {
          const url = `/admin/api/guardarNCInvoiceJ2`; //llamado a la API para guardar la resolucion NC DIAN de forma local
          const respuesta = await fetch(url,  { method: 'POST',
                                                headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                                body: JSON.stringify(resolNC)
                                              });
          const resultado = await respuesta.json();
          return resultado;
      } catch (error) {
        console.log(error);
      }
    }


    ///////    CREAR COMPAÑIA EN J2    ////////
    async function crearCompanyJ2(datoscompañia: Record<string, FormDataEntryValue>, token:string):Promise<string|undefined>{
      datoscompañia.token = token;
      try {
            const url = `/admin/api/crearCompanyJ2`; //llamado a la API para crear la compañia en j2
            const respuesta = await fetch(url,  { method: 'POST',
                                                  headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                                  body: JSON.stringify(datoscompañia)
                                                });
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              const tablaCompañias = document.querySelector('#tablaCompañias tbody');

              const tr = tablaCompañias?.querySelector('tr[id="company'+datoscompañia.identification_number+'"]');
              if(tr){
                tr.remove();
              }

              tablaCompañias?.insertAdjacentHTML('beforeend', filaCompañiaDian(resultado.id, datoscompañia.business_name, datoscompañia.identification_number, datoscompañia.idsoftware));

              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              // añadir a los selects de obtener compañia para resoluciones y de set pruebas
              SetCompañiaToSelect(resultado.id, token, datoscompañia.business_name+'');
              companiesAll.push({id:resultado.id, identification_number:datoscompañia.identification_number+'', business_name:datoscompañia.business_name+'', idsoftware:datoscompañia.idsoftware+'', token});
              return resultado.id;
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
          } catch (error) {
              console.log(error);
          }
    }

    function filaCompañiaDian(id:string, businessName:unknown, identificationNumber:unknown, idsoftware:unknown):string{
      const idSeguro = escapeHtmlDian(id);
      const nombreSeguro = escapeHtmlDian(businessName);
      const documentoSeguro = escapeHtmlDian(identificationNumber);
      const softwareSeguro = escapeHtmlDian(idsoftware);
      return `
        <tr id="company${documentoSeguro}">
          <td>${idSeguro}</td>
          <td>
            <span class="table-entity">
              <span class="table-entity__icon"><i class="fa-solid fa-building-user"></i></span>
              <span>${nombreSeguro}</span>
            </span>
          </td>
          <td><span class="table-badge table-badge--primary">${documentoSeguro}</span></td>
          <td><span class="table-badge table-badge--neutral max-w-md !whitespace-normal break-all font-mono">${softwareSeguro}</span></td>
          <td class="accionestd">
            <div class="acciones-btns">
              <button id="${idSeguro}" class="table-action table-action--danger" type="button" title="Eliminar compañia">
                <span class="material-symbols-outlined eliminarcompañia">delete</span>
              </button>
            </div>
          </td>
        </tr>`;
    }

    function escapeHtmlDian(valor:unknown):string{
      return String(valor ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[caracter] as string));
    }
    
    //Tabla lista de compañias
    document.querySelector('#tablaCompañias tbody')?.addEventListener('click', (e:Event)=>{
      const target = e.target as HTMLElement;
      if(target?.classList.contains("eliminarcompañia")){
        const id = target.parentElement?.id;
        const oneC = companiesAll.find(x=>x.id == id)!;
        eliminarCompañia(id!, oneC?.identification_number, oneC.token);
      }
    });

    ///////    ELIMINAR COMPAÑIA    ///////
    function eliminarCompañia(id:string, identification_number:string, token:string){
      Swal.fire({
          customClass: {
            popup: 'j2-confirm j2-confirm--danger',
            icon: 'j2-confirm__icon',
            title: 'j2-confirm__title',
            htmlContainer: 'j2-confirm__text',
            actions: 'j2-confirm__actions',
            confirmButton: 'j2-confirm__button j2-confirm__button--danger',
            cancelButton: 'j2-confirm__button j2-confirm__button--cancel'
          },
          icon: 'question',
          title: 'Desea eliminar la compañia?',
          html: "La compañia sera eliminada definitivamente de la configuracion DIAN.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false,
      }).then((result:any) => {
          if (result.isConfirmed) {
            sendDeleteCompany(id, identification_number, token)
          }
      });
    }


    function sendDeleteCompany(id:string, identification_number:string, token:string){
      (async ()=>{ 
        try {
          const url = "https://apidianj2.com/api/ubl2.1/config/deleteCompany"; //llamado a la API REST Dianlaravel
          const respuesta = await fetch(url, {
                                                method: 'DELETE',
                                                headers: { "Accept": "application/json", "Content-Type": "application/json", "Authorization": "Bearer "+token },
                                                body: JSON.stringify({"identification_number": identification_number})
                                              });
          const resultado = await respuesta.json();
          if(resultado.message){
            const urlX = "/admin/api/eliminarCompanyLocal?id="+identification_number; // para eliminar compañia localmente
            const respuestaLocal = await fetch(urlX); 
            const resultadoLocal = await respuestaLocal.json(); 
            if(!isNaN(Number(id)) && resultadoLocal.exito !== undefined){
              document.querySelector('#company'+identification_number)?.remove();
              deleteFromCompany(id);
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
                title: 'Compañia eliminada',
                html: resultado.message || 'La compañia fue eliminada correctamente.',
                confirmButtonText: 'OK',
                buttonsStyling: false,
              });
            }
          }else{
            msjalertToast('error', '¡Error!', 'Error intentalo nuevamente');
          }
        } catch (error) {
          console.log(error);
          return false;
        }
      })();
    }


    ///////  eliminar de los select de obtener resolucion y enviar set de pruebas  ///////
    function deleteFromCompany(id:string){
      selectResolucioncompañia.querySelector(`option[value=${id}]`)?.remove();
      selectSetCompañia.querySelector(`option[value=${id}]`)?.remove();
    }


    ///////    Set compañia en los select    ///////
    function SetCompañiaToSelect(id:string, token:string, business_name:string){
      selectResolucioncompañia.insertAdjacentHTML('afterbegin', `<option data-token="" value="${id}" >${business_name}</option>`);
      selectSetCompañia.insertAdjacentHTML('afterbegin', `<option data-token="${token}" value="${id}" >${business_name}</option>`);
    }
    

    function base64(archivo: File):Promise<string>{
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
            const base64Completo = reader.result as string;
            const base64Puro = base64Completo.split(',')[1];
            resolve(base64Puro);
        };
        reader.onerror = () => reject(new Error('Error al leer el archivo'));
        reader.readAsDataURL(archivo);
      });
    }

    /*function getDgv(nit: number): number {
        const multiplicadores = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        const digitos = nit.toString().trim().split('').map(Number);
        let suma = 0;
        digitos.forEach((digito, indice) => {
            const posicionMultiplicador = digitos.length - indice;
            suma += digito * multiplicadores[posicionMultiplicador - 1];
        }); 
        const modulo = suma%11;
        return modulo > 1 ?(11 - modulo):modulo;
    }*/


    function cerrarDialogoExterno(event:Event) {
      if(event.target === miDialogoAdquirirCompañia || event.target === miDialogoCompañia || event.target === miDialogosetpruebas || event.target === miDialogoGetResolucion || event.target === miDialogoRecepcionDocumentos || (event.target as HTMLInputElement).value === 'Salir' || (event.target as HTMLInputElement).value === 'Cancelar') {
        miDialogoAdquirirCompañia.close();  
        miDialogoCompañia.close();
        miDialogoGetResolucion.close();
        miDialogosetpruebas.close();
        miDialogoRecepcionDocumentos?.close();
        document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateCompañia') as HTMLFormElement)?.reset();
    }


    POS.cerrarDialogoExterno = cerrarDialogoExterno;
    POS.crearResolucion = crearResolucion;
    POS.crearCompanyJ2 = crearCompanyJ2;
    POS.crearResolucionNCJ2 = crearResolucionNCJ2;
  }

})();
