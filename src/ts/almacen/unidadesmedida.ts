(()=>{

    if(document.querySelector('.unidadesmedida')){
        const btnCrearUnidadMedida = document.querySelector('#btnCrearUnidadMedida');
        const miDialogoUnidadMedida = document.querySelector('#miDialogoUnidadMedida') as any;
        let tablaUnidadesMedida:HTMLElement;

        tablaUnidadesMedida = ($('#tablaUnidadesMedida') as any).DataTable(configdatatables);

        ///////////////BTN PARA CREAR UNIDAD DE MEDIDA////////////////
        btnCrearUnidadMedida?.addEventListener('click', (e):void=>{
            limpiarformdialog();
            document.querySelector('#modalUnidadMedida')!.textContent = "Crear unidad de medida";
            (document.querySelector('#btnEditarCrearUnidadMedida') as HTMLInputElement).value = "Crear";
            miDialogoUnidadMedida.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        });

        document.querySelector('#tablaUnidadesMedida')?.addEventListener('click', (e:Event)=>{
            var btn = e.target as HTMLElement;
            if(btn?.classList.contains('editarUnidadMedida')||btn.parentElement?.classList.contains("editarUnidadMedida"))editarUnidadMedida(e);
            if(btn?.classList.contains('eliminarUnidadMedida')||btn.parentElement?.classList.contains("eliminarUnidadMedida"))eliminarUnidadMedida(e);   
        });


        ///////////////EDITAR UNIDAD DE MEDIDA////////////////
        function editarUnidadMedida(e:Event){
            let idunidadmedida = (e.target as HTMLElement).parentElement?.id!;
            if((e.target as HTMLElement)?.tagName === 'I')idunidadmedida = (e.target as HTMLElement).parentElement?.parentElement?.id!;
            (document.querySelector('#formCrearUpdateUnidad') as HTMLFormElement).action = "/admin/almacen/editarunidademedida";
            document.querySelector('#modalUnidadMedida')!.textContent = "Actualizar unidad de medida";
            (document.querySelector('#btnEditarCrearUnidadMedida') as HTMLInputElement)!.value = "Actualizar";
            (document.querySelector('#idunidadmedida') as HTMLInputElement).value = idunidadmedida;
            (document.querySelector('#unidad') as HTMLInputElement).value = ((e.target as HTMLElement).closest('.acciones-btns') as HTMLElement)?.dataset.unidadmedida!; 
            miDialogoUnidadMedida.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        }


        function eliminarUnidadMedida(e:Event):void{
            let form = (e.target as HTMLElement).parentElement!;
            if((e.target as HTMLElement).tagName === 'I')form = form.parentElement!;
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar la unidad de medida?',
                text: "La unidad solo puede eliminarse si no esta siendo utilizada.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed)(form as HTMLFormElement).submit();
            });
        }

        /*document.querySelector('.eliminarUnidadMedida')?.addEventListener('submit', (e:Event)=>{
            console.log(55);
            e.preventDefault();
        });*/

        /*function eliminarUnidadMedida(e:Event):void{
            let idunidadmedida = (e.target as HTMLElement).parentElement?.id!;
            if((e.target as HTMLElement)?.tagName === 'I')idunidadmedida = (e.target as HTMLElement).parentElement?.parentElement?.id!;
            console.log(idunidadmedida);
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar la unidad de medida?',
                text: "La unidad de medida sera eliminado definitivamente.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {*/
                    /*(async ()=>{ 
                        const datos = new FormData();
                        datos.append('id', idproducto);
                        try {
                            const url = "/admin/api/eliminarProducto";
                            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                            const resultado = await respuesta.json();  
                            if(resultado.exito !== undefined){
                              (tablaProductos as any).row(indiceFila+info.start).remove().draw(); 
                              (tablaProductos as any).page(info.page).draw('page');
                              Swal.fire(resultado.exito[0], '', 'success') 
                            }else{
                                Swal.fire(resultado.error[0], '', 'error')
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();*///cierre de async()
                /*}
            });
        }*/
        
        function limpiarformdialog(){
            (document.querySelector('#formCrearUpdateUnidad') as HTMLFormElement)?.reset();
            (document.querySelector('#formCrearUpdateUnidad') as HTMLFormElement).action = "/admin/almacen/crear_unidadmedida";
        }

        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
        if (f === miDialogoUnidadMedida || (f as HTMLInputElement).value === 'Salir' || (f as HTMLInputElement).value === 'Actualizar') {
            miDialogoUnidadMedida.close();
            document.removeEventListener("click", cerrarDialogoExterno);
        }
        }
    }

})();
