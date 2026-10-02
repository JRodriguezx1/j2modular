(():void=>{

  if(document.querySelector('.gestionbancos')){
    const crearBanco = document.querySelector('#crearBanco') as HTMLButtonElement;
    const miDialogoBanco = document.querySelector('#miDialogoBanco') as any;

    let indiceFila=0, control=0, tablaBancos:HTMLElement;

    type bancosapi = {
        id:string,
        nombre: string,
        numerocuenta: string,
        saldo: string,
        estado: string,
        created_at: string
      };
  
      let bancos:bancosapi[]=[], unbanco:bancosapi|undefined;

      (async ()=>{
        try {
            const url = "/admin/api/allbancos"; //llamado a la API REST y se trae todos los bancos
            const respuesta = await fetch(url); 
            bancos = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();

    //////////////////  TABLA //////////////////////
  tablaBancos = ($('#tablaBancos') as any).DataTable(configdatatablesToolbar);
   modernizarToolbarDataTable('#tablaBancos');

    crearBanco.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalBanco')!.textContent = "Crear banco";
        (document.querySelector('#btnEditarCrearBanco') as HTMLInputElement).value = "Crear";
        miDialogoBanco.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaBancos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarBanco")||(e.target as HTMLElement).parentElement?.classList.contains("editarBanco"))editarBanco(e);
      if(target?.classList.contains("eliminarBanco")||target.parentElement?.classList.contains("eliminarBanco"))eliminarBanco(e);
    });

    //////////////////// ventana modal al Actualizar/Editar banco  //////////////////////
    function editarBanco(e:Event){
      let idbanco = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idbanco = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalBanco')!.textContent = "Actualizar banco";
      (document.querySelector('#btnEditarCrearBanco') as HTMLInputElement)!.value = "Actualizar";
      
      unbanco = bancos.find(x => x.id==idbanco); //me trae a la banco seleccionada
      (document.querySelector('#nombreBanco')as HTMLInputElement).value = unbanco?.nombre!;
      (document.querySelector('#numeroCuenta')as HTMLInputElement).value = unbanco?.numerocuenta!;
      
      indiceFila = (tablaBancos as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoBanco.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar banco  //////////////////////
    document.querySelector('#formCrearUpdateBanco')?.addEventListener('submit', e=>{
      let urlApi = "crearBanco";
      if(control)urlApi = "actualizarBanco";

        e.preventDefault();
        var info = (tablaBancos as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unbanco?.id?unbanco?.id:'');
          datos.append('nombre', $('#nombreBanco').val()as string);
          datos.append('numerocuenta', $('#numeroCuenta').val()as string);
          try {
              const url = "/admin/api/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoBanco.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de la banco ///
                  bancos = [...bancos, resultado.banco];
                  (tablaBancos as any).row.add(filaBanco((tablaBancos as any).rows().count() + 1, resultado.banco)).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de bancos ///
                  bancos.forEach(a=>{if(a.id == unbanco?.id)a = Object.assign(a, resultado.banco[0]);});
                  indiceFila += info.start;
                  const datosActuales = (tablaBancos as any).row(indiceFila).data();
                  const bancoActualizado = {...resultado.banco[0], created_at: resultado.banco[0].created_at ?? unbanco?.created_at};
                  (tablaBancos as any).row(indiceFila).data(filaBanco(datosActuales[0], bancoActualizado)).draw();
                  (tablaBancos as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });

    function filaBanco(numero:number, banco:any):any[]{
      return [
        numero,
        renderNombreBanco(banco?.nombre),
        renderBadgeBanco(banco?.numerocuenta, 'table-badge--info !whitespace-normal break-all'),
        renderBadgeBanco(banco?.created_at, 'table-badge--warning'),
        `<div class="acciones-btns" id="${banco?.id}" data-banco="${banco?.nombre}">
            <button class="btn-md btn-turquoise editarBanco"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="btn-md btn-red eliminarBanco"><i class="fa-solid fa-trash-can"></i></button>
        </div>`
      ];
    }

    function renderNombreBanco(nombre:any):string{
        return `<span class="table-entity">
                  <span class="table-entity__icon"><i class="fa-solid fa-building-columns"></i></span>
                  <span>${nombre}</span>
                </span>`;
    }

    function renderBadgeBanco(valor:any, clases:string):string{
      return `<span class="table-badge ${clases}">${valor}</span>`;
    }


    ////////////////////  Eliminar banco  //////////////////////
    function eliminarBanco(e:Event){
      let idbanco = (e.target as HTMLElement).parentElement!.id, info = (tablaBancos as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idbanco = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaBancos as any).row((e.target as HTMLElement).closest('tr')).index();
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
          title: 'Eliminar banco',
          html: '<strong>Esta accion no se puede deshacer.</strong><br>El banco sera eliminado definitivamente.',
          showCancelButton: true,
          confirmButtonText: 'Si, eliminar',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idbanco);
                  try {
                      const url = "/admin/api/eliminarBanco";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaBancos as any).row(indiceFila+info.start).remove().draw(); 
                        (tablaBancos as any).page(info.page).draw('page'); 
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
                          title: 'Banco eliminado',
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
      if (event.target === miDialogoBanco || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoBanco.close();
          document.removeEventListener("click", cerrarDialogoExterno);
          /*if((event.target as HTMLElement).closest('.finCerrarbanco')){  //Cuando se hace el cierre de banco
            confirmarcierre();
          }*/
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateBanco') as HTMLFormElement)?.reset();
    }

  }

})();
