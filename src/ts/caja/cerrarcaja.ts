(():void=>{

  if(document.querySelector('.cerrarcaja')){
    const modalArqueocaja:any = document.querySelector("#modalArqueocaja");
    const Modalcerrarcaja:any = document.querySelector("#Modalcerrarcaja");
    const modalCambiarCaja:any = document.querySelector("#modalCambiarCaja");
    const btnArqueocaja = document.querySelector<HTMLButtonElement>("#btnArqueocaja");
    const btnCerrarcaja = document.querySelector<HTMLButtonElement>("#btnCerrarcaja");
    const btnImprimirDetalleCaja = document.querySelector('#btnImprimirDetalleCaja') as HTMLButtonElement;
    const btnCambiarCaja = document.querySelector<HTMLButtonElement>("#btnCambiarCaja");
    const btnVerCierreWeb = document.querySelector('#btnVerCierreWeb') as HTMLButtonElement;
    const inputsmediospago = document.querySelectorAll<HTMLInputElement>('.inputmediopago');
    const formArqueocaja = document.querySelector('#formArqueocaja') as HTMLFormElement;
    const formCambiarCaja = document.querySelector('#formCambiarCaja') as HTMLFormElement;
    const inputsDenominaciones = Array.from(document.querySelectorAll<HTMLInputElement>('.denominacion'));
    const arqueoTotal = document.querySelector<HTMLElement>('#arqueoTotal');
    const formatoPesos = new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      maximumFractionDigits: 0
    });

    btnArqueocaja?.addEventListener('click', ():void=>{
      modalArqueocaja.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    btnCerrarcaja?.addEventListener('click', ():void=>{
      Modalcerrarcaja.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    btnCambiarCaja?.addEventListener('click', ():void=>{
      modalCambiarCaja.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    inputsDenominaciones.forEach(input=>{
      input.addEventListener('input', actualizarVistaArqueo);
    });
    actualizarVistaArqueo();
    

    //////////////////     Arqueo de caja      //////////////////////
    formArqueocaja?.addEventListener('submit', (e:Event)=>{
      e.preventDefault();
      sumatoriaDenominaciones();
      const datos = new FormData(formArqueocaja);
      datos.append('idcierrecaja', document.querySelector('#idCierrecaja')?.textContent!);
      (async ()=>{
        try {
            const url = "/admin/api/arqueocaja";  //api llamada en cajacontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              modalArqueocaja.close();
              document.removeEventListener("click", cerrarDialogoExterno);
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
      
    });

    function sumatoriaDenominaciones():void{
      const ef = document.querySelector('#Efectivo') as HTMLInputElement;
      const sum = inputsDenominaciones.reduce((total, denominacion)=>(Number(denominacion.dataset.moneda)*valorEntero(denominacion.value))+total, 0);
      ef.value = sum+'';
      const eventInput = new Event('input');  //crea un evento
      ef.dispatchEvent(eventInput); // se dispara el evento de manera manual, --->abajo<--, es decir cuando se introduce un valor en el campo efectivo se dispara el evento.
    }

    function actualizarVistaArqueo():void{
      const totalArqueado = inputsDenominaciones.reduce((total, input)=>{
        const subtotal = Number(input.dataset.moneda) * valorEntero(input.value);
        const subtotalElemento = input.closest('.arqueo-row')?.querySelector<HTMLElement>('.arqueo-row-total');
        if(subtotalElemento) subtotalElemento.textContent = formatoPesos.format(subtotal);
        return total + subtotal;
      }, 0);

      if(arqueoTotal) arqueoTotal.textContent = formatoPesos.format(totalArqueado);
    }

    function valorEntero(valor:string):number{
      return Number(valor.replace(/[^\d]/g, '')) || 0;
    }

    ///////////  eventos a los inputs medios de pago de declarar valores /////////////
    inputsmediospago.forEach(m=>{
      m.addEventListener('input', (e)=>{  
        const inputmediopago = (e.target as HTMLInputElement);
        const valormediopagodeclarado:number =  parseInt((e.target as HTMLInputElement).value.replace(/[,.]/g, ''));
        (async ()=>{
          const datos = new FormData();
          datos.append('id_mediopago', inputmediopago.dataset.idmediopago+'');
          datos.append('nombremediopago', inputmediopago.name);
          datos.append('valordeclarado', valormediopagodeclarado+'');
          datos.append('idcierrecaja', document.querySelector('#idCierrecaja')?.textContent!);
          try {
              const url = "/admin/api/declaracionDinero";  //api llamada en cajacontrolador.php
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();
              if(resultado.exito !== undefined){
                inputmediopago.style.color = "#02db02";
                inputmediopago.style.fontWeight = "500";
                actualizarAnalisis(inputmediopago.dataset.idmediopago!, valormediopagodeclarado+'', inputmediopago.name);
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();
      });
    });


    function actualizarAnalisis(id: string, valordeclarado: string, mediopago:string):void{
      const coldeclarado = document.querySelector(`.coldeclarado[data-mediopagoid="${id}"]`);
      if(coldeclarado){
        const coldif = coldeclarado.nextElementSibling;
        coldeclarado.textContent = Number(valordeclarado).toLocaleString('es-ES', { useGrouping: true });
        const colsistem = coldeclarado.previousElementSibling?.textContent?.replace(/[.,]/g, '');
        coldif!.textContent = ((Number(valordeclarado) - Number(colsistem))).toLocaleString('es-ES', { useGrouping: true }); 
      }else{
        const cuerpoanalisis = document.querySelector('.cuerpoanalisis');
        cuerpoanalisis?.insertAdjacentHTML('beforeend', `
          <tr class="${mediopago=='Efectivo'?'!border-2 !border-indigo-600':''}">
            <td class="">${mediopago}</td> 
            <td class="colsistem">0</td>
            <td class="coldeclarado" data-mediopagoid="${id}">${Number(valordeclarado).toLocaleString('es-ES', { useGrouping: true })}</td>
            <td class="coldif">${Number(valordeclarado).toLocaleString('es-ES', { useGrouping: true })}</td>
          </tr>`
        );
      }
    }

    function confirmarcierre(){
      const datos = new FormData();
      
      datos.append('idcierrecaja', document.querySelector('#idCierrecaja')?.textContent!);
      
      (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
      Modalcerrarcaja.close();
      document.removeEventListener("click", cerrarDialogoExterno);
      (async ()=>{
        try {

            const url = "/admin/api/cierrecajaconfirmado";  //api llamada en cajacontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              setTimeout(() => {
                (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
                window.location.href = `/admin/caja/detallecierrecaja?id=${resultado.ultimocierre[0]}`;
              }, 1600);
            }else{
              (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
    }

    const idimprimircierrecaja = document.querySelector('#idCierrecaja')?.textContent;
    ///////// imprimir detalle cierre
    btnImprimirDetalleCaja?.addEventListener('click', ()=>{
        const ventana = window.open('/printdetallecierre?id='+idimprimircierrecaja, '_blank');
        if(ventana){
          ventana.onload = ()=>{
            ventana?.focus();
            ventana?.print();
            setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana después de unos segundos
          };
        }
    });
    ///////// ver detalle cierre en web
    btnVerCierreWeb?.addEventListener('click', ()=>{
        const ventana = window.open('/printdetallecierre?id='+idimprimircierrecaja, '_blank');
        if(ventana)ventana.onload = ()=>ventana?.focus();
    });

    //////////////////     Cambio de caja      //////////////////////
    formCambiarCaja?.addEventListener('submit', (e:Event)=>{
      e.preventDefault();
      const datos = new FormData();
      datos.append('idcaja', $('#CambiarCaja').val()as string); //select del modal de cambio de caja
      (async ()=>{
        try {
            const url = "/admin/api/datoscajaseleccionada";  //api llamada en cajacontrolador.php para traer datos de venta de la caja seleccionada 
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              modalCambiarCaja.close();
              document.removeEventListener("click", cerrarDialogoExterno);
              console.log(resultado);
              //cambiar el id del cierre de caja en id="#idCierrecaja"
              document.querySelector('#nombreCaja')!.textContent = $('#CambiarCaja option:selected').text();
              document.querySelector('#idCierrecaja')!.textContent = resultado.ultimocierre.id;
              //mostar datos de la caja seleccionada
              printdiscriminarmediospago(resultado.discriminarmediospagos);
              printindicadores(resultado.ultimocierre);
              printdetalleimpuesto(resultado.discriminarimpuesto);
              //printgastosdelacaja();
              //printdiscriminadoabonos();
              printsobrantesfaltantes(resultado.sobrantefaltante);
              printventasxusuarios(resultado.ventasxusuarios);
              printventas(resultado.facturas);
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
      
    });

    function printdiscriminarmediospago(data: Record<string, {idmediopago:string, mediopago:string, valor:string}>){
      const tbodyMediosPago = document.querySelector('#tablaMediosPago tbody') as HTMLTableElement;
      while(tbodyMediosPago.firstChild)tbodyMediosPago.removeChild(tbodyMediosPago.firstChild);
      
      Object.values(data).forEach((row:{idmediopago:string, mediopago:string, valor:string})=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="">${row.mediopago}</td> 
                        <td class=""><strong>$ </strong>${Number(row.valor).toLocaleString()}</td>
                      `;
        tbodyMediosPago.appendChild(tr);
      });
    }

    function printindicadores(obj:{id:string, idcaja:string, id_usuario:string, nombrecaja:string, nombreusuario:string, fechainicio:string, fechacierre:string, ncambiosaventa:string, totalcotizaciones:string, totalfacturas:string, facturaselectronicas:string, facturaspos:string, valorfe:string, descuentofe:string, valorpos:string, descuentopos:string, basecaja:string, ventasenefectivo:string, creditocapital:string, creditos:string, abonostotales:string, abonosenefectivo:string, abonoscreditos:string, abonosseparados:string, gastoscaja:string, gastosbanco:string, dineroencaja:string, domicilios:string, ndomicilios:string, realencaja:string, ingresoventas:string, totaldescuentos:string, realventas:string, valorimpuestototal:string, basegravable:string, estado:string}){
      const basecaja = Number(obj.basecaja);
      const ventasenefectivo = Number(obj.ventasenefectivo);
      const gastoscaja = Number(obj.gastoscaja);
      const domicilios = Number(obj.domicilios);
      const ventasContado = Number(obj.ingresoventas); //ventas de contado ya esta aplicado los descuentos
      const ventasACredito = Number(obj.creditocapital); //ventas a credito, aqui ya esta aplicado los descuentos
      const totaldescuentos = Number(obj.totaldescuentos);
      const abonosenefectivo = Number(obj.abonosenefectivo);
      const abonostotales = Number(obj.abonostotales);

      document.querySelector('#basecajaResumen')!.textContent = '$'+basecaja.toLocaleString();
      document.querySelector('#gastoscajaResumen')!.textContent = '$'+gastoscaja.toLocaleString();
      document.querySelector('#domiciliosResumen')!.textContent = '$'+domicilios.toLocaleString();
      document.querySelector('#ingresoventasResumen')!.textContent = '$'+(ventasContado+ventasACredito).toLocaleString();
      document.querySelector('#totalfacturasResumen')!.textContent = obj.totalfacturas;
      document.querySelector('#totalcotizacionesResumen')!.textContent = obj.totalcotizaciones;

      document.querySelector('#baseIngresoCaja')!.textContent = '+ $'+basecaja.toLocaleString();
      document.querySelector('#ventasEfectivo')!.textContent = '+ $'+ventasenefectivo.toLocaleString();
      document.querySelector('#abonosEfectivo')!.textContent = '+ $'+abonosenefectivo.toLocaleString();
      document.querySelector('#gastosCaja')!.textContent = '- $'+gastoscaja.toLocaleString();
      document.querySelector('#dineroCaja')!.textContent = '= $'+(basecaja+ventasenefectivo-gastoscaja).toLocaleString();
      document.querySelector('#domicilios')!.textContent = '- $'+domicilios.toLocaleString();
      document.querySelector('#realCaja')!.textContent = '= $'+(basecaja+ventasenefectivo-gastoscaja-domicilios).toLocaleString();
      
      document.querySelector('#ingresoVentasTotal')!.textContent = '+ $'+(ventasContado).toLocaleString();
      document.querySelector('#creditos')!.textContent = '+ $'+ventasACredito.toLocaleString();
      document.querySelector('#totalDescuentos')!.textContent = ' $'+totaldescuentos.toLocaleString();
      document.querySelector('#totaldeventas')!.textContent = ' $'+(ventasContado+ventasACredito).toLocaleString();
      document.querySelector('#abonosTotales')!.textContent = '+ $'+abonostotales.toLocaleString();
      document.querySelector('#ingresoCajaDelDia')!.textContent = ' $'+(ventasContado+abonostotales).toLocaleString();
      document.querySelector('#totalGastosCaja')!.textContent = '- $'+gastoscaja.toLocaleString();
      document.querySelector('#totalDomicilios')!.textContent = '= $'+(domicilios).toLocaleString();
      document.querySelector('#realVentas')!.textContent = '= $'+(ventasContado+ventasACredito-domicilios-gastoscaja).toLocaleString();
      document.querySelector('#totalBaseGravable')!.textContent = '+ $'+Number(obj.basegravable).toLocaleString();
      document.querySelector('#impuestoTotal')!.textContent = '+ $'+Number(obj.valorimpuestototal).toLocaleString();
      document.querySelector('#otrosGastosBancarios')!.textContent = '+ $'+Number(obj.gastosbanco).toLocaleString();
      
      document.querySelector('#cantidadFacturasFE')!.textContent = obj.facturaselectronicas;
      document.querySelector('#cantidadFacturasPOS')!.textContent = obj.facturaspos;

      document.querySelector('#abonoscreditos')!.innerHTML = `<td class=""><strong>$ </strong>${Number(obj.abonoscreditos).toLocaleString()}</td>`;
      document.querySelector('#abonosseparados')!.innerHTML = `<td class=""><strong>$ </strong>${Number(obj.abonosseparados).toLocaleString()}</td>`;
    }

    function printdetalleimpuesto(data: {basegravable:string, impuestos:string, tarifa:string, valorimpuesto:string}[]){
      const tablaDetalleImpuestos = document.querySelector('#tablaDetalleImpuestos tbody') as HTMLTableElement;
      while(tablaDetalleImpuestos.firstChild)tablaDetalleImpuestos.removeChild(tablaDetalleImpuestos.firstChild);
      data.forEach(row=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="">${row.tarifa}</td> 
                        <td class=""><strong>$ </strong>${Number(row.basegravable).toLocaleString()}</td>
                        <td class=""><strong>$ </strong>${Number(row.valorimpuesto).toLocaleString()}</td>
                      `;
        tablaDetalleImpuestos.appendChild(tr);
      });
    }

    function printsobrantesfaltantes(array: {id_mediopago:string, idcierrecajaid:string, nombremediopago:string, valordeclarado:number, valorsistema:number}[]){
      const sobranteFaltante = document.querySelector('#sobranteFaltante tbody') as HTMLTableElement;
      while(sobranteFaltante.firstChild)sobranteFaltante.removeChild(sobranteFaltante.firstChild);
      array.forEach(row=>{
        const tr = document.createElement('tr');
        if(row.nombremediopago=='Efectivo')tr.classList.add('!border-2', '!border-indigo-600');
        tr.innerHTML = `<td class="">${row.nombremediopago}</td> 
                        <td class="colsistem">${row.valorsistema.toLocaleString()}</td>
                        <td class="coldeclarado" data-mediopagoid="${row.id_mediopago}">${row.valordeclarado.toLocaleString()}</td>
                        <td class="coldif">${(row.valordeclarado - row.valorsistema).toLocaleString()}</td>
                      `;
        sobranteFaltante.appendChild(tr);
      });
    }

    function printventasxusuarios(array: {Nombre:string, N_ventas:string, ventas:string}[]){
      const ventasxusuarios = document.querySelector('#ventasXUsuario tbody') as HTMLTableElement;
      while(ventasxusuarios.firstChild)ventasxusuarios.removeChild(ventasxusuarios.firstChild);
      array.forEach(row=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="">${row.Nombre}</td>
                        <td class="">${row.N_ventas}</td>
                        <td class=""><strong>$ </strong>${Number(row.ventas).toLocaleString()}</td>
                      `;
        ventasxusuarios.appendChild(tr);
      });
    }

    function printventas(array: {id:string, idcliente:string, idvendedor:string, idcaja:string, idconsecutivo:string, iddireccion:string, idtarifazona:string, idcierrecaja:string, cliente:string, vendedor:string, caja:string, tipofacturador:string, mediosdepago:{id:string, id_factura:string, mediopago:string, valor:string}[], direccion:string, tarifazona:string, totalunidades:string, recibido:string, transaccion:string, tipoventa:string, cotizacion:string, estado:string, cambioaventa:string, subtotal:string, base:string, valorimpuestototal:string, dctox100:string, descuento:string, total:string, observacion:string, departamento:string, ciudad:string, entrega:string, valortarifa:string, fechacreacion:string, fechapago:string}[]){
      const tablaVentas = document.querySelector('#tablaVentas tbody') as HTMLTableElement;
      while(tablaVentas.firstChild)tablaVentas.removeChild(tablaVentas.firstChild);
      array.forEach((row, i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="">${i}</td>        
                        <td class="">${row.fechapago}</td> 
                        <td class="">${row.cliente}</td> 
                        <td class="">${row.id}</td>
                        <td>
                            <div data-estado="${row.estado}" data-totalpagado="${row.total}" id="${row.id}" class="mediosdepago max-w-full flex flex-wrap gap-2">
                                ${row.mediosdepago.map(mp=>`<button class="cierre-badge cierre-badge--neutral">${mp.mediopago}</button>`).join(' ')}
                            </div>
                        </td>
                        <td><span class="cierre-badge ${row.estado=='Paga'?'cierre-badge--success':'cierre-badge--info'}">${row.estado}</span></td>
                        <td class="cierre-number">$ ${Number(row.subtotal).toLocaleString()}</td>
                        <td class="cierre-number">$ ${Number(row.total).toLocaleString()}</td>
                        <td class="accionestd"><div class="acciones-btns" id="${row.id}">
                                <a class="cierre-table-action" href="/admin/caja/ordenresumen?id=${row.id}">Ver</a> <button class="cierre-badge cierre-badge--neutral"><i class="fa-solid fa-print"></i></button>
                            </div>
                        </td>
                      `;
        tablaVentas.appendChild(tr);
      });
    }


    function cerrarDialogoExterno(event:Event) {
      if (event.target === modalArqueocaja || event.target === Modalcerrarcaja || event.target === modalCambiarCaja || (event.target as HTMLInputElement).value === 'Cancelar' || (event.target as HTMLElement).closest('.salircerrarcaja') || (event.target as HTMLElement).closest('.finCerrarcaja')) {
          modalArqueocaja.close();
          Modalcerrarcaja.close();
          modalCambiarCaja.close();
          document.removeEventListener("click", cerrarDialogoExterno);

          if((event.target as HTMLElement).closest('.finCerrarcaja')){  //Cuando se hace el cierre de caja
            confirmarcierre();
          }
      }
    }
  }

})();
