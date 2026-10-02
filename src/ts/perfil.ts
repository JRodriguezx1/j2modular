(()=>{
    if(document.querySelector('.perfil')){
      const btnCambiarEmail = document.querySelector('#btnCambiarEmail');
      const miDialogoUpEmail = document.querySelector('#miDialogoUpEmail') as any;
      //const btnCerrarUpDireccion = document.querySelector('#btnCerrarUpDireccion') as HTMLButtonElement;
      
  
      btnCambiarEmail?.addEventListener('click', (e):void=>{
        miDialogoUpEmail.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      }); 

      //btnCerrarUpDireccion.addEventListener('click', ()=>miDialogoUpDireccion.close());

  
      function cerrarDialogoExterno(event:Event) {
        if (event.target === miDialogoUpEmail || (event.target as HTMLInputElement).value === 'salir') {
          miDialogoUpEmail.close();
          document.removeEventListener("click", cerrarDialogoExterno);
        }
      }
    }
  
  })();