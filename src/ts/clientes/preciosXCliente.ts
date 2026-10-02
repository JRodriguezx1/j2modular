(()=>{
    
    if(document.querySelector('.preciosXCliente')){
        const listaProductos = document.querySelector('.listaProductos');
        //const subproducto:HTMLSelectElement = document.querySelector('#subproducto')!;
        let precioPersonalizado = document.querySelector('#precioPersonalizado') as HTMLInputElement; //input cantidad

        

        
        ($('#productos') as any).select2({
            width: '100%',
            maximumSelectionLength: 1,
            dropdownCssClass: 'select2-theme-dropdown',
        });



        ///////////////// EVENTO AL FORMULARIO ASOCIAR PRODUCTO //////////////////
        document.querySelector('#formAddProducto')?.addEventListener('submit', (e)=>{
            e.preventDefault();
            const producto = $('#productos').find('option:selected');
            let valor:number = Number(precioPersonalizado.value);

            (async ()=>{ 
                const datos = new FormData();
                datos.append('idcliente', (document.querySelector('#idcliente') as HTMLInputElement).value);
                datos.append('idproducto', $('#productos').val()as string);
                datos.append('precioxcliente', valor.toString());
                try {
                    const url = "/admin/api/clientes/preciospersonalizados";  //asocia el precio personalizado al cliente
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                      msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                        /////validar si es el mismo subproducto, y actualizar 
                        validarProducto($('#productos').val()as string, producto.data('producto'));
                        ////// reset form ///////
                        ($('#productos') as any).val([]).trigger('change');
                        //$(`#subproducto option[value="${$('#subproducto').val()}"]`).remove();
                        (document.querySelector('#formAddProducto') as HTMLFormElement)?.reset();
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();//cierre de async()
        });


        function validarProducto(idproducto:string, producto:string){
            const pro = document.querySelector(`.listaProductos div[id="${idproducto}"]`);
            if(pro){
                pro.querySelector('strong')!.textContent = '$'+Number(precioPersonalizado.value).toLocaleString('es-CO');
            }else{
                listaProductos?.insertAdjacentHTML('beforeend', `
                <div id="${idproducto}" class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 shadow-sm" role="alert">
                    <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-box"></i></span>
                    <div class="min-w-0 flex-1">
                        <strong class="block text-xl font-black leading-tight text-indigo-600">$${Number(precioPersonalizado.value).toLocaleString('es-CO')}</strong>
                        <p class="mt-1 break-words text-lg font-bold leading-snug text-slate-900">${producto}</p>
                    </div>
                    <button type="button" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:border-rose-400 hover:bg-rose-100" title="Eliminar precio personalizado">
                        <span id="${idproducto}" class="material-symbols-outlined">cancel</span>
                    </button>
                </div>`);
            }
        }


        listaProductos?.addEventListener('click', (e:Event)=>{
            const btn = (e.target as HTMLSpanElement); //contiene el id del producto
            if(btn.tagName == "SPAN")eliminarPrecioPersonalizado((document.querySelector('#idcliente') as HTMLInputElement).value, btn.id);
        });


        function eliminarPrecioPersonalizado(idcliente:string, idproducto:string){
            const pro = document.querySelector(`.listaProductos div[id="${idproducto}"]`);
            (async ()=>{
                try {
                  const url = "/admin/api/clientes/eliminarPrecioPersonalizado?idcliente="+idcliente+"&idproducto="+idproducto; //llamado a la API REST para eliminar precio personlizado a cliente
                  const respuesta = await fetch(url); 
                  const resultado = await respuesta.json();
                  if(resultado.exito !== undefined){
                    pro?.remove();
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                  }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                  }
                } catch (error) {
                    console.log(error);
                }
            })();
        }

    }

})();
