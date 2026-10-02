(():void=>{

  if(document.querySelector('.electronicaspendientes')){

    const consultarFechaPersonalizada = document.querySelector('#consultarFechaPersonalizada') as HTMLButtonElement;
    const btnmesactual = document.querySelector('#btnmesactual') as HTMLButtonElement;
    const btnmesanterior = document.querySelector('#btnmesanterior') as HTMLButtonElement;
    const btnhoy = document.querySelector('#btnhoy') as HTMLButtonElement;
    const btnayer = document.querySelector('#btnayer') as HTMLButtonElement;
    let fechainicio:string = "", fechafin:string = "", tablaelectronicasPendientes:HTMLElement;

    interface invoice {
        id:string,     //id de la factura_electronica
        orden:string,  //id de la factura
        prefijo:string,
        numero:string, //numero del consecutivo de la factura electronica
        resolucion:string,
        cufe:string,
        identificacion:string,
        nombre:string,
        tipoventa:string,
        base:string,
        valorimpuestototal:string,
        total:string,
        created_at: string
    }

    let facturaselectronicaspendientes:invoice[] = [];

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
        callApiInvoice(fechainiciobtn, fechafinbtn);
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
        callApiInvoice(fechainiciobtn, fechafinbtn);
    });

    btnhoy.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        const inicioDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 0, 0, 0);
        const finDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioDia);
        const fechafinbtn:string = formatoFecha(finDia);
        callApiInvoice(fechainiciobtn, fechafinbtn);
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
        callApiInvoice(fechainiciobtn, fechafinbtn);
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
      callApiInvoice(fechainicio, fechafin);
    });

    async function callApiInvoice(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin);
        try {
            const url = "/admin/api/electronicaspendientes"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            facturaselectronicaspendientes = resultado;
            printTableFacturasPagas();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }


    printTableFacturasPagas();
    function printTableFacturasPagas(){
        tablaelectronicasPendientes = ($('#tablaelectronicasPendientes') as any).DataTable({
            "responsive": true,
            destroy: true, // importante si recargas la tabla
            data: facturaselectronicaspendientes,
            columns: [
                        {title: 'Orden', data: 'orden'}, 
                        {title: 'Prefijo', data: 'prefijo'}, 
                        {title: 'Numero', data: 'numero'},
                        { 
                            title: 'Abrir', 
                            data: null, 
                            orderable: false, 
                            searchable: false, 
                            render: (data: any, type: any, row: any) => {return `<a class="btn-ver btn-xs btn-light" target="_blank" href="/admin/reportes/detalleInvoice?id=${row.orden}" data-id="${row.orden}">Abrir</a>`}
                        },
                        {title: 'Identificacion', data: 'identificacion'}, 
                        {title: 'Nombre', data: 'nombre'}, 
                        {title: 'Tipo venta', data: 'tipoventa'}, 
                        {title: 'Base', data: 'base', render: (data:number) => `$${Number(data).toLocaleString()}`}, 
                        {title: 'Valor imp', data: 'valorimpuestototal', render: (data:number) => `$${Number(data).toLocaleString()}`}, 
                        {title: 'Total', data: 'total', render: (data:number) => `$${Number(data).toLocaleString()}`}, 
                        {title: 'Fecha', data: 'created_at'}
                    ],
            pageLength: 25,
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
            },
        });
    }


  }

})();
