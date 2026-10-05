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

export interface CreateReservationData {
    clientId: number;
    numberOfPeople: number;
    startDate: string;
    endDate: string;
    resourceId: number | null;
    observations: string | null;
}

export interface CreateReservationResponse {
    success: boolean;
    reservationId?: number;
    message?: string;
}

////////// interfaz para el detalle de la reserva
export interface ReservationResource {
    id: number;
    name: string;
    capacity: number;
    zoneId: number | null;
    zoneName: string | null;
    shape: string | null;
}

export interface ReservationDetail {
    id: number;
    clientId: number;
    clientName: string;
    numberOfPeople: number;
    startDate: string;
    endDate: string;
    status: string;
    observations: string | null;
    resources: ReservationResource[];
}

export interface ReservationDetailResponse {
    success: boolean;
    reservation?: ReservationDetail;
    message?: string;
}