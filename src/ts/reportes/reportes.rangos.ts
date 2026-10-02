(()=>{

    if(
        !document.querySelector('.ventasgenerales') &&
        !document.querySelector('.reporteEmisores') &&
        !document.querySelector('.remisiones') &&
        !document.querySelector('.cuotasCreditos') &&
        !document.querySelector('.movimientosinventarios') &&
        !document.querySelector('.listaProductosComprados') &&
        !document.querySelector('.estadosfinancierosCreditos') &&
        !document.querySelector('.recibosCaja'))return;

     const POS = (window as any).POS;

    const consultarFechaPersonalizada = document.querySelector('#consultarFechaPersonalizada') as HTMLButtonElement;
    const btnmesactual = document.querySelector('#btnmesactual') as HTMLButtonElement;
    const btnmesanterior = document.querySelector('#btnmesanterior') as HTMLButtonElement;
    const btnhoy = document.querySelector('#btnhoy') as HTMLButtonElement;
    const btnayer = document.querySelector('#btnayer') as HTMLButtonElement;
    let fechainicio:string = "", fechafin:string = "";

    // SELECTOR DE FECHAS DEL CALENDARIO
    const esReporteCuotas = Boolean(document.querySelector('.cuotasCreditos'));
    const esReporteVentasGenerales = Boolean(document.querySelector('.ventasgenerales'));
    const usaCalendarioModerno = esReporteCuotas || esReporteVentasGenerales;
    const dateRangeInput = ($('input[name="datetimes"]') as any);

    function resaltarBotonConsultar(){
      if(!usaCalendarioModerno || !consultarFechaPersonalizada)return;

      consultarFechaPersonalizada.classList.remove('daterangepicker-action--attention');
      void consultarFechaPersonalizada.offsetWidth;
      consultarFechaPersonalizada.classList.add('daterangepicker-action--attention');
      consultarFechaPersonalizada.scrollIntoView({ behavior: 'smooth', block: 'center' });

      setTimeout(()=>{
        consultarFechaPersonalizada.classList.remove('daterangepicker-action--attention');
      }, 2400);
    }

    inicializarDateRangePicker(dateRangeInput, { autoUpdateInput: !usaCalendarioModerno });

    $('input[name="datetimes"]').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
        var endDate = picker.endDate.format('YYYY-MM-DD HH:mm:ss');
        fechainicio = startDate;
        fechafin = endDate;
        if(usaCalendarioModerno){
          ($(this) as any).val(`${picker.startDate.format('DD/MM/YYYY hh:mm A')} - ${picker.endDate.format('DD/MM/YYYY hh:mm A')}`);
          resaltarBotonConsultar();
        }
        //(document.querySelector('#fechainicio') as HTMLParagraphElement).textContent = fechainicio;
        //(document.querySelector('#fechafin') as HTMLParagraphElement).textContent = fechafin;
    });

    $('input[name="datetimes"]').on('cancel.daterangepicker', function() {
      if(usaCalendarioModerno){
        ($(this) as any).val('');
        fechainicio = '';
        fechafin = '';
      }
    });

    btnmesactual?.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        // Primer dÃ­a del mes actual
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        // Ãšltimo dÃ­a del mes actual
        const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        const fechainiciobtn:string = primerDia.toISOString().split('T')[0];
        const fechafinbtn:string = ultimoDia.toISOString().split('T')[0];
        POS.callApiReporte(fechainiciobtn, fechafinbtn);
    });


    btnmesanterior?.addEventListener('click', (e:Event)=>{
        // Fecha actual
        const hoy = new Date();
        // Primer dÃ­a del mes anterior
        const primerDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
        // Ãšltimo dÃ­a del mes anterior
        const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        const fechainiciobtn:string = primerDiaMesAnterior.toISOString().split('T')[0];
        const fechafinbtn:string = ultimoDiaMesAnterior.toISOString().split('T')[0];
        POS.callApiReporte(fechainiciobtn, fechafinbtn);
    });

    btnhoy?.addEventListener('click', (e:Event)=>{
        const hoy = new Date();
        const inicioDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 0, 0, 0);
        const finDia = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioDia);
        const fechafinbtn:string = formatoFecha(finDia).substring(0, 10);
        POS.callApiReporte(fechainiciobtn, fechafinbtn);
    });

    btnayer?.addEventListener('click', (e:Event)=>{
        // Fecha actual
        const hoy = new Date();
        // DÃ­a anterior
        const ayer = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() - 1);
        // Inicio y fin del dÃ­a anterior
        const inicioAyer = new Date(ayer.getFullYear(), ayer.getMonth(), ayer.getDate(), 0, 0, 0);
        const finAyer = new Date(ayer.getFullYear(), ayer.getMonth(), ayer.getDate(), 23, 59, 59);
        const fechainiciobtn:string = formatoFecha(inicioAyer);
        const fechafinbtn:string = formatoFecha(finAyer).substring(0, 10);
        POS.callApiReporte(fechainiciobtn, fechafinbtn);
    });

    function formatoFecha(fecha: Date): string {
        const anio = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, "0");
        const dia = String(fecha.getDate()).padStart(2, "0");
        const hora = String(fecha.getHours()).padStart(2, "0");
        const minuto = String(fecha.getMinutes()).padStart(2, "0");
        const segundo = String(fecha.getSeconds()).padStart(2, "0");
        return `${anio}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
    }


    ////// consulta por fecha personalizada
    consultarFechaPersonalizada.addEventListener('click', ()=>{
      if(fechainicio == '' || fechafin == ''){
         msjalertToast('error', 'Â¡Error!', "Elegir fechas a consultar");
         return;
      }
    
      POS.callApiReporte(fechainicio, fechafin);
    });

})();




