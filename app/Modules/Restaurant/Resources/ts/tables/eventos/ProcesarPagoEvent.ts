import { AccountTotals } from "../accountApi";
import { createCart } from "../cart";
import { createCli } from "../cli";
import { createCliDian } from "../cliDian";
import { msjalertToast } from "../../appFunction";
import { ProcesarPago, PaymentError, payData  } from "../orderApi";


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
// Cuenta a la que pertenecen los valores actuales del modal.
let paymentAccountId: number | null = null;

type Cart = ReturnType<typeof createCart>;
type Cli = ReturnType<typeof createCli>;
type CartKey = | `account:${number}` | `occupation:${number}` | `table:${number}`;
interface ActiveOrder{
    resourceId: number,
    occupationId: number|null,
    accountId: number|null,
    cartKey: CartKey,
    cart: Cart,
    cli: Cli,
    cliDian: ReturnType<typeof createCliDian>,
    totales: AccountTotals|null,
    summaryPending: boolean,
}

interface PaymentMethodData{
    idmediopago: number,
    valor: string // Importe decimal sin separadores: "25000.50"
}

export interface AccountPaymentData{
    accountId: number,
    idcaja: number,
    idconsecutivo: number,
    expectedTotal: string,
    mediosPago: PaymentMethodData[],
}

