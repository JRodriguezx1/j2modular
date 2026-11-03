

const processPaymentButton = document.querySelector('#processPaymentButton') as HTMLElement;
const miDialogoFacturar = document.querySelector('#miDialogoFacturar') as HTMLDialogElement;
const btnCaja = document.querySelector('#caja') as HTMLSelectElement; //select de la caja en el modal pagar
const btnTipoFacturador = document.querySelector('#facturador') as HTMLSelectElement; //select del consecutivo o facturador en el modal de pago
const btnPagar = document.getElementById('btnPagar') as HTMLInputElement;
const botonAgregarMedioPago = document.querySelector('#btnTogglePaymentMethods') as HTMLElement|null;
const acordeonMediosPago = document.querySelector('#first') as HTMLInputElement|null;
const mediospago = document.querySelectorAll<HTMLInputElement>('.mediopago');
const interes = document.querySelector('#interes') as HTMLSelectElement;
const abonoinicial = (document.querySelector('#abonoinicial') as HTMLInputElement);
const cantidadcuotas = (document.querySelector('#cantidadcuotas') as HTMLInputElement);
//let tipoventa:string="Contado";
let valoresCredito = {capital:0, abonoinicial:0, cantidadcuotas:1, montocuota:0, frecuenciapago:1, interes:0, interestotal:0, valorinterestotal:0, valorinteresxcuota:0, montototal:0};

const mapMediospago = new Map();

export function registeraddclienteEvents(getContextVersion: () => number): void{

    let clientRequestVersion = 0;

    processPaymentButton?.addEventListener('click', (e)=>{ miDialogoFacturar.showModal(); });

    selectFacturadorSegunCaja(btnCaja);

    btnCaja.addEventListener('change', (e:Event)=>selectFacturadorSegunCaja(e.target as HTMLSelectElement));

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
}



function selectFacturadorSegunCaja(z:HTMLSelectElement){
    $('#facturador').val(z.options[z.selectedIndex].dataset.idfacturador??'1');
}

function abrirMediosPago(){
    if(acordeonMediosPago)acordeonMediosPago.checked = true;
    const primerMedioEditable = Array.from(mediospago).find(input=>!input.readOnly && !input.disabled);
    setTimeout(()=>primerMedioEditable?.focus(), 80);
}


function pago(){
    //initvaloresCredito();
        document.querySelector('#totalPagar')!.textContent = `${POS.valorTotal.total.toLocaleString()}`;
        //como se puede cerrar el modal y aumentar los productos, hay calcular los inputs
        let totalotrosmedios = 0;
        mediospago.forEach((item, index)=>{
            if(index>0)totalotrosmedios += parseFloat(item.value.trim().replace(/\./g, '').replace(',', '.'))||0;
        });

        if(POS.valorTotal.total<totalotrosmedios){
            totalotrosmedios = 0;
            mapMediospago.clear();
            $('.mediopago').val(0);
        }
        if(POS.tipoventa == "Contado"){
            (document.querySelector('.Efectivo')! as HTMLInputElement).value =  `${(POS.valorTotal.total-totalotrosmedios).toLocaleString()}`;
            mapMediospago.set('1', POS.valorTotal.total-totalotrosmedios); //inicialmente el valor total se establece para efectivo
        }
        if(POS.valorTotal.total-totalotrosmedios == 0 && mapMediospago.has('1'))mapMediospago.delete('1');
        calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
        
        //ocultarMensajeMetodosPago();
        //if(POS.tipoventa == "Credito")calculoTasaInteres();
        //if(POS.tipoventa == "Contado")initvaloresCredito();
      },
}