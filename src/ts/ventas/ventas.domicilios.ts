(()=>{
  if(!document.querySelector('.ventas'))return;

    const POS = (window as any).POS;

    const selectCliente = POS.gestionClientes.selectCliente;
    const dirEntrega = POS.gestionClientes.dirEntrega;
    const valorDomicilio = document.querySelector('#valorDomicilio') as HTMLInputElement;
    const btnPresencial = document.querySelector('#btnPresencial') as HTMLButtonElement;
    const btnDomicilio = document.querySelector('#btnEntrega') as HTMLButtonElement;  //domicilio
    let nombretarifa:string = '', tipoEntrega = 0;

    btnPresencial.addEventListener('click', ()=> cambiarTipoEntrega('Presencial'));
    btnDomicilio.addEventListener('click', ()=> cambiarTipoEntrega('Domicilio'));

    function cambiarTipoEntrega(tipo: 'Presencial' | 'Domicilio'): void {
        tipoEntrega = tipo == 'Presencial' ? 0 : 1;
        if(tipo === 'Domicilio' && selectCliente.value === ''){
            msjalertToast('warning', 'Cliente requerido', 'Selecciona un cliente para activar el domicilio.');
            POS.gestionClientes.resaltarSelectorCliente();
            console.log(tipoEntrega);
        }
        printTarifaEnvio();
        POS.valorCarritoTotal();
        actualizarBotonesEntrega(tipo);
    }


    function actualizarBotonesEntrega(tipo:string): void {
        btnPresencial.classList.remove('bg-white', 'text-indigo-700', 'shadow-sm', 'text-slate-600', 'font-semibold', 'font-bold');
        btnDomicilio.classList.remove('bg-white', 'text-indigo-700', 'shadow-sm', 'text-slate-600', 'font-semibold', 'font-bold');
        if(tipo === 'Domicilio'){
            btnPresencial?.classList.add('text-slate-600', 'font-semibold');
            btnDomicilio?.classList.add('bg-white', 'text-indigo-700', 'shadow-sm', 'font-bold');
            valorDomicilio?.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-100');
        }else{
            btnDomicilio?.classList.add('text-slate-600', 'font-semibold');
            btnPresencial?.classList.add('bg-white', 'text-indigo-700', 'shadow-sm', 'font-bold');
            valorDomicilio?.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-100');
        }
    }


    valorDomicilio?.addEventListener('input', (e:Event)=>{
        const tarifaDomicilio = (e.target as HTMLInputElement).value;
        if(Number(tarifaDomicilio) > 0 && tipoEntrega === 0){
            cambiarTipoEntrega('Domicilio');
            return;
        }
        printTarifaEnvio();
        POS.valorCarritoTotal();
        actualizarBotonesEntrega('Domicilio');
    });


    ///////// funcion que imprime el valor de la tarifa segun direccion ///////////
    function printTarifaEnvio():void{
      const tarifas = POS.tarifas; //viene de ahelper.clientes.ts
      const selectDir = dirEntrega.options[dirEntrega.selectedIndex];
      document.querySelector('#confirmarDespacho')?.classList.remove('hidden');
      if(tipoEntrega === 0 || dirEntrega.selectedIndex == -1){
        document.querySelector('#confirmarDespacho')?.classList.add('hidden');
        POS.valorTotal.valortarifa = 0;
        nombretarifa = '';
        tipoEntrega = 0;
        return;
      }
      if(selectDir?.dataset.idtarifa && tipoEntrega === 1){
        const objtarifa = tarifas.find((tarifa:any) =>{
          if(tarifa.idcliente == selectDir.dataset.idcliente && tarifa.id == selectDir.dataset.idtarifa)return true;
        });
        if(valorDomicilio.value == '' || isNaN(Number(valorDomicilio.value))){
          POS.valorTotal.valortarifa = Number(objtarifa?.valor);
        }else{
          POS.valorTotal.valortarifa = Number(valorDomicilio.value || 0);  //valor del domicilio del input Domicilio
        }
        POS.valorTotal.idtarifa = Number(objtarifa?.id);
        nombretarifa = objtarifa?.nombre;
        tipoEntrega = 1;
      }
    }

    const reiniciarDomicilio = ():void=>{
        tipoEntrega = 0;
        actualizarBotonesEntrega('Presencial');
    }

  const gestionarDomiciliosVenta = {
     get nombretarifa(): string {
        return nombretarifa;
    },
    get tipoEntrega(): number {
        return tipoEntrega;
    },
    reiniciarDomicilio

  };

  POS.gestionarDomiciliosVenta = gestionarDomiciliosVenta;
  POS.printTarifaEnvio = printTarifaEnvio;  //usado en clientes.ts
  POS.valorDomicilio = valorDomicilio;
})();