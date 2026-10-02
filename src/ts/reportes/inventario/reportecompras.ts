(():void=>{

  if(document.querySelector('.reportescompras')){

    const consultarFechaPersonalizada = document.querySelector('#consultarFechaPersonalizada') as HTMLButtonElement;
    const btnmesactual = document.querySelector('#btnmesactual') as HTMLButtonElement;
    const btnmesanterior = document.querySelector('#btnmesanterior') as HTMLButtonElement;
    const btnhoy = document.querySelector('#btnhoy') as HTMLButtonElement;
    const btnayer = document.querySelector('#btnayer') as HTMLButtonElement;
    let fechainicio:string = "", fechafin:string = "", tablaReportesCompras:HTMLElement;

    interface compras {
        id:string,
        formapago:string,
        nfactura:string,
        impuesto:string,
        cantidaditems:string,
        observacion:string,
        estado:string,
        subtotal:string,
        valortotal:string,
        fechacompra:string,
        nombreusuario:string,
        nombreproveedor:string,
        nombrecaja:string
        estadocierrecaja:string
    } 

    let datosCompras:compras[] = [];

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
        callApiCompras(fechainiciobtn, fechafinbtn);
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
        callApiCompras(fechainiciobtn, fechafinbtn);
    });

    btnhoy.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        const inicioDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 0, 0, 0);
        const finDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioDia);
        const fechafinbtn:string = formatoFecha(finDia);
        callApiCompras(fechainiciobtn, fechafinbtn);
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
        callApiCompras(fechainiciobtn, fechafinbtn);
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
      callApiCompras(fechainicio, fechafin);
    });

    async function callApiCompras(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/reportecompras"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            datosCompras = resultado;
            console.log(datosCompras);
            printTableCompras();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }


    printTableCompras();
    function printTableCompras(){
        tablaReportesCompras = ($('#tablaReportesCompras') as any).DataTable({
            "responsive": true,
            destroy: true, // importante si recargas la tabla
            data: datosCompras,
            columns: [
                {title: 'id', data: 'id'}, 
                {title: 'usuario', data: 'nombreusuario'},
                {title: 'proveedor', data: 'nombreproveedor'},
                {title: 'caja', data: null, render: (data:compras)=>{ const x = data.estadocierrecaja == '0'?'Abierta':'Cerrada'; return `${data.nombrecaja} - ${x}`; }},
                {title: 'formapago', data: 'formapago'}, 
                {title: 'nfactura', data: 'nfactura'},
                {title: 'cantidaditems', data: 'cantidaditems'},
                {title: 'valortotal', data: 'valortotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                {title: 'fechacompra', data: 'fechacompra'},
                { 
                    title: 'Acciones', 
                    data: null, 
                    orderable: false, 
                    searchable: false, 
                    render: (data: any, type: any, row: any) => {return `<button class="btn-ver mr-4" data-id="${row.id}">✳️​</button> <button class="btn-eliminar" data-id="${row.id}">⛔​</button>`}
                }
            ],
            
            /*pageLength: 25,
            language: {
                search: 'Busqueda',
                emptyTable: 'No Hay datos disponibles',
                zeroRecords:    "No se encontraron registros coincidentes",
                lengthMenu: '_MENU_ Entradas por pagina',
                info: 'Mostrando pagina _PAGE_ de _PAGES_',
                infoEmpty: 'No hay entradas a mostrar',
                infoFiltered: ' (filtrado desde _MAX_ registros)',
                paginate: {"first": "<<", "last": ">>", "next": ">", "previous": "<"}
            },
            layout: {
                topStart: {
                    buttons: [
                    {extend: 'copyHtml5', text: 'Copia'}, 
                    {extend: 'excelHtml5', title: 'facturas procesadas'}, 
                    {extend: 'csvHtml5', title: 'facturas procesadas'}, 
                    {extend: 'pdfHtml5', title: 'facturas procesadas'}, 
                    {extend: 'print', title: 'facturas procesadas', text: 'Imprimir'},
                    'colvis'
                    ],
                    pageLength: 'pageLength'
                }
            },*/
        });
    }


    //evento click sobre toda la tabla compras
    document.querySelector('#tablaReportesCompras')?.addEventListener("click", (e)=>{
      const target = e.target as HTMLButtonElement;
      let idcompra = target?.dataset.id;
      if((e.target as HTMLButtonElement)?.classList.contains("btn-ver"))verCompra(idcompra);
      if(target?.classList.contains("btn-eliminar"))eliminarCompra(idcompra, target);
    });

    function verCompra(idcompra:string|undefined){
        const compra = datosCompras.find(x=>x.id==idcompra);
        if(compra?.id != null)window.location.href = '/admin/reportes/detallecompra?id='+compra.id;
    }

    function eliminarCompra(idcompra:string|undefined, target:HTMLButtonElement){
        const compra = datosCompras.find(x=>x.id==idcompra);
        const fila = (tablaReportesCompras as any).row(target.closest('tr'));
        if(compra?.estadocierrecaja === '0'){
            Swal.fire({
                customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
                icon: 'question',
                title: 'Desea eliminar la compra del sistema?',
                text: "La compra sera eliminado por completo de la aplicacion.",
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    (async ()=>{
                        const datos = new FormData();
                        datos.append('id', compra.id);
                        try {
                            const url = "/admin/api/eliminarcompra"; //llamado a la API REST en reportescontrolador para eliminar la compra
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
             msjalertToast('error', '¡Error!', 'No se puede eliminar la compra, porque la caja esta cerrada');
        }
    }


  }

})();
