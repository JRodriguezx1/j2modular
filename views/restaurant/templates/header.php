<?php

    $currentPath = strtok($_SERVER['REQUEST_URI'] ?? '/','?');
    function restaurantNavActive(string $currentPath, string $path): bool{ 
        return str_starts_with($currentPath, $path);
    }

?>

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">

    <!-- Header principal -->
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">

        <!-- Marca -->
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white">
                <span class="material-symbols-outlined">restaurant</span>
            </div>

            <div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl  font-bold tracking-tight text-slate-900">JDOS</span>
                    <span class="hidden text-lg font-medium text-slate-400 sm:inline">Restaurant</span>
                </div>
            </div>
        </div>

        <!-- Información derecha -->
        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Sucursal -->
            <div class="hidden items-center gap-2 rounded-xl bg-slate-50 px-3 py-2 lg:flex">
                <span class="material-symbols-outlined text-3xl text-slate-600">store</span>
                <div>
                    <p class="text-xl font-medium uppercase tracking-wide  text-slate-500 m-0">Sucursal</p>
                    <!-- Temporal -->
                    <p class="text-sm font-semibold text-slate-900 m-0">Principal</p>
                </div>
            </div>
            <!-- Notificaciones -->
            <button
                type="button"
                class="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <!-- Usuario -->
            <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-lg font-semibold text-indigo-700">J</button>
        </div>

    </div>


    <!-- Navegación operacional -->

    <nav class="overflow-x-auto border-t border-slate-100 px-4 py-2 sm:px-6 lg:px-8">
        <div class="flex min-w-max items-center gap-3">
            <!-- MESAS -->
            <a
                href="/restaurant/tables"
                class="flex h-12 min-w-44 items-center gap-3 rounded-xl px-5 text-lg font-semibold transition-all duration-200
                    <?= restaurantNavActive($currentPath, '/restaurant/tables')
                        ? 'bg-gradient-to-r from-indigo-600 to-violet-500 text-white shadow-md shadow-indigo-200'
                        : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'
                    ?>"
            >
                <span class="material-symbols-outlined text-[23px] <?= restaurantNavActive($currentPath, '/restaurant/tables') ? 'text-white' : 'text-indigo-600'?>">
                    table_restaurant
                </span>
                Mesas
            </a>


            <!-- COMANDAS -->
            <a
                href="/restaurant/orders"
                class="flex h-12 min-w-44 items-center gap-3 rounded-xl px-5 text-lg font-semibold transition-all duration-200
                    <?= restaurantNavActive($currentPath, '/restaurant/orders')
                        ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-200'
                        : 'bg-orange-50 text-orange-800 hover:bg-orange-100'
                    ?>"
            >
                <span class="material-symbols-outlined text-[23px] <?= restaurantNavActive($currentPath, '/restaurant/orders') ? 'text-white' : 'text-orange-600'?>">
                    receipt_long
                </span>
                Comandas
            </a>


            <!-- COCINA -->
            <a
                href="/restaurant/kitchen"
                class="flex h-12 min-w-44 items-center gap-3 rounded-xl px-5 text-lg font-semibold transition-all duration-200
                    <?= restaurantNavActive($currentPath, '/restaurant/kitchen')
                        ? 'bg-gradient-to-r from-rose-500 to-red-500 text-white shadow-md shadow-rose-200'
                        : 'bg-rose-50 text-rose-700 hover:bg-rose-100 hover:shadow-sm'
                    ?>"
            >
                <span class="material-symbols-outlined text-[23px] <?= restaurantNavActive($currentPath, '/restaurant/kitchen') ? 'text-white' : 'text-rose-600'?>">
                    skillet
                </span>
                Cocina
            </a>


            <!-- RESERVAS -->
            <a
                href="/restaurant/reservations"
                class="flex h-12 min-w-44 items-center gap-3 rounded-xl px-5 text-lg font-semibold transition-all duration-200
                    <?= restaurantNavActive($currentPath, '/restaurant/reservations')
                        ? 'bg-gradient-to-r from-blue-600 to-sky-500 text-white shadow-md shadow-blue-200'
                        : 'bg-blue-50 text-blue-700 hover:bg-blue-100 hover:shadow-sm'
                    ?>"
            >
                <span class="material-symbols-outlined text-[23px] <?= restaurantNavActive($currentPath, '/restaurant/reservations') ? 'text-white' : 'text-blue-600'?>">
                    calendar_month
                </span>
                Reservas
            </a>


            <!-- SEPARADOR -->
            <div class="mx-1 hidden h-8 w-px bg-slate-200 md:block"></div>


            <!-- POS -->
            <a
                href="/admin/ventas"
                class="flex h-12 min-w-36 items-center gap-3 rounded-xl px-5 text-lg font-semibold transition-all duration-200
                    <?= restaurantNavActive($currentPath, '/admin/ventas')
                        ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white shadow-md shadow-emerald-200'
                        : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:shadow-sm'
                    ?>"
            >
                <span class="material-symbols-outlined text-[23px] <?= restaurantNavActive($currentPath, '/admin/ventas') ? 'text-white' : 'text-emerald-600'?>">
                    point_of_sale
                </span>
                POS
            </a>
        </div>

    </nav>

</header>