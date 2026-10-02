(()=>{
  if(!document.querySelector('.ventas')&&!document.querySelector('.crearseparado')&&!document.querySelector('.modorapido'))return;

    const POS = (window as any).POS;

    //obtener valor porcentual del interes por cuota
    let tasaInteresDB:number = 0;
    /*(async ()=>{
      try {
          const url = "/admin/api/getParamGlobal"; //llamado a la API REST
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          tasaInteresDB = Number(resultado.tasa_de_interes_por_cuota.valor_final);
      } catch (error) {
          console.log(error);
      }
    })();*/
    tasaInteresDB = Number(getParam.tasa_de_interes_por_cuota.valor_final);

    const mediospago = document.querySelectorAll<HTMLInputElement>('.mediopago');
    const interes = document.querySelector('#interes') as HTMLSelectElement;
    const abonoinicial = (document.querySelector('#abonoinicial') as HTMLInputElement);
    const cantidadcuotas = (document.querySelector('#cantidadcuotas') as HTMLInputElement);
    //let tipoventa:string="Contado";
    let valoresCredito = {capital:0, abonoinicial:0, cantidadcuotas:1, montocuota:0, frecuenciapago:1, interes:0, interestotal:0, valorinterestotal:0, valorinteresxcuota:0, montototal:0};

    //eventos a los inputs medios de pago
    mediospago.forEach(m=>{
      m.addEventListener('input', (e)=>{ calcularmediospago(e);});
      m.addEventListener('focus', ()=>{
        const valorActual = parseFloat(m.value.trim().replace(/\./g, '').replace(',', '.'))||0;
        if(valorActual === 0)m.value = '';
      });
      m.addEventListener('blur', (e)=>{
        if(m.value.trim() !== '')return;
        m.value = '0';
        if(!m.readOnly)calcularmediospago(e);
      });
    });

    const botonAgregarMedioPago = document.querySelector('#btnTogglePaymentMethods') as HTMLElement|null;
    const acordeonMediosPago = document.querySelector('#first') as HTMLInputElement|null;

    function abrirMediosPago(){
      if(acordeonMediosPago)acordeonMediosPago.checked = true;
      const primerMedioEditable = Array.from(mediospago).find(input=>!input.readOnly && !input.disabled);
      setTimeout(()=>primerMedioEditable?.focus(), 80);
    }

    botonAgregarMedioPago?.addEventListener('click', (event)=>{
      event.preventDefault();
      event.stopPropagation();
      abrirMediosPago();
    });

    botonAgregarMedioPago?.addEventListener('keydown', (event)=>{
      if(event.key !== 'Enter' && event.key !== ' ')return;
      event.preventDefault();
      abrirMediosPago();
    });

    /////////////////////  evento al input recibido  //////////////////////////
    document.querySelector<HTMLInputElement>('#recibio')?.addEventListener('input', (e)=>{
      calcularCambio((e.target as HTMLInputElement).value);
    });
    
    const gestionSubirModalPagar = {
        
      subirModalPagar(){
        //initvaloresCredito();
        document.querySelector('#totalPagar')!.textContent = `${POS.valorTotal.total.toLocaleString()}`;
        //como se puede cerrar el modal y aumentar los productos, hay calcular los inputs
        let totalotrosmedios = 0;
        mediospago.forEach((item, index)=>{
            if(index>0)totalotrosmedios += parseFloat(item.value.trim().replace(/\./g, '').replace(',', '.'))||0;
        });

        if(POS.valorTotal.total<totalotrosmedios){
            totalotrosmedios = 0;
            POS.mapMediospago.clear();
            $('.mediopago').val(0);
        }
        if(POS.tipoventa == "Contado"){
            (document.querySelector('.Efectivo')! as HTMLInputElement).value =  `${(POS.valorTotal.total-totalotrosmedios).toLocaleString()}`;
            POS.mapMediospago.set('1', POS.valorTotal.total-totalotrosmedios); //inicialmente el valor total se establece para efectivo
        }
        if(POS.valorTotal.total-totalotrosmedios == 0 && POS.mapMediospago.has('1'))POS.mapMediospago.delete('1');
        calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
        ocultarMensajeMetodosPago();
        if(POS.tipoventa == "Credito")calculoTasaInteres();
        if(POS.tipoventa == "Contado")initvaloresCredito();
      },
      calculoTasaInteres,
      mostrarMensajeMetodosPago,
      valoresCredito
      
    };


    function initvaloresCredito(){
      /*valoresCredito.capital = POS.valorTotal.total;  //inicializar el capital en el obj
      valoresCredito.cantidadcuotas = Number(cantidadcuotas.value);
      valoresCredito.montocuota = (valoresCredito.capital - valoresCredito.abonoinicial)/valoresCredito.cantidadcuotas;
      valoresCredito.interes = 0;  //No aplicar interes
      valoresCredito.interestotal = 0;  // 0% de interes
      valoresCredito.valorinterestotal = 0; //0$ de interes total de la factura
      valoresCredito.valorinteresxcuota = 0;  //0$ de interes de cada cuota
      valoresCredito.montototal = valoresCredito.capital;
      (document.querySelector('#montocuota') as HTMLInputElement).value = valoresCredito.montocuota+'';*/
      valoresCredito.capital=0; 
      valoresCredito.abonoinicial=0;
      valoresCredito.cantidadcuotas=1;
      valoresCredito.montocuota=0;
      valoresCredito.frecuenciapago=1,
      valoresCredito.interes=0;  //No aplicar interes
      valoresCredito.interestotal=0;  // 0% de interes
      valoresCredito.valorinterestotal=0;  //0$ de interes total de la factura
      valoresCredito.valorinteresxcuota=0;  //0$ de interes de cada cuota
      valoresCredito.montototal=0;
    }

    
    function ocultarMensajeMetodosPago(forzar = false){
      const mensaje = document.querySelector('#paymentMethodsRequiredMessage') as HTMLElement;
      if(!mensaje)return;
      let totalMediosPago = 0;
      for(const value of POS.mapMediospago.values())totalMediosPago += Number(value);
      const abonoCompleto = totalMediosPago >= valoresCredito.abonoinicial;
      if(!forzar && !abonoCompleto)return;
      mensaje.classList.remove('show');
      document.querySelector('.payment-methods-panel')?.classList.remove('payment-methods-panel--attention');
    }

    function mostrarMensajeMetodosPago(){
      const alertaSuperior = document.querySelector('#divmsjalertaprocesarpago') as HTMLElement;
      const mensaje = document.querySelector('#paymentMethodsRequiredMessage') as HTMLElement;
      const panel = document.querySelector('.payment-methods-panel') as HTMLElement;
      if(alertaSuperior)alertaSuperior.innerHTML = '';
      if(acordeonMediosPago)acordeonMediosPago.checked = true;

      if(mensaje){
        mensaje.classList.add('show');
        void mensaje.offsetWidth;
      }

      if(panel){
        panel.classList.remove('payment-methods-panel--attention');
        void panel.offsetWidth;
        panel.classList.add('payment-methods-panel--attention');
        panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(()=>{
          panel.classList.remove('payment-methods-panel--attention');
          mensaje.classList.remove('show');
        }, 2200);
      }

      const primerMedioEditable = Array.from(mediospago).find(input=>!input.readOnly && !input.disabled);
      setTimeout(()=>primerMedioEditable?.focus(), 180);
    }


    function calcularmediospago(e:Event){
        let totalotrosmedios = 0;
        mediospago.forEach((item, index)=>{ //sumar todos los medios de pago menos el efectivo
          if(index>0&&POS.tipoventa=="Contado" || POS.tipoventa=="Credito")totalotrosmedios += parseFloat(item.value.trim().replace(/\./g, '').replace(',', '.'))||0;
        });
        if(totalotrosmedios<=POS.valorTotal.total&&POS.tipoventa == "Contado" || totalotrosmedios<=valoresCredito.abonoinicial&&POS.tipoventa == "Credito"){
          if(POS.tipoventa == "Contado"){
            POS.mapMediospago.set('1', POS.valorTotal.total-totalotrosmedios);
            if(POS.valorTotal.total-totalotrosmedios == 0 && POS.mapMediospago.has('1'))POS.mapMediospago.delete('1'); //se elimina medio de pago efectivo
          }
          const valorMedioPago = parseFloat((e.target as HTMLInputElement).value.trim().replace(/\./g, '').replace(',', '.'))||0;
          POS.mapMediospago.set((e.target as HTMLInputElement).id, valorMedioPago);
          if(valorMedioPago == 0 && POS.mapMediospago.has((e.target as HTMLInputElement).id))POS.mapMediospago.delete((e.target as HTMLInputElement).id);
        }else{ //si la suma de los medios de pago superan el valor total, toma el ultimo input digitado y lo reestablece a su ultimo valor
          if(POS.mapMediospago.has((e.target as HTMLInputElement).id)){
            (e.target as HTMLInputElement).value = POS.mapMediospago.get((e.target as HTMLInputElement).id).toLocaleString();
          }else{
            (e.target as HTMLInputElement).value = '0';
          }
        }
        if(POS.tipoventa == "Contado"){
          mediospago[0].value = (POS.mapMediospago.get('1')??0).toLocaleString();  //medio de pago en efectivo
        }
        calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
        ocultarMensajeMetodosPago();
    }

    function calcularCambio(recibido:string):void{
      recibido = recibido.trim().replace(/\./g, '').replace(',', '.');
      if(Number(recibido)>POS.mapMediospago.get('1')){
        (document.querySelector('#cambio') as HTMLElement).textContent = (Number(recibido)-POS.mapMediospago.get('1')).toLocaleString()+'';
        return;
      }
      (document.querySelector('#cambio') as HTMLElement).textContent = '0';
    }


    ////////////////////  CALCULO DE VALORES DEL CREDITO  //////////////////////

    interes.addEventListener('change', e=>calculoTasaInteres());
    cantidadcuotas.addEventListener('input', e=>calculoTasaInteres());
    abonoinicial.addEventListener('input', e=>calculoTasaInteres());

    function calculoTasaInteres(){
      let creditofinal = recalcularCapitalXAbono(); //capital inicial - abono
      initMediosPagos();
      valoresCredito.cantidadcuotas = Number(cantidadcuotas.value);
      if(cantidadcuotas.value == '1' || (interes.value == '0' || interes.value == '')){
        (document.querySelector('#montocuota') as HTMLInputElement).value =  (Math.ceil((creditofinal/valoresCredito.cantidadcuotas)*100)/100)+'';
        //(document.querySelector('#interestotal') as HTMLInputElement).value = '0';
        //(document.querySelector('#valorinteresxcuota') as HTMLInputElement).value = '0';
        //(document.querySelector('#valorinterestotal') as HTMLInputElement).value = '0';
        //(document.querySelector('#montototal') as HTMLInputElement).value = creditofinal.toLocaleString();
        valoresCredito.montocuota = (Math.ceil((creditofinal/valoresCredito.cantidadcuotas)*100)/100);
        valoresCredito.interestotal = 0;
        valoresCredito.valorinteresxcuota = 0;
        valoresCredito.valorinterestotal = 0;
        valoresCredito.montototal = creditofinal;
      }
      if(cantidadcuotas.value !== '1'&&interes.value === '1'){
        const valorInteres = creditofinal*(tasaInteresDB/100)*valoresCredito.cantidadcuotas;
        const total = creditofinal + valorInteres;
        const valorxcuota = Math.ceil((total/valoresCredito.cantidadcuotas)*100)/100;
        (document.querySelector('#montocuota') as HTMLInputElement).value = valorxcuota+'';
        //(document.querySelector('#interestotal') as HTMLInputElement).value = 2*valoresCredito.cantidadcuotas+''; //porcentaje de interes total
        //(document.querySelector('#valorinteresxcuota') as HTMLInputElement).value = valorInteres/valoresCredito.cantidadcuotas+'';
        //(document.querySelector('#valorinterestotal') as HTMLInputElement).value = valorInteres.toLocaleString();  //valor del interes total del credito
        //(document.querySelector('#montototal') as HTMLInputElement).value = total.toLocaleString();  //valor total del credito
        valoresCredito.interes = 1;
        valoresCredito.montocuota = valorxcuota;
        valoresCredito.interestotal = tasaInteresDB*valoresCredito.cantidadcuotas;
        valoresCredito.valorinteresxcuota = valorInteres/valoresCredito.cantidadcuotas;
        valoresCredito.valorinterestotal = valorInteres;
        valoresCredito.montototal = total;
      }
      //(document.querySelector('#capitalinicial') as HTMLInputElement).value = capital.value.toLocaleString();
      //(document.querySelector('#abono') as HTMLInputElement).value = Number(abonoinicial.value).toLocaleString();
      //(document.querySelector('#capitalFinanciado') as HTMLInputElement).value = creditofinal+'';
      
    }

    function recalcularCapitalXAbono():number{
      const capital = POS.valorTotal.total;
      let abono = Number(abonoinicial.value??0);
      const capitalinicial:number = capital;
      let creditofinal:number = capitalinicial;
      if(capital>0){
        if(abono < capitalinicial){
          creditofinal = capitalinicial-abono;
        }else{
          abonoinicial.value = '0';
        }
      }
      valoresCredito.capital = capital;
      valoresCredito.abonoinicial = abono;
      document.querySelector('#valorAbono')!.textContent = abono.toLocaleString();
      return creditofinal;
    }


    function initMediosPagos(){
      mediospago.forEach(z=>z.value = '0');
      POS.mapMediospago.clear();
      ocultarMensajeMetodosPago(true);
    }

    POS.gestionSubirModalPagar = gestionSubirModalPagar;

})();

