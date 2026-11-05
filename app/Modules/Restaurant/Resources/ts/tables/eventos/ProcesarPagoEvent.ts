import { AccountTotals } from "../accountApi";
import { createCart } from "../cart";
import { createCli } from "../cli";
import { createCliDian } from "../cliDian";


const processPaymentButton = document.querySelector('#processPaymentButton') as HTMLElement;
const miDialogoFacturar = document.querySelector('#miDialogoFacturar') as HTMLDialogElement;
const btnCaja = document.querySelector('#caja') as HTMLSelectElement; //select de la caja en el modal pagar
const btnTipoFacturador = document.querySelector('#facturador') as HTMLSelectElement; //select del consecutivo o facturador en el modal de pago
const btnPagar = document.getElementById('btnPagar') as HTMLInputElement;
const botonAgregarMedioPago = document.querySelector('#btnTogglePaymentMethods') as HTMLElement|null;
const acordeonMediosPago = document.querySelector('#first') as HTMLInputElement|null;
const mediospago = document.querySelectorAll<HTMLInputElement>('.mediopago');
const imprimir = document.querySelector('input[name="imprimir"]:checked') as HTMLInputElement;
//const interes = document.querySelector('#interes') as HTMLSelectElement;
//const abonoinicial = (document.querySelector('#abonoinicial') as HTMLInputElement);
//const cantidadcuotas = (document.querySelector('#cantidadcuotas') as HTMLInputElement);
//let tipoventa:string="Contado";
//let valoresCredito = {capital:0, abonoinicial:0, cantidadcuotas:1, montocuota:0, frecuenciapago:1, interes:0, interestotal:0, valorinterestotal:0, valorinteresxcuota:0, montototal:0};
const mapMediospago = new Map();


type Cart = ReturnType<typeof createCart>;
type Cli = ReturnType<typeof createCli>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;
interface ActiveOrder {
    resourceId: number,
    occupationId: number|null,
    accountId: number|null,
    cartKey: CartKey,
    cart: Cart,
    cli: Cli,
    cliDian: ReturnType<typeof createCliDian>,
    totales: AccountTotals|null
}

export function registerProcesarPagoEvents(getActiveOrder: () => ActiveOrder|null, getContextVersion: () => number): void{

    let clientRequestVersion = 0;

    processPaymentButton?.addEventListener('click', (e)=>{ 
        miDialogoFacturar.showModal();
        document.querySelector('.Efectivo')?.setAttribute('readonly', 'true');
        document.querySelector('#inputscreditos')?.classList.add('hidden');
        document.querySelector('#inputscreditos')?.classList.remove('flex');
        const orden = getActiveOrder();
        initModalPago(orden?.totales??null);
    });

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

    //eventos a los inputs medios de pago
    mediospago.forEach(m=>{
      m.addEventListener('input', (e)=>{
        const orden = getActiveOrder();
        calcularmediospago(e, orden?.totales??null);
      });
      m.addEventListener('focus', ()=>{
        const valorActual = parseFloat(m.value.trim().replace(/\./g, '').replace(',', '.'))||0;
        if(valorActual === 0)m.value = '';
      });
      m.addEventListener('blur', (e)=>{
        if(m.value.trim() !== '')return;
        m.value = '0';
        const orden = getActiveOrder();
        if(!m.readOnly)calcularmediospago(e, orden?.totales??null);
      });
    });

    /////////////////////  evento al input recibido  //////////////////////////
    document.querySelector<HTMLInputElement>('#recibio')?.addEventListener('input', (e)=>{
      calcularCambio((e.target as HTMLInputElement).value);
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

function initModalPago(totales: AccountTotals|null){
    const total = Number(totales?.total??0);
    document.querySelector('#totalPagar')!.textContent = `$${total.toLocaleString()}`;
    //como se puede cerrar el modal y aumentar los productos, hay calcular los inputs
    let totalotrosmedios = 0;
    mediospago.forEach((item, index)=>{
        if(index>0)totalotrosmedios += parseFloat(item.value.trim().replace(/\./g, '').replace(',', '.'))||0;
    });

    if(total<totalotrosmedios){
        totalotrosmedios = 0;
        mapMediospago.clear();
        $('.mediopago').val(0);
    }
        
    (document.querySelector('.Efectivo')! as HTMLInputElement).value =  `${(total-totalotrosmedios).toLocaleString()}`;
    mapMediospago.set('1', total-totalotrosmedios); //inicialmente el valor total se establece para efectivo
        
}


function calcularmediospago(e:Event, totales: AccountTotals|null){
    let totalotrosmedios = 0;
    const total = Number(totales?.total??0);
    mediospago.forEach((item, index)=>{ //sumar todos los medios de pago menos el efectivo
        if(index>0)totalotrosmedios += parseFloat(item.value.trim().replace(/\./g, '').replace(',', '.'))||0;
    });
    if(totalotrosmedios<=total){
        
        mapMediospago.set('1', total-totalotrosmedios);
        if(total-totalotrosmedios == 0 && mapMediospago.has('1'))mapMediospago.delete('1'); //se elimina medio de pago efectivo
        
        const valorMedioPago = parseFloat((e.target as HTMLInputElement).value.trim().replace(/\./g, '').replace(',', '.'))||0;
        mapMediospago.set((e.target as HTMLInputElement).id, valorMedioPago);
        if(valorMedioPago == 0 && mapMediospago.has((e.target as HTMLInputElement).id))mapMediospago.delete((e.target as HTMLInputElement).id);
    }else{ //si la suma de los medios de pago superan el valor total, toma el ultimo input digitado y lo reestablece a su ultimo valor
        if(mapMediospago.has((e.target as HTMLInputElement).id)){
        (e.target as HTMLInputElement).value = mapMediospago.get((e.target as HTMLInputElement).id).toLocaleString();
        }else{
        (e.target as HTMLInputElement).value = '0';
        }
    }
    
    mediospago[0].value = (mapMediospago.get('1')??0).toLocaleString();  //medio de pago en efectivo
    
    calcularCambio(document.querySelector<HTMLInputElement>('#recibio')!.value);
    //ocultarMensajeMetodosPago();
}


function calcularCambio(recibido:string):void{
    recibido = recibido.trim().replace(/\./g, '').replace(',', '.');
    if(Number(recibido)>mapMediospago.get('1')){
    (document.querySelector('#cambio') as HTMLElement).textContent = (Number(recibido)-mapMediospago.get('1')).toLocaleString()+'';
    return;
    }
    (document.querySelector('#cambio') as HTMLElement).textContent = '0';
}

/*function pago(){
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
      
}*/