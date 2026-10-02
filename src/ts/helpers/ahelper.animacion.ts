(()=>{
  if(!document.querySelector('.ventas')&&!document.querySelector('.modorapido'))return;

  const POS = (window as any).POS;

  const carritoMovilBadge = document.querySelector('#carritoMovilBadge') as HTMLElement;


  function actualizarBadgeCarritoMovil(cantidad:number): void{ //contador de productos en el boton de abrir modal para facturar en pantalla pequeña
    carritoMovilBadge.textContent = formatCantidadBadge(cantidad);
    carritoMovilBadge.classList.toggle('is-visible', cantidad > 0);
  }

  let ventaCarritoToastTimer:number|undefined;
  function mostrarFeedbackCarrito(nombre:string, cantidadProducto:number): void{
    if(window.innerWidth >= 992)return;
    const ventaCarritoToast = document.querySelector('#ventaCarritoToast') as HTMLElement;
    const ventaCarritoToastTitle = document.querySelector('#ventaCarritoToastTitle') as HTMLElement;
    const ventaCarritoToastMeta = document.querySelector('#ventaCarritoToastMeta') as HTMLElement;
    ventaCarritoToastTitle.textContent = `${nombre} agregado`;
    ventaCarritoToastMeta.textContent = `Cantidad: ${cantidadProducto}`;
    ventaCarritoToast.classList.add('is-visible');
    if(ventaCarritoToastTimer)window.clearTimeout(ventaCarritoToastTimer);
    ventaCarritoToastTimer = window.setTimeout(()=>ventaCarritoToast.classList.remove('is-visible'), 1500);
  }
  

  const gestionAnimaciones = {  //objeto a exportar
    mostrarFeedbackCarrito,
    actualizarBadgeCarritoMovil,
  };

  POS.gestionAnimaciones = gestionAnimaciones;

})();