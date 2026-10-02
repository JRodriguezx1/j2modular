(():void=>{

  if(document.querySelector('.tarifas')){
    const crearTarifa = document.querySelector('#crearTarifa') as HTMLButtonElement;
    const miDialogoTarifa = document.querySelector('#miDialogoTarifa') as any;

    let indiceFila=0, control=0, tablaTarifas:HTMLElement;

    type tarifasapi = {
        id:string,
        nombre: string,
        valor: string,
      };
  
      let tarifas:tarifasapi[]=[], unatarifa:tarifasapi|undefined;

      (async ()=>{
        try {
            const url = "/admin/api/alltarifas"; //llamado a la API REST y se trae todos las tarifas
            const respuesta = await fetch(url); 
            tarifas = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();

    //////////////////  TABLA //////////////////////
  tablaTarifas = ($('#tablaTarifas') as any).DataTable(configdatatablesToolbar);
   modernizarToolbarDataTable('#tablaTarifas');

    crearTarifa.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalTarifa')!.textContent = "Crear tarifa";
        (document.querySelector('#btnEditarCrearTarifa') as HTMLInputElement).value = "Crear";
        miDialogoTarifa.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaTarifas')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarTarifa")||(e.target as HTMLElement).parentElement?.classList.contains("editarTarifa"))editarTarifa(e);
      if(target?.classList.contains("eliminarTarifa")||target.parentElement?.classList.contains("eliminarTarifa"))eliminarTarifa(e);
    });

    //////////////////// ventana modal al Actualizar/Editar tarifa  //////////////////////
    function editarTarifa(e:Event){
      let idtarifa = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idtarifa = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalTarifa')!.textContent = "Actualizar tarifa";
      (document.querySelector('#btnEditarCrearTarifa') as HTMLInputElement)!.value = "Actualizar";
      
      unatarifa = tarifas.find(x => x.id==idtarifa); //me trae a la tarifa seleccionada
      (document.querySelector('#nombreTarifa')as HTMLInputElement).value = unatarifa?.nombre!;
      (document.querySelector('#valorTarifa')as HTMLInputElement).value = unatarifa?.valor!;
      
      indiceFila = (tablaTarifas as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoTarifa.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar tarifa  //////////////////////
    document.querySelector('#formCrearUpdateTarifa')?.addEventListener('submit', e=>{
      let urlApi = "crearTarifa";
      if(control)urlApi = "actualizarTarifa";

        e.preventDefault();
        var info = (tablaTarifas as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unatarifa?.id?unatarifa?.id:'');
          datos.append('nombre', $('#nombreTarifa').val()as string);
          datos.append('valor', $('#valorTarifa').val()as string);
          try {
              const url = "/admin/api/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoTarifa.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de la tarifa ///
                  tarifas = [...tarifas, resultado.tarifa];
                  (tablaTarifas as any).row.add(filaTarifa((tablaTarifas as any).rows().count() + 1, resultado.tarifa)).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de tarifas ///
                  tarifas.forEach(a=>{if(a.id == unatarifa?.id)a = Object.assign(a, resultado.tarifa[0]);});
                  indiceFila += info.start;
                  const datosActuales = (tablaTarifas as any).row(indiceFila).data();
                  (tablaTarifas as any).row(indiceFila).data(filaTarifa(datosActuales[0], resultado.tarifa[0])).draw();
                  (tablaTarifas as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });

    function filaTarifa(numero:number, tarifa:any):any[]{
      return [
        numero,
        `<span class="table-entity">
          <span class="table-entity__icon"><i class="fa-solid fa-percent"></i></span><span>${tarifa?.nombre}</span>
        </span>`,
        renderBadgeTarifa(tarifa?.valor),
        `<div class="acciones-btns" id="${tarifa?.id}" data-tarifa="${tarifa?.nombre}">
            <button class="btn-md btn-turquoise editarTarifa"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="btn-md btn-red eliminarTarifa"><i class="fa-solid fa-trash-can"></i></button>
        </div>`
      ];
    }

    function renderBadgeTarifa(valor:any):string{
        return `<span class="table-badge table-badge--success">$${ Number(valor ?? 0).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
    }


    ////////////////////  Eliminar tarifa  //////////////////////
    function eliminarTarifa(e:Event){
      let idtarifa = (e.target as HTMLElement).parentElement!.id, info = (tablaTarifas as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idtarifa = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaTarifas as any).row((e.target as HTMLElement).closest('tr')).index();
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
          title: 'Eliminar tarifa',
          html: '<strong>Esta accion no se puede deshacer.</strong><br>La tarifa sera eliminada definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idtarifa);
                  try {
                      const url = "/admin/api/eliminarTarifa";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaTarifas as any).row(indiceFila+info.start).remove().draw(); 
                        (tablaTarifas as any).page(info.page).draw('page'); 
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
                          title: 'Tarifa eliminada',
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

    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoTarifa || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoTarifa.close();
          document.removeEventListener("click", cerrarDialogoExterno);
          /*if((event.target as HTMLElement).closest('.finCerrartarifa')){  //Cuando se hace el cierre de tarifa
            confirmarcierre();
          }*/
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateTarifa') as HTMLFormElement)?.reset();
    }

  }

})();
