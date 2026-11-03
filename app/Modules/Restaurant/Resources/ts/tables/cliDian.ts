import { adquiriente } from "./eventos/customerDianEvent";

export function createCliDian(){
    let consumidorFinal: adquiriente = crearConsumidorfinal();

    function addCliDian(cliDian:adquiriente){

        if(consumidorFinal)Object.assign(consumidorFinal, cliDian);
    }

    // Devolvemos copias para que la vista no modifique el carrito accidentalmente.
    function getCliDian(): adquiriente{
        return {...consumidorFinal};
    }

    function clear(): void{
        Object.assign(consumidorFinal, crearConsumidorfinal());
    }

    return {addCliDian, getCliDian, clear};

}

function crearConsumidorfinal(): adquiriente{
    return {
                id: '1',
                type_document_identification_id: '3',
                identification_number: '222222222222',
                business_name: 'Consumidor final',
                email: null,
                address: null,
                municipality_id: null,
                department_id: null,
                ciudad_nombre: null,
                type_organization_id: '2',
                type_regime_id: '2',
                phone: null
            };
}