const mobilemenu = document.querySelector('#mobile-menu');  //seleccion por id
const sidebar = document.querySelector('.sidebar') as HTMLElement|null;  //seleccion por calses
const btnmenux = document.querySelector('#mobile-menux');
const barra = document.querySelector('.barra-mobile') as HTMLElement|null;
const nametop:HTMLElement|null = document.querySelector('.nametop');
const selectSucursal = document.querySelector('#selectSucursal') as HTMLSelectElement;
const sucursalSeleccionada = document.querySelector('#sucursalSeleccionada') as HTMLElement|null;
const opcionesSucursal = document.querySelectorAll('.js-sucursal-option') as NodeListOf<HTMLElement>;
const toggleSucursalMenu = document.querySelector('#toggleSucursalMenu') as HTMLElement|null;
const sucursalMenuLista = document.querySelector('#sucursalMenuLista') as HTMLElement|null;
const iconSucursalMenu = document.querySelector('#iconSucursalMenu') as HTMLElement|null;
const btnMoneda = document.querySelector('#btnMoneda') as HTMLButtonElement;
const miDialogoMonedaEquivalente = document.querySelector('#miDialogoMonedaEquivalente') as HTMLDialogElement;
declare let Chart:any; //declare le indica a typescript que la variable chart viene de manera externa
declare const Swal: any;
declare var moment: any;
declare let List: any; 
declare let bwipjs: any;
declare const QRCode: any;
declare const mediosPagoDB: MedioPago[];  //mediosPagoDB inyectada por medio de json desde la vista ventas/index.php interfaz en ventas.type.ts
declare const clientesDB: Cliente[];
declare let comisionTotalEmpleadosDB: number;
declare let comisionTotalPagadaBusinessDB: number;
declare const getParam:any;  //getParam inyectada por medio de json desde la vista ventas/index.php interfaz en ventas.type.ts
declare const percentComisionUser:string;
declare let deudatotalCiente:string;
declare const sucursal:any;

let monedas: divisa[] = [
    {
        id: 2,
        nombre: 'Peso colombiano',
        codigo: 'COP',
        simbolo: '$'
    },
    {
        id: 3,
        nombre: 'Bolívar venezolano',
        codigo: 'VES',
        simbolo: 'Bs.'
    },
    {
        id: 4,
        nombre: 'Dólar estadounidense',
        codigo: 'USD',
        simbolo: 'US$'
    },
    {
        id: 5,
        nombre: 'Euro',
        codigo: 'EUR',
        simbolo: '€'
    },
    {
        id: 6,
        nombre: 'Real brasileño',
        codigo: 'BRL',
        simbolo: 'R$'
    }
];

(window as any).POS = (window as any).POS || {};

 // Submdulos
  if (!(window as any).POS.gestionarDescuentos) {
    (window as any).POS.gestionarDescuentos = {};
  }

if(mobilemenu){
    mobilemenu.addEventListener('click', function(){
      if (sidebar && barra) {
        sidebar.classList.toggle('mostrar');
        barra.classList.toggle('ocultarmenu');
      }
    });
}
if(btnmenux){
    btnmenux.addEventListener('click', function(){
      if (sidebar && barra) {
        sidebar.classList.toggle('mostrar');
        barra.classList.toggle('ocultarmenu');
      }
    });
}
/////////////////////// animacion del sidebar toggle ///////////////////////////
document.querySelector('.sidebartoggle')!.addEventListener('click', (e)=>{
  if(sidebar)sidebar.classList.toggle('sidebar-fija');
});


