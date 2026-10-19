import {closeActiveOccupation, initActiveOccupation, openActiveOccupationDetails} from './activeOccupation';
import {closeOrderWorkspace, initOrderWorkspace, openOrderWorkspace} from './orderWorkspace';

export function initTables(): void {
    initActiveOccupation();
    initOrderWorkspace();

    document.addEventListener('click', event => {
        const target = event.target as HTMLElement;
        const action = target.closest<HTMLElement>('[data-open-active-detail], [data-open-order-workspace]');
        const card = action?.closest<HTMLElement>('[data-table-id]');  //data-table-id del articule de la mesa

        if(!action || !card)return;

        const resourceId = Number(card.dataset.resourceId);
        if(!resourceId)return;

        if(action.hasAttribute('data-open-active-detail')){
            closeOrderWorkspace();
            void openActiveOccupationDetails(resourceId); //abrir panel de detalle de mesa y cuenta
        }else{
            closeActiveOccupation();
            void openOrderWorkspace(resourceId, card.dataset.tableName ?? 'Mesa'); //abrir panel de operaciones para la mesa
            //consultar la cuenta, resumen de la cuenta, productos registrados etc.
        }
    });
}
