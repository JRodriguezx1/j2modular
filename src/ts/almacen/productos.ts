(()=>{
  if(document.querySelector('.productos')){
    const crearProducto = document.querySelector('#crearProducto');
    const btnUploadExcel = document.querySelector('#btnUploadExcel');
    const miDialogoProducto = document.querySelector('#miDialogoProducto') as any;
    const miDialogoImportarExcel = document.querySelector('#miDialogoImportarExcel') as any;
    const btnXCerrarModalproducto = document.querySelector('#btnXCerrarModalproducto') as HTMLButtonElement;
    const inputupImage = document.querySelector('#upImage') as HTMLInputElement;  //input para cargar el archivo imagen
    const btncustomUpImage = document.querySelector('#customUpImage');
    const imginputfile = document.querySelector('#imginputfile') as HTMLImageElement;  //img
    const tipoproducto = document.querySelector('#tipoproducto') as HTMLSelectElement;
    const contentnuevosprecios = document.querySelector('#contentnuevosprecios') as HTMLElement;
    let indiceFila=0, control=0, tablaProductos:HTMLElement;
    

    type productsapi = {
      id:string,
      idcategoria: string,
      idunidadmedida: string,
      nombre: string,
      foto: string,
      impuesto: string,
      marca: string,
      tipoproducto: string,
      tipoproduccion: string,
      sku: string,
      unidadmedida: string;
      descripcion: string,
      peso: string,
      medidas: string,
      color: string,
      funcion: string,
      uso:string,
      fabricante: string,
      garantia: string,
      stock: string,
      stockminimo: string,
      categoria: string,
      precio_compra: string,
      precio_venta: string,
      percentcomision: string,
      prioridadcomision: string,
      fecha_ingreso: string,
      preciosadicionales: {id:string, idproductoid:string, precio:string, estado:string, created_at:string}[]
      //idservicios:{idempleado:string, idservicio:string}[]
    };

    let products:productsapi[]=[], unproducto:productsapi;

    (async ()=>{
      try {
          const url = "/admin/api/allproducts"; //llamado a la API REST y se trae todos los productos
          const respuesta = await fetch(url); 
          products = await respuesta.json(); 
          console.log(products);
      } catch (error) {
          console.log(error);
      }
    })();

    const divAlert = document.querySelector('.divmsjalerta0') as HTMLElement;
    if(divAlert)borrarMsjAlert(divAlert);

    ///////// Habilita el select precio de compra, y deshabilita el select de tipo de produccion si es producto simple, si es compuesto lo deshabilita y habilita el select de tipo de produccion
    tipoproducto?.addEventListener('change', (e:Event)=>{
      const targetDom = e.target as HTMLSelectElement;
      const preciocompra = document.querySelector('.preciocompra') as HTMLElement;
      const stock = document.querySelector('.stock') as HTMLElement;
      const habtipoproduccion = document.querySelector('.habtipoproduccion') as HTMLElement;
      if(targetDom.value == '0'){  //  0 = simple
        stock.style.display = 'flex';
        preciocompra.style.display = 'flex';
        habtipoproduccion.style.display = "none";
        document.querySelector('#preciocompra')?.setAttribute("required", "");
        document.querySelector('#tipoproduccion')?.removeAttribute("required");
      }
      else{
        stock.style.display = 'none';
        preciocompra.style.display = 'none';
        habtipoproduccion.style.display = "flex";
        document.querySelector('#preciocompra')?.removeAttribute("required");
        document.querySelector('#tipoproduccion')?.setAttribute("required", "");
      }
    });


    btnXCerrarModalproducto.addEventListener('click', (e)=>{
        miDialogoProducto.close();
        document.removeEventListener("click", cerrarDialogoExterno);
    });


    //AÑADOR INPUTS PARA PRECIOS PERSONALIZADOS
    const btnAddNewPrice = document.querySelector('#btnAddNewPrice') as HTMLButtonElement;
    if(btnAddNewPrice){
        let i=1;
        btnAddNewPrice.addEventListener('click', (e:Event)=>{
          let clone = (e.target as HTMLButtonElement).previousElementSibling?.cloneNode(true) as HTMLInputElement;  
          //console.log(clone.children[1].name = `niveles[nombrenivel${++i}]`);
          clone.removeAttribute('id');
          clone.required = false;
          clone.value='';
          clone.name = `nuevosprecios[]`;
          clone.classList.add('nuevosprecios');
          contentnuevosprecios.appendChild(clone);
          console.log(clone);
        });
    }


    btnUploadExcel?.addEventListener('click', ()=>{
      miDialogoImportarExcel.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });


    //////////////////  TABLA //////////////////////
    tablaProductos = ($('#tablaProductos') as any).DataTable(configdatatables);

    //btn para crear producto
    crearProducto?.addEventListener('click', (e):void=>{
      control = 0;
      limpiarformdialog();
      while(contentnuevosprecios.firstChild)contentnuevosprecios.removeChild(contentnuevosprecios.firstChild);
      document.querySelector('#modalProducto')!.textContent = "Crear producto";
      (document.querySelector('#btnEditarCrearProducto') as HTMLInputElement).value = "Crear";
      (document.querySelector('.stock')as HTMLInputElement).style.display = "block";
      (document.querySelector('.preciocompra')as HTMLInputElement).style.display = "block";
      miDialogoProducto.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    //evento a la tabla
    document.querySelector('#tablaProductos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if((e.target as HTMLElement)?.classList.contains("editarProductos")||(e.target as HTMLElement).parentElement?.classList.contains("editarProductos"))editarProductos(e);
      if(target?.classList.contains("bloquearProductos")||target.parentElement?.classList.contains("bloquearProductos"))bloquearProductos(e);
      if(target?.classList.contains("eliminarProductos")||target.parentElement?.classList.contains("eliminarProductos"))eliminarProductos(e);
    });

    function editarProductos(e:Event){
      let idproducto = (e.target as HTMLElement).parentElement?.id!;
      if((e.target as HTMLElement)?.tagName === 'I')idproducto = (e.target as HTMLElement).parentElement?.parentElement?.id!;
      control = 1;
      document.querySelector('#modalProducto')!.textContent = "Actualizar producto";
      (document.querySelector('#btnEditarCrearProducto') as HTMLInputElement)!.value = "Actualizar";
      unproducto = products.find(x=>x.id === idproducto)!; //obtengo el producto.
      $('#categoria').val(unproducto?.idcategoria??'');
      (document.querySelector('#nombre')as HTMLInputElement).value = unproducto?.nombre!;
      $('#tipoproducto').val(unproducto?.tipoproducto??'0');  //0 = simple,  1 = compuesto
      $('#idunidadmedida').val(unproducto?.idunidadmedida??'1');
      (document.querySelector('.stock')as HTMLInputElement).style.display = "block";
      (document.querySelector('.preciocompra')as HTMLInputElement).style.display = "block";
      (document.querySelector('.habtipoproduccion') as HTMLElement).style.display = "none";
      if(unproducto?.tipoproducto == '1'){
        (document.querySelector('.stock')as HTMLInputElement).style.display = "none";
        (document.querySelector('.preciocompra')as HTMLInputElement).style.display = "none";
        (document.querySelector('.habtipoproduccion') as HTMLElement).style.display = "block";
        $('#tipoproduccion').val(unproducto.tipoproduccion);
      }
      (document.querySelector('#stock')as HTMLInputElement).value = unproducto?.stock??'';
      (document.querySelector('#preciocompra')as HTMLInputElement).value = unproducto?.precio_compra??'';
      (document.querySelector('#precioventa')as HTMLInputElement).value = unproducto?.precio_venta??'';
      (document.querySelector('#precioventa')as HTMLInputElement).dataset.idproductoid = unproducto.id;
      (document.querySelector('#percentcomision')as HTMLInputElement).value = unproducto.percentcomision;
      (document.querySelector('#prioridadcomision')as HTMLInputElement).value = unproducto.prioridadcomision;
      imprimirpreciosadicionales(unproducto.preciosadicionales);
      (document.querySelector('#sku')as HTMLInputElement).value = unproducto?.sku??'';
      (document.querySelector('#impuesto')as HTMLInputElement).value = unproducto?.impuesto??'';
      (document.querySelector('#stockminimo')as HTMLInputElement).value = unproducto?.stockminimo??'';
      imginputfile.src = "/build/img/"+unproducto?.foto;
      indiceFila = (tablaProductos as any).row((e.target as HTMLElement).closest('tr')).index();
      miDialogoProducto.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    function imprimirpreciosadicionales(preciosadicionales:{id:string, idproductoid:string, precio:string, estado:string, created_at:string}[]){
      while(contentnuevosprecios.firstChild)contentnuevosprecios.removeChild(contentnuevosprecios.firstChild);
      preciosadicionales.forEach(z =>{
        contentnuevosprecios.insertAdjacentHTML('afterbegin', `<input 
          data-id="${z.id}"
          class="nuevosprecios bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white h-14 text-xl focus:outline-none focus:ring-1"
          type="number" 
          min="0" 
          step="0.01"
          placeholder="Precio de venta incluido el impuesto" 
          name="nuevosprecios[]" 
          value="${z.precio}" 
          >`);
      });
    }

    ////////////////////  Actualizar/Editar producto  //////////////////////
    document.querySelector('#formCrearUpdateProducto')?.addEventListener('submit', e=>{

      let w = document.querySelectorAll<HTMLInputElement>('.nuevosprecios');
      //console.log(Array.from(w).map(input=>({id: input.dataset.id, precio: parseFloat(input.value) })).filter(x=>x.precio>0));
      if(control){
        e.preventDefault();
        var imgFile:(string|File), info = (tablaProductos as any).page.info();
        imgFile=unproducto.foto;
        if(!imginputfile.src.includes(unproducto.foto)){ //se lee la etiqueta img para cambio de imagen
          imgFile = ((e.target as HTMLFormElement).elements.namedItem("upImage") as HTMLInputElement).files?.[0]!; //obtengo el archivo
          if(imgFile){
            if(imgFile.type!=="image/png"&&imgFile.type!=="image/jpeg"){
              msjAlert('error', 'No es un formato valido para la foto', (document.querySelector('#divmsjalerta1') as HTMLElement));
              return;
            }
            if(imgFile.size>31000000){ //si es mayor a 31MB
              msjAlert('error', 'La imagen no debe superar los 500KB', document.querySelector('#divmsjalerta1')!);
              return;
            }
          }
        }
        (async ()=>{
          const datos = new FormData();
          datos.append('id', unproducto!.id);
          datos.append('idcategoria', $('#categoria').val()as string);
          datos.append('nombre', $('#nombre').val()as string);
          datos.append('foto', imgFile); //en el backend no se lee con $_POST, se lee con $_FILES
          datos.append('tipoproducto', $('#tipoproducto').val()as string);  //0=simple o 1=compuesto
          datos.append('idunidadmedida', $('#idunidadmedida').val()as string);
          datos.append('unidadmedida', $('#idunidadmedida option:selected').text());
          datos.append('stock', $('#stock').val()as string);
          datos.append('precio_compra', $('#preciocompra').val()as string);
          datos.append('precio_venta', $('#precioventa').val()as string);
          datos.append('nuevosprecios', JSON.stringify(Array.from(w).map(input=>({id: input.dataset.id, idproductoid: input.dataset.idproductoid, precio: parseFloat(input.value), estado:1 })).filter(x=>x.precio>=0)));
          datos.append('idprecionsadicionales', JSON.stringify(Array.from(w).filter(input => input.dataset.id && parseFloat(input.value) > 0).map( input=>input.dataset.id ))); //solo los ids de la tabla precios_personalizados
          datos.append('sku', $('#sku').val()as string);
          datos.append('tipoproduccion', ($('#tipoproduccion').val()as string)==null?'0':($('#tipoproduccion').val()as string));
          datos.append('impuesto', $('#impuesto').val()as string);
          datos.append('stockminimo', $('#stockminimo').val()as string);
          datos.append('percentcomision', $('#percentcomision').val()as string);
          datos.append('prioridadcomision', $('#prioridadcomision').val() as string);
          try {
              const url = "/admin/api/actualizarproducto";
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json(); 
              console.log(resultado); 
              if(resultado.exito !== undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                /// actualizar el arregle del producto ///
                products.forEach(a=>{if(a.id == unproducto.id)a = Object.assign(a, resultado.producto[0]);});
                ///////// cambiar la fila completa, su contenido //////////
                const datosActuales = (tablaProductos as any).row(indiceFila).data();
                /*img*/datosActuales[1] = `<div class="text-center"><img class="inline" style="width: 50px;" src="/build/img/${resultado.producto[0].foto}" alt=""></div>`;
                /*NOMBRE*/datosActuales[2] ='<div class="w-80 whitespace-normal">'+$('#nombre').val()+'</div>';
                /*CATEGORIA*/datosActuales[3] = $('#categoria option:selected').text();
                /*MARCA*/datosActuales[4] = '';
                /*CODIGO*/datosActuales[5] = $('#sku').val();
                /*PRECIOVENTA*/datosActuales[6] = $('#precioventa').val();
                
                (tablaProductos as any).row(indiceFila).data(datosActuales).draw();
                (tablaProductos as any).page(info.page).draw('page'); //me mantiene la pagina actual
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
              miDialogoProducto.close();
              document.removeEventListener("click", cerrarDialogoExterno);
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
      } //fin if(control)
    });


    function bloquearProductos(e:Event){
      let reg = (e.target as HTMLElement).parentElement, info = (tablaProductos as any).page.info();
      if((e.target as HTMLElement).tagName === 'SPAN')reg = (e.target as HTMLElement).parentElement!.parentElement;
      indiceFila = (tablaProductos as any).row((e.target as HTMLElement).closest('tr')).index();
      (async ()=>{

          const datos = new FormData();
          datos.append('id', reg!.id);
          try {
              const url = "/admin/api/cambiarestadoproducto";
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                
                const s1 = `<div class="acciones-btns my-[0.7rem]" id="${reg!.id}">
                              ${resultado.tipoproducto[0] == 1?`<a class="btn-xs btn-blue" title="Agregar Materia Prima" href="/admin/almacen/componer?id=${reg!.id}"><i class="fa-solid fa-subscript text-[17px] leading-none"></i></a>`:''}
                              <button class="btn-xs btn-lima" title="Más opciones"><i class="fa-solid fa-circle-plus text-[17px] leading-none"></i></button>
                              <button class="btn-xs btn-turquoise editarProductos" title="Actualizar Producto"><i class="fa-solid fa-pen-to-square text-[17px] leading-none"></i></button>
                              <button class="btn-xs ${resultado.estado[0]==1?'btn-light':'btn-orange'} bloquearProductos" title="Bloquear Producto"><span class="material-symbols-outlined text-[18px] leading-none">hide_source</span></button>
                              <button class="btn-xs btn-red eliminarProductos" title="Eliminar Producto"><i class="fa-solid fa-trash-can text-[17px] leading-none"></i></button>
                            </div>`;
                            
                (tablaProductos as any).cell((tablaProductos as any).row(indiceFila), 7).data(s1).draw(); //se modifica solo la columna con la fila correspondiente, y destruye la que habai antes
                (tablaProductos as any).page(info.page).draw('page'); //me mantiene la pagina actual

                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              }else{
                  Swal.fire(resultado.error[0], '', 'error')
              }
          } catch (error) {
              console.log(error);
          }
      })();
    }

    function eliminarProductos(e:Event){
      let idproducto = (e.target as HTMLElement).parentElement!.id, info = (tablaProductos as any).page.info();
      if((e.target as HTMLElement).tagName === 'I')idproducto = (e.target as HTMLElement).parentElement!.parentElement!.id;
      indiceFila = (tablaProductos as any).row((e.target as HTMLElement).closest('tr')).index();
      Swal.fire({
          customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
          icon: 'question',
          title: 'Desea eliminar el prducto?',
          text: "El prducto sera eliminado definitivamente de todas las SEDES.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idproducto);
                  try {
                      const url = "/admin/api/eliminarProducto";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaProductos as any).row(indiceFila).remove().draw(); 
                        (tablaProductos as any).page(info.page).draw('page');
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

    //////////////////// Cargar imagen como preview  //////////////////////
    btncustomUpImage?.addEventListener('click', ()=>inputupImage?.click());
    inputupImage?.addEventListener('change', function(){
      const file = this.files?.[0];
      if(file){
        const reader = new FileReader();
        reader.onload = function(){
          const resrult = reader.result;
          if(typeof resrult == "string")imginputfile.src = resrult;
        } 
        reader.readAsDataURL(file);
      }
    });

    function limpiarformdialog():void{
      (document.querySelector('#formCrearUpdateProducto') as HTMLFormElement)?.reset();
      imginputfile.src = '';
    }
    function cerrarDialogoExterno(event:Event) {
      if (event.target === miDialogoImportarExcel || (event.target as HTMLInputElement).value === 'salir' || (event.target as HTMLInputElement).value === 'Cancelar') {
        miDialogoProducto.close();
        miDialogoImportarExcel.close();
        document.removeEventListener("click", cerrarDialogoExterno);
      }
    }
  }

})();