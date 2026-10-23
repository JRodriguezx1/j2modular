
const btnAddCliente = document.querySelector('#addcliente') as HTMLElement;
const miDialogoAddCliente = document.querySelector('#miDialogoAddCliente') as HTMLDialogElement;

//evento al boton de seleccionar cliente
export function registeraddclienteEvents(): void{
    btnAddCliente?.addEventListener('click', (e)=>{
        miDialogoAddCliente.showModal();
        ($('#selectCliente') as any).select2({
            dropdownParent: $('#miDialogoAddCliente'),
            placeholder: "Seleccionar el cliente",
            maximumSelectionLength: 1
        });
        
    });
}

