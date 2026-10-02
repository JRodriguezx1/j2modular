(()=>{
  if(!document.querySelector('.ventas'))return;

    const POS = (window as any).POS;

    const btnredimir = document.querySelector('#btnredimir') as HTMLButtonElement;  //btn de otros
    const miDialogoRedimir = document.querySelector('#miDialogoRedimir') as HTMLDialogElement;
    const clienteRedimir = document.querySelector('#clienteRedimir') as HTMLElement;
    const viewPtsCli = document.querySelector('#viewPtsCli') as HTMLElement;
    const valorTotalPtstext = document.querySelector('#valorTotalPts') as HTMLElement;
    const inputCantidadPuntos = document.querySelector('#inputCantidadPuntos') as HTMLInputElement;
    const puntosValorText = document.querySelector('#puntosValor') as HTMLElement;
    const aplicarPtsFactura = document.querySelector('#aplicarPtsFactura') as HTMLButtonElement;
    const valorPunto:string = getParam.valor_por_punto.valor_final;
    let valorPts:number = 0, equivalencia:number = 0;

    ///////////////////// Evento al btn Redimir /////////////////////////
    btnredimir?.addEventListener('click', (e:Event)=>{
        if(POS.gestionClientes.selectCliente.value == ''){
            msjalertToast('warning', 'Cliente requerido', 'Selecciona un cliente y una direccion antes de registrar la venta a credito.');
            POS.gestionClientes.resaltarSelectorCliente();
            return;
        }
        if(POS.carrito.length){
            miDialogoRedimir.showModal();
            document.addEventListener("click", POS.cerrarDialogoExterno);
        }
    });

    const esNumeroValido = (valor: string | number): boolean => {
        return String(valor).trim() !== '' && Number.isFinite(Number(valor));
    };


    inputCantidadPuntos?.addEventListener('input', (e:Event)=>{
        const inputPts:number|null =  obtenerNumero((e.target as HTMLInputElement))??0;
        if(!esNumeroValido(valorPunto) || inputPts != null && !esNumeroValido(inputPts)){
            console.log('Puntos o valor por punto no son números válidos');
            return;
        }
        gestionRedmir.pts = inputPts;
        valorPts = inputPts* Number(valorPunto);
        puntosValorText.textContent = '$'+valorPts.toLocaleString();
    });


    aplicarPtsFactura?.addEventListener('click', e=>{
        e.preventDefault();
        //validar que los puntos o valor de los puntos no supere los del cliente 
        if(valorPts>equivalencia)return;
        // validar que los puntos no superen el valor de la factura
        if(valorPts>POS.valorTotal.subtotal)return;
        
        POS.valorTotal.descuento = valorPts;
        POS.valorTotal.total = POS.valorTotal.subtotal - POS.valorTotal.descuento + POS.valorTotal.valortarifa;
        document.querySelector('#total')!.textContent = '$ '+POS.valorTotal.total.toLocaleString();
        (document.querySelector('#descuento') as HTMLElement).textContent = '$'+POS.valorTotal.descuento.toLocaleString();
        POS.gestionSubirModalPagar?.calculoTasaInteres?.();
        miDialogoRedimir.close();
        document.removeEventListener("click", POS.cerrarDialogoExterno);
    });


    const actualizarPuntosDOM = (nombre:string, pts:string):void=>{
        clienteRedimir.textContent = nombre;
        viewPtsCli.textContent = pts+' PTS';
        if(!esNumeroValido(pts) ||!esNumeroValido(valorPunto)){
            console.log('Puntos o valor por punto no son números válidos');
            return;
        }
        equivalencia = Number(pts)*Number(valorPunto);
        valorTotalPtstext.textContent = 'Equivale: $'+equivalencia.toLocaleString();
    }


    const gestionRedmir = {
      miDialogoRedimir,  
      actualizarPuntosDOM,
      get valorPts():number{
        return valorPts;
      },
      get equivalencia():number{
        return equivalencia;
      },
      pts: 0
    };

    (window as any).POS.gestionRedmir = gestionRedmir;

})();