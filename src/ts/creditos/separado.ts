(()=>{
  if(document.querySelector('.crearseparado')){

    const POS = (window as any).POS;
     
    const btnCrearSeparado = document.querySelector('#btnCrearSeparado');
    const miDialogoFacturar = document.querySelector('#miDialogoFacturar') as any;
    const tablaSeparado = document.querySelector('#tablaventa tbody') as HTMLBodyElement;
    const btnPagar = document.getElementById('btnPagar') as HTMLInputElement;
    const btnCaja = document.querySelector('#caja') as HTMLSelectElement; //select de la caja en el modal pagar
    const carritoVacio = document.querySelector('#carritoVacio') as HTMLElement;


    type conversionunidadesapi = {
      id:string,
      idproducto: string,
      idsubproducto: string,
      idunidadmedidabase: string,
      idunidadmedidadestino: string,
      nombreunidadbase: string,
      nombreunidaddestino: string,
      factorconversion: string,
    };

    interface Item {
      id_impuesto: number,
      facturaid: number,
      basegravable: number,
      valorimpuesto: number
    }

    let allConversionUnidades:conversionunidadesapi[] = [];
    let carrito:CarritoItem[]=[];
    let allproducts:productsapi[] = [];
    let filteredData: {id:string, text:string, tipo:string, tipoproducto:string, tipoproduccion:string, sku:string, unidadmedida:string, precio:string}[];   //tipo = 0 es producto simple,  1 = subproducto
    const valorTotal = {subtotal: 0, base: 0, valorimpuestototal: 0, dctox100: 0, descuento: 0, idtarifa: 0, valortarifa: 0, total: 0}; //datos global de la venta
    let factimpuestos:Item[] = [], tipoventa:string="Contado";
    const mapMediospago = new Map();
    
    const constImp: {[key:string]: number} = {};
    constImp['excluido'] = 0;
    constImp['0'] = 0;  //exento de iva, tarifa 0%
    constImp['5'] = 0.0476190476190476; //iva, tarifa al 5%,  Bienes/servicios al 5
    constImp['8'] = 0.0740740740740741; //inc, tarifa al 8%,  impuesto nacional al consumo
    constImp['16'] = 0.1379310344827586; //iva, tarifa al 16%,  contratos firmados con el estado antes de ley 1819
    constImp['19'] = 0.1596638655462185; //iva, tarifa al 19%,  tarifa general

    ($('#cliente') as any).select2({
      placeholder: "Seleccionar el cliente",
      maximumSelectionLength: 1,
      dropdownCssClass: 'select2-theme-dropdown'
    });
    ($('#frecuenciapago') as any).select2({
      placeholder: "Seleccionar el dia de pago",
      maximumSelectionLength: 1,
      dropdownCssClass: 'select2-theme-dropdown'
    });

     document.addEventListener("click", cerrarDialogoExterno);

    (async ()=>{
        try {
            const url = "/admin/api/allproducts"; //llamado a la API REST en el controlador almacencontrolador para treaer todas los productos simples y subproductos
            const respuesta = await fetch(url); 
            const resultado:productsapi[] = await respuesta.json();
            filteredData = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1').map(item => ({ id: item.id, text: item.nombre, tipo:item.tipoproducto??'1', tipoproducto: item.tipoproducto, tipoproduccion: item.tipoproduccion, sku: item.sku, unidadmedida: item.unidadmedida, precio:item.precio_venta }));
            activarselect2();
            allproducts = resultado.filter(x=>x.habilitarventa=='1'&&x.visible=='1');
        } catch (error) {
            console.log(error);
        }
    })();

    (async ()=>{
        try {
            const url = "/admin/api/allConversionesUnidades"; //llamado a la API REST en el controlador almacencontrolador para treaer todas las conversiones de unidades
            const respuesta = await fetch(url); 
            allConversionUnidades = await respuesta.json();
        } catch (error) {
            console.log(error);
        }
    })();

    //DESACTIVADO EL CANAL DE VENTA
    document.querySelector('#contenedorCanalVenta')?.classList.add('hidden');
    document.querySelector('#canalVenta')?.removeAttribute('required');

    function activarselect2(){
      ($('#articulo') as any).select2({ 
          data: filteredData,
          placeholder: "Selecciona un item",
          maximumSelectionLength: 1,
          dropdownCssClass: 'select2-theme-dropdown',
          /*
          templateResult: function (data:{id:string, text:string, tipo:string}) {
              // Personalizar cómo se muestra cada opción en el dropdown
              if (!data.id) { return data.text; }  // Si no hay id, solo mostrar el texto
              const html = `
                  <div class="custom-option">
                      <span class="item-name">${data.text}</span> 
                      <span class="item-type">${data.tipo}</span>  <!-- Mostrar tipo adicional -->
                  </div>`;
              return $(html);  // Devolver el HTML personalizado
          }*/
      });
    }

    ////// EVENTO AL SELECT ARTICULOS O ITEMS PARA SELECCIONAR EL ITEM Y AÑADIR AL CARRITO ////// 
    $("#articulo").on('change', (e)=>{
        let datos = ($('#articulo') as any).select2('data')[0];
        if(datos){
          const itemselected = allproducts.find(x=>x.id == datos.id)!
          const productoConfigurado = structuredClone(itemselected);
          filtrarInsumos(productoConfigurado);
          actualizarCarrito(datos.id, datos.precio, productoConfigurado);
          $("#articulo").val('null').trigger('change');
        }
    });

    function actualizarCarrito(id:string, precio:number = 1, productoConfigurado:productsapi|null){
      const index = carrito.findIndex(x=>x.idproducto==id && x.valorunidad == precio && mismaConfiguracion(x, productoConfigurado!));
      if(index == -1){  //si el item seleccionado no existe en el carrito, agregarlo.
          const productototal = Number(productoConfigurado?.precio_venta);
          const productovalorimp = productototal*constImp[productoConfigurado?.impuesto??'0']; //si producto.impuesto es null toma el valor de cero
          
          const item:CarritoItem = {
              id: '',
              idproducto: productoConfigurado?.id!,
              tipoproducto: productoConfigurado?.tipoproducto!,  ////tipo = 0 es producto simple,  1 = subproducto
              tipoproduccion: productoConfigurado?.tipoproduccion!,
              foto: '',
              costo: productoConfigurado?.precio_compra!,
              valorunidad: Number(productoConfigurado?.precio_venta),
              nombreproducto: productoConfigurado?.nombre!,
              rendimientoestandar: productoConfigurado?.rendimientoestandar!,
              cantidad: 1,
              stock: 1,
              promediostock: Number(productoConfigurado?.promediostock),
              prioridadcomision: productoConfigurado?.prioridadcomision!,
              percentcomision: productoConfigurado?.percentcomision!,
              valorcomision: 0,
              impuesto: productoConfigurado?.impuesto!,
              subtotal: productototal,
              base: productototal-productovalorimp,
              valorimp: productovalorimp,
              descuento: 0,
              total: productototal,
              insumos: productoConfigurado?.insumos??[]
          }
          carrito = [...carrito, item];
          POS.carrito = carrito;
      }else{  //si ya existe en el carrito, sumar
        sumarcantidad(carrito[index], carrito[index].cantidad+1, index);
      }
      printItemTable();
      valorCarritoTotal();
    }


    function mismaConfiguracion(productCarrito: any, producto2: productsapi):boolean {
        const mapaProduct2 = new Map(producto2.insumos.map((ins:any) => [ins.id_subproducto, ins]));
        // Deben ser el mismo producto
        if(productCarrito.idproducto !== producto2.id)return false;

        // Deben tener la misma cantidad de insumos
        if(productCarrito.insumos.length !== producto2.insumos.length)return false;

        for (const insumo1 of productCarrito.insumos) {
            //const insumo2 = producto2.insumos.find((x:any) => x.id_subproducto == insumo1.id_subproducto);
            const insumo2:any = mapaProduct2.get(insumo1.id_subproducto);
            if (!insumo2)return false;
            // Comparar selección
            if (Number(insumo1.seleccionado) !== Number(insumo2.seleccionado))
                return false;
            // Comparar cantidad
            if (Number(insumo1.cantidadsubproducto) !== Number(insumo2.cantidadsubproducto))
                return false;
        }
        return true;
    }


    function sumarcantidad(productoCarrito:CarritoItem, cantidad:number, index:number){
        if(cantidad < 0)cantidad = 0;
        productoCarrito.cantidad = cantidad;
        productoCarrito.stock = cantidad;
        productoCarrito.subtotal = (productoCarrito.valorunidad)*productoCarrito.cantidad;
        productoCarrito.total = productoCarrito.subtotal;
        productoCarrito.valorcomision = (productoCarrito.subtotal*productoCarrito.percentcomision)/100;
        //calculo del impuesto y base por producto en el carrito deventas
        productoCarrito.valorimp = parseFloat((productoCarrito.total*constImp[productoCarrito.impuesto??0]).toFixed(3));
        productoCarrito.base = parseFloat((productoCarrito.total-productoCarrito.valorimp).toFixed(3));
        (tablaSeparado?.querySelector(`TR[data-indexcarrito="${index}"] .inputcantidad`) as HTMLInputElement).value = carrito[index].stock+'';
        (tablaSeparado?.querySelector(`TR[data-indexcarrito="${index}"]`)?.children?.[3] as HTMLElement).textContent = "$"+productoCarrito.total.toLocaleString();
        valorCarritoTotal();
    }


    function printItemTable(){
        while(tablaSeparado.firstChild)tablaSeparado.removeChild(tablaSeparado.firstChild);
        let options:string = '';
        ///const productounidades = allConversionUnidades.filter(x => x.idproducto === id); 
        //productounidades.forEach(u=>options+=`<option data-factor="${u.factorconversion}" value="${u.idproducto}" >${u.nombreunidaddestino}</option>`);
        carrito.forEach((item, i)=>{
          const tr = document.createElement('TR') as HTMLTableRowElement;
          tr.classList.add('productselect');
          tr.dataset.indexcarrito = i+'';
          tr.insertAdjacentHTML('afterbegin',    
            `<td class="">
                <p class="nombreproducto text-xl font-semibold text-slate-800 leading-7 break-words">${item.nombreproducto}</p>
            </td>
            <td class="">
              <div class="">
                <button type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="menos material-symbols-outlined text-base">remove</span></button>
                <input
                  type="text"
                  class="inputcantidad w-16 max-w-[12ch] h-9 px-2 rounded-lg border border-slate-300 text-center font-medium text-xl outline-none focus:border-indigo-500"
                  value="${item.stock}"
                >
                <button type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="mas material-symbols-outlined text-base">add</span></button>
              </div>
            </td>
            <td class="text-xl font-semibold text-slate-900">$${Number(item.valorunidad).toLocaleString()}</td>
            <td class="text-xl font-bold text-slate-900">$${Number(item.total).toLocaleString()}</td>
            <td class="">
                <div class="">
                    <button class="eliminarProducto w-9 h-9 rounded-lg border border-red-200 bg-red-50 text-red-500 transition-all duration-300 hover:bg-red-600 hover:text-white  hover:border-red-600 hover:shadow-md">
                        <i class="fa-solid fa-trash-can text-base"></i>
                    </button>
                </div>
            </td>`);

          tablaSeparado?.appendChild(tr);
        });
    }

    function valorCarritoTotal(){
      carritoVacio.classList.toggle('hidden', carrito.length > 0); //quitar imagen de carrito en el carrito de compras
      //calcular el impuesto discriminado por tarifa
      const idimpuesto: Record<string, number> = {'0': 1, '5': 2, '16': 3, '19': 4, 'excluido': 5, '8': 6 };
      const objbase:{'0':number, '5':number, '16':number, '19':number, 'excluido':number, '8':number} = {'0': 0, '5': 0, '16': 0, '19': 0, 'excluido':0, '8': 0};

      const mapImpuesto = new Map();
      carrito.forEach(x=>{
        if(x.impuesto){
          if(mapImpuesto.has(x.impuesto)){
            const valor = mapImpuesto.get(x.impuesto) + x.total*constImp[x.impuesto];
            mapImpuesto.set(x.impuesto, valor);
          }else{
            mapImpuesto.set(x.impuesto, x.total*constImp[x.impuesto]);
          }
        }
        if(x.impuesto == null)x.impuesto = "excluido";
          objbase[x.impuesto as keyof typeof objbase] += x.base;
          const impValor = mapImpuesto.get(x.impuesto)??0;
          const index = factimpuestos.findIndex(Obj=>Obj.id_impuesto == idimpuesto[x.impuesto]);
          if(index!=-1){ //si existe remplazar obj
            factimpuestos[index] = {id_impuesto:idimpuesto[x.impuesto], facturaid:0, basegravable:objbase[x.impuesto as keyof typeof objbase], valorimpuesto: impValor};
          }else{
            factimpuestos = [...factimpuestos, {id_impuesto:idimpuesto[x.impuesto], facturaid:0, basegravable:objbase[x.impuesto as keyof typeof objbase], valorimpuesto: impValor}];
          }
      });

      //Valor del impuesto total de todos los productos, es decir de la factura;
      let valorTotalImp:number = 0;
      for(let valorImp of mapImpuesto.values())valorTotalImp += valorImp;
      
      valorTotal.valorimpuestototal = parseFloat(valorTotalImp.toFixed(3));  //valor del impuesto total factura de todos los productos
      valorTotal.subtotal = carrito.reduce((total, x)=>x.total+total, 0);
      valorTotal.base = valorTotal.subtotal - valorTotal.valorimpuestototal;  //valor de la base total factura de todos los productos
      valorTotal.total = valorTotal.subtotal + valorTotal.valortarifa - valorTotal.descuento;
      //console.log(valorTotal);
      //console.log(factimpuestos);
      document.querySelector('#subTotal')!.textContent = '$'+valorTotal.subtotal.toLocaleString();
      (document.querySelector('#impuesto') as HTMLElement).textContent = '$'+valorTotalImp.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      document.querySelector('#total')!.textContent = '$ '+valorTotal.total.toLocaleString();
      (document.querySelector('#montocuota') as HTMLInputElement).value = valorTotal.total+'';
      //valorTotal.total = Number((document.querySelector('#abonoinicial') as HTMLInputElement).value);
      //POS.gestionSubirModalPagar.valoresCredito
      POS.valorTotal = valorTotal;
      POS.mapMediospago = mapMediospago;
      //**** recalcular valores del credito
      POS.gestionSubirModalPagar.calculoTasaInteres();
    }


    //////////////////  TABLA //////////////////////
    tablaSeparado?.addEventListener('click', (e:Event)=>{
      const elementProduct = (e.target as HTMLElement)?.closest('.productselect');
      const idProduct = (elementProduct as HTMLElement).dataset.id!;
      const precio = (elementProduct as HTMLElement).dataset.precio!;
      const indexcarrito = Number((elementProduct as HTMLElement).dataset.indexcarrito!);
      let productoCarrito = carrito[indexcarrito];
      
      if((e.target as HTMLElement).classList.contains('menos'))
        sumarcantidad(productoCarrito, productoCarrito.cantidad-1, indexcarrito);
      
      if((e.target as HTMLElement).classList.contains('inputcantidad')){
        if((e.target as HTMLElement).dataset.event != "eventInput"){
          e.target?.addEventListener('input', (e)=>{
            let val = (e.target as HTMLInputElement).value;
            val = val.replace(/[^0-9.]/g, '');
            const partes = val.split('.');
            if(partes.length > 2)val = partes[0]+'.'+partes.slice(1).join('');
            if (val.startsWith('.'))val = '1';
            if (val === '' || isNaN(parseFloat(val))) val = '0';

            (e.target as HTMLInputElement).value = val;
            sumarcantidad(productoCarrito, Number(val), indexcarrito);
          });
          (e.target as HTMLElement).dataset.event = "eventInput"; //se marca al input que ya tiene evento añadido
        }
      }

      if((e.target as HTMLElement).classList.contains('mas'))
        sumarcantidad(productoCarrito, productoCarrito.cantidad+1, indexcarrito);
      
      if((e.target as HTMLElement).classList.contains('eliminarProducto') || (e.target as HTMLElement).tagName == "I"){
        carrito.splice(indexcarrito, 1);
        printItemTable();
        valorCarritoTotal();
      }
    });


    //btn para abrir modal de pago
    btnCrearSeparado?.addEventListener('click', (e)=>{
      e.preventDefault();
      if((document.querySelector('#cliente') as HTMLSelectElement).value === ''){
        msjAlert('error', 'Debe seleccionar el cliente.', (document.querySelector('#divmsjalerta') as HTMLElement));
        return;
      }
      if((document.querySelector('#cantidadcuotas') as HTMLSelectElement).value === ''){
        msjAlert('error', 'Debe indicar la cantidad de cuotas.', (document.querySelector('#divmsjalerta') as HTMLElement));
        return;
      }
      if(carrito.length){
        document.querySelector('.Efectivo')?.removeAttribute('readonly');
        //document.querySelector('#inputscreditos')?.remove();  //elimina los inputs de credito en el modal de procesar pago.
        document.querySelector('#campoabonoinicial')?.remove();
        document.querySelector('#campocantidadcuotas')?.remove();
        document.querySelector('#campomontocuota')?.remove();
        document.querySelector('#abonoTotal')?.classList.remove('hidden');
        document.querySelector('#textPrint')!.textContent = '¿Desea imprimir comprobante?';
        tipoventa = "Credito";
        //mapMediospago.clear();
        POS.tipoventa = tipoventa;
        //POS.valorTotal = valorTotal;
        //POS.mapMediospago = mapMediospago;
        POS.gestionSubirModalPagar.subirModalPagar();
        miDialogoFacturar.showModal();
      }
    });


    document.querySelector('#formfacturar')?.addEventListener('submit', e=>{
      e.preventDefault();
      if(valorTotal.total <= 0 || valorTotal.subtotal <= 0){
        msjAlert('error', 'Medio de pago no indicado', (document.querySelector('#divmsjalertaprocesarpago') as HTMLElement));
        return;
      }
      //calcular si el totoal de los medios de pago es menor al abono inicial, abortar pago...
      let totalMediosPago:number = 0;
      for(let value of mapMediospago.values())totalMediosPago+=value;
      if(totalMediosPago<POS.gestionSubirModalPagar.valoresCredito.abonoinicial){
        //msjAlert('error', 'Valor a pagar no corresponde', (document.querySelector('#divmsjalertaprocesarpago') as HTMLElement));
        POS.gestionSubirModalPagar.mostrarMensajeMetodosPago?.();
        return;
      }

      btnPagar.disabled = true;
      btnPagar.value = 'Procesando...';
      procesarSeparado();
    });


    async function procesarSeparado(){
      const imprimir = document.querySelector('input[name="imprimir"]:checked') as HTMLInputElement;
      const valoresCredito = POS.gestionSubirModalPagar.valoresCredito;
      valoresCredito.cliente_id = (document.querySelector('#cliente') as HTMLSelectElement).value;
      valoresCredito.valorpagado = valoresCredito.abonoinicial;
      valoresCredito.cajaid = btnCaja.value;
      valoresCredito.totalunidades = carrito.reduce((total, producto)=>producto.cantidad+total, 0)+'';
      valoresCredito.base = valorTotal.base.toFixed(3);
      valoresCredito.valorimpuestototal = valorTotal.valorimpuestototal+'';
      valoresCredito.dctox100 = valorTotal.dctox100+'';
      valoresCredito.descuento = valorTotal.descuento+'';
      valoresCredito.nota = (document.querySelector('#nota') as HTMLInputElement).value;

      const datos = new FormData();
      datos.append('cliente_id', $('#cliente').val()as string);
      datos.append('abonoinicial', $('#abonoinicial').val()as string);
      datos.append('cantidadcuotas', $('#cantidadcuotas').val()as string);
      datos.append('montocuota', $('#montocuota').val()as string);
      datos.append('frecuenciapago', $('#frecuenciapago').val()as string);
      datos.append('carrito', JSON.stringify(carrito.filter(x=>x.cantidad>0)));  //envio de todos los productos con sus cantidades
      datos.append('mediospago', JSON.stringify(Array.from(mapMediospago, ([mediopago_id, valor])=>({mediopago_id, idcuota:0, valor}))));
      datos.append('valorefectivo', mapMediospago.get('1')??'0');
      datos.append('factimpuestos', JSON.stringify(factimpuestos));
      datos.append('valoresCredito', JSON.stringify(valoresCredito));

      datos.append('totalunidades', carrito.reduce((total, producto)=>producto.cantidad+total, 0)+'');
      datos.append('base', valorTotal.base.toFixed(3));
      datos.append('valorimpuestototal', valorTotal.valorimpuestototal+''); //valor total del impuesto. 
      datos.append('dctox100', valorTotal.dctox100+'');
      datos.append('descuento', valorTotal.descuento+'');
      //datos.append('nota', (document.querySelector('#nota') as HTMLInputElement).value);
      try {
          const url = "/admin/api/crearSeparado";  //va al controlador creditoscontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();
          if(resultado.exito !== undefined){
            msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            /////// reinciar modulo de ventas
            //vaciarventa();
            btnPagar.disabled = false;
            btnPagar.value = 'Pagar';
            miDialogoFacturar.close();
            
            if(resultado.idcredito && imprimir.value === '1')
              await printTicketSeparado(resultado.idcredito);
            
            setTimeout(() => { window.location.href = "/admin/creditos"; }, 900);
          }else{
            msjalertToast('error', '¡Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
    }

    function printTicketSeparado(idcredito:string): Promise<void>{
      return new Promise<void>((resolve, reject) => {
        setTimeout(() => {
          window.open("/admin/printPDFPOSSeparado?id=" + idcredito, "_blank");
          resolve();
        }, 700);
      })
    }

    function cerrarDialogoExterno(event:Event) {
      const miDialogoDescuento = POS.gestionarDescuentos.miDialogoDescuento;
      const f = event.target;
      if (f === miDialogoFacturar || f === miDialogoDescuento || (f as HTMLInputElement).closest('.salir') || (f as HTMLInputElement).value === 'salir' || (f as HTMLInputElement).value === 'Cancelar') {
        miDialogoFacturar.close();
        miDialogoDescuento.close();
      }
    }


    POS.cerrarDialogoExterno = cerrarDialogoExterno;
  }

})();
