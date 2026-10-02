(()=>{
    if(document.querySelector('.subproductos')){
      const crearSubProducto = document.querySelector('#crearSubProducto');
      const btnUploadExcel = document.querySelector('#btnUploadExcel');
      const miDialogoSubProducto = document.querySelector('#miDialogoSubProducto') as any;
      const miDialogoImportarExcel = document.querySelector('#miDialogoImportarExcel') as HTMLDialogElement;
      let indiceFila=0, control=0, tablaSubProductos:HTMLElement;
      
      type subproductsapi = {
        id:string,
        id_unidadmedida: string,
        unidadmedida: string,
        nombre: string,
        sku: string,
        proveedor: string,
        descripcion: string,
        medidas: string,
        color: string,
        uso:string,
        stock: string,
        stockminimo: string,
        precio_compra: string,
        fecha_ingreso: string,
      };
  
      let subproducts:subproductsapi[]=[], unsubproducto:subproductsapi;
  
      (async ()=>{
        try {
            const url = "/admin/api/allsubproducts"; //llamado a la API REST
            const respuesta = await fetch(url); 
            subproducts = await respuesta.json(); 
            console.log(subproducts);
        } catch (error) {
            console.log(error);
        }
      })();

      
      btnUploadExcel?.addEventListener('click', ()=>{
        miDialogoImportarExcel.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      });
  
      //////////////////  TABLA //////////////////////
      tablaSubProductos = ($('#tablaSubProductos') as any).DataTable(configdatatables);
  
      crearSubProducto?.addEventListener('click', (e):void=>{
        control = 0;
        limpiarformdialog();
        document.querySelector('#modalSubProducto')!.textContent = "Crear insumo";
        (document.querySelector('#btnEditarCrearSubProducto') as HTMLInputElement).value = "Crear";
        miDialogoSubProducto.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      });
  
      ///////////// EVENTOS A LA TABLA SUBPRODUCTOS /////////////
      document.querySelector('#tablaSubProductos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
        const target = e.target as HTMLElement;
        if(target?.classList.contains("editarSubProductos")||target.parentElement?.classList.contains("editarSubProductos"))editarSubProductos(e);
        if(target?.classList.contains("eliminarSubProductos")||target.parentElement?.classList.contains("eliminarSubProductos"))eliminarSubProductos(e);
      });
  
      function editarSubProductos(e:Event){
        let idsubproducto = (e.target as HTMLElement).parentElement?.id!;
        if((e.target as HTMLElement)?.tagName === 'I')idsubproducto = (e.target as HTMLElement).parentElement?.parentElement?.id!;
        control = 1;
        document.querySelector('#modalSubProducto')!.textContent = "Actualizar insumo";
        (document.querySelector('#btnEditarCrearSubProducto') as HTMLInputElement)!.value = "Actualizar";
        unsubproducto = subproducts.find(x=>x.id === idsubproducto)!;
        $('#unidadmedida').val(unsubproducto?.id_unidadmedida??'');
        (document.querySelector('#nombre')as HTMLInputElement).value = unsubproducto?.nombre!;
        (document.querySelector('#sku')as HTMLInputElement).value = unsubproducto?.sku!;
        (document.querySelector('#preciocompra')as HTMLInputElement).value = unsubproducto?.precio_compra??'';
        (document.querySelector('#stockminimo')as HTMLInputElement).value = unsubproducto?.stockminimo??'';
        
        indiceFila = (tablaSubProductos as any).row((e.target as HTMLElement).closest('tr')).index();
        miDialogoSubProducto.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }
  
      ////////////////////  Actualizar/Editar producto  //////////////////////
      document.querySelector('#formCrearUpdateSubProducto')?.addEventListener('submit', e=>{
        if(control){
          e.preventDefault();
          var info = (tablaSubProductos as any).page.info();
          
          (async ()=>{ 
            const datos = new FormData();
            datos.append('id', unsubproducto!.id);
            datos.append('id_unidadmedida', $('#unidadmedida').val()as string);
            datos.append('unidadmedida', $('#unidadmedida').find('option:selected').text());
            datos.append('nombre', $('#nombre').val()as string);
            datos.append('sku', $('#sku').val()as string);
            datos.append('precio_compra', $('#preciocompra').val()as string);
             datos.append('stockminimo', $('#stockminimo').val()as string);
            try {
                const url = "/admin/api/actualizarsubproducto";
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json(); 
                console.log(resultado); 
                if(resultado.exito !== undefined){
                  msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                  /// actualizar el arregle del subproducto ///
                  subproducts.forEach(a=>{if(a.id == unsubproducto.id)a = Object.assign(a, resultado.subproducto[0]);});
                  ///////// cambiar la fila completa, su contenido //////////
                  const datosActuales = (tablaSubProductos as any).row(indiceFila+=info.start).data();
                  /*NOMBRE*/datosActuales[1] ='<div class="w-80 whitespace-normal">'+$('#nombre').val()+'</div>';
                  /*proveedor*/datosActuales[2] = '';
                  /*SKU*/datosActuales[3] = $('#sku').val();
                  /*UNIDADMEDIDA*/datosActuales[4] = $('#unidadmedida option:selected').text();
                  /*PRECIOCOMPRA*/datosActuales[5] = '$'+Number($('#preciocompra').val())?.toLocaleString();
                  (tablaSubProductos as any).row(indiceFila).data(datosActuales).draw();
                  (tablaSubProductos as any).page(info.page).draw('page'); //me mantiene la pagina actual
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
  
      function eliminarSubProductos(e:Event){
        let idsubproducto = (e.target as HTMLElement).parentElement!.id, info = (tablaSubProductos as any).page.info();
        if((e.target as HTMLElement).tagName === 'I')idsubproducto = (e.target as HTMLElement).parentElement!.parentElement!.id;
        indiceFila = (tablaSubProductos as any).row((e.target as HTMLElement).closest('tr')).index();
        Swal.fire({
            customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
            icon: 'question',
            title: 'Desea eliminar el insumo?',
            text: "El insumo sera eliminado definitivamente.",
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result:any) => {
            if (result.isConfirmed) {
                (async ()=>{ 
                    const datos = new FormData();
                    datos.append('id', idsubproducto);
                    try {
                        const url = "/admin/api/eliminarSubProducto";
                        const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                        const resultado = await respuesta.json();  
                        if(resultado.exito !== undefined){
                          (tablaSubProductos as any).row(indiceFila).remove().draw(); 
                          (tablaSubProductos as any).page(info.page).draw('page');
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
        (document.querySelector('#formCrearUpdateSubProducto') as HTMLFormElement)?.reset();
        (document.querySelector('#formCrearUpdateSubProducto') as HTMLFormElement).action = "/admin/almacen/crear_subproducto";
      }
      function cerrarDialogoExterno(event:Event) {
        const f = event.target;
        if (f === miDialogoSubProducto || f === miDialogoImportarExcel || (f as HTMLInputElement).value === 'Cancelar' || (f as HTMLInputElement).value === 'salir' || (f as HTMLInputElement).value === 'Actualizar') {
            miDialogoSubProducto.close();
            miDialogoImportarExcel.close();
          document.removeEventListener("click", cerrarDialogoExterno);
        }
      }
    }
  
  })();