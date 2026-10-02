(()=>{

    if(!document.querySelector('.parqueadero'))return;

    const updateTarifa = document.querySelector('#updateTarifa') as HTMLInputElement;
    const viewTarifa = document.querySelector('#viewTarifa') as HTMLInputElement;
    const miDialogoUpdateTarifa = document.querySelector('#miDialogoUpdateTarifa') as HTMLDialogElement;
    const miDialogoViewTarifa = document.querySelector('#miDialogoViewTarifa') as HTMLDialogElement;
    const type_car = document.querySelector('#type_car') as HTMLSelectElement;
    const tablaListaTarifas = document.querySelector('#tablaListaTarifas tbody') as HTMLBodyElement;

    document.addEventListener("click", cerrarDialogoExterno);

    updateTarifa.addEventListener('click', ()=>miDialogoUpdateTarifa.showModal());
    viewTarifa.addEventListener('click', ()=>{
        miDialogoViewTarifa.showModal();
        viewTarifas();
    });

    document.querySelector('#formTipoVehiculo')?.addEventListener('submit', async(e:Event)=>{
        e.preventDefault();
        
        const datos = new FormData();
        datos.append('id', type_car.value);
        datos.append('nombre', type_car.selectedOptions[0].textContent??'');
        datos.append('tarifa_hora', (document.querySelector('#tarifa_hora') as HTMLInputElement).value.replace(/\./g, '').replace(',', '.'));
        datos.append('tarifa_dia', (document.querySelector('#tarifa_dia') as HTMLInputElement).value.replace(/\./g, '').replace(',', '.'));
        try {
            const url = "/admin/api/parqueadero/createUpdateTarifa"; //llama a la api que esta en parqueaderocontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();

            if(resultado.exito != undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                
            }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
            }
            miDialogoUpdateTarifa.close();
        } catch (error) {
            console.log(error);
        }
    });


    async function viewTarifas():Promise<void> {
        try {
            const url = `/admin/api/parqueadero/allTarifas`; //llamado a la API REST parqueaderocontrolador, detalle tarifas
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json();
            detalleTarifas(resultado);
        } catch (error) {
            console.log(error);
        }
    }


    function detalleTarifas(resultado:{id:string, nombre:string, tarifa_hora:string, tarifa_dia:string, estado:string}[]){
        while(tablaListaTarifas.firstChild)tablaListaTarifas.removeChild(tablaListaTarifas.firstChild);
        resultado.forEach(tarifa=>{
          const tr = document.createElement('tr') as HTMLTableRowElement;
          //tr.classList.add('productselect');
          tr.innerHTML = `<td class="text-center">${tarifa.nombre}</td>
                          <td class="text-center">$${Number(tarifa.tarifa_hora).toLocaleString()}</td>
                          <td class="text-center">$${Number(tarifa.tarifa_dia).toLocaleString()}</td>`;
          tablaListaTarifas.prepend(tr);
        });
    }


    function cerrarDialogoExterno(event:Event) {
        const f = event.target;
        if (f=== miDialogoUpdateTarifa || f === miDialogoViewTarifa || (f as HTMLInputElement).value === 'salir' || (f as HTMLInputElement).value === 'Cancelar' || (f as HTMLElement).id == 'btnXCerrarModalViewTarifa' || (f as HTMLElement).id == 'btnXCerrarModalDetalleComision') {
            miDialogoUpdateTarifa.close();
            miDialogoViewTarifa.close();
        }
    }


})();