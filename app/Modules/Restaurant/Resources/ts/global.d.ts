/*interface SweetAlertOptions {
    icon?:
        | 'success'
        | 'error'
        | 'warning'
        | 'info'
        | 'question';

    title?: string;
    text?: string;
    confirmButtonText?: string;
    showConfirmButton?: boolean;
    timer?: number;
}*/

interface Cliente {
    id: string;
    nombre: string;
    apellido: string;
    tipodocumento: string;
    identificacion: string;
    telefono: string;
    email: string;
    fecha_nacimiento: string | null;
    total_compras: string | null;
    ultima_compra: string | null;
    totaldebe: string | null;
    limitecredito: string | null;
    data1: string | null;
    created_at: string;
}

declare const Swal:any;
declare const select2:any;
declare const clientesDB: Cliente[];