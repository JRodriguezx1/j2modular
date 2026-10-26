export function escapexHTML(value: string): string{
    return value.replace(/[&<>"']/g, (char) => {
        const entities: Record<string, string> = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        };
        return entities[char];
    });
}

export function escapeCreateDOMHtml(value: string): string {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}

export function setElementText(id: string, value: string): void{
    const element = document.getElementById(id);
    if(element)element.textContent = value;
}

export function cerrarDialogoExterno(event:Event): void{
    const f = event.target;
    //Verificar que sea un evento raton
    if(!(event instanceof MouseEvent))return;
    //Biscar todos los dialogs que esten abiertos en el DOM
    const dialogsAbiertos = document.querySelectorAll<HTMLDialogElement>('dialog[open]');
    //verificar si el clic fue fuera del dialogo
    dialogsAbiertos.forEach( dialog=>{
        const rect = dialog.getBoundingClientRect();
        const clicFuera = event.clientX<rect.left || event.clientX>rect.right || event.clientY<rect.top || event.clientY>rect.bottom;
        if(clicFuera && f === dialog || (f as HTMLButtonElement).textContent.trim() === 'Cancelar' || (f as HTMLInputElement).closest('.salir'))
            dialog.close();
    });
}

export function ajustarAnchoCantidad(input:HTMLInputElement, cantidad:number|string): void{
    const largo = String(cantidad || 0).length;
    input.style.width = `${Math.min(Math.max(largo + 2, 5), 10)}ch`;
  }