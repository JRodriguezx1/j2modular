(()=>{
    if(!document.querySelector('.reporteEmisores'))return;

    const POS = (window as any).POS;

    const tablaReporteEmisores = ($('#tablaReporteEmisores') as any);
    const tablaventasXemisor = ($('#tablaventasXemisor') as any);

    interface i_ingresosEmisores {
        emisor:string,
        numventas:string,
        subtotal:string,
        base:string,
        impuesto:string,
        descuento:string,
        totalventas:string,
        ingresos:string
    }

    interface i_ventasXEmisor {
        num_orden:string,
        fechapago:string,
        prefijo:string,
        num_consecutivo:string,
        tipofacturador:string,
        tipoventa:string,
        subtotal:string,
        base:string,
        valorimpuestototal:string,
        descuento:string,
        total:string,
        vendedor:string,
        caja:string
    } 


    let f1:string = '', f2:string = '', idemisor:string = '', ingresosEmisores:i_ingresosEmisores[] = [], ventasXEmisor:i_ventasXEmisor[] = [];


    ($('#selectEmisor') as any).select2({ 
        placeholder: "Selecciona un item",
        maximumSelectionLength: 1,
    });


    $("#selectEmisor").on('change', (e)=>{
            idemisor = (e.target as HTMLInputElement).value;
            if(f1!=''&&f2!='')callApiReporte(f1, f2);
    });


    async function callApiReporte(dateinicio:string, datefin:string){
        f1 = dateinicio;
        f2 = datefin;
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        datos.append('idemisor', idemisor);
        try {
            const url = "/admin/api/reportes/reporteEmisores"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            ingresosEmisores = resultado.ingresos;
            ventasXEmisor = resultado.ventasXEmisor;
            reporteEmisores();
            printVentasXEmisor();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }

    
    reporteEmisores();
    function reporteEmisores(){
        tablaReporteEmisores.DataTable({
            destroy: true, // importante si recargas la tabla
            data: ingresosEmisores,
            pageLength: 25,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Emisor', data: 'emisor', render: (data:string) => `<div class="w-48 whitespace-normal">${data}</div>`},
                        {title: 'N° ventas', data: 'numventas'},
                        {title: 'Subtotal', data: 'subtotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Base gravable', data: 'base', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Impuesto', data: 'impuesto', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Descuento', data: 'descuento', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total', data: 'totalventas', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Ingresos', data: 'ingresos', render: (data:number) => `<p class="m-0 font-semibold"> $${Number(data).toLocaleString()} </p>`}
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
                        {extend: 'excelHtml5', title: 'emisores procesadas'},  
                        {extend: 'pdfHtml5', title: 'emisores procesadas'}, 
                        {extend: 'print', title: 'emisores procesadas', text: 'Imprimir'},
                        'colvis'
                    ],
                    pageLength: 'pageLength'
                }
            },
        });
    }


    printVentasXEmisor();
    function printVentasXEmisor(){
        tablaventasXemisor.DataTable({
            destroy: true, // importante si recargas la tabla
            data: ventasXEmisor,
            pageLength: 25,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Orden', data: 'num_orden'},
                        {title: 'Fecha Pago', data: 'fechapago'},
                        {
                            title: 'N° Factura', 
                            data: null, 
                            orderable: false, 
                            searchable: false,
                            render: (data: any, type: any, row: any) => {return `${row.prefijo}${row.num_consecutivo}`}
                        },
                        {title: 'Tipo', data: 'tipofacturador', render: (data:string) => `<div class="w-48 whitespace-normal">${data}</div>`},
                        {title: 'Tipo Venta', data: 'tipoventa'},
                        { 
                            title: 'Abrir', 
                            data: null, 
                            orderable: false, 
                            searchable: false, 
                            render: (data: any, type: any, row: any) => {return `<a class="btn-ver btn-xs btn-light" target="_blank" href="/admin/caja/ordenresumen?id=${row.id}" data-id="${row.id}">Abrir</a>`}
                        },
                        {title: 'Subtotal', data: 'subtotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'B. Gravable', data: 'base', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Imp', data: 'valorimpuestototal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Descuento', data: 'descuento', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total', data: 'total', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Vendedor', data: 'vendedor'},
                        {title: 'Caja', data: 'caja'}
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
                        {extend: 'excelHtml5', title: 'emisores procesadas'},  
                        {extend: 'pdfHtml5', title: 'emisores procesadas'}, 
                        {extend: 'print', title: 'emisores procesadas', text: 'Imprimir'},
                        'colvis'
                    ],
                    pageLength: 'pageLength'
                }
            },
        });
    }


    POS.callApiReporte = callApiReporte;

})();