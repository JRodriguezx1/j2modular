(():void=>{

  if(document.querySelector('.gestionimpresoras')){
    const crearImpresora = document.querySelector('#crearImpresora') as HTMLButtonElement;
    const miDialogoIMpresora = document.querySelector('#miDialogoIMpresora') as any;

    //let indiceFila=0, control=0, tablaImpresoras:HTMLElement;

    let control = 0;
    let tablaImpresoras: any;
    let filaSeleccionada: any = null;

    type printersApi = {
      id:string,
      nombre: string,
      nombrecompartido: string,
      estacion: string,
      mm: string,
      estado: string,
      created_at: string
    };

    let impresoras:printersApi[]=[], unPrinter:printersApi|undefined;

    (async ()=>{
      try {
          const url = "/admin/api/config/allPrinters"; //llamado a la API REST y se trae todos las impresoras
          const respuesta = await fetch(url); 
          impresoras = await respuesta.json(); 
      } catch (error) {
          console.log(error);
      }
    })();

    //////////////////  TABLA //////////////////////
  tablaImpresoras = ($('#tablaImpresoras') as any).DataTable(configdatatablesToolbar);
   modernizarToolbarDataTable('#tablaImpresoras');

    crearImpresora.addEventListener('click', ()=>{
        control = 0;
        unPrinter = undefined;
        filaSeleccionada = null;
        limpiarformdialog();
        document.querySelector('#modalIMpresora')!.textContent = "Crear punto de impresora";
        (document.querySelector('#btnEditarCrearImpresora') as HTMLInputElement).value = "Crear";
        miDialogoIMpresora.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaImpresoras')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target.closest('.editarImpresora'))editarImpresora(target);
      if(target.closest('.eliminarImpresora'))eliminarImpresora(target);
    });

    //////////////////// ventana modal al Actualizar/Editar impresora  //////////////////////
    function editarImpresora(target: HTMLElement){
      const contenedorAcciones = target.closest('.acciones-btns') as HTMLElement | null;
      const idImpresora = contenedorAcciones?.id;
      if(!idImpresora){
        msjalertToast('error', '¡Error!', 'No fue posible identificar la impresora');
        return;
      }
      unPrinter = impresoras.find(impresora =>impresora.id == idImpresora);
      if(!unPrinter){
          msjalertToast('error', '¡Error!', 'No se encontró la información de la impresora');
          return;
      }
      control = 1;
      filaSeleccionada = obtenerFilaDataTable(target);
      document.querySelector('#modalIMpresora')!.textContent = "Actualizar punto de impresora";
      (document.querySelector('#btnEditarCrearImpresora') as HTMLInputElement)!.value = "Actualizar";
      (document.querySelector('#nombreImpresora')as HTMLInputElement).value = unPrinter.nombre!;
      (document.querySelector('#nombreCompartido')as HTMLInputElement).value = unPrinter.nombrecompartido!;
      (document.querySelector('#anchoPapel')as HTMLInputElement).value = unPrinter.mm!;
      (document.querySelector('#estacion')as HTMLInputElement).value = unPrinter.estacion!;
      miDialogoIMpresora.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Crear/Editar impresora  //////////////////////
    document.querySelector('#formCrearUpdateIMpresora')?.addEventListener('submit', async e=>{
        e.preventDefault();
        const botonEnviar = document.querySelector('#btnEditarCrearImpresora') as HTMLInputElement;
        if(botonEnviar.disabled)return;
        
        botonEnviar.disabled = true;
        let urlApi = control==1 ? "actualizarPrinter": "crearPrinter";
        
        const datos = new FormData();
        if(control === 1 && unPrinter)datos.append('id', unPrinter.id);
        datos.append('nombre', $('#nombreImpresora').val()as string);
        datos.append('nombrecompartido', $('#nombreCompartido').val()as string);
        datos.append('mm', $('#anchoPapel').val()as string);
        datos.append('estacion', $('#estacion').val()as string);
        try {
            const url = "/admin/api/config/"+urlApi;
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();

            if(!respuesta.ok)throw new Error( resultado.error?.[0] ?? 'No fue posible procesar la solicitud');
            if(resultado.error){
              msjalertToast('error', '¡Error!', resultado.error[0]);
              return;
            }

            if(resultado.exito !== undefined){
              miDialogoIMpresora.close();
              document.removeEventListener("click", cerrarDialogoExterno);
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              if(!control){ //si es crear registro
                /// actualizar el arregle de las impresoras ///
                impresoras = [...impresoras, resultado.printer];
                tablaImpresoras.row.add(filaImpresora((tablaImpresoras as any).rows().count() + 1, resultado.printer)).draw(false); // draw(false) evita recargar toda la tabla
              }else{ //si es actualizar
                /// actualizar el arregle de impresoras ///
                const printerActualizada = resultado.printer;
                impresoras.forEach(a=>{if(a.id == unPrinter?.id)Object.assign(a, printerActualizada);});
                //actualizar tabla de datatable
                if(filaSeleccionada){
                  const datosFila = filaSeleccionada.data();
                  filaSeleccionada.data(filaImpresora(datosFila[0], printerActualizada)).draw(false);
                }
              }
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
          }catch(error){
              console.log(error);
          }finally{
            botonEnviar.disabled = false;
          }
    });

    function filaImpresora(numero:number, impresora:any):any[]{
      return [
        numero,
        renderNombreImpresora(impresora?.nombre),
        renderBadgeImpresora(impresora?.nombrecompartido, 'shared'),
        renderBadgeImpresora(impresora?.estacion, 'station'),
        renderBadgeImpresora(`${impresora?.mm ?? ''} mm`, 'paper'),
        renderEstadoImpresora(impresora?.estado),
        `<div class="acciones-btns" id="${escapeHtmlImpresora(impresora?.id)}" data-impresora="${escapeHtmlImpresora(impresora?.nombre)}">
            <button class="btn-md btn-turquoise editarImpresora"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="btn-md btn-red eliminarImpresora"><i class="fa-solid fa-trash-can"></i></button>
        </div>`
      ];
    }

    function renderNombreImpresora(nombre:any):string{
      return `<span class="table-entity">
        <span class="table-entity__icon"><i class="fa-solid fa-print"></i></span>
        <span>${escapeHtmlImpresora(nombre)}</span>
      </span>`;
    }

    function renderBadgeImpresora(valor:any, modificador:string):string{
      const clases:Record<string,string> = {
        shared: 'table-badge--primary !whitespace-normal break-words',
        station: 'table-badge--neutral',
        paper: 'table-badge--warning'
      };
      return `<span class="table-badge ${clases[modificador] ?? 'table-badge--neutral'}">${escapeHtmlImpresora(valor)}</span>`;
    }

    function renderEstadoImpresora(estado:any):string{
      const activa = String(estado) === '1' || String(estado).toLowerCase() === 'activa' || String(estado).toLowerCase() === 'activo';
      return `<span class="table-status ${activa ? 'table-status--success' : 'table-status--danger'}">${activa ? 'Activa' : 'Inactiva'}</span>`;
    }

    function escapeHtmlImpresora(valor:any):string{
      return String(valor ?? '').replace(/[&<>"']/g, (caracter:string) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[caracter] as string));
    }

    ////////////////////  Eliminar impresora  //////////////////////
    function eliminarImpresora(target: HTMLElement){
      
      const contenedorAcciones = target.closest('.acciones-btns') as HTMLElement | null;
      const idImpresora = contenedorAcciones?.id;
      if(!idImpresora){
        msjalertToast('error', '¡Error!', 'No fue posible identificar la impresora');
        return;
      }
      filaSeleccionada = obtenerFilaDataTable(target);
      
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
          title: 'Eliminar impresora',
          html: '<strong>Esta accion no se puede deshacer.</strong><br>La impresora sera eliminada definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idImpresora);
                  try {
                      const url = "/admin/api/config/eliminarPrinter";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        impresoras = impresoras.filter(impresora =>impresora.id !== String(idImpresora));
                        filaSeleccionada.remove().draw(false);
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
                          title: 'Impresora eliminada',
                          text: resultado.exito[0],
                          confirmButtonText: 'OK'
                        })
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
                            title: 'No se pudo eliminar',
                            text: resultado.error[0],
                            confirmButtonText: 'OK'
                          })
                      }
                  } catch (error) {
                      console.log(error);
                  }
              })();//cierre de async()
          }
      });
    }


    function obtenerFilaDataTable(elemento: HTMLElement): any {
      let filaHtml = elemento.closest('tr') as HTMLTableRowElement | null;
      if(filaHtml?.classList.contains('child'))
          filaHtml = filaHtml.previousElementSibling as HTMLTableRowElement;
      return tablaImpresoras.row(filaHtml);
    }

    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoIMpresora || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoIMpresora.close();
          document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateIMpresora') as HTMLFormElement)?.reset();
    }

  }

})();