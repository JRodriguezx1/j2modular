(()=>{
    if(!document.querySelector('.listaProductosComprados'))return;

    const POS = (window as any).POS;

    const tablaProductosComprados = ($('#tablaProductosComprados') as any);

    interface i_detalleItem {
        idcompra:string,
        iddetalle:string,
        usuario:string,
        nitproveedor:string,
        proveedor:string,
        nfactura:string,
        nombre_item:string,
        unidad:string,
        tipo:string,
        costounitario:string,
        cantidad:string,
        subtotal:string,
        impuesto:string,
        costototal:string,
        fecha:string,
        stock_actual:string
    }

    let item:i_detalleItem[] = [];

    async function callApiReporte(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/reportes/listaProductosComprados"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            item = resultado;
            console.log(item);
            reporteProductosComprados();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }

    
    reporteProductosComprados();
    function reporteProductosComprados(){
        tablaProductosComprados.DataTable({
            destroy: true, // importante si recargas la tabla
            data: item,
            pageLength: 25,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Usuario', data: 'usuario', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'Proveedor', data: 'proveedor', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'N-Fact', data: 'nfactura'},
                        {title: 'producto', data: 'nombre_item', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
                        {title: 'Und', data: 'unidad'},
                        {title: 'Unidad', data: 'costounitario', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Cantidad', data: 'cantidad'},
                        {title: 'Total', data: 'costototal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Stock Actual', data: 'stock_actual'},
                        {title: 'Fecha Compra', data: 'fechacompra', render: (data:string) => `<div class="w-44 whitespace-normal">${data}</div>`},
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