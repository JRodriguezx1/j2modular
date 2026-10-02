<?php if(!empty($alertas) && is_array($alertas)): ?>
    <div class="pointer-events-none fixed right-5 top-24 z-[9999] flex w-[calc(100vw-2.5rem)] max-w-[52rem] flex-col gap-3 sm:right-8" data-alert-toast-container>
        <?php
            foreach($alertas as $key => $alerta){
                if($key === "exito" || $key === "error")
                foreach($alerta as $mensaje){
                    $esError = $key === 'error';
                    $contenedor = $esError
                        ? 'border-rose-200 bg-white text-rose-900 ring-rose-100'
                        : 'border-emerald-200 bg-white text-emerald-900 ring-emerald-100';
                    $icono = $esError
                        ? 'bg-rose-50 text-rose-600'
                        : 'bg-emerald-50 text-emerald-600';
                    $barra = $esError ? 'bg-rose-500' : 'bg-emerald-500';
                    $faIcon = $esError ? 'fa-circle-exclamation' : 'fa-circle-check';
                    ?>
                    <div class="pointer-events-auto translate-x-0 overflow-hidden rounded-2xl border <?php echo $contenedor; ?> opacity-100 shadow-xl shadow-slate-900/10 ring-1 transition-all duration-300 ease-out" role="alert" data-alert-toast>
                        <div class="flex items-start gap-5 px-6 py-5">
                            <span class="mt-0.5 grid h-14 w-14 shrink-0 place-items-center rounded-2xl <?php echo $icono; ?>">
                                <i class="fa-solid <?php echo $faIcon; ?> text-2xl"></i>
                            </span>
                            <div class="min-w-0 pr-2">
                                <p class="m-0 text-xl font-extrabold leading-7 text-slate-900">
                                    <?php echo $esError ? 'Atenci&oacute;n' : 'Operaci&oacute;n exitosa'; ?>
                                </p>
                                <p class="m-0 mt-1 text-lg font-semibold leading-7 text-slate-600">
                                    <?php echo $mensaje;?>
                                </p>
                            </div>
                        </div>
                        <div class="h-1 w-full <?php echo $barra; ?>"></div>
                    </div>
                    <?php
                }
            }
        ?>
    </div>
    <script>
        (() => {
            const alerts = document.querySelectorAll('[data-alert-toast]:not([data-alert-bound])');
            alerts.forEach((alert) => {
                alert.setAttribute('data-alert-bound', 'true');

                window.setTimeout(() => {
                    alert.classList.add('translate-x-6', 'opacity-0');
                    alert.classList.remove('translate-x-0', 'opacity-100');

                    window.setTimeout(() => {
                        alert.remove();
                    }, 320);
                }, 5000);
            });
        })();
    </script>
<?php endif; ?>

