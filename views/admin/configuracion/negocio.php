<div class="mx-auto w-full">
    <div class="mb-5 flex flex-col items-start justify-between gap-6 rounded-xl border border-slate-200 bg-gradient-to-br from-indigo-600/10 to-cyan-500/5 p-6 md:flex-row md:items-center">
        <div>
            <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Datos del negocio</p>
            <h2 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Informaci&oacute;n del negocio</h2>
            <p class="mb-0 mt-1 text-lg text-slate-500">Actualiza la informaci&oacute;n que aparece en facturas, recibos y documentos impresos.</p>
        </div>
        <div class="flex size-32 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white p-3 md:size-36">
            <?php if(!empty($negocio->logo)): ?>
                <img class="h-full w-full object-contain" src="/build/img/<?php echo $negocio->logo; ?>" alt="Logo del negocio">
            <?php else: ?>
                <span class="material-symbols-outlined text-6xl text-indigo-600">storefront</span>
            <?php endif; ?>
        </div>
    </div>

    <form class="formulario gap-6" action="/admin/configuracion/editarnegocio" enctype="multipart/form-data" method="POST">
        <section class="border border-slate-200 px-5 py-5 rounded-xl bg-gradient-to-br from-indigo-600/5 to-cyan-500/5">
            <div class="flex flex-col md:flex-row gap-5 justify-between md:items-center">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-600/10 text-4xl text-indigo-600 font-medium">store</span>
                    <div>
                        <p class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Nueva sede</p>
                        <h3 class="text-slate-900 text-2xl leading-6 font-bold">Crear sucursal o sede</h3>
                        <span class="mt-1 text-lg leading-snug text-slate-500">Registra puntos de operaci&oacute;n para cajas, ventas, emisores y facturadores.</span>
                    </div>
                </div>
                <button id="abrirNuevaSede" class="btnDialog btnDialog_primary" type="button"><i class="fa-solid fa-plus"></i>Crear sede</button>
            </div>

            <div class="flex items-center gap-3 mt-4 ml-20">
                <span class="material-symbols-outlined text-indigo-600 text-3xl">account_tree</span>
                <p class="text-slate-500 text-lg m-0">Crea una sede independiente sin modificar los datos principales del negocio.</p>
            </div>
        </section>

        <section class="border border-slate-200 p-6 rounded-xl">
            <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
                <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium">badge</span>
                <div>
                    <h3 class="text-slate-900 text-2xl font-bold">Identificaci&oacute;n</h3>
                    <p class="my-0 text-lg leading-snug text-slate-500">Nombre comercial, sede y datos tributarios.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="formulario__campo ">
                    <label class="formulario__label" for="nombreEmpresa">Empresa</label>
                    <input id="nombreEmpresa" class="formulario__input" type="text" placeholder="Nombre de la empresa" name="negocio" value="<?php echo $negocio->negocio??''; ?>" required>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="nombreSucursal">Nombre de la sucursal</label>
                    <input id="nombreSucursal" class="formulario__input" type="text" placeholder="Nombre de la sucursal" name="nombre" value="<?php echo $negocio->nombre??''; ?>" required>
                </div>

                <div class="formulario__campo col-span-full">
                    <label class="formulario__label" for="datosencabezados">Datos del RUT</label>
                    <textarea id="datosencabezados" class="formulario__textarea formulario__textarea--textarea" name="datosencabezados" placeholder="Datos de encabezado de la factura" rows="4"><?php echo $negocio->datosencabezados ?? '';?></textarea>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="nitEmpresa">NIT</label>
                    <input id="nitEmpresa" class="formulario__input" type="text" placeholder="NIT del negocio" name="nit" value="<?php echo $negocio->nit ?? '';?>" required>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="host">HOST</label>
                    <input id="host" class="formulario__input" type="text" placeholder="Prefijo o subdominio de la cuenta" name="host" value="<?php echo $negocio->host??'';?>">
                </div>
            </div>
        </section>

        <section class="border border-slate-200 p-6 rounded-xl">
            <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
                <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium">location_on</span>
                <div>
                    <h3 class="text-slate-900 text-2xl font-bold">Ubicaci&oacute;n y contacto</h3>
                    <p class="my-0 text-lg leading-snug text-slate-500">Datos de atenci&oacute;n visibles para clientes y reportes.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="formulario__campo">
                    <label class="formulario__label" for="ciudadSucursal">Ciudad</label>
                    <input id="ciudadSucursal" class="formulario__input" type="text" placeholder="Ciudad del negocio" name="ciudad" value="<?php echo $negocio->ciudad ?? '';?>" required>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="direccionSucursal">Direcci&oacute;n</label>
                    <input id="direccionSucursal" class="formulario__input" type="text" placeholder="Direcci&oacute;n del negocio" name="direccion" value="<?php echo $negocio->direccion ?? '';?>" required>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="telefonoSucursal">Tel&eacute;fono</label>
                    <input id="telefonoSucursal" class="formulario__input" type="number" placeholder="Tel&eacute;fono fijo de contacto" name="telefono" value="<?php echo $negocio->telefono ?? '';?>">
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="celularSucursal">Celular</label>
                    <input id="celularSucursal" class="formulario__input" type="number" min="3000000000" max="3777777777" placeholder="M&oacute;vil de contacto" name="movil" value="<?php echo $negocio->movil ?? '';?>" required>
                </div>

                <div class="formulario__campo col-span-full">
                    <label class="formulario__label" for="emailSucursal">Correo electr&oacute;nico</label>
                    <input id="emailSucursal" class="formulario__input" type="email" placeholder="Correo electr&oacute;nico" name="email" value="<?php echo $negocio->email ?? '';?>" required>
                </div>
            </div>
        </section>

        <section class="border border-slate-200 p-6 rounded-xl">
            <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
                <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium">share</span>
                <div>
                    <h3 class="text-slate-900 text-2xl font-bold">Presencia digital</h3>
                    <p class="my-0 text-lg leading-snug text-slate-500">Canales sociales, QR alternativo y logo del negocio.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="formulario__campo">
                    <label class="formulario__label" for="QREmpresa">www / QR alternativo</label>
                    <input id="QREmpresa" class="formulario__input" type="text" placeholder="Texto o link a imprimir en QR" name="www" value="<?php echo $negocio->www??'';?>">
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="wsNegocio">Whatsapp</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex w-20 shrink-0 items-center justify-center rounded-l-lg bg-green-600 text-3xl text-white"><i class="fa-brands fa-whatsapp"></i></span>
                        <input id="wsNegocio" class="formulario__input formulario__input--sociales" type="number" min="3000000000" max="3777777777" name="ws" placeholder="Whatsapp" value="<?php echo $negocio->ws ?? '';?>">
                    </div>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="facebookNegocio">Facebook</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex w-20 shrink-0 items-center justify-center rounded-l-lg bg-blue-600 text-3xl text-white"><i class="fa-brands fa-facebook"></i></span>
                        <input id="facebookNegocio" class="formulario__input formulario__input--sociales" type="text" name="facebook" placeholder="Facebook" value="<?php echo $negocio->facebook ?? ''; ?>">
                    </div>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="instagramNegocio">Instagram</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex w-20 shrink-0 items-center justify-center rounded-l-lg bg-pink-600 text-3xl text-white"><i class="fa-brands fa-instagram"></i></span>
                        <input id="instagramNegocio" class="formulario__input formulario__input--sociales" type="text" name="instagram" placeholder="Instagram" value="<?php echo $negocio->instagram ?? ''; ?>">
                    </div>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="youtubeNegocio">YouTube</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex w-20 shrink-0 items-center justify-center rounded-l-lg bg-red-600 text-3xl text-white"><i class="fa-brands fa-youtube"></i></span>
                        <input id="youtubeNegocio" class="formulario__input formulario__input--sociales" type="text" name="youtube" placeholder="Youtube" value="<?php echo $negocio->youtube ?? ''; ?>">
                    </div>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="logo">Logo</label>
                    <input id="logo" class="formulario__input formulario__input--filelogo" type="file" accept="image/*" name="logo">
                    <?php if(!empty($negocio->logo)): ?>
                        <small class="mt-1 block text-base text-slate-500"><?php echo $negocio->logo;?></small>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="flex justify-stretch pt-1 md:justify-end">
            <button class="btnDialog btnDialog_primary" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                Actualizar
            </button>
        </div>
    </form>

    <dialog id="miDialogoNuevaSede" class="detalledialog_md">
        <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
            <span class="material-symbols-outlined inline-flex size-20 shrink-0 items-center justify-center rounded-lg bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">store</span>
            <div>
                <p class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Sede</p>
                <h3 class="text-slate-900 text-3xl leading-6 font-bold">Crear nueva sucursal</h3>
                <span class="mt-1 text-lg leading-snug text-slate-500">Completa los datos de contacto y operaci&oacute;n de la nueva sede.</span>
            </div>
        </div>

        <form class="formulario--grid" action="#" method="POST" onsubmit="return false;">
            <div class="formulario__campo">
                <label class="formulario__label" for="nombreNuevaSede">Nombre de la sede</label>
                <input id="nombreNuevaSede" class="formulario__input" type="text" name="nombre_sede" placeholder="Ej: Sede norte">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="ciudadNuevaSede">Ciudad</label>
                <input id="ciudadNuevaSede" class="formulario__input" type="text" name="ciudad_sede" placeholder="Ciudad de operaci&oacute;n">
            </div>

            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="direccionNuevaSede">Direcci&oacute;n</label>
                <input id="direccionNuevaSede" class="formulario__input" type="text" name="direccion_sede" placeholder="Direcci&oacute;n de la sede">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="telefonoNuevaSede">Tel&eacute;fono</label>
                <input id="telefonoNuevaSede" class="formulario__input" type="number" name="telefono_sede" placeholder="Tel&eacute;fono fijo">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="celularNuevaSede">Celular / WhatsApp</label>
                <input id="celularNuevaSede" class="formulario__input" type="number" min="3000000000" max="3777777777" name="celular_sede" placeholder="M&oacute;vil de contacto">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="emailNuevaSede">Correo</label>
                <input id="emailNuevaSede" class="formulario__input" type="email" name="email_sede" placeholder="correo@sede.com">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="responsableNuevaSede">Responsable</label>
                <input id="responsableNuevaSede" class="formulario__input" type="text" name="responsable_sede" placeholder="Encargado de la sede">
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="estadoNuevaSede">Estado</label>
                <select id="estadoNuevaSede" class="formulario__input" name="estado_sede">
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </select>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="codigoNuevaSede">C&oacute;digo interno</label>
                <input id="codigoNuevaSede" class="formulario__input" type="text" name="codigo_sede" placeholder="Opcional">
            </div>

            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="observacionesNuevaSede">Observaciones</label>
                <textarea id="observacionesNuevaSede" class="formulario__textarea formulario__textarea--textarea !min-h-32" name="observaciones_sede" placeholder="Notas internas de la sede" rows="3"></textarea>
            </div>

            <div class="formulario__contenedorBtns--grid">
                <button id="cerrarNuevaSede" class="btnDialog btnDialog_light" type="button">Salir</button>
                <button class="btnDialog btnDialog_primary" type="button">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar sede
                </button>
            </div>
        </form>
    </dialog>
</div>

<script>
    (() => {
        const abrirNuevaSede = document.querySelector('#abrirNuevaSede');
        const miDialogoNuevaSede = document.querySelector('#miDialogoNuevaSede');
        const cerrarNuevaSede = document.querySelector('#cerrarNuevaSede');

        abrirNuevaSede?.addEventListener('click', () => miDialogoNuevaSede?.showModal());
        cerrarNuevaSede?.addEventListener('click', () => miDialogoNuevaSede?.close());
        miDialogoNuevaSede?.addEventListener('click', (event) => {
            if(event.target === miDialogoNuevaSede) {
                miDialogoNuevaSede.close();
            }
        });
    })();
</script>
