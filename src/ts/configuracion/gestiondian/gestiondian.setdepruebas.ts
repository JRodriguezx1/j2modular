(()=>{
    if(!document.querySelector('.gestionDian'))return;
    const POS = (window as any).POS;

    const miDialogosetpruebas = document.querySelector('#miDialogosetpruebas') as any;
    const selectSetCompañia = document.querySelector('#selectSetCompañia') as HTMLSelectElement;

    ///////   ENVIAR SET DE PRUEBAS    ///////
    document.querySelector('#formSetPruebas')?.addEventListener('submit', async(e:Event)=>{
      e.preventDefault();
      const id = selectSetCompañia.options[selectSetCompañia.selectedIndex]?.value;
      const test = document.querySelector('#idsetpruebas') as HTMLInputElement;

      const companiesAll = POS.companiesAll as companiesDian[];

      const oneC = companiesAll.find(x=>x.id == id)!;
      const token = oneC.token;

      const date = new Date().toLocaleDateString('sv-SE');
      const number = 992500000 + (Math.floor(Math.random()*500000)+1);  //rango de 1 a 500000

      const factura = {
        prefix: "SETP",
        number, // dinámico
        type_document_id: "1",
        date,   // dinámico
        time: "00:00:01",
        resolution_number: "18760000001",
        sendmail: false,
        notes: "Factura Electrónica de pruebas Auto",
        payment_form: {
          payment_form_id: "1",
          payment_method_id: "10",
          payment_due_date: date,
          duration_measure: "0"
        },
        customer: {
          identification_number: "222222222222",
          name: "Consumidor Final",
          phone: null,
          address: null,
          type_document_identification_id: 3,
          type_organization_id: 1,
          municipality_id: null
        },
        invoice_lines: [
          {
            unit_measure_id: "70",
            invoiced_quantity: "1.00",
            line_extension_amount: "2000.00",
            free_of_charge_indicator: false,
            tax_totals: [
              {
                tax_id: 1,
                tax_amount: "0.00",
                taxable_amount: "2000.00",
                percent: "0"
              }
            ],
            description: "Producto Prueba",
            code: "155",
            type_item_identification_id: "4",
            price_amount: "2000.00",
            base_quantity: "1"
          }
        ],
        legal_monetary_totals: {
          line_extension_amount: "2000",
          tax_exclusive_amount: "2000",
          tax_inclusive_amount: "2000",
          allowance_total_amount: "0",
          charge_total_amount: "0",
          payable_amount: "2000"
        },
        tax_totals: [
          {
            tax_id: 1,
            tax_amount: "0.00",
            percent: "0",
            taxable_amount: "2000.00"
          }
        ]
      };

      try {
        const url = "https://apidianj2.com/api/ubl2.1/invoice/"+test.value; //llamado a la API REST Dianlaravel
        const respuesta = await fetch(url, {
                                              method: 'POST',
                                              headers: {
                                                "Accept": "application/json",
                                                "Content-Type": "application/json",
                                                "Authorization": "Bearer "+token
                                              },
                                              body: JSON.stringify(factura)
                                            });
        const resultado = await respuesta.json();
        modoproduccion(token);
        miDialogosetpruebas.close();
        document.removeEventListener("click", POS.cerrarDialogoExterno);
        return resultado.success;
      } catch (error) {
        console.log(error);
        return false;
      }
    });

    ///////    ENVIOREMNET MODO PRUEBAS - PRODUCCION    ///////
    async function modoproduccion(token?:string){
      try {
        const url = "https://apidianj2.com/api/ubl2.1/config/environment"; //llamado a la API REST Dian-laravel
        const respuesta = await fetch(url, {
                                              method: 'PUT',
                                              headers: {
                                                "Accept": "application/json",
                                                "Content-Type": "application/json",
                                                "Authorization": "Bearer "+token
                                              },
                                              body: JSON.stringify({"type_environment_id": 1, "payroll_type_environment_id": 2})
                                            });
        const resultado = await respuesta.json();
        msjalertToast('success', '¡Éxito!', 'Modo de producción activado correctamente.');
        return resultado.success;
      } catch (error) {
        console.log(error);
        return false;
      }

    }

    const gestionarSetPruebas = {  //objeto a exportar
        miDialogosetpruebas,
        selectSetCompañia,
    };

  POS.gestionarSetPruebas = gestionarSetPruebas;

})();