///////////////////// FUNCION QUE IMPRIME MENSAJE TIPO ALERTA /////////////////////
function msjAlert(tipo:string, msj:string, divmsjalerta:HTMLElement):void{
  const esError = tipo === 'error';
  const titulo = esError ? 'Atencion requerida' : 'Operacion exitosa';
  const icono = esError ? 'fa-circle-exclamation' : 'fa-circle-check';
  divmsjalerta.insertAdjacentHTML('beforeend', `<div class="alerta alerta__${tipo}" role="alert">
      <span class="alerta__icono"><i class="fa-solid ${icono}"></i></span>
      <span class="alerta__contenido">
        <strong>${titulo}</strong>
        <span>${msj}</span>
      </span>
    </div>`
  );
  borrarMsjAlert(divmsjalerta);
}
//////////////////// BORRAR MENSAJES TIPO ALERTA /////////////////////
/*(borrarMsjAlert =()=>{  //se aplica de manera global
  const msj = document.querySelector('#divmsjalerta');
  if(document.querySelector('.alerta'))setTimeout(()=>{ while(msj.firstChild)msj.removeChild(msj.firstChild);}, 5000);
})();*/
function borrarMsjAlert(divmsj:HTMLElement):void{  //se aplica de manera global
  //const msj = document.querySelector('#divmsjalerta')!;
  if(document.querySelector('.alerta'))setTimeout(()=>{ while(divmsj.firstChild)divmsj.removeChild(divmsj.firstChild);}, 5000);
}
//////////////////// FUNCION QUE IMPRIME UN MENSAJE FORMATO TOAST ////////////////////
//msjalertToast('error', 'ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡Error!', 'debe seleccionar una imagen')
function msjalertToast(icono:string, tipo:string, msj:string){
    Swal.fire({
    icon: icono,
    title: tipo,
    text: msj,
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3200,
    timerProgressBar: true,
    showClass: {
      popup: 'animate__animated animate__fadeInRight animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutRight animate__faster'
    },
    customClass: {
      popup: `j2-toast j2-toast--${icono}`,
      icon: 'j2-toast__icon',
      title: 'j2-toast__title',
      htmlContainer: 'j2-toast__text',
      timerProgressBar: 'j2-toast__progress'
    }
    });
}

/////////////////// FUNCION CONTADOR DE CARACTERES ////////////////////
(function countchars():void{
  const numinput = document.querySelectorAll('.count-charts') as NodeListOf<HTMLElement>;  
  numinput.forEach(element =>{ //element es cada label
      element.textContent = (parseInt(element.dataset.num!)-(element.previousElementSibling as HTMLInputElement).value.length).toString();
      element.previousElementSibling?.addEventListener('input', (e)=>{ //seleccionamos el input o el textarea en donde se escribe y se le da el evento de teclas
          element.textContent = parseInt(element.dataset.num!)-(e.target as HTMLInputElement).value.length+"";
            
          if(parseInt(element.dataset.num!)-(e.target as HTMLInputElement).value.length <= 0){
              let cadena = (e.target as HTMLInputElement).value.slice(0, parseInt(element.dataset.num!));
              (e.target as HTMLInputElement).value = cadena;
              element.textContent =`${0}`;
          }
      });
  });
})();

/////////////////// paginacion de negocio empleado, malla, config //////////////////
if(document.querySelector('#tabulacion')){ // view/admin/adminconfig/index.php
  const renderid = document.querySelector('#tabulacion input[type="radio"]:checked')!.nextElementSibling as HTMLElement; //se selecciona el input cheked y luego su span q contiene el id de la pagina a mostrar
  document.querySelector<HTMLElement>(`.${renderid.id}`)!.style.display = "block"; //mostramos la primera seccion
  const btns_navtabs = document.querySelectorAll('.tabs span');
  const paginas = document.querySelectorAll<HTMLElement>('.paginas'); //seleccionamos las secciones o paginas a mostrar
  btns_navtabs.forEach(Element => {
      Element.addEventListener('click', (e)=>{ //cada btn o enlace
          const target = e.target as HTMLElement;
          paginas.forEach(pagina =>pagina.style.display = "none"); ////quitamos la class mostrarseccion a todas las secciones
          document.querySelector<HTMLElement>(`.${target.id}`)!.style.display = "block"; //añadimos la class mostrarseccion a la la seccion o pagina correspondiente
          ajustarDataTable();
      });
  });
}

let dataTableTimer:number|undefined;
function ajustarDataTable(){
  if(dataTableTimer)window.clearTimeout(dataTableTimer);
  window.setTimeout(()=>{
      try{
        (($.fn as any).dataTable)?.tables({ visible: true, api: true }).columns.adjust().responsive?.recalc();
      }catch(error){
        console.log(error);
      }
  }, 0);
}


