(()=>{
  if(document.querySelector('.ventas')){

    const POS = (window as any).POS;
     
    const selectCliente = POS.gestionClientes.selectCliente;
    const dirEntrega = POS.gestionClientes.dirEntrega;
    const productos = document.querySelectorAll<HTMLElement>('#producto')!;
    const selectVendedor = (document.querySelector('#vendedor') as HTMLSelectElement);
    const contentproducts = document.querySelector('#productos');
    const totalunidades = document.querySelector('#totalunidades') as HTMLElement;
    const tablaventa = document.querySelector('#tablaventa tbody');
    const carritoVacio = document.querySelector('#carritoVacio') as HTMLElement;
    const btnguardar = document.querySelector('#btnguardar');
    const btnfacturar = document.querySelector('#btnfacturar');
    const btnaplicarcredito = document.querySelector('#btnaplicarcredito');
    const miDialogoAddCliente = POS.gestionClientes.miDialogoAddCliente;
    //const miDialogoAddDir = POS.gestionClientes.miDialogoAddDir;
    const miDialogoOtrosProductos = POS.gestionOtrosProductos.miDialogoOtrosProductos;
    const miDialogoPreciosAdicionales = POS.gestionarPreciosAdicionales.miDialogoPreciosAdicionales;
    const miDialogoFacturarA = POS.gestionarAdquiriente.miDialogoFacturarA;
    const miDialogoDescuento = POS.gestionarDescuentos.miDialogoDescuento;
    const miDialogoRedimir = POS.gestionRedmir.miDialogoRedimir;
    const miDialogoCredito = document.querySelector('#miDialogoCredito') as any;
    const miDialogoGuardar = document.querySelector('#miDialogoGuardar') as any;
    const miDialogoFacturar = document.querySelector('#miDialogoFacturar') as any;
    const miDialogoCalculadora = document.querySelector('#miDialogoCalculadora') as HTMLDialogElement;
    const btnCaja = document.querySelector('#caja') as HTMLSelectElement; //select de la caja en el modal pagar
    const btnTipoFacturador = document.querySelector('#facturador') as HTMLSelectElement; //select del consecutivo o facturador en el modal de pago
    const btnPagar = document.getElementById('btnPagar') as HTMLInputElement;
    
    let carrito:{id:string, idproducto:string, tipoproducto:string, tipoproduccion:string, idcategoria: string, foto:string, nombreproducto: string, rendimientoestandar:string, costo:string, valorunidad: string, stock: number, promediostock: number, prioridadcomision: string, percentcomision: number, valorcomision: number, subtotal: number, base:number, impuesto:string, valorimp:number, descuento:number, total: number, insumos:insumo[]}[]=[];
    const valorTotal = {porcentgananciauser: 0, valorgananciauser: 0, subtotal: 0, base: 0, valorimpuestototal: 0, dctox100: 0, descuento: 0, idtarifa: 0, valortarifa: 0, total: 0}; //datos global de la venta
    let tarifas:{id:string, idcliente:string, nombre:string, valor:string}[] = []; 
    let indexcarrito:number, nombretarifa:string|undefined='', tipoventa:string="Contado";
    let printerBT:string = getParam.impresora_principal_de_CAJA_para_Android_por_BT.valor_final;
    let viewTasaCambio:string = getParam.mostrar_tasa_de_cambio_de_divisa.valor_final;

    const constImp: {[key:string]: number} = {};
    constImp['excluido'] = 0;
    constImp['0'] = 0;  //exento de iva, tarifa 0%
    constImp['5'] = 0.0476190476190476; //iva, tarifa al 5%,  Bienes/servicios al 5
    constImp['8'] = 0.0740740740740741; //inc, tarifa al 8%,  impuesto nacional al consumo
    constImp['16'] = 0.1379310344827586; //iva, tarifa al 16%,  contratos firmados con el estado antes de ley 1819
    constImp['19'] = 0.1596638655462185; //iva, tarifa al 19%,  tarifa general

    interface Item {
      id_impuesto: number,
      facturaid: number,
      basegravable: number,
      valorimpuesto: number
    }
    
    let factimpuestos:Item[] = [], products:productsapi[]=[];
    const mapMediospago = new Map();

    const mediosPagoDBMAP = new Map<string, string>(  //se usa para imprimir los medios de pago en el servidor de impresion
      mediosPagoDB.map(m => [m.id, m.mediopago]) //mediosPagoDB se declara en app.ts el cual viene del <script> en index.php que convierte el array de medios de pago de php a js.
    );

    (async ()=>{
      products = await POS.productosAPI.getProductosAPI();
      POS.products = products;  //Se expone globalmente
      POS.gestionOtrosProductos.otrosproductos();
    })();

    /*productos.forEach(producto=>{
      producto.addEventListener('click', (e)=>{
        console.log(producto.dataset.id);
      });
    });*/
    


                       /******** *********/

    selectFacturadorSegunCaja(btnCaja);
    btnCaja.addEventListener('change', (e:Event)=>selectFacturadorSegunCaja(e.target as HTMLSelectElement));
    function selectFacturadorSegunCaja(z:HTMLSelectElement){
      $('#facturador').val(z.options[z.selectedIndex].dataset.idfacturador??'1');
    }

    POS.gestionClientes.clientes();  //inicializa modulo de clientes

    //////////// evento a toda el area de los productos a seleccionar //////////////
    contentproducts?.addEventListener('click', (e:Event)=>{
      const elementProduct = (e.target as HTMLElement)?.closest('.producto');
      if(!elementProduct)return;

      if((e.target as HTMLElement).closest('#precioadicional')){
        POS.gestionarPreciosAdicionales.abrirDialogo(elementProduct);  //ejecuta los precios adicionales
        return;   
      }

      const productoItem = products.find(x=>x.id == (elementProduct as HTMLElement).dataset.id);
      const productoConfigurado = structuredClone(productoItem!);
      filtrarInsumos(productoConfigurado);
      actualizarCarrito((elementProduct as HTMLElement).dataset.id!, 1, true, true, productoItem?.precio_venta, productoConfigurado);
      const productoAgregado = carrito.find(x=>x.idproducto == (elementProduct as HTMLElement).dataset.id && x.valorunidad == productoItem?.precio_venta && mismaConfiguracion(x, productoConfigurado));
      //animacion de aviso cuando se selecciona un producto en dispositivo pequeño
      elementProduct.classList.remove('producto-agregado-feedback');
      elementProduct.classList.add('producto-agregado-feedback');
      setTimeout(()=>elementProduct.classList.remove('producto-agregado-feedback'), 900);
      POS.gestionAnimaciones.mostrarFeedbackCarrito(productoAgregado?.nombreproducto??'', productoAgregado?.stock ?? 1);
    });
    

    function printProduct(id:string, precio:string, index:number){ //recibe el id del producto
      //if(indice === -1)return;
      const uncarrito = carrito[index];

      const tr = document.createElement('TR');
      tr.classList.add( 'productselect', 'hover:bg-slate-50', 'transition-colors' );
      tr.dataset.id = `${id}`;
      tr.dataset.precio = precio;
      tr.dataset.indexcarrito = index+'';
      tr.insertAdjacentHTML('afterbegin',    
        `<td class="">
            <p class="nombreproducto text-xl font-semibold text-slate-800 leading-7 break-words">${uncarrito?.nombreproducto}</p>
        </td>
        <td class="">
          <div class="">
            <button type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="menos material-symbols-outlined text-base">remove</span></button>
            <input
              type="text"
              class="inputcantidad w-16 max-w-[12ch] h-9 px-2 rounded-lg border border-slate-300 text-center font-medium text-xl outline-none focus:border-indigo-500"
              value="${uncarrito.stock}"
            >
            <button type="button" class="shrink-0 bg-indigo-700 text-white rounded-full"><span class="mas material-symbols-outlined text-base">add</span></button>
          </div>
        </td>
        <td class="text-xl font-semibold text-slate-900">$${Number(uncarrito?.valorunidad).toLocaleString()}</td>
        <td class="text-xl font-bold text-slate-900">$${Number(uncarrito?.total).toLocaleString()}</td>
        <td class="">
            <div class="">
                <button class="eliminarProducto w-9 h-9 rounded-lg border border-red-200 bg-red-50 text-red-500 transition-all duration-300 hover:bg-red-600 hover:text-white  hover:border-red-600 hover:shadow-md">
                    <i class="fa-solid fa-trash-can text-base"></i>
                </button>
            </div>
        </td>`);
      tablaventa?.appendChild(tr);
    } //oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"


    function actualizarCarrito(id:string, cantidad:number, control:boolean, stateinput:boolean, precio:string = '0', productoConfigurado:productsapi|null){
      ///limpiar el campo de buscar producto
      //POS.reiniciarCatalogoVentas?.();
      const index = carrito.findIndex(x=>x.idproducto==id && x.valorunidad == precio && mismaConfiguracion(x, productoConfigurado!)); //devuelve el index si el producto existe
      
      if(index>-1){
        editarCantidad(index, 1, control, stateinput);
      }else{  //agregar a carrito si el producto no esta agregado en carrito, se agrega por primera vez
        const producto = products.find(x=>x.id==id)!; //products es el arreglo de todos los productos traido por api
        if(cantidad < 0)cantidad = 0;
        const productovalorimp = (Number(precio)*cantidad)*constImp[producto.impuesto??'0']; //si producto.impuesto es null toma el valor de cero
        const productototal = Number(precio)*cantidad;

        //varia segun la prioridad de la comision
        if(producto.prioridadcomision === '0'){  //si el porcentaje es por usuario
          //producto.percentcomision = Number(percentComisionUser);  ////porcentaje de comision del usuario logueado
          producto.percentcomision = Number(selectVendedor.options[selectVendedor.selectedIndex].dataset.comision);
        }
        const valorcomision:number = (productototal*producto.percentcomision)/100;

        //obtener nombre de insumo cuando es radiobutton o checkbox
        let insumos = productoConfigurado?.insumos||[];
        //obtener el ultimo insumo de seleccion unica
        let seleccionUnica:insumo|undefined;
        for(let i = insumos.length-1; i>=0; i--)
          if(insumos[i].seleccionado === "1" &&insumos[i].grupos_insumos?.tipo === "0"){
            seleccionUnica = insumos[i];
            break
          }
        
        var a:{id:string, idproducto:string, tipoproducto:string, tipoproduccion:string, idcategoria: string, nombreproducto: string, rendimientoestandar:string, foto:string, costo:string, valorunidad: string, stock: number, promediostock: number, prioridadcomision:string, percentcomision: number, valorcomision: number, subtotal: number, base:number, impuesto:string, valorimp:number, descuento:number, total:number, insumos:insumo[]} = {
          id: '',
          idproducto: producto?.id!,
          tipoproducto: producto.tipoproducto,
          tipoproduccion: producto.tipoproduccion,
          idcategoria: producto.idcategoria,
          nombreproducto:`${producto.nombre} ${seleccionUnica?.nombre?'('+seleccionUnica.nombre+')':''}`,
          rendimientoestandar: producto.rendimientoestandar,
          foto: producto.foto,
          costo: producto.precio_compra,
          valorunidad: precio,
          stock: cantidad,
          promediostock: Number(producto.promediostock),
          prioridadcomision: producto.prioridadcomision,
          percentcomision: Number(producto.percentcomision),
          valorcomision: valorcomision,
          subtotal: productototal, //este es el subtotal del producto
          base: productototal-productovalorimp,
          impuesto: producto.impuesto, //porcentaje de impuesto, es null si es excluido de iva
          valorimp: productovalorimp,
          descuento: 0,
          total: productototal, //valorunidad x cantidad
          insumos: productoConfigurado?.insumos??[]
        }
        
        carrito = [...carrito, a];
        const indice = carrito.length - 1;
        valorCarritoTotal();
        printProduct(id, precio, indice);
        POS.carrito = carrito;
      }
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
        // Comparar selecciÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³n
        if (Number(insumo1.seleccionado) !== Number(insumo2.seleccionado))
            return false;
        // Comparar cantidad
        if (Number(insumo1.cantidadsubproducto) !== Number(insumo2.cantidadsubproducto))
            return false;
      }
      return true;
    }

    //evento al select del vendedor
    selectVendedor.addEventListener('change', (e:Event)=>{
      const target = e.target as HTMLSelectElement;
      const comisionUser:number = Number(target.selectedOptions[0].dataset.comision);
      carrito.forEach(x=>{ 
        if(x.prioridadcomision === '0'){
          x.percentcomision = comisionUser;
          x.valorcomision = (x.total*comisionUser)/100;
        }
      });
      valorCarritoTotal();
    });

    ////////////////////// valores finales subtotal y total ////////////////////////
    function valorCarritoTotal(){
      carritoVacio.classList.toggle('hidden', carrito.length > 0); //quitar imagen de carrito en el carrito de compras
      /*if(!carrito.length)return;*/
      valorTotal.valorgananciauser = 0;
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
        
        //calcular valor total de comision para el usuario
        valorTotal.valorgananciauser += Number(x.valorcomision);
      });
     
      //Valor del impuesto total de todos los productos, es decir de la factura;
      let valorTotalImp:number = 0;
      for(let valorImp of mapImpuesto.values())valorTotalImp += valorImp;
      
      valorTotal.valorimpuestototal = parseFloat(valorTotalImp.toFixed(3));  //valor del impuesto total factura de todos los productos
      valorTotal.subtotal = carrito.reduce((total, x)=>x.total+total, 0);
      valorTotal.porcentgananciauser =  (100*valorTotal.valorgananciauser)/valorTotal.subtotal;
      valorTotal.base = valorTotal.subtotal - valorTotal.valorimpuestototal;  //valor de la base total factura de todos los productos
      valorTotal.total = valorTotal.subtotal + valorTotal.valortarifa - valorTotal.descuento;
      document.querySelector('#subTotal')!.textContent = '$'+valorTotal.subtotal.toLocaleString();
      (document.querySelector('#impuesto') as HTMLElement).textContent = '$'+valorTotalImp.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      (document.querySelector('#valorTarifa') as HTMLElement).textContent = '$'+valorTotal.valortarifa.toLocaleString();
      document.querySelector('#total')!.textContent = '$ '+valorTotal.total.toLocaleString();
      // cantidad total de productos
      const cantidadTotalProductos = carrito.reduce((total, producto)=>producto.stock+total, 0);
      totalunidades.textContent = formatCantidadBadge(cantidadTotalProductos);
      POS.gestionAnimaciones.actualizarBadgeCarritoMovil(cantidadTotalProductos);
      //equivalencia divisa
      if(viewTasaCambio === '1'){
        const divisa = monedas.find(x=>x.id == sucursal.idmoneda);
        (document.querySelector('#equivalente') as HTMLParagraphElement).textContent = '$'+(valorTotal.total * sucursal.tasacambio).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        (document.querySelector('#monedaCodigo') as HTMLParagraphElement).textContent = divisa?.codigo??'';
      }
      actualizarStockVisible();
    }


    function actualizarStockVisible(): void {
      const tarjetas = document.querySelectorAll<HTMLElement>('#productos .producto');
      tarjetas.forEach(tarjeta => {
          const idProducto = tarjeta.dataset.id;
          const producto = products.find(producto => producto.id == idProducto);
          const elementoStock = tarjeta.querySelector<HTMLElement>('.stockProduct');

          if(!producto || !elementoStock)return;
          // Conserva el guion utilizado actualmente para este tipo de producto.
          if(producto.tipoproducto === '1' && producto.tipoproduccion === '0'){
              elementoStock.textContent = ' - ';
              return;
          }

          /*
          * Un producto puede aparecer varias veces en el carrito
          * con diferentes precios o configuraciones.
          */
          const cantidadEnCarrito = carrito.reduce(
              (total, item) => {
                  if(item.idproducto == producto.id)return total + Number(item.stock);
                  return total;
              },
              0
          );

          const stockDisponible = Number(producto.stock) - cantidadEnCarrito;
          elementoStock.textContent = stockDisponible.toFixed(2);
      });
    }


    /////////////////////// evento a la tabla de productos de venta (carrito) //////////////////////////
    tablaventa?.addEventListener('click', (e:Event)=>{
      const elementProduct = (e.target as HTMLElement)?.closest('.productselect');
      indexcarrito = Number((elementProduct as HTMLElement).dataset.indexcarrito!);
      let productoCarrito = carrito[indexcarrito];

      if((e.target as HTMLElement).classList.contains('nombreproducto')){
        const inputMerma = document.querySelector('#inputMerma') as HTMLInputElement;
        inputMerma.value = '';
        if(getParam.activar_calculadira_de_merma_en_modulo_de_ventas.valor_final == '1'){
          miDialogoCalculadora.showModal();
          inputMerma.focus();
          document.addEventListener("click", cerrarDialogoExterno);
        }
      }
      
      if((e.target as HTMLElement).classList.contains('menos'))
        editarCantidad(indexcarrito, productoCarrito!.stock-1, false, true);
      
      if((e.target as HTMLElement).classList.contains('mas'))
        editarCantidad(indexcarrito, productoCarrito!.stock+1, false, true);
      
      if((e.target as HTMLElement).classList.contains('eliminarProducto') || (e.target as HTMLElement).tagName == "I"){
        carrito.splice(indexcarrito, 1);
        while(tablaventa.firstChild)tablaventa.removeChild(tablaventa.firstChild);
        carrito.forEach((item, i) =>printProduct(item.idproducto, item.valorunidad, i));
        valorCarritoTotal();
      }
    });


    tablaventa?.addEventListener('input', e=>{
      const input = e.target as HTMLInputElement;
      if (!input.classList.contains('inputcantidad')) return;
      const fila = input?.closest('.productselect') as HTMLTableRowElement;
      indexcarrito = Number(fila.dataset.indexcarrito!);
    
      let val = input.value;
      val = val.replace(/[^0-9.]/g, '');
      const partes = val.split('.');
      if(partes.length > 2)val = partes[0]+'.'+partes.slice(1).join('');
      if (val.startsWith('.'))val = '1';
      if (val === '' || isNaN(parseFloat(val))) val = '';

      input.value = val;
      editarCantidad(indexcarrito, Number(input.value), false, false);
    });


    function editarCantidad(index:number, cantidad:number, control:boolean, stateinput:boolean){
        if(cantidad < 0 && (carrito[index].stock + cantidad)<0){
          cantidad = 0;
          carrito[index].stock = 0;
          carrito[index].total = 0;
        }
        if(control){ //cuando el producto se agrega desde la lista de productos
          carrito[index].stock += cantidad;
        }else{ //cuando el producto se agrega por el input cantidad
          carrito[index].stock = cantidad;
        }
        
        carrito[index].subtotal = (parseFloat(carrito[index].valorunidad)*carrito[index].stock);
        carrito[index].total = carrito[index].subtotal;
        carrito[index].valorcomision = (carrito[index].subtotal*carrito[index].percentcomision)/100;
        //calculo del impuesto y base por producto en el carrito de ventas
        carrito[index].valorimp = parseFloat((carrito[index].total*constImp[carrito[index].impuesto??0]).toFixed(3));
        carrito[index].base = parseFloat((carrito[index].total-carrito[index].valorimp).toFixed(3));

        valorCarritoTotal();
        const inputCantidad = tablaventa?.querySelector(`TR[data-indexcarrito="${index}"] .inputcantidad`) as HTMLInputElement;
        if(stateinput)inputCantidad.value = carrito[index].stock+'';
        ajustarAnchoCantidad(inputCantidad, carrito[index].stock);
        (tablaventa?.querySelector(`TR[data-indexcarrito="${index}"]`)?.children?.[3] as HTMLElement).textContent = "$"+carrito[index].total.toLocaleString();
    }


    //calculadora que se ejecuta cuando se da clic sobre el nombre del producto
    document.querySelector('#btnMermaCantidad')?.addEventListener('click', (e:Event)=>{
      const inputMerma = (document.querySelector('#inputMerma') as HTMLInputElement).value;
      const {stock} = carrito[indexcarrito];
      let nuevaCantidad = stock - Number(inputMerma);
      if(nuevaCantidad<0)nuevaCantidad=0;
      (tablaventa?.querySelector(`TR[data-indexcarrito="${indexcarrito}"] .inputcantidad`) as HTMLInputElement).value = nuevaCantidad+'';
      editarCantidad(indexcarrito, nuevaCantidad, false, false);
      miDialogoCalculadora.close();
      document.removeEventListener("click", cerrarDialogoExterno);
    });

    /*btnvaciar?.addEventListener('click', ()=>{
      if(carrito.length){
        miDialogoVaciar.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }
    });*/

    btnguardar?.addEventListener('click', ()=>{
      if(carrito.length && valorTotal.total>0){
        miDialogoGuardar.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }
    });

    btnaplicarcredito?.addEventListener('click', ()=>{
      if(POS.gestionarDomiciliosVenta.tipoEntrega && (selectCliente.value =='' || !dirEntrega.value) || selectCliente.value ==''){
        msjalertToast('warning', 'Cliente requerido', 'Selecciona un cliente y una direccion antes de registrar la venta a credito.');
        POS.gestionClientes.resaltarSelectorCliente();
        return;
      }
      if(carrito.length && valorTotal.total>0){
        document.querySelector('.Efectivo')?.removeAttribute('readonly');
        document.querySelector('#inputscreditos')?.classList.add('flex');
        document.querySelector('#inputscreditos')?.classList.remove('hidden');
        if(tipoventa == "Contado"){
          mapMediospago.clear();
          $('.mediopago').val(0);
        }
        tipoventa = "Credito";
        POS.tipoventa = tipoventa;
        POS.gestionSubirModalPagar.subirModalPagar();
        //miDialogoCredito.showModal();
        miDialogoFacturar.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }
    });

    btnfacturar?.addEventListener('click', ()=>{
      if(POS.gestionarDomiciliosVenta.tipoEntrega && (selectCliente.value =='' || !dirEntrega.value)){
        msjAlert('error', 'Cliente o direccion no seleccionado', (document.querySelector('#divmsjalerta1') as HTMLElement));
        POS.gestionClientes.resaltarSelectorCliente();
        return;
      }
      
      if(POS.gestionarDomiciliosVenta.tipoEntrega)document.querySelector('#confirmarDespacho')?.classList.remove('hidden');

      if(carrito.length && valorTotal.total>0){
        document.querySelector('.Efectivo')?.setAttribute('readonly', 'true');
        document.querySelector('#inputscreditos')?.classList.add('hidden');
        document.querySelector('#inputscreditos')?.classList.remove('flex');
        tipoventa = "Contado";
        POS.tipoventa = tipoventa;
        POS.gestionSubirModalPagar.subirModalPagar();
        miDialogoFacturar.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }
    });


    function cerrarDialogoExterno(event:Event) {
      const f = event.target;
      if (f === miDialogoDescuento || f === miDialogoCredito || f === miDialogoGuardar || f === miDialogoFacturar || f === miDialogoRedimir || f === miDialogoAddCliente || f === miDialogoOtrosProductos || f === miDialogoFacturarA || /*f === miDialogoAddDir ||*/ f=== miDialogoPreciosAdicionales || f === miDialogoCalculadora || 
        (f as HTMLInputElement).closest('.salir') || (f as HTMLInputElement).closest('.novaciar') || (f as HTMLInputElement).closest('.cotizacion') || (f as HTMLInputElement).closest('.remision') || (f as HTMLInputElement).closest('.siguardar') || (f as HTMLButtonElement).value == "Cancelar" || (f as HTMLButtonElement).value == "Redimir" ||/*(f as HTMLButtonElement).value == "Seleccionar" ||*/ (f as HTMLButtonElement).classList.contains('btnCerrarPreciosAdicionales') ) {
        miDialogoDescuento.close();
        //miDialogoCredito.close();
        miDialogoGuardar.close();
        miDialogoFacturar.close();
        miDialogoRedimir.close();
        miDialogoAddCliente.close();
        //miDialogoAddDir.close();
        miDialogoFacturarA.close();
        miDialogoOtrosProductos.close();
        miDialogoPreciosAdicionales.close();
        miDialogoCalculadora.close();
        document.removeEventListener("click", cerrarDialogoExterno);
        if((f as HTMLInputElement).closest('.cotizacion')){
          tipoventa = "";
          procesarpedido('Guardado', '1');
        }
        if((f as HTMLInputElement).closest('.remision')){
          tipoventa = "";
          procesarpedido('Remision', '0');
        }
        if((f as HTMLInputElement).closest('.redimir')){
          tipoventa = "";
          if(POS.gestionRedmir.valorPts < 1){
            msjalertToast('error', 'Error!', 'Debe indicar una cantidad minima de puntos.');
            return;
          }
          if(POS.gestionRedmir.valorPts > POS.gestionRedmir.equivalencia){
            msjalertToast('error', 'Error!', 'Los puntos superan el limite permitido.');
            return;
          }
          procesarpedido('Redimido', '0');
        }
        //if((f as HTMLInputElement).closest('.sivaciar'))vaciarventa();
      }
    }

    function vaciarventa():void{
      if(datosfactura?.id)datosfactura.id = '';
      (document.querySelector('#formFacturarA') as HTMLFormElement)?.reset();
      (document.querySelector('#formfacturar') as HTMLFormElement)?.reset();
      (document.querySelector('#formAddCliente') as HTMLFormElement).reset();
      (document.querySelector('#badgeEstado') as HTMLParagraphElement).textContent = 'SIN CLIENTE';
      (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.remove('bg-green-100', 'text-green-600');
      (document.querySelector('#badgeEstado') as HTMLParagraphElement).classList.add('bg-gray-100', 'text-gray-700');
      (document.querySelector('#resumenCliente') as HTMLParagraphElement).textContent = "Seleccionar cliente";
      mapMediospago.clear();
      $('.mediopago').val(0);
      carrito.length = 0;
      factimpuestos.length = 0;

      history.replaceState({}, "", "/admin/ventas");
      while(tablaventa?.firstChild)tablaventa.removeChild(tablaventa?.firstChild);
      carritoVacio.classList.toggle('hidden', carrito.length > 0);
      (document.querySelector('#npedido') as HTMLInputElement).value = '';
      document.querySelector('#subTotal')!.textContent = '$'+0;
      document.querySelector('#impuesto')!.textContent = '$'+0;
      (document.querySelector('#descuento') as HTMLElement).textContent = '$'+0;
      (document.querySelector('#valorTarifa') as HTMLElement).textContent = '$'+0;
      document.querySelector('#total')!.textContent = '$'+0;
      (document.querySelector('#inputCantidadPuntos') as HTMLInputElement).value = '0';
      for(const key in valorTotal)valorTotal[key as keyof typeof valorTotal] = 0; //reiniciar objeto
      $('#selectCliente').val('').trigger('change');   //aqui tambien se reinicia el valor de la tarifa y al disparar este evento, se ejecuta POS.valorCarritoTotal(); linea 135 de ahelper.clientes.ts 
      POS.gestionarDomiciliosVenta.reiniciarDomicilio();
      POS.gestionRedmir.pts = 0;
      //volver a mapear los productos con los valores originales de inventario
      //actualizar DOM
      for(const prod of products){
          const item = POS.hackerList.get('id', prod.id)[0];
          prod.precio_venta = prod.precio_original!;
          if(item)item.elm.querySelector('.precioVenta').textContent = '$'+Number(prod.precio_venta).toLocaleString();
      }
      POS.reiniciarCatalogoVentas?.();
    }


    ////////////////// evento al bton pagar del modal facturar //////////////////////
    document.querySelector('#formfacturar')?.addEventListener('submit', e=>{
      e.preventDefault();
      if(valorTotal.total <0 || valorTotal.subtotal <0){
        msjAlert('error', 'No se puede procesar pago con $0', (document.querySelector('#divmsjalertaprocesarpago') as HTMLElement));
        return;
      }

      //calcular si el totoal de los medios de pago es menor al abono inicial, abortar pago...
      let totalMediosPago:number = 0;
      for(let value of mapMediospago.values())totalMediosPago+=value;
      if(totalMediosPago<POS.gestionSubirModalPagar.valoresCredito.abonoinicial){
        POS.gestionSubirModalPagar.mostrarMensajeMetodosPago?.();
        return;
      }

      if(tipoventa == "Credito" && (isNaN(POS.gestionSubirModalPagar.valoresCredito.cantidadcuotas)||POS.gestionSubirModalPagar.valoresCredito.cantidadcuotas<=0)){
        msjAlert('error', 'Plazo de cuotas no especificado', (document.querySelector('#divmsjalertaprocesarpago') as HTMLElement));
        return;
      }
  
      btnPagar.disabled = true;
      btnPagar.value = 'Procesando...';
      procesarpedido('Paga', '0');
    });

    async function procesarpedido(estado:string, ctz:string){
      const imprimir = document.querySelector('input[name="imprimir"]:checked') as HTMLInputElement;
      const despachar = document.querySelector('#despachar') as HTMLInputElement;
      const valoresCredito = POS.gestionSubirModalPagar.valoresCredito;
      const tipoEntrega:number = POS.gestionarDomiciliosVenta.tipoEntrega;
      const datos = new FormData();
      datos.append('id', datosfactura?.id??'');
      datos.append('idemisor', btnCaja.selectedOptions[0].dataset.idemisor??'');
      datos.append('idcliente', (document.querySelector('#selectCliente') as HTMLSelectElement).value || '1');
      datos.append('idvendedor', selectVendedor.value);
      datos.append('idcaja', btnCaja.value);
      datos.append('idconsecutivo', btnTipoFacturador.value);
      datos.append('iddireccion', dirEntrega.value);
      datos.append('idtarifazona', valorTotal.idtarifa+'');
      datos.append('idcanaldeventa', (document.querySelector('#canalVenta') as HTMLSelectElement)?.value??'1');
      datos.append('cliente', selectCliente.value==''?'N/A':selectCliente.options[selectCliente.selectedIndex].textContent!);
      datos.append('vendedor', $('#vendedor option:selected').text());
      datos.append('caja', (document.querySelector('#caja option:checked') as HTMLSelectElement).textContent!);
      datos.append('tipofacturador', btnTipoFacturador.options[btnTipoFacturador.selectedIndex].textContent!);
      datos.append('direccion', dirEntrega.options[dirEntrega.selectedIndex]?.text??'');
      datos.append('tarifazona', nombretarifa||'');
      datos.append('carrito', JSON.stringify(carrito.filter(x=>x.stock>0)));  //envio de todos los productos con sus cantidades
      datos.append('totalunidades', totalunidades.textContent!);
      //datos.append('mediosPago', JSON.stringify(Object.fromEntries(mapMediospago)));
      datos.append('mediosPago', JSON.stringify(Array.from(mapMediospago, ([idmediopago, valor])=>({idmediopago, id_factura:0, valor}))));
      datos.append('factimpuestos', JSON.stringify(factimpuestos));
      datos.append('recibido', document.querySelector<HTMLInputElement>('#recibio')!.value);
      datos.append('transaccion', '');
      datos.append('tipoventa', tipoventa);
      datos.append('valoresCredito', JSON.stringify(valoresCredito));
      datos.append('cotizacion', ctz);  //1= cotizacion, 0 = no cotizacion pagada.
      datos.append('remision', estado=='Remision'?'1':'0');  //1= cotizacion, 0 = no cotizacion pagada.
      datos.append('estado', estado);
      datos.append('porcentgananciauser', valorTotal.porcentgananciauser.toFixed(2));
      datos.append('valorgananciauser', valorTotal.valorgananciauser.toFixed(2));
      datos.append('subtotal', valorTotal.subtotal+'');
      datos.append('base', valorTotal.base.toFixed(3));
      datos.append('valorimpuestototal', valorTotal.valorimpuestototal+''); //valor total del impuesto. 
      datos.append('dctox100',valorTotal.dctox100+'');
      datos.append('descuento',valorTotal.descuento+'');
      datos.append('total', valorTotal.total.toString());
      datos.append('observacion', document.querySelector<HTMLTextAreaElement>('#observacion')!.value);
      datos.append('departamento', '');
      datos.append('ciudad', (document.querySelector('#ciudad') as HTMLInputElement).value);
      datos.append('entrega', tipoEntrega==0?'Presencial':'Domicilio');
      datos.append('entregado', estado=='Paga'&&tipoEntrega==1?(despachar.checked?'1':'0'):(estado=='Paga'&&tipoEntrega==0 || estado=='Redimido'&&tipoEntrega==0)?'1':'0'); //si es remision no se ha entregado, si es pago y entrega a domicilio se define segun el checkbox de despachar, si es pago y entrega presencial se marca como entregado
      datos.append('valortarifa', valorTotal.valortarifa+'');
      datos.append('puntos_descontados', (estado==='Remision' || estado === 'Guardado' ) ? 0 : POS.gestionRedmir.pts);
      datos.append('datosAdquiriente', JSON.stringify(POS.gestionarAdquiriente.datosAdquiriente));
      datos.append('opc1', '');
      datos.append('opc2', '');
  
      try {
          const url = "/admin/api/facturar";  //va al controlador ventascontrolador
          const respuesta = await fetch(url, {method: 'POST', body: datos}); 
          const resultado = await respuesta.json();

          if(resultado.exito !== undefined){
            if(estado == "Paga"){
              resultado.dataInvoice.items = carrito.filter(x=>x.stock>0);
              resultado.dataInvoice.mediospago = Array.from(mapMediospago, ([idmediopago, valor])=>({
                idmediopago,
                mediopago: mediosPagoDBMAP.get(idmediopago),
                valor,
              }));
            }

            limpiarFormFacturar();
            msjalertToast('success', 'Exito!', resultado.exito[0]);
            //ENVIAR FACTURA A DIAN SI ES FACTURACION ELECTRONICA
            if(btnTipoFacturador.options[btnTipoFacturador.selectedIndex].dataset.idtipofacturador == '1'){
              const resDian = await POS.sendInvoiceAPI.sendInvoice(resultado.idfactura);
              POS.gestionarAdquiriente.datosAdquiriente = {}; //reiniciar datos de adquiriente cada vez que se facture electronicamente
              resultado.dataInvoice.cufe = resDian.cufe;
              resultado.dataInvoice.link = resDian.link;
              console.log(resDian);
            }
            //IMPRIMIR TICKET POS
            if(resultado.idfactura && imprimir.value === '1')printTicketPOS(resultado.idfactura, resultado.dataInvoice);
            vaciarventa();
            products = await POS.productosAPI.getProductosAPI();
            POS.products = products;
            actualizarStockVisible();
          }else{
            limpiarFormFacturar();
            msjalertToast('error', 'Error!', resultado.error[0]);
          }
      } catch (error) {
          console.log(error);
      }
    }


    function limpiarFormFacturar(){
      btnPagar.disabled = false;
      btnPagar.value = 'Pagar';
      miDialogoFacturar.close();
      document.removeEventListener("click", cerrarDialogoExterno);
      (document.getElementById('contenedorDesktop') as HTMLDivElement).classList.add('translate-x-full');
      (document.getElementById('overlayCarrito') as HTMLDivElement).classList.add('hidden');
    }


    async function printTicketPOS(idfactura:string, datainvoice:DataInvoice){
      console.log(datainvoice);
      ////// cuando no es impresora CAJA por BT
      const isAndroid = /Android/i.test(navigator.userAgent);

      if(printerBT === '1'){  //solo aplica para impresora principal si es android
        const builder = new InvoiceTicketBuilder2(datainvoice);
        const ticket = await builder.generate(true); //true para version buffer bytes
        
        const base64 = bytesToBase64(ticket);
        //const url = `intent://base64,${base64}#Intent;scheme=rawbt;package=ru.a4024.rawbtprinter;end;`;
        //const url = `intent://base64,${base64}#Intent;scheme=rawbt;package=ru.a4024.rawbtprinter;end;`;
        //window.location.href = url;
        if (isAndroid)window.location.href = `rawbt:base64,${base64}`;

        //version string
          //const encoder = new TextEncoder();
          //const bytes = encoder.encode(ticket);
        //descargar .bin a equipo
          /*const blob = new Blob([ticket], { type: 'application/octet-stream' });
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'ticket.bin';
          a.click();
          URL.revokeObjectURL(url);*/
      }

      if(printerBT !== '1'){
        const dataPrinter = {
          businessId: datainvoice.host,
          sucursal: datainvoice.sucursal,
          printerName: 'CAJA',
          tipoTicket: 'ticket',
          content: datainvoice
        };

        console.log(dataPrinter);

        try {
          //const url = "http://localhost:3100/api/print/printJob"; //llamado a la API server print nodejs/ts
          const url = "https://servidorimpresionposws-production.up.railway.app/api/print/printJob"; //llamado a la API server print nodejs/ts
          const respuesta = await fetch(url, {
            method: 'POST',
            headers: { "Accept": "application/json", "Content-Type": "application/json" },
            body: JSON.stringify(dataPrinter)
        });
          const resultado = await respuesta.json();
          console.log(resultado);
        } catch (error) {
          console.log(error);
        }
      }

      setTimeout(() => {
        window.open("/admin/printPDFPOS?id=" + idfactura, "_blank");  //llama a printcontrolador.php
      }, 1000);

    }

    
    /////////////////////////obtener datos de cotizacion /////////////////////
    const parametrosURL = new URLSearchParams(window.location.search);
    const id = parametrosURL.get('id');
    let datosfactura:{id:string, idcliente: string, idvendedor:string, idcaja:string, idconsecutivo:string, iddireccion:string, idtarifazona:string, idcierrecaja:string, num_orden:string, cliente:string, vendedor:string, caja:string, tipofacturador:string, direccion:string, tarifazona:string, totalunidades:string, recibido:string, transaccion:string, tipoventa:string,
                      cotizacion:string, estado:string, cambioaventa:string, referencia:string, subtotal:string, base:string, valorimpuestototal:string, dctox100:string, descuento:string, total:string, observacion:string, departamento:string, ciudad:string, entrega:string, valortarifa:string, fechacreacion:string, fechapago:string, opc1:string, opc2:string};
    if(id){
      (async ()=>{
        try {
            const url = "/admin/api/getcotizacion_venta?id="+id; //llamado a la API REST
            const respuesta = await fetch(url); 
            const resultado = await respuesta.json();
            datosfactura = resultado.factura;
            carrito = resultado.productos;
            carrito.forEach((item, i) =>printProduct(item.idproducto, item.valorunidad, i));
            //valorCarritoTotal(); //recalcula impuestos de la cotizacion y valores totales
            (document.querySelector('#npedido') as HTMLInputElement).value = datosfactura.num_orden;
            $('#selectCliente').val(datosfactura.idcliente).trigger('change');
        } catch (error) {
            console.log(error);
        }
      })();
    }

    function limpiarformdialog(){
      (document.querySelector('#formAddCliente') as HTMLFormElement)?.reset();
    }


    //exponer variables y funciones globalmente
    POS.limpiarformdialog = limpiarformdialog;
    POS.valorCarritoTotal = valorCarritoTotal;
    POS.actualizarCarrito = actualizarCarrito;
    //POS.calcularCambio = calcularCambio;
    POS.cerrarDialogoExterno = cerrarDialogoExterno;
    POS.tarifas = tarifas;
    POS.valorTotal = valorTotal;
    POS.mapMediospago = mapMediospago;
    POS.tipoventa = tipoventa;
    POS.carrito = carrito;
    //POS.products = products;
  } 


})();
