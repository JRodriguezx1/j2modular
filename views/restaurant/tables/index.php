<?php

    $activeTables = array_values(array_filter( $tables ?? [], fn($table) => $table['active'] ?? false));
    $activeZones = array_values(array_filter($zones ?? [], fn($zone) => $zone['active'] ?? false));
    $totalTables = count($activeTables);
    $availableTables = count(array_filter($activeTables, fn($table) => ($table['operationalStatus'] ?? '') === 'disponible'));
    $occupiedTables = count(array_filter($activeTables, fn($table) => ($table['operationalStatus'] ?? '') === 'ocupada'));
    $reservedTables = count(array_filter($activeTables, fn($table) => ($table['operationalStatus'] ?? '')  === 'reservada'));

?>

<div class="mx-auto w-full max-w-[1920px] px-4 py-6 sm:px-6 lg:px-8">
    <!--
        En pantallas muy grandes:
        Mesas + panel lateral de reservas.
    -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <!-- ================================================= -->
        <!-- MESAS                                             -->
        <!-- ================================================= -->
        <section class="min-w-0">
            <!-- Encabezado -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <h1 class="text-2xl font-bold text-slate-900">Mesas</h1>
                    <div class="hidden h-5 w-px bg-slate-200 sm:block"></div>

                    <div class=" flex flex-wrap items-center gap-4 text-lg font-medium text-slate-500">
                        <span><?= $totalTables ?> mesas</span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2  rounded-full bg-emerald-500"></span>
                            <?= $availableTables ?> disponibles
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            <?= $occupiedTables ?> ocupadas
                        </span>
                    </div>
                </div>

                <!-- Actualizar -->
                <button type="button" class=" flex h-10 items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[19px]">refresh</span>
                    Actualizar
                </button>

            </div>


            <!-- Zonas -->
            <div class="mb-4 overflow-x-auto pb-1">
                <div class="flex min-w-max items-center gap-2">
                    <button type="button" data-zone="all" class="rounded-xl bg-indigo-600 px-3.5 py-2 text-sm font-medium text-white shadow-sm">
                        Todas
                    </button>

                    <?php foreach ($activeZones as $zone): ?>
                        <button type="button" data-zone="<?= (int) $zone['id'] ?>" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600">
                            <?= htmlspecialchars($zone['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>


            <!-- Grid mesas -->
            <?php if (!empty($activeTables)): ?>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5 2xl:grid-cols-5 min-[1750px]:grid-cols-5">
                    <?php foreach ($activeTables as $table): ?>
                        <?php
                            $status = $table['operationalStatus'] ?? 'disponible';
                            $statusClasses = match ($status) {
                                'ocupada' => 'border-amber-200 bg-amber-50/40',
                                'reservada' => 'border-indigo-200 bg-indigo-50/40',
                                'limpieza' => 'border-sky-200 bg-sky-50/40',
                                'mantenimiento',
                                'fuera_servicio' => 'border-slate-300 bg-slate-100',
                                default => 'border-slate-200 bg-white'
                            };

                            $badgeClasses = match ($status) {
                                'ocupada' => 'bg-amber-100 text-amber-700',
                                'reservada' => 'bg-indigo-100 text-indigo-700',
                                'limpieza' => 'bg-sky-100 text-sky-700',
                                'mantenimiento',
                                'fuera_servicio' => 'bg-slate-200 text-slate-600',
                                default => 'bg-emerald-100 text-emerald-700'
                            };
                        ?>

                        <button
                            type="button"
                            data-table-id="<?= (int) $table['id'] ?>"
                            data-active-occupation
                            data-resource-id="<?=(int) $table['resourceId']?>"
                            data-zone="<?=$table['zoneId'] !== null ? (int) $table['zoneId'] : ''?>"
                            data-status="<?= htmlspecialchars($status) ?>"
                            data-reservation-id="<?= $table['reservationId'] !== null ? (int) $table['reservationId'] : ''?>"

                            class="group relative min-h-[145px] rounded-2xl border p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md <?= $statusClasses ?>">
                            <!-- Nombre + estado visual -->
                            <div class="flex items-start justify-between gap-3">
                                <h2 class=" text-base font-semibold text-slate-900"><?= htmlspecialchars($table['name']) ?></h2>
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full <?= $status === 'disponible' ? 'bg-emerald-500' : ($status === 'ocupada' ? 'bg-amber-500' : 'bg-slate-400')?>"></span>
                            </div>

                            <!-- Capacidad -->
                            <div class="my-7 flex items-center justify-center gap-2 text-slate-500">
                                <span class=" material-symbols-outlined text-4xl">group</span>
                                <span class="text-lg font-semibold"><?= (int) $table['capacity'] ?></span>
                            </div>

                            <!-- Estado -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-lg px-2.5 py-1.5 text-lg leading-4 font-semibold uppercase <?= $badgeClasses ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $status)) ?>
                                </span>
                                <?php if ($status === 'ocupada' && !empty($table['occupiedSince'])): ?>
                                    <div class="flex items-center gap-1.5 text-base font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-3xl">schedule</span>
                                        En uso
                                    </div>
                                <?php elseif ($status === 'reservada' && !empty($table['reservationStart'])):
                                    $reservationTime = date('g:i A', strtotime($table['reservationStart']));  ?>
                                    <div class="flexitems-center gap-1.5 text-base font-semibold text-indigo-700">
                                        <span class="material-symbols-outlined text-3xl">event</span>
                                        <?= htmlspecialchars($reservationTime) ?>
                                    </div>
                                <?php elseif ($status === 'limpieza'): ?>
                                    <div class="flex items-center gap-1.5 text-base font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-3xl">cleaning_services</span>
                                        Pendiente de limpieza
                                    </div>
                                <?php elseif ($status === 'mantenimiento'): ?>
                                    <div class="flex items-center gap-1.5 text-base font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-3xl">build</span>
                                        En mantenimiento
                                    </div>
                                <?php elseif ($status === 'fuera_servicio'): ?>
                                    <div class="flex items-center gap-1.5 text-base font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-3xl">block</span>
                                        Fuera de servicio
                                    </div>
                                <?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-slate-300">table_restaurant</span>
                    <h3 class="mt-4 text-base font-semibold text-slate-700">No hay mesas configuradas</h3>
                    <p class="mt-1 text-sm text-slate-400">Las mesas activas aparecerán aquí.</p>
                </div>

            <?php endif; ?>
        </section>

        <!-- ================================================= -->
        <!-- RESERVAS                                          -->
        <!-- Solo visible desde XL (1280px)                    -->
        <!-- ================================================= -->
        <aside class="hidden xl:block">
            <div class="sticky top-[145px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <!-- Header reservas -->
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Reservas de hoy</h2>
                        <p class="mt-1 mb-2 text-base text-slate-500">Próximas llegadas</p>
                    </div>
                    <?php if (($totalReservations ?? 0) > 0): ?>
                        <span class=" flex h-8 min-w-8 items-center justify-center rounded-lg bg-indigo-50 px-2 text-lg font-semibold text-indigo-600">
                            <?= (int) $totalReservations ?>
                        </span>
                     <?php endif; ?>
                </div>

                <!-- Reservas -->

              <?php if (!empty($reservations)): ?>

                <div class="divide-y divide-slate-100">
                    <?php foreach ($reservations as $index => $reservation): ?>
                        <?php
                            $startTimestamp = strtotime($reservation['startDate']);
                            $startTime = date('g:i A', $startTimestamp);
                            $resources = $reservation['resources'] ?? [];
                            $resourceNames = array_column( $resources, 'name');
                            $resourceText = !empty($resourceNames) ? implode(', ', $resourceNames) : null;
                            $status = $reservation['status'] ?? 'pendiente';
                        ?>


                        <button 
                            type="button"
                            data-reservation-id="<?=(int) $reservation['id']?>"
                            class="group block w-full px-5 py-4 text-left transition hover:bg-slate-50">
                            <!-- Primera reserva -->
                            <?php if ($index === 0): ?>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-indigo-600">Próxima</span>
                                </div>
                            <?php endif; ?>

                            <!-- Hora + estado -->
                            <div class="flex items-start justify-between gap-3">
                                <span class="text-base font-bold text-slate-900"><?= htmlspecialchars($startTime) ?></span>
                                <?php if ($status === 'confirmada'): ?>
                                    <span class="rounded-md bg-emerald-100 px-2.5 py-1.5 text-base font-bold uppercase text-emerald-700">Confirmada</span>
                                <?php elseif ($status === 'pendiente'): ?>
                                    <span class="rounded-md bg-amber-50 px-2.5 py-1.5 text-base font-bold uppercase text-amber-700">Pendiente</span>
                                <?php else: ?>
                                    <span class="rounded-md bg-slate-100  px-2.5 py-1.5 text-base font-bold uppercase text-slate-600">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Cliente -->
                            <p class="mt-1 mb-2 truncate text-base font-semibold text-slate-800"><?= htmlspecialchars($reservation['clientName']) ?></p>

                            <!-- Personas -->
                            <div class="mt-2 flex items-center gap-4 text-base text-slate-500">
                                <?php if ($reservation['numberOfPeople'] !== null): ?>
                                    <span class="flex items-center gap-1">
                                        <span class=" material-symbols-outlined text-4xl">group</span>
                                        <?= (int)$reservation['numberOfPeople']?> personas
                                    </span>
                                    <!-- Mesa -->
                                    <?php if ($resourceText !== null): ?>
                                        <div class="flex items-center gap-1.5  text-base font-medium text-slate-600">
                                            <span class="material-symbols-outlined text-4xl text-slate-400">table_restaurant</span>
                                            <span class="truncate"> <?= htmlspecialchars($resourceText) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center gap-1.5 text-base font-semibold text-amber-600">
                                            <span class="material-symbols-outlined text-4xl">warning</span>
                                            Sin mesa asignada
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                        </button>
                    <?php endforeach; ?>
                </div>

              <?php else: ?>
                    <!-- Estado temporal -->
                    <div class="flex min-h-[280px] flex-col items-center justify-center px-6 py-10 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <p class="mt-4 text-lg font-semibold text-slate-700">Sin reservas próximas</p>
                        <p class="mt-1 max-w-[220px] text-base leading-5 text-slate-500">
                            Las reservas del día aparecerán aquí automáticamente.
                        </p>
                    </div>
              <?php endif; ?>
              <!-- Footer -->
                <a
                    href="/restaurant/reservations"
                    class="flex items-center justify-center gap-1 border-t border-slate-100 px-5 py-4 text-base font-semibold text-indigo-600 transition hover:bg-indigo-50">
                    Ver todas las reservas
                    <span class=" material-symbols-outlined text-[17px]">arrow_forward</span>
                </a>
            </div>
        </aside>

    </div>

</div>



<div id="activeOccupationOverlay" class="fixed inset-0 z-40 hidden bg-slate-950/30 backdrop-blur-[1px]"></div>

<aside
    id="activeOccupationDrawer"
    class="fixed right-0 top-0 z-50
        flex h-full w-full max-w-md translate-x-full flex-col bg-white
        shadow-2xl transition-transform duration-300"
    aria-hidden="true"
>
    <header
        class="flex shrink-0
            items-center justify-between
            border-b border-slate-200 px-5 py-4"
    >
        <div>
            <p class="text-xs font-medium
                    uppercase tracking-wide
                    text-slate-500"
            >
                Mesa ocupada
            </p>

            <h2 class="text-lg font-semibold text-slate-900">Atención activa</h2>
        </div>

        <button
            type="button"
            data-close-active-occupation
            class="
                flex size-10 items-center justify-center
                rounded-xl
                text-slate-500 hover:bg-slate-100"
        >
            <span class="material-symbols-outlined">
                close
            </span>
        </button>
    </header>

    <div id="activeOccupationContent" class="min-h-0 flex-1 overflow-y-auto p-5"></div>

    <div id="activeOccupationActions" class=" hidden shrink-0 border-t border-slate-200 bg-white p-4"></div>
</aside>