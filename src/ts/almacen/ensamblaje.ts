(()=>{
    
    if(document.querySelector('.ensamblaje')){
        const listaSubproductos = document.querySelector('.listaSubproductos');
        //const subproducto:HTMLSelectElement = document.querySelector('#subproducto')!;
        const selectUnidadMedida = document.querySelector('#unidadmedida')! as HTMLSelectElement;
        let amount = document.querySelector('#cantidad') as HTMLInputElement; //input cantidad

        type conversionunidadesapi = {
            id:string,
            idsubproducto: string,
            idunidadmedidabase: string,
            idunidadmedidadestino: string,
            nombreunidadbase: string,
            nombreunidaddestino: string,
            factorconversion: string,
            //idservicios:{idempleado:string, idservicio:string}[]
          };

        let allConversionUnidades:conversionunidadesapi[] = [], factorC = 0;

        (async ()=>{
            try {
                const url = "/admin/api/allConversionesUnidades"; //llamado a la API REST en el controlador almacencontrolador para treaer todas las conversiones de unidades
                const respuesta = await fetch(url); 
                allConversionUnidades = await respuesta.json(); 
            } catch (error) {
                console.log(error);
            }
        })();

        
        ($('#subproducto') as any).select2({
            maximumSelectionLength: 1,
        });


        ////// EVENTO AL SUBPRODUCTO PARA CARGAR LAS UNIDADES DE MEDIDA EQUIVALENTES ////// 
        $("#subproducto").on('change', (e)=>{
            const idsub = (e.target as HTMLOptionElement).value;
            const subund = allConversionUnidades.filter(x =>x.idsubproducto == idsub);
            mostrarSelectUnidades(subund);
        });

        /////////// CARGAR LAS UNIDADES DE MEDIDA EN EL SELECT unidadmedida  ////////////
        function mostrarSelectUnidades(subund:conversionunidadesapi[]):void{
            while(selectUnidadMedida?.firstChild)selectUnidadMedida.removeChild(selectUnidadMedida?.firstChild);
            subund.forEach(und =>{
              const option = document.createElement('option');
              option.textContent = und.nombreunidaddestino;
              option.value = und.id;  //und.id  =  es el id de la tabla conversionunidades
              //option.dataset.idunidadmedidabase = und.idunidadmedidabase;
              //option.dataset.nombreunidadbase = und.nombreunidadbase;
              selectUnidadMedida.appendChild(option);
            });
            calcularFactor();
        }

        ////////// EVENTO AL SELECT DE UNIDAD DE MEDIDA ///////////
        selectUnidadMedida.addEventListener('change', calcularFactor);

        (document.querySelector('#cantidad') as HTMLInputElement).addEventListener('input', (e:Event)=>{
            calcularFactor();
        });

        function calcularFactor():void{
            const idConversionUnidad:string|undefined = selectUnidadMedida.options[selectUnidadMedida.selectedIndex]?.value;
            const reg = allConversionUnidades.find(x=>x.id == idConversionUnidad);
            factorC = Number(reg?.factorconversion);
        }

        //////////////  EVENTO AL INPUT RENDIMIENTO ESTANDAR /////////////////
        document.querySelector('#rendimientoestandar')?.addEventListener('input', (e:Event)=>{
            const inputRendimiendoEstandar = (e.target as HTMLInputElement);
            (async ()=>{ 
                const datos = new FormData();
                datos.append('id', (document.querySelector('#idproducto') as HTMLInputElement).value);
                datos.append('rendimientoestandar', inputRendimiendoEstandar.value);
                try {
                    const url = "/admin/api/setrendimientoestandar";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                        inputRendimiendoEstandar.style.color = "#02db02";
                        inputRendimiendoEstandar.style.fontWeight = "500";
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
        
        });

        ///////////////// EVENTO AL FORMULARIO ASOCIAR SUBPRODUCTO //////////////////
        document.querySelector('#formAddSubproducto')?.addEventListener('submit', (e)=>{
            e.preventDefault();
            const subproducto = $('#subproducto').find('option:selected');
            const unidadmedida:string = $('#unidadmedida').find('option:selected').text();
            const tipoGrupo = (document.querySelector('#tipoGrupo') as HTMLSelectElement)?.value || '0';
            const marcadoDefecto = (document.querySelector('#marcadoDefecto') as HTMLSelectElement)?.value || '1';
            const permitirAumentar = (document.querySelector('#permitirAumentar') as HTMLSelectElement)?.value || '1';
            let AmountSubpx:number = factorC*Number(amount.value);
            (async ()=>{ 
                const datos = new FormData();
                datos.append('id_producto', (document.querySelector('#idproducto') as HTMLInputElement).value);
                datos.append('id_subproducto', $('#subproducto').val()as string);
                datos.append('cantidadsubproducto', AmountSubpx.toString());
                datos.append('costo', (Number(subproducto.data('costo'))*AmountSubpx)+'');
                datos.append('grupos_insumos', tipoGrupo);
                datos.append('seleccionado', marcadoDefecto);
                datos.append('permite_aumentar', permitirAumentar);
                
                try {
                    const url = "/admin/api/ensamblar";  //asocia el producto con el sub producto en la tabla productos_sub
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                      msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                        /////validar si es el mismo subproducto, y actualizar 
                        validarSubproducto(
                          $('#subproducto').val() as string,
                          unidadmedida,
                          subproducto.data('subproducto'),
                          tipoGrupo,
                          marcadoDefecto,
                          permitirAumentar
                        );
                        ////// reset form ///////
                        ($('#subproducto') as any).val([]).trigger('change');
                        //$(`#subproducto option[value="${$('#subproducto').val()}"]`).remove();
                        (document.querySelector('#formAddSubproducto') as HTMLFormElement)?.reset();
                    }else{
                      msjalertToast('error', '¡Error!', resultado.error[0]);
                    }
                } catch (error) {
                    console.log(error);
                }
            })();//cierre de async()
        });


        function validarSubproducto(idsubproducto:string, unidadmedida:string, subproducto:string, tipoGrupo:string, marcadoDefecto:string, permitirAumentar:string){
            const tipoGrupoText = (document.querySelector(`#tipoGrupo option[value="${tipoGrupo}"]`) as HTMLOptionElement)?.textContent?.trim() || tipoGrupo;
            const sub = document.querySelector(`.listaSubproductos div[id="${idsubproducto}"]`);
            const detailsHtml = `
                <div class="flex-1">
                    <div class="mb-3">
                        <p class="text-2xl font-semibold text-slate-800">
                            ${subproducto}
                        </p>

                        <p class="mt-2 text-indigo-600 font-semibold text-lg">
                            ${Number(amount.value).toFixed(2)} ${unidadmedida}
                        </p>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-4 text-lg">
                        <span class="rounded-md px-4 py-1.5 bg-slate-100 text-slate-700">
                            📦 Grupo: ${tipoGrupoText}
                        </span>

                        <span class="rounded-md px-4 py-1.5 font-medium ${
                            marcadoDefecto == '1'
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-rose-100 text-rose-700'
                        }">
                            ${
                                marcadoDefecto == '1'
                                    ? '✔ Seleccionado'
                                    : '✖ No seleccionado'
                            }
                        </span>

                        <span class="rounded-md px-4 py-1.5 font-medium ${
                            permitirAumentar == '1'
                                ? 'bg-indigo-100 text-indigo-700'
                                : 'bg-amber-100 text-amber-700'
                        }">
                            ${
                                permitirAumentar == '1'
                                    ? '➕ Permite aumentar'
                                    : '🚫 Cantidad fija'
                            }
                        </span>
                    </div>
                </div>
            `;
            if(sub){
                const button = sub.querySelector('button');
                sub.innerHTML = detailsHtml;
                if(button) sub.appendChild(button);
            }else{
                listaSubproductos?.insertAdjacentHTML('beforeend', `
                <div id="${idsubproducto}" class="mb-5 flex justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-all duration-200 items-center"  role="alert">
                    ${detailsHtml}
                    <button
                        type="button"
                        class="self-start mt-1 rounded-full p-2 text-slate-400 transition-all duration-200 hover:bg-red-100 hover:text-red-600">

                        <span
                            id="${idsubproducto}"
                            class="material-symbols-outlined">

                            close
                        </span>
                    </button>
                </div>`
                );
            }
        }


        listaSubproductos?.addEventListener('click', (e:Event)=>{
            const btn = (e.target as HTMLSpanElement);
            if(btn.tagName == "SPAN")desasociarSubproducto((document.querySelector('#idproducto') as HTMLInputElement).value, btn.id);
        });


        function desasociarSubproducto(idproducto:string, idsubproducto:string){
            const sub = document.querySelector(`.listaSubproductos div[id="${idsubproducto}"]`);
            (async ()=>{
                try {
                  const url = "/admin/api/desasociarsubproducto?idproducto="+idproducto+"&idsubproducto="+idsubproducto; //llamado a la API REST para desasociar subproducto del producto principal
                  const respuesta = await fetch(url); 
                  const resultado = await respuesta.json();
                  if(resultado.exito !== undefined){
                    sub?.remove();
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