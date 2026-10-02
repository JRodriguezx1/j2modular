(()=>{
  if(!document.querySelector('.ventas'))return;

  const POS = (window as any).POS;

  const miDialogoPreciosAdicionales = document.querySelector('#miDialogoPreciosAdicionales') as HTMLDialogElement;
  const aplicarOpcionesProducto = document.querySelector('#aplicarOpcionesProducto') as HTMLButtonElement;
  const inputComisionProducto = document.querySelector('#comisionproducto') as HTMLInputElement;
  const inputCantidadCalculada = document.querySelector('#inputCantidadCalculada') as HTMLInputElement;
  const operationSum = document.querySelector('#operationSum') as HTMLInputElement;
  const operationLess = document.querySelector('#operationLess') as HTMLInputElement;
  const reset = document.querySelector('#reset') as HTMLInputElement;
  const textCantidadCalculada = document.querySelector('#textCantidadCalculada') as HTMLParagraphElement;
  const lastOperation = document.querySelector('#lastOperation') as HTMLParagraphElement;
  let cantidadTotal:number = 0;
  let historialCalculadora: string[] = [];

  let producto:  Partial<productsapi>|undefined = {};
  let productoConfigurado: Partial<productsapi> | undefined = {};

  function cargarPreciosAdicionales() {
    const listaPrecios = document.querySelector('#listaPrecios') as HTMLElement;
    while(listaPrecios.firstChild)listaPrecios.removeChild(listaPrecios.firstChild);
    producto?.preciosadicionales?.forEach(precio=>{
        const preciohtml = `
        <label class="group flex justify-between items-center p-5 rounded-2xl bg-white border border-slate-200 cursor-pointer transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <input
                    type="radio"
                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 inputprecioadicional accent-indigo-600 cursor-pointer"
                    data-idproducto="${producto?.id}"
                    name="precioSeleccionado"
                    value="${precio.precio}"
                >
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wider">
                        Precio adicional
                    </p>
                    <p class="text-xl font-bold text-slate-800">
                        $${Number(precio.precio).toLocaleString()}
                    </p>
                </div>
            </div>

            <span class="material-symbols-outlined text-slate-300 text-3xl transition-all duration-300 group-hover:scale-110 group-hover:text-indigo-500 group-hover:rotate-6">
                sell
            </span>
        </label>`;
      listaPrecios.insertAdjacentHTML('beforeend', preciohtml);
    });
  }


  aplicarOpcionesProducto.addEventListener('click', ()=>{
    const comision = inputComisionProducto.value.trim();
    const seleccionado = document.querySelector('.inputprecioadicional:checked') as HTMLInputElement;
    const inputPrecioLibre = document.querySelector('#precioLibre') as HTMLInputElement;
    if(producto != undefined && comision!=='' && !Number.isNaN(Number(comision))){  //ajustar porcentaje de comision a producto
      producto.percentcomision = Number(comision);
      //actualizar el carrito con el nuevo valor de comision
      const unitem:CarritoItem|undefined = (POS.carrito as CarritoItem[])?.find(x=>x.idproducto == producto?.id);
      if(unitem!=undefined)unitem.percentcomision = Number(comision);
    }
    miDialogoPreciosAdicionales.close();
    document.removeEventListener("click", POS.cerrarDialogoExterno);
    const precioLibre = obtenerNumero(inputPrecioLibre);
    const prioridadValor = precioLibre!=null?precioLibre:seleccionado?.checked?seleccionado.value:producto?.precio_venta;
    filtrarInsumos(productoConfigurado); //esta en app.ts
    if(producto != undefined)POS.actualizarCarrito(producto.id, cantidadTotal==0?1:cantidadTotal, true, true, prioridadValor+'', productoConfigurado);
  });


  //****CALCULADORA
  inputCantidadCalculada.addEventListener('keydown', (e: KeyboardEvent)=>{
    if(e.key === '+') {
      e.preventDefault();
      calcular('sumar');
    }
    if(e.key === '-') {
      e.preventDefault();
      calcular('restar');
    }
  });

  function calcular(operacion: 'sumar' | 'restar'):void{
    const cantidad = Number(inputCantidadCalculada.value);
    /*if(isNaN(cantidad) || inputCantidadCalculada.value === ''){
      inputCantidadCalculada.focus();
      return;
    }*/
    if(operacion === 'sumar'){
      cantidadTotal += cantidad;
      //lastOperation.textContent = `+${cantidad}`;
      historialCalculadora.push(`+${cantidad}`);
      lastOperation.textContent = historialCalculadora.join('   ');
    }else{
      cantidadTotal -= cantidad;
      //lastOperation.textContent = `-${cantidad}`;
      historialCalculadora.push(`-${cantidad}`);
      lastOperation.textContent = historialCalculadora.join('   ');
    }
    textCantidadCalculada.textContent = cantidadTotal.toFixed(2);
    inputCantidadCalculada.value = '';
    inputCantidadCalculada.focus();
  }

  operationSum.addEventListener('click', ()=>calcular('sumar'));
  operationLess.addEventListener('click', ()=>calcular('restar'));

  reset.addEventListener('click', ()=>{
    cantidadTotal = 0;
    inputCantidadCalculada.value = '';
    textCantidadCalculada.textContent = '0';
    lastOperation.textContent = '0';
    historialCalculadora.length = 0;
  });
  //****FIN CALCULADORA
  

  /////////// INSUMOS DINAMICAMENTE  /////////////
  function cargarInsumos(){
    const listaInsumos = document.querySelector('#listaInsumos') as HTMLElement;

    listaInsumos.innerHTML = "";
    const { obligatorios, insumosgrupo } = agruparInsumos();
    if(insumosgrupo.size === 0 && obligatorios.length === 0){
        renderVariacionesVacias(listaInsumos);
        return;
    }
    renderObligatorios(obligatorios, listaInsumos);
    insumosgrupo.forEach(insumogrupo =>renderGrupo(insumogrupo, listaInsumos));
    eventosCheckbox();
    eventosRadio();
    eventosCantidadInsumo();
  }

  function renderVariacionesVacias(contenedor: HTMLElement){
      contenedor.insertAdjacentHTML("beforeend",
          `<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
              <span class="material-symbols-outlined text-indigo-500 text-5xl mb-3">inventory_2</span>
              <h5 class="text-2xl font-bold text-slate-800">Sin variaciones configuradas</h5>
              <p class="mt-2 text-lg text-slate-500">Este producto no tiene insumos u opciones adicionales para seleccionar.</p>
          </div>`
      );
  }

  function agruparInsumos() {
    const obligatorios = producto?.insumos?.filter(i => i.grupos_insumos === null) ?? [];
    
    const insumosgrupo = new Map<number, insumo[]>();
    producto?.insumos
        ?.filter(i => i.grupos_insumos !== null)
        .forEach(insumo => {
            const idGrupo = Number(insumo.grupos_insumos?.id);
            if (!insumosgrupo.has(idGrupo))
                insumosgrupo.set(idGrupo, []);
            insumosgrupo.get(idGrupo)!.push(insumo);
        });

    return { obligatorios, insumosgrupo };
  }

  function renderObligatorios(insumos:any[], contenedor:HTMLElement){
      if(insumos.length===0) return;
      let html = `<div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 mt-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-emerald-600 text-3xl">check_circle</span>
                        <div>
                            <h5 class="text-xl font-bold text-slate-800">Incluidos</h5>
                            <p class="text-slate-500 text-lg leading-6">Estos insumos vienen incluidos con el producto.</p>
                        </div>
                    </div>
                <div class="space-y-2">`;

      insumos.forEach(insumo=>{
          html += `<div class="flex items-center gap-3 rounded-xl bg-white border border-slate-200 px-4 py-3">
                        <span class="material-symbols-outlined text-emerald-500">done</span>
                        <span class="font-medium text-slate-700">${insumo.nombre}</span>
                    </div>`;
      });

      html += `</div></div>`;
      contenedor.insertAdjacentHTML("beforeend", html);
  }

  //   insumos = [{idproducto:1, idsubproducto: 3, seleccionado: 1}]
  function renderGrupo(insumos:any[], contenedor:HTMLElement){
    const grupo = insumos[0].grupos_insumos;
    const panel = document.createElement("div");
    panel.className = "rounded-2xl border border-slate-200 bg-slate-50 p-5";
    panel.innerHTML = `
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 mb-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-indigo-600 text-3xl mt-1">
                    ${Number(grupo.tipo) === 0 ? 'radio_button_checked' : 'check_box'}
                </span>

                <div>
                    <h3 class="text-2xl font-bold text-slate-800">${grupo.nombre}</h3>
                    <p class="text-slate-500 text-lg leading-6 mt-1">
                        ${Number(grupo.tipo) === 0 ? 'Seleccione solamente una opci&oacute;n.' : 'Puede seleccionar una o varias opciones.'}
                    </p>
                </div>
            </div>
        </div>
        <div class="space-y-3" data-opciones-grupo></div>`;

    contenedor.appendChild(panel);
    const opciones = panel.querySelector('[data-opciones-grupo]') as HTMLElement;

    if(Number(grupo.tipo)===0){
        renderRadios(insumos, opciones);
    }else{
        renderCheckbox(insumos, opciones);
    }
  }

  function renderRadios(insumos:any[], contenedor:HTMLElement){
      insumos.forEach(insumo=>{
          contenedor.insertAdjacentHTML("beforeend",
              `<label class="group flex items-center justify-between gap-4 rounded-2xl bg-white tarjeta-radio-insumo border border-slate-200 cursor-pointer px-4 py-4 transition-all duration-300 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md">

                  <div class="flex items-center gap-4 min-w-0">
                      <input
                          type="radio"
                          class="inputInsumoRadio w-6 h-6 accent-indigo-600 cursor-pointer shrink-0"
                          data-idproducto="${producto?.id}"
                          data-idinsumo="${insumo.id_subproducto}"
                          data-idgrupo="${insumo.grupos_insumos.id}"
                          name="grupo_${insumo.grupos_insumos.id}"
                          ${Number(insumo.seleccionado) ? "checked" : ""}
                      >
                      <p class="text-xl font-semibold text-slate-800 leading-6 truncate">${insumo.nombre}</p>
                  </div>
              </label>`
          );
      });
  }
  function renderCheckbox(insumos:any[], contenedor:HTMLElement){
      insumos.forEach(insumo=>{
        // Temporal hasta conectar con backend
          insumo.precio_extra = 1600;

          contenedor.insertAdjacentHTML("beforeend",
              `<label
                    data-idproducto="${producto?.id}"
                    data-idinsumo="${insumo.id_subproducto}"
                    data-idgrupo="${insumo.grupos_insumos.id}"
                    class="tarjeta-checkbox-insumo block rounded-2xl border border-slate-200 bg-white p-4 cursor-pointer transition-all duration-300 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md"
                >

                  <div class="flex items-center justify-between gap-4">
                      <div class="flex items-center gap-4 flex-1 min-w-0">
                          <input
                              type="checkbox"
                              class="inputInsumoCheckbox w-6 h-6 accent-indigo-600 cursor-pointer shrink-0"
                              ${Number(insumo.seleccionado) ? "checked":""}
                          >

                          <p class="text-xl font-semibold text-slate-800 leading-6 truncate">
                              ${insumo.nombre}
                          </p>
                      </div>

                      ${htmlPrecioExtra(insumo)}
                  </div>

                  ${htmlCantidad(insumo)}
              </label>`
          );
      });
  }

  function htmlPrecioExtra(insumo: insumo): string {
      const precioExtra = Number(insumo.precio_extra);

      if (precioExtra <= 0) return "";

      return `
          <span
              class="badge-precio-extra inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-200 px-3 py-1.5 text-base font-bold text-emerald-700 shadow-sm transition-transform duration-200 shrink-0">
              <span class="material-symbols-outlined text-base">add</span>
              $${precioExtra.toLocaleString('es-CO')}
          </span>`;
  }
  
  function htmlCantidad(insumo: insumo): string {
    if (Number(insumo.permite_aumentar) === 0) return "";

    return `
        <div class="contenedor-cantidad overflow-hidden transition-all duration-300 ease-out ${Number(insumo.seleccionado)
                ? 'max-h-40 opacity-100 mt-5 translate-y-0'
                : 'max-h-0 opacity-0 mt-0 translate-y-2'}">

            <p class="mb-3 text-base font-semibold text-slate-600 text-center">Cantidad a agregar</p>
            <div class="flex justify-center mt-3">
                <div class="inline-flex items-center rounded-2xl border border-slate-300 overflow-hidden shadow-sm bg-white">

                    <button
                        type="button"
                        class="btnCantidadMenos w-14 h-14 active:scale-95 transition-all duration-150 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-2xl font-bold">
                        -
                    </button>
                    <input
                        type="number"
                        value="1"
                        min="1"
                        class="inputCantidadInsumo w-28 h-14 text-center text-lg font-bold border-x border-slate-300 outline-none focus:ring-0"
                    >
                    <button
                        type="button"
                        class="btnCantidadMas w-14 h-14 active:scale-95 transition-all duration-150 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-2xl font-bold">
                        +
                    </button>
                </div>
            </div>
        </div>`;
  }

  function eventosCheckbox():void {
      const checkboxes = document.querySelectorAll<HTMLInputElement>(".inputInsumoCheckbox");
      checkboxes.forEach(checkbox => {
          checkbox.addEventListener("change", actualizarCheckbox);
          // 🔥 Pinta la tarjeta si ya viene seleccionada
          if(checkbox.checked)
              checkbox.closest(".tarjeta-checkbox-insumo")?.classList.add("border-indigo-400", "bg-indigo-50", "ring-2", "ring-indigo-100", "shadow-md");
      });
  }

  function eventosRadio():void {
    const radios = document.querySelectorAll<HTMLInputElement>(".inputInsumoRadio");
    radios.forEach(radio => radio.addEventListener("change", actualizarRadio));
  }

  function eventosCantidadInsumo():void {
    const cantidades = document.querySelectorAll<HTMLInputElement>(".inputCantidadInsumo");
    cantidades.forEach(input => input.addEventListener("change", actualizarCantidadInsumo));
  }

  function actualizarCantidadInsumo(e: Event):void {
    const input = e.target as HTMLInputElement;
    const tarjeta = input.closest(".tarjeta-checkbox-insumo") as HTMLLabelElement;
    const idInsumo = Number(tarjeta.dataset.idinsumo);
    const insumo = productoConfigurado?.insumos?.find(item => Number(item.id_subproducto) === idInsumo);
    if(!insumo)return;

    const cantidadBase = Number(producto?.insumos?.find(item => Number(item.id_subproducto) === idInsumo)?.cantidadsubproducto ?? 0);
    const cantidad = Number(input.value);
    const cantidadValida = Number.isFinite(cantidad) && cantidad >= cantidadBase ? cantidad : cantidadBase;

    insumo.cantidadsubproducto = cantidadValida.toString();
    input.value = cantidadValida.toString();
  }

  function actualizarCheckbox(e: Event):void {
    const checkbox = e.target as HTMLInputElement;
    const tarjeta = checkbox.closest(".tarjeta-checkbox-insumo") as HTMLLabelElement;
    const idInsumo = Number(tarjeta.dataset.idinsumo);
    const insumo = productoConfigurado?.insumos?.find(i => Number(i.id_subproducto) === idInsumo);

    if (!insumo) return;

    insumo.seleccionado = checkbox.checked ? '1' : '0';

    // 🔥 Obtener la tarjeta
    const badge = tarjeta?.querySelector(".badge-precio-extra");
    const contenedorCantidad =
        tarjeta?.querySelector(".contenedor-cantidad");
    if (!tarjeta) return;

    if (checkbox.checked) {
        tarjeta.classList.add("border-indigo-400", "bg-indigo-50", "ring-2", "ring-indigo-100", "shadow-md", "scale-[1.01]");

        badge?.classList.add("scale-110");

        setTimeout(() => { badge?.classList.remove("scale-110"); }, 150);
        
        contenedorCantidad?.classList.remove("max-h-0", "opacity-0", "mt-0");
        contenedorCantidad?.classList.add("max-h-40", "opacity-100", "mt-5");
    } else {
        tarjeta.classList.remove("border-indigo-400", "bg-indigo-50", "ring-2", "ring-indigo-100", "shadow-md", "scale-[1.01]");
        contenedorCantidad?.classList.remove( "max-h-40", "opacity-100", "mt-5");
        contenedorCantidad?.classList.add( "max-h-0", "opacity-0", "mt-0");
    }
  }


  function actualizarRadio(e: Event):void {
    const radio = e.target as HTMLInputElement;
    if(!radio.checked)return;

    const idGrupo = Number(radio.dataset.idgrupo);
    const idInsumo = Number(radio.dataset.idinsumo);
    productoConfigurado?.insumos?.forEach(insumo => {
        if(Number(insumo.grupos_insumos?.id) === idGrupo)insumo.seleccionado = '0';
    });

    const seleccionado = productoConfigurado?.insumos?.find(i=>Number(i.id_subproducto) === idInsumo);
    if(seleccionado)seleccionado.seleccionado = '1';

    document.querySelectorAll(".tarjeta-radio-insumo").forEach(card =>card.classList.remove("border-indigo-400", "bg-indigo-50", "shadow-lg"));

    const tarjeta = radio.closest(".tarjeta-radio-insumo");
    tarjeta?.classList.add("border-indigo-400", "bg-indigo-50", "shadow-lg");  
  }
  ///////// FIN INSUMOS DINAMICOS  //////////


  const gestionarPreciosAdicionales = {
    miDialogoPreciosAdicionales,
    abrirDialogo(elementProduct: HTMLElement) {
      (document.querySelector('#textCardProduct') as HTMLParagraphElement).textContent = elementProduct.querySelector('.card-producto')?.textContent??'';
      miDialogoPreciosAdicionales.showModal();
        const inputPrecioLibre = document.querySelector('#precioLibre') as HTMLInputElement;

    if(inputPrecioLibre)inputPrecioLibre.value = "";
      cantidadTotal = 0;
      textCantidadCalculada.textContent = '0';
      lastOperation.textContent = '0';
      historialCalculadora.length = 0;
      const products = POS.products as productsapi[];
      producto = products.find(x=>x.id==elementProduct.dataset.id!);
      productoConfigurado = structuredClone(producto);
      cargarPreciosAdicionales();
      cargarInsumos();
      document.addEventListener("click", POS.cerrarDialogoExterno);
    },
  };

  POS.gestionarPreciosAdicionales = gestionarPreciosAdicionales;

})();
