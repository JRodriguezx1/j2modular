(()=>{
    if(!document.querySelector('.remisiones'))return;

    const POS = (window as any).POS;

    const tablaRemisiones = ($('#tablaRemisiones') as any);

    interface i_remisiones {
        id:string,
        fechacreacion:string,
        fechaentrega?:string,
        vendedor:string,
        cliente:string,
        num_orden:string,
        entrega:string,
        entregado:string,
    }

    let remisiones:i_remisiones[] = [];
    const labelsRemisiones = ['Id', 'Fecha', 'Fecha entrega', 'Usuario', 'Cliente', 'Orden', 'Entrega', 'Estado', 'Acciones'];


    async function callApiReporte(dateinicio:string, datefin:string){
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/reportes/remisiones"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            remisiones = resultado;
            printRemisiones();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }

    
    printRemisiones();
    function printRemisiones(){
        const dataTableRemisiones = tablaRemisiones.DataTable({
            destroy: true, // importante si recargas la tabla
            data: remisiones,
            pageLength: 25,
            order: [[ 1, 'desc' ]],
            responsive: {
                details: false
            },
            columns: [
                        {title: 'Id', data: 'id', className: 'all dtr-control', responsivePriority: 1},
                        {title: 'Fecha', data: 'fechacreacion', className: 'all', responsivePriority: 2, render: (data:string) => `<div class="w-36 whitespace-normal">${data}</div>`},
                        {title: 'Fecha Entrega', data: 'fechaentrega', responsivePriority: 7, render: (data:string) => `<div class="text-center">${data??'-'}</div>`},
                        {title: 'Usuario', data: 'vendedor', responsivePriority: 8, render: (data:string) => `<div class="w-48 whitespace-normal">${data}</div>`},
                        {title: 'Cliente', data: 'cliente', responsivePriority: 4, render: (data:string) => `<div class="w-48 whitespace-normal">${data}</div>`},
                        {title: 'Orden', data: 'num_orden', responsivePriority: 3},
                        {title: 'Entrega', data: 'entrega', responsivePriority: 5, render: (data:string) => `<span class="rm-delivery">${data || '-'}</span>`},
                        {title: 'Estado', data: 'entregado', responsivePriority: 6, render: (data:string) => {
                            const entregado = data == '1';
                            return `<span class="rm-status ${entregado ? 'rm-status--done' : 'rm-status--pending'}">${entregado ? 'Despachado' : 'Pendiente'}</span>`;
                        }},
                        { 
                            title: 'Acciones', 
                            data: null, 
                            responsivePriority: 9,
                            orderable: false, 
                            searchable: false, 
                            render: (data: any, type: any, row: any) => {return `<a class="rm-action-open" target="_blank" href="/admin/caja/ordenresumen?id=${row.id}" data-id="${row.id}">Abrir</a>`}
                        },        
            ],
            createdRow: function(row: HTMLTableRowElement) {
                labelsRemisiones.forEach((label, index) => {
                    row.children[index]?.setAttribute('data-rm-label', label);
                });
            },
            drawCallback: function() {
                document.querySelector('.rm-table-card')?.classList.add('rm-remisiones-mobile');
            },
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
            },
        });

        const tablaRemisionesEl = document.querySelector('#tablaRemisiones') as HTMLTableElement|null;
        if(tablaRemisionesEl){
            tablaRemisionesEl.onclick = (e:MouseEvent)=>{
                const target = e.target as HTMLElement;
                const expandCell = target.closest('td') as HTMLTableCellElement|null;
                if(!expandCell || expandCell.cellIndex !== 0 || window.innerWidth > 768)return;

                const row = expandCell.closest('tr');
                if(!row)return;

                const dataTableRow = dataTableRemisiones.row(row);
                if(dataTableRow.child.isShown()){
                    dataTableRow.child.hide();
                    row.classList.remove('parent');
                    return;
                }

                const cells = Array.from(row.querySelectorAll<HTMLTableCellElement>('td'));
                const detailRows = cells.slice(2).map((cell, index) => {
                    const title = labelsRemisiones[index + 2] || '';
                    return `<li><span class="dtr-title">${title}</span><span class="dtr-data">${cell.innerHTML}</span></li>`;
                }).join('');

                dataTableRow.child(`<ul class="dtr-details">${detailRows}</ul>`, 'child').show();
                row.classList.add('parent');
            };
        }
    }


    POS.callApiReporte = callApiReporte;

})();
