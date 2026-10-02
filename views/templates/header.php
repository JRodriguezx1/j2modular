<!-- <script src="https://cdn.tailwindcss.com"></script> -->

<!-- Flowbite -->
<!-- <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script> -->

<div class="barra-mobile">
    <div class="barra-mobile__logo">
        <img id="logoj2" class="w-80 h-28" src="/build/img/Logoj2blanco.png" alt="logoj2">
    </div>

    <!-- aviso de vencimiento centrado -->
    <?php if(isset($this->getData()['Aviso_vencimiento'])): ?>
        <div class="subscription-alert-wrap subscription-alert-wrap--mobile">
            <a class="subscription-alert" href="/suspendido" role="alert" aria-label="Ver detalle de suspension y pago">
                <span class="subscription-alert__icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>

                <div class="subscription-alert__body">
                    <span class="subscription-alert__title">
                        <?php echo $this->data['msj_titulo_aviso_vencimiento']; ?>
                    </span>

                    <span class="subscription-alert__message">
                        <?php echo $this->data['msj_texto_aviso_vencimiento']; ?>
                    </span>
                </div> 
            </a>
        </div>
    <?php endif; ?>
</div>
    <!-- Menu flotante en la parte inferior -->
    <div class="fixed bottom-4 left-1/2 z-30 h-[7rem] w-full max-w-[33rem] -translate-x-1/2 rounded-full border border-gray-200 bg-white shadow-lg md:hidden">
        <div class="grid h-full max-w-[33rem] grid-cols-5 mx-auto group">
            
            <?php if(tienePermiso('Habilitar modulo de configuracion')&&userPerfil()==3 || userPerfil()<3): ?>
            <a href="/admin/configuracion" data-tooltip-target="tooltip-home" class="inline-flex flex-col items-center justify-center px-5 rounded-s-full hover:bg-gray-50 ">
                <svg class="w-5 h-5 mb-1 text-gray-500  group-hover:text-indigo-600 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2"/>
                </svg>
                <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Ajustes</span>
                <p class="text-base my-0 text-gray-500">Ajuste</p>
            </a>
            <?php endif; ?>

            <?php if(tienePermiso('Habilitar modulo de inventario') || userPerfil()<=3): ?>
            <a href="/admin/almacen" data-tooltip-target="tooltip-almacen" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50  group">
                <svg class="w-5 h-5 mb-1 text-gray-500  group-hover:text-indigo-600 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11.074 4 8.442.408A.95.95 0 0 0 7.014.254L2.926 4h8.148ZM9 13v-1a4 4 0 0 1 4-4h6V6a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h17a1 1 0 0 0 1-1v-2h-6a4 4 0 0 1-4-4Z"/>
                    <path d="M19 10h-6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1Zm-4.5 3.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM12.62 4h2.78L12.539.41a1.086 1.086 0 1 0-1.7 1.352L12.62 4Z"/>
                </svg>
                <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Almacen</span>
                <p class="text-base my-0 text-gray-500">Almacen</p>
            </a>
            <?php endif; ?>

            <div class="flex items-center justify-center menu">
                <button data-tooltip-target="tooltip-new" id="mobile-menu1" type="button" class="inline-flex items-center justify-center w-14 h-14 font-medium bg-indigo-500 rounded-full hover:bg-indigo-600 group focus:ring-4 focus:ring-indigo-300 focus:outline-none ">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                    </svg>
                    <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Mas opciones</span>
                </button>
            </div>
            
            <?php if(tienePermiso('Habilitar modulo de caja') || userPerfil()<=3): ?>
            <a href="/admin/caja" data-tooltip-target="tooltip-caja"    class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50  group">
                <!-- Icono caja registradora -->
                <svg class="w-5 h-5 mb-1 text-gray-500  group-hover:text-indigo-600 " 
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M4 10h16v10H4zM8 10V6h8v4M7 14h10M7 18h10M10 6h4" />
                </svg>
                <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Caja</span>
                <p class="text-base my-0 text-gray-500">Caja</p>
            </a>
            <?php endif; ?>

            <?php if(tienePermiso('Habilitar modulo de venta') || userPerfil()<=3): ?>
            <a href="/admin/ventas<?php echo (getConfigLocal()['habilitar_venta_modo_rapido']??null)?->valor_final == 1?'/modorapido':''; ?>" data-tooltip-target="tooltip-venta" class="inline-flex flex-col items-center justify-center px-5 rounded-e-full hover:bg-gray-50  group">
                <svg class="w-5 h-5 mb-1 text-gray-500  group-hover:text-indigo-600 " 
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 
                            0c-1.1 0-1.99.9-1.99 2S15.9 22 17 22s2-.9 2-2-.9-2-2-2zm-12.83-2h13.66c.75 
                            0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 22.34 7H6.21l-.94-2H1v2h3l3.6 
                            7.59-1.35 2.44C5.11 17.37 6 19 7 19h12v-2H7l1.1-2h9.45c.75 
                            0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 22.34 7H6.21z"/>
                </svg>
                <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Ventas</span>
                <p class="text-base my-0 text-gray-500">Venta</p>
            </a>
            <?php endif; ?>
            <div id="tooltip-venta" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip ">
                Ventas
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>
    </div>

