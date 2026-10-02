(():void=>{
  if(document.querySelector('.gestionproveedores')){
    const crearProveedor = document.querySelector('#crearProveedor') as HTMLButtonElement;
    const miDialogoProveedor = document.querySelector('#miDialogoProveedor') as any;

    let indiceFila=0, control=0, tablaProveedores:HTMLElement;

    type proveedoresapi = {
        id:string,
        nit: string,
        nombre: string,
        telefono: string,
        email: string,
        direccion: string,
        pais: string,
        ciudad: string,
        created_at: string
      };
  
      let proveedores:proveedoresapi[]=[], unproveedor:proveedoresapi|undefined;
      const proveedoresIniciales = document.querySelector('#proveedoresIniciales');
      try {
        proveedores = JSON.parse(proveedoresIniciales?.textContent || '[]');
      } catch (error) {
        console.log(error);
      }

     //////////////////  TABLA //////////////////////
    tablaProveedores = ($('#tablaProveedores') as any).DataTable(configdatatables);

    crearProveedor.addEventListener('click', ()=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalProveedor')!.textContent = "Crear proveedor";
        (document.querySelector('#btnEditarCrearProveedor') as HTMLInputElement).value = "Crear";
        miDialogoProveedor.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
    });


    document.querySelector('#tablaProveedores')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarProveedor")||(e.target as HTMLElement).parentElement?.classList.contains("editarProveedor"))editarProveedor(e);
      if(target?.classList.contains("eliminarProveedor")||target.parentElement?.classList.contains("eliminarProveedor"))eliminarProveedor(e);
    });

    //////////////////// ventana modal al Actualizar/Editar proveedor  //////////////////////
    function editarProveedor(e:Event){
      let idproveedor = (e.target as HTMLElement).parentElement?.id;
      if((e.target as HTMLElement)?.tagName === 'I')idproveedor = (e.target as HTMLElement).parentElement?.parentElement?.id;
      control = 1;
      document.querySelector('#modalProveedor')!.textContent = "Actualizar proveedor";
      (document.querySelector('#btnEditarCrearProveedor') as HTMLInputElement)!.value = "Actualizar";
      
      unproveedor = proveedores.find(x => x.id==idproveedor); //me trae a la proveedor seleccionada
      (document.querySelector('#nit')as HTMLInputElement).value = unproveedor?.nit!;
      (document.querySelector('#nombreProveedor')as HTMLInputElement).value = unproveedor?.nombre!;
      
      indiceFila = (tablaProveedores as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoProveedor.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar proveedor  //////////////////////
    document.querySelector('#formCrearUpdateProveedor')?.addEventListener('submit', e=>{
      let urlApi = "crearProveedor";
      if(control)urlApi = "actualizarProveedor";

        e.preventDefault();
        var info = (tablaProveedores as any).page.info();
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', unproveedor?.id?unproveedor?.id:'');
          datos.append('nit', $('#nit').val()as string);
          datos.append('nombre', $('#nombreProveedor').val()as string);
          try {
              const url = "/admin/api/"+urlApi;
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                miDialogoProveedor.close();
                document.removeEventListener("click", cerrarDialogoExterno);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                if(!control){ //si es crear registro
                  /// actualizar el arregle de los proveedores ///
                  proveedores = [...proveedores, resultado.proveedor];
                  (tablaProveedores as any).row.add([
                      (tablaProveedores as any).rows().count() + 1,
                      resultado.proveedor.nit,
                      resultado.proveedor.nombre,
                      resultado.proveedor.created_at,
                      `<div class="acciones-btns" id="${resultado.proveedor.id}" data-proveedor="${resultado.proveedor.nombre}">
                          <button class="btn-md btn-turquoise editarProveedor"><i class="fa-solid fa-pen-to-square"></i></button>
                          <button class="btn-md btn-red eliminarProveedor"><i class="fa-solid fa-trash-can"></i></button>
                      </div>`
                  ]).draw(false); // draw(false) evita recargar toda la tabla
                }else{ //si es actualizar
                  /// actualizar el arregle de proveedores ///
                  proveedores.forEach(a=>{if(a.id == unproveedor?.id)a = Object.assign(a, resultado.proveedor[0]);});
                  const datosActuales = (tablaProveedores as any).row(indiceFila+=info.start).data();
                  /*NIT*/       datosActuales[1] = resultado.proveedor[0].nit;
                  /*PROVEEDOR*/ datosActuales[2] = resultado.proveedor[0].nombre;
                  (tablaProveedores as any).row(indiceFila).data(datosActuales).draw();
                  (tablaProveedores as any).page(info.page).draw('page'); //me mantiene la pagina actual
                }
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
    });


    ////////////////////  Eliminar proveedor  //////////////////////
    function eliminarProveedor(e:Event){
      let idproveedor = (e.target as HTMLElement).parentElement!.id, info = (tablaProveedores as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idproveedor = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaProveedores as any).row((e.target as HTMLElement).closest('tr')).index();
      Swal.fire({
          customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
          icon: 'question',
          title: 'Desea eliminar el proveedor?',
          text: "El proveedor solo puede eliminarse si no tiene compras asociadas.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idproveedor);
                  try {
                      const url = "/admin/api/eliminarProveedor";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaProveedores as any).row(indiceFila).remove().draw(); 
                        (tablaProveedores as any).page(info.page).draw('page'); 
                        Swal.fire(resultado.exito[0], '', 'success')
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
      if (event.target === miDialogoProveedor || (event.target as HTMLInputElement).value === 'Salir') {
          miDialogoProveedor.close();
          document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateProveedor') as HTMLFormElement)?.reset();
    }

  }

})();
