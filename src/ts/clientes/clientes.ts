(()=>{
    if(document.querySelector('.clientes')){
      const btncrearCliente = document.querySelector('#crearCliente');
      const btncrearDireccion = document.querySelector('#crearDireccion');
      const miDialogoCliente = document.querySelector('#miDialogoCliente') as any;
      const miDialogoCrearDireccion = document.querySelector('#miDialogoCrearDireccion') as any;
      const miDialogoUpDireccion = document.querySelector('#miDialogoUpDireccion') as any;
      const selectdirecciones = document.querySelector('#selectdirecciones') as HTMLSelectElement;
      const btnCerrarUpDireccion = document.querySelector('#btnCerrarUpDireccion') as HTMLButtonElement;
      let select2Init = false, indiceFila=0, control=0, tablaClientes:HTMLElement, filaClienteActual:any = null;
      
      type direccionesapi = {
        id:string,
        idcliente:string,
        idtarifa:string,
        tarifa:{id:string, idcliente:string, nombre:string, valor:string},
        direccion:string,
        ciudad:string,
        departamento:string
      };
      let direcciones:direccionesapi[]=[];
  
      type clientesapi = {
        id:string,
        nombre: string,
        apellido: string,
        tipodocumento: string,
        identificacion: string,
        telefono: string,
        email: string,
        fecha_nacimiento: string,
        total_compras: string,
        ultima_compra: string;
        data1: string,
        //idservicios:{idempleado:string, idservicio:string}[]
      };
  
      let clientes:clientesapi[]=[], uncliente:clientesapi;
  
      (async ()=>{
        try {
            const url = "/admin/api/allclientes"; //llamado a la API REST y se trae todos los productos
            const respuesta = await fetch(url); 
            clientes = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();
  
      
  
      document.addEventListener("click", cerrarDialogoExterno);
      //////////////////  TABLA //////////////////////
      tablaClientes = ($('#tablaClientes') as any).DataTable({...configdatatables,});
      modernizarToolbarDataTable('#tablaClientes');
  
      btncrearCliente?.addEventListener('click', (e):void=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalCliente')!.textContent = "Crear cliente";
        (document.querySelector('#btnEditarCrearCliente') as HTMLInputElement).value = "Crear";
        miDialogoCliente.showModal();
      });


      btncrearDireccion?.addEventListener('click', (e):void=>{
        control = 0;
        document.querySelector('#modalDireccion')!.textContent = "Crear direccion";
        (document.querySelector('#btnEditarCrearDireccion') as HTMLInputElement).value = "Crear";
        miDialogoCrearDireccion.showModal();
        activarSelect2();
      });


      function activarSelect2(){
        if(select2Init)return;
        ($('#selectcliente') as any).select2({
          dropdownParent: $('#miDialogoCrearDireccion'),
          placeholder: "Seleccionar el cliente",
          minimumResultsForSearch: Infinity,
          width: '100%',
          dropdownCssClass: 'select2-theme-dropdown'
        });
        select2Init = true;
      }
  
      document.querySelector('#tablaClientes')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
        const target = e.target as HTMLElement;
        if((e.target as HTMLElement)?.classList.contains("editarClientes")||(e.target as HTMLElement).parentElement?.classList.contains("editarClientes"))editarClientes(e);
        if(target?.classList.contains("eliminarClientes")||target.parentElement?.classList.contains("eliminarClientes"))eliminarClientes(e);
        if(target?.classList.contains("editarEliminarDireccion")||target.parentElement?.classList.contains("editarEliminarDireccion"))upRemoveDir(e);
      });
  
      function editarClientes(e:Event){
        const target = e.target as HTMLElement;
        let idcliente = target.parentElement?.id!;
        if(target?.tagName === 'I')idcliente = target.parentElement?.parentElement?.id!;
        const currentRow = target.closest('tr');
        const dataRow = currentRow?.classList.contains('child') ? currentRow.previousElementSibling : currentRow;
        filaClienteActual = (tablaClientes as any).row(dataRow);
        indiceFila = filaClienteActual.index();
        control = 1;
        document.querySelector('#modalCliente')!.textContent = "Actualizar cliente";
        (document.querySelector('#btnEditarCrearCliente') as HTMLInputElement)!.value = "Actualizar";
        uncliente = clientes.find(x=>x.id === idcliente)!;
        (document.querySelector('#nombre')as HTMLInputElement).value = uncliente?.nombre!;
        (document.querySelector('#apellido')as HTMLInputElement).value = uncliente?.apellido!;
        $('#tipodocumento').val(uncliente?.tipodocumento??'');
        (document.querySelector('#identificacion')as HTMLInputElement).value = uncliente?.identificacion??'';
        (document.querySelector('#telefono')as HTMLInputElement).value = uncliente?.telefono??'';
        (document.querySelector('#email')as HTMLInputElement).value = uncliente?.email??'';
        (document.querySelector('#fecha_nacimiento')as HTMLInputElement).value = uncliente?.fecha_nacimiento??'';
        
        miDialogoCliente.showModal();
      }
  
      ////////////////////  Actualizar/Editar clientes  //////////////////////
      document.querySelector('#formCrearUpdateCliente')?.addEventListener('submit', e=>{
        if(control){
          e.preventDefault();
          var info = (tablaClientes as any).page.info();
          
          (async ()=>{ 
            const datos = new FormData();
            datos.append('idcliente', uncliente!.id);
            datos.append('nombre', $('#nombre').val()as string);
            datos.append('apellido', $('#apellido').val()as string);
            datos.append('tipodocumento', $('#tipodocumento').val()as string);
            datos.append('identificacion', $('#identificacion').val()as string);
            datos.append('telefono', $('#telefono').val()as string);
            datos.append('email', $('#email').val()as string);
            datos.append('fecha_nacimiento', $('#fecha_nacimiento').val()as string);
            try {
                const url = "/admin/api/actualizarCliente";
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json(); 
                if(resultado.exito !== undefined){
                  msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                  /// actualizar el arreglo de clientes ///
                  clientes = clientes.map(a => a.id == uncliente.id ? Object.assign(a, resultado.cliente?.[0] ?? {}) : a);
                  ///////// cambiar la fila completa, su contenido //////////
                  const filaApi = filaClienteActual || (tablaClientes as any).row(indiceFila);
                  const datosActuales = filaApi.data();
                  const nombre = ($('#nombre').val() as string) || '';
                  const apellido = ($('#apellido').val() as string) || '';
                  const identificacion = ($('#identificacion').val() as string) || '';
                  const telefono = ($('#telefono').val() as string) || '';
                  const email = ($('#email').val() as string) || '';
                  //filaApi.data(renderClienteRowData({ id: uncliente.id, identificacion, nombre, apellido, telefono, email, acciones: datosActuales[6] })).draw(false);
                  (tablaClientes as any).page(info.page).draw(false); //me mantiene la pagina actual
                  try {
                    (tablaClientes as any).columns.adjust().responsive.recalc();
                  } catch (error) {
                    console.log(error);
                  }
                }else{
                  msjalertToast('error', '¡Error!', resultado.error[0]);
                }
                miDialogoCliente.close();
                filaClienteActual = null;
                
            } catch (error) {
                console.log(error);
            }
          })();//cierre de async()
        } //fin if(control)
      });
  

      function upRemoveDir(e:Event){ //actualizar o eliminar direccion
        let idcliente = (e.target as HTMLElement).parentElement!.id, info = (tablaClientes as any).page.info();
        if((e.target as HTMLElement).tagName === 'I')idcliente = (e.target as HTMLElement).parentElement!.parentElement!.id;
        (async ()=>{
          try {
            const url = "/admin/api/clientes/direccionesXcliente?id="+idcliente; //llamado a la API REST y se trae las direcciones segun cliente elegido
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json();
            direcciones = resultado;
            addDireccionSelect(resultado.direcciones);
          } catch (error) {
              console.log(error);
          }
        })();
        miDialogoUpDireccion.showModal();
      }


       ////// añade direccion al select de direcciones al miDialogoUpDireccion, cuando se desea actualizar o eliminar la direccion de un cliente
      function addDireccionSelect<T extends {id:string, idcliente:string, idtarifa:string, tarifa:{id:string, idcliente:string, nombre:string, valor:string}, direccion:string, ciudad:string, departamento:string}>(addrs: T[]):void{
        while(selectdirecciones?.firstChild)selectdirecciones.removeChild(selectdirecciones?.firstChild);
        addrs.forEach(dir =>{
          const option = document.createElement('option');
          option.textContent = dir.direccion;
          option.value = dir.id;
          option.dataset.idcliente = dir.idcliente;
          option.dataset.idtarifa = dir.idtarifa;
          selectdirecciones.appendChild(option);
        });
        $('#uptarifa').val(addrs[0].idtarifa);
        (document.querySelector('#updepartamento') as HTMLInputElement).value = addrs[0].departamento;
        (document.querySelector('#upciudad') as HTMLInputElement).value = addrs[0].ciudad;
        (document.querySelector('#updireccion') as HTMLInputElement).value = addrs[0].direccion;
      }


      ///////// Evento al select de direcciones en el modal actualizar direciones de cada cliente ////////////
      selectdirecciones?.addEventListener('change', (e)=>{
        const select = (e.target as HTMLSelectElement);
        const idDir:string = select.options[select.selectedIndex].value;
        const objDireccion = direcciones.find(x=>x.id == idDir)!;
        $('#uptarifa').val(objDireccion?.idtarifa??1);
        (document.querySelector('#updepartamento') as HTMLInputElement).value = objDireccion.departamento;
        (document.querySelector('#upciudad') as HTMLInputElement).value = objDireccion.ciudad;
        (document.querySelector('#updireccion') as HTMLInputElement).value = objDireccion.direccion;
      });


      document.querySelector('#formUpDireccion')?.addEventListener('submit', e=>{
          e.preventDefault();
          // verificar si se oprimio el btn eliminar o actualizar del modal actualizar direccion
        });
        

      function eliminarClientes(e:Event){
        const target = e.target as HTMLElement;
        let idcliente = target.parentElement!.id, info = (tablaClientes as any).page.info();
        if(target.tagName === 'I')idcliente = target.parentElement!.parentElement!.id;
        indiceFila = (tablaClientes as any).row((e.target as HTMLElement).closest('tr')).index();
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
            title: 'Eliminar cliente',
            html: `Esta accion eliminara definitivamente al cliente.`,
            showCancelButton: true,
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'No',
        }).then((result:any) => {
            if (result.isConfirmed) {
                (async ()=>{ 
                    const datos = new FormData();
                    datos.append('id', idcliente);
                    try {
                        const url = "/admin/api/eliminarCliente";
                        const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                        const resultado = await respuesta.json();  
                        if(resultado.exito !== undefined){
                          (tablaClientes as any).row(indiceFila+info.start).remove().draw(); 
                          (tablaClientes as any).page(info.page).draw('page');
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
                            title: 'Cliente eliminado',
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


      btnCerrarUpDireccion.addEventListener('click', ()=>miDialogoUpDireccion.close());
  
  
      function limpiarformdialog(){
        (document.querySelector('#formCrearUpdateCliente') as HTMLFormElement)?.reset();
      }

      function escapeClienteHtml(value:string):string{
        return value.replace(/[&<>"']/g, (char) => {
          const entities:{[key:string]:string} = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
          };
          return entities[char];
        });
      }

      function cerrarDialogoExterno(event:Event) {
        if (event.target === miDialogoCliente || event.target === miDialogoCrearDireccion || event.target === miDialogoUpDireccion || (event.target as HTMLInputElement).value === 'salir') {
          miDialogoCliente.close();
          miDialogoCrearDireccion.close();
          miDialogoUpDireccion.close();
          
        }
      }
    }
  
  })();
