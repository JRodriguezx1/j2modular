import {initActiveOccupation} from './activeOccupation';


export function initTables(): void {

    console.log('Restaurant → Tables cargado x');
    initActiveOccupation();
    /*const tables = document.querySelectorAll('.restaurant-table');

    tables.forEach(table => {

        table.addEventListener('click', () => {
            console.log('Mesa seleccionada');
        });

    });*/

}