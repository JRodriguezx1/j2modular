
export function createCli(){
    let cliente: Cliente = createEmptyCliente();

    // undefined: todavía no conocemos el cliente confirmado de la factura.
    let savedClientId: number|null|undefined = undefined;
    let edited = false;

    function parseClientId(rawId:string): number|null {
        if(rawId === '')return null;
        const id = Number(rawId);
        if(!Number.isSafeInteger(id) || id <= 0)throw new Error('El cliente seleccionado no es válido.');
        return id;
    }

    // Cargar la presentación inicial no es una edición del usuario.
    // Por ahora este cliente puede venir de la ocupación.
    function loadCli(cli: Cliente|null): void{
        if(cli)parseClientId(cli.id); //parseClientId se usa para validar el ID del cliente y produce una excepción si no es válido.
        Object.assign(cliente, createEmptyCliente(), cli ?? {});
        savedClientId = undefined;
        edited = false;
    }

    function addCli(id:string, cli:Cliente){
        parseClientId(cli.id);
        if(cliente)Object.assign(cliente, cli);
        edited = true;
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
        edited = true;
    }

    function getClientId(): number|null {
        return parseClientId(cliente.id);
    }

    // undefined: conservar; null: retirar; número: asignar.
    function getClientChange(): number|null|undefined{
        if(!edited)return undefined;
        const currentId = getClientId();
        // Si volvió al cliente confirmado, no hay cambio pendiente.
        return currentId === savedClientId ? undefined : currentId;
    }

    // Llamar únicamente después de confirmar el guardado.
    function confirmSavedClient(clientId: number|null): void{
        savedClientId = clientId;
        // Si hubiera una selección posterior al envío, conservarla pendiente.
        edited = getClientId() !== clientId;
    }

    return {addCli, getCli, clear, loadCli, getClientId, getClientChange, confirmSavedClient};

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
