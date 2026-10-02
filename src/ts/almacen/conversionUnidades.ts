(()=>{
    if(document.querySelector('.conversionUnidades')){
      const miDialogoConversionUnidad = document.querySelector('#miDialogoConversionUnidad') as any;
      const unidadesMedidas = document.querySelector('#unidadesMedidas') as HTMLSelectElement;
      const contenedor = document.querySelector('#contenedor') as HTMLDivElement;
      const factor = document.querySelector('#equivalente') as HTMLInputElement;
      let tablaSubProductos:HTMLElement;

      document.addEventListener("click", cerrarDialogoExterno);
      
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

      interface IunidadesMedida {
        id:string,
        nombre:string
      }
  
      type conversionunidadesapi = {
        id:string,
        idproducto: string,
        idsubproducto: string,
        idunidadmedidabase: string,
        idunidadmedidadestino: string,
        nombreunidadbase: string,
        nombreunidaddestino: string,
        factorconversion: string,
      };

      let subproducts:subproductsapi[]=[], unsubproducto:subproductsapi, allUnidadesMedida:IunidadesMedida[]=[], allConversionUnidades:conversionunidadesapi[] = [];
  
      (async ()=>{
        try {
            const url = "/admin/api/allsubproducts"; //llamado a la API REST
            const respuesta = await fetch(url); 
            subproducts = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();


      (async ()=>{
        try {
            const url = "/admin/api/almacen/allUnidadesMedida"; //llamado a la API REST
            const respuesta = await fetch(url); 
            allUnidadesMedida = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();


      (async ()=>{
        try {
            const url = "/admin/api/allConversionesUnidades";
            const respuesta = await fetch(url); 
            allConversionUnidades = await respuesta.json(); 
        } catch (error) {
            console.log(error);
        }
      })();
  
      //////////////////  TABLA //////////////////////
      tablaSubProductos = ($('#tablaSubProductos') as any).DataTable(configdatatables);
  
  
      ///////////// EVENTOS A LA TABLA SUBPRODUCTOS /////////////
      document.querySelector('#tablaSubProductos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
        const target = e.target as HTMLElement;
        if(target?.classList.contains("editarUnidadMedida")||target.parentElement?.classList.contains("editarUnidadMedida"))editarUnidadMedida(e);
      });
  
      function editarUnidadMedida(e:Event){
        while(unidadesMedidas.firstChild)unidadesMedidas.removeChild(unidadesMedidas.firstChild);
        let idsubproducto = (e.target as HTMLElement).parentElement?.id!;
        if((e.target as HTMLElement)?.tagName === 'I')idsubproducto = (e.target as HTMLElement).parentElement?.parentElement?.id!;
        unsubproducto = subproducts.find(x=>x.id === idsubproducto)!;
  
        $('#nombreInsumo').text(unsubproducto?.nombre??'');
        $('#unidadMedidaBase').text(unsubproducto?.unidadmedida??'');
        $('#unidadMedidaBase2').val(unsubproducto?.unidadmedida??'');

        const unidadesDestino = allConversionUnidades.filter(c => c.idsubproducto === unsubproducto.id).map(c => c.idunidadmedidadestino);
        allUnidadesMedida.forEach(u=>{
          if(!unidadesDestino.includes(u.id)){
            const option = document.createElement('option');
            option.value = u.id;
            option.textContent = u.nombre;
            unidadesMedidas.appendChild(option);
          }
        });
        imprimirConversionesUnidades(allConversionUnidades);
        miDialogoConversionUnidad.showModal();
      }


      function imprimirConversionesUnidades(allConversionUnidades:conversionunidadesapi[]){
        const subAllUnidades = allConversionUnidades.filter(z=>z.idsubproducto==unsubproducto.id);
        contenedor.innerHTML = '';
        subAllUnidades.forEach(conversion => {
            contenedor.innerHTML += `
                <div class="flex items-center justify-between p-3 mb-3 bg-white border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-6 text-lg">
                        <span>
                            <span class="font-medium text-slate-500">Base:</span>
                            <span class="text-slate-900 font-medium">${conversion.factorconversion} ${conversion.nombreunidadbase}</span>
                        </span>
                        <span>
                            <span class="font-medium text-slate-500">Equivale A:</span>
                            <span class="text-slate-900 font-medium">1 ${conversion.nombreunidaddestino}</span>
                        </span>
                    </div>
                    <button
                        type="button"
                        data-id="${conversion.id}"
                        data-idunidadmedidadestino="${conversion.idunidadmedidadestino}"
                        data-nombreunidaddestino="${conversion.nombreunidaddestino}"
                        class="btn-eliminar px-3 py-1.5 text-base font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 transition-colors"
                    >
                        Eliminar
                    </button>
                </div>
            `;
        });
      }


      //ELIMINAR CONVERSION DE UNIDAD
      contenedor.addEventListener('click', (e) => {
          const target = e.target as HTMLElement;
          if(target.classList.contains('btn-eliminar')){
              const id = target.dataset.id; //id de la tabla conversionunidad
              const idunidadmedidadestino = target.dataset.idunidadmedidadestino;
              const nombreunidaddestino = target.dataset.nombreunidaddestino;
              (async ()=>{
                try {
                  const url = "/admin/api/almacen/eliminarConversionUnidad?id="+id; //llamado a la API REST para eliminar unidad de conversion
                  const respuesta = await fetch(url); 
                  const resultado = await respuesta.json();
                  if(resultado.exito !== undefined){
                    msjAlert('exito', resultado.exito, document.querySelector('#divmsjalerta1')!);
                    allConversionUnidades = allConversionUnidades.filter(conversion => conversion.id != id);
                    imprimirConversionesUnidades(allConversionUnidades);
                    //agregar la unidad de medida al select de unidades de medida
                    const option = document.createElement('option');
                    if(idunidadmedidadestino&&nombreunidaddestino){
                      option.value = idunidadmedidadestino;
                      option.textContent = nombreunidaddestino;
                      unidadesMedidas.appendChild(option);
                    }
                  }else{
                    msjAlert('error', resultado.error, document.querySelector('#divmsjalerta1')!);
                  }
                } catch (error) {
                    console.log(error);
                }
              })();       
          }
      });

  
      ////////////////////  Actualizar conversion unidad de medida  //////////////////////
      document.querySelector('#formConversionUnidad')?.addEventListener('submit', e=>{
          e.preventDefault();
          (async ()=>{ 
            const datos = new FormData();
            datos.append('idsubproducto', unsubproducto.id);
            datos.append('idunidadmedidabase', unsubproducto.id_unidadmedida);
            datos.append('idunidadmedidadestino', $('#unidadesMedidas').val()as string);
            datos.append('nombreunidadbase', unsubproducto.unidadmedida);
            datos.append('nombreunidaddestino', $('#unidadesMedidas option:selected').text());
             datos.append('factorconversion', factor.value??'0');
            try {
                const url = "/admin/api/almacen/crearNuevaConversionUnidad";
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json(); 
                if(resultado.exito !== undefined){
                  msjAlert('exito', resultado.exito[0], document.querySelector('#divmsjalerta1')!);
                  const nuevaConversion:conversionunidadesapi = resultado.newCv;
                  allConversionUnidades.push(nuevaConversion);
                  imprimirConversionesUnidades(allConversionUnidades);
                  //eliminar del select de unidades de medida el option
                  const option = unidadesMedidas.querySelector(`option[value="${nuevaConversion.idunidadmedidadestino}"]`);
                  option?.remove();
                  factor.value = '';
                }else{
                  msjalertToast('error', '¡Error!', resultado.error[0]);
                }
            } catch (error) {
                console.log(error);
            }
          })();

      });

      
      function cerrarDialogoExterno(event:Event) {
        if (event.target === miDialogoConversionUnidad || (event.target as HTMLInputElement).value === 'Salir' || (event.target as HTMLInputElement).value === 'Actualizar')
            miDialogoConversionUnidad.close();
      }
    }
  
  })();