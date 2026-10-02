<div class="empleados">
    <section>
        <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">groups</span>
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de empleados</h2>
                    <p class="my-0 text-lg leading-snug text-slate-500">Administra usuarios, perfiles de acceso y credenciales del equipo.</p>
                </div>
            </div>
            <button id="crearempleado" class="btnDialog btnDialog_primary">
                <i class="fa-solid fa-plus"></i>
                Crear empleado
            </button>
        </div>

        <div class="datatable-card config-table-card">
            <table id="tablaempleados" class="display responsive nowrap tabla datatable-table" width="100%">
                <thead>
                    <tr>
                        <th>N.</th>
                        <th>Nombre</th>
                        <th>Imagen</th>
                        <th>Usuario</th>
                        <th>Perfil</th>
                        <th class="accionesth">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($empleados as $index => $value): 
                        if($value->perfil!=1): ?>
                        <tr> 
                            <td class=""><?php echo $index+1;?></td>        
                            <td class="config-employee-name"><?php echo $value->nombre.' '.$value->apellido;?></td> 
                            <td class="" ><div class="config-employee-avatar" data-initials="<?php echo strtoupper(substr($value->nombre, 0, 1).substr($value->apellido ?? '', 0, 1));?>"><img src="/build/img/<?php echo $value->img;?>" alt="" onerror="this.style.display='none';"></div></td> 
                            <td class="config-employee-user"><?php echo $value->nickname;?></td>
                            <td class=""><span class="config-profile-badge"><?php echo $value->perfil==1?'root':($value->perfil==2?'Superior':($value->perfil==3?'Administrador':'Asesor'));?></span></td>
                            <td class="accionestd">
                                <div class="acciones-btns" id="<?php echo $value->id;?>" data-empleado="<?php echo $value->nombre.' '.$value->apellido;?>">
                                    <button class="btn-md btn-turquoise editarEmpleado" title="Actualizar datos empleados"><i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-md btn-lima updatePassword" title="Cambiar contraseña"><i class="fa-solid fa-key"></i>
                                    </button>
                                    <button class="btn-md btn-red eliminarEmpleado" title="Eliminar empleado"><i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <dialog class="detalledialog_md config-empleado-dialog" id="miDialogoEmpleado">
        <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
            <span class="material-symbols-outlined inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">person_add</span>
            <div>
                <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Empleado</p>
                <h4 id="modalEmpleado" class="text-3xl font-bold leading-6 text-slate-900">Crear empleado</h4>
                <small class="mt-1 block text-lg leading-snug text-slate-500">Registra los datos de acceso, perfil e imagen del empleado.</small>
            </div>
        </div>
        <div id="divmsjalertaempleado1" class="px-8"></div>
        <form id="formCrearUpdateEmpleado" class="formulario px-8 pb-8" action="/admin/configuracion/crear_empleado" enctype="multipart/form-data" method="POST">
            
                <div class="formulario__campo config-empleado-dialog__photo">
                    <div class="formulario__contentinputfile">
                        <div class="formulario__imginputfile"><img id="imginputfile" src="" alt=""></div>
                        <p class="text-greymouse">Subir imagen</p>
                    </div>
                    <input id="upImage" class="formulario__inputfile" type="file" accept="image/*" name="img" hidden>
                    <button id="customUpImage" class="config-empleado-dialog__upload" type="button">
                        <i class="fa-solid fa-camera"></i>
                        Cargar imagen
                    </button>
                </div>
            <div class="grid grid-cols-1 gap-x-6 gap-y-1 sm:grid-cols-6">
                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label flex items-center gap-1 group relative" for="nombre">
                        Nombre
                        <!-- icono obligatorio -->
                        <span class="text-red-500 font-bold cursor-help">⚠️</span>

                        <!-- tooltip -->
                        <span
                        class="absolute left-1/2 -translate-x-1/2 -top-8 bg-gray-800 text-white text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Campo obligatorio
                        </span>
                    </label>

                    <div class="formulario__dato">
                        <input
                        id="nombreempleado"
                        class="formulario__input"
                        type="text"
                        placeholder="Nombre del empleado"
                        name="nombre"
                        value="<?php echo $empleado->nombre??'';?>"
                        required
                        >
                    </div>
                </div>

                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="apellido">Apellido</label>
                    <div class="formulario__dato">
                        <input id="apellidoempleado" class="formulario__input" type="text" placeholder="Apellido del empleado" name="apellido" value="<?php echo $empleado->apellido??'';?>">
                        <!-- <label data-num="42" class="count-charts" for="">42</label> -->
                    </div>
                </div>

                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label flex items-center gap-1 group relative" for="nickname">
                        Usuario
                        <!-- icono obligatorio -->
                        <span class="text-red-500 font-bold cursor-help">⚠️</span>
                        <!-- tooltip -->
                        <span
                            class="absolute left-1/2 -translate-x-1/2 -top-8 bg-gray-800 text-white text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            Campo obligatorio
                        </span>
                    </label>

                    <div class="formulario__dato">
                        <input
                        id="nicknameempleado"
                        class="formulario__input"
                        type="text"
                        placeholder="Usuario del empleado"
                        name="nickname"
                        value="<?php echo $empleado->nickname??'';?>"
                        required
                        >
                    </div>
                </div>

                <!--<div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="cedula">Cédula</label>
                    <div class="formulario__dato">
                        <input id="cedulaempleado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Cedula del empleado" name="cedula" value="<?php //echo $empleado->cedula??'';?>">-->
                        <!-- <label data-num="42" class="count-charts" for="">42</label> -->
                    <!--</div>
                </div>-->

                <div id="campopass1" class="formulario__campo sm:col-span-3">
                    <label class="formulario__label flex items-center gap-1 group relative" for="password">
                        Contraseña
                        <!-- icono obligatorio -->
                        <span class="text-red-500 font-bold cursor-help">⚠️</span>
                        <span
                            class="absolute left-1/2 -translate-x-1/2 -top-8 bg-gray-800 text-white text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            Campo obligatorio
                        </span>
                    </label>

                    <div class="formulario__dato">
                        <input
                        id="passwordempleado"
                        class="formulario__input"
                        type="password"
                        placeholder="Contraseña de sesión"
                        name="password"
                        value="<?php echo $empleado->password??'';?>"
                        required
                        >
                    </div>
                </div>

                <div id="campopass2" class="formulario__campo sm:col-span-3">
                    <label class="formulario__label flex items-center gap-1 group relative" for="contraseña2">
                        Confirmar contraseña
                        <!-- icono obligatorio -->
                        <span class="text-red-500 font-bold cursor-help">⚠️</span>
                        <span
                        class="absolute left-1/2 -translate-x-1/2 -top-8 bg-gray-800 text-white text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Campo obligatorio
                        </span>
                    </label>

                    <div class="formulario__dato">
                        <input
                        id="passwordempleado2"
                        class="formulario__input"
                        type="password"
                        placeholder="Confirmar contraseña"
                        name="password2"
                        value="<?php echo $empleado->contraseña2??'';?>"
                        required
                        >
                    </div>
                </div>

                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="movil">Teléfono</label>
                    <div class="formulario__dato">
                        <input id="movilempleado" class="formulario__input" type="number" min="3000000000" max="3777777777" placeholder="Tu Movil" name="movil" value="<?php echo $empleado->movil??'';?>">
                    </div>
                </div>

                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="porcentajeganancia">Porcentaje de ganancia (%)</label>
                    <div class="formulario__dato">
                        <input
                            id="porcentajeganancia"
                            class="formulario__input"
                            type="number"
                            min="00"
                            max="100"
                            placeholder="Porcentaje de ganancia por venta"
                            name="porcentajeganancia"
                            value="<?php echo $empleado->porcentajeganancia??'';?>"
                        >
                    </div>
                </div>
                <!--<div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="email">Email</label>
                    <div class="formulario__dato">
                        <input id="emailempleado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" type="email" placeholder="Tu Email" name="email" value="<?php //echo $empleado->email??'';?>">
                    </div>
                </div>-->
                <!--<div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="direccion">Dirección</label>
                    <div class="formulario__dato">
                        <input id="direccionempleado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Direccion de  residencia" name="direccion" value="<?php //echo $empleado->direccion??'';?>">-->
                        <!-- <label data-num="90" class="count-charts" for="">90</label> -->
                    <!--</div>
                </div>-->
                <!--<div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="departamento">Departamento</label>
                    <div class="formulario__dato">
                        <input id="departamentoempleado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="departamento" name="departamento" value="<?php //echo $empleado->departamento??''; ?>">
                    </div>
                </div>-->
                <!--<div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label" for="ciudad">Ciudad</label>
                    <div class="formulario__dato">
                        <input id="ciudadempleado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="ciudad" name="ciudad" value="<?php //echo $empleado->ciudad??'';?>">-->
                        <!-- <label data-num="14" class="count-charts" for="">14</label> -->
                    <!--</div>
                </div>-->
                <div class="formulario__campo sm:col-span-3">
                    <label class="formulario__label flex items-center gap-1 group relative" for="perfil">
                        Perfil
                        <!-- icono obligatorio -->
                        <span class="text-red-500 font-bold cursor-help">⚠️</span>

                        <!-- tooltip -->
                        <span
                        class="absolute left-1/2 -translate-x-1/2 -top-8 bg-gray-800 text-white text-xs rounded-md px-2 py-1 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        Campo obligatorio
                        </span>
                    </label>
                    
                    <select id="perfilempleado" class="formulario__select" name="perfil"required>
                        <option value="" disabled selected>-Seleccionar-</option>
                        <?php if($user['perfil']<3): ?>
                            <option value="2" <?php echo $empleado->perfil==2?'selected':'';?>>Supervisor</option>
                        <?php endif ?>
                        <?php if($user['perfil']<4): ?>
                            <option value="3" <?php echo $empleado->perfil==3?'selected':'';?>>Administrador</option>
                        <?php endif ?>
                        <option value="4" <?php echo $empleado->perfil==4?'selected':'';?>>Asesor</option>
                        
                    </select>
                </div>
            </div>

            <div id="contentpermisos" style="display: none;">
            <!-- Módulo de Ventas -->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de Ventas</p>

                    <label for="habilitarmoduloventa" class="flex items-center justify-between cursor-pointer mb-3">
                    <span class="text-gray-600 text-xl ">Habilitar módulo de venta</span>
                    <input id="habilitarmoduloventa" name="permisos[]" value="1" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="registrarventa" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Registrar nueva venta</span>
                        <input id="registrarventa" name="permisos[]" value="2" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="anularventa" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Anular una venta</span>
                        <input id="anularventa" name="permisos[]" value="3" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="aplicardescuentos" class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-600  text-xl">Aplicar descuentos</span>
                        <input id="aplicardescuentos" name="permisos[]" value="4" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>

                <!-- Módulo dashboard -->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de Inicio</p>

                    <label for="habilitarmoduloinicio" class="flex items-center justify-between cursor-pointer mb-3">
                    <span class="text-gray-600 text-xl ">Habilitar módulo de inicio</span>
                    <input id="habilitarmoduloinicio" name="permisos[]" value="18" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>

                <!-- Módulo de Inventario -->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de Inventario</p>

                    <label for="habilitarmoduloinventario" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Habilitar módulo de inventario</span>
                        <input id="habilitarmoduloinventario" name="permisos[]" value="5" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="consultarstock" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Consultar stock</span>
                        <input id="consultarstock" name="permisos[]" value="6" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="ajustarinventario" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Hacer ajustes de inventario</span>
                        <input id="ajustarinventario" name="permisos[]" value="7" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="actualizarprecios" class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-600  text-xl">Actualizar precios o datos</span>
                        <input id="actualizarprecios" name="permisos[]" value="8" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>

                <!-- Módulo de Facturación -->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de Facturación</p>

                    <label for="habilitarmodulofacturacion" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600 ">Habilitar módulo de facturación</span>
                        <input id="habilitarmodulofacturacion" name="permisos[]" value="9" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="emitirfacturaselectronicas" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Emitir facturas electrónicas</span>
                        <input id="emitirfacturaselectronicas" name="permisos[]" value="10" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="eliminarfacturas" class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-600  text-xl">Anular facturas y notas créditos</span>
                        <input id="eliminarfacturas" name="permisos[]" value="11" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>

                <!-- Módulo de Caja -->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de Caja</p>

                    <label for="habilitarmodulocaja" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Habilitar módulo de caja</span>
                        <input id="habilitarmodulocaja" name="permisos[]" value="12" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="abrircaja" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Abrir caja registradora</span>
                        <input id="abrircaja" name="permisos[]" value="13" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="cerrarcaja" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Cerrar caja y generar arqueo</span>
                        <input id="cerrarcaja" name="permisos[]" value="14" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="verdetallecaja" class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-600 ">Ver detalle de la caja</span>
                        <input id="verdetallecaja" name="permisos[]" value="15" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>

                <!-- Módulo de Separados/Credito-->
                <div class="p-4 border border-gray-200 rounded-lg moduloventas mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulo de separados/creditos</p>

                    <label for="habilitarmoduloseparados" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Habilitar módulo de credito/separados</span>
                        <input id="habilitarmoduloseparados" name="permisos[]" value="19" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="editarseparados" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Editar separados activos</span>
                        <input id="editarseparados" name="permisos[]" value="20" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="eliminarseparados" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Eliminar separados</span>
                        <input id="eliminarseparados" name="permisos[]" value="21" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div id="contentpermisosadmin" style="display: none;">
                <div class="p-4 border border-gray-200 rounded-lg moduloadmin mb-4">
                    <p class="text-slate-600 text-xl font-bold mb-4">Módulos Administrativos</p>

                    <label for="habilitarreportes" class="flex items-center justify-between cursor-pointer mb-3">
                        <span class="text-gray-600  text-xl">Habilitar módulo de reportes</span>
                        <input id="habilitarreportes" name="permisos[]" value="16" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>

                    <label for="habilitarconfiguracion" class="flex items-center justify-between cursor-pointer">
                        <span class="text-gray-600  text-xl">Habilitar módulo de configuración</span>
                        <input id="habilitarconfiguracion" name="permisos[]" value="17" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 relative transition">
                            <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_light" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEditarCrearEmpleado" class="btnDialog btnDialog_primary" type="submit" value="Crear">
            </div>
        </form>
    </dialog><!--fin crear empleado-->

    <dialog class="detalledialog_xs" id="miDialogoContraseña">
        <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
            <div class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Credenciales</p>
                <h4 class="text-3xl font-bold leading-6 text-slate-900">Cambiar contrase&ntilde;a</h4>
                <small class="mt-1 block text-lg leading-snug text-slate-500">Actualiza la clave de acceso del empleado seleccionado.</small>
            </div>
        </div>
        <div id="divmsjalertaempleado2" class="px-8"></div>
        <form id="formContraseña" class="formulario gap-4 px-8 pb-8 pt-6" method="POST">
            <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-user-lock"></i></span>
                <div>
                    <small class="block text-base font-extrabold uppercase text-slate-500">Empleado</small>
                    <h5 id="nombreEmpleadoPass" class="mt-1 text-xl font-extrabold text-slate-900">Julian Rodriguez</h5>
                </div>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="changePassword">Nueva contraseña</label>
                <input id="changePassword" class="formulario__input" type="password" placeholder="Ingrese la nueva contraseña" value="" required>
            </div>
            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_light" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEnviarContrasela" class="btnDialog btnDialog_primary" type="submit" value="Actualizar">
            </div>
        </form>
    </dialog><!--fin Act/ediar contraseña por empleado-->
</div>
