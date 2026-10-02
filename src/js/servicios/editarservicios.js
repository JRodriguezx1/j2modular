(function(){
  if(document.querySelector('.editarservicios')){
    $('#idcategoriaS').select2();
    $('#updateCategoria').select2({dropdownParent: $("#miDialogoServicio")});
    const dialogoEditarServicio = document.getElementById("miDialogoServicio");
    const dialogoQuestion = document.getElementById("miDialogoQuestionService");
    const cards = document.querySelectorAll('.card-description');
    const formUpdateServicio = document.querySelector('#formUpdateServicio');
    
    var options = {
      valueNames: [ 'card-title', 'card-category']
    };
    var hackerList = new List('hacker-list', options);

    //evento a card-description servicio
    cards.forEach(card=>{
      card.addEventListener('click', ()=>{
        dialogoQuestion.children[0].children[0].textContent = card.querySelector('.card-price').textContent;
        dialogoQuestion.children[0].children[1].textContent = card.querySelector('.card-title').textContent;
        dialogoQuestion.children[0].children[2].textContent = card.querySelector('.updatedate').textContent;
        dialogoQuestion.children[1].id = card.id;
        dialogoQuestion.showModal();
        document.addEventListener("click", cerrarDialogoExterno);
      });
    });

    function cerrarDialogoExterno(event) {
      const f = event.target;
      if (event.target === dialogoQuestion || event.target == dialogoEditarServicio || f.closest('.editarservicio') || f.closest('.eliminarservicio') || event.target.value === 'cancelar' || event.target.value === 'Actualizar'){
          dialogoQuestion.close();
          dialogoEditarServicio.close();
          document.removeEventListener("click", cerrarDialogoExterno);
          if(f.closest('.editarservicio')){
            ///actualizar el servicio
            cargardatos(f.closest('.editarservicio').parentElement.id);
            dialogoEditarServicio.showModal();
            document.addEventListener("click", cerrarDialogoExterno);
          }
          if(f.closest('.eliminarservicio')){
            //eliminar el servicio
            eliminarServicio(f.closest('.eliminarservicio').parentElement.id);
          } 
      }
    } //fin cerrar dialogoExterno

    // cargar datos del servicio en el modal de actualizar el servicio
    function cargardatos(id){
      var elemento = document.querySelector(`.card-description[id="${id}"]`);
      document.querySelector('#updateid').value = id;
      $('#updateCategoria').val(elemento.dataset.categoria).trigger('change');
      document.querySelector('#updatenombre').value = elemento.querySelector('.card-title').textContent;
      $('#updateprecio').val(elemento.querySelector('.card-price').textContent.replace('.', '').slice(1));
    }

    formUpdateServicio.addEventListener('submit', (e)=>{
      e.preventDefault();
      const idservicio =  $('#updateid').val();
      console.log(idservicio);
      (async ()=>{ 
        const datos = new FormData();
        datos.append('id', idservicio);
        datos.append('idcategoriaS', $('#updateCategoria').val());
        datos.append('nombre', $('#updatenombre').val());
        datos.append('precio', $('#updateprecio').val()); 
        try {
            const url = "/admin/api/actualizarServicio";
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();  
            if(resultado.exito !== undefined){
              var card = document.querySelector(`.card-description[id="${idservicio}"]`);
              card.querySelector('.card-title').textContent = $('#updatenombre').val();
              card.querySelector('.card-price').textContent = '$'+Number($('#updateprecio').val()).toLocaleString(); //con number y tolocalestring formateamos con puntos de miles
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
            }else{
              msjalertToast('error', '¡Error!', resultado.error[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();//cierre de async() 
    });


    function eliminarServicio(id){
      Swal.fire({
        customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
        icon: 'question',
        title: 'Desea eliminar el servicio?',
        text: "El servicio sera elminado y las citas deben reprogramarse!",
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
      }).then((result) => {
          if (result.isConfirmed) {
            (async() =>{ //
              const datos = new FormData();
              datos.append('id', id);
              try {
                  const url = "/admin/api/eliminarservicio";
                  const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                  const resultado = await respuesta.json();  
                  if(resultado.exito !== undefined){
                    Swal.fire(resultado.exito[0], '', 'success');
                    const divservicio = document.querySelector(`.card-description[id="${id}"]`).parentElement;
                    divservicio.remove();
                  }else{
                    Swal.fire(resultado.error[0], '', 'error');
                  }
              } catch (error) {
                  console.log(error);
              }
            })();
          }
      }); //fin swal.fire
    }

  }
})();
