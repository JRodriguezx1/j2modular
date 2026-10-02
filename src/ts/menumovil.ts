

// const mobilemenu1 = document.querySelector('#mobile-menu1')as HTMLButtonElement;
// const miDialogomenumovil = document.querySelector('#miDialogomenumovil') as any;

// mobilemenu1.addEventListener('click', ()=>{
//     miDialogomenumovil.showModal();
//     document.addEventListener('click', cerrarDialogoExterno);
// });


// function cerrarDialogoExterno(event:Event){
//  const f = event.target;
//  if(f === miDialogomenumovil){
//     miDialogomenumovil.close();
//  }
// }
const mobilemenu1 = document.querySelector('#mobile-menu1') as HTMLButtonElement;
const miDialogomenumovil = document.querySelector('#miDialogomenumovil') as HTMLDialogElement;

// aseguramos que al cargar la página el modal esté cerrado y oculto
miDialogomenumovil.close();
miDialogomenumovil.setAttribute('hidden', 'true');

mobilemenu1.addEventListener('click', () => {
  miDialogomenumovil.showModal();
  miDialogomenumovil.removeAttribute('hidden');
  document.addEventListener('click', cerrarDialogoExterno);
});

function cerrarDialogoExterno(event: Event) {
  const f = event.target;
  if (f === miDialogomenumovil) {
    miDialogomenumovil.close();
    miDialogomenumovil.setAttribute('hidden', 'true');
    document.removeEventListener('click', cerrarDialogoExterno);
  }
}


