(()=>{
  if(document.querySelector('.creditos')){

    type creditsapi = {
      id:string,
      id_fksucursal: string,
      idemisor: string,
      usuariofk: string,
      idtipofinanciacion:string,
      factura_id: string,
      cliente_id: string,
      idestadocreditos: string,
      num_orden: string,
      nombrecliente: string,
      capital: string,
      abonoinicial: string,
      abonodecuotas: string,
      saldopendiente: string,
      numcuota: string,
      cantidadcuotas: string,
      montocuota: string,
      frecuenciapago: string,
      fechainicio: string,
      fechafin: string,
      diasenmora: string,
      interes: string,
      interesxcuota: string,
      interestotal: string,
      valorinteresxcuota: string,
      valorinterestotal: string,
      montototal: string,
      fechavencimiento: string,
      productoentregado: string,
      base: string,
      valorimpuestototal: string,
      dctox100: string,
      descuento: string,
      abonototalantiguo: string,
      cantidadcuotasantiguas: string,
      fechaultimoabonoantiguo: string,
      nota: string,
      cliente?: string,
      telefono?: string,
      identificacion?: string,
      sucursal?: string,
      estado: string,
      created_at: string,
    };

    type CreditoInterSucursal = {
      id: string,
      num_orden: string,
      idtipofinanciacion: string,
      id_fksucursal: string,
      fechainicio: string,
      fechavencimiento: string,
      montototal: string,
      saldopendiente: string,
      cliente: string,
      telefono: string,
      identificacion: string,
      sucursal: string,
    };

    type RespuestaBusquedaIntersucursal = {
      ok: boolean,
      data?: CreditoInterSucursal[],
      error?: string,
    };
    
    /*interface Item {
      id_impuesto: number,
      facturaid: number,
      basegravable: number,
      valorimpuesto: number
    }
    let factimpuestos:Item[] = [];*/
    const creditoInterSucursal = document.querySelector("#creditoInterSucursal") as HTMLInputElement;
    const miDialogoBuscarIntersucursal = document.querySelector('#miDialogoBuscarIntersucursal') as HTMLDialogElement;
    const listaCreditosIntersucursales = document.querySelector('#listaCreditosIntersucursales') as HTMLDivElement;

    let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;
    let credits:creditsapi[]=[], uncredito:creditsapi;
    let indiceFila=0, debounceTimer: ReturnType<typeof setTimeout>, controller: AbortController | null = null;


    document.addEventListener("click", cerrarDialogoExterno);

    /*(async ()=>{
      try {
          const url = "/admin/api/allcredits"; //llamado a la API REST y se trae todos los productos
          const respuesta = await fetch(url); 
          credits = await respuesta.json(); 
          console.log(credits);
      } catch (error) {
          console.log(error);
      }
    })();*/


    document.querySelector('#btnInterSucursal')?.addEventListener('click', ()=>{
      miDialogoBuscarIntersucursal.showModal();
      requestAnimationFrame(()=>creditoInterSucursal.focus());
    });

    creditoInterSucursal.addEventListener('input', (e:Event)=>{
      const contact = (e.target as HTMLInputElement).value.trim();
      clearTimeout(debounceTimer);
      controller?.abort();// Cancela un fetch anterior si todavía está en ejecución

      if(contact === ''){
        renderEstadoBusqueda('Escribe el ID del crédito, el nombre del cliente o su identificación.');
        return;
      }

      const esIdNumerico = /^\d+$/.test(contact);
      if(!esIdNumerico && contact.length < 4){
        renderEstadoBusqueda('Ingresa mínimo 4 caracteres para buscar por cliente o identificación.');
        return;
      }

      debounceTimer = setTimeout(async () => {
        const requestController = new AbortController();
        controller = requestController;
        renderEstadoBusqueda('Buscando créditos abiertos en otras sucursales...', 'cargando');
        try {
              const url = "/admin/api/creditos/buscarIntersucursal?q="+encodeURIComponent(contact); //llamado a la API REST
              const respuesta = await fetch(url, {
                method: 'GET',
                headers:{"Accept": "application/json"},
                signal: requestController.signal
              });
              const resultado = await respuesta.json() as RespuestaBusquedaIntersucursal;
              if(requestController.signal.aborted)return;
              if(!respuesta.ok || !resultado.ok){
                throw new Error(resultado.error || 'No fue posible realizar la búsqueda.');
              }
              printCreditosInterSucursal(resultado.data ?? []);
        }catch(error){
          if(error instanceof DOMException && error.name === 'AbortError') {
              return;
          }
          renderEstadoBusqueda(error instanceof Error ? error.message : 'No fue posible realizar la búsqueda.', 'error');
        }finally{
          if(controller === requestController)controller = null;
        }
      }, 400);
    });


    function renderEstadoBusqueda(mensaje:string, tipo:'normal'|'cargando'|'error' = 'normal'):void{
      while(listaCreditosIntersucursales.firstChild)listaCreditosIntersucursales.removeChild(listaCreditosIntersucursales.firstChild);
      const estado = document.createElement('p');
      estado.className = 'rounded-lg border p-4 text-center text-base font-semibold';
      estado.classList.add(
        tipo === 'error' ? 'border-rose-200' : tipo === 'cargando' ? 'border-indigo-200' : 'border-slate-200',
        tipo === 'error' ? 'bg-rose-50' : tipo === 'cargando' ? 'bg-indigo-50' : 'bg-slate-50',
        tipo === 'error' ? 'text-rose-700' : tipo === 'cargando' ? 'text-indigo-700' : 'text-slate-500'
      );
      estado.textContent = mensaje;
      listaCreditosIntersucursales.appendChild(estado);
    }


    function printCreditosInterSucursal(creditos:CreditoInterSucursal[]):void{
      while(listaCreditosIntersucursales.firstChild)listaCreditosIntersucursales.removeChild(listaCreditosIntersucursales.firstChild);
      if(creditos.length === 0){
        renderEstadoBusqueda('No se encontraron créditos abiertos en otras sucursales.');
        return;
      }
      const html = creditos.map(credito => `
        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="table-badge table-badge--info">${credito.idtipofinanciacion === '2'? 'Separado': 'Crédito'}</span>
                <span class="rounded-full bg-indigo-50 px-4 py-2 text-base font-semibold text-indigo-600">${credito.sucursal || 'Sin información'}</span>
            </div>

            <div class="flex justify-between items-center">
              <div class="">
                <h5 class="mb-0 mt-3 text-xl font-semibold text-slate-900">${credito.cliente || 'Cliente sin nombre'}</h5>
                <p class="mt-1 mb-0 text-lg text-slate-500">
                    Identificación: ${credito.identificacion || 'No registrada'} * Teléfono: ${credito.telefono || 'No registrado'}
                </p>
              </div>
              <div class="flex items-center gap-4">
                <div class="">
                  <p class="m-0 text-base font-semibold text-slate-500">Crédito</p>
                  <p class="m-0 text-lg font-bold text-slate-900">#${credito.id}</p>
                </div>
                <div class="">
                  <p class="m-0 text-base font-semibold text-slate-500">Saldo pendiente</p>
                  <p class="m-0 text-lg font-bold text-slate-900">$${Number(credito.saldopendiente).toLocaleString()}</p>
                </div>
              </div>
            </div>

            <div class="mt-2 flex justify-end">
              <a class="btnDialog btnDialog_primary" href="/admin/creditos/detallecredito?id=${credito.id}">
                Seleccionar
              </a>
            </div>
        </article>`).join('');
        listaCreditosIntersucursales.innerHTML = html;
    }

    
    //////////////////  TABLA //////////////////////
    let tablaCreditos = ($('#tablaCreditos') as any).DataTable({
      ...configdatatablesgenerico,
      layout: {
        topStart: 'buttons',
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
      },
      buttons: [
        {extend: 'copyHtml5', className: 'creditos-export-button creditos-export-button--copy', text: '<span class="creditos-export-button__icon"><i class="fa-regular fa-copy"></i></span><span>Copiar</span>', title: 'creditos-y-separados'},
        {extend: 'excelHtml5', className: 'creditos-export-button creditos-export-button--excel', text: '<span class="creditos-export-button__icon"><i class="fa-regular fa-file-excel"></i></span><span>Excel</span>', title: 'creditos-y-separados'},
        {extend: 'csvHtml5', className: 'creditos-export-button creditos-export-button--csv', text: '<span class="creditos-export-button__icon"><i class="fa-solid fa-file-csv"></i></span><span>CSV</span>', title: 'creditos-y-separados'},
        {extend: 'pdfHtml5', className: 'creditos-export-button creditos-export-button--pdf', text: '<span class="creditos-export-button__icon"><i class="fa-regular fa-file-pdf"></i></span><span>PDF</span>', title: 'creditos-y-separados'},
        {extend: 'print', className: 'creditos-export-button creditos-export-button--print', text: '<span class="creditos-export-button__icon"><i class="fa-solid fa-print"></i></span><span>Imprimir</span>', title: 'creditos-y-separados'},
        {extend: 'colvis', className: 'creditos-export-button creditos-export-button--columns', text: '<span class="creditos-export-button__icon"><i class="fa-solid fa-table-columns"></i></span><span>Columnas</span>'}
      ],
      order: [[ 0, 'desc' ]]
    });
    modernizarToolbarDataTable('#tablaCreditos');
    modernizarBotonesExportacionCreditos(tablaCreditos);


    //evento a la tabla
    document.querySelector('#tablaCreditos tbody')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target?.classList.contains("anularCredito")||(e.target as HTMLElement).parentElement?.classList.contains("anularCredito"))anularCredito(e);
      if(target?.classList.contains("printPOSSeparado"))printPOSSeparado(target.id);
    });


    function anularCredito(e:Event){
      let idcredito = (e.target as HTMLElement).parentElement?.id!, info = (tablaCreditos as any).page.info();
      if((e.target as HTMLElement)?.tagName === 'I')idcredito = (e.target as HTMLElement).parentElement?.parentElement?.id!;
      indiceFila = (tablaCreditos as any).row((e.target as HTMLElement).closest('tr')).index();
      
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
          buttonsStyling: false,
          icon: 'question',
          title: 'Desea anular el credito?',
          text: "El credito sera anulado definitivamente.",
          showCancelButton: true,
          confirmButtonText: 'Si, anular',
          cancelButtonText: 'Cancelar',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idcredito);
                  try {
                      const url = "/admin/api/anularSeparado";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();
                      if(resultado.exito !== undefined){
                        const datosActuales = (tablaCreditos as any).row(indiceFila).data();
                        datosActuales[9] = '<span class="table-status table-status--danger">Anulado</span>';
                        datosActuales[10] = `<div class="acciones-btns" id="${idcredito}"><a class="table-action table-action--view" href="/admin/creditos/detallecredito?id=${idcredito}" title="Ver detalle del credito"><i class="fa-solid fa-chart-simple"></i></a></div>`;
                        (tablaCreditos as any).row(indiceFila).data(datosActuales).draw();
                        (tablaCreditos as any).page(info.page).draw('page'); 
                        Swal.fire({
                          customClass: {
                            popup: 'j2-confirm j2-confirm--success',
                            icon: 'j2-confirm__icon',
                            title: 'j2-confirm__title',
                            htmlContainer: 'j2-confirm__text',
                            actions: 'j2-confirm__actions j2-confirm__actions--single',
                            confirmButton: 'j2-confirm__button j2-confirm__button--confirm'
                          },
                          buttonsStyling: false,
                          icon: 'success',
                          title: 'Credito anulado',
                          text: resultado.exito[0],
                          confirmButtonText: 'OK'
                        });
                      }else{
                          Swal.fire({
                            customClass: {
                              popup: 'j2-confirm j2-confirm--danger',
                              icon: 'j2-confirm__icon',
                              title: 'j2-confirm__title',
                              htmlContainer: 'j2-confirm__text',
                              actions: 'j2-confirm__actions j2-confirm__actions--single',
                              confirmButton: 'j2-confirm__button j2-confirm__button--danger'
                            },
                            buttonsStyling: false,
                            icon: 'error',
                            title: 'No se pudo anular',
                            text: resultado.error[0],
                            confirmButtonText: 'OK'
                          });
                      }
                  } catch (error) {
                      console.log(error);
                      Swal.fire({
                        customClass: {
                          popup: 'j2-confirm j2-confirm--danger',
                          icon: 'j2-confirm__icon',
                          title: 'j2-confirm__title',
                          htmlContainer: 'j2-confirm__text',
                          actions: 'j2-confirm__actions j2-confirm__actions--single',
                          confirmButton: 'j2-confirm__button j2-confirm__button--danger'
                        },
                        buttonsStyling: false,
                        icon: 'error',
                        title: 'No se pudo anular',
                        text: 'Intenta nuevamente o revisa la conexion.',
                        confirmButtonText: 'OK'
                      });
                  }
              })();//cierre de async()
          }
      });
    }


    async function printPOSSeparado(idcredito:string){
      //printTicketPOS(resultado.idfactura, resultado.dataInvoice);

      try{
        const url = "/admin/api/getCreditoSeparado?id="+idcredito; //llamado a la API REST - creditocontrolador 
        const respuesta = await fetch(url); 
        const resultado = await respuesta.json();
        const isAndroid = /Android/i.test(navigator.userAgent);
        if(printerBT === '1'){
          const builder = new ticketCreditoSeparadoBuilder(resultado);
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
        //window.open("/admin/printPDFPOS?id=" + idfactura, "_blank");  //controlador printcontrolador
      }catch(error){
        console.log(error);
      }

      if(!isNaN(Number(idcredito)))
        window.open("/admin/printPDFPOSSeparado?id=" + idcredito, "_blank"); //controlador printcontrolador
    }

    
    async function printTicketPOS(idfactura:string, datainvoice:DataInvoice){
      
      /*try {
        const url = "http://localhost:3100/api/printPOS/ticket1/CAJA"; //llamado a la API REST apidiancontrolador.php
        const respuesta = await fetch(url, {
          method: 'POST',
          headers: { "Accept": "application/json", "Content-Type": "application/json" },
          body: JSON.stringify(datainvoice)
        });
        const resultado = await respuesta.json();
        console.log(resultado);
      } catch (error) {
        console.log(error);
      }*/
    }


    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if(!(f instanceof Element))return;
      if(f === miDialogoBuscarIntersucursal || f.closest('.btnXCerrarInterSucursal')){
        miDialogoBuscarIntersucursal.close();
        clearTimeout(debounceTimer);
        controller?.abort();
        controller = null;
        creditoInterSucursal.value = '';
        renderEstadoBusqueda('Escribe el ID del crédito, el nombre del cliente o su identificación.');
      }
    }
  }
})();
