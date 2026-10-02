(():void=>{

  if(document.querySelector('.gestioncajas')){
    const crearCaja = document.querySelector('#crearCaja') as HTMLButtonElement;
    const miDialogoCaja = document.querySelector('#miDialogoCaja') as any;

    let indiceFila=0, control=0, tablaCajas:HTMLElement;

    type cajasapi = {
        id:string,
        idemisor:string,
        idtipoconsecutivo: string,
        nombre: string,
        negocio: string
      };
  
      let cajas:cajasapi[]=[], unacaja:cajasapi|undefined;
      (async ()=>{
        try {
            const url = "/admin/api/allcajas"; //llamado a la API REST y se trae todos las cajas
            const respuesta = await fetch(url); 
            cajas = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();

     //////////////////  TABLA //////////////////////
   tablaCajas = ($('#tablaCajas') as any).DataTable(configdatatablesToolbar);
    modernizarToolbarDataTable('#tablaCajas');

    crearCaja.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalCaja')!.textContent = "Crear caja";
        (document.querySelector('#btnEditarCrearCaja') as HTMLInputElement).value = "Crear";
        miDialogoCaja.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaCajas')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarCaja")||(e.target as HTMLElement).parentElement?.classList.contains("editarCaja"))editarCaja(e);
      if(target?.classList.contains("eliminarCaja")||target.parentElement?.classList.contains("eliminarCaja"))eliminarCaja(e);
    });

    //////////////////// ventana modal al Actualizar/Editar caja  //////////////////////
    function editarCaja(e:Event){
      let idcaja = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idcaja = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalCaja')!.textContent = "Actualizar caja";
      (document.querySelector('#btnEditarCrearCaja') as HTMLInputElement)!.value = "Actualizar";
      
      unacaja = cajas.find(x => x.id==idcaja); //me trae a la caja seleccionada
      (document.querySelector('#nombrecaja')as HTMLInputElement).value = unacaja?.nombre!;
      $('#idtipoconsecutivo').val(unacaja?.idtipoconsecutivo??'');
      $('#negociogestioncaja').val(1);
      $('#idEmisorCaja').val(unacaja?.idemisor??'');
      
      indiceFila = (tablaCajas as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoCaja.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar caja  //////////////////////
    document.querySelector('#formCrearUpdateCaja')?.addEventListener('submit', e=>{
      let urlApi = "crearCaja";
      if(control)urlApi = "actualizarCaja";

        e.preventDefault();
        var info = (tablaCajas as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unacaja?.id?unacaja?.id:'');
          datos.append('idemisor', $('#idEmisorCaja').val()as string);
          datos.append('idtipoconsecutivo', $('#idtipoconsecutivo').val()as string);
          datos.append('nombre', $('#nombrecaja').val()as string);
          datos.append('negocio', $('#negociogestioncaja').val()as string);
          try {
              const url = "/admin/api/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoCaja.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de la caja ///
                  cajas = [...cajas, resultado.caja];
                  (tablaCajas as any).row.add([
                      (tablaCajas as any).rows().count() + 1,
                      resultado.caja.nombre,
                      resultado.caja.nombreconsecutivo.nombre,
                      resultado.caja.negocio,
                      $('#idEmisorCaja option:selected').text(),
                      `<div class="acciones-btns" id="${resultado.caja.id}" data-caja="${resultado.caja.nombre}">
                          <button class="btn-md btn-turquoise editarCaja"><i class="fa-solid fa-pen-to-square"></i></button>
                          <button class="btn-md btn-red eliminarCaja"><i class="fa-solid fa-trash-can"></i></button>
                      </div>`
                  ]).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de cajas ///
                  cajas.forEach(a=>{if(a.id == unacaja?.id)a = Object.assign(a, resultado.caja[0]);});
                  const datosActuales = (tablaCajas as any).row(indiceFila+=info.start).data();
                  /*CAJA*/      datosActuales[1] = resultado.caja[0].nombre;
                  /*FACT AUTO*/ datosActuales[2] = $('#idtipoconsecutivo option:selected').text();
                  /*SEDE*/   datosActuales[3] = $('#negociogestioncaja option:selected').text();
                  /*EMISOR*/   datosActuales[4] = $('#idEmisorCaja option:selected').text();
                  (tablaCajas as any).row(indiceFila).data(datosActuales).draw();
                  (tablaCajas as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });


    ////////////////////  Eliminar caja  //////////////////////
    function eliminarCaja(e:Event){
      let idcaja = (e.target as HTMLElement).parentElement!.id, info = (tablaCajas as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idcaja = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaCajas as any).row((e.target as HTMLElement).closest('tr')).index();
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
          title: 'Eliminar caja',
          html: '<strong>Esta accion no se puede deshacer.</strong><br>La caja sera eliminada definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idcaja);
                  try {
                      const url = "/admin/api/eliminarCaja";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaCajas as any).row(indiceFila+info.start).remove().draw(); 
                        (tablaCajas as any).page(info.page).draw('page'); 
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
                          title: 'Caja eliminada',
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
                        text: 'Intenta nuevamente o revisa la conexion.',
                        confirmButtonText: 'OK'
                      })
                  }
              })();//cierre de async()
          }
      });
    }

    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoCaja || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoCaja.close();
          document.removeEventListener("click", cerrarDialogoExterno);
          /*if((event.target as HTMLElement).closest('.finCerrarcaja')){  //Cuando se hace el cierre de caja
            confirmarcierre();
          }*/
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateCaja') as HTMLFormElement)?.reset();
    }

  }

})();
