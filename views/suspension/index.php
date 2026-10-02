<?php

$fechaVencimiento = new DateTime($sucursal->fecha_corte);
$hoy = new DateTime();

$diferencia = $hoy->diff($fechaVencimiento);
$diasVencidos = $diferencia->days;
$estaVencido = $hoy > $fechaVencimiento;
$diasPendientes = $estaVencido ? 0 : $diasVencidos;
$estadoLabel = $estaVencido ? 'Cuenta suspendida' : 'Suscripcion por vencer';
$estadoTexto = $estaVencido
    ? 'Tu acceso esta temporalmente suspendido hasta validar el pago.'
    : 'Tu suscripcion esta proxima a vencer. Puedes renovar para evitar interrupciones.';
$whatsappText = rawurlencode('Hola, acabo de realizar el pago de mi suscripcion.');

?>

<div class="box min-h-screen bg-slate-100">
    <div class="mx-auto flex min-h-screen w-full flex-col justify-center px-5 py-6 md:px-8 md:py-10" style="max-width: 1360px;">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-700 via-indigo-600 to-sky-500 px-6 py-6 text-white md:px-10 md:py-12">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-white/10 [clip-path:polygon(30%_0,100%_0,100%_100%,0_100%)]"></div>

                <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex min-w-0 flex-col gap-5 md:flex-row md:items-center">
                        <img src="/build/img/Logoj2blanco.png" alt="J2 Software POS" class="h-16 w-fit max-w-[180px] shrink-0 md:h-20 md:max-w-none">

                        <div class="flex min-w-0 items-start gap-4">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:h-14 md:w-14">
                                <i class="fa-solid fa-triangle-exclamation text-2xl md:text-3xl"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="mb-1 text-sm font-black uppercase tracking-[.16em] text-white/75 md:tracking-[.22em]">Estado del servicio</p>
                                <h1 class="m-0 text-3xl font-black leading-tight md:text-4xl"><?= $estadoLabel; ?></h1>
                                <p class="mb-0 mt-1 text-base font-medium leading-snug text-white/90 md:text-lg"><?= $estadoTexto; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/15 px-5 py-4 text-left ring-1 ring-white/20 backdrop-blur md:min-w-72 md:px-6 md:py-5">
                        <p class="mb-1 text-sm font-black uppercase tracking-[.18em] text-white/70">Valor a renovar</p>
                        <p class="m-0 text-4xl font-black">$<?= number_format($sucursal->valorplan, 2, ',', '.'); ?></p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-white/80"><?= $sucursal->plan->nombre; ?></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_1.05fr] lg:p-12">
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-6 lg:p-8">
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <p class="mb-1 text-sm font-black uppercase tracking-[.18em] text-indigo-600">Cuenta</p>
                            <h2 class="m-0 text-3xl font-black text-slate-900">Detalle de suspension</h2>
                        </div>
                        <span class="rounded-full bg-rose-100 px-4 py-2 text-sm font-black uppercase tracking-wide text-rose-700">
                            <?= $estaVencido ? 'Vencida' : 'Activa'; ?>
                        </span>
                    </div>

                    <div class="divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-slate-500">Sucursal</span>
                            <strong class="text-right text-slate-900"><?= $sucursal->nombre; ?></strong>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-slate-500">Plan</span>
                            <strong class="text-right text-slate-900"><?= $sucursal->plan->nombre; ?></strong>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-slate-500">Fecha de corte</span>
                            <strong class="text-right text-rose-600"><?= $sucursal->fecha_corte; ?></strong>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <span class="text-slate-500"><?= $estaVencido ? 'Dias vencidos' : 'Dias restantes'; ?></span>
                            <strong class="rounded-xl <?= $estaVencido ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?> px-3 py-1">
                                <?= $estaVencido ? $diasVencidos : $diasPendientes; ?>
                            </strong>
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-900">
                        <p class="m-0 text-base font-bold">
                            Al validar el pago, el sistema se reactivara automaticamente. Tiempo estimado:
                            <span class="text-amber-700">3 minutos</span>.
                        </p>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 lg:p-8">
                    <div class="mb-5">
                        <p class="mb-1 text-sm font-black uppercase tracking-[.18em] text-indigo-600">Pago</p>
                        <h2 class="m-0 text-3xl font-black text-slate-900">Medios disponibles</h2>
                        <p class="mb-0 mt-1 text-base text-slate-500">Realiza el pago y envia el comprobante para acelerar la validacion.</p>
                    </div>

                    <div class="grid gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 lg:p-6">
                            <div class="flex items-center gap-3">
                                <span class="grid h-11 w-11 place-items-center rounded-xl bg-indigo-100 text-indigo-700">
                                    <i class="fa-solid fa-building-columns text-xl"></i>
                                </span>
                                <div>
                                    <h3 class="m-0 text-xl font-black text-slate-900">Transferencia bancaria</h3>
                                    <p class="m-0 text-sm font-semibold text-slate-500">Bancolombia</p>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-2 text-base text-slate-600">
                                <p class="m-0"><strong class="text-slate-800">Cuenta:</strong> -</p>
                                <p class="m-0"><strong class="text-slate-800">Titular:</strong> -</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 lg:p-6">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-cyan-100 text-cyan-700">
                                        <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                                    </span>
                                    <h3 class="m-0 text-xl font-black text-slate-900">Nequi / Daviplata</h3>
                                </div>
                                <p class="mb-0 mt-4 text-base font-bold text-slate-700">3022016786 / 304 2029683</p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 lg:p-6">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-violet-100 text-violet-700">
                                        <i class="fa-solid fa-at text-xl"></i>
                                    </span>
                                    <h3 class="m-0 text-xl font-black text-slate-900">Bre-B</h3>
                                </div>
                                <p class="mb-0 mt-4 text-base font-bold text-slate-700">@3022016786</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-6 py-5 md:flex-row md:items-center md:justify-between lg:px-12 lg:py-8">
                <p class="m-0 text-base font-semibold text-slate-500">Ten a la mano el comprobante antes de solicitar la reactivacion.</p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a
                        href="https://wa.me/573234433298?text=<?= $whatsappText; ?>"
                        target="_blank"
                        class="inline-flex min-h-16 items-center justify-center gap-3 rounded-xl bg-emerald-500 px-7 py-4 text-xl font-black text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600 lg:px-8">
                        <i class="fa-brands fa-whatsapp"></i>
                        Enviar comprobante
                    </a>

                    <a
                        href="/login"
                        class="inline-flex min-h-16 items-center justify-center gap-3 rounded-xl bg-indigo-600 px-7 py-4 text-xl font-black text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700 lg:px-8">
                        <i class="fa-solid fa-rotate-right"></i>
                        Ya realice el pago
                    </a>
                </div>
            </div>
        </div>

        <p class="mb-0 mt-8 text-center text-base font-semibold text-slate-500">
            &copy; <?= date('Y') ?> J2 SOFTWARE MULTISUCURSAL
        </p>
    </div>
</div>
