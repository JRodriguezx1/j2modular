(()=>{
    if(!document.querySelector('.comisiones'))return;

    const POS = (window as any).POS;

    const selectEmpleado = document.querySelector('#selectEmpleado') as HTMLSelectElement;
    const tipo = document.querySelector('#tipo') as HTMLSelectElement;
    const comisiontotalUser = document.querySelector('#comisiontotalUser') as HTMLParagraphElement;
    const comisionTotalUserPagada = document.querySelector('#comisionTotalUserPagada') as HTMLParagraphElement;
    const comisionUserPendiente = document.querySelector('#comisionUserPendiente') as HTMLParagraphElement;
    const btnLiquidar = document.querySelector('#btnLiquidar') as HTMLButtonElement;
    const miDialogoLiquidar = document.querySelector('#miDialogoLiquidar') as HTMLDialogElement;
    const miDialogoDetalleComision = document.querySelector('#miDialogoDetalleComision') as HTMLDialogElement;
    const inputValorLiquidar = (document.querySelector('#valorLiquidar') as HTMLInputElement);
    const origenRetiro = document.querySelectorAll<HTMLInputElement>('input[name="origenRetiro"]');
    let tablaMovimientosComisiones:HTMLElement, comisionPendienteUser = 0, comisionPagadaUser = 0;
    const tablaItemsComision = document.querySelector('#tablaItemsComision tbody') as HTMLBodyElement;

    interface ComisionTotalUser {
        comisiontotal: number;
    }
    interface HistorialPagos {
        movimientos: any[]; // mejor reemplazar por un tipo específico si lo conoces
        totalPagado: number;
    }
    interface ResponseComision {
        comisionTotaluser: ComisionTotalUser;
        historialPagos: HistorialPagos;
    }


    document.addEventListener("click", cerrarDialogoExterno);

    //api para traer valores del usuario a consultar
    async function callApiReporte(dateinicio:string, datefin:string){
        if(selectEmpleado.value === ''){
            msjalertToast('error', '¡Error!', "Seleccionar un empleado de la lista");
            return;
        }

        (document.querySelector('#textNameUser') as HTMLElement).textContent = "Detalle de Movimientos - "+$('#selectEmpleado option:selected').text();

        //(document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
        const datos = new FormData();
        datos.append('fechainicio', dateinicio);
        datos.append('fechafin', datefin);
        datos.append('idempleado', selectEmpleado.value);
        try {
            const url = "/admin/api/comisiones/comisionesXUser"; //llama a la api que esta en comisionescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            printWidgetsUser(resultado);
            
            //(document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
        } catch (error) {
            console.log(error);
        }
    }


    function printWidgetsUser(resultado:ResponseComision){
        comisionPagadaUser = resultado.historialPagos.totalPagado;
        comisionPendienteUser = resultado.comisionTotaluser.comisiontotal-comisionPagadaUser;

        comisiontotalUser.textContent = '$'+Number(resultado.comisionTotaluser.comisiontotal).toLocaleString('es-CO', {minimumFractionDigits:2, maximumFractionDigits:2});
        comisionTotalUserPagada.textContent = '$'+comisionPagadaUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        comisionUserPendiente.textContent = '$'+comisionPendienteUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        printTableMovimientosComisiones(resultado.historialPagos.movimientos);
    }


    btnLiquidar.addEventListener('click', ()=>{ miDialogoLiquidar.showModal(); });


    /// evento a los inputs type radio para elegir origne del gasto = caja o bancos
    origenRetiro.forEach(element =>element.addEventListener('click', showCajasBancos));

    function showCajasBancos(){
      const selectorigen = document.querySelector('input[name="origenRetiro"]:checked');
      if(selectorigen?.id == 'gastocaja'){
        document.querySelector('#showbancos')?.classList.add('hidden');
        document.querySelector('#banco')?.removeAttribute("required");
      }else{
        document.querySelector('#showbancos')?.classList.remove('hidden');
        document.querySelector('#banco')?.setAttribute("required", "");
      }
    }


    document.querySelector('#formCrearUpdateLiquidar')?.addEventListener('submit', async(e:Event)=>{
        e.preventDefault();
        const imprimir = document.querySelector('input[name="printComprobante"]') as HTMLInputElement;
        //validar que no supere el monto total a liquidar comision del usuario
        if(selectEmpleado.value === '' || Number(inputValorLiquidar.value) <= 0 || Number(inputValorLiquidar.value)>Number(comisionPendienteUser)){
            msjalertToast('error', '¡Error!', "Seleccionar un empleado de la lista o valor a liquidar no es valido");
            return;
        }
        const datos = new FormData();
        datos.append('fkusuarioid', selectEmpleado.value);
        datos.append('valor', inputValorLiquidar.value);
        datos.append('mediopago', (document.querySelector('#mediopago') as HTMLInputElement).value);
        datos.append('tipo', tipo.value);
        datos.append('referencia', (document.querySelector('#observacion') as HTMLInputElement).value);
        datos.append('origengasto', document.querySelector<HTMLInputElement>('input[name="origenRetiro"]:checked')!.value);
        datos.append('idcaja', document.querySelector<HTMLInputElement>('#caja')!.value);
        datos.append('idbanco', document.querySelector<HTMLInputElement>('#banco')?.value??'');
        try {
            const url = "/admin/api/comisiones/liquidarComision"; //llama a la api que esta en comisionescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito != undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                const nuevoMovimiento = {
                    fecha: new Date().toISOString(),
                    tipo: `<div class=""> ${tipo.value} <p class=" text-slate-600 text-sm mt-2">${(document.querySelector('#mediopago') as HTMLInputElement).value}</p></div>`,
                    entrada: 0,
                    salida: Number(inputValorLiquidar.value),
                    id: resultado.id
                };
                (tablaMovimientosComisiones as any).row.add(nuevoMovimiento).draw();
                //actualizar widget del usuario
                comisionPagadaUser += nuevoMovimiento.salida;
                comisionPendienteUser -= nuevoMovimiento.salida;
                comisionTotalUserPagada.textContent = '$'+comisionPagadaUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                comisionUserPendiente.textContent = '$'+comisionPendienteUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                //actualizar widget business
                comisionTotalPagadaBusinessDB+=nuevoMovimiento.salida;
                (document.querySelector('#comisionTotalPagadaGlobal') as HTMLParagraphElement).textContent = " - $"+(comisionTotalPagadaBusinessDB.toLocaleString());
                (document.querySelector('#comisionPendienteGlobal') as HTMLParagraphElement).textContent = "$"+(comisionTotalEmpleadosDB-comisionTotalPagadaBusinessDB).toLocaleString();
                if(imprimir.checked)await printComprobantePago(resultado.id);
                (e.target as HTMLFormElement).reset();
                document.querySelector('#showbancos')?.classList.add('hidden');
                document.querySelector('#banco')?.removeAttribute("required");
            }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
            }
            miDialogoLiquidar.close();
        } catch (error) {
            console.log(error);
        }
    });


    function printComprobantePago(idPagoComision:string): Promise<void>{
      return new Promise<void>((resolve, reject) => {
        setTimeout(() => {
          window.open("/admin/printPDFPOSPagoComision?id=" + idPagoComision, "_blank");
          resolve();
        }, 700);
      })
    }


    printTableMovimientosComisiones([]);
    function printTableMovimientosComisiones(movimientos:any){
        tablaMovimientosComisiones = ($('#tablaMovimientosComisiones') as any).DataTable({
            "responsive": true,
            pageLength: 25,
            destroy: true, // importante si recargas la tabla
            data: movimientos,
            order: [[0, "desc"]],
            columns: [
                {title: 'Fecha', data: 'fecha'}, 
                {title: 'Concepto', data: 'tipo', render: (data: any, type: any, row: any) => `<div class=""> ${row.tipo}<p class=" text-slate-600 text-sm mt-2">${row.concepto!==undefined?row.concepto:''}</p></div>`},
                {title: 'Crédito (+)', data: 'entrada', render: (data:number, type: any, row: any) => `<div class="${Number(row.entrada)>0?'text-green-500':''}"><button class="btn-detalle" data-idfactura="${row.id}"> + $${Number(row.entrada).toLocaleString()} </button></div>`},
                {title: 'Débito (-)', data: 'salida', render: (data: any, type: any, row: any) => `<div class="${row.salida>0?'text-red-500':''}"> - $${row.salida.toLocaleString()}</div>`},
                {title: 'Acciones', data: null, render: (data: any, type: any, row: any) => `<button class="btn-eliminar" data-id="${row.entrada == 0&&row.salida!=0?row.id:''}">${row.entrada == 0&&row.salida!=0?'⛔':' - '}</button>`},
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


    //EVENTO A LA TABLA DE MOVIMIENTOS DE COMISIONES
    document.querySelector('#tablaMovimientosComisiones')?.addEventListener("click", (e)=>{
        const target = e.target as HTMLButtonElement;
        let idmovimento = target?.dataset.id;
        if(target?.classList.contains("btn-detalle")){
            detalleFacturaComision(target?.dataset.idfactura);
            miDialogoDetalleComision.showModal();
        }

        if(target?.classList.contains("btn-eliminar")){
            if(!idmovimento || isNaN(Number(idmovimento))){
                msjalertToast('error', '¡Error!', "id del movimiento no es valido");
                return;
            }
            eliminarMovimientoComision(idmovimento, target);
        }
    });


    async function detalleFacturaComision(idfactura:string|undefined){
        while(tablaItemsComision.firstChild)tablaItemsComision.removeChild(tablaItemsComision.firstChild);
        try {
            const url = "/admin/api/comisiones/detalleFacturaComision?id="+idfactura; //llamado a la API REST 
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){ 
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                (document.querySelector('#numFactura') as HTMLParagraphElement).textContent = `factura: ${resultado.factura.prefijo}-${resultado.factura.num_consecutivo}`;
                (document.querySelector('#valorVenta') as HTMLParagraphElement).textContent = `venta: $${Number(resultado.factura.total).toLocaleString()}`;
                (document.querySelector('#nombreCaja') as HTMLParagraphElement).textContent = `${resultado.factura.caja}`;
                (document.querySelector('#fechaPago') as HTMLParagraphElement).textContent = `${resultado.factura.fechapago}`;
                (document.querySelector('#vendedor') as HTMLParagraphElement).textContent = `${resultado.vendedor.nombre} ${(resultado.vendedor.apellido)??''}`;

                resultado.productos.forEach((ins: CarritoItem)=>{
                    const tr = document.createElement('tr') as HTMLTableRowElement;
                    tr.innerHTML = `<td class="text-center">${ins.idproducto}</td>
                                    <td class="text-center">${ins.nombreproducto}</td>
                                    <td class="text-center">${ins.cantidad}</td>
                                    <td class="text-center">$${Number(ins.valorunidad).toLocaleString()}</td>
                                    <td class="text-center">$${Number(ins.total).toLocaleString()}</td>
                                    <td class="text-center">${ins.percentcomision}%</td>
                                    <td class="text-center">$${Number(ins.valorcomision).toLocaleString()}</td>`;
                    tablaItemsComision.prepend(tr);
                });
            }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
    }


    function eliminarMovimientoComision(idmovimento:string, target:HTMLButtonElement){
        const fila = (tablaMovimientosComisiones as any).row(target.closest('tr'));
        Swal.fire({
            customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
            icon: 'question',
            title: 'Desea eliminar el egreso de comision?',
            text: "La comision sera eliminado por completo del sistema.",
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result:any) => {
            if (result.isConfirmed) {
                (async ()=>{
                    try {
                        const url = "/admin/api/comisiones/eliminarMovimientoComision?id="+idmovimento; //llamado a la API REST en reportescontrolador para eliminar un ingreso
                        const respuesta = await fetch(url);
                        const resultado = await respuesta.json();
                        if(resultado.exito !== undefined){ 
                            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                            fila.remove().draw(false);
                            //actualizar widget del usuario
                            comisionPagadaUser -= Number(resultado.valor);
                            comisionPendienteUser += Number(resultado.valor);
                            comisionTotalUserPagada.textContent = '$'+comisionPagadaUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            comisionUserPendiente.textContent = '$'+comisionPendienteUser.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            //actualizar widget business
                            comisionTotalPagadaBusinessDB-=Number(resultado.valor);
                            (document.querySelector('#comisionTotalPagadaGlobal') as HTMLParagraphElement).textContent = "$"+comisionTotalPagadaBusinessDB.toLocaleString();
                            (document.querySelector('#comisionPendienteGlobal') as HTMLParagraphElement).textContent = "$"+(comisionTotalEmpleadosDB-comisionTotalPagadaBusinessDB).toLocaleString();
                        }else{
                            msjalertToast('error', '¡Error!', resultado.error[0]);
                        }
                    } catch (error) {
                        console.log(error);
                    }
                })();
            }
        });
    }

    function cerrarDialogoExterno(event:Event) {
        const f = event.target;
        if (f=== miDialogoLiquidar || f === miDialogoDetalleComision || (f as HTMLInputElement).value === 'salir' || (f as HTMLInputElement).value === 'Cancelar' || (f as HTMLElement).id == 'btnXCerrarModalLiquidar' || (f as HTMLElement).id == 'btnXCerrarModalDetalleComision') {
            miDialogoLiquidar.close();
            miDialogoDetalleComision.close();
        }
    }


    POS.callApiReporte = callApiReporte;
    
})();