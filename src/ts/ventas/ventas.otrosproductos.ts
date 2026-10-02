(()=>{
  if(!document.querySelector('.ventas'))return;

    const POS = (window as any).POS;

    const btnotros = document.querySelector('#btnotros') as HTMLButtonElement;  //btn de otros
    const miDialogoOtrosProductos = document.querySelector('#miDialogoOtrosProductos') as any;
    let otrosproductos:{id:number, nombre:string, cantidad:number, valorunidad:number, total:number}={
      id: 0,
      nombre: '',
      cantidad: 0,
      valorunidad: 0,
      total: 0
    }

    ///////////////////// Evento al btn Otros /////////////////////////
    btnotros.addEventListener('click', (e:Event)=>{
      miDialogoOtrosProductos.showModal();
      document.addEventListener("click", POS.cerrarDialogoExterno);
    });

    const gestionOtrosProductos = {
      miDialogoOtrosProductos,  
      otrosproductos(){

        /////////////////////Evento al formulario de agregar otros productos //////////////////////
        document.querySelector('#formOtrosProductos')?.addEventListener('submit', (e:Event)=>{
            e.preventDefault();
            const formelements = (e.target as HTMLFormElement).elements;
            const impuesto = (formelements.namedItem('porcentaje_de_impuesto') as HTMLSelectElement).value;
            const cantidadotros = Number((formelements.namedItem('cantidadotros') as HTMLInputElement).value);
            const preciootros = Number((formelements.namedItem('preciootros') as HTMLInputElement).value);
            
            otrosproductos!.id += -1 ;
            otrosproductos!.nombre = (formelements.namedItem('nombreotros') as HTMLInputElement).value;
            otrosproductos!.cantidad = cantidadotros;
            otrosproductos!.valorunidad = preciootros/cantidadotros;
            otrosproductos!.total = preciootros;
            
            POS.products.push({
                ID: otrosproductos!.id+'',
                id: otrosproductos!.id+'', 
                idcategoria: '-1',
                idunidadmedida: '1',
                nombre: otrosproductos!.nombre,
                foto: 'na',
                impuesto: impuesto==''?null:impuesto, //impuesto en %
                marca: 'na',
                tipoproducto: '-1', // 0 = simple,  1 = compuesto
                tipoproduccion: '-1', //0 = inmediato, 1 = construccion
                codigo: '-1',
                unidadmedida: 'Unidad',
                descripcion: 'na',
                peso: 'na',
                medidas: 'na',
                color: 'na',
                funcion: 'na',
                uso: 'na',
                fabricante: 'na',
                garantia: 'na',
                stock: '0',
                promediostock: '0',
                stockminimo: '1',
                categoria: 'na',
                rendimientoestandar: '1',
                precio_compra: '0',
                precio_venta: otrosproductos!.valorunidad+'',
                percentcomision: 0,
                fecha_ingreso: '',
                estado: '1',
                visible: '1',
                insumos: []
            });

            POS.actualizarCarrito(otrosproductos.id+'', cantidadotros, false, false, otrosproductos!.valorunidad+'', null);
            POS.mostrarFeedbackCarrito?.(otrosproductos!.nombre || 'Producto personalizado', cantidadotros);
            miDialogoOtrosProductos.close();
            document.removeEventListener("click", POS.cerrarDialogoExterno);
            (e.target as HTMLFormElement).reset();
        });

      }
    };

    (window as any).POS.gestionOtrosProductos = gestionOtrosProductos;

})();