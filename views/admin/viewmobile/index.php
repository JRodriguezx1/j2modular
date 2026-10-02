<div class="viewmobile">
    <div class="viewmobile__titulos">
        <!-- <h2 class="viewmobile__heading"><?php echo $titulo; ?>Holaaaaa </h2> -->
        <p class="viewmobile__descripcion">Módulos Adicionales</p> 
    </div>    
    <div class="viewmobile__contenedor">
        <nav class="viewmobile__menu">
            <div class="viewmobile__bloquemovil">
                <a href="/admin/facturacion" class="viewmobile__enlace <?php echo validar_string_url('/facturacion')?'viewmobile__enlace--actual':''; ?>" >
                    <i class="fa-solid fa-credit-card"></i>
                    <span class="viewmobile__menu-texto">Facturación</span>
                </a>

                <a href="/admin/reportes" class="viewmobile__enlace viewmobile__otrosmodulos <?php echo validar_string_url('/reportes')?'viewmobile__enlace--actual':''; ?>" >
                    <i class="fa-solid fa-coins"></i>
                    <span class="viewmobile__menu-texto">reportes</span>
                </a>

                <a href="/admin/clientes" class="viewmobile__enlace viewmobile__otrosmodulos <?php echo validar_string_url('/clientes')?'viewmobile__enlace--actual':''; ?>" >
                    <i class="fa-solid fa-users"></i>
                    <span class="viewmobile__menu-texto">clientes</span>
                </a>

                <a href="/admin/fidelizacion" class="viewmobile__enlace viewmobile__otrosmodulos <?php echo validar_string_url('/fidelizacion')?'viewmobile__enlace--actual':''; ?>" >
                    <i class="fa-solid fa-gift"></i>
                    <span class="viewmobile__menu-texto">descuentos</span>
                </a>

                <a href="/admin/adminconfig" class="viewmobile__enlace viewmobile__otrosmodulos <?php echo validar_string_url('/adminconfig')?'viewmobile__enlace--actual':''; ?>" >
                    <i class="fa-solid fa-gears"></i>
                    <span class="viewmobile__menu-texto">administrador</span>
                </a>
            </div>
        </nav>
    </div>
</div>