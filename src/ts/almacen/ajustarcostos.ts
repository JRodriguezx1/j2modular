(()=>{
    if(document.querySelector('.ajustarcostos')){
        let tablaAjustarCostos:HTMLElement;

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
                tablaAjustarCostos = ($('#tablaAjustarCostos') as any).DataTable(configdatatablesajustarcostos);
            } catch (error) {
                console.log(error);
            }
        })();

        //tablaAjustarCostos = ($('#tablaAjustarCostos') as any).DataTable(configdatatables);
        //indiceFila = (tablaAjustarCostos as any).row(1).data();

        function pirntUnidades():void{
            allConversionUnidades.forEach(subUnd =>{
                let select = document.querySelector(`tr[data-idsubproducto="${subUnd.idsubproducto}"]`)?.children[5].children[0];
                if(select == undefined)select = document.querySelector(`tr[data-idproducto="${subUnd.idproducto}"]`)?.children[5].children[0];
                const option = document.createElement('option');
                option.textContent = subUnd.nombreunidaddestino;
                option.dataset.factor = subUnd.factorconversion;
                select?.appendChild(option);
            });
        }
 

        /*const fila = (tablaAjustarCostos as any).row((idx:number, data:any, node:HTMLElement)=>{
            return node.dataset.idproducto === subUnd.idsubproducto;
        }).data();*/
        //console.log(fila[4]);
      /*fila[4] = `<select class="formulario__select" required>
                        <option value="" disabled selected>-Seleccionar-</option>
                        <option value=""> Unidad </option>
                    </select>`;
        (tablaAjustarCostos as any).row(2).data(fila).draw();*/

        //tablaAjustarCostos = ($('#tablaAjustarCostos') as any).DataTable(configdatatables);
        
        document.querySelector('#tablaAjustarCostos')?.addEventListener('focusout', (e:Event)=>{
            var input = e.target as HTMLInputElement;
            let tipoelemento:string = '', idelemento:string = '';
            if(input.classList.contains('inputAjustarCosto')){
                
                var tr = input.closest('tr');
                const selectUnidad = tr?.querySelector('.formulario__select') as HTMLSelectElement;
                const factor:number = Number(selectUnidad.options[selectUnidad.selectedIndex].dataset.factor);
                
                var trx = input.closest('.fila') as HTMLElement;  //fila padre
                if(!trx)trx = tr?.previousSibling as HTMLElement;

                if(trx.classList.contains('producto')){
                    idelemento = trx.dataset.idproducto!;
                    tipoelemento = '0'; //si es producto
                }
                else{
                    idelemento = trx.dataset.idsubproducto!;
                    tipoelemento = '1';  // si es un subproducto
                }

                ////////////// enviar datos por api para actualizar costos /////////////
                if(Number(input.value) != Number(trx.dataset?.costo||0))
                actualizarcostos(tipoelemento, idelemento, factor, input);
            }
        });


        function actualizarcostos(tipoelemento:string, idelemento:string, factor:number, input:HTMLInputElement){
            if(input.value=='')input.value = '0';
            const costo = Number(input.value)/factor;
            console.log(costo);
            (async ()=>{ 
                const datos = new FormData();
                datos.append('tipoelemento', tipoelemento); //1 = subproducto
                datos.append('idelemento', idelemento);
                datos.append('precio_compra', costo+'');
                try {
                    const url = "/admin/api/actualizarcostos";  //asocia el producto con el sub producto en la tabla productos_sub
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