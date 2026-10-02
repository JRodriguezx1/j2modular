(():void=>{
  if(document.querySelector('.gastoseingresos')){
    const consultarFechaPersonalizada = document.querySelector('#consultarFechaPersonalizada') as HTMLButtonElement;
    const btnmesactual = document.querySelector('#btnmesactual') as HTMLButtonElement;
    const btnmesanterior = document.querySelector('#btnmesanterior') as HTMLButtonElement;
    const btnhoy = document.querySelector('#btnhoy') as HTMLButtonElement;
    const btnayer = document.querySelector('#btnayer') as HTMLButtonElement;
    let fechainicio:string = "", fechafin:string = "", tablaGastos:HTMLTableElement, tablaIngresos:HTMLTableElement;

    interface apigastos {
        Id:string,
        id_compra:string,
        idg_cierrecaja:string,
        nombrecaja:string,
        nombrebanco:string,
        nombre:string,
        operacion:string,
        categoriagasto:string,
        estado:string,
        valor:string,
        descripcion:string,
        imgcomprobante:string,
        fecha:string
    }

    interface apiingresos {
        id:string,
        operacion:string,
        valor:string,
        fecha:string,
        nombrecaja:string,
        estado:string,
        nombreusuario:string
    }

    let datosGastos:apigastos[] = [], datosIngresos:apiingresos[] = [];


    // SELECTOR DE FECHAS DEL CALENDARIO
    inicializarDateRangePicker($('input[name="datetimes"]') as any);

    $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        var endDate = picker.endDate.format('YYYY-MM-DD HH:mm:ss');
        fechainicio = startDate;
        fechafin = endDate;
        //(document.querySelector('#fechainicio') as HTMLParagraphElement).textContent = fechainicio;
        //(document.querySelector('#fechafin') as HTMLParagraphElement).textContent = fechafin;
    });

    btnmesactual.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        // Primer día del mes actual
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        // Último día del mes actual
        const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        const fechainiciobtn:string = primerDia.toISOString().split('T')[0];
        const fechafinbtn:string = ultimoDia.toISOString().split('T')[0];
        callApiGastosIngresos(fechainiciobtn, fechafinbtn);
    });


    btnmesanterior.addEventListener('click', (e:Event)=>{
        // Fecha actual
        const hoy = new Date();
        // Primer día del mes anterior
        const primerDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
        // Último día del mes anterior
        const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        const fechainiciobtn:string = primerDiaMesAnterior.toISOString().split('T')[0];
        const fechafinbtn:string = ultimoDiaMesAnterior.toISOString().split('T')[0];
        callApiGastosIngresos(fechainiciobtn, fechafinbtn);
    });

    btnhoy.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        const inicioDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 0, 0, 0);
        const finDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioDia);
        const fechafinbtn:string = formatoFecha(finDia);
        callApiGastosIngresos(fechainiciobtn, fechafinbtn);
    });

    btnayer.addEventListener('click', (e:Event)=>{
        // Fecha actual
        const hoy = new Date();
        // Día anterior
        const ayer = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() - 1);
        // Inicio y fin del día anterior
        const inicioAyer = new Date(ayer.getFullYear(), ayer.getMonth(), ayer.getDate(), 0, 0, 0);
        const finAyer = new Date(ayer.getFullYear(), ayer.getMonth(), ayer.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioAyer);
        const fechafinbtn:string = formatoFecha(finAyer);
        callApiGastosIngresos(fechainiciobtn, fechafinbtn);
    });

    function formatoFecha(fecha: Date): string {
        const año = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, "0");
        const dia = String(fecha.getDate()).padStart(2, "0");
        const hora = String(fecha.getHours()).padStart(2, "0");
        const minuto = String(fecha.getMinutes()).padStart(2, "0");
        const segundo = String(fecha.getSeconds()).padStart(2, "0");
        return `${año}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
    }


    ////// consulta por fecha personalizada
    consultarFechaPersonalizada.addEventListener('click', ()=>{
      if(fechainicio == '' || fechafin == ''){
         msjalertToast('error', '¡Error!', "Elegir fechas a consultar");
         return;
      }
      callApiGastosIngresos(fechainicio, fechafin);
    });

    async function callApiGastosIngresos(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/gastoseingresos"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            datosGastos = resultado.gastos;
            datosIngresos = resultado.ingresos;
            console.log(resultado);
            printTableGastos();
            printTableIngresos();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }
    


    printTableGastos();
    function printTableGastos(){
        tablaGastos = ($('#tablaGastos') as any).DataTable({
            destroy: true, // importante si recargas la tabla
            data: datosGastos,
            columns: [
                        {title: 'id', data: 'Id'}, 
                        {   
                            title: 'Caja', 
                            data: null, render: (data: apigastos) => { 
                                            const x = data.estado == '0'?'Abierta':'Cerrada';
                                            return `${data.nombrecaja} - ${x}`;
                                        } 
                        },
                        {title: 'Banco', data: 'nombrebanco'},
                        {title: 'Usuario', data: 'nombre'},
                        {title: 'Operacion', data: 'operacion'},
                        {title: 'Tipo Gasto', data: 'categoriagasto'},
                        {title: 'Valor', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Fecha', data: 'fecha'},
                        { 
                            title: 'Acciones', 
                            data: null, 
                            orderable: false, 
                            searchable: false, 
                            render: (data: any, type: any, row: any) => { 
                                    return `<button class="btn-ver" data-id="${row.Id}">✳️​</button>
                                            <button class="btn-imgcomprobante" data-id="${row.Id}">🧾​</button>
                                            <button class="btn-eliminar" data-id="${row.Id}">⛔​</button>`}
                        },
                        {title: 'Descripcion', data: 'descripcion'},
                    ],
        });
    }


    printTableIngresos();
    function printTableIngresos(){
        tablaIngresos = ($('#tablaIngresos') as any).DataTable({
            destroy: true, // importante si recargas la tabla
            data: datosIngresos,
            columns: [
                        {title: 'id', data: 'id'}, 
                        {   
                            title: 'Caja', 
                            data: null, render: (data: apiingresos) => { 
                                            const x = data.estado == '0'?'Abierta':'Cerrada';
                                            return `${data.nombrecaja} - ${x}`;
                                        } 
                        },
                        {title: 'Usuario', data: 'nombreusuario'},
                        {title: 'Operacion', data: 'operacion'},
                        {title: 'Valor', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Fecha', data: 'fecha'},
                        { 
                            title: 'Acciones', 
                            data: null, 
                            orderable: false, 
                            searchable: false, 
                            render: (data: any, type: any, row: any) => {return `<button class="btn-eliminar" data-id="${row.id}">⛔​</button>`}
                        }
                    ],
        });
    }
    

    //evento click sobre toda la tabla gastos
    document.querySelector('#tablaGastos')?.addEventListener("click", (e)=>{
      const target = e.target as HTMLButtonElement;
      let idgasto = target?.dataset.id;
      if((e.target as HTMLButtonElement)?.classList.contains("btn-ver"))verGasto(idgasto);
      if(target?.classList.contains("btn-imgcomprobante"))imgcomprobante(idgasto);
      if(target?.classList.contains("btn-eliminar"))eliminarGasto(idgasto, target);
    });

    //solo aplica para gastos de tipo compra
    function verGasto(idgasto:string|undefined){
        const gasto = datosGastos.find(x=>x.Id==idgasto);
        if(gasto?.id_compra != null)window.location.href = '/admin/reportes/detallecompra?id='+gasto.id_compra;
    }

    function imgcomprobante(idgasto:string|undefined){
        const gasto = datosGastos.find(x=>x.Id==idgasto);
        if(gasto?.imgcomprobante)window.open("/build/img/"+gasto?.imgcomprobante, "_blank");
    }

    function eliminarGasto(idgasto:string|undefined, target:HTMLButtonElement){
        const gasto = datosGastos.find(x=>x.Id==idgasto);
        const fila = (tablaGastos as any).row(target.closest('tr'));
        if(gasto?.estado === '0'){
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar el gasto de la caja?',
                text: "El gasto sera eliminado por completo de la caja.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (async ()=>{
                        const datos = new FormData();
                        datos.append('id', gasto.Id);
                        try {
                            const url = "/admin/api/eliminargasto"; //llamado a la API REST en reportescontrolador para eliminar el gasto
                            const respuesta = await fetch(url, {method: 'POST', body: datos});
                            const resultado = await respuesta.json();
                            if(resultado.exito !== undefined){ 
                                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                                fila.remove().draw(false);
                            }else{
                                msjalertToast('error', '¡Error!', resultado.error[0]);
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();
                }
            });
        }else{
             msjalertToast('error', '¡Error!', 'No se puede eliminar el gasto, porque la caja esta cerrada');
        }
    }


    //evento click sobre toda la tabla ingresos
    document.querySelector('#tablaIngresos')?.addEventListener("click", (e)=>{
      const target = e.target as HTMLButtonElement;
      let idingreso = target?.dataset.id;
      if(target?.classList.contains("btn-eliminar"))eliminarIngreso(idingreso, target);
    });

    function eliminarIngreso(idingreso:string|undefined, target:HTMLButtonElement){
        const ingreso = datosIngresos.find(x=>x.id==idingreso);
        const fila = (tablaIngresos as any).row(target.closest('tr'));
        if(ingreso?.estado === '0'){
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar el ingreso de la caja?',
                text: "El ingreso sera eliminado por completo de la caja.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (async ()=>{
                        const datos = new FormData();
                        datos.append('id', ingreso.id);
                        try {
                            const url = "/admin/api/eliminaringreso"; //llamado a la API REST en reportescontrolador para eliminar un ingreso
                            const respuesta = await fetch(url, {method: 'POST', body: datos});
                            const resultado = await respuesta.json();
                            if(resultado.exito !== undefined){ 
                                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                                fila.remove().draw(false);
                            }else{
                                msjalertToast('error', '¡Error!', resultado.error[0]);
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();
                }
            });
        }else{
             msjalertToast('error', '¡Error!', 'No se puede eliminar el ingreso, porque la caja esta cerrada');
        }
    }

  }

})();
