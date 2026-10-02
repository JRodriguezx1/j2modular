const page = document.body.dataset.page;

async function bootstrap(): Promise<void> {

    switch (page) {

        case 'tables': {
            const module = await import('./tables');
            module.initTables();
            break;
        }

        /*case 'kitchen': {

            const module = await import('./kitchen');
            module.initKitchen();

            break;
        }*/

        case 'reservations': {
            const module = await import('./reservations');
            module.initReservations();
            break;
        }

    }

}

bootstrap();