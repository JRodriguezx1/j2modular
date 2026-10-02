(()=>{
    if(!document.querySelector('.recibosCaja'))return;

    const POS = (window as any).POS;

    const tablaRecibosCaja = ($('#tablaRecibosCaja') as any);

    interface i_ingresosEmisores {
        id: string,
        num_orden:string,
        fecha:string,
        concepto:string,
        numero_documento:string,
        tercero:string,
        mediopago:string,
        observacion:string,
        cajero:string,
        caja: string,
        valor:string,
        valormediopago:string,
        emisor:string
    }



    let ingresosEmisores:i_ingresosEmisores[] = [];



    async function callApiReporte(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/reportes/recibosCaja"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            ingresosEmisores = resultado;
            console.log(ingresosEmisores);
            reporteRecibosCaja();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }

    
    reporteRecibosCaja();
    function reporteRecibosCaja(){
        tablaRecibosCaja.DataTable({
            destroy: true, // importante si recargas la tabla
            data: ingresosEmisores,
            pageLength: 25,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Num', data: 'num_orden'},
                        {title: 'Fecha', data: 'fecha', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'Concepto', data: 'concepto', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'Documento', data: 'documento'},
                        {title: 'Tercero', data: 'tercero', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'Medio Pago', data: 'mediopago'},
                        {title: 'Cajero', data: 'cajero'},
                        {title: 'Valor', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Valor pagado', data: 'valormediopago', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Emisor', data: 'emisor', render: (data:string) => `<div class="w-44 whitespace-normal">${data==null?'Negocio':data}</div>`},
                        {title: 'Observacion', data: 'observacion', render: (data:string) => `<div class="w-52 whitespace-normal">${data}</div>`},
                        {title: 'Estado', data: 'estadoMov', render: (data:number) => `<div class="">${data==1?'Registrado':'Anulado'}</div>`}
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