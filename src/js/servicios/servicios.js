(function(){
    
    if(document.querySelector('.servicios')){
        //////////////////---- CATEGORIAS ----////////////////////
        const crearCategoria = document.querySelector('#crearCategoria');
        const dialogo = document.getElementById("miDialogoCategoria");
        const inconoscategoria = document.querySelectorAll('.icono-categoria');
        const formCrearUpdateCategoria = document.querySelector('#formCrearUpdateCategoria');
        const inputnombreCT = document.querySelector('#inputnombreCT');
        const inputimagenCT = document.querySelector('#inputimagenCT');
        let indiceFila=0, control=0, tablacategorias, categoriaId=0;

        //////////////////  TABLA //////////////////////
        tablacategorias = $('#tablacategorias').DataTable(configdatatables);  //configuracion del plugin datatable

        ////////evento a los iconos de las categorias ////////
        inconoscategoria.forEach(element => {
            element.addEventListener('click', (e)=>{
            const quitarresaltado = document.querySelector('.resaltar-icono');
            if(quitarresaltado)quitarresaltado.classList.remove('resaltar-icono');
            e.target.parentElement.classList.add('resaltar-icono');
            var icono = e.target.alt;
            if(e.target.tagName=='DIV')icono = e.target.firstElementChild.alt;
            document.querySelector('#inputimagenCT').value = icono+'.png';
            document.querySelector('#btnEnviarCrearCategoria').focus();
            })
        });

        //////////////////// ventana modal al crear una categoria  //////////////////////
        crearCategoria.addEventListener('click', (e)=>{
            control = 0;
            //e.preventDefault();
            const quitarresaltado = document.querySelector('.resaltar-icono');
            if(quitarresaltado)quitarresaltado.classList.remove('resaltar-icono');
            limpiarformdialog();
            dialogo.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
        });
        
        document.querySelector('#tablacategorias').addEventListener("click", (e)=>{ //evento click sobre toda la tabla
            if(e.target.classList.contains("stateCategoria"))cambiarStateCategoria(e);
            if(e.target.classList.contains("editarCategoria")||e.target.parentElement.classList.contains("editarCategoria"))editarCategoria(e);
            if(e.target.classList.contains("eliminarCategoria")||e.target.parentElement.classList.contains("eliminarCategoria"))eliminarCategoria(e);
        });

        function cambiarStateCategoria(e){
            const button=e.target, info = tablacategorias.page.info();
            indiceFila = tablacategorias.row(button.closest('tr')).index();
            (async ()=>{ 
                const datos = new FormData();
                datos.append('id', button.id);
                datos.append('estado', button.dataset.state==0?1:0);
                try {
                    const url = "/admin/api/updateStateCategoria";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();  
                    if(resultado){
                      const s1 = `<button id="${ button.id}" data-state="${button.dataset.state==0?1:0}" class="btn-md ${button.dataset.state==0?'btn-green':'btn-red'} stateCategoria">${button.dataset.state==0?'Activo':'Inactivo'}</button>`;
                      tablacategorias.cell(tablacategorias.row(indiceFila+=info.start), 5).data(s1).draw(); //se modifica solo la columna con la fila correspondiente, y destruye la que habai antes
                      tablacategorias.page(info.page).draw('page'); //me mantiene la pagina actual
                    } 
                } catch (error) {
                    console.log(error);
                }
              })();//cierre de async()
        }

        function editarCategoria(e){
            let categoria = e.target.parentElement;
            if(e.target.tagName === 'I')categoria = e.target.parentElement.parentElement;
            const quitarresaltado = document.querySelector('.resaltar-icono');
            if(quitarresaltado)quitarresaltado.classList.remove('resaltar-icono');
            Array.from(document.querySelectorAll('.icono-categoria')).some((element) =>{
                if(element.firstElementChild.alt == categoria.dataset.img.replace('.png', '')){
                    element.classList.add('resaltar-icono');
                    return true;
                }
            });
            categoriaId = categoria.id;
            inputnombreCT.value = categoria.dataset.nombre;
            inputimagenCT.value = categoria.dataset.img;
            control = 1;
            dialogo.showModal();
            indiceFila = tablacategorias.row(e.target.closest('tr')).index(); //obtener el indice de la fila de la tabla
            document.addEventListener("click", cerrarDialogoExterno);
        }

        formCrearUpdateCategoria.addEventListener('submit', e=>{
            e.preventDefault();
            if(!document.querySelector('.resaltar-icono')){
                msjAlert('error', 'Debe seleccionar una imagen para la categoria', document.querySelector('#divmsjalerta1')); //funcion definida en 1app.js
                borrarMsjAlert();  //Funcion definida en 1app.js
                return;
            }
            if(control){updateCategoriaApi();
            }else{ e.target.submit();}
        });

        function updateCategoriaApi(){
            const info = tablacategorias.page.info();
            (async ()=>{ 
                const datos = new FormData();
                datos.append('id', categoriaId);
                datos.append('nombre', inputnombreCT.value);
                datos.append('imagen', inputimagenCT.value); 
                try {
                    const url = "/admin/api/actualizarCategoria";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();  
                    if(resultado.exito !== undefined){
                      ///////// cambiar la fila completa, su contenido //////////
                      const datosActuales = tablacategorias.row(indiceFila+=info.start).data();
                      /*NOMBRE*/datosActuales[2] = inputnombreCT.value;
                      /*IMAGEN*/datosActuales[3] = `<div class="text-center"><img style="width: 40px;" src="/build/img/iconos_categoria/${inputimagenCT.value}"></div>`;
                      /*ACCIONES*/datosActuales[7] = `<div class="acciones-btns" id="${categoriaId}" data-nombre="${inputnombreCT.value}" data-img="${inputimagenCT.value}">
                                                        <button class="btn-md btn-turquoise editarCategoria"><i class="fa-solid fa-pen-to-square"></i></button><button class="btn-md btn-green editarServicios"><i class="fa-solid fa-grip-vertical"></i></button><button class="btn-md btn-red eliminarCategoria"><i class="fa-solid fa-trash-can"></i></button>
                                                      </div>`;
                      tablacategorias.row(indiceFila).data(datosActuales).draw();
                      tablacategorias.page(info.page).draw('page'); //me mantiene la pagina actual
                      dialogo.close(); //cerrar el modal
                      msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();//cierre de async()
        }

        function eliminarCategoria(e){
            let idcategoria = e.target.parentElement.id;
            if(e.target.tagName === 'I')idcategoria = e.target.parentElement.parentElement.id;
            const info = tablacategorias.page.info();
            indiceFila = tablacategorias.row(e.target.closest('tr')).index();
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar la categoria?',
                text: "Todos los servicios asociados a la categoria se eliminaran!",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    (async ()=>{ 
                        const datos = new FormData();
                        datos.append('id', idcategoria);
                        try {
                            const url = "/admin/api/eliminarCategoria";
                            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                            const resultado = await respuesta.json();  
                            if(resultado.exito !== undefined){
                              ///////// eliminar la fila //////////
                              tablacategorias.row(indiceFila+info.start).remove().draw(); //elimina la fila completa
                              tablacategorias.page(info.page).draw('page'); //me mantiene la pagina actual
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


        function cerrarDialogoExterno(event) {
            if (event.target === dialogo || event.target.value === 'cancelar') {
                dialogo.close();
                document.removeEventListener("click", cerrarDialogoExterno);
            }
        }
    
        function limpiarformdialog(){
            document.querySelector('#formCrearUpdateCategoria').reset();
        }
    }
    
    
    
})();