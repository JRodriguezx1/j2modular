(():void=>{

  if(document.querySelector('.fechazetadiario')){

    const btnsmultiselect = document.querySelectorAll<HTMLElement>('.btnmultiselect');
    const selectedcajas = document.querySelectorAll<HTMLLIElement>('.selectedcajas');
    const consultarZDiario = document.querySelector('#consultarZDiario') as HTMLButtonElement;
    const tbodyMediosPago = document.querySelector('#tablaMediosPago tbody') as HTMLTableElement;
    const base = document.querySelector('#base') as HTMLElement;
    const valorImpuestoTotal = document.querySelector('#valorImpuestoTotal') as HTMLElement;
    const ingresoVentas = document.querySelector('#ingresoVentas') as HTMLElement;
    const totalDescuentos = document.querySelector('#totalDescuentos') as HTMLElement;
    const realVentas = document.querySelector('#realVentas') as HTMLElement;
    const cantidadElectronicas = document.querySelector('#cantidadElectronicas') as HTMLElement;
    const cantidadPOS = document.querySelector('#cantidadPOS') as HTMLElement;
    const valorElectronicas = document.querySelector('#valorElectronicas') as HTMLElement;
    const valorPOS = document.querySelector('#valorPOS') as HTMLElement;
    let fechainicio:string = "", fechafin:string = "";

    //evento a los select drop dawn para abrir y cerrar
    btnsmultiselect.forEach((btnmultiselect, index) =>{
      btnmultiselect.addEventListener('click', (e:Event)=>{
        cerrarlosmultiselect(index);
        btnmultiselect.classList.toggle('open');
      });
    });
    function cerrarlosmultiselect(index:number){
      btnsmultiselect.forEach((element, i) =>{
        if(i != index || !element.classList.contains('open'))element.classList.remove('open');
      });
    }
    document.querySelector('.fechazetadiario')?.addEventListener('click', (e:Event)=>{
      if(!(e.target as HTMLElement).closest('.btnmultiselect')&&!(e.target as HTMLElement).closest('.list-items')){
        const btnmultiselect = document.querySelector('.open');
        if(btnmultiselect)btnmultiselect.classList.toggle('open');
      }
    });
    // mostrar el nombre de las cajas,segun se selecciona
    selectedcajas.forEach(c=>{c.addEventListener('click', cajas_seleccionadas);});

    function cajas_seleccionadas(){
      const inputscaja = document.querySelectorAll<HTMLInputElement>('input.selectedcajas[type="checkbox"]:checked');
      const cajastext = document.querySelector('#cajastext') as HTMLElement;
      cajastext.textContent = ". ";
      inputscaja.forEach((inputcaja, i) =>{
        if(i<inputscaja.length-1){
          cajastext.textContent += inputcaja.nextElementSibling?.textContent!+' - ';
        }else{
          cajastext.textContent += inputcaja.nextElementSibling?.textContent!;
        }
      });
    }


    // SELECTOR DE FECHAS DEL CALENDARIO
    inicializarDateRangePicker($('input[name="datetimes"]') as any);

    $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        var endDate = picker.endDate.format('YYYY-MM-DD HH:mm:ss');
        fechainicio = startDate;
        fechafin = endDate;
        (document.querySelector('#fechainicio') as HTMLParagraphElement).textContent = fechainicio;
        (document.querySelector('#fechafin') as HTMLParagraphElement).textContent = fechafin;
    });

    consultarZDiario.addEventListener('click', ()=>{
      if(fechainicio == '' || fechafin == ''){
         msjalertToast('error', '¡Error!', "Elegir fechas a consultar");
         return;
      }
      const cajas = document.querySelectorAll<HTMLInputElement>('input.selectedcajas[type="checkbox"]:checked');
      const facturadores = document.querySelectorAll<HTMLInputElement>('input.facturador[type="checkbox"]:checked');
      
      const valuecajas:string[] = Array.from(cajas).map(c=>c.value);
      const valuefacturadores:string[] = Array.from(facturadores).map(f=>f.value);

      (document.querySelector('.content-spinner1') as HTMLElement).style.display = "grid";
      (async ()=>{
        const datos = new FormData();
        datos.append('fechainicio', fechainicio);
        datos.append('fechafin', fechafin);
        datos.append('cajas', JSON.stringify(valuecajas));
        datos.append('facturadores', JSON.stringify(valuefacturadores));
        try {
            const url = "/admin/api/consultafechazetadiario"; //llama a la api que esta en reportescontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            console.log(resultado);
           (document.querySelector('.content-spinner1') as HTMLElement).style.display = "none";
           reiniciarTablas();
           imprimirtablaMediosPago(resultado.datosmediospago);
           imprimirIngresos(resultado.datosventa);
        } catch (error) {
            console.log(error);
        }
      })();

    });


    function imprimirtablaMediosPago(datosmediospago:{id:string, nombre:string, valor:string}[]){
      datosmediospago.forEach(mp =>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="">${mp.nombre}</td> 
                        <td class="">$${Number(mp.valor).toLocaleString()}</td>`;
        tbodyMediosPago.appendChild(tr);
      });
    }

    function imprimirIngresos(datosventa:{subtotalventa:string, base:string, valorimpuestototal:string, totalventa:string, ELECTRONICAS:string, POS:string, total_ELECTRONICAS:string, total_POS:string}){
      base.textContent = '$'+Number(datosventa.base).toLocaleString();
      valorImpuestoTotal.textContent = '$'+Number(datosventa.valorimpuestototal).toLocaleString();
      ingresoVentas.textContent = '$'+Number(datosventa.subtotalventa).toLocaleString();
      totalDescuentos.textContent = '$'+(Number(datosventa.subtotalventa)-Number(datosventa.totalventa)).toLocaleString();
      realVentas.textContent = '$'+Number(datosventa.totalventa).toLocaleString();
      cantidadElectronicas.textContent = datosventa.ELECTRONICAS;
      cantidadPOS.textContent = datosventa.POS;
      valorElectronicas.textContent = '$'+Number(datosventa.total_ELECTRONICAS).toLocaleString();
      valorPOS.textContent = '$'+Number(datosventa.total_POS).toLocaleString();
    }

    function reiniciarTablas(){
      while(tbodyMediosPago.firstChild)tbodyMediosPago.removeChild(tbodyMediosPago.firstChild);
      ingresoVentas.textContent = '';
      totalDescuentos.textContent = "";
      realVentas.textContent = "";
      cantidadElectronicas.textContent = '';
      cantidadPOS.textContent = '';
      valorElectronicas.textContent = '';
      valorPOS.textContent = '';
    }

  }

})();
