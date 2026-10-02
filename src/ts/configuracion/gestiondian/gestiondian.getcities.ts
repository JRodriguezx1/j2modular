(()=>{
    if(!document.querySelector('.gestionDian'))return;
    const POS = (window as any).POS;

    const selectDepartments = document.querySelector('#department_id') as HTMLSelectElement;
    const selectdCities = document.querySelector('#municipality_id') as HTMLSelectElement;

    type municipalities = {
        id:string,
        department_id:string,
        name:string,
        code:string,
      };
      let cities:municipalities[]=[];

    /////       Obtener municipio segun departamento        ///////
    selectDepartments?.addEventListener('change', (e:Event)=>{
      const x:HTMLOptionElement = (e.target as HTMLOptionElement);
      imprimirCiudades(x.value);
    });

    function imprimirCiudades(x:string){
      (async ()=>{
        try {
          const url = "/admin/api/citiesXdepartments?id="+x; //llamado a la API REST y se trae las cities segun cliente elegido
          const respuesta = await fetch(url); 
          const resultado = await respuesta.json(); 
          if(resultado.error){
            Swal.fire(resultado.error[0], '', 'error')
          }else{
            cities = resultado;
            addCitiesToSelect(cities);
          }
        } catch (error) {
            console.log(error);
        }
      })();
    }
       
    function addCitiesToSelect<T extends {id:string, department_id:string, name:string, code:string}>(addrs: T[]):void{
      while(selectdCities?.firstChild)selectdCities.removeChild(selectdCities?.firstChild);
      addrs.forEach(x =>{
        const option = document.createElement('option');
        option.textContent = x.name;
        option.value = x.id;
        option.dataset.code = x.code;
        option.dataset.department_id = x.department_id;
        selectdCities.appendChild(option);
      });
      
    }

})();