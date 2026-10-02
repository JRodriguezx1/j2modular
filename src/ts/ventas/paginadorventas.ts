
(()=>{
    if(document.querySelector('.ventas')){

      const POS = (window as any).POS;

      const inputBuscar = document.querySelector<HTMLInputElement>('#buscarproducto');
      const filtrocategorias = document.querySelectorAll('.filtrocategorias');  //los btns de las categorias
      const btnCategorias = document.querySelector<HTMLButtonElement>('#btnCategorias');
      const menuCategorias = document.querySelector<HTMLDivElement>('#menuCategorias');

      var options = {
        valueNames: [ 'card-producto', { data: ['id', 'categoria', 'code'] }],
        page: 18,
        pagination: true,
      };
      const productos = document.querySelectorAll<HTMLElement>('.producto');
      if(productos.length)
      var hackerList = new List('hacker-list', options);
      POS.hackerList = hackerList;  //exportar
      
      //resatlar la categoria seleccionada
      const setCategoriaActiva = (categoriaActiva: string) => {
        filtrocategorias.forEach((categoriaEl) => {
          const item = categoriaEl as HTMLElement;
          const check = item.querySelector('i');
          const activo = item.dataset.categoria === categoriaActiva;

          item.classList.toggle('categoria-activa', activo);
          item.classList.toggle('bg-indigo-50', activo);
          item.classList.toggle('text-indigo-700', activo);
          item.classList.toggle('font-semibold', activo);
          item.classList.toggle('text-slate-700', !activo);
          check?.classList.toggle('hidden', !activo);
        });
      };

      POS.reiniciarCatalogoVentas = ():void => {
        if(inputBuscar)inputBuscar.value = '';
        const categorySelect = document.querySelector('#categorySelect');
        if(categorySelect)categorySelect.textContent = 'Todos';
        setCategoriaActiva('Todos'); //resalta la categoria todos, despues de realizar venta
        if(hackerList){
          hackerList.search('');
          hackerList.filter();
          hackerList.show(1, options.page);
          hackerList.update();
        }
      };

      filtrocategorias.forEach(element => {
        element.addEventListener('click', (e)=>{
          const categoriaItem = (e.target as HTMLElement).closest('.filtrocategorias') as HTMLElement | null;
          const categoria:string = categoriaItem?.dataset.categoria || 'Todos';
          document.querySelector('#categorySelect')!.textContent = categoria;
          setCategoriaActiva(categoria);
          if(hackerList)
            hackerList.filter((item: any)=>{
              hackerList.search('');
              if(categoria === 'Todos')return true;
              return item.values().categoria === categoria
            });
        });
      });


      inputBuscar?.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;

        e.preventDefault();

        if (!hackerList) return;

        const items = hackerList.visibleItems;

        console.log(items);
        if (!items.length) {
          inputBuscar.value = '';
          hackerList.search('');
          return;
        }

        const productosku = items[0].elm as HTMLElement;
        const products = POS.products as productsapi[];
        const productoItem = products.find(x=>x.id == productosku.dataset.id);
        const productoConfigurado = structuredClone(productoItem);
        filtrarInsumos(productoConfigurado);
        POS.actualizarCarrito(productosku.dataset.id, 1, true, true, productoItem?.precio_venta, productoConfigurado);

        inputBuscar.value = '';
        hackerList.search('');
      });


      //////--------  Apertura y cierre del btn Categorias

      btnCategorias?.addEventListener('click', (e) => {
        e.stopPropagation(); // evita cierre inmediato
        menuCategorias?.classList.toggle('hidden');
        if(!menuCategorias?.classList.contains('hidden'))document.addEventListener('click', cerrarMenuCategorias);
      });
      // cerrar al hacer click en una categorÃ­a
      document.querySelectorAll('.filtrocategorias').forEach(el => {
        el.addEventListener('click', () => {
          menuCategorias?.classList.add('hidden');
          document.removeEventListener('click', cerrarMenuCategorias);
        });
      });
      // cerrar al hacer click fuera del menÃº
      function cerrarMenuCategorias() {
        menuCategorias?.classList.add('hidden');
        document.removeEventListener('click', cerrarMenuCategorias);
      }



      //////---------------------------------------------------------------------------


      const btnCarritoMovil = document.querySelector('#btnCarritoMovil') as HTMLButtonElement; //btn para abrir el modal del carrito de ventas en movil
      const contenidocarrito = document.getElementById('contenidocarrito') as HTMLElement;
      const contenedorDesktop = document.getElementById('contenedorDesktop') as HTMLDivElement;
      //const miDialogoCarritoMovil = document.getElementById('miDialogoCarritoMovil') as HTMLDialogElement;
      const btnCerrarCarritoMovil = document.querySelector('#btnCerrarCarritoMovil') as HTMLButtonElement;
      const overlayCarrito = document.getElementById('overlayCarrito') as HTMLDivElement;
      const screenWidth:number = window.innerWidth;

      /*if(screenWidth<992)miDialogoCarritoMovil.appendChild(contenidocarrito);

      window.addEventListener('resize', ()=>{
        if (window.innerWidth >= 992) {
          contenedorDesktop.appendChild(contenidocarrito);
          miDialogoCarritoMovil.close();
          document.removeEventListener("click", cerrarDialogoExterno);
        } else {
          miDialogoCarritoMovil.appendChild(contenidocarrito);
        }
      });*/

      // btn para abrir el carrito de ventas en movil
      btnCarritoMovil?.addEventListener('click', (e)=>{
        //miDialogoCarritoMovil.showModal();
        //document.addEventListener("click", cerrarDialogoExterno);
        contenedorDesktop.classList.remove('translate-x-full');
        overlayCarrito.classList.remove('hidden');
      });

      btnCerrarCarritoMovil.addEventListener('click', ()=>{
        //miDialogoCarritoMovil.close();
        //document.removeEventListener("click", cerrarDialogoExterno);
        contenedorDesktop.classList.add('translate-x-full');
        overlayCarrito.classList.add('hidden');
      });

      /*function cerrarDialogoExterno(event:Event) {
        const f = event.target;
        if (f === miDialogoCarritoMovil ) {
          miDialogoCarritoMovil.close();
          document.removeEventListener("click", cerrarDialogoExterno);
        }
      }*/


      /*const paginador ={

      }
      POS.paginador = paginador;*/

    }

})();
