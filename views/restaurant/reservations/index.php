<?php
    $totalReservations = count($reservations);
    $pendingReservations = 0;
    $confirmedReservations = 0;
    $totalPeople = 0;

    foreach($reservations as $reservation) {
        if($reservation['status'] === 'pendiente')$pendingReservations++;
        if($reservation['status'] === 'confirmada')$confirmedReservations++;
        $totalPeople += (int) ($reservation['numberOfPeople'] ?? 0);
    }

    $selectedTimestamp = strtotime($selectedDate);
    $previousDate = date('Y-m-d', strtotime( '-1 day', $selectedTimestamp));
    $nextDate = date('Y-m-d', strtotime('+1 day', $selectedTimestamp));
    $today = date('Y-m-d');
    $isToday = $selectedDate === $today;

    $days = ['Sunday'=>'domingo', 'Monday'=>'lunes', 'Tuesday'=>'martes', 'Wednesday'=>'miércoles', 'Thursday'=>'jueves', 'Friday'=>'viernes', 'Saturday'=>'sábado'];

    $months = [
        1  => 'enero',
        2  => 'febrero',
        3  => 'marzo',
        4  => 'abril',
        5  => 'mayo',
        6  => 'junio',
        7  => 'julio',
        8  => 'agosto',
        9  => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $dayName = $days[date('l', $selectedTimestamp)];
    $monthName = $months[(int) date('n', $selectedTimestamp)];
    $formattedDate = $dayName . ', ' . date('j', $selectedTimestamp) . ' de ' . $monthName . ' de ' . date('Y', $selectedTimestamp);
?>


<div class="mx-auto w-full max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8 space-y-4">
    <!-- Page header -->
    <div class="flex flex-col gap-4 sm:flex-row justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Reservas</h1>
            <p class="mt-1 mb-0 text-base text-slate-500">Gestiona las llegadas y asignación de mesas.</p>
        </div>
        <button
            type="button"
            id="btnNewReservation"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700">
            <span class="material-symbols-outlined text-[19px]">add</span>
            Nueva reserva
        </button>
    </div>

    <!-- Date navigator -->
    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-center justify-center gap-2">
            <a
                href="/restaurant/reservations?date=<?=htmlspecialchars($previousDate)?>"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                <span class=" material-symbols-outlined text-[20px]">chevron_left</span>
            </a>
            <div class="min-w-0 px-2">
                <p class="m-0 text-lg font-semibold capitalize text-slate-800 w-full sm:w-96 text-center"><?= htmlspecialchars($formattedDate) ?></p>
            </div>
            <a
                href="/restaurant/reservations?date=<?=htmlspecialchars($nextDate)?>"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                <span class=" material-symbols-outlined text-[20px]">chevron_right</span>
            </a>
        </div>

        <?php if (!$isToday): ?>
            <a
                href="/restaurant/reservations"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                <span class=" material-symbols-outlined text-[17px]">today</span>
                Hoy
            </a>
        <?php endif; ?>

    </div>


    <!-- Summary -->
    <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        <!-- Total -->
        <div class="rounded-xl border border-slate-200 bg-white px-6 py-3">
            <p class="mt-0 text-lg font-medium text-slate-500">Reservas</p>
            <p class="mt-1 mb-0 text-3xl font-bold text-indigo-500"><?= $totalReservations ?></p>
        </div>

        <!-- Pending -->
        <div class="rounded-xl border border-slate-200 bg-white px-6 py-3">
            <p class="mt-0 text-lg font-medium text-slate-500">Pendientes</p>
            <p class="mt-1 mb-0 text-3xl font-bold text-amber-500"><?= $pendingReservations ?></p>
        </div>

        <!-- Confirmed -->
        <div class="rounded-xl border border-slate-200 bg-white px-6 py-3">
            <p class="mt-0  text-lg font-medium text-slate-500">Confirmadas</p>
            <p class="mt-1 mb-0 text-3xl font-bold text-emerald-500"><?= $confirmedReservations ?></p>
        </div>

        <!-- People -->
        <div class="rounded-xl border border-slate-200 bg-white px-6 py-3">
            <p class="mt-0 text-lg font-medium text-slate-500">Personas</p>
            <p class="mt-1 mb-0 text-3xl font-bold text-slate-900"><?= $totalPeople ?></p>
        </div>
    </div>


    <!-- Reservations -->
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <?php if (!empty($reservations)): ?>
            <div class="flex flex-wrap gap-4 p-6">
                <?php foreach ($reservations as $reservation): ?>
                    <?php
                        $startTime = date('g:i A', strtotime($reservation['startDate']));
                        $endTime = date('g:i A', strtotime($reservation['endDate']));
                        $resources = $reservation['resources'] ?? [];
                        $resourceNames = array_column($resources,'name');
                        $resourceText = !empty($resourceNames) ? implode( ', ', $resourceNames) : null;
                        $status = $reservation['status'];
                    ?>

                    <button
                        type="button"
                        data-reservation-id="<?= (int)$reservation['id']?>"
                        class="group grid w-auto grid-cols-1 gap-3 px-5 py-4 text-left transition hover:bg-slate-50/80 md:items-center border border-slate-100 rounded-xl"
                    >
                        <!-- Time -->
                        <div class="flex gap-4 items-center">
                            <p class="m-0 text-lg font-bold text-slate-900"><?= htmlspecialchars($startTime) ?></p>
                            <p class="m-0 text-base text-slate-500 font-medium"> hasta: <?= htmlspecialchars($endTime) ?></p>
                        </div>

                        <!-- Reservation information -->
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <p class="mt-0 truncate text-lg font-semibold text-slate-800"><?= htmlspecialchars($reservation['clientName']) ?></p>

                                <?php if($reservation['numberOfPeople'] !== null): ?>
                                    <span class="flex items-center gap-1 text-base text-slate-500">
                                        <span class="material-symbols-outlined text-[15px]">group</span>
                                        <?=(int)$reservation['numberOfPeople']?> personas
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Resource -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                                <?php if ($resourceText): ?>
                                    <span class="flex items-center gap-1 text-base font-medium text-slate-500">
                                        <span class=" material-symbols-outlined text-[15px]">table_restaurant </span>
                                        <?= htmlspecialchars($resourceText) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="flex items-center gap-1 text-base font-semibold text-amber-600">
                                        <span class="material-symbols-outlined text-[15px]">warning</span>
                                        Sin mesa asignada
                                    </span>
                                <?php endif; ?>
                                <?php if(!empty($reservation['observations'])): ?>
                                    <span class="max-w-md truncate text-base text-slate-500"><?= htmlspecialchars($reservation['observations']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>


                        <!-- Status -->
                        <div>
                            <?php if ($status === 'confirmada'): ?>
                                <span class="inline-flex rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Confirmada</span>
                            <?php elseif ($status === 'pendiente'): ?>
                                <span class="inline-flex rounded-lg bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Pendiente</span>
                            <?php elseif ($status === 'cancelada'): ?>
                                <span class="inline-flex rounded-lg bg-rose-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-rose-600">Cancelada</span>
                            <?php else: ?>
                                <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; ?>

            </div>

        <?php else: ?>
            <div class="flex min-h-[320px] flex-col items-center justify-center px-6 py-12 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-5">
                    <span class="material-symbols-outlined text-[25px]">event_available</span>
                </div>
                <p class=" mt-4text-sm font-semibold text-slate-700">No hay reservas para este día</p>
                <p class="mt-1 text-xs text-slate-5">Las nuevas reservas aparecerán aquí.</p>
            </div>
        <?php endif; ?>

    </section>
</div>


<!-- Overlay -->
<div id="reservationDrawerOverlay" class=" fixed inset-0 z-40 hidden bg-slate-950/30 backdrop-blur-[1px]"></div>

<!-- New reservation drawer -->
<aside id="reservationDrawer" class="fixed right-0 top-0 z-50 flex h-full w-full max-w-xl md:max-w-4xl xl:max-w-6xl translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Nueva reserva</h2>
            <p class="mt-0.5 text-base text-slate-500">Registra una nueva llegada al restaurante.</p>
        </div>

        <button
            type="button"
            id="btnCloseReservationDrawer"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
        >
            <span class="material-symbols-outlined text-[21px]">close</span>
        </button>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto px-6 py-5">
        <form id="reservationForm" class="flex h-full flex-col gap-6">
            <!-- Client -->
            <!--<div>
                <label for="reservationClient" class="mb-2 block text-lg font-semibold text-slate-700">Cliente</label>

                <button
                    type="button"
                    id="reservationClient"
                    class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-left transition hover:border-slate-300"
                >
                    <span class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">person_search</span>
                        </span>
                        <span>
                            <span id="reservationClientName" class="block text-base font-semibold text-slate-900">Buscar cliente</span>
                            <span class="block text-lg text-slate-500">Nombre, teléfono o identificación</span>
                        </span>
                    </span>
                    <span class="material-symbols-outlined text-[19px] text-slate-400">chevron_right</span>
                </button>

                <input type="hidden" id="reservationClientId" name="clientId">
            </div>-->
            <div class="space-y-2">
                <label class="text-xs font-semibold text-slate-700">Cliente</label>
                <input  id="reservationClientId" type="hidden" name="clientId">
                <!-- Cliente seleccionado -->
                <div id="selectedCustomer" class="hidden">
                    <div class="flex items-center gap-3 rounded-xl border border-indigo-200 bg-indigo-50/50 p-3">
                        <div id="selectedCustomerInitials" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700"></div>
                        <div class="min-w-0 flex-1">
                            <p id="selectedCustomerName" class="truncate text-sm font-semibold text-slate-800"></p>
                            <p id="selectedCustomerInfo" class=" mt-0.5 truncate text-xs text-slate-500"></p>
                        </div>

                        <button id="btnChangeCustomer" type="button" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            Cambiar
                        </button>
                    </div>
                </div>

                <!-- Buscador -->
                <div id="customerSearch">
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">
                            search
                        </span>

                        <input
                            id="customerSearchInput"
                            type="search"
                            autocomplete="off"
                            placeholder="Nombre, identificación o teléfono"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-9 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                        >

                        <span
                            id="customerSearchLoader"
                            class="material-symbols-outlined absolute right-3 top-1/2 hidden -translate-y-1/2 animate-spin text-[18px] text-indigo-500"
                        >
                            progress_activity
                        </span>
                    </div>
                    <div id="customerSearchResults" class="mt-2 hidden overflow-hidden rounded-xl border border-slate-200 bg-white"></div>
                </div>
            </div>

            <!-- Date -->
            <div>
                <label for="reservationDate" class="mb-2 block text-lg font-semibold text-slate-700">Fecha</label>
                <input
                    id="reservationDate"
                    type="date"
                    name="date"
                    value="<?= htmlspecialchars($selectedDate) ?>"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-base text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                >
            </div>

            <!-- Times -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="reservationStartTime" class="mb-2 block text-lg font-semibold text-slate-700">Hora entrada</label>
                    <input
                        id="reservationStartTime"
                        type="time"
                        name="startTime"
                        step="1800"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-base text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                    >
                </div>
                <div>
                    <label for="reservationEndTime" class="mb-2 block text-lg font-semibold text-slate-700">Hora salida</label>
                    <input
                        id="reservationEndTime"
                        type="time"
                        name="endTime"
                        step="1800"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-base text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                    >
                </div>
            </div>

            <!-- People -->
            <div>
                <label class="mb-2 block text-xs font-semibold text-slate-700">Personas</label>
                <div class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2">
                    <button
                        id="btnDecreasePeople"
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                    >
                        <span class="material-symbols-outlined text-[19px]">remove</span>
                    </button>

                    <div class="text-center">
                        <span id="reservationPeopleValue" class="block text-xl font-bold text-slate-900">2</span>
                        <span class="text-xl text-slate-500">personas</span>
                    </div>

                    <button
                        id="btnIncreasePeople"
                        type="button"
                        class="flex h-9  w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                    >
                        <span class="material-symbols-outlined text-[19px]">add</span>
                    </button>
                </div>
                <input type="hidden" id="reservationPeople" name="numberOfPeople" value="2">
            </div>

            <!-- Available tables -->
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <label class="text-lg font-semibold text-slate-700">Mesa</label>
                    <span id="availableTablesCounter" class="text-lg font-medium text-slate-500"></span>
                </div>
                <div id="availableTables" class="min-h-[100px] rounded-xl border border-dashed border-slate-200 p-4">
                    <div class="flex min-h-[70px] flex-col items-center justify-center text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-400">table_restaurant</span>
                        <p class="mt-1 text-base text-slate-400">Selecciona fecha y horario.</p>
                    </div>
                </div>
                <input type="hidden" id="reservationResourceId" name="resourceId">
            </div>

            <!-- Observations -->
            <div>
                <label for="reservationObservations" class="mb-2 block text-lg font-semibold text-slate-700">Observaciones</label>
                <textarea
                    id="reservationObservations"
                    name="observations"
                    rows="3"
                    maxlength="512"
                    placeholder="Cumpleaños, ubicación preferida, silla para bebé..."
                    class="w-full resize-none rounded-xl border border-slate-200 px-4 py-3 text-base text-slate-700 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                >
                </textarea>
            </div>

            <!-- Footer -->
            <div class="mt-auto flex items-center justify-end gap-2 border-t border-slate-200 bg-white px-6 py-4">
                <button
                    type="button"
                    id="btnCancelReservation"
                    class="rounded-xl px-4 py-2.5 text-base font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    id="btnSaveReservation"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    Guardar reserva
                </button>
            </div>

        </form>
    </div>
</aside>


<div id="reservationDetailOverlay" class="fixed inset-0 z-40 hidden bg-slate-900/30 backdrop-blur-[1px]"></div>
<aside id="reservationDetailDrawer" class="fixed right-0 top-0 z-50 h-full w-full max-w-md translate-x-full bg-white shadow-2xl transition-transform duration-300">
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-6">
        <div>
            <h4 class="font-semibold text-slate-900">Detalle de reserva</h4>
            <p class="text-base text-slate-500 m-0">Información de la reserva</p>
        </div>
        <button type="button" data-reservation-detail-close class="flex h-9 w-9 items-center justify-center rounded-lg hover:bg-slate-100">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="reservationDetailContent" class="h-[calc(100%-4rem)] overflow-y-auto p-6">

    </div>
    
    <div id="reservationDetailActions" class="hidden shrink-0 border-t border-slate-200 bg-white p-4 ">

    </div>
</aside>


<script type="application/json" id="restaurantReservationsData">
    <?= json_encode(['zones' => $zones ?? [] ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>