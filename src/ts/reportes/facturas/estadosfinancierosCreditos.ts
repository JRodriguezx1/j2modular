(()=>{
    if(!document.querySelector('.estadosfinancierosCreditos'))return;
        const POS = (window as any).POS;

        let tablaEstdosFinancierosCreditos:HTMLElement;
        let estadosFinancierosCreditos:[] = [];


        async function callApiReporte(dateinicio:string, datefin:string){
            document.querySelector('#fecha1')!.textContent = dateinicio;
            document.querySelector('#fecha2')!.textContent = datefin;
            (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
            const datos = new FormData();
            datos.append('fechainicio', dateinicio);
            datos.append('fechafin', datefin);
            try {
                const url = "/admin/api/reportes/creditos/estadosFinancieros"; //llama a la api que esta en reportescontrolador.php
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                estadosFinancierosCreditos = await respuesta.json();
                printTableEstadosFinancierosCreditos();
            (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
            } catch (error) {
                console.log(error);
            }
        }


        printTableEstadosFinancierosCreditos();
        function printTableEstadosFinancierosCreditos(){
            tablaEstdosFinancierosCreditos = ($('#tablaEstdosFinancierosCreditos') as any).DataTable({
                "responsive": true,
                pageLength: 25,
                destroy: true, // importante si recargas la tabla
                data: estadosFinancierosCreditos,
                columns: [
                    {title: 'N°', data: 'num_orden'},
                    {title: 'Emisor', data: 'nombreEmisor', render: (data:string) => `<div class="w-48 whitespace-normal">${data??'Negocio'}</div>`},
                    {title: 'Fecha', data: 'fechainicio'},
                    {title: 'Credito', data: 'capitalTotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {title: 'Costo', data: 'costo_total', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {title: 'Utl. Comercial', data: 'utilidad_comercial', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {title: 'Utl. Proyectada', data: 'utilidad_proyectada', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {title: 'Pagado', data: 'valor_pagado', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {title: 'Utl Realizada', data: 'utilidad_realizada', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    {
                        title: 'Estado',
                        data: 'estado',
                        render: (data: any, type: any, row: any) => {return `<a class="btn-xs ${row.estado=='2'?'btn-blue':row.estado=='1'?'btn-lima':'btn-light'}" target="_blank" href="/admin/creditos/detallecredito?id=${row.id}">${row.estado=='1'?'Finalizado':'Abierto'}</a>`}
                    },
                ],
                
                
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
                        {extend: 'excelHtml5', title: 'facturas procesadas'}, 
                        {extend: 'pdfHtml5', title: 'facturas procesadas'}, 
                        {extend: 'print', title: 'facturas procesadas', text: 'Imprimir'},
                        'colvis'
                        ],
                        pageLength: 'pageLength'
                    }
                }
            });
        }

        POS.callApiReporte = callApiReporte;
    
})();