(()=>{
  if(document.querySelector('.ventasxtransaccion')){

    const btnmensual = document.querySelector('#btnmensual') as HTMLButtonElement;
    const btndiario = document.querySelector('#btndiario') as HTMLButtonElement;
    const miDialogoMes = document.querySelector('#miDialogoMes') as any;
    const miDialogoDiario = document.querySelector('#miDialogoDiario') as any;
    let tablaTransaccioneXVenta:HTMLTableElement;

    interface transacciones {
      fecha:string,
      total_venta:string,
      num_transacciones:string,
      promedio_transaccion:string,
      transaccion_mas_alta:string,
      transaccion_mas_baja:string
    }
    let datatransacciones:transacciones[] = [];

    btnmensual?.addEventListener('click', ()=>{
        miDialogoMes.showModal(); //seleccionar año
        document.addEventListener("click", cerrarDialogoExterno);
    });

    btndiario?.addEventListener('click', ()=>{
        miDialogoDiario.showModal();  //seleccionar mes
        document.addEventListener("click", cerrarDialogoExterno);
    });


    //seleccionar año, transacciones mes acumulado de un año
    document.querySelector('#formMes')?.addEventListener('submit', e=>{
      e.preventDefault();
      const inputselectaño = document.querySelector('#inputselectaño') as HTMLInputElement;
      (async ()=>{
        try {
          const url = "/admin/api/ventasxtransaccionanual?x="+inputselectaño.value; //llamado a la API REST en reportescontrolador.php
          const respuesta = await fetch(url);
          const resultado = await respuesta.json();
          datatransacciones = resultado;
          console.log(resultado);
          printTableTranscciones()
        } catch (error) {
            console.log(error);
        }
      })();
      miDialogoMes.close();
      document.removeEventListener("click", cerrarDialogoExterno);
    });


    //seleccionar mes, transacciones acumuladas por dia durante un mes y año elejido
    document.querySelector('#formDiario')?.addEventListener('submit', e=>{
      e.preventDefault();
      const inputselectmesyaño = document.querySelector('#inputselectmesyaño') as HTMLInputElement;
      (async ()=>{
        try {
          const url = "/admin/api/ventasxtransaccionmes?x="+inputselectmesyaño.value; //llamado a la API REST en reportescontrolador.php
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json();
          datatransacciones = resultado;
          printTableTranscciones()
        } catch (error) {
            console.log(error);
        }
      })();
      miDialogoDiario.close();
      document.removeEventListener("click", cerrarDialogoExterno);
    });


    printTableTranscciones();
    function printTableTranscciones(){

        tablaTransaccioneXVenta = ($('#tablaTransaccioneXVenta') as any).DataTable({
            destroy: true, // importante si recargas la tabla
            data: datatransacciones,
            columns: [{title: 'Fecha', data: 'fecha'}, {title: 'Toal venta', data: 'total_venta', render: (data: number) => `$${Number(data).toLocaleString()}`}, {title: 'Numero de transacciones', data: 'num_transacciones'}, {title: 'Promedio por transacción', data: 'promedio_transaccion'}, {title: 'Transacción mas alta', data: 'transaccion_mas_alta'}, {title: 'Transacción mas baja', data: 'transaccion_mas_baja'}],
        });
    }



    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if (f === miDialogoMes || f === miDialogoDiario || (f as HTMLInputElement).closest('.salir')) {
        miDialogoMes.close();
        miDialogoDiario.close();
        document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

  }


})();
   