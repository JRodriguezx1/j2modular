(()=>{

    if(document.querySelector('.configsuscripcion')){
        const btnDetalleSuscriptor = document.getElementById('btnDetalleSuscriptor') as HTMLButtonElement;
        const btnRegistrarPago = document.getElementById('btnRegistrarPago') as HTMLButtonElement;
        const miDialogoRegistrarPago:any = document.querySelector("#miDialogoRegistrarPago");
        const miDialogoDetalleSuscripcion:any = document.querySelector("#miDialogoDetalleSuscripcion");
        const estadoText = document.querySelector('#estadoText') as HTMLParagraphElement;

        document.addEventListener("click", cerrarDialogoExterno);

        btnDetalleSuscriptor?.addEventListener('click',()=>{
            miDialogoDetalleSuscripcion.showModal();
        });
        btnRegistrarPago?.addEventListener('click',()=>{
            miDialogoRegistrarPago.showModal();
        });


        document.querySelector('#formDetalleSuscripcion')?.addEventListener('submit', async(e:Event)=>{
            e.preventDefault();
            const form = e.target as HTMLFormElement;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const detalleSuscrip = {
                idplan: data.idplan,
                valorplan: Number(data.valorplan)||0,
                fecha_corte: data.fecha_corte,
                estado: data.estado,
                descuento: Number(data.descuento)||0,
                detalledescuento: data.detalle_descuento,
                cargo: Number(data.cargo)||0,
                detallecargo: data.detalle_cargo,
            }
            try {
                const url = "/admin/api/suscripcion/detalleSuscripcion"; //llamado a la API REST suscripcioncontrolador.php actualiza la sucursal
                const respuesta = await fetch(url, {
                                            method: 'POST', 
                                            headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                            body: JSON.stringify(detalleSuscrip) 
                                        });
                const resultado = await respuesta.json();
                console.log(resultado);
                if(resultado.exito != undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    actualizarDetalleSuscripcion(detalleSuscrip);
                }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                }

            } catch (error) {
                console.log(error);
            }
           miDialogoDetalleSuscripcion.close();
        });


        document.querySelector('#formRegistrarPago')?.addEventListener('submit', async(e:Event)=>{
            e.preventDefault();
            const form = e.target as HTMLFormElement;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const pago = {
                cantidad_plan: data.cantidad_plan,
                valor_pagado: Number(data.valor_pagado)||0,
                mediopago: data.medio_pago,
                descripcion: data.descripcion,
            }
            try {
                const url = "/admin/api/suscripcion/registrarPago"; //llamado a la API REST suscripcioncontrolador.php
                const respuesta = await fetch(url, {
                                            method: 'POST', 
                                            headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                            body: JSON.stringify(pago) 
                                        });
                const resultado = await respuesta.json();
                console.log(resultado);
                if(resultado.exito != undefined){
                    msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                    actualizarDetalleSuscripcion(resultado.sucursal);
                }else{
                    msjalertToast('error', '¡Error!', resultado.error[0]);
                }
                miDialogoRegistrarPago.close();
            } catch (error) {
                console.log(error);
            }
    
        });


        function actualizarDetalleSuscripcion(detalleSuscrip:{valorplan: number, fecha_corte: FormDataEntryValue, estado: FormDataEntryValue, descuento: number, detalledescuento: FormDataEntryValue, cargo: number, detallecargo: FormDataEntryValue}){
            const formatoMoneda = (valor:number) => '$'+valor.toLocaleString('es-CO');
            const total = detalleSuscrip.valorplan + detalleSuscrip.cargo - detalleSuscrip.descuento;
            (document.querySelector('#fecha_corteText') as HTMLParagraphElement).textContent = detalleSuscrip.fecha_corte as string;
            (document.querySelector('#valorplanText') as HTMLParagraphElement).textContent = formatoMoneda(detalleSuscrip.valorplan);
            (document.querySelector('#valorplanResumen') as HTMLParagraphElement).textContent = formatoMoneda(detalleSuscrip.valorplan);
            (document.querySelector('#totalSuscripcionText') as HTMLParagraphElement).textContent = formatoMoneda(total);
            estadoText.textContent = detalleSuscrip.estado == '1'?"Activo":"Suspendido";
            if (detalleSuscrip.estado === '1') {
                estadoText.classList.replace('bg-red-100', 'bg-green-100');
                estadoText.classList.replace('text-red-700', 'text-green-700');
                estadoText.classList.replace('border-red-300', 'border-green-300');
            } else {
                estadoText.classList.replace('bg-green-100', 'bg-red-100');
                estadoText.classList.replace('text-green-700', 'text-red-700');
                estadoText.classList.replace('border-green-300', 'border-red-300');
            }
        }


        function cerrarDialogoExterno(event:Event) {
            const f = event.target;
            if (f=== miDialogoRegistrarPago || f === miDialogoDetalleSuscripcion || (f as HTMLInputElement).value === 'Cancelar' || (f as HTMLButtonElement).closest('.btnXCerrarRegistroPago') ) {
                miDialogoRegistrarPago.close();
                miDialogoDetalleSuscripcion.close();
            }
        }
        
    }


})();
