import type { TableState } from './orderApi';

// Colores que ya utiliza la vista PHP.
const styles: Record<string, {card:string, badge:string, dot:string}> = {
    disponible: {
        card: 'border-slate-200 bg-white',
        badge: 'bg-emerald-100 text-emerald-700',
        dot: 'bg-emerald-500'
    },
    ocupada: {
        card: 'border-amber-200 bg-amber-50/40',
        badge: 'bg-amber-100 text-amber-700',
        dot: 'bg-amber-500'
    },
    reservada: {
        card: 'border-indigo-200 bg-indigo-50/40',
        badge: 'bg-indigo-100 text-indigo-700',
        dot: 'bg-slate-400'
    },
    limpieza: {
        card: 'border-sky-200 bg-sky-50/40',
        badge: 'bg-sky-100 text-sky-700',
        dot: 'bg-slate-400'
    },
    mantenimiento: {
        card: 'border-slate-300 bg-slate-100',
        badge: 'bg-slate-200 text-slate-600',
        dot: 'bg-slate-400'
    },
    fuera_servicio: {
        card: 'border-slate-300 bg-slate-100',
        badge: 'bg-slate-200 text-slate-600',
        dot: 'bg-slate-400'
    }
};

export function updateTableCards(tables: TableState[]): void{
    for(const table of tables){
        // resourceId corresponde a recursos.id, no a mesas.id.
        const card = document.querySelector<HTMLElement>(`article[data-table-id][data-resource-id="${table.resourceId}"]`);
        // Solo actualizar las tarjetas presentes en esta pantalla.
        if(!card)continue;
        // La vista muestra únicamente recursos activos.
        if(!table.active){
            card.remove();
            continue;
        }

        const badge = card.querySelector<HTMLElement>('[data-open-active-detail]');
        const dot = card.querySelector<HTMLElement>('[data-estado-visual]');
        const content = card.querySelector<HTMLElement>('[data-contenidoBtn]');

        if(!badge || !dot || !content)
            throw new Error('La tarjeta de una mesa está incompleta.');

        const status = table.operationalStatus;
        const style = styles[status] ?? styles.fuera_servicio;

        card.dataset.status = status;
        card.dataset.reservationId = String(table.reservationId ?? '');
        card.dataset.occupationId = String(table.occupationId ?? '');

        // Retirar únicamente los colores anteriores. Conservar las demás clases, por ejemplo las de filtros.
        for(const previous of Object.values(styles)){
            card.classList.remove(...previous.card.split(' '));
            badge.classList.remove(...previous.badge.split(' '));
            dot.classList.remove(previous.dot);
        }

        card.classList.add(...style.card.split(' '));
        badge.classList.add(...style.badge.split(' '));
        dot.classList.add(style.dot);

        // Conservar el comportamiento visual de la vista PHP.
        for(const name of ['transition', 'duration-200', 'hover:-translate-y-0.5', 'hover:shadow-md'])
            card.classList.toggle(name, status === 'ocupada');

        badge.textContent = status.replace(/_/g, ' ');
        // Quitar el detalle anterior, como "En uso". Conservamos el botón existente y sus atributos.
        content.replaceChildren(badge);

        let detail = '';

        if(status === 'ocupada'){
            detail = 'En uso';
        } else if(status === 'reservada'){
            // La fecha viene del servidor como YYYY-MM-DD HH:mm:ss.
            const time = table.reservationStart?.slice(11, 16);
            detail = time ? `Reserva ${time}` : 'Reservada';
        } else if(status === 'limpieza'){
            detail = 'Pendiente de limpieza';
        } else if(status === 'mantenimiento'){
            detail = 'En mantenimiento';
        } else if(status === 'fuera_servicio'){
            detail = 'Fuera de servicio';
        }

        if(detail){
            const label = document.createElement('span');
            label.className = 'text-base font-medium text-slate-500';
            label.textContent = detail;
            content.append(label);
        }
    }

    updateTableCounters();
}

function updateTableCounters(): void {
    // Contar las tarjetas existentes, incluso si un filtro las oculta.
    const cards = Array.from(document.querySelectorAll<HTMLElement>('article[data-table-id][data-resource-id]'));
    const available = cards.filter(card => card.dataset.status === 'disponible').length;
    const occupied = cards.filter(card => card.dataset.status === 'ocupada').length;

    const values = {
        total: `${cards.length} mesas`,
        disponible: `${available} disponibles`,
        ocupada: `${occupied} ocupadas`
    };

    for(const [key, text] of Object.entries(values)){
        const element = document.querySelector<HTMLElement>(`[data-table-count="${key}"]`);
        if(element)element.textContent = text;
    }
    
}