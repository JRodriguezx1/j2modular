(function(){
  if(document.querySelector('.empleado')){
    const crearempleado = document.querySelector('#crearempleado');
    const dialogo = document.getElementById("miDialogoEmpleado");
    const dialogoSkills = document.getElementById("miDialogoSkills");
    const btnupImage = document.querySelector('#upImage');
    const btncustomUpImage = document.querySelector('#customUpImage');
    const imginputfile = document.querySelector('#imginputfile');  //img

    const inputNombre = document.querySelector('#nombre');
    const inputApellido = document.querySelector('#apellido');
    const inputMovil = document.querySelector('#movil ');
    const inputEmail = document.querySelector('#email');
    const inputDepartamento = document.querySelector('#departamento');
    const inputCiudad = document.querySelector('#ciudad');
    const inputDireccion = document.querySelector('#direccion');
    const inputPerfil = document.querySelector('#perfil');

    let indiceFila=0, control=0, tablaempleados, empleadosapi, unempleado;

    /////////////////  traer todos los empleados con sus skills  ///////////////////
    (async ()=>{
      try {
          const url = "/admin/api/getAllemployee"; //llamado a la API REST
          const respuesta = await fetch(url); 
          empleadosapi = await respuesta.json(); 
          console.log(empleadosapi);
      } catch (error) {
          console.log(error);
      }
    })();

    //////////////////  TABLA //////////////////////
    tablaempleados = $('#tablaempleados').DataTable(configdatatables);

    //////////////////// ventana modal al crear empleado  //////////////////////
    crearempleado.addEventListener('click', (e)=>{
      control = 0;
      limpiarformdialog();
      document.querySelector('#modalEmpleado').textContent = "Crear empleado";
      document.querySelector('#btnEditarCrearEmpleado').value = "Crear";
      dialogo.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    });

     //////////////////// Cargar imagen como preview  //////////////////////
    btncustomUpImage.addEventListener('click', ()=>btnupImage.click());
    btnupImage.addEventListener('change', function(){
      const file = this.files[0];
      if(file){
        const reader = new FileReader();
        reader.onload = function(){
          const resrult = reader.result;
          imginputfile.src = resrult;
        } 
        reader.readAsDataURL(file);
      }
    });


    document.querySelector('#tablaempleados').addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      if(e.target.classList.contains("editarEmpleado")||e.target.parentElement.classList.contains("editarEmpleado"))editarEmpleado(e);
      if(e.target.classList.contains("empleadoSkills")||e.target.parentElement.classList.contains("empleadoSkills"))empleadoSkills(e);
      if(e.target.classList.contains("eliminarEmpleado")||e.target.parentElement.classList.contains("eliminarEmpleado"))eliminarEmpleado(e);
    });

    //////////////////// ventana modal al Actualizar/Editar empleado  //////////////////////
    function editarEmpleado(e){
      let idempleado = e.target.parentElement.id;
      if(e.target.tagName === 'I')idempleado = e.target.parentElement.parentElement.id;
      control = 1;
      document.querySelector('#modalEmpleado').textContent = "Actualizar Empleado";
      document.querySelector('#btnEditarCrearEmpleado').value = "Actualizar";
      unempleado = empleadosapi.find(x => x.id==idempleado); //me trae al empleado seleccionado
      imginputfile.src = "/build/img/"+unempleado.img;
      inputNombre.value = unempleado.nombre;
      inputApellido.value = unempleado.apellido;
      inputMovil.value = unempleado.movil;
      inputEmail.value = unempleado.email;
      inputDepartamento.value = unempleado.departamento;
      inputCiudad.value = unempleado.ciudad;
      inputDireccion.value = unempleado.direccion;
      $('#perfil').val(unempleado.perfil);
      indiceFila = tablaempleados.row(e.target.closest('tr')).index();
      dialogo.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    ////////////////////  Actualizar/Editar empleado  //////////////////////
    document.querySelector('#formCrearUpdateEmpleado').addEventListener('submit', e=>{
      if(control){
        e.preventDefault();
        var imgFile=unempleado.img, info = tablaempleados.page.info();
        if(!imginputfile.src.includes(unempleado.img)){ //cambio de imagen
          imgFile = e.target.elements["upImage"].files[0]; //obtengo el archivo
          if(imgFile){
            if(imgFile.type!=="image/png"&&imgFile.type!=="image/jpeg"){
              msjAlert('error', 'No es un formato valido para la foto', document.querySelector('#divmsjalerta1'));
              return;
            }
            if(imgFile.size>550000){ //si es mayor a 550KB
              msjAlert('error', 'La imagen no debe superar los 500KB', document.querySelector('#divmsjalerta1'));
              return;
            }
          }
        }
        
        (async ()=>{ 
          const datos = new FormData();
          datos.append('idempleado', unempleado.id);
          datos.append('img', imgFile); //en el backend no se lee con $_POST, se lee con $_FILES
          datos.append('nombre', inputNombre.value);
          datos.append('apellido', inputApellido.value);
          datos.append('movil', inputMovil.value);
          datos.append('email', inputEmail.value);
          datos.append('departamento', inputDepartamento.value);
          datos.append('ciudad', inputCiudad.value);
          datos.append('direccion', inputDireccion.value);
          datos.append('perfil', inputPerfil.value);
          try {
              const url = "/admin/api/actualizarEmpleado";
              const respuesta = await fetch(url, {method: 'POST', body: datos}); 
              const resultado = await respuesta.json();  
              if(resultado.exito !== undefined){
                msjalertToast('success', '¡Éxito!', resultado.exito[0]);
                /// actualizar el arregle del empleado ///
                //
                ///////// cambiar la fila completa, su contenido //////////
                const datosActuales = tablaempleados.row(indiceFila+=info.start).data();
                /*NOMBRE*/datosActuales[1] = inputNombre.value +' '+inputApellido.value;
                /*img*/datosActuales[2] = `<div class="text-center"><img style="width: 40px;" src="/build/img/${resultado.rutaimg[0]}" alt=""></div>`;
                /*MOVIL*/datosActuales[3] = inputMovil.value;
                /*EMAIL*/datosActuales[4] = inputEmail.value;
                /*CEDULA*/datosActuales[5] = ' ';
                /*PERFIL*/datosActuales[6] = inputPerfil.value==1?'Empleado':inputPerfil.value==2?'Admin':'Propietario';
                
                tablaempleados.row(indiceFila).data(datosActuales).draw();
                tablaempleados.page(info.page).draw('page'); //me mantiene la pagina actual
              }else{
                msjalertToast('error', '¡Error!', resultado.exito[0]);
              }
          } catch (error) {
              console.log(error);
          }
        })();//cierre de async()
      } //fin if(control)
    });


    ////////////////////  Actualizar skills del empleado  //////////////////////
    function empleadoSkills(e){
      let idempleado = e.target.parentElement.id;
      if(e.target.tagName === 'I')idempleado = e.target.parentElement.parentElement.id;
      document.querySelectorAll('.inputskills input[type="checkbox"]').forEach(checkbox=>{checkbox.checked=false}); //limpia los checkbox
      unempleado = empleadosapi.find(x => x.id==idempleado);
      var{idservicios} = unempleado; //servicios es el arreglo con solo los skills
      idservicios.forEach(s =>{
        const inputskill = document.querySelector(`input[data-skillid="${s.idservicio}"]`);
        inputskill.checked = true;
      });
      dialogoSkills.showModal();
      document.addEventListener("click", cerrarDialogoExterno);
    }

    document.querySelector('#formSkills').addEventListener('submit', e=>{
      e.preventDefault();
      var arrayservicios = [];
      document.querySelectorAll('.inputskills input[type="checkbox"]').forEach(x=>{if(x.checked)arrayservicios = [...arrayservicios, x.value];});
      (async ()=>{
        const datos = new FormData();
        datos.append('idempleado', unempleado.id);
        datos.append('idservicio', arrayservicios);
        try {
            const url = "/admin/api/actualizarSkillsEmpleado";
            const respuesta = await fetch(url, {method: 'POST', body: datos}); 
            const resultado = await respuesta.json();
            if(resultado.exito !== undefined){
              msjalertToast('success', '¡Éxito!', resultado.exito[0]);
              /// actualizar el arregle de los skills "empleadosapi" ///
              empleadosapi.forEach(elemento => {
                if(elemento.id == unempleado.id)
                  for(a=0; a<arrayservicios.length; a++)
                    elemento.idservicios = [...elemento.idservicios, {idempleado: unempleado.id, idservicio: arrayservicios[a]}];
              });

            }else{
              msjalertToast('error', '¡Error!', resultado.exito[0]);
            }
        } catch (error) {
            console.log(error);
        }
      })();
    });


    ////////////////////  Eliminar el empleado  //////////////////////
    function eliminarEmpleado(e){
      let idempleado = e.target.parentElement.id, info = tablaempleados.page.info();
      if(e.target.tagName === 'I')idempleado = e.target.parentElement.parentElement.id;
      indiceFila = tablaempleados.row(e.target.closest('tr')).index();
      Swal.fire({
          customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
          icon: 'question',
          title: 'Desea eliminar el empleado?',
          text: "El empleado sera eliminado definitivamente.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
      }).then((result) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idempleado);
                  try {
                      const url = "/admin/api/eliminarEmpleado";
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        Swal.fire(resultado.exito[0], '', 'success')
                        tablaempleados.row(indiceFila+info.start).remove().draw(); 
                        tablaempleados.page(info.page).draw('page'); 
                      }else{
                          Swal.fire(resultado.error[0], '', 'error')
                      }
                  } catch (error) {
                      console.log(error);
                  }
              })();//cierre de async()
          }
      });
    }


    function cerrarDialogoExterno(event) {
      if (event.target === dialogo || event.target === dialogoSkills || event.target.value === 'cancelar' || event.target.value === 'Actualizar') {
          dialogo.close();
          dialogoSkills.close();
          document.removeEventListener("click", cerrarDialogoExterno);
      }
    }

    function limpiarformdialog(){
      imginputfile.src = ' ';
      document.querySelector('#formCrearUpdateEmpleado').reset();
    }

  }
})();