<div class="hidden items-center justify-between bg-white px-8 py-4 shadow-sm md:flex">
    <!-- izquierda -->
    <div class="flex items-center gap-3">
        <span class="sidebartoggle material-symbols-outlined cursor-pointer">menu</span>
        <span class="bg-indigo-600 hover:bg-indigo-700 text-white text-xl font-bold px-6 py-2 rounded-full shadow-xl transition duration-300 ease-in-out transform hover:scale-110 uppercase tracking-wide">
            <?php echo nombreSucursal(); ?>
        </span>
    </div>


    <!-- aviso de vencimiento centrado -->
    <?php if(isset($this->getData()['Aviso_vencimiento'])): ?>
        <div class="subscription-alert-wrap hidden tlg:flex">
            <a class="subscription-alert" href="/suspendido" role="alert" aria-label="Ver detalle de suspension y pago">
                <span class="subscription-alert__icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>

                <div class="subscription-alert__body">
                    <span class="subscription-alert__title">
                        <?php echo $this->data['msj_titulo_aviso_vencimiento']; ?>
                    </span>

                    <span class="subscription-alert__message">
                        <?php echo $this->data['msj_texto_aviso_vencimiento']; ?>
                    </span>
                </div>
            </a>
        </div>
    <?php endif; ?>


    <!-- derecha -->
    <div class="flex items-center gap-4 mr-4">

        <a class="inline-block w-auto rounded-lg border-0 bg-indigo-600 px-8 py-4 text-center text-[1.4rem] font-bold text-white transition-colors duration-300 hover:bg-indigo-700" href="/logout">
            Cerrar Sesi&oacute;n
        </a>

        <button type="button" data-dropdown-toggle="notification-dropdown"
            class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-300">
            <i class="text-4xl fa-solid fa-bell"></i>
        </button>

        <div id="notification-dropdown"
            class="group relative flex rounded-full focus-within:ring-4 focus-within:ring-gray-300">

            <img class="w-14 h-14 rounded-full ring-2 ring-indigo-100" src="/build/img/avatar/avatar9.jpg" alt="user" />

            <div
                class="absolute z-50 bg-white flex flex-col items-stretch top-[calc(100%+.8rem)] right-0 w-96 rounded-xl border border-slate-200 p-3 shadow-2xl scale-y-0 opacity-0 group-hover:scale-y-100 group-hover:opacity-100 origin-top-right duration-200">

                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-3 py-3">
                    <img class="w-12 h-12 rounded-full ring-2 ring-white shadow-sm" src="/build/img/avatar/avatar9.jpg" alt="user" />
                    <div class="min-w-0 text-left">
                        <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Usuario activo</p>
                        <p class="truncate text-xl font-bold text-slate-800">
                            <?php echo $user['nombre']; ?>
                        </p>
                    </div>
                </div>

                <div class="form-field">
                    <label for="selectSucursal" class=""> Sucursal</label>
                    <div class="form-input">
                        <span><i class="fa-solid fa-code-branch"></i></span>
                        <select id="selectSucursal" class="">
                            <?php foreach($sucursales as $val): ?>
                                <option value="<?php echo $val->id;?>" <?php if($val->id == $user['idsucursal']) echo 'selected'; ?>><?php echo htmlspecialchars($val->nombre, ENT_QUOTES, 'UTF-8');?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="my-3 h-px bg-slate-200"></div>

                <a class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-lg font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                    href="/admin/dashboard">
                    <i class="fa-solid fa-house w-6 text-center text-indigo-500"></i>
                    <span>Inicio</span>
                </a>

                <a class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-lg font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                    href="/admin/perfil">
                    <i class="fa-solid fa-user w-6 text-center text-indigo-500"></i>
                    <span>Mi Perfil</span>
                </a>

                <?php if($user['perfil']<4): ?>
                    <a class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-lg font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                        href="/admin/comisiones">
                        <i class="fa-solid fa-percent w-6 text-center text-indigo-500"></i>
                        <span>Comisiones</span>
                    </a>
                <?php endif; ?>

                <button id="btnMoneda" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-lg font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                    <i class="fa-solid fa-dollar-sign w-6 text-center text-indigo-500"></i>
                    <span>Moneda Equivalente</span>
                </button>

                <a class="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-lg font-semibold text-rose-600 transition hover:bg-rose-50"
                    href="/logout">
                    <i class="fa-solid fa-right-from-bracket w-6 text-center"></i>
                    <span>Cerrar sesi&oacute;n</span>
                </a>

            </div>
        </div>
    </div>
