(():void=>{
  if(document.querySelector('.caja')){
    const modalGastosIngresos:any = document.querySelector("#gastosIngresos");
    const modalcambioMedioPago:any = document.querySelector("#cambioMedioPago");
    const miDialogoAbrirCaja:any = document.querySelector("#miDialogoAbrirCaja");
    const btnGastosingresos = document.querySelector<HTMLButtonElement>("#btnGastosingresos");
    const btnAbrirCajon = document.querySelector<HTMLButtonElement>("#btnAbrirCajon");
    const operacion = document.querySelector('#operacion') as HTMLSelectElement;
    const origengasto = document.querySelectorAll<HTMLInputElement>('input[name="origengasto"]');
    const mediosPago = document.querySelectorAll<HTMLInputElement>('.mediopago'); //todos los medios de pago del modal
    const totalPagado = document.querySelector('#totalPagado') as HTMLSpanElement;
    const numfactura = document.querySelector('#numfactura') as HTMLLabelElement;
    const inputAbrirCaja = document.querySelector('#inputAbrirCaja') as HTMLInputElement;
    const formGastosingresos = document.querySelector('#formGastosingresos') as HTMLFormElement;
    const btnEnviargastosingresos = document.querySelector('#btnEnviargastosingresos') as HTMLInputElement;

    let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;
    let tablaListaPedidos:HTMLElement;
    let estadofactura:string, contentMP:HTMLElement, idfactura:string = '0';
    let mediospagoDB:{id:string, idmediopago:string, id_factura:string, valor:string}[];
    let nuevosMediosPago:{idmediopago:string, valor: string}[]=[];  // guardar los medios de pago a enviar a backend
    const setMediosPagoDB = new Set();
    const mapMediospago = new Map();
    let totalpagadointerno = 0;

    tablaListaPedidos = ($('#tablaListaPedidos') as any).DataTable(configdatatablescaja);


    interface clavesApi {
      clave:string,
      valor_default:string|null,
      valor_final:string|null,
      valor_local:string|null
    };

    let claveAbrirCajon:clavesApi[];

    (async ()=>{
      try {
          const url = "/admin/api/getPasswords"; //llamado a la API REST
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          claveAbrirCajon = resultado;
      } catch (error) {
          console.log(error);
      }
    })();


    //////// clic al btn gastos/ingresos
    btnGastosingresos?.addEventListener('click', ():void=>{
      modalGastosIngresos.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    formGastosingresos.addEventListener('submit', (e:Event)=>{
      btnEnviargastosingresos.disabled = true;
      btnEnviargastosingresos.textContent = "Enviando...";
    });

    ///////// cambio de tipo de operacion si ingreso o gasto, si es gasto habilita el select de los tipos de gastos
    operacion?.addEventListener('change', (e:Event)=>{
      const targetDom = e.target as HTMLSelectElement;
      const tipodegasto = document.querySelector('.tipodegasto') as HTMLElement;

      if(targetDom.value == 'gasto'){
        tipodegasto.style.display = 'flex';
        document.querySelector('#tipodegasto')?.setAttribute("required", ""); //categoria de los gastos
        document.querySelector('#origengasto')?.classList.remove('hidden');  //origen del gasto ya sea por caja o banco
        document.querySelector('#origengasto')?.classList.add('grid');
        showCajasBancos();
      }
      else{ // ingreso a caja
        tipodegasto.style.display = 'none';
        document.querySelector('#tipodegasto')?.removeAttribute("required");
        document.querySelector('#origengasto')?.classList.add('hidden');
        document.querySelector('#origengasto')?.classList.remove('grid');
        document.querySelector('#showcajas')?.classList.remove('hidden'); //mostar caja
        document.querySelector('#showbancos')?.classList.add('hidden'); //oculta banco
        document.querySelector('#banco')?.removeAttribute("required");
      }
    });

    /// evento a los inputs type radio para elegir origne del gasto = caja o bancos
    origengasto.forEach(element =>element.addEventListener('click', showCajasBancos));


    function showCajasBancos(){
      const selectorigen = document.querySelector('input[name="origengasto"]:checked');
      if(selectorigen?.id == 'gastocaja'){
        document.querySelector('#showcajas')?.classList.remove('hidden');
        document.querySelector('#showbancos')?.classList.add('hidden');
        document.querySelector('#banco')?.removeAttribute("required");
      }else{
        document.querySelector('#showbancos')?.classList.remove('hidden');
        document.querySelector('#banco')?.setAttribute("required", "");
      }
    }


    //////// clic al btn abrir cajon monedero
    btnAbrirCajon?.addEventListener('click', ():void=>{
      miDialogoAbrirCaja.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

    //evento al boton confirmar para eliminar orden
    document.querySelector('.siAbrirCajon')?.addEventListener('click', (event:Event)=>{
      const v:number = validarPasswordDcto();
      if(!v)return;
      if((event.target as HTMLInputElement).closest('.siAbrirCajon'))abrirCajonMonedero();
    });

    const abrirCajonMonedero = async():Promise<void>=>{
        try {
            const url = "http://localhost:3100/api/printPOS/openCashDrawer/CAJA";  //api llama servidor de impresion local.
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            console.log(resultado);
            if(resultado){
              msjalertToast('success', 'Â¡Ã‰xito!', 'Apertura de cajon enviada');
              miDialogoAbrirCaja.close();
              document.removeEventListener("click", cerrarDialogoExterno);
            }else{
              msjalertToast('error', 'Â¡Error!', 'Error en el cajon monedero');
            }
        } catch (error) {
            console.log(error);
        }
    }


    ////////////// Evento a la tabla lista de pedidos ///////////////
    document.querySelector('#tablaListaPedidos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      if(target?.classList.contains("mediosdepago")||target.parentElement?.classList.contains("mediosdepago"))cambiomediopago(target);
      if(target?.classList.contains("printPOS")||target.parentElement?.classList.contains("printPOS"))printPOS(target);
      if(target?.classList.contains("printPDF")||target.parentElement?.classList.contains("printPDF"))printPDF(target);
    });


    //////////// Modal para el cambio de medio de pago ////////////
    function cambiomediopago(target:HTMLElement){
      idfactura = target.id;
      contentMP = target;
      estadofactura = target.dataset.estado??'';
      totalPagado.textContent = Number(target.dataset.totalpagado??'0').toLocaleString();
      if(target.tagName == "BUTTON"){
        idfactura = target.parentElement!.id;
        contentMP = target.parentElement!;
        estadofactura = target.parentElement?.dataset.estado??'';
        totalPagado.textContent = Number(target.parentElement!.dataset.totalpagado??'0').toLocaleString();
      }
      facturamediospago();     //muestra los valores de los medios de pago de cada factura

      if(estadofactura == "Eliminada")document.querySelector('#btnEnviarCambioMedioPago')?.classList.add('!hidden');
      if(estadofactura == "Paga")document.querySelector('#btnEnviarCambioMedioPago')?.classList.remove('!hidden');
      modalcambioMedioPago.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    function resaltarMediosActuales(){
        // Ocultar todas las etiquetas y quitar resaltados anteriores
        document.querySelectorAll('.medio-actual').forEach(etiqueta=>{
            etiqueta.classList.add('hidden');
        });

        document.querySelectorAll('.input-medio-pago').forEach(input=>{
            input.classList.remove(
                'border-indigo-500',
                'ring-2',
                'ring-indigo-200',
                'bg-indigo-50'
            );
        });

        // Contar cuÃ¡ntos medios tienen saldo
        let cantidadConSaldo = 0;

        mediosPago.forEach(input => {
            const valor = parseInt(
                input.value.replace(/[,.]/g, '')
            );

            if (valor > 0) {
                cantidadConSaldo++;
            }
        });

        // Resaltar los medios que tienen saldo
        mediosPago.forEach(input=>{
            const valor = parseInt(
                input.value.replace(/[,.]/g,'')
            );

            if(valor > 0){
                const contenedor = input.closest('.contenedor-medio-pago');

                const etiqueta = contenedor?.querySelector('.medio-actual') as HTMLElement;

                if(etiqueta){
                    etiqueta.classList.remove('hidden');

                    if(cantidadConSaldo === 1){
                        etiqueta.textContent = 'â‘  Medio de pago actual';
                    }else{
                        etiqueta.textContent = 'âœ“ Pago original';
                    }
                }

                input.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-200', 'bg-indigo-50');
            }
        });
    }

    function facturamediospago(){  //muestra los valores de los medios de pago de cada factura
      numfactura.textContent = 'Factura NÂ° : '+idfactura;
      (async ()=>{
        try {
          const url = "/admin/api/mediospagoXfactura?id="+idfactura; //llamado a la API REST y se trae los medios de pago segun factura
          const respuesta = await fetch(url); 
          mediospagoDB = await respuesta.json(); 
          //console.log(mediospagoDB);
          //const setMediosPagoDB = new Set(mediospagoDB.map(x=>x.idmediopago));
          //console.log(setMediosPagoDB);

          //agrupar por idmediopago y sumar su valor
          totalpagadointerno = 0;
          mediospagoDB = Object.values(mediospagoDB.reduce<Record<string, {id:string, idmediopago:string, id_factura:string, valor:string}>>((obj, x)=>{
            totalpagadointerno+=Number(x.valor);
            const k = x.idmediopago;
            if(!obj[k])obj[k] = {...x, valor: '0'}
            obj[k].valor = (Number(obj[k].valor) + Number(x.valor))+'';
            return obj;
          }, {}));
          console.log(totalpagadointerno);

          mediospagoDB.forEach(x => setMediosPagoDB.add(x.idmediopago));//se llena el set con los medios de pago de la DB
          mediosPago.forEach(mediopago =>{  //se llena los inputs medios de pago con los medios de pago de la DB
            mediopago.value = '0';
            for(let i=0; i<mediospagoDB.length; i++)
              if(mediopago.id == mediospagoDB[i].idmediopago){
                mediopago.value =  Number(mediospagoDB[i].valor).toLocaleString();
                mapMediospago.set(mediopago.id, Number(mediospagoDB[i].valor));
                break;
              }
          });
          resaltarMediosActuales();
        } catch (error) {
            console.log(error);
        }
      })();
    }

    mediosPago.forEach(mp =>{
      mp.addEventListener('input', (e)=>{
        let totalmediospago = 0;

        mediosPago.forEach((item)=>{ // sumar todos los medios de pago
          totalmediospago += parseInt(
            (item as HTMLInputElement).value.replace(/[,.]/g, '')
          );
        });

        if(totalmediospago <= parseInt(totalPagado.textContent!.replace(/[,.]/g, ''))){

          // Guardar el Ãºltimo valor vÃ¡lido del input
          mapMediospago.set(
            (e.target as HTMLInputElement).id,
            parseInt((e.target as HTMLInputElement).value.replace(/[,.]/g, ''))
          );

          // Actualizar diferencia
          document.querySelector('#diferencia')!.textContent =
            (
              parseInt(totalPagado.textContent!.replace(/[,.]/g, ''))
              - totalmediospago
            ).toLocaleString();

        }else{
          // Restaurar el Ãºltimo valor vÃ¡lido
          if(mapMediospago.has((e.target as HTMLInputElement).id)){
            (e.target as HTMLInputElement).value =
              mapMediospago.get((e.target as HTMLInputElement).id).toLocaleString();

          }else{
            (e.target as HTMLInputElement).value = '0';
          }
        }
      });
    });


    async function printPOS(target: HTMLElement){
      let idfactura = target.parentElement!.id;
      if(target.tagName === 'I')idfactura = target.parentElement!.parentElement!.id;
      //obtener factura por fetch
      try{
        const url = "/admin/api/getInvoice?id="+idfactura; //llamado a la API REST - cajacontrolador y se trae detalle de factura 
        const respuesta = await fetch(url); 
        const resultado:DataInvoice = await respuesta.json();
        const isAndroid = /Android/i.test(navigator.userAgent);
        if(printerBT === '1'){
          const builder = new InvoiceTicketBuilder2(resultado);
          console.log(resultado);
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


        if(printerBT !== '1'){
          const dataPrinter = {
            businessId: resultado.host,
            sucursal: resultado.sucursal,
            printerName: 'CAJA',
            tipoTicket: 'ticket',
            content: resultado
          };
          try {
            const url = "https://servidorimpresionposws-production.up.railway.app/api/print/printJob"; //llamado a la API server print nodejs/ts
            const respuesta = await fetch(url, {
              method: 'POST',
              headers: { "Accept": "application/json", "Content-Type": "application/json" },
              body: JSON.stringify(dataPrinter)
            });
            const resultado = await respuesta.json();
            console.log(resultado);
          } catch (error) {
            console.log(error);
          }
        }


        window.open("/admin/printPDFPOS?id=" + idfactura, "_blank");  //controlador printcontrolador
      }catch(error){
        console.log(error);
      }
    }

    function printPDF(target: HTMLElement){
      let idfactura = target.parentElement!.id;
      let cotizacion:string = target.parentElement?.dataset.cotizacion!;
      let urlprintPDF:string = "/printfacturacarta?id=";
      if(target.tagName === 'I'){
        idfactura = target.parentElement!.parentElement!.id;
        cotizacion = target.parentElement!.parentElement?.dataset.cotizacion!;
      }
      if(cotizacion === '1')urlprintPDF = "/printcotizacion?id=";
      const ventana = window.open(urlprintPDF + idfactura, "_blank");  //cajacontrolador
      if(ventana){
        ventana.onload = ()=>{
          ventana?.focus();
          ventana?.print();
          setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana despuÃ©s de unos segundos
        };
      }
    }

    ////////////////// evento al bton para cambiar medios de pago //////////////////////
    document.querySelector('#formCambioMedioPago')?.addEventListener('submit', e=>{
      e.preventDefault();
      let totalotrosmedios = 0;
      nuevosMediosPago.length = 0;
      mediosPago.forEach((item, index)=>{ 
        totalotrosmedios += parseInt((item as HTMLInputElement).value.replace(/[,.]/g, '')); //sumar todos los medios de pago de los inputs
        if(item.value != '0')
          nuevosMediosPago = [...nuevosMediosPago, {idmediopago: item.id, valor: item.value.replace(/[,.]/g, '')}];  //obtengo los nuevos medios de pago difente a cero
      });

      if(totalotrosmedios != parseInt(totalPagado.textContent!.replace(/[,.]/g, '')) && totalotrosmedios!=totalpagadointerno){
        msjAlert('error', 'Valor diferente al pagado', (document.querySelector('#divmsjalerta2') as HTMLElement));
        return;
      }
      actualizarMediosPago();
    });


    async function actualizarMediosPago(){
      const setNuevosMediosPago = new Set(nuevosMediosPago.map(x=>x.idmediopago));
      ////cruzar los datos de los medios de pago de la DB y de los nuevos medios de pago////
      //////// medios de pago iguales o compartidos ///////////
      const mediospagocompartidos = mediospagoDB.filter(x=>setNuevosMediosPago.has(x.idmediopago));
      //////// nuevos medios de pago que no estan en DB, crear o agregar
      const nuevosMediosPagoNoEnDB = nuevosMediosPago.filter(x=>!setMediosPagoDB.has(x.idmediopago));
      //////// medios de pago de la DB que no estan en los nuevos medios de pago, eliminar
      const mediosPagoDBNoEnNuevos = mediospagoDB.filter(x=>!setNuevosMediosPago.has(x.idmediopago));

      const datos = new FormData();
      datos.append('id_factura', idfactura);
      datos.append('efectivoDB', mediospagoDB.find(x=>x.idmediopago=='1')?.valor??'0'+'');
      datos.append('nuevoEfectivo', nuevosMediosPago.find(x=>x.idmediopago=='1')?.valor??'0'+'');
      datos.append('mediospagocompartidos', JSON.stringify(mediospagocompartidos));
      datos.append('nuevosMediosPagoNoEnDB', JSON.stringify(nuevosMediosPagoNoEnDB));
      datos.append('mediosPagoDBNoEnNuevos', JSON.stringify(mediosPagoDBNoEnNuevos));
      datos.append('nuevosMediosPago', JSON.stringify(nuevosMediosPago));  
      try {
          const url = "/admin/api/cambioMedioPago";  //va al controlador ventascontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();
          if(resultado.exito !== undefined){
            msjalertToast('success', 'Â¡Ã‰xito!', resultado.exito[0]);
            updateMP(resultado.mediosPagoUpdate);
          }else{
            msjalertToast('error', 'Â¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
      modalcambioMedioPago.close();
      document.removeEventListener("click", cerrarDialogoExterno);
    }

    function updateMP(mediosPagoUpdate:{id:string, idmediopago:string, id_factura:string, valor:string, mediopago:string}[]){
      while(contentMP.firstChild)contentMP.removeChild(contentMP.firstChild);
      mediosPagoUpdate.forEach(btnMP=>{
        const button = document.createElement('button');
        button.classList.add('btn-xs', 'btn-light', 'mr-2');
        button.textContent = btnMP.mediopago;
        contentMP.appendChild(button);
      });
    }

    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if (f === modalGastosIngresos || f === modalcambioMedioPago || f === miDialogoAbrirCaja || (f as HTMLInputElement).closest('.noAbrirCajon') || (f as HTMLInputElement).value === 'cancelar') {
          modalGastosIngresos.close();
          modalcambioMedioPago.close();
          miDialogoAbrirCaja.close();
          document.removeEventListener("click", cerrarDialogoExterno);
      }
    }
    
    function validarPasswordDcto():number{
      const clave = claveAbrirCajon.find(c => c.clave=='clave_para_abrir_cajÃ³n_monedero');
      if(clave?.valor_final!==null && inputAbrirCaja.value !== clave?.valor_final){
        msjAlert('error', 'El password es invalido', (document.querySelector('#divmsjalerta3') as HTMLElement));
        return 0;
      }
      return 1;
    }
  }

})();
