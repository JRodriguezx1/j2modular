
(():void=>{

    if(document.querySelector('.detallecliente')){

        const btnTotalCuotas = document.querySelector('#btnTotalCuotas') as HTMLButtonElement;
        const btnPagoDeudaTotal = document.querySelector('#btnPagoDeudaTotal') as HTMLButtonElement;
        const miDialogoTotalCuotas = document.querySelector('#miDialogoTotalCuotas') as HTMLDialogElement;
        const miDialogoPagoTotal = document.querySelector('#miDialogoPagoTotal') as HTMLDialogElement;
        const miDialogoAbono = document.querySelector('#miDialogoAbono') as HTMLDialogElement;
        const tablaCuotas = document.querySelector('#tablaCuotas tbody') as HTMLBodyElement;

        let idcredito:string|undefined, montocuota:string = '', saldopendienteCredito:string='0', indiceFila:HTMLDivElement;
        let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;

        document.addEventListener("click", cerrarDialogoExterno);

        const parametrosURL = new URLSearchParams(window.location.search);
        const id = parametrosURL.get('id');

        function clientesGraficas($url:string, $dato:string){
            if(id!=null&&!Number.isNaN(id))
                (async ()=>{
                    try {
                        const url = $url+id;
                        const respuesta = await fetch(url); 
                        const resultado = await respuesta.json();
                        if($dato == 'comprasXMes')comprasXMesXCliente(resultado);
                        if($dato == 'ventasXCategorias')ventasXCategoriasXCliente(resultado);
                    } catch (error) {
                        console.log(error);
                    }
                })();
        }

        clientesGraficas('/admin/api/clientes/comprasXMesXCliente?id=', 'comprasXMes');
        clientesGraficas('/admin/api/clientes/ventasXCategoriasXCliente?id=', 'ventasXCategorias');
        

        function comprasXMesXCliente(resultado:{periodo:string, ventas_totales:string}[]){
            // Compras por mes
            const ctxMes = (document.getElementById("chartComprasMes") as HTMLCanvasElement).getContext('2d');
            if (ctxMes) {
                new Chart(ctxMes, {
                type: "line",
                data: {
                    labels: resultado.map(x=>x.periodo),
                    datasets: [{
                    label: "Compras",
                    data: resultado.map(x=>x.ventas_totales),
                    borderColor: "rgba(99, 102, 241, 1)",
                    backgroundColor: "rgba(99, 102, 241, 0.2)",
                    tension: 0.4,
                    fill: true,
                    }]
                },
                options: { responsive: true }
                });
            }
        }

        function ventasXCategoriasXCliente(resultado:{categoria:string, idcategoria:string, unidades_vendidas:string, venta_total_categoria:string}[]){
            // Categorías más compradas
            const ctxCat = (document.getElementById("chartCategorias") as HTMLCanvasElement).getContext('2d');
            if (ctxCat) {
                new Chart(ctxCat, {
                type: "doughnut",
                data: {
                    labels: resultado.map(x=>x.categoria),
                    datasets: [{
                        data: resultado.map(x=>x.unidades_vendidas),
                        backgroundColor: [
                            "rgba(99, 102, 241, 0.8)",   // Indigo
                            "rgba(16, 185, 129, 0.8)",   // Emerald
                            "rgba(249, 115, 22, 0.8)",   // Orange
                            "rgba(107, 114, 128, 0.8)"   // Gray
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: "bottom" } }
                }
                });
            }
        }


        btnTotalCuotas.addEventListener('click', ()=>{
            miDialogoTotalCuotas.showModal();
            imprimirTotalCuotasXcliente();
        });


        btnPagoDeudaTotal.addEventListener('click', ()=>{
            if(!Number.isNaN(deudatotalCiente) && Number(deudatotalCiente)>0){
                miDialogoPagoTotal.showModal();
                return;
            }
            Swal.fire({
                customClass: {
                    popup: 'j2-confirm j2-confirm--success',
                    icon: 'j2-confirm__icon',
                    title: 'j2-confirm__title',
                    htmlContainer: 'j2-confirm__text',
                    actions: 'j2-confirm__actions j2-confirm__actions--single',
                    confirmButton: 'j2-confirm__button j2-confirm__button--confirm'
                },
                buttonsStyling: false,
                icon: 'info',
                title: 'Sin deuda pendiente',
                text: 'Este cliente no tiene saldo pendiente por pagar.',
                confirmButtonText: 'OK'
            });
        });


        document.querySelector('#formPagoTotalDeuda')?.addEventListener('submit', async (e:Event)=>{
            e.preventDefault();
            if(id!=null&&!Number.isNaN(id)){
                const datos = new FormData();
                datos.append('idcliente', id);
                datos.append('idcaja', $('#PagoTotal_caja').val() as string);
                datos.append('idmediodepago', $('#PagoTotal_mediopago').val() as string);
                datos.append('valorDeudaTotal', deudatotalCiente);
                try {
                    const url = "/admin/api/creditos/pagarDeudaTotal";  //api en creditoscontrolador
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();
                    if(resultado.exito !== undefined){
                        document.querySelector('#totalDeudaText')!.textContent = '$0';
                        document.querySelectorAll<HTMLTableCellElement>('.pendiente').forEach(td=>{
                            const tr = td.parentElement;
                            if(tr){
                                tr.children[5].textContent = '$0';
                                tr?.children[5].classList.remove('!text-red-600');
                                tr.children[6].innerHTML = '<span class="table-status table-status--success">Finalizado</span>';
                                tr.children[6].classList.remove('is-open', 'pendiente');
                                tr.children[6].classList.add('is-done');
                            }
                        });
                        miDialogoPagoTotal.close();
                        Swal.fire({
                            customClass: {
                                popup: 'j2-confirm j2-confirm--success',
                                icon: 'j2-confirm__icon',
                                title: 'j2-confirm__title',
                                htmlContainer: 'j2-confirm__text',
                                actions: 'j2-confirm__actions j2-confirm__actions--single',
                                confirmButton: 'j2-confirm__button j2-confirm__button--confirm'
                            },
                            buttonsStyling: false,
                            icon: 'success',
                            title: 'Pago registrado',
                            text: resultado.exito[0],
                            confirmButtonText: 'OK'
                        });
                    }else{
                        Swal.fire({
                            customClass: {
                                popup: 'j2-confirm j2-confirm--danger',
                                icon: 'j2-confirm__icon',
                                title: 'j2-confirm__title',
                                htmlContainer: 'j2-confirm__text',
                                actions: 'j2-confirm__actions j2-confirm__actions--single',
                                confirmButton: 'j2-confirm__button j2-confirm__button--danger'
                            },
                            buttonsStyling: false,
                            icon: 'error',
                            title: 'No se pudo registrar',
                            text: resultado.error[0],
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.log(error);
                }
            }
        });



        async function imprimirTotalCuotasXcliente(){
            if(id!=null&&!Number.isNaN(id)){
                try {
                    const url = `/admin/api/totalCuotasXcliente?id=${id}`; //llamado a la API REST 
                    const respuesta = await fetch(url); 
                    const resultado:{capital:string, fechafin:string, fechainicio:string, fechapagado:string, idestadocreditos:string, interestotal:string, num_orden:string, numerocuota:string, valorpagado:string}[] = await respuesta.json();

                    while(tablaCuotas.firstChild)tablaCuotas.removeChild(tablaCuotas.firstChild);
                    resultado.forEach(c=>{
                        const tr = document.createElement('tr') as HTMLTableRowElement;
                        tr.innerHTML = `<td class="text-center">${c.num_orden}</td>
                                        <td class="text-center">$ ${(Number(c.capital)+Number(c.interestotal)).toLocaleString()}</td>
                                        <td class="text-center">${c.numerocuota}</td>
                                        <td class="text-center">$ ${Number(c.valorpagado).toLocaleString()}</td>
                                        <td class="text-center">${c.fechapagado}</td>
                                        <td class="text-center">${c.idestadocreditos=='1'?'Finalizado':c.idestadocreditos=='2'?'Abierto':'Anulado'}</td>`;
                        tablaCuotas.prepend(tr);
                    });

                } catch (error) {
                    console.log(error);
                }
            }
        }


        ////////////// Evento a la tabla cuotas ///////////////
        document.querySelector('#tablaCreditos')?.addEventListener("click", (e:Event)=>{ //evento click sobre toda la tabla
            const target = e.target as HTMLButtonElement;
            if(target?.classList.contains("abonarCredito") || target?.parentElement?.classList.contains("abonarCredito") )abonarCredito(target);
            if(target?.classList.contains("anularCredito") || target?.parentElement?.classList.contains("anularCredito"))anularCredito(target);
        });


        function abonarCredito(target: HTMLButtonElement){
            const element = target.closest('div');
            if(!element)return;
            idcredito = element?.id;
            montocuota = element?.dataset.montocuota||'0';
            if(idcredito == undefined || Number.isNaN(idcredito))return
            saldopendienteCredito = element?.dataset.saldopendiente!;
            if(Number.isNaN(saldopendienteCredito) || Number(saldopendienteCredito)<=0)return;
            miDialogoAbono.showModal();
            document.querySelector('#numCredito')!.textContent = "Credito No.: "+idcredito;
            document.querySelector('#saldopendiente')!.textContent = "Saldo pendiente: "+saldopendienteCredito;
            indiceFila = (element.parentElement?.parentElement) as HTMLTableRowElement;
        }

        document.querySelector('#formrealizarAbono')?.addEventListener('submit', async (e:Event)=>{
            e.preventDefault();
            const imprimirAbono = document.querySelector('#imprimirAbono') as HTMLInputElement;
            const valorabono = (document.querySelector('#abono') as HTMLInputElement).value;
            const datos = new FormData();
            datos.append('id_credito', idcredito||'');
            datos.append('cajaid', $('#abono_caja').val() as string);
            datos.append('mediopagoid', $('#abono_mediopago').val() as string);
            datos.append('valorpagado', valorabono);
            datos.append('montocuota', montocuota);
            try {
                const url = "/admin/api/creditos/registrarAbonoFromCli";  //api en creditoscontrolador
                const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                const resultado = await respuesta.json();
                if(resultado.exito !== undefined){
                    deudatotalCiente = (Number(deudatotalCiente)-Number(valorabono))+'';
                    saldopendienteCredito = (Number(saldopendienteCredito)-Number(valorabono))+'';
                    document.querySelector('#totalDeudaText')!.textContent = '$'+deudatotalCiente;
                    indiceFila.children[5].textContent = '$'+saldopendienteCredito;
                    if((Number(saldopendienteCredito))== 0){
                        indiceFila.children[5].classList.remove('!text-red-600');
                        indiceFila.children[6].innerHTML = '<span class="table-status table-status--success">Finalizado</span>';
                        indiceFila.children[6].classList.remove('is-open', 'pendiente');
                        indiceFila.children[6].classList.add('is-done');
                    }else{
                        indiceFila.children[6].innerHTML = '<span class="table-status table-status--warning">Abierto</span>';
                    }
                    (indiceFila.children[7].children[0] as HTMLDivElement).dataset.saldopendiente = saldopendienteCredito;
                    miDialogoAbono.close();
                    Swal.fire({
                        customClass: {
                            popup: 'j2-confirm j2-confirm--success',
                            icon: 'j2-confirm__icon',
                            title: 'j2-confirm__title',
                            htmlContainer: 'j2-confirm__text',
                            actions: 'j2-confirm__actions j2-confirm__actions--single',
                            confirmButton: 'j2-confirm__button j2-confirm__button--confirm'
                        },
                        buttonsStyling: false,
                        icon: 'success',
                        title: 'Abono registrado',
                        text: resultado.exito[0],
                        confirmButtonText: 'OK'
                    });
                    if(resultado.idcuota && imprimirAbono.checked)printPOSComprobanteAbono(resultado.idcuota);
                }else{
                    miDialogoAbono.close();
                    Swal.fire({
                        customClass: {
                            popup: 'j2-confirm j2-confirm--danger',
                            icon: 'j2-confirm__icon',
                            title: 'j2-confirm__title',
                            htmlContainer: 'j2-confirm__text',
                            actions: 'j2-confirm__actions j2-confirm__actions--single',
                            confirmButton: 'j2-confirm__button j2-confirm__button--danger'
                        },
                        buttonsStyling: false,
                        icon: 'error',
                        title: 'No se pudo registrar',
                        text: resultado.error[0],
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.log(error);
            }
        });


        async function printPOSComprobanteAbono(idabono:string|undefined){
            if(idabono==undefined)return;
            try{
                const url = "/admin/api/creditos/getAbono?id="+idabono; //llamado a la API REST - creditocontrolador 
                const respuesta = await fetch(url); 
                const resultado = await respuesta.json();
                const isAndroid = /Android/i.test(navigator.userAgent);
                if(printerBT === '1'){
                const builder = new ticketAbonoBuilder(resultado);
                const ticket = await builder.generate(true); //true para version buffer bytes
                const base64 = bytesToBase64(ticket);
                if(isAndroid)window.location.href = `rawbt:base64,${base64}`;
                //descargar .bin a equipo
                /*const blob = new Blob([ticket], { type: 'application/octet-stream' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'ticket.bin';
                a.click();
                URL.revokeObjectURL(url);*/
                }
            }catch(error){
                console.log(error);
            }

            if(!isNaN(Number(idabono)))
                window.open("/admin/printPDFAbonoCredito?id=" + idabono, "_blank"); //controlador printcontrolador
        }


        function anularCredito(target: HTMLButtonElement){
            const idabono = target.parentElement?.id;
            const fila = target.closest('tr');
            if(idabono==undefined)return;
            Swal.fire({
                customClass: {
                    popup: 'j2-confirm j2-confirm--danger',
                    icon: 'j2-confirm__icon',
                    title: 'j2-confirm__title',
                    htmlContainer: 'j2-confirm__text',
                    actions: 'j2-confirm__actions',
                    confirmButton: 'j2-confirm__button j2-confirm__button--danger',
                    cancelButton: 'j2-confirm__button j2-confirm__button--cancel'
                },
                buttonsStyling: false,
                icon: 'question',
                title: 'Anular credito',
                html: '<strong>Esta accion no se puede deshacer.</strong><br>El credito sera anulado definitivamente.',
                showCancelButton: true,
                confirmButtonText: 'Si, anular',
                cancelButtonText: 'No',
            }).then((result:any) => {
                if (result.isConfirmed) {
                    /*(async ()=>{ 
                        try {
                            const url = "/admin/api/creditos/anularAbono?id="+idabono;
                            const respuesta = await fetch(url); 
                            const resultado = await respuesta.json();
                            if(resultado.exito !== undefined){
                                fila?.remove();
                                Swal.fire(resultado.exito[0], '', 'success');
                            }else{
                                Swal.fire(resultado.error[0], '', 'error');
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    })();//cierre de async()*/
                }
            });
        }


        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
            if (f=== miDialogoTotalCuotas || f === miDialogoPagoTotal || f === miDialogoAbono || (f as HTMLElement).id === 'btnCerrarTotalCuotas' || (f as HTMLElement).id === 'btnCerrarPagoTotal' || (f as HTMLElement).id === 'btnXCerrarModalAbono' || (f as HTMLButtonElement).value == 'Salir'){
                miDialogoTotalCuotas.close();
                miDialogoPagoTotal.close();
                miDialogoAbono.close();
            }
        }

    }

})();
