(()=>{
    if(document.querySelector('.inventarioxproducto')){
        let  tablaStockRapido:HTMLElement;
        tablaStockRapido = ($('#tablaStockRapido') as any).DataTable(configdatatablesstockrapido);
    }

})();