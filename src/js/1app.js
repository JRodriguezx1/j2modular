///////////////////// variables superglobales de todo el sistema /////////////////////
//let serviciosglobal;

///////////////////// OBJETO DE CONFIGURACION DEL PLUGIN DATATABLES /////////////////////
const configdatatables = {
  "paging": true,
  "lengthChange": true,
  "searching": true,
  "ordering": true,
  "info": true,
  "autoWidth": true,
  "responsive": true,
  "deferRender": true,
  "retrieve": true,
  "processing": true,
  language: {
      search: 'Busqueda',
      emptyTable: 'No Hay datos disponibles',
      zeroRecords:    "No se encontraron registros coincidentes",
      lengthMenu: '_MENU_ Entradas por pagina',
      info: 'Mostrando pagina _PAGE_ de _PAGES_',
      infoEmpty: 'No hay entradas a mostrar',
      infoFiltered: ' (filtrado desde _MAX_ registros)',
      paginate: {"first": "<<", "last": ">>", "next": ">", "previous": "<"}
  }
}

///////////////////// FUNCION QUE IMPRIME MENSAJE TIPO ALERTA /////////////////////
function msjAlert(tipo, msj, divmsjalerta){
  divmsjalerta.insertAdjacentHTML('beforeend', `<div class="alerta alerta__${tipo}">
      <p><i class="fa-solid fa-circle-exclamation"></i> ${msj}</p></div>`
  );
}

//////////////////// BORRAR MENSAJES TIPO ALERTA /////////////////////
(borrarMsjAlert =()=>{  //se aplica de manera global
  const msj = document.querySelector('#divmsjalerta');
  if(document.querySelector('.alerta'))setTimeout(()=>{ while(msj.firstChild)msj.removeChild(msj.firstChild);}, 5000);
})();

//////////////////// FUNCION QUE IMPRIME UN MENSAJE FORMATO TOAST ////////////////////
//msjalertToast('error', '¡Error!', 'debe seleccionar una imagen')
function msjalertToast(icono, tipo, msj){
    Swal.fire({
    icon: icono,  // Puedes cambiar el ícono (success, error, warning, info, etc.)
    title: tipo,
    text: msj,
    toast: true,
    position: 'top-end',  
    showConfirmButton: false,  
    timer: 2700  
    });
}

/////////////////// FUNCION CONTADOR DE CARACTERES ////////////////////
(function countchars(){
  const numinput = document.querySelectorAll('.count-charts');  
  numinput.forEach(element =>{ //element es cada label
      element.textContent = element.dataset.num-element.previousElementSibling.value.length;
      element.previousElementSibling.addEventListener('input', (e)=>{ //seleccionamos el input o el textarea en donde se escribe y se le da el evento de teclas
          element.textContent = element.dataset.num-e.target.value.length;
            
          if(element.dataset.num-e.target.value.length <= 0){
              let cadena = e.target.value.slice(0, element.dataset.num);
              e.target.value = cadena;
              element.textContent = 0;
          }
      });
  });
})();


 //////////////////////bar progress //////////////////////
 if(document.querySelector('.barraprogreso')){
  const negocios = document.querySelectorAll('#negocio');
  const label = document.querySelector('.barraprogreso__label');
  const barra = document.querySelector('.barraprogreso__barra');
  let bar = 0;
  negocios.forEach(negocio=>{if(negocio.value)bar++;});
  barra.classList.add('animation');
  label.textContent = ((bar*100)/11).toFixed(1)+'%';
  const animationbarra = document.querySelector('.animation');
  animationbarra.style.width = label.textContent; //barra de progreso dinamicamente
}


/////////////////// paginacion de negocio empleado, malla, config //////////////////
if(document.querySelector('#tabulacion')){ // view/admin/adminconfig/index.php
  const renderid = document.querySelector('#tabulacion input[type="radio"]:checked').nextElementSibling; //se selecciona el input cheked y luego su span q contiene el id de la pagina a mostrar
  document.querySelector(`.${renderid.id}`).classList.add('mostrarseccion'); //mostramos la primera seccion
  const btns_navtabs = document.querySelectorAll('.tabs span');
  const paginas = document.querySelectorAll('.paginas'); //seleccionamos las secciones o paginas a mostrar
  btns_navtabs.forEach(Element => {
      Element.addEventListener('click', (e)=>{ //cada btn o enlace
          paginas.forEach(pagina => pagina.classList.remove('mostrarseccion')); ////quitamos la class mostrarseccion a todas las secciones
          document.querySelector(`.${e.target.id}`).classList.add('mostrarseccion'); //añadimos la class mostrarseccion a la la seccion o pagina correspondiente
      });
  });
}

///////////////////// borrar html ////////////////////////
function borrarhtml(elemento){
  horasdisponibles = [];
  while(elemento.firstElementChild)elemento.removeChild(elemento.firstElementChild);
}


const serviciosGlobal = async ()=>{ //funcion llamada en pack.js
  try {
      const url = "/admin/api/getservices"; //llamado a la API REST
      const respuesta = await fetch(url); 
      const resultado = await respuesta.json();
      return resultado;
  } catch (error) {
      console.log(error);
  }
}

