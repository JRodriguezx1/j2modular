(function(){
  if(document.querySelector('.contenedorsetup')){

    const radios = document.querySelectorAll<HTMLInputElement>('.contenedorsetup input[type="radio"]');
    const claves = document.querySelectorAll<HTMLInputElement>('.keyinput');
    const porcentaje_de_impuesto = document.querySelector('#porcentaje_de_impuesto') as HTMLSelectElement;
    const indicador_caja = document.querySelector('#indicador_caja') as HTMLSelectElement;
    const medidaFuenteDatosFactura = document.querySelector('#medidaFuenteDatosFactura') as HTMLSelectElement;
    const medidaFuenteSeccionClienteFactura = document.querySelector('#medidaFuenteSeccionClienteFactura') as HTMLSelectElement;
    const medidaFuenteProductosFactura = document.querySelector('#medidaFuenteProductosFactura') as HTMLSelectElement;
    
    
    const resaltarParametroGuardado = (elemento:HTMLElement|null)=>{
      if(!elemento)return;
      const contenedor = elemento.closest('.config-system-content .accordion_tab_content > .grid > div, .config-whatsapp-event') as HTMLElement|null;
      const objetivo = contenedor ?? elemento;
      objetivo.classList.remove('config-param-saved');
      window.setTimeout(()=>objetivo.classList.add('config-param-saved'), 20);
      window.setTimeout(()=>objetivo.classList.remove('config-param-saved'), 1800);
    };

    radios.forEach(radio => {
      radio.addEventListener('change', () => {
        // Obtenemos el grupo (name) del radio
        const grupo:string = radio.name;
        // Obtenemos la opción seleccionada de ese grupo
        const seleccionado = document.querySelector(`input[name="${grupo}"]:checked`) as HTMLInputElement;

        //console.log(`${grupo}, Opción seleccionada: ${seleccionado.value}`);
        cambiarparametro(grupo, seleccionado.value, seleccionado);
      });
    });

    ////////////// inputs tipo radio  ////////////////
    function cambiarparametro(clave:string, valor:string, elemento?:HTMLElement){
      const datos = new FormData();
      datos.append(clave, valor);
      (async ()=>{
        try {
            const url = "/admin/api/parametrosSistema";  //api llamada en parametroscontrolador.php
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              resaltarParametroGuardado(elemento ?? null);
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
    }

    //////////  CLAVES  ////////////
    ///////////  eventos a los inputs de las claves, limite de descuento, cantidad de zeta diarios etc 'Campos de entrada' /////////////
    claves.forEach(c=>{
      c.addEventListener('input', (e)=>{  
        const inputClave = (e.target as HTMLInputElement);
        const valorClave = (e.target as HTMLInputElement).value;
        
        (async ()=>{
          const datos = new FormData();
          datos.append(inputClave.name, valorClave);
          try {
              const url = "/admin/api/parametrosSistemaClaves";  //api llamada en parametroscontrolador.php
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();
              if(resultado.exito !== undefined){
                resaltarParametroGuardado(inputClave);
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();
      });
    });



    //////////////////   campo tipo de impuesto (%)   /////////////////////
    porcentaje_de_impuesto?.addEventListener('input', parametrosSistemaTipoSelect);
    //////////////////   Indicadores de caja   /////////////////////
    indicador_caja?.addEventListener('input', parametrosSistemaTipoSelect);
    //////////////////   Indicadores de caja   /////////////////////
    medidaFuenteDatosFactura?.addEventListener('input', parametrosSistemaTipoSelect);
    //////////////////   Indicadores de caja   /////////////////////
    medidaFuenteSeccionClienteFactura?.addEventListener('input', parametrosSistemaTipoSelect);
    //////////////////   Indicadores de caja   /////////////////////
    medidaFuenteProductosFactura?.addEventListener('input', parametrosSistemaTipoSelect);


    function parametrosSistemaTipoSelect(e:Event){
      const select = (e.target as HTMLSelectElement);
      (async ()=>{
          const datos = new FormData();
          datos.append(select.name, select.value);
          try {
              const url = "/admin/api/parametrosSistemaTipoSelect";  //api llamada en parametroscontrolador.php
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();
              if(resultado.exito !== undefined){
                resaltarParametroGuardado(select);
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              }else{
                msjalertToast('error', '¡Error!', resultado.error[0]);
              }
          } catch (error) {
              console.log(error);
          }
      })();
    }

    
  }

})();
