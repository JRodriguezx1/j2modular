<div class="configuracion mx-auto w-full max-w-screen-2xl overflow-x-hidden rounded-none border-0 border-slate-200 bg-gradient-to-b from-violet-50 via-white to-white to-10% p-4 text-slate-800 shadow-sm sm:rounded-xl sm:border lg:p-6 !pb-[5.5rem]">
    <section class="pb-4 mb-5 border-b-2 border-indigo-500">
        <div>
            <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Administraci&oacute;n</p>
            <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Configuraci&oacute;n</h1>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Gestiona los datos del negocio, usuarios, facturaci&oacute;n y par&aacute;metros generales.</p>
        </div>
    </section>

    <div id="tabulacion" class="tabs configuracion-tabs">
        <div class="tabs-content configuracion-tabs__content">

            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginanegocio??'';?> >
                <span id="pagina1"><i class="fa-solid fa-store"></i>Negocio</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginaempleado??'';?> >
                <span id="pagina2"><i class="fa-solid fa-file-signature"></i>Emisores</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginaempleado??'';?> >
                <span id="pagina3"><i class="fa-solid fa-users-gear"></i>Empleados</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginamalla??'';?> >
                <span id="pagina4"><i class="fa-solid fa-cash-register"></i>Cajas</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina5"><i class="fa-solid fa-receipt"></i>Facturadores</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina6"><i class="fa-solid fa-building-columns"></i>Bancos</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina7"><i class="fa-solid fa-percent"></i>Tarifas</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina8"><i class="fa-solid fa-credit-card"></i>M. pago</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina9"><i class="fa-solid fa-file-invoice"></i>DIAN</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio" <?php echo $paginadesc??'';?> >
                <span id="pagina10"><i class="fa-solid fa-print"></i>Impresoras</span>
            </label>
            <label class="configuracion-tab">
                <input type="radio" name="radio">
                <span id="pagina11"><i class="fa-solid fa-sliders"></i>Sistema</span>
            </label>
        </div>
    </div>

    <?php include __DIR__. "/../../templates/alertas.php"; ?>

    <section class="min-h-[52rem] min-w-0 w-full max-w-full overflow-x-hidden rounded-lg border border-slate-200 bg-white/80 p-4 lg:p-6 [&>.paginas]:min-w-0">
        <div class="hidden max-w-screen-md mx-auto paginas pagina1"><?php include __DIR__. "/negocio.php";?></div>
        <div class="hidden paginas pagina2"><?php include __DIR__. "/emisores.php";?></div>
        <div class="hidden paginas pagina3"><?php include __DIR__. "/empleados.php";?></div>
        <div class="hidden paginas pagina4"><?php include __DIR__. "/gestioncajas.php";?></div>
        <div class="hidden paginas pagina5"><?php include __DIR__. "/gestionfacturadores.php";?></div>
        <div class="hidden paginas pagina6"><?php include __DIR__. "/gestionbancos.php";?></div>
        <div class="hidden paginas pagina7"><?php include __DIR__. "/tarifas.php";?></div>
        <div class="hidden paginas pagina8"><?php include __DIR__. "/mediospago.php";?></div>
        <div class="hidden paginas pagina9"><?php include __DIR__. "/dian.php";?></div>
        <div class="hidden paginas pagina10"><?php include __DIR__. "/impresoras.php";?></div>
        <div class="hidden paginas pagina11">
            <div class="config-system accordion_inv paramSistem grid items-start gap-4 lg:grid-cols-[23rem_minmax(0,1fr)] lg:gap-6">

                <input type="radio" name="config" id="btn1" checked>
                <input type="radio" name="config" id="btn2">
                <input type="radio" name="config" id="btn3">
                <input type="radio" name="config" id="btn4">
                <input type="radio" name="config" id="btn5">
                <input type="radio" name="config" id="btn6">
                <input type="radio" name="config" id="btn7">
                <input type="radio" name="config" id="btn8">
                <input type="radio" name="config" id="btn9">
                <input type="radio" name="config" id="btn10">

                <aside class="btnsetup border border-slate-200 rounded-xl bg-slate-50 p-4">
                    <div class="flex items-center gap-4 border-b border-slate-200 mb-4 pb-4">
                        <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium">tune</span>
                        <div>
                            <strong class="text-slate-900 text-2xl block">Ajustes de sistema</strong>
                            <small class="my-0 text-lg leading-snug text-slate-500">Par&aacute;metros generales</small>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-x-2 gap-y-2 tlg:gap-y-3">
                        <label class="config-tab btn1" for="btn1"><i class="fa-solid fa-cash-register"></i>Caja</label>
                        <label class="config-tab btn2" for="btn2"><i class="fa-solid fa-boxes-stacked"></i>Inventario</label>
                        <label class="config-tab btn3" for="btn3"><i class="fa-solid fa-key"></i>Claves</label>
                        <label class="config-tab btn4" for="btn4"><i class="fa-solid fa-user-shield"></i>Permisos</label>
                        <label class="config-tab btn5" for="btn5"><i class="fa-solid fa-print"></i>Impresi&oacute;n</label>
                        <label class="config-tab btn6" for="btn6"><i class="fa-solid fa-scale-balanced"></i>Impuesto</label>
                        <label class="config-tab btn7" for="btn7"><i class="fa-solid fa-file-invoice-dollar"></i>Facturaci&oacute;n</label>
                        <label class="config-tab btn8" for="btn8"><i class="fa-solid fa-gears"></i>Sistema</label>
                        <label class="config-tab btn9" for="btn9"><i class="fa-brands fa-whatsapp"></i>Whatsapp</label>
                        <label class="config-tab btn10" for="btn10"><i class="fa-solid fa-id-card"></i>Suscripci&oacute;n</label>
                    </div>
                </aside>

                <div class="contenedorsetup config-system-content min-h-[48rem] min-w-0 overflow-auto bg-white p-4">
                    <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" role="search">
                        <span class="material-symbols-outlined text-indigo-600 font-medium">search</span>
                        <input
                            class="min-h-11 min-w-0 flex-1 border-none bg-transparent text-xl text-slate-800 outline-none placeholder:text-slate-400"
                            id="buscarParametroSistema"
                            type="search"
                            autocomplete="off"
                            placeholder="Buscar par&aacute;metro del sistema"
                            aria-label="Buscar par&aacute;metro del sistema"
                        >
                        <button id="limpiarBusquedaParametroSistema" class="hidden cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600" type="button" aria-label="Limpiar b&uacute;squeda"><span class="material-symbols-outlined px-2 py-1 text-2xl">close</span></button>
                        <small id="resultadoBusquedaParametroSistema" class="w-full whitespace-nowrap text-base leading-5 text-slate-500 sm:w-auto" aria-live="polite"></small>
                    </div>
                    <?php include __DIR__. "/ajustesdelsistema/caja.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/inventario.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/claves.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/permisos.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/impresion.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/impuestos.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/facturacion.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/sistema.php"; ?>
                    <?php include __DIR__. "/ajustesdelsistema/whatsapp.php"; ?>
                    <?php if(userPerfil()==1): ?>
                        <?php include __DIR__. "/ajustesdelsistema/suscripcion.php"; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

