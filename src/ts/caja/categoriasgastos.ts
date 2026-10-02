(()=>{
    if(document.querySelector('.categoriasgastos')){
        const btnCrearCategoriaGasto = document.querySelector('#btnCrearCategoriaGasto');
        const miDialogoCategoriaGasto = document.querySelector('#miDialogoCategoriaGasto') as any;
        let tablaCategoriasGastos:HTMLElement;

        tablaCategoriasGastos = ($('#tablaCategoriasGastos') as any).DataTable(configdatatables25reg);

        ///////////////BTN PARA CREAR CATEGORIA DE GASTO////////////////
        btnCrearCategoriaGasto?.addEventListener('click', (e):void=>{
            limpiarformdialog();
            document.querySelector('#modalCategoriaGasto')!.textContent = "Crear categoria de gasto";
            (document.querySelector('#btnEditarCrearCategoriaGasto') as HTMLInputElement).value = "Crear";
            miDialogoCategoriaGasto.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        });

        document.querySelector('#tablaCategoriasGastos')?.addEventListener('click', (e:Event)=>{
            var btn = e.target as HTMLElement;
            if(btn?.classList.contains('editarCategoriaGasto')||btn.parentElement?.classList.contains("editarCategoriaGasto"))editarCategoriaGasto(e);
            if(btn?.classList.contains('eliminarCategoriaGasto')||btn.parentElement?.classList.contains("eliminarCategoriaGasto"))eliminarCategoriaGasto(e);   
        });


        ///////////////EDITAR CATEGORIA DE GASTO////////////////
        function editarCategoriaGasto(e:Event){
            let idcategoriagasto = (e.target as HTMLElement).parentElement?.id!;
            if((e.target as HTMLElement)?.tagName === 'I')idcategoriagasto = (e.target as HTMLElement).parentElement?.parentElement?.id!;
            (document.querySelector('#formCrearUpdateCategoriaGasto') as HTMLFormElement).action = "/admin/caja/editarcategoriagasto";
            document.querySelector('#modalCategoriaGasto')!.textContent = "Actualizar categoria de gasto";
            (document.querySelector('#btnEditarCrearCategoriaGasto') as HTMLInputElement)!.value = "Actualizar";
            (document.querySelector('#idCategoriaGasto') as HTMLInputElement).value = idcategoriagasto;
            (document.querySelector('#nombre') as HTMLInputElement).value = ((e.target as HTMLElement).closest('.acciones-btns') as HTMLElement)?.dataset.categoriagasto!; 
            miDialogoCategoriaGasto.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        }


        function eliminarCategoriaGasto(e:Event):void{
            let form = (e.target as HTMLElement).parentElement!;
            if((e.target as HTMLElement).tagName === 'I')form = form.parentElement!;
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar la categoria de gasto?',
                text: "La categoria de gasto sera eliminado definitivamente.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed)(form as HTMLFormElement).submit();
            });
        }
        
        function limpiarformdialog(){
            (document.querySelector('#formCrearUpdateCategoriaGasto') as HTMLFormElement)?.reset();
            (document.querySelector('#formCrearUpdateCategoriaGasto') as HTMLFormElement).action = "/admin/caja/crear_categoriaGasto";
        }

        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
        if (f === miDialogoCategoriaGasto || (f as HTMLInputElement).value === 'Salir' || (f as HTMLInputElement).value === 'Actualizar') {
            miDialogoCategoriaGasto.close();
            document.removeEventListener("click", cerrarDialogoExterno);
        }
        }
    }

})();