function mesyanoactual():[string, number]
{
  const fecha = new Date();
  const mesTexto:string = fecha.toLocaleString('es-CO', { month: 'long' });
  const ano:number = fecha.getFullYear();
  return [mesTexto, ano];
}

function getDgv(nit: number): number {
    const multiplicadores = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
    const digitos = nit.toString().trim().split('').map(Number);
    let suma = 0;
    digitos.forEach((digito, indice) => {
        const posicionMultiplicador = digitos.length - indice;
        suma += digito * multiplicadores[posicionMultiplicador - 1];
    }); 
    const modulo = suma%11;
    return modulo > 1 ?(11 - modulo):modulo;
}


let audioCtx: AudioContext;
function beep(frecuencia = 800, duracion = 60) {
  if (!audioCtx) audioCtx = new (window.AudioContext || (window as any).webkitAudioContext)();
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();

  osc.frequency.value = frecuencia;
  osc.type = 'square';
  gain.gain.value = 0.08;

  osc.connect(gain);
  gain.connect(audioCtx.destination);

  osc.start();
  osc.stop(audioCtx.currentTime + duracion / 1000);
}


function flashCantidad(element:HTMLElement, color:string) {
    const clase = color === 'up'? 'bg-green-200': 'bg-red-200';
    element.classList.add(clase);
    setTimeout(() => element.classList.remove(clase), 120);
}


function bytesToBase64(data: string|Uint8Array) {
    if (typeof data === 'string')
      return btoa(data);

    let binary = '';
    if(data instanceof Uint8Array)
      for (let i = 0; i < data.length; i++) 
          binary += String.fromCharCode(data[i]);
    return btoa(binary);
}


function formatearMoneda(input: HTMLInputElement): void {
    let valor = input.value.replace(/[^\d,]/g, '');
    //const partes = valor.split(',');
    // Formatear parte entera
    //partes[0] = parseInt(partes[0] || '0').toLocaleString('es-CO');

    let [entera, ...decimales] = valor.split(',');
    entera= parseInt(entera || '0').toLocaleString('es-CO');
    /*if (partes.length > 2) {
      valor = partes[0] + ',' + partes.slice(1).join('');
    }*/
    // MÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ximo 2 decimales
    //if (partes[1])partes[1] = partes[1].substring(0, 2);
    //input.value = partes.join(',');
    input.value = entera+(decimales.length ? ',' + decimales.join('') : '');
}


function obtenerNumero(input: HTMLInputElement):number|null{
    if(input == null)return null;
    const valor = input.value.replace(/\./g, '').replace(',', '.').trim();
    if (valor === '')return null;
    const numero = Number(valor);
    return Number.isNaN(numero) ? null : numero;
}


function filtrarInsumos(productoConfigurado:Partial<productsapi>|undefined):void{
  const insumos = productoConfigurado?.insumos;
  if(!insumos?.length) return;

  const insumosActivos = insumos.filter(insumoActual =>
    insumoActual.grupos_insumos === null || insumoActual.seleccionado === "1"
  );
  insumos.splice(0, insumos.length, ...insumosActivos);
}


 function formatCantidadBadge(cantidad:number): string{
    if(cantidad >= 1000000){
    return (cantidad / 1000000).toLocaleString('es-CO', {maximumFractionDigits: 1}) + 'M';
    }
    if(cantidad >= 10000){
    return (cantidad / 1000).toLocaleString('es-CO', {maximumFractionDigits: 1}) + 'K';
    }
    return cantidad.toLocaleString('es-CO');
  }

  function ajustarAnchoCantidad(input:HTMLInputElement, cantidad:number|string): void{
    const largo = String(cantidad || 0).length;
    input.style.width = `${Math.min(Math.max(largo + 2, 5), 10)}ch`;
  }

function cerrarMenuSucursal():void{
  sucursalMenuLista?.classList.add('hidden');
  iconSucursalMenu?.classList.remove('rotate-180');
}

toggleSucursalMenu?.addEventListener('click', (event:MouseEvent)=>{
  event.stopPropagation();
  sucursalMenuLista?.classList.toggle('hidden');
  iconSucursalMenu?.classList.toggle('rotate-180');
});

