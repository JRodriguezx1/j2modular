import type {ReservationsPageData} from './types';
import {initReservationDrawer} from './reservationDrawer';
import {initCustomerSearch} from './customerSearch';
import {initReservationForm} from './reservationForm';
import {initReservationDetail} from './reservationDetail';

export function initReservations(): void{
    console.log('Restaurant → Reservations cargado');
    const data = getPageData();
    initReservationDrawer(data); //inicializa el drawer de reservas
    initCustomerSearch();  //inicializa el buscador de clientes en el formulario de reservas
    initReservationForm(); //inicializa el formulario de reservas
    initReservationDetail()
}

function getPageData(): ReservationsPageData {
    const element = document.querySelector<HTMLScriptElement>('#restaurantReservationsData'); //data del script de reservations/index.php
    if(!element)return {zones: []};

    try{
        return JSON.parse(element.textContent ?? '{}') as ReservationsPageData;
    } catch(error){
        console.error('Restaurant → Error leyendo datos de reservas', error);
        return { zones: [] };
    }
}