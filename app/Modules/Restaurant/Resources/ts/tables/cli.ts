
export function createCli(){
    let cliente: Cliente = createEmptyCliente();

    function addCli(id:string, cli:Cliente){
        if(cliente)Object.assign(cliente, cli);
    }

    /*function updateCli(id: string, cliente:Cliente): void{
        const index = clientes.findIndex(cliente => cliente.id === id);
        if(index === -1)throw new Error('El cliente no esta en la orden.');
        clientes[index] = cliente;
    }
    
    function removeCli(id: string): void{
        clientes = clientes.filter(cliente => cliente.id !== id);
    }*/

    // Devolvemos copias para que la vista no modifique el carrito accidentalmente.
    function getCli(): Cliente{
        return {...cliente};
    }

    function clear(): void{
        Object.assign(cliente, createEmptyCliente());
    }

    return {addCli, getCli, clear};

}

function createEmptyCliente(): Cliente{
    return {
                id: '',
                nombre: '',
                apellido: '',
                tipodocumento: '',
                identificacion: '',
                telefono: '',
                email: '',
                fecha_nacimiento: null,
                total_compras: null,
                ultima_compra: null,
                totaldebe: null,
                limitecredito: null,
                data1: null,
                direcciones: [],
                created_at: ''
            };
}