opcionesSucursal.forEach((opcion)=>{
  opcion.addEventListener('click', ()=>{
    const value = opcion.dataset.sucursalvalue || '';
    const label = opcion.dataset.sucursallabel || opcion.textContent?.trim() || 'Cambiar de Sede';
    if(sucursalSeleccionada){
      sucursalSeleccionada.textContent = label;

    }
    cerrarMenuSucursal();
  });
});

document.addEventListener('click', (event:MouseEvent)=>cerrarMenuSucursal());

//evento para el cambio de sucursal
selectSucursal.addEventListener('change', async(e: Event)=>{
  const datos = {
      idsucursal: (e.target as HTMLInputElement).value,
      //idusuario: ''
  };

  const url = "/admin/api/changeSucursal/select";
  const respuesta = await fetch(url, {
      method: 'POST', 
      headers: { "Accept": "application/json", "Content-Type": "application/json" },
      body: JSON.stringify(datos) 
  });
  const resultado = await respuesta.json();
  if(resultado.error){
    msjalertToast('error', 'Error', resultado.error);
    return;
  }else{
    msjalertToast('success', 'Exito', resultado.success);

    setTimeout(()=>{ window.location.href = "/admin/dashboard"; }, 850);
    
  }
  
});


//abrir ventana del modal de cambio de divisa
document.addEventListener("click", cerrarDialogo);
btnMoneda.addEventListener('click', ()=>{
  miDialogoMonedaEquivalente.showModal();
});


document.querySelector('#formTasaCambio')?.addEventListener('submit', async(e)=>{
  e.preventDefault();
  const divisa = document.querySelector('#divisa') as HTMLSelectElement|null;
  const tasaCambio = document.querySelector('#tasaCambio') as HTMLInputElement|null;
  const btnConfirmar = document.querySelector('#btnConfirmarTasaCambio') as HTMLButtonElement|null;
  const idmoneda = Number(divisa?.value);
  const tasacambio = tasaCambio ? obtenerNumero(tasaCambio) : null;

  if(!Number.isInteger(idmoneda) || idmoneda <= 0 || tasacambio === null || tasacambio <= 0){
    msjalertToast('error', 'Error', 'Selecciona una moneda e ingresa una tasa de cambio válida.');
    return;
  }

  const datos = { idmoneda, tasacambio };

  try{
        if(btnConfirmar)btnConfirmar.disabled = true;
        const url = "/admin/api/param/changeTasaCambio"; //llamado a la API REST Dian-laravel para consultar las resoluciones
        const respuesta = await fetch(url, {
          method: 'POST',
          headers: { "Accept": "application/json", "Content-Type": "application/json" },
          body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();
        if(!respuesta.ok || resultado.error){
          msjalertToast('error', 'Error', resultado.error || 'No fue posible actualizar la tasa de cambio.');
          return;
        }
        msjalertToast('success', 'Éxito', resultado.success);
        miDialogoMonedaEquivalente.close();
        //inyectar en la vista de ventas.
        if(getParam.mostrar_tasa_de_cambio_de_divisa.valor_final === '1'){
          const divisa = monedas.find(x=>x.id === resultado.data.idmoneda);
          (document.querySelector('#equivalente') as HTMLParagraphElement).textContent = '$'+((window as any).POS.valorTotal.total * resultado.data.tasacambio).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
          (document.querySelector('#monedaCodigo') as HTMLParagraphElement).textContent = divisa?.codigo??'';
        }
        sucursal.tasacambio = resultado.data.tasacambio;
        sucursal.idmoneda = resultado.data.idmoneda;
      }catch(error){
        console.error(error);
        msjalertToast('error', 'Error', 'No fue posible conectar con el servidor.');
      }finally{
        if(btnConfirmar)btnConfirmar.disabled = false;
      }
});


function cerrarDialogo(event:Event) {
  const f = event.target;
  if (f=== miDialogoMonedaEquivalente || (f as HTMLInputElement).value === 'Salir') {
    miDialogoMonedaEquivalente.close();
  }
}