</div>


<!-- Menu movil barra inferior -->
<dialog id="miDialogomenumovil" class="fixed inset-x-0 bottom-0 top-auto m-0 mx-auto w-full max-w-[48rem] max-h-[90dvh] overflow-y-auto rounded-t-3xl border-0 bg-white p-0 shadow-2xl backdrop:bg-slate-900/50 motion-safe:open:animate-menuSlideUp" aria-labelledby="tituloMenuMovil" hidden>


  <div class="relative flex flex-col bg-slate-100 px-6 pt-5 pb-[calc(1.5rem+env(safe-area-inset-bottom))] sm:px-8">
    <div class="flex min-w-0 items-center justify-between gap-3 rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-200 via-indigo-100 to-cyan-100 p-3 pr-[5.5rem]">
      <div class="flex shrink-0 justify-center rounded-xl bg-white p-2 shadow-sm">
        <img id="logoj2" class="h-14 w-32 shrink-0 object-contain" src="/build/img/Logoj2indigo.png" alt="JDOS">
      </div>
      <div class="min-w-0 text-right">
        <span class="inline-flex max-w-full items-center justify-center break-words rounded-xl bg-white/80 px-3 py-2 text-base font-bold uppercase text-indigo-700">
          <?php echo nombreSucursal(); ?>
        </span>
      </div>
    </div>

    <div class="mt-6 flex shrink-0 items-center justify-between">
      <h4 id="tituloMenuMovil" class="m-0 text-3xl font-bold leading-none text-slate-900">Men&uacute;</h4>
      <button type="button" aria-label="Cerrar menú" onclick="document.getElementById('miDialogomenumovil').close()"
              class="absolute right-9 top-8 flex h-[44px] w-[44px] items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm transition-colors hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
        <span class="material-symbols-outlined text-4xl leading-none">close</span>
      </button>
    </div>

    <nav class="mt-4 grid grid-cols-2 gap-3">
      <?php if(tienePermiso('Mostrar dashboard') || userPerfil()<=3): ?>
        <a href="/admin/dashboard" class="flex min-h-[8rem] min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-sm text-center text-xl font-semibold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
          <span class="material-symbols-outlined inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-3xl text-indigo-600">home</span>
          <span>Inicio</span>
        </a>
      <?php endif; ?>

      <?php if(tienePermiso(html_entity_decode('Habilitar m&oacute;dulo de credito/separados', ENT_QUOTES, 'UTF-8')) || tienePermiso('Habilitar modulo de credito/separados') || userPerfil()<=3): ?>
        <a href="/admin/creditos" class="flex min-h-[8rem] min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-sm text-center text-xl font-semibold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
          <span class="material-symbols-outlined inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-3xl text-cyan-700">swap_horiz</span>
          <span>Cr&eacute;ditos</span>
        </a>
      <?php endif; ?>

      <?php if(tienePermiso('Habilitar modulo de reportes')&&userPerfil()==3 || userPerfil()<3): ?>
        <a href="/admin/reportes" class="flex min-h-[8rem] min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-sm text-center text-xl font-semibold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
          <span class="material-symbols-outlined inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-3xl text-indigo-600">format_list_bulleted</span>
          <span>Reportes</span>
        </a>
      <?php endif; ?>

      <a href="/admin/clientes" class="flex min-h-[8rem] min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-sm text-center text-xl font-semibold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
        <span class="material-symbols-outlined inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-3xl text-cyan-700">support_agent</span>
        <span>Clientes</span>
      </a>

      <a href="/admin/perfil" class="col-span-2 flex min-h-[6rem] min-w-0 flex-row items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-white p-3 shadow-sm text-center text-xl font-semibold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
        <span class="material-symbols-outlined inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-3xl text-indigo-600">manage_accounts</span>
        <span>Perfil</span>
      </a>
    </nav>

    <div class="mt-5 shrink-0 rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-center">
      <p class="m-0 break-words text-xl font-bold text-slate-800"><?php echo $_SESSION['nombre']; ?></p>
      <div class="mt-3 flex flex-wrap items-stretch justify-center gap-3 text-lg">
        <?php if($user['perfil']<4): ?>
          <a class="btnDialog btnDialog_light flex-1" href="/admin/comisiones">Comisiones</a>
        <?php endif; ?>
        <a class="btnDialog btnDialog_light flex-1 !text-rose-600" href="/logout">Cerrar sesi&oacute;n</a>
      </div>
      <p class="mb-0 mt-3 text-sm font-medium text-slate-400">JDOS <?php echo $_SESSION['sucursal']->version; ?></p>
    </div>
  </div>
