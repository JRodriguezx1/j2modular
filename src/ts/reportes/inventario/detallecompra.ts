(():void=>{

  if(document.querySelector('.detallecompra')){

    const btnPrintCompra = document.querySelector('#btnPrintCompra') as HTMLButtonElement;

    //capturar el id de la compra por la url
    const parametrosURL = new URLSearchParams(window.location.search);
    const id:string = parametrosURL.get('id')??'';
    ///////// imprimir detalle de compra
    btnPrintCompra.addEventListener('click', ()=>{
      if(id!='' || !isNaN(Number(id))){
        //const ventana = window.open('/printDetalleCompra?id='+id, '_blank');
        const ventana = window.open('/admin/printComprobanteCompraPDF?id='+id, '_blank');
        if(ventana){
            ventana.onload = ()=>{
                ventana?.focus();
                ventana?.print();
            };
        }
      }else{
        msjalertToast('error', '¡Error!', 'Error al generar impresion.');
      }
    });

  }

})();