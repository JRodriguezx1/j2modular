<!--<aside class="dashboard__sidebar">
    <nav class="dashboard__menu">
        <a href="/admin/dashboard" class="dashboard__enlace <?php echo validar_string_url('/dashboard')?'dashboard__enlace--actual':''; ?>">
            <i class="fa-solid fa-house"></i>
            <span class="dashboard__menu-texto">inicio</span>
        </a>

        <a href="/admin/servicios" class="dashboard__enlace <?php echo validar_string_url('/servicios')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-list"></i>
            <span class="dashboard__menu-texto">servicios</span>
        </a>

        <a href="/admin/facturacion" class="dashboard__enlace <?php echo validar_string_url('/facturacion')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-credit-card"></i>
            <span class="dashboard__menu-texto">facturacion</span>
        </a>

        <a href="/admin/reportes" class="dashboard__enlace <?php echo validar_string_url('/reportes')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-coins"></i>
            <span class="dashboard__menu-texto">reportes</span>
        </a>

        <a href="/admin/citas" class="dashboard__enlace <?php echo validar_string_url('/citas')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-calendar"></i>
            <span class="dashboard__menu-texto">citas</span>
        </a>

        <a href="/admin/clientes" class="dashboard__enlace <?php echo validar_string_url('/clientes')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-users"></i>
            <span class="dashboard__menu-texto">clientes</span>
        </a>

        <a href="/admin/fidelizacion" class="dashboard__enlace <?php echo validar_string_url('/fidelizacion')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-gift"></i>
            <span class="dashboard__menu-texto">descuentos</span>
        </a>
        <?php //if($user['admin']>2): ?>
        <a href="/admin/adminconfig" class="dashboard__enlace <?php echo validar_string_url('/adminconfig')?'dashboard__enlace--actual':''; ?>" >
            <i class="fa-solid fa-gears"></i>
            <span class="dashboard__menu-texto">administrador</span>
        </a>
        <?php //endif; ?>
    </nav>
</aside>-->

<aside class="sidebar relative">
    <div class="uptask flex h-28 flex-col items-center justify-center p-0">
        <img class="sidebar-mini-logo" src="/build/img/logo-j2-mini-blanco-limpio.png" alt="JDOS">
        <img id="logoj2" class="w-80 h-28" src="/build/img/Logoj2blanco.png" alt="logoj2">

        <div class="menux mt-2">
            <img id="mobile-menux" src="/build/img/cerrar.svg" alt="cerrar menu">
        </div>
    </div>
    
    <nav class="sidebar-nav"> <!-- el tamaÃ±o de las letras de los links <a> estan definidos en 1.6rem en gloables.scss -->
        <?php if(tienePermiso('Mostrar dashboard') || userPerfil()<=3): ?>
            <a class="<?php echo ($titulo === 'Inicio')?'activo':''; ?>" href="/admin/dashboard"><span class="material-symbols-outlined">home</span> <label class="btnav"> Inicio</label> </a>
        <?php endif; ?>
        <?php if(tienePermiso('Habilitar modulo de contabilidad')): ?>
            <a class="<?php echo ($titulo === 'Contabilidad')?'activo':''; ?>" href="/admin/contabilidad"><span class="material-symbols-outlined"> article</span> <label class="btnav"> Informes Contables</label></a>
        <?php endif; ?>
         <?php if(1): ?>
            <!--<a class="<?php //echo ($titulo === 'Nomina electronica')?'activo':''; ?>" href="/admin/nominaelectronica"><span class="material-symbols-outlined"> article</span> <label class="btnav"> Nomina Electronica</label></a>-->
        <?php endif; ?>
        <?php if(tienePermiso('Habilitar modulo de inventario') || userPerfil()<=3): ?>
            <a class="<?php echo ($titulo === 'Almacen')?'activo':''; ?>" href="/admin/almacen"><span class="material-symbols-outlined">warehouse</span> <label class="btnav"> Almacen</label></a>
        <?php endif; ?>
        <?php if(tienePermiso('Habilitar modulo de caja') || userPerfil()<=3): ?>
            <a class="<?php echo ($titulo === 'Caja')?'activo':''; ?>" href="/admin/caja"><span class="material-symbols-outlined">point_of_sale</span> <label class="btnav"> Caja</label></a>
         <?php endif; ?>
        <?php if(tienePermiso('Habilitar modulo de venta') || userPerfil()<=3): ?>
            <a class="<?php echo ($titulo === 'Ventas')?'activo':''; ?>" href="/admin/ventas<?php echo (getConfigLocal()['habilitar_venta_modo_rapido']??null)?->valor_final == 1?'/modorapido':''; ?>"><span class="material-symbols-outlined">storefront</span> <label class="btnav"> Ventas</label></a>
        <?php endif; ?>
        <?php if(tienePermiso('Habilitar módulo de credito/separados') || userPerfil()<=3): ?>
            <a class="<?php echo ($titulo === 'Creditos')?'activo':''; ?>" href="/admin/creditos"><span class="material-symbols-outlined">swap_horiz</span> <label class="btnav"> Creditos</label></a>
        <?php endif; ?>
        <?php if(tienePermiso('Habilitar modulo de reportes')&&userPerfil()==3 || userPerfil()<3): ?>
            <a class="<?php echo ($titulo === 'Reportes')?'activo':''; ?>" href="/admin/reportes"><span class="material-symbols-outlined">finance</span> <label class="btnav"> Reportes</label></a>
        <?php endif; ?>
        <?php //if(tienePermiso('Habilitar modulo de venta')): ?>
            <a class="<?php echo (str_contains($titulo, 'Clientes'))?'activo':''; ?>" href="/admin/clientes"><span class="material-symbols-outlined">support_agent</span> <label class="btnav"> Clientes</label></a>
        <?php //endif; ?>    
        <?php //if(tienePermiso('Habilitar modulo de venta')): ?>
            <a class="<?php echo ($titulo === 'Perfil')?'activo':''; ?>" href="/admin/perfil"><span class="material-symbols-outlined">manage_accounts</span> <label class="btnav"> Perfil</label></a>
        <?php //endif; ?>
        <?php if(tienePermiso('Habilitar modulo de configuracion')&&userPerfil()==3 || userPerfil()<3): ?>
        <a class="<?php echo ($titulo === 'Configuracion')?'activo':''; ?>" href="/admin/configuracion"><span class="material-symbols-outlined">settings</span> <label class="btnav">Ajustes</label></a>
        <?php endif; ?>
    </nav>
    <p class="text-white text-xl font-semibold text-center absolute bottom-0 inset-x-0">JDOS  <?php echo $_SESSION['sucursal']->version; ?></p>
</aside>





