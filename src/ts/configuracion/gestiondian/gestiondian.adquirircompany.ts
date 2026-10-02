(()=>{
    if(!document.querySelector('.gestionDian'))return;
    const POS = (window as any).POS;

    const btnAdquirirCompañia = document.querySelector('#btnAdquirirCompañia') as HTMLButtonElement;
    const miDialogoAdquirirCompañia = document.querySelector('#miDialogoAdquirirCompañia') as any;

    btnAdquirirCompañia.addEventListener('click', ()=>{
        miDialogoAdquirirCompañia.showModal();
        document.addEventListener("click", POS.cerrarDialogoExterno);
    });

    ///////    CONSULTAR COMPAÑIA POR NIT   ///////
    document.querySelector('#formAdquirirCompañia')?.addEventListener('submit', async (e:Event)=>{
      e.preventDefault();
      const nit = (document.querySelector('#nitcompany') as HTMLInputElement).value;
      const adquirirCompañiaPassword = (document.querySelector('#adquirirCompañiaPassword') as HTMLInputElement).value;
      miDialogoAdquirirCompañia.close();
      document.removeEventListener("click", POS.cerrarDialogoExterno);
      try {
        const url = `https://apidianj2.com/api/getCompany/${nit}`; //llamado a la API REST Dian-laravel para consultar la compañia por nit
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();
        const idcompany = await POS.crearCompanyJ2({ business_name:resultado.compañia.user.name, identification_number: resultado.compañia.identification_number, idsoftware: resultado.compañia.software.identifier, pinsoftware:12345, estado:1 }, resultado.compañia.user.api_token);
        
        /////    crear resolucion para NC    ///////
        const extprefix = (resultado.compañia.user.name).match(/[a-zA-Z]/g)!;
        const a:string = extprefix[0];
        const b:string = extprefix[extprefix.length-1];
        POS.crearResolucionNCJ2({ type_document_id: '4', prefix: 'NC'+a+b, from: '1', to: '99999999' }, resultado.compañia.identification_number, idcompany);
      } catch (error) {
        console.log(error);
      }
    });


    const gestionAdquirirCompany = {  //objeto a exportar
        miDialogoAdquirirCompañia,
    };

    POS.gestionAdquirirCompany = gestionAdquirirCompany;

})();