(():void=>{

  if(document.querySelector('.detallecierrecaja')){
    const btnImprimirDetalleCaja = document.querySelector('#btnImprimirDetalleCaja') as HTMLButtonElement;
    const btnVerCierreWeb = document.querySelector('#btnVerCierreWeb') as HTMLButtonElement;
    const btnVerCierreWs = document.querySelector('#btnVerCierreWs') as HTMLButtonElement;

    let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;
    //capturar el id del cierre de caja por la url
    const parametrosURL = new URLSearchParams(window.location.search);
    const id:string = parametrosURL.get('id')??'';

    if(id==''||id==null||isNaN(Number(id)))return;

    ///////// imprimir detalle cierre
    btnImprimirDetalleCaja?.addEventListener('click', ()=>{
      if(id!=''){
          const ventana = window.open('/printdetallecierre?id='+id, '_blank');
          if(ventana){
              ventana.onload = ()=>{
                  ventana?.focus();
                  ventana?.print();
                  setTimeout(() => { ventana?.close(); }, 200); // Cerrar la ventana después de unos segundos
              };
          }
      }else{
        msjalertToast('error', '¡Error!', 'Error al generar detalle de cierre.');
      }
    });


    ///////// ver detalle cierre en web
    btnVerCierreWeb?.addEventListener('click', ()=>{
        const ventana = window.open('/printdetallecierre?id='+id, '_blank');
        if(ventana)ventana.onload = ()=>ventana?.focus();
    });


    ///////// ver y enviar detalle de cierre por whatsapp
    btnVerCierreWs?.addEventListener('click', async()=>{
        const url = "/admin/api/ws/sendtextDetalleCierreCaja?id="+id; //llamado a la API REST para enviar detalle de cierre por ws
        try {
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json();
          if(resultado && resultado.code === 200 && !resultado.error){
            msjalertToast('success', '¡Éxito!', 'Mensaje detalle de cierre de caja enviado');
          }else{
            msjalertToast('error', '¡Error!', 'Error al enviar mensaje de cierre de caja');
          }
        } catch (error) {
          console.log(error);
        }
    });


    ////////////// Evento a la tabla lista de pedidos ///////////////
    document.querySelector('#tablaListaPedidos')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      //if(target?.classList.contains("mediosdepago")||target.parentElement?.classList.contains("mediosdepago"))cambiomediopago(target);
      if(target?.classList.contains("printPOS")||target.parentElement?.classList.contains("printPOS"))printPOS(target);
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
          const builder = new InvoiceTicketBuilder(resultado);
          const ticket = await builder.generate(true); //true para version buffer bytes
          const base64 = bytesToBase64(ticket);
          if(isAndroid)window.location.href = `rawbt:base64,${base64}`;

        }else{
          const dataPrinter = {
            businessId: resultado.negocio,
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

  }

})();