<!-- MODAL PARA CREAR O ANADIR CLIENTE -->
<dialog id="miDialogoAddCliente" class="midialog-sm !p-0">
    <div class="max-h-[88vh] overflow-y-auto p-6 md:p-7">
        <div class="mb-5 flex items-start justify-between gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-indigo-100 text-indigo-700 shadow-sm">
                    <i class="fa-solid fa-user text-2xl"></i>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold uppercase tracking-[.18em] text-indigo-600">Cliente</p>
                    <h4 class="m-0 text-3xl font-bold leading-tight text-slate-900">Seleccionar o crear cliente</h4>
                    <p class="mt-1 text-lg text-slate-500">Busca un cliente existente o registra uno nuevo para esta venta.</p>
                </div>
            </div>
            <button type="button" class="salir grid h-11 w-11 shrink-0 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>

        <div class="space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-4 flex items-center gap-3">
                    <div class="grid h-11 w-11 place-items-center rounded-lg bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-magnifying-glass text-xl"></i>
                    </div>
                    <div>
                        <h5 class="text-xl font-bold text-slate-900">Buscar cliente existente</h5>
                        <p class="text-lg text-slate-500">Selecciona un cliente ya registrado.</p>
                    </div>
                </div>
                <select
                    id="selectCliente"
                    type="text"
                    name="selectCliente"
                    class="block min-h-[3.5rem] w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    multiple="multiple"
                    required
                >
                    <?php foreach($clientes as $cliente): ?>
                        <option data-tipoID="<?php echo $cliente->tipodocumento;?>" data-identidad="<?php echo $cliente->identificacion;?>" value="<?php echo $cliente->id;?>"><?php echo $cliente->nombre.' '.$cliente->apellido;?></option>
                    <?php endforeach ?>
                </select>
            </section>

            <form id="formAddCliente" class="formulario space-y-4" action="/" method="POST">
                <section class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="mb-4 flex items-center gap-3 border-b border-slate-200 pb-3">
                        <div class="grid h-11 w-11 place-items-center rounded-lg bg-indigo-100 text-indigo-700">
                            <i class="fa-solid fa-user-plus text-xl"></i>
                        </div>
                        <div>
                            <h5 class="text-2xl font-bold text-slate-900">Crear nuevo cliente</h5>
                            <p class="text-lg text-slate-500">Completa los datos principales del cliente.</p>
                        </div>
                    </div>

                    <div class="grid gap-x-4 gap-y-3 md:grid-cols-2">
                        <div class="formulario__campo">
                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="nombreclientenuevo">Nombre <span class="text-red-500">*</span></label>
                            <input id="nombreclientenuevo" type="text" name="nombreclientenuevo" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                        </div>

                        <div class="formulario__campo">
                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="clientenuevoapellido">Apellido</label>
                            <input id="clientenuevoapellido" type="text" name="clientenuevoapellido" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                        </div>

                        <div class="formulario__campo">
                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="identificacion">Documento</label>
                            <input id="identificacion" name="identificacion" type="text" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                        </div>

                        <div class="formulario__campo">
                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="telefono">Tel&eacute;fono <span class="text-red-500">*</span></label>
                            <input id="telefono" type="text" name="telefono" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                        </div>

                        <div class="formulario__campo md:col-span-2">
                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="direccionEntrega">Direcci&oacute;n de entrega</label>
                            <select id="direccionEntrega" type="text"
                                name="direccionEntrega"
                                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >
                                <option value="1" disabled selected>-Seleccionar-</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white px-4 pb-4">
                    <div class="accordion !mt-0">
                        <input type="checkbox" id="opcionesCliente">
                        <label class="etiqueta flex cursor-pointer items-center justify-between gap-3 border-b border-slate-200 py-4 text-left text-indigo-700" for="opcionesCliente">
                            <span class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-lg bg-indigo-100 text-indigo-700">
                                    <i class="fa-solid fa-sliders text-lg"></i>
                                </span>
                                <span>
                                    <span class="block text-xl font-bold leading-tight text-slate-900">Opciones adicionales</span>
                                    <span class="mt-1.5 block text-base font-medium leading-relaxed text-slate-500">Documento, correo y ubicaci&oacute;n del cliente.</span>
                                </span>
                            </span>
                        </label>

                        <div class="wrapper">
                            <div class="wrapper-content">
                                <div class="content mt-4">
                                    <div class="grid gap-x-4 gap-y-3 md:grid-cols-2">
                                        <div class="formulario__campo">
                                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="tipodocumento">Tipo documento</label>
                                            <select id="tipodocumento" name="tipodocumento" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                                                <option value="1">Registro civil</option>
                                                <option value="2">Tarjeta de identidad</option>
                                                <option value="3" selected>Cedula de ciudadania</option>
                                                <option value="4">Tarjeta de extranjeria</option>
                                                <option value="5">Cedula de extrangeria</option>
                                                <option value="6">NIT</option>
                                                <option value="7">Pasaporte</option>
                                                <option value="8">Documento de identificacion extranjero</option>
                                                <option value="9">NIT de otro pais</option>
                                                <option value="10">NUIP</option>
                                            </select>
                                        </div>

                                        <div class="formulario__campo">
                                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="clientenuevoemail">Email</label>
                                            <input id="clientenuevoemail" name="clientenuevoemail" type="email" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                                        </div>

                                        <div class="formulario__campo">
                                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="departamento">Departamento</label>
                                            <input id="departamento" type="text" name="departamento" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                                        </div>

                                        <div class="formulario__campo">
                                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="ciudad">Ciudad</label>
                                            <input id="ciudad" type="text" name="ciudad" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                                        </div>

                                        <div class="formulario__campo md:col-span-2">
                                            <label class="formulario__label mb-2 block text-lg font-semibold text-slate-700" for="clientenuevodireccion">Nueva direccion</label>
                                            <input id="clientenuevodireccion" type="text" name="clientenuevodireccion" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" <?= $conflocal['obligatorio_todos_los_campos_al_crear_cliente']->valor_final==1?'required':''; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-200 pt-4">
                    <button type="button" class="btn-md btn-turquoise salir !m-0 !w-full !py-4">Cancelar</button>
                    <input id="btnCrearAddCliente" type="submit" value="Guardar" class="btn-md btn-indigo !m-0 !w-full !py-4">
                </div>
            </form>
        </div>
    </div>
</dialog>