</dialog>


<dialog id="miDialogoMonedaEquivalente" class="w-[95%] max-w-lg overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 transition-all duration-300 ease-out open:scale-100 open:opacity-100">

    <div class="px-8 pb-2 pt-7 text-center">
        <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-lg shadow-indigo-600/10 ring-1 ring-indigo-100">
            <i class="fa-solid fa-landmark text-4xl"></i>
        </div>

        <h4 class="m-0 text-3xl font-bold leading-tight text-slate-900">
            Moneda equivalente
        </h4>

        <p class="mx-auto mt-2 max-w-sm text-lg font-medium leading-5 text-slate-500">
            Establecer la tasa de cambio para la siguiente divisa.
        </p>
    </div>

    <div class="border-t border-slate-200 px-8 pb-8 pt-6">
        <div id="divmsjalertaTasaCambio"></div>

        <form id="formTasaCambio" class="formulario" method="POST">

            <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-5">
                
                <div class="mb-2 flex items-center gap-3 text-left">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-coins text-base"></i>
                    </span>
                    <label for="divisa" class="m-0 block text-lg font-semibold text-slate-800">Divisa destino</label>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl bg-indigo-50 text-indigo-500">
                        <i class="fa-solid fa-coins text-lg"></i>
                    </span>
                    <select id="divisa" class="h-16 w-full rounded-2xl border border-slate-300 bg-white py-3 pl-16 pr-4 text-lg font-semibold text-slate-900 placeholder:font-medium placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        <option disabled selected>-Seleccionar-</option>
                        <option value="2">Peso colombiano - COP</option>
                        <option value="3">Bolívar - VES</option>
                        <option value="4">Dólar estadounidense - USD</option>
                        <option value="5">Euro - EUR</option>
                        <option value="6">Real brasileño - BRL</option>
                    </select>
                </div>

                <div class="mt-5 mb-2 flex items-center gap-3 text-left">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-dollar-sign text-lg"></i>
                    </span>
                    <label for="tasaCambio" class="m-0 block text-lg font-semibold text-slate-800">Tasa de cambio</label>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl bg-indigo-50 text-indigo-500">
                        <i class="fa-solid fa-dollar-sign text-base"></i>
                    </span>
                    <input
                        id="tasaCambio"
                        class="h-16 w-full rounded-2xl border border-slate-300 bg-white py-3 pl-16 pr-4 text-lg font-semibold text-slate-900 placeholder:font-medium placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        type="text"
                        placeholder="ej: 100.000"
                        oninput="formatearMoneda(this)"
                        required
                    >
                </div>

            </div>

            <div class="mt-2 grid grid-cols-2 gap-3 border-t border-slate-200 pt-4">
                <button
                    class="inline-flex h-16 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-xl font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                    type="button"
                    value="Salir">
                    Cancelar
                </button>

                <button
                    id="btnConfirmarTasaCambio"
                    class="inline-flex h-16 items-center justify-center rounded-xl bg-indigo-600 px-5 text-xl font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"
                    type="submit">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    confirmar
                </button>
            </div>
        </form>
    </div>
</dialog>

