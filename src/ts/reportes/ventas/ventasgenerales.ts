
(()=>{

    if(!document.querySelector('.ventasgenerales'))return;

    const POS = (window as any).POS;
    const dataTablesLanguage = {
        search: 'Busqueda',
        emptyTable: 'No Hay datos disponibles',
        zeroRecords: 'No se encontraron registros coincidentes',
        lengthMenu: '_MENU_ Entradas por pagina',
        info: 'Mostrando pagina _PAGE_ de _PAGES_',
        infoEmpty: 'No hay entradas a mostrar',
        infoFiltered: ' (filtrado desde _MAX_ registros)',
        paginate: { first: '<<', last: '>>', next: '>', previous: '<' }
    };

    const tablaProductosVendidos = ($('#tablaProductosVendidos') as any);
    const tablaMediosPagos = ($('#tablaMediosPagos') as any);
    const tablacreditosSeparados = ($('#tablacreditosSeparados') as any);
    const tablaGastos = ($('#tablaGastos') as any);
    const tablaIngresoCanalventa = ($('#tablaIngresoCanalventa') as any);
    const tablaVentasXUsuario = ($('#tablaVentasXUsuario') as any);
    const tablaResumenVentas = ($('#tablaResumenVentas') as any);
    const tablaResumenCreditos = document.querySelector('#tablaResumenCreditos tbody');
    const printBalance = document.querySelector('#printBalance') as HTMLButtonElement;

    function limpiarToolbarDataTable(selectorTabla:string):void{
        const tabla = document.querySelector(selectorTabla) as HTMLTableElement|null;
        const card = tabla?.closest('.config-table-card');
        card?.querySelector('.config-datatable-custom-toolbar')?.remove();
        card?.classList.remove('has-custom-datatable-toolbar');
    }
    
    interface i_productosVendidos {
        idproducto:string,
        cantidadrefencia:string,
        nombreproducto:string,
        totalProductosVendidos:string,
        valorunidad:string,
        valorTotal:string
    }

    interface i_mediosPagos {
        idmediopago:string,
        cantidadMP:string,
        mediopago:string,
        valor:string
    }

    interface i_creditosSeparados {
        idestadocreditos:string,
        estado:string
        carteraTotal:string,
        carteraXCobrar:string,
        totalAbonado:string,
        total:string,
    }

    interface i_canaldeVenta {
        canaldeventa:string,
        transacciones:string,
        valor:string,
    }


    interface i_ventasEmpleados {
        empleado:string,
        ventasRealizadas:string,
        totalVentas:string,
        porcentaje:string,
        valorComision:string,
    }

    interface i_gastos {
        descripcion:string,
        tipogasto:string,
        valor:string,
    }
    
    interface i_resumenVentas {
        ventas:string,
        total_ventas:string,
        total_costo:string,
        ganancia:string,
        margenutilidad:string,
    }

    interface i_resumenCreditos {
        creditos:string,
        capitalTotal:string,
        costo_total:string,
        utilidad_comercial:string,
        utilidad_proyectada:string,
        valor_pagado:string,
        utilidad_realizada:string
    }

    let productosVendidos:i_productosVendidos[] = [], mediosPagos:i_mediosPagos[] = [], creditosSeparados:i_creditosSeparados[] = [], ventasEmpleados:i_ventasEmpleados[]=[], gastos:i_gastos[]=[], canalVenta:i_canaldeVenta[]=[], resumenVentas:i_resumenVentas[]=[], resumenCreditos:i_resumenCreditos[]=[];
    let totalabonos:number = 0, dateStart = '', dateEnd = '';

    async function callApiReporte(dateinicio:string, datefin:string){
        dateStart = dateinicio;
        dateEnd = datefin;
        document.querySelector('#fecha1')!.textContent = dateinicio;
        document.querySelector('#fecha2')!.textContent = datefin;
        
        (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin+' 23:59:59');
        try {
            const url = "/admin/api/reportes/reportesGenerales"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            productosVendidos = resultado.productosVendidos;
            mediosPagos = resultado.mediosPagos;
            creditosSeparados = resultado.separados;
            ventasEmpleados = resultado.ventasXusuario;
            gastos = resultado.gastos;
            canalVenta = resultado.canalVenta;
            resumenCreditos = resultado.resumenCreditos;
            resumenVentas = resultado.resumenVentas;
            printProductosVendidos();
            printMediosPagos();
            printCreditosSeparados();
            printVentasUsuarios();
            printGastos();
            printCanalVenta();
            totalabonos = Number(resultado.totalabonos);
            printResumen(Number(resultado.totalDescuentos[0].total_descuentos));
            document.querySelector('#totalDescto')!.textContent = '$'+Number(resultado.totalDescuentos[0].total_descuentos).toLocaleString();
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }


    printProductosVendidos();
    function printProductosVendidos(){
        limpiarToolbarDataTable('#tablaProductosVendidos');
        tablaProductosVendidos.DataTable({
            destroy: true, // importante si recargas la tabla
            data: productosVendidos,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Producto', data: 'nombreproducto'},
                        {title: 'Cantidad Vendida', data: 'totalProductosVendidos'},
                        {title: 'V. Unidad', data: 'valorunidad', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total Ventas', data: 'valorTotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Costo Total', data: 'costoTotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Utilidad', data: 'utilidad', render: (data:number) => `$${Number(data).toLocaleString()}`}
                    ],
        });
        modernizarToolbarDataTable('#tablaProductosVendidos');
    }

    printMediosPagos();
    function printMediosPagos(){
        limpiarToolbarDataTable('#tablaMediosPagos');
        tablaMediosPagos.DataTable({
            destroy: true, // importante si recargas la tabla
            data: mediosPagos,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Medio de Pago', data: 'mediopago'},
                        {title: 'Transacciones', data: 'cantidadMP'},
                        {title: 'Total Ventas', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    ],
        });
        modernizarToolbarDataTable('#tablaMediosPagos');
    }

    printCreditosSeparados();
    function printCreditosSeparados(){
        limpiarToolbarDataTable('#tablacreditosSeparados');
        tablacreditosSeparados.DataTable({
            destroy: true, // importante si recargas la tabla
            data: creditosSeparados,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {
                            title: 'Estado', 
                            data: 'estado', 
                            render: (data: any, type: any, row: any) => {
                                const clase = row.estado == 'Abierto' ? 'table-status--warning' : row.estado == 'Finalizado' ? 'table-status--success' : 'table-status--danger';
                                return `<span class="table-status ${clase}">${row.estado}</span>`;
                            }
                        },
                        {title: 'Cartera Total', data: 'carteraTotal', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Cartera Por Cobrar', data: 'carteraXCobrar', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total Abonado', data: 'totalAbonado', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total', data: 'total'}
                    ],
        });
        modernizarToolbarDataTable('#tablacreditosSeparados');
    }


    printCanalVenta();
    function printCanalVenta(){
        limpiarToolbarDataTable('#tablaIngresoCanalventa');
        tablaIngresoCanalventa.DataTable({
            destroy: true, // importante si recargas la tabla
            data: canalVenta,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'asc' ]],
            columns: [
                        {title: 'Canal De Venta', data: 'canalVenta'},
                        {title: 'Transacciones', data: 'transacciones'},
                        {title: 'Valor', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    ],
        });
        modernizarToolbarDataTable('#tablaIngresoCanalventa');
    }


    printVentasUsuarios();
    function printVentasUsuarios(){
        limpiarToolbarDataTable('#tablaVentasXUsuario');
        tablaVentasXUsuario.DataTable({
            destroy: true, // importante si recargas la tabla
            data: ventasEmpleados,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Empleado', data: 'empleado'},
                        {title: 'Ventas Realizadas', data: 'ventasRealizadas'},
                        {title: 'Total Ventas', data: 'totalVentas', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Porcentaje', data: 'porcentaje'},
                        {title: 'Valor comision', data: 'valorComision', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    ],
        });
        modernizarToolbarDataTable('#tablaVentasXUsuario');
    }


    printGastos();
    function printGastos(){
        limpiarToolbarDataTable('#tablaGastos');
        tablaGastos.DataTable({
            destroy: true, // importante si recargas la tabla
            data: gastos,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Descripcion', data: 'descripcion'},
                        {title: 'Tipo Gasto', data: 'tipogasto'},
                        {title: 'Valor', data: 'valor', render: (data:number) => `$${Number(data).toLocaleString()}`},
                    ],
        });
        modernizarToolbarDataTable('#tablaGastos');
    }


    printResumen(0);
    function printResumen(dscto:number){

        const contentUtilidadRF = document.querySelector('#contentUtilidadRF') as HTMLSpanElement;
        const textUtilidadRF = document.querySelector('#textUtilidadRF') as HTMLSpanElement;
        const utilidadRF = document.querySelector('#utilidadRF') as HTMLSpanElement;

        const ventaBruta = Number(resumenVentas[0]?.total_ventas??0);
        const ventaNeta = ventaBruta - dscto;
        const totalIngreso = ventaNeta+totalabonos;
        const totalEgreso = Number(gastos.at(-1)?.valor ?? 0);
        const utilidadTotal = totalIngreso - totalEgreso;
        const margenUtilidadTotal = totalIngreso > 0 ? (utilidadTotal / totalIngreso) * 100 : 0;
        const rentabilidadTotal = totalEgreso > 0 ? (utilidadTotal / totalEgreso) * 100 : 0;

        //cards
        (document.querySelector('#IngresoTotalCard') as HTMLElement).textContent = '$'+totalIngreso.toLocaleString();
        (document.querySelector('#utilidadCard') as HTMLElement).textContent = '$'+utilidadTotal.toLocaleString();
        (document.querySelector('#totalAbonosCard') as HTMLElement).textContent = '$'+totalabonos.toLocaleString();
        (document.querySelector('#carteraPendienteCard') as HTMLElement).textContent = '$'+Number(creditosSeparados[1]?.carteraXCobrar??0).toLocaleString();

        //tabla de resumen financiero
        (document.querySelector('#ventaBrutaRF') as HTMLSpanElement).textContent = '$'+ventaBruta.toLocaleString();
        (document.querySelector('#descuentosRF') as HTMLSpanElement).textContent = '-$'+dscto.toLocaleString();
        (document.querySelector('#ventaNetaRF') as HTMLSpanElement).textContent =  '$'+ventaNeta.toLocaleString();
        (document.querySelector('#totalAbonosRF') as HTMLSpanElement).textContent = '$'+totalabonos.toLocaleString();
        (document.querySelector('#ingresoTotalRF') as HTMLSpanElement).textContent = '$'+totalIngreso.toLocaleString();
        (document.querySelector('#egresosRF') as HTMLSpanElement).textContent = '-$'+totalEgreso.toLocaleString();
        (document.querySelector('#margenUtilidadRF') as HTMLSpanElement).textContent = margenUtilidadTotal.toLocaleString()+'%';
        utilidadRF.textContent = '$'+utilidadTotal.toLocaleString();
        if(utilidadTotal>=0){
            contentUtilidadRF.classList.add('bg-emerald-500/10', 'border-emerald-500/20');
            textUtilidadRF.classList.add('text-emerald-700');
            utilidadRF.classList.add('text-emerald-600');
            contentUtilidadRF.classList.remove('bg-red-500/10', 'border-red-500/20');
            textUtilidadRF.classList.remove('text-red-700');
            utilidadRF.classList.remove('text-red-600');
        }else{
            contentUtilidadRF.classList.remove('bg-emerald-500/10', 'border-emerald-500/20');
            textUtilidadRF.classList.remove('text-emerald-700');
            utilidadRF.classList.remove('text-emerald-600');
            contentUtilidadRF.classList.add('bg-red-500/10', 'border-red-500/20');
            textUtilidadRF.classList.add('text-red-700');
            utilidadRF.classList.add('text-red-600');
        }



        //resumen financiero total de ventas
        limpiarToolbarDataTable('#tablaResumenVentas');
        tablaResumenVentas.DataTable({
            destroy: true, // importante si recargas la tabla
            data: resumenVentas,
            pageLength: 25,
            language: dataTablesLanguage,
            order: [[ 1, 'desc' ]],
            columns: [
                        {title: 'Ventas', data: 'ventas'},
                        {title: 'Total Ventas Productos', data: 'total_ventas', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Total Costo Productos', data: 'total_costo', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Ganancia', data: 'ganancia', render: (data:number) => `$${Number(data).toLocaleString()}`},
                        {title: 'Margen Utilidad', data: 'margenutilidad', render: (data:number) => `${Number(data).toLocaleString()}%`},
                    ],
        });
        modernizarToolbarDataTable('#tablaResumenVentas');

        //tabla resumen financiero de creditos
        const tr = document.createElement('tr') as HTMLTableRowElement;
        while(tablaResumenCreditos?.firstChild)tablaResumenCreditos.removeChild(tablaResumenCreditos.firstChild);
        tr.insertAdjacentHTML('afterbegin', `
          <td>${resumenCreditos[0]?.creditos??0}</td>
          <td>$${Number(resumenCreditos[0]?.capitalTotal??0).toLocaleString()}</td>
          <td>$${Number(resumenCreditos[0]?.costo_total??0).toLocaleString()}</td>
          <td>$${Number(resumenCreditos[0]?.utilidad_comercial??0).toLocaleString()}</td>
          <td>$${Number(resumenCreditos[0]?.utilidad_proyectada??0).toLocaleString()}</td>
          <td>$${Number(resumenCreditos[0]?.valor_pagado??0).toLocaleString()}</td>
          <td>$${Number(resumenCreditos[0]?.utilidad_realizada??0).toLocaleString()}</td>`);
        tablaResumenCreditos?.appendChild(tr);

        //tabla rentabilidad
        (document.querySelector('#ingresoTotal') as HTMLElement).textContent = '$'+totalIngreso.toLocaleString();
        (document.querySelector('#egreso') as HTMLElement).textContent = '$'+totalEgreso.toLocaleString();
        (document.querySelector('#utilidadTotal') as HTMLElement).textContent = '$'+utilidadTotal.toLocaleString();
        (document.querySelector('#margenUtilidadTotal') as HTMLElement).textContent = margenUtilidadTotal.toFixed(2) + '%';
        (document.querySelector('#rentabilidadTotal') as HTMLElement).textContent = rentabilidadTotal.toFixed(2) + '%';
    }


    printBalance.addEventListener('click', ()=>{
      const contentBalanceGeneral = document.querySelector('#contentBalanceGeneral') as HTMLElement;

      const ventana = window.open('', '_blank', 'width=900,height=700');
      if(ventana){
        ventana?.document.write(`
            <html>
                <head>
                    <title>Balance</title>
                    <link rel="stylesheet" href="/build/css/tailwindapp.css">
                    <link rel="stylesheet" href="/build/css/app.css">
                </head>
                <body class="p-6">
                    <div class="my-8 p-6">
                        <h2 class="text-gray-600 text-3xl font-semibold my-12 pb-4 text-center">ðŸ“Š Balance financiero General</h2>
                        <p class="text-2xl text-gray-600 mb-0">PERIODO, <span class="text-gray-900 font-semibold ml-8">Inicio: ${dateStart} - Fin: ${dateEnd}</span></p>    
                    </div>
                    ${contentBalanceGeneral.innerHTML}
                </body>
            </html>
        `);

        ventana.document.close();
        ventana.onload = ()=>{
            ventana?.focus();
            ventana?.print();
            setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana despuÃ©s de unos segundos
        };
      }
    });


    POS.callApiReporte = callApiReporte;

})();
