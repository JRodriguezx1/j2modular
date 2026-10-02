(():void=>{
  if(document.querySelector('.configNotificationWS')){

    const nombreWS = document.querySelector('#nombreWS') as HTMLInputElement;
    const movilWS = document.querySelector('#movilWS') as HTMLInputElement;
    const tipoWS = document.querySelector('#tipoWS') as HTMLInputElement;
    const tablaNumbersWS = document.querySelector('#tablaNumbersWS tbody') as HTMLTableElement;
    let indiceFila=0, control=0;

    tablaNumbersWS?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target?.classList.contains("test"))sendTest(target.closest('tr')?.id);//console.log(target.closest('tr'));
      if(target?.classList.contains("eliminarContacto")||target.parentElement?.classList.contains("eliminarContacto"))deleteContactNotificationWS(e);
    });
    

    async function sendTest(id:string|undefined){
      try {
          const url = "/admin/api/ws/notificacionWS/sendTest?id="+id;
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          if(resultado && resultado.code === 200 && !resultado.error){
            msjalertToast('success', '¡Éxito!', 'Mensaje de prueba enviado');
          }else{
              msjalertToast('error', '¡Error!', 'Error al enviar el test');
          }
      } catch (error) {
          console.log(error)
      }
    }


    ////////////////////  Eliminar contacto de notificacion de whatsapp  //////////////////////
    function deleteContactNotificationWS(e:Event){
      const fila = (e.target as HTMLTableRowElement).closest('tr');
      if(!fila)return;

      indiceFila = Number(fila.id);
      if(!Number.isInteger(indiceFila))return;

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
          title: 'Eliminar contacto',
          html: 'El destino de notificacion de WhatsApp sera eliminado definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'Cancelar',
          buttonsStyling: false
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', indiceFila+'');
                  try {
                      const url = "/admin/api/ws/notificacionWS/eliminarContacto?id="+indiceFila;
                      const respuesta = await fetch(url); 
                      const resultado = await respuesta.json(); 
                      if(resultado.exito !== undefined){
                        fila.remove();
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
                          title: 'Contacto eliminado',
                          html: resultado.exito[0] ?? 'El contacto fue eliminado correctamente.',
                          confirmButtonText: 'OK',
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

    ////////////////////  Crear contacto para notificacion de whatsapp  //////////////////////
    document.querySelector('#formCreateContactNotifcationWs')?.addEventListener('submit', e=>{
        e.preventDefault();
        
        (async ()=>{ 
          const datos = new FormData();
          //datos.append('id', unacaja?.id?unacaja?.id:'');
          datos.append('nombre', nombreWS.value);
          datos.append('movil', movilWS.value);
          datos.append('tipo', tipoWS.value);
          try {
              const url = "/admin/api/ws/notificacionWS/crearContacto";
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                const tr = document.createElement('tr');
                tr.id = resultado.data[1];
                tr.insertAdjacentHTML('afterbegin', `
                  <td><span class="table-entity"><span class="table-entity__icon !bg-green-100 !text-green-600"><i class="fa-brands fa-whatsapp"></i></span><span>${nombreWS.value}</span></span></td>
                  <td><span class="table-badge table-badge--info">${movilWS.value}</span></td>
                  <td><span class="table-badge table-badge--primary">${tipoWS.value}</span></td>
                  <td><button class="test inline-flex h-12 items-center justify-center rounded-md bg-indigo-50 px-4 text-base font-extrabold text-indigo-700 transition hover:bg-indigo-100" type="button">Test</button></td>
                  <td><span class="table-status table-status--success">Activo</span></td>
                  <td><button class="table-action table-action--danger eliminarContacto" type="button" title="Eliminar contacto"><i class="fa-solid fa-trash-can"></i></button></td>`);
                tablaNumbersWS?.appendChild(tr);
                (document.querySelector('#formCreateContactNotifcationWs') as HTMLFormElement)?.reset();
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });



  }

})();
