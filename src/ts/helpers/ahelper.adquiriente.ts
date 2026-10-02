(()=>{
  if(!document.querySelector('.ventas')&&!document.querySelector('.modorapido'))return;

  const POS = (window as any).POS;

  interface getAdquirienteDian {
    ReceiverEmail:string, 
    ReceiverName:string, 
    StatusCode:string
  }

  interface adquirientes {
    id?:string,
    type_document_identification_id:string,
    identification_number:string,
    business_name:string,
    email:string,
    address:string,
    municipality_id?:string,
    department_id?:string,
    ciudad_nombre:string
    type_organization_id:string,
    type_regime_id:string,
    phone:string
  }

  type municipalities = {
        id:string,
        department_id:string,
        name:string,
        code:string,
  };
  let cities:municipalities[]=[];

  const facturarA = document.querySelector('#facturarA') as HTMLButtonElement;
  const miDialogoFacturarA = document.querySelector('#miDialogoFacturarA') as any;
  const formFacturarA = document.querySelector('#formFacturarA') as HTMLFormElement;
  const documentinput = document.querySelector('#identification_number') as HTMLInputElement;
  const businessNameInput = document.querySelector('#business_name') as HTMLInputElement;
  const btnBuscarAdquiriente = document.querySelector('#btnBuscarAdquiriente') as HTMLButtonElement;
  const selectDepartments = document.querySelector('#department_id') as HTMLSelectElement;
  const selectdCities = document.querySelector('#municipality_id') as HTMLSelectElement;
  let customers:adquirientes[] = [];
  let customersfiltrado:adquirientes|undefined, token:string|undefined;


  (async ()=>{
    const url = `/admin/api/filterAdquirientes`; 
    const respuesta = await fetch(url);
    const resultado = await respuesta.json();
    customers = resultado;
    //formatearponentes(resultado);
  })();

  //consultar token
  (async ()=>{
    const url = `/admin/api/getCompaniesAll`; 
    const respuesta = await fetch(url);
    const resultado:{id:string, identification_number:string, business_name:string, idsoftware:string, token:string, estado:string}[] = await respuesta.json();
    token = resultado.find(x=>x.estado==='1')?.token;
  })();

  btnBuscarAdquiriente.addEventListener('click', (e:Event)=>{
    if(documentinput.value.trim().length > 4 && token!=undefined){
      console.log(documentinput.value.trim());
      GetAcquirerDian(documentinput.value.trim());
    }
  });

  const GetAcquirerDian = async (identificationnumber: string)=>{
    try {
          const url = "https://apidianj2.com/api/ubl2.1/getAcquirer/13/"+identificationnumber;  //va al controlador ventascontrolador
          const respuesta = await fetch(url, {
                                    method: 'GET',
                                    headers: { 
                                      "Accept": "application/json", 
                                      "Content-Type": "application/json",
                                      "Authorization": "Bearer "+token
                                    },
                                  }); 
          const resultado = await respuesta.json();
          const {ReceiverEmail, ReceiverName, StatusCode}:getAdquirienteDian = resultado.data.GetAcquirerResult;
          if(StatusCode === '200'){
            (formFacturarA['business_name'] as HTMLInputElement).value = ReceiverName;
            (formFacturarA['email'] as HTMLInputElement).value = ReceiverEmail;
          }else{
            msjAlert('error', 'No se encontro cliente en base de datos de la Dian', document.querySelector('#divmsjalertanoclienteDian') as HTMLElement);
          }
      } catch (error) {
          console.log(error);
      }
  }



  //buscar adquiriente
  documentinput.addEventListener('input', buscarAquiriente);
  businessNameInput.addEventListener('input', buscarAquiriente);

  function buscarAquiriente(e:Event){
      const input = e.target as HTMLInputElement;
      const busqueda:string = input.value.toLowerCase();

      if(busqueda.length<3){
        customersfiltrado = undefined;
        //mostrarAdquiriente();
        return;
      }

      customersfiltrado = customers.find(adq =>{
        if(input.id==='identification_number'){
          return adq.identification_number.trim() === busqueda.trim();
        }else{
          const nombre = adq.business_name.toLowerCase();
          return nombre.trim() === busqueda.trim();
        }
      });
      
      mostrarAdquiriente();
  }

  function mostrarAdquiriente(){
    if(customersfiltrado != undefined){
      $('#type_document_identification_id').val(customersfiltrado.type_document_identification_id);
      documentinput.value = customersfiltrado.identification_number;
      (document.querySelector('#business_name') as HTMLInputElement).value = customersfiltrado.business_name;
      (document.querySelector('#email') as HTMLInputElement).value = customersfiltrado.email;
      (document.querySelector('#address') as HTMLInputElement).value = customersfiltrado.address;
      if(customersfiltrado.department_id){
        $('#department_id').val(customersfiltrado.department_id);
        imprimirCiudades(customersfiltrado.department_id);
      }
      //$('#municipality_id').val(customersfiltrado.municipality_id);
      $('#type_organization_id').val(customersfiltrado.type_organization_id);
      $('#type_regime_id').val(customersfiltrado.type_regime_id);
      (document.querySelector('#phone') as HTMLInputElement).value = customersfiltrado.phone;
    }else{
      $('#type_document_identification_id').val("");
      //(document.querySelector('#business_name') as HTMLInputElement).value = '';
      (document.querySelector('#email') as HTMLInputElement).value = '';
      (document.querySelector('#address') as HTMLInputElement).value = '';
      $('#department_id').val("");
      $('#municipality_id').val("");
      $('#type_organization_id').val("");
      $('#type_regime_id').val("");
      (document.querySelector('#phone') as HTMLInputElement).value = "";
    }
  }



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
    if(customersfiltrado!=undefined&&customersfiltrado.municipality_id)$('#municipality_id').val(customersfiltrado.municipality_id);
  }


  ///////////////////// evento al btn facturar A /////////////////////
  facturarA.addEventListener('click', (e:Event)=>{
    miDialogoFacturarA.showModal();
    document.addEventListener("click", POS.cerrarDialogoExterno);
  });

   
  formFacturarA.addEventListener('submit', (e:Event)=>{
    e.preventDefault();
    const data = new FormData(formFacturarA);
    const datosAdquiriente: Record<string, FormDataEntryValue> = Object.fromEntries(data.entries());
    miDialogoFacturarA.close();
    document.removeEventListener("click", POS.cerrarDialogoExterno);

    const identification = datosAdquiriente.identification_number as string;
    const requiereEmail = identification && identification!='222222222222';
    // Validar email solo si identification_number es distinto de 222222222222 y no vacío
    if (requiereEmail && !validarEmail(datosAdquiriente.email as string)) {
        Swal.fire("Correo incorrecto", "Debe ingresar un email válido o enviar a consumidor final o generico: 222222222222", "error");
        return; // detiene el proceso
    }

    if(identification && identification!='222222222222'){
      const dv = getDgv(Number(identification));
      datosAdquiriente.dv = dv.toString();
    }

    console.log(datosAdquiriente);

    POS.gestionarAdquiriente.datosAdquiriente = datosAdquiriente; //guarda en el objeto global
    //guardar adquiriente en DB.
    guardarAdquiriente(datosAdquiriente);
  });

  function validarEmail(correo: string): boolean {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(correo);
  }


  async function guardarAdquiriente(datosAdquiriente: Record<string, FormDataEntryValue>){
    try {
          const url = "/admin/api/guardarAdquiriente";  //va al controlador ventascontrolador
          const respuesta = await fetch(url, {
                                    method: 'POST',
                                    headers: { "Accept": "application/json", "Content-Type": "application/json" },
                                    body: JSON.stringify(datosAdquiriente)
                                  }); 
          const resultado = await respuesta.json();
          //añadir o actualizar al arreglo customers
          if(resultado.tipo == "crear"){
            customers = [...customers, resultado.obj];
          }else{
            /// actualizar el arregle del adquiriente o customers ///
            customers.forEach(a=>{if(a.identification_number == resultado.obj.identification_number)a = Object.assign(a, resultado.obj);});
          }
          datosAdquiriente.id = resultado.obj.id;
      } catch (error) {
          console.log(error);
      }
  }
  

  const gestionarAdquiriente = {  //objeto a exportar
    miDialogoFacturarA,
    datosAdquiriente: {} //inicializar 
  };

  POS.gestionarAdquiriente = gestionarAdquiriente;

})();