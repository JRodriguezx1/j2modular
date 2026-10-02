(():void=>{

  if(document.querySelector('.gestionEmisores')){
    const crearEmisor = document.querySelector('#crearEmisor') as HTMLButtonElement;
    const miDialogoEmisor = document.querySelector('#miDialogoEmisor') as any;

    let indiceFila=0, control=0, tablaEmisores:HTMLElement;
    let selectSucursalEmisorActivo = false;

    type emisoresApi = {
      id:string,
      nombre: string,
      nit: string,
      datosencabezados: string,
      telefono: string,
      estado: string,
      created_at: string
    };

    let emisores:emisoresApi[]=[], unEmisor:emisoresApi|undefined;
    (async ()=>{
      try {
          const url = "/admin/api/config/allEmisores"; //llamado a la API REST y se trae todos los emisores
          const respuesta = await fetch(url); 
          emisores = await respuesta.json(); 
      } catch (error) {
          console.log(error);
      }
    })();

     //////////////////  TABLA //////////////////////
   tablaEmisores = ($('#tablaEmisores') as any).DataTable(configdatatablesToolbar);
    modernizarToolbarDataTable('#tablaEmisores');

    crearEmisor.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalEmisor')!.textContent = "Crear nuevo emisor";
        (document.querySelector('#btnEditarCrearEmisor') as HTMLInputElement).value = "Crear";
        miDialogoEmisor.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaEmisores')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target?.classList.contains("stateEmisor"))changeState(e);
      if((e.target as HTMLElement)?.classList.contains("editarEmisor")||(e.target as HTMLElement).parentElement?.classList.contains("editarEmisor"))editarEmisor(e);
      if(target?.classList.contains("eliminarEmisor")||target.parentElement?.classList.contains("eliminarEmisor"))eliminarEmisor(e);
    });


    ///////////////  Cambiar estado del medio de pago  ////////////////
    function changeState(e:Event){
      const button=(e.target as HTMLButtonElement), info = (tablaEmisores as any).page.info();
      indiceFila =  (tablaEmisores as any).row(button.closest('tr')).index();
      (async ()=>{ 
        const datos = new FormData();
        datos.append('id', button.id);
        datos.append('estado', button.dataset.state=='0'?'1':'0');
        try {
            const url = "/admin/api/config/updateStateEmisor";
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();  
            if(resultado.exito !== undefined){
              const s1 = `<button id="${button.id}" data-state="${button.dataset.state=='0'?'1':'0'}" class="stateEmisor btn-xs ${button.dataset.state=='0'?'btn-lima':'btn-red'}">${button.dataset.state=='0'?'Activo':'Inactivo'}</button>`;
              (tablaEmisores as any).cell((tablaEmisores as any).row(indiceFila+=info.start), 4).data(s1).draw(); //se modifica solo la columna con la fila correspondiente, y destruye la que habai antes
              (tablaEmisores as any).page(info.page).draw('page'); //me mantiene la pagina actual
            }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
        } catch (error) {
            console.log(error);
        }
      })();//cierre de async()
    }

    //////////////////// ventana modal al Actualizar/Editar emisor  //////////////////////
    function editarEmisor(e:Event){
      let idemisor = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idemisor = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalEmisor')!.textContent = "Actualizar el emisor";
      (document.querySelector('#btnEditarCrearEmisor') as HTMLInputElement)!.value = "Actualizar";
      
      unEmisor = emisores.find(x => x.id==idemisor); //me trae el emisor seleccionado
      (document.querySelector('#nombreEmisor')as HTMLInputElement).value = unEmisor?.nombre!;
      (document.querySelector('#nitEmisor')as HTMLInputElement).value = unEmisor?.nit!;
      (document.querySelector('#datosencabezadosEmisor')as HTMLTextAreaElement).textContent = unEmisor?.datosencabezados!;
      (document.querySelector('#movilEmisor')as HTMLInputElement).value = unEmisor?.telefono!;
      
      indiceFila = (tablaEmisores as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoEmisor.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Crear/Editar emisor  //////////////////////
    document.querySelector('#formCrearUpdateEmisor')?.addEventListener('submit', e=>{
      let urlApi = "crearEmisor";
      if(control)urlApi = "actualizarEmisor";

        e.preventDefault();
        var info = (tablaEmisores as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unEmisor?.id?unEmisor?.id:'');
          datos.append('nombre', $('#nombreEmisor').val()as string);
          datos.append('nit', $('#nitEmisor').val()as string);
          datos.append('datosencabezados', $('#datosencabezadosEmisor').text());
          datos.append('telefono', $('#movilEmisor').val()as string);
          try {
              const url = "/admin/api/config/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoEmisor.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de las emisores ///
                  emisores = [...emisores, resultado.emisor];
                  (tablaEmisores as any).row.add([
                      (tablaEmisores as any).rows().count() + 1,
                      resultado.emisor.nombre,
                      resultado.emisor.nit,
                      resultado.emisor.telefono,
                      `<button id="${resultado.emisor.id}" data-state="${resultado.emisor.estado}" class="stateEmisor btn-xs btn-lima">Activo</button>`,
                      `<div class="acciones-btns" id="${resultado.emisor.id}">
                          <button class="btn-md btn-turquoise editarEmisor"><i class="fa-solid fa-pen-to-square"></i></button>
                          <button class="btn-md btn-red eliminarEmisor"><i class="fa-solid fa-trash-can"></i></button>
                      </div>`
                  ]).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de emisores ///
                  emisores.forEach(a=>{if(a.id == unEmisor?.id)a = Object.assign(a, resultado.emisor[0]);});
                  const datosActuales = (tablaEmisores as any).row(indiceFila+=info.start).data();
                  /*NOMBRE*/ datosActuales[1] = resultado.emisor[0].nombre;
                  /*nit*/ datosActuales[2] = resultado.emisor[0].nit;
                  /*telefono*/ datosActuales[3] = resultado.emisor[0].telefono;

                  (tablaEmisores as any).row(indiceFila).data(datosActuales).draw();
                  (tablaEmisores as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });


    ////////////////////  Eliminar emisor  //////////////////////
    function eliminarEmisor(e:Event){
      const accion = (e.target as HTMLElement).closest('.acciones-btns') as HTMLElement|null;
      let idemisor = accion?.id || (e.target as HTMLElement).parentElement!.id, info = (tablaEmisores as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idemisor = (e.target as HTMLElement).parentElement!.parentElement!.id;
      const nombreEmisor = accion?.dataset.emisor || 'este emisor';
      indiceFila = (tablaEmisores as any).row((e.target as HTMLElement).closest('tr')).index();
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
          icon: 'warning',
          title: 'Eliminar emisor',
          html: `Se eliminara definitivamente <strong>${nombreEmisor}</strong>. Esta accion no se puede deshacer.`,
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          reverseButtons: true,
          buttonsStyling: false,
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idemisor);
                  try {
                      const url = "/admin/api/config/eliminarEmisor";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaEmisores as any).row(indiceFila+info.start).remove().draw(); 
                        (tablaEmisores as any).page(info.page).draw('page'); 
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
                          title: 'Emisor eliminado',
                          html: resultado.exito[0],
                          confirmButtonText: 'Aceptar',
                          buttonsStyling: false
                        })
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

    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoEmisor || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoEmisor.close();
          document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateEmisor') as HTMLFormElement)?.reset();
      if(selectSucursalEmisorActivo)($('#sucursalEmisor') as any).val('').trigger('change');
    }

  }

})();
