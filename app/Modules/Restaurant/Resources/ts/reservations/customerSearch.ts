import type {Customer, CustomersResponse} from './types';

let controller: AbortController | null = null;


export async function searchCustomers(term: string): Promise<Customer[]> {
    controller?.abort();
    const query = term.trim();
    if(query.length < 2)return [];
    controller = new AbortController();
    const params = new URLSearchParams({q: query});

    const response = await fetch(`/restaurant/api/customers/search?${params}`, {
            method: 'GET',
            signal: controller.signal,
            headers: {Accept: 'application/json'}
        }
    );

    const result = await response.json() as CustomersResponse;
    if(!response.ok)
        throw new Error(result.message ?? 'No fue posible buscar clientes.');
    return result.customers ?? [];
}


export function initCustomerSearch(): void {
    const input = document.querySelector<HTMLInputElement>('#customerSearchInput');
    const results = document.querySelector<HTMLElement>('#customerSearchResults');
    const loader = document.querySelector<HTMLElement>('#customerSearchLoader');
    const searchContainer = document.querySelector<HTMLElement>('#customerSearch');
    const selectedContainer = document.querySelector<HTMLElement>('#selectedCustomer');
    const clientIdInput = document.querySelector<HTMLInputElement>('#reservationClientId');
    const selectedName = document.querySelector<HTMLElement>('#selectedCustomerName');
    const selectedInfo = document.querySelector<HTMLElement>('#selectedCustomerInfo');
    const selectedInitials = document.querySelector<HTMLElement>('#selectedCustomerInitials');
    const btnChange = document.querySelector<HTMLButtonElement>('#btnChangeCustomer');

    if(!input || !results || !loader || !searchContainer || !selectedContainer || !clientIdInput || !selectedName || !selectedInfo || !selectedInitials)
        return;
    
    let debounceTimer: number | null = null;

    input.addEventListener('input', () => {
        if(debounceTimer !== null)window.clearTimeout(debounceTimer);
        const term = input.value.trim();
        if(term.length < 2){
            hideResults();
            loader.classList.add('hidden');
            return;
        }
        debounceTimer = window.setTimeout(() => { performSearch(term); }, 300);
    });


    const performSearch = async(term: string):Promise<void> => {
        loader.classList.remove('hidden');

        try {
            const customers = await searchCustomers(term);
            /*
            * El usuario pudo seguir escribiendo
            * mientras llegaba la respuesta.
            */
            if(input.value.trim() !== term)return;
            renderResults(customers);


        } catch (error) {
            if(error instanceof DOMException && error.name === 'AbortError')return;
            renderError(error instanceof Error ? error.message : 'No fue posible buscar clientes.');
        }finally{
            loader.classList.add('hidden');
        }
    }


    const renderResults = (customers: Customer[]):void => {
        results.innerHTML = '';
        results.classList.remove('hidden');

        if(customers.length === 0){
            const empty = document.createElement('div');
            empty.className = 'px-4 py-4 text-center';

            empty.innerHTML = `
                <span class="material-symbols-outlined text-[22px] text-slate-300">
                    person_search
                </span>
                <p class="mt-1 text-xs text-slate-500">No encontramos clientes.</p>`;

            results.append(empty);
            return;
        }


        for(const customer of customers){
            const button = createCustomerResult(customer);
            results.append(button);
        }
    }


    function createCustomerResult(customer: Customer): HTMLButtonElement {
        const button = document.createElement('button');
        button.type = 'button';

        button.className =
            'flex w-full items-center gap-3 ' +
            'border-b border-slate-100 ' +
            'px-3 py-2.5 text-left ' +
            'transition last:border-b-0 ' +
            'hover:bg-slate-50';

        const avatar = document.createElement('div');

        avatar.className =
            'flex h-8 w-8 shrink-0 ' +
            'items-center justify-center ' +
            'rounded-full bg-slate-100 ' +
            'text-[10px] font-bold text-slate-600';

        avatar.textContent = getInitials(customer.fullName);
        const content = document.createElement('div');
        content.className = 'min-w-0 flex-1';
        const name = document.createElement('p');
        name.className = 'truncate text-xs font-semibold ' + 'text-slate-700';
        name.textContent = customer.fullName;
        const info = document.createElement('p');
        info.className = 'mt-0.5 truncate text-[10px] ' + 'text-slate-400';
        info.textContent = buildCustomerInfo(customer);
        content.append(name, info);
        button.append(avatar, content);
        button.addEventListener('click', () => { selectCustomer(customer); });
        return button;
    }


    const selectCustomer = (customer: Customer):void => {
        clientIdInput.value = String(customer.id);
        selectedName.textContent = customer.fullName;
        selectedInfo.textContent = buildCustomerInfo(customer);
        selectedInitials.textContent = getInitials(customer.fullName);
        searchContainer.classList.add('hidden');
        selectedContainer.classList.remove('hidden');
        input.value = '';
        hideResults();
    }


    const clearCustomer =():void => {
        clientIdInput.value = '';
        selectedName.textContent = '';
        selectedInfo.textContent = '';
        selectedInitials.textContent = '';
        selectedContainer.classList.add('hidden');
        searchContainer.classList.remove('hidden');
        input.value = '';
        hideResults();
        requestAnimationFrame( () => {input.focus();});
    }

    btnChange?.addEventListener('click',  clearCustomer);


    const hideResults = (): void => {
        results.classList.add('hidden');
        results.innerHTML = '';
    }


    function getInitials(fullName: string): string {
        const parts = fullName .trim().split(/\s+/).filter(Boolean);

        if(parts.length === 0)return '?';
        
        if(parts.length === 1)
            return parts[0] .substring(0, 2) .toUpperCase();

        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }


    function buildCustomerInfo(customer: Customer): string {
        const parts: string[] = [];
        if(customer.identification)
            parts.push(customer.identification);
        if(customer.phone)
            parts.push(customer.phone);
        return parts.join(' · ');
    }


    const renderError = (message: string):void => {
        results.innerHTML = '';
        results.classList.remove('hidden');
        const error = document.createElement('div');
        error.className = 'px-4 py-3 text-center ' + 'text-xs text-rose-500';
        error.textContent = message;
        results.append(error);
    }

}