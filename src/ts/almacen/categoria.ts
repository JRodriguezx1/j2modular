(()=>{
  if(document.querySelector('.categorias')){
    const crearCategoria = document.querySelector('#crearCategoria');
    const miDialogoCategoria = document.querySelector('#miDialogoCategoria') as any;
    let indiceFila=0, control=0, tablaCategorias:HTMLElement;

    type productsapi = {
      id:string,
      idcategoria: string,
      nombre: string,
      foto: string,
      marca: string,
      codigo: string,
      descripcion: string,
      peso: string,
      medidas: string,
      color: string,
      funcion: string,
      uso:string,
      fabricante: string,
      garantia: string,
      stock: string,
      categoria: string,
      precio_compra: string,
      precio_venta: string,
      fecha_ingreso: string,
      //idservicios:{idempleado:string, idservicio:string}[]
    };

    let products:productsapi[]=[], unproducto:productsapi;

    //////////////////  TABLA //////////////////////
    tablaCategorias = ($('#tablaCategorias') as any).DataTable(configdatatables25reg);

    crearCategoria?.addEventListener('click', (e):void=>{
      control = 0;
      limpiarformdialog();
      document.querySelector('#modalCategoria')!.textContent = "Crear categoria";
      (document.querySelector('#btnEditarCrearCategoria') as HTMLInputElement).value = "Crear";
      miDialogoCategoria.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    document.querySelector('#tablaCategorias')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarCategoria")||(e.target as HTMLElement).parentElement?.classList.contains("editarCategoria"))editarCategoria(e);
      if(target?.classList.contains("eliminarCategoria")||target.parentElement?.classList.contains("eliminarCategoria"))eliminarCategoria(e);
    });

    function editarCategoria(e:Event){
      control = 1;
      let idcategoria = (e.target as HTMLElement).parentElement?.id!;
      if((e.target as HTMLElement)?.tagName === 'I')idcategoria = (e.target as HTMLElement).parentElement?.parentElement?.id!;
      //(document.querySelector('#formCrearUpdateCategoria') as HTMLFormElement).action = "/admin/almacen/actualizar_categoria";
      document.querySelector('#modalCategoria')!.textContent = "Actualizar categoria";
      (document.querySelector('#btnEditarCrearCategoria') as HTMLInputElement)!.value = "Actualizar";
      (document.querySelector('#idcategoria') as HTMLInputElement).value = idcategoria;
      (document.querySelector('#categoria') as HTMLInputElement).value = ((e.target as HTMLElement).closest('.acciones-btns') as HTMLElement)?.dataset.categoria!; 
      indiceFila = (tablaCategorias as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoCategoria.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar categoria  //////////////////////
    document.querySelector('#formCrearUpdateCategoria')?.addEventListener('submit', e=>{
      if(control){
        e.preventDefault();
        var info = (tablaCategorias as any).page.info();
        (async ()=>{ 
          const datos = new FormData();
          datos.append('id', $('#idcategoria').val() as string);
          datos.append('nombre', $('#categoria').val()as string);
          try {
              const url = "/admin/api/actualizar_categoria";
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json(); 
              console.log(resultado); 
              if(resultado.exito !== undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                ///////// cambiar la fila completa, su contenido //////////
                const datosActuales = (tablaCategorias as any).row(indiceFila+=info.start).data();
                /*Nombre categoria*/datosActuales[2] = $('#categoria').val();
                /*data-categoria*/datosActuales[4] = `<div class="acciones-btns" id="${$('#idcategoria').val()}" data-categoria="${$('#categoria').val()}"><button class="btn-md btn-turquoise editarCategoria"><i class="fa-solid fa-pen-to-square"></i></button><button class="btn-md btn-red eliminarCategoria"><i class="fa-solid fa-trash-can"></i></button></div>`;//$('#categoria').val();
                
                (tablaCategorias as any).row(indiceFila).data(datosActuales).draw();
                (tablaCategorias as any).page(info.page).draw('page'); //me mantiene la pagina actual
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
              document.addEventListener("click", cerrarDialogoExterno);
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
      } //fin if(control)
    });

    function eliminarCategoria(e:Event){
      let idcategoria = (e.target as HTMLElement).parentElement!.id, info = (tablaCategorias as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idcategoria = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaCategorias as any).row((e.target as HTMLElement).closest('tr')).index();
      Swal.fire({
          customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
          icon: 'question',
          title: 'Desea eliminar la categoria?',
          text: "La categoria solo puede eliminarse si no tiene productos asociados.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idcategoria);
                  try {
                      const url = "/admin/api/eliminarCategoria";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();
                      if(resultado.length || resultado.exito !== undefined){
                        (tablaCategorias as any).row(indiceFila+info.start).remove().draw(); 
                        (tablaCategorias as any).page(info.page).draw('page'); 
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


    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateCategoria') as HTMLFormElement)?.reset();
      (document.querySelector('#formCrearUpdateCategoria') as HTMLFormElement).action = "/admin/almacen/crear_categoria";
      (document.querySelector('#idcategoria') as HTMLInputElement).value = '0';
    }

    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoCategoria || (event.target as HTMLInputElement).value === 'Salir' || (event.target as HTMLInputElement).value === 'Actualizar') {
        miDialogoCategoria.close();
        document.removeEventListener("click", cerrarDialogoExterno);
      }
    }
  }

})();
