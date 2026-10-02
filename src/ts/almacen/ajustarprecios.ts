(()=>{
    if(document.querySelector('.cambioPrecios')){
        let tablaAjustarPrecios:HTMLElement;

        type conversionunidadesapi = {
            id:string,
            idproducto: string,
            idsubproducto: string,
            idunidadmedidabase: string,
            idunidadmedidadestino: string,
            nombreunidadbase: string,
            nombreunidaddestino: string,
            factorconversion: string,
            //idservicios:{idempleado:string, idservicio:string}[]
          };

        let allConversionUnidades:conversionunidadesapi[] = [];
        (async ()=>{
            try {
                const url = "/admin/api/allConversionesUnidades"; //llamado a la API REST en el controlador almacencontrolador para treaer todas las conversiones de unidades
                const respuesta = await fetch(url); 
                allConversionUnidades = await respuesta.json();
                console.log(allConversionUnidades);
                pirntUnidades();
                tablaAjustarPrecios = ($('#tablaAjustarPrecios') as any).DataTable(configdatatablesajustarcostos);
            } catch (error) {
                console.log(error);
            }
        })();

        //tablaAjustarPrecios = ($('#tablaAjustarPrecios') as any).DataTable(configdatatables);
        //indiceFila = (tablaAjustarPrecios as any).row(1).data();

        function pirntUnidades():void{
            allConversionUnidades.forEach(subUnd =>{
                let select = document.querySelector(`tr[data-idproducto="${subUnd.idproducto}"]`)?.children[5].children[0];
                const option = document.createElement('option');
                option.textContent = subUnd.nombreunidaddestino;
                option.dataset.factor = subUnd.factorconversion;
                select?.appendChild(option);
            });
        }
 

        /*const fila = (tablaAjustarPrecios as any).row((idx:number, data:any, node:HTMLElement)=>{
            return node.dataset.idproducto === subUnd.idsubproducto;
        }).data();*/
        //console.log(fila[4]);
      /*fila[4] = `<select class="formulario__select" required>
                        <option value="" disabled selected>-Seleccionar-</option>
                        <option value=""> Unidad </option>
                    </select>`;
        (tablaAjustarPrecios as any).row(2).data(fila).draw();*/

        //tablaAjustarPrecios = ($('#tablaAjustarPrecios') as any).DataTable(configdatatables);
        
        document.querySelector('#tablaAjustarPrecios')?.addEventListener('focusout', (e:Event)=>{
            var input = e.target as HTMLInputElement;
            let idelemento:string = '';
            if(input.classList.contains('inputAjustarPrecio')){
                
                var tr = input.closest('tr');
                const selectUnidad = tr?.querySelector('.formulario__select') as HTMLSelectElement;
                const factor:number = Number(selectUnidad.options[selectUnidad.selectedIndex].dataset.factor);
                
                var trx = input.closest('.fila') as HTMLElement;  //fila padre
                if(!trx)trx = tr?.previousSibling as HTMLElement;

                if(trx.classList.contains('producto')){
                    idelemento = trx.dataset.idproducto!;
                }

                ////////////// enviar datos por api para actualizar precios /////////////
                if(Number(input.value) != Number(trx.dataset?.precio||0))
                actualizarPreciosVenta(idelemento, factor, input);
            }
        });


        function actualizarPreciosVenta(idelemento:string, factor:number, input:HTMLInputElement){
            if(input.value=='')input.value = '0';
            const precio = Number(input.value)/factor;
            (async ()=>{ 
                const datos = new FormData();
                datos.append('idelemento', idelemento);
                datos.append('precio_venta', precio+'');
                try {
                    const url = "/admin/api/actualizarPreciosVenta";  //asocia el producto con el sub producto en la tabla productos_sub
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                        input.style.color = "#02db02";
                        input.style.fontWeight = "500";
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