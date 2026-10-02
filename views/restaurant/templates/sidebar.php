<?php

$currentPath = strtok(
    $_SERVER['REQUEST_URI'] ?? '/',
    '?'
);

function restaurantNavActive(string $currentPath, string $path): bool {
    return str_starts_with($currentPath, $path);
}

?>

<aside
    class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:flex lg:flex-col  ">

    <!-- Logo / Workspace -->

    <div
        class="
            flex
            h-16
            items-center
            gap-3
            border-b
            border-slate-100
            px-5
        "
    >

        <div
            class="
                flex
                h-10
                w-10
                items-center
                justify-center
                rounded-xl
                bg-indigo-600
                text-white
            "
        >
            <span class="material-symbols-outlined">
                restaurant
            </span>
        </div>

        <div>
            <p class="text-base font-bold text-slate-900">
                JDOS
            </p>

            <p class="text-xs font-medium text-slate-400">
                Restaurant
            </p>
        </div>

    </div>


    <!-- Navegación -->

    <nav class="flex-1 space-y-1 p-3">

        <!-- Mesas -->

        <a
            href="/restaurant/tables"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                transition

                <?= restaurantNavActive(
                    $currentPath,
                    '/restaurant/tables'
                )
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-slate-600 hover:bg-slate-100'
                ?>
            "
        >

            <span class="material-symbols-outlined">
                table_restaurant
            </span>

            <span>
                Mesas
            </span>

        </a>


        <!-- Comandas -->

        <a
            href="/restaurant/orders"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                transition

                <?= restaurantNavActive(
                    $currentPath,
                    '/restaurant/orders'
                )
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-slate-600 hover:bg-slate-100'
                ?>
            "
        >

            <span class="material-symbols-outlined">
                receipt_long
            </span>

            <span>
                Comandas
            </span>

        </a>


        <!-- Cocina -->

        <a
            href="/restaurant/kitchen"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                transition

                <?= restaurantNavActive(
                    $currentPath,
                    '/restaurant/kitchen'
                )
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-slate-600 hover:bg-slate-100'
                ?>
            "
        >

            <span class="material-symbols-outlined">
                skillet
            </span>

            <span>
                Cocina
            </span>

        </a>


        <!-- Reservas -->

        <a
            href="/restaurant/reservations"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                transition

                <?= restaurantNavActive(
                    $currentPath,
                    '/restaurant/reservations'
                )
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-slate-600 hover:bg-slate-100'
                ?>
            "
        >

            <span class="material-symbols-outlined">
                calendar_month
            </span>

            <span>
                Reservas
            </span>

        </a>

    </nav>


    <!-- Accesos JDOS -->

    <div
        class="
            space-y-1
            border-t
            border-slate-100
            p-3
        "
    >

        <a
            href="/admin/ventas"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                text-slate-600
                transition
                hover:bg-slate-100
            "
        >

            <span class="material-symbols-outlined">
                point_of_sale
            </span>

            Ir al POS

        </a>


        <a
            href="/admin"
            class="
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3
                text-sm
                font-medium
                text-slate-600
                transition
                hover:bg-slate-100
            "
        >

            <span class="material-symbols-outlined">
                settings
            </span>

            Administración

        </a>

    </div>

</aside>