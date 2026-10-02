type AppDateRangePickerOptions = Record<string, any>;

/**
 * Inicializa Date Range Picker con la configuracion y el tema visual global.
 * Las opciones recibidas sobrescriben los valores predeterminados.
 */
function inicializarDateRangePicker(input:any, options:AppDateRangePickerOptions = {}):void{
    if(!input?.length)return;

    const defaultLocale = {
        format: 'M/DD hh:mm A',
        applyLabel: 'Aplicar',
        cancelLabel: 'Cancelar',
        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        monthNames: [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ]
    };

    input.daterangepicker({
        timePicker: true,
        startDate: moment().set({ hour: 0, minute: 0, second: 1 }),
        endDate: moment().set({ hour: 23, minute: 59, second: 59 }),
        ...options,
        locale: {
            ...defaultLocale,
            ...(options.locale ?? {})
        }
    });

    input.each(function(this:HTMLElement){
        const picker = ($(this) as any).data('daterangepicker');
        picker?.container?.addClass('app-daterangepicker');
    });
}
