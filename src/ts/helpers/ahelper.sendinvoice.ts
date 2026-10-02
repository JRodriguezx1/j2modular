(()=>{
  if(document.querySelector('.ventas') || document.querySelector('.ordenresumen') || document.querySelector('.detalleinvoice') || document.querySelector('.modorapido')){

    const sendInvoiceAPI = {

      async sendInvoice(x:number){
        console.log(x);
        try {
            const url = "/admin/api/sendInvoice"; //llamado a la API REST apidiancontrolador.php
            const respuesta = await fetch(url, {
              method: 'POST', 
              headers: { "Accept": "application/json", "Content-Type": "application/json" },
              body: JSON.stringify({id: x}) 
            });
            
            const responseDian = await respuesta.json();
            /*return new Promise(resolve => {
              setTimeout(() => {
                  resolve('ok');
              }, 4000);
            }); */
            return responseDian;
        } catch (error) {
            console.log(error);
        }
      }
    };
  

    (window as any).POS.sendInvoiceAPI = sendInvoiceAPI;
  }

})();