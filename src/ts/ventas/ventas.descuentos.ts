(()=>{
  if(!document.querySelector('.ventas')&&!document.querySelector('.crearseparado'))return;

    const POS = (window as any).POS;

    const btndescuento = document.querySelector('#btndescuento') as HTMLButtonElement;
    const miDialogoDescuento = document.querySelector('#miDialogoDescuento') as any;
    const tipoDescts = document.querySelectorAll<HTMLInputElement>('input[name="tipodescuento"]'); //radio buttom
    const inputDescuento = document.querySelector('#inputDescuento') as HTMLInputElement;
    const inputDescuentoClave = document.querySelector('#inputDescuentoClave') as HTMLInputElement;
    let valorMax = 0;
    
    interface clavesApi {
      clave:string,
      valor_default:string|null,
      valor_final:string|null,
      valor_local:string|null
    };

    let clavedcto:clavesApi[];

    (async ()=>{
      try {
          const url = "/admin/api/getPasswords"; //llamado a la API REST
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          clavedcto = resultado;
      } catch (error) {
          console.log(error);
      }
    })();
    
    btndescuento?.addEventListener('click', ()=>{
      if(POS.carrito.length){
        valorMax = POS.valorTotal.subtotal;
        //validar que si al reducir los productos o aumentar recalcular el porcentaje
        miDialogoDescuento.showModal();
        document.addEventListener("click", POS.cerrarDialogoExterno);
      }
    });

    /////////////////////  logica del descuento  //////////////////////////
    tipoDescts.forEach(desc=>{ //evento a los radiobutton
      desc.addEventListener('change', (e:Event)=>{
        if((e.target as HTMLInputElement).value === "porcentaje"){
          valorMax = 100;
          inputDescuento.value = '';
        }
        if((e.target as HTMLInputElement).value === "valor"){
          inputDescuento.value = '';
          valorMax = POS.valorTotal.subtotal;
        }
      });
    });

    inputDescuento?.addEventListener('input', (e)=>{
      var valorInput:number = Number((e.target as HTMLInputElement).value);
      if(valorInput > valorMax){
        inputDescuento.value = valorMax+'';
        valorInput = valorMax; 
      }
    });

    document.querySelector('#formDescuento')?.addEventListener('submit', e=>{
      e.preventDefault();
      const valorInput:number = Number(inputDescuento.value);
      if(tipoDescts[0].checked){  //tipo valor
        POS.valorTotal.dctox100 = Math.round((valorInput*100)/POS.valorTotal.subtotal);  // valor en porcentaje
        POS.valorTotal.descuento = valorInput;  //valor del dcto
      }
      if(tipoDescts[1].checked){ //tipo porcentaje
        POS.valorTotal.descuento = (POS.valorTotal.subtotal*valorInput)/100;  //valor descontado
        POS.valorTotal.dctox100 = valorInput;  //valor en porcentaje
      }
      //validar en backend password
      const v:number = validarPasswordDcto();
      if(!v)return;
      
      POS.valorTotal.total = POS.valorTotal.subtotal - POS.valorTotal.descuento + POS.valorTotal.valortarifa;
      document.querySelector('#total')!.textContent = '$ '+POS.valorTotal.total.toLocaleString();
      (document.querySelector('#descuento') as HTMLElement).textContent = '$'+POS.valorTotal.descuento.toLocaleString();
      POS.gestionSubirModalPagar?.calculoTasaInteres?.();
      miDialogoDescuento.close();
      document.removeEventListener("click", POS.cerrarDialogoExterno);
    });
    
    function validarPasswordDcto():number{
      const clave = clavedcto.find(c => c.clave=='clave_para_agregar_descuento');
      if(clave?.valor_final!==null && inputDescuentoClave.value !== clave?.valor_final){
        msjAlert('error', 'El password es invalido', (document.querySelector('#divmsjalertaClaveDcto') as HTMLElement));
        return 0;
      }
      return 1;
    }
    
    const gestionarDescuentos = { miDialogoDescuento }

    POS.gestionarDescuentos = gestionarDescuentos;
    

})();
