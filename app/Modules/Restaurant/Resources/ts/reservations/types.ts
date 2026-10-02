export interface RestaurantZone {
    id: number;
    name: string;
    active: boolean;
}

export interface AvailableTable {
    id: number;
    resourceId: number;
    name: string;
    capacity: number;
    zoneId: number | null;
    shape: string | null;
}

export interface AvailableTablesResponse {
    success: boolean;
    tables?: AvailableTable[];
    message?: string;
}

export interface ReservationsPageData {
    zones: RestaurantZone[];
}

export interface Customer {
    id: number;
    fullName: string;
    identification: string | null;
    phone: string | null;
    email: string | null;
}

export interface CustomersResponse {
    success: boolean;
    customers?: Customer[];
    message?: string;
}