export function registerProcesarPagoEvents(getActiveOrder: () => ActiveOrder|null, setSavingOrder: (saving: boolean) => void, finishPaidAccount: (result: payData) => void): void{


    processPaymentButton?.addEventListener('click', (event)=>{
        event.preventDefault();
        // Evitar inicializar nuevamente un modal que ya está abierto.
        if(miDialogoFacturar.open)return;
        // Ahora este callback usa getEditableOrder().
        const orden = getActiveOrder();
        if(orden === null){
            msjalertToast('warning', 'Cuenta no disponible', 'Espera a que termine la operación. Si hay una confirmación pendiente, pulsa Guardar pedido para recuperarla.');
            return;
        }
        // Un carrito temporal todavía no tiene una cuenta para cobrar.
        if(orden.accountId === null || orden.occupationId === null){
            msjalertToast('warning', 'Guarda el pedido', 'Primero debes guardar el pedido para crear la cuenta.');
            return;
        }
        // Puede existir una cuenta con importes anteriores todavía en memoria.
        if(orden.summaryPending){
            msjalertToast('warning', 'Resumen pendiente', 'Cierra y vuelve a abrir la mesa para actualizar los consumos y totales antes de cobrar.');
            return;
        }

        // El cobro debe incluir todos los productos que preparaste.
        if(orden.cart.getItems().length > 0){
            msjalertToast('warning','Productos pendientes', 'Pulsa Guardar pedido antes de cobrar la cuenta.');
            return;
        }

        try{
        // undefined significa que no hay cambios de cliente.
        // null también es un cambio: retirar el cliente.
            if(orden.cli.getClientChange() !== undefined){
                msjalertToast('warning', 'Cliente pendiente', 'Pulsa Guardar pedido para guardar el cambio de cliente.');
                return;
            }
            // Necesitamos un resumen disponible y un total válido.
            if(orden.totales === null){
                msjalertToast('warning', 'Resumen no disponible', 'Vuelve a abrir la mesa para consultar los totales.');
                return;
            }

            const total = Number(orden.totales.total);
            // Permitimos cero: una cuenta puede tener cortesías.
            if(!Number.isFinite(total) || total < 0){
                msjalertToast('warning', 'Total inválido', 'Vuelve a abrir la mesa para actualizar el resumen.');
                return;
            }

            document.querySelector('.Efectivo')?.setAttribute('readonly', 'true');
            document.querySelector('#inputscreditos')?.classList.add('hidden');
            document.querySelector('#inputscreditos')?.classList.remove('flex');
            // Conservamos el cálculo actual de los medios de pago.
            // Efectivo sigue siendo el total menos los otros medios.
            initModalPago(orden.accountId, orden.totales);
            // Abrir solamente después de pasar las validaciones.
            miDialogoFacturar.showModal();
        }catch(error){
            msjalertToast('warning', 'Revisar cuenta', error instanceof Error ? error.message: 'No fue posible preparar el cobro.');
        }
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


    ////////    Procesar el pago    //////////
    let processingPayment = false;
    // Evitar que Escape cierre el modal mientras se está cobrando.
    miDialogoFacturar.addEventListener('cancel', event => {
        if(processingPayment)event.preventDefault();
    });

    const formFacturar = miDialogoFacturar.querySelector<HTMLFormElement>('#formfacturar');
    formFacturar?.addEventListener('submit', async event => {
        event.preventDefault();
        if(!miDialogoFacturar.open || processingPayment  || btnPagar.disabled)return;

        // Conservar la referencia de la cuenta que estamos cobrando.
        const orden = getActiveOrder();
        let paymentConfirmed = false;
        let locked = false;
        try{
            const datos = prepararDatosCobro(orden); // Este callback ya utiliza getEditableOrder().
            processingPayment = true;
            btnPagar.disabled = true;
            // Bloquear campos y cierre del modal.
            miDialogoFacturar.inert = true;
            miDialogoFacturar.setAttribute('aria-busy', 'true');
            // Bloquear productos, clientes y cambios de mesa.
            setSavingOrder(true);
            locked = true;
            const result = await ProcesarPago(datos);
            // A partir de aquí, cualquier fallo es de actualización visual: el servidor ya confirmó el cobro.
            paymentConfirmed = true;
            // La cuenta ya fue cobrada. Mientras conectamos la limpieza del workspace, impedir otro cobro usando el resumen anterior.
            if(orden)orden.summaryPending = true;
            // Cierre programático después de confirmar el pago.
            miDialogoFacturar.close();
            resetPaymentModal();
            // Limpiar la cuenta y consultar el estado actual de las mesas.
            finishPaidAccount(result);
            console.log('Resultado del cobro:', result);
            msjalertToast('success', 'Cobro realizado', 'El cobro se completó correctamente.');
        }catch(error){
            if(paymentConfirmed){
                // No presentar este fallo como un pago rechazado.
                msjalertToast('warning', 'Cobro realizado', 'El pago se confirmó, pero no se pudo completar la actualización de la pantalla. No vuelvas a cobrar esta cuenta.');
            }else{
                if(orden && error instanceof PaymentError && error.requiresRefresh) 
                    orden.summaryPending = true; //No sabemos con certeza si el servidor completó el cobro. prepararDatosCobro bloqueará otro intento hasta actualizar.
                msjalertToast('warning', 'Revisar cobro', error instanceof Error ? error.message : 'No fue posible procesar el cobro.');
            }
        }finally{
            // Liberar solamente el bloqueo adquirido por este evento.
            if(locked)setSavingOrder(false);
            // Liberar el botón tanto en éxito como en error. summaryPending impide reenviar cuando hace falta actualizar.
            processingPayment = false;
            btnPagar.disabled = false;
            miDialogoFacturar.inert = false;
            miDialogoFacturar.removeAttribute('aria-busy');
        }
    });
}

function resetPaymentModal(): void {
    // Esta distribución ya pertenecía a una cuenta cobrada.
    paymentAccountId = null;
    mapMediospago.clear();
    mediospago.forEach(input => {input.value = '0';});
    const recibido = document.querySelector<HTMLInputElement>('#recibio');
    const cambio = document.querySelector<HTMLElement>('#cambio');
    const totalPagar = document.querySelector<HTMLElement>('#totalPagar');
    const observacion = miDialogoFacturar.querySelector<HTMLTextAreaElement>('#observacion');
    if (recibido) recibido.value = '';
    if (cambio) cambio.textContent = '0';
    if (totalPagar) totalPagar.textContent = '$0';
    if (observacion) observacion.value = '';
}


function selectFacturadorSegunCaja(z:HTMLSelectElement){
    $('#facturador').val(z.options[z.selectedIndex].dataset.idfacturador??'1');
}

function abrirMediosPago(){
    if(acordeonMediosPago)acordeonMediosPago.checked = true;
    const primerMedioEditable = Array.from(mediospago).find(input=>!input.readOnly && !input.disabled);
    setTimeout(()=>primerMedioEditable?.focus(), 80);
}

function initModalPago(accountId: number, totales: AccountTotals): void{
    const total = Number(totales.total);
    const efectivo = document.querySelector<HTMLInputElement>('.Efectivo');
    const recibido = document.querySelector<HTMLInputElement>('#recibio');
    const cambio = document.querySelector<HTMLElement>('#cambio');
    const totalPagar = document.querySelector<HTMLElement>('#totalPagar');

    if(!efectivo || !recibido || !cambio || !totalPagar)
        throw new Error('El modal de cobro está incompleto.');

    // Primera apertura o una cuenta diferente: quitar los valores que pertenecían a la cuenta anterior.
    if(paymentAccountId !== accountId){
        mediospago.forEach(input => {input.value = '0';});
        recibido.value = '';
        cambio.textContent = '0';
    }

    totalPagar.textContent = `$${total.toLocaleString()}`;
    // Reconstruir el mapa con los valores visibles. En la misma cuenta conservamos los otros medios de pago.
    mapMediospago.clear();
    let totalOtrosMedios = 0;
    mediospago.forEach(input => {
        // El efectivo se calcula después como el saldo restante.
        if(input === efectivo)return;
        const valor = parseFloat(input.value.trim().replace(/\./g, '').replace(',', '.')) || 0;
        totalOtrosMedios += valor;
        if(valor > 0)mapMediospago.set(input.id, valor);
    });

    // El total pudo disminuir por un descuento o una anulación.
    // Conservamos tu comportamiento: si la distribución ya no cabe, reiniciamos los medios y asignamos el total a efectivo.
    if(totalOtrosMedios > total){
        totalOtrosMedios = 0;
        mapMediospago.clear();
        mediospago.forEach(input => { input.value = '0'; });
    }

    const valorEfectivo = total - totalOtrosMedios;
    efectivo.value = valorEfectivo.toLocaleString();
    // Tu cálculo actual identifica el efectivo con el ID "1".
    if(valorEfectivo > 0)mapMediospago.set('1', valorEfectivo);
    // Actualizar el cambio aunque estemos reabriendo la misma cuenta.
    calcularCambio(recibido.value);
    // Asociar los valores preparados con esta cuenta.
    paymentAccountId = accountId;
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


function calcularCambio(recibido: string): void{
    const valorRecibido = parseFloat(recibido.trim().replace(/\./g, '').replace(',', '.')) || 0;
    // Si toda la cuenta se paga con otros medios, efectivo es cero.
    const efectivo = mapMediospago.get('1') ?? 0;
    // Si lo recibido es menor al efectivo requerido, mostrar cero.
    const cambio = Math.max(0, valorRecibido - efectivo);
    const elemento = document.querySelector<HTMLElement>('#cambio');
    if(elemento)elemento.textContent = cambio.toLocaleString();
}


// Recibe un importe sin separadores de miles: "25000.50".
function importeACentavos(valor: string): number{
    if(!/^\d+(?:\.\d{1,2})?$/.test(valor))
        throw new Error('Los importes deben ser positivos o cero y tener máximo dos decimales.');

    const [entero, decimal = ''] = valor.split('.');
    const centavos = Number(entero) * 100 + Number(decimal.padEnd(2, '0'));
    if(!Number.isSafeInteger(centavos))throw new Error('El importe es demasiado grande.');
    return centavos;
}

// Lee el formato que muestran los campos del modal: "25.000,50".
// Un campo vacío equivale a cero.
function leerMedioEnCentavos(input: HTMLInputElement): number{
    const texto = input.value.trim();
    if (texto === '') return 0;
    // Aceptar enteros, separadores de miles y hasta dos decimales.
    if(!/^(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d{1,2})?$/.test(texto))
        throw new Error(`Revisa el importe de ${input.name || input.id}: utiliza máximo dos decimales.`);
    const normalizado = texto.replace(/\./g, '').replace(',', '.');
    return importeACentavos(normalizado);
}

// Verifica que exista una opción seleccionada con un ID válido.
function leerSeleccionId(select: HTMLSelectElement, nombre: string): number{
    const opcion = select.selectedOptions[0];
    const id = Number(opcion?.value);
    if(!opcion || opcion.disabled || !Number.isSafeInteger(id) || id <= 0)throw new Error(`Selecciona ${nombre}.`);
    return id;
}


function prepararDatosCobro(orden: ActiveOrder | null): AccountPaymentData{
    // Revalidar al pulsar Pagar, no solo al abrir el modal.
    if(orden === null || orden.accountId === null || orden.occupationId === null)
        throw new Error('No hay una cuenta disponible para cobrar.');

    if(paymentAccountId !== orden.accountId)
        throw new Error('El modal pertenece a otra cuenta. Ciérralo y vuelve a abrir el cobro.');

    if(orden.summaryPending || orden.totales === null)
        throw new Error('Actualiza el resumen de la cuenta antes de cobrar.');

    if(orden.cart.getItems().length > 0)
        throw new Error('Guarda los productos pendientes antes de cobrar.');

    if(orden.cli.getClientChange() !== undefined)
        throw new Error('Guarda el cambio de cliente antes de cobrar.');

    const idcaja = leerSeleccionId(btnCaja, 'una caja');
    const idconsecutivo = leerSeleccionId(btnTipoFacturador, 'un facturador');
    // El total del backend usa punto decimal, sin separadores de miles.
    const totalCentavos = importeACentavos(String(orden.totales.total));
    const pagos: PaymentMethodData[] = [];
    const ids = new Set<number>();
    let sumaCentavos = 0;
    // Leer los campos visibles para validar lo que el usuario ve. No recalculamos ni cambiamos su distribución aquí.
    mediospago.forEach(input => {
        const centavos = leerMedioEnCentavos(input);
        // No enviar medios cuyo importe sea cero.
        if(centavos === 0)return;
        const id = Number(input.id);
        if(!Number.isSafeInteger(id) || id <= 0)
            throw new Error('Hay un medio de pago con identificador inválido.');

        if (ids.has(id))
            throw new Error('Hay un medio de pago repetido.');

        ids.add(id);
        sumaCentavos += centavos;
        if(!Number.isSafeInteger(sumaCentavos))
            throw new Error('La suma de los medios de pago es demasiado grande.');
        pagos.push({idmediopago: id, valor: (centavos / 100).toFixed(2)});
    });

    if(sumaCentavos !== totalCentavos)
        throw new Error('La suma de los medios de pago debe coincidir con el total de la cuenta.');
    // Una cuenta con total cero permite una lista de pagos vacía.
    return { accountId:orden.accountId, idcaja, idconsecutivo, expectedTotal:(totalCentavos/100).toFixed(2), mediosPago:pagos };
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