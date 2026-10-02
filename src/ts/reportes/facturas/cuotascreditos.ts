(()=>{
    if(!document.querySelector('.cuotasCreditos'))return;

    const POS = (window as any).POS;
    const tablaCuotasCreditos = ($('#tablaCuotasCreditos') as any);
    const spinner = document.querySelector('.content-spinner1') as HTMLElement;
    const totalRegistros = document.querySelector('#cuotasCreditosTotalRegistros') as HTMLElement;
    const totalValor = document.querySelector('#cuotasCreditosTotalValor') as HTMLElement;
    const periodoReporte = document.querySelector('#cuotasCreditosPeriodo') as HTMLElement;

    interface i_cuotasCreditos {
        fechapagado:string,
        idtipofinanciacion:string,
        cliente:string,
        credito:string,
        numerocuota:string,
        valorpormedio:string,
        mediopago:string,
        idestadocreditos:string,
    } 

    let cuotasCreditos:i_cuotasCreditos[] = [];

    async function callApiReporte(dateinicio:string, datefin:string){
        spinner.style.display = "grid";
        const datos = new FormData();
        const fechainicioConsulta = normalizarFechaConsulta(dateinicio, 'inicio');
        const fechafinConsulta = normalizarFechaConsulta(datefin, 'fin');
        datos.append('fechainicio', fechainicioConsulta);
        datos.append('fechafin', fechafinConsulta);

        try {
            const url = "/admin/api/reportes/creditos/cuotasCreditos";
            const respuesta = await fetch(url, {method: 'POST', body: datos});
            const resultado = await respuesta.json();
            cuotasCreditos = Array.isArray(resultado) ? resultado : [];
            actualizarResumen(fechainicioConsulta, fechafinConsulta);
            printCuotasCreditos();
            spinner.style.display = "none";
        } catch (error) {
            console.log(error);
            spinner.style.display = "none";
        }
    }

    function monedaCOP(valor:number|string){
        return '$'+Number(valor || 0).toLocaleString('es-CO');
    }

    function normalizarFechaConsulta(fecha:string, tipo:'inicio'|'fin'){
        const fechaLimpia = (fecha || '').trim();
        if(!fechaLimpia)return '';

        if(fechaLimpia.includes(':'))return fechaLimpia;

        return tipo === 'inicio' ? `${fechaLimpia} 00:00:00` : `${fechaLimpia} 23:59:59`;
    }

    function formatearPeriodo(fecha:string){
        return fecha.replace(' ', ' - ');
    }

    function actualizarResumen(dateinicio:string = '', datefin:string = ''){
        const total = cuotasCreditos.reduce((acum, cuota)=>acum + Number(cuota.valorpormedio || 0), 0);
        if(totalRegistros)totalRegistros.textContent = cuotasCreditos.length.toLocaleString('es-CO');
        if(totalValor)totalValor.textContent = monedaCOP(total);
        if(periodoReporte)periodoReporte.textContent = dateinicio && datefin ? `${formatearPeriodo(dateinicio)} - ${formatearPeriodo(datefin)}` : 'Sin periodo consultado';
    }

    printCuotasCreditos();
    actualizarResumen();

    function printCuotasCreditos(){
        document.querySelector('#tablaCuotasCreditos_wrapper')
            ?.closest('.config-table-card')
            ?.querySelector('.config-datatable-custom-toolbar')
            ?.remove();

        tablaCuotasCreditos.DataTable({
            destroy: true,
            data: cuotasCreditos,
            pageLength: 25,
            order: [[0, 'desc']],
            columns: [
                {title: 'Fecha', data: 'fechapagado', className: 'whitespace-nowrap'},
                {
                    title: 'Tipo',
                    data: 'idtipofinanciacion',
                    render: (data:number) => `<span class="table-badge ${data==1?'table-badge--primary':'table-badge--info'}">${data==1?'Credito':'Separado'}</span>`
                },
                {
                    title: 'Cliente',
                    data: 'cliente',
                    render: (data:string) => `<span class="table-entity"><span class="table-entity__icon"><i class="fa-solid fa-user"></i></span><span>${data || 'Sin cliente'}</span></span>`
                },
                {title: 'Credito', data: 'credito', render: (data:string) => `<span class="table-badge table-badge--neutral">${data || '-'}</span>`},
                {title: 'No. cuota', data: 'numerocuota', render: (data:string) => `<strong>${data || '0'}</strong>`},
                {title: 'Valor', data: 'valorpormedio', render: (data:number) => `<strong class="table-amount">${monedaCOP(data)}</strong>`},
                {title: 'Medio de pago', data: 'mediopago', render: (data:string) => `<span class="table-badge table-badge--info">${data || 'No indicado'}</span>`},
                {
                    title: 'Estado',
                    data: 'idestadocreditos',
                    render: (data: any, type: any, row: any) => {
                        const estado = row.idestadocreditos=='1'?'Finalizado':row.idestadocreditos=='2'?'Abierto':'Anulado';
                        const clase = row.idestadocreditos=='1'?'table-status--success':row.idestadocreditos=='2'?'table-status--warning':'table-status--danger';
                        return `<span class="table-status ${clase}">${estado}</span>`;
                    }
                }
            ],
            language: {
                search: 'Busqueda',
                emptyTable: 'No hay datos disponibles',
                zeroRecords: "No se encontraron registros coincidentes",
                lengthMenu: '_MENU_ Entradas por pagina',
                info: 'Mostrando pagina _PAGE_ de _PAGES_',
                infoEmpty: 'No hay entradas a mostrar',
                infoFiltered: ' (filtrado desde _MAX_ registros)',
                paginate: {"first": "<<", "last": ">>", "next": ">", "previous": "<"}
            },
            layout: {
                topStart: {
                    buttons: [
                        {extend: 'excelHtml5', title: 'cuotas creditos'},
                        {extend: 'pdfHtml5', title: 'cuotas creditos'},
                        {extend: 'print', title: 'cuotas creditos', text: 'Imprimir'},
                        'colvis'
                    ],
                    pageLength: 'pageLength'
                }
            },
        });
        modernizarToolbarDataTable('#tablaCuotasCreditos');
    }

    POS.callApiReporte = callApiReporte;
})();
