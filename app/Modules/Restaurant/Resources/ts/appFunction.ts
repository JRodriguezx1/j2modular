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


export function msjalertToast(icono:string, tipo:string, msj:string){
    Swal.fire({
        icon: icono,
        title: tipo,
        text: msj,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
        showClass: { popup: 'animate__animated animate__fadeInRight animate__faster' },
        hideClass: { popup: 'animate__animated animate__fadeOutRight animate__faster' },
        customClass: {
            popup: `j2-toast j2-toast--${icono}`,
            icon: 'j2-toast__icon',
            title: 'j2-toast__title',
            htmlContainer: 'j2-toast__text',
            timerProgressBar: 'j2-toast__progress'
        }
    });
}


let ventaCarritoToastTimer:number|undefined;
export function mostrarFeedbackCarrito(nombre:string, cantidadProducto:number): void{
    if(window.innerWidth >= 992)return;
    const ventaCarritoToast = document.querySelector('#ventaCarritoToast') as HTMLElement;
    const ventaCarritoToastTitle = document.querySelector('#ventaCarritoToastTitle') as HTMLElement;
    const ventaCarritoToastMeta = document.querySelector('#ventaCarritoToastMeta') as HTMLElement;
    ventaCarritoToastTitle.textContent = `${nombre} agregado`;
    ventaCarritoToastMeta.textContent = `Cantidad: ${cantidadProducto}`;
    ventaCarritoToast.classList.add('is-visible');
    if(ventaCarritoToastTimer)window.clearTimeout(ventaCarritoToastTimer);
    ventaCarritoToastTimer = window.setTimeout(()=>ventaCarritoToast.classList.remove('is-visible'), 1500);
}


export function getDgv(nit: number): number {
    const multiplicadores = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
    const digitos = nit.toString().trim().split('').map(Number);
    let suma = 0;
    digitos.forEach((digito, indice) => {
        const posicionMultiplicador = digitos.length - indice;
        suma += digito * multiplicadores[posicionMultiplicador - 1];
    }); 
    const modulo = suma%11;
    return modulo > 1 ?(11 - modulo):modulo;
}

