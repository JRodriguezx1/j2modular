(():void=>{

  if(document.querySelector('.mediosPagos')){
    const crearMedioPago = document.querySelector('#crearMedioPago') as HTMLButtonElement;
    const miDialogoMedioPago = document.querySelector('#miDialogoMedioPago') as any;

    let indiceFila=0, control=0, tablamediosPagos:HTMLElement;

    type mediospagoapi = {
        id:string,
        mediopago: string,
        estado: string,
        nick: string
      };
  
      let mediospagos:mediospagoapi[]=[], unmediopago:mediospagoapi|undefined;

      (async ()=>{
        try {
            const url = "/admin/api/allmediospago"; //llamado a la API REST y se trae todos las medios de pago
            const respuesta = await fetch(url); 
            mediospagos = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();

    //////////////////  TABLA //////////////////////
  tablamediosPagos = ($('#tablamediosPagos') as any).DataTable(configdatatablesToolbar);
   modernizarToolbarDataTable('#tablamediosPagos');

    crearMedioPago.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalMedioPago')!.textContent = "Crear medio de pago";
        (document.querySelector('#btnEditarCrearMedioPago') as HTMLInputElement).value = "Crear";
        miDialogoMedioPago.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablamediosPagos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target?.classList.contains("statemediopago"))changeState(e);
      if(target?.classList.contains("editarMedioPago")||target.parentElement?.classList.contains("editarMedioPago"))editarMedioPago(e);
      if(target?.classList.contains("eliminarMedioPago")||target.parentElement?.classList.contains("eliminarMedioPago"))eliminarMedioPago(e);
    });


    ///////////////  Cambiar estado del medio de pago  ////////////////
    function changeState(e:Event){
      const button=(e.target as HTMLButtonElement), info = (tablamediosPagos as any).page.info();
      indiceFila =  (tablamediosPagos as any).row(button.closest('tr')).index();
      (async ()=>{ 
        let estado = button.dataset.state=='0'?'1':'0';
        const datos = new FormData();
        datos.append('id', button.id);
        datos.append('estado', estado);
        try {
            const url = "/admin/api/updateStateMedioPago";
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();  
            if(resultado.exito !== undefined){
              const s1 = `<button id="${button?.id}" data-state="${button.dataset.state == estado}" class="statemediopago table-status cursor-pointer justify-center border-0 transition hover:-translate-y-px hover:shadow-md ${estado == '1' ? 'table-status--success' : 'table-status--danger'}">${estado == '1' ? 'Activo' : 'Inactivo'}</button>`;
              (tablamediosPagos as any).cell((tablamediosPagos as any).row(indiceFila+=info.start), 2).data(s1).draw(); //se modifica solo la columna con la fila correspondiente, y destruye la que habai antes
              (tablamediosPagos as any).page(info.page).draw('page'); //me mantiene la pagina actual
            }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
        } catch (error) {
            console.log(error);
        }
      })();//cierre de async()
    }

    //////////////////// ventana modal al Actualizar/Editar medio de pago  //////////////////////
    function editarMedioPago(e:Event){
      let idmediopago = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idmediopago = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalMedioPago')!.textContent = "Actualizar medio de pago";
      (document.querySelector('#btnEditarCrearMedioPago') as HTMLInputElement)!.value = "Actualizar";
      
      unmediopago = mediospagos.find(x => x.id==idmediopago); //me trae el emdio de pago seleccionado
      (document.querySelector('#nombreMedioPago')as HTMLInputElement).value = unmediopago?.mediopago!;
      
      indiceFila = (tablamediosPagos as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoMedioPago.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar medio de pago  //////////////////////
    document.querySelector('#formCrearUpdateMedioPago')?.addEventListener('submit', e=>{
      let urlApi = "crearMedioPago";
      if(control)urlApi = "actualizarMedioPago";

        e.preventDefault();
        var info = (tablamediosPagos as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unmediopago?.id?unmediopago?.id:'');
          datos.append('mediopago', $('#nombreMedioPago').val()as string);
          try {
              const url = "/admin/api/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoMedioPago.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de los medios de pagos ///
                  mediospagos = [...mediospagos, resultado.mediopago];
                  (tablamediosPagos as any).row.add(filaMedioPago((tablamediosPagos as any).rows().count() + 1, resultado.mediopago)).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de medios de pagos ///
                  const medioActualizado = Array.isArray(resultado.mediopago) ? resultado.mediopago[0] : resultado.mediopago;
                  mediospagos.forEach(a=>{if(a.id == unmediopago?.id)a = Object.assign(a, medioActualizado);});
                  indiceFila += info.start;
                  const datosActuales = (tablamediosPagos as any).row(indiceFila).data();
                  (tablamediosPagos as any).row(indiceFila).data(filaMedioPago(datosActuales[0], medioActualizado)).draw();
                  (tablamediosPagos as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });

    function filaMedioPago(numero:number, medioPago:any):any[]{
      return [
        numero,
        `<span class="table-entity">
          <span class="table-entity__icon"><i class="fa-solid fa-credit-card"></i></span>
          <span>${medioPago?.mediopago}</span>
        </span>`,
        `<button id="${medioPago?.id}" data-state="${medioPago?.estado == 1 ? '1' : '0'}" class="statemediopago table-status cursor-pointer justify-center border-0 transition hover:-translate-y-px hover:shadow-md ${medioPago?.estado == 1 ? 'table-status--success' : 'table-status--danger'}">${medioPago?.estado == 1 ? 'Activo' : 'Inactivo'}</button>`,
        renderAccionesMedioPago(medioPago)
      ];
    }

    function renderAccionesMedioPago(medioPago:any):string{
      if(Number(medioPago?.id) === 1)return '';
      return `<div class="acciones-btns" id="${medioPago?.id}" data-mediopago="${medioPago?.mediopago}">
        <button class="btn-md btn-turquoise editarMedioPago"><i class="fa-solid fa-pen-to-square"></i></button>
        <button class="btn-md btn-red eliminarMedioPago"><i class="fa-solid fa-trash-can"></i></button>
      </div>`;
    }
    

    ////////////////////  Eliminar medio de pago  //////////////////////
    function eliminarMedioPago(e:Event){
      let idmediopago = (e.target as HTMLElement).parentElement!.id, info = (tablamediosPagos as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idmediopago = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablamediosPagos as any).row((e.target as HTMLElement).closest('tr')).index();
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
          title: 'Eliminar medio de pago',
          html: '<strong>Esta accion no se puede deshacer.</strong><br>El medio de pago sera eliminado definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idmediopago);
                  try {
                      const url = "/admin/api/eliminarMedioPago";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablamediosPagos as any).row(indiceFila+info.start).remove().draw(); 
                        (tablamediosPagos as any).page(info.page).draw('page'); 
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
                          title: 'Medio eliminado',
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
      if (event.target === miDialogoMedioPago || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoMedioPago.close();
          document.removeEventListener("click", cerrarDialogoExterno);
          /*if((event.target as HTMLElement).closest('.finCerrarcaja')){  //Cuando se hace el cierre de caja
            confirmarcierre();
          }*/
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateMedioPago') as HTMLFormElement)?.reset();
    }
  }
})();
