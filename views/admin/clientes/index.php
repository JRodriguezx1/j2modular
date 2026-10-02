<div class="box clientes [@media(max-width:991px)]:!pb-[calc(10rem+env(safe-area-inset-bottom))]">
  
    <?php include __DIR__. "/../../templates/alertas.php"; ?>

    <header class="flex flex-col items-stretch gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 mb-6 p-4 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="mb-1 text-base font-extrabold uppercase text-indigo-600">CRM</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Gesti&oacute;n de clientes</h1>
        <p class="mt-1 text-lg leading-snug text-slate-500">Administra clientes, direcciones, estad&iacute;sticas y precios personalizados desde una vista m&aacute;s clara.</p>
      </div>

      <div class="flex justify-start gap-4 lg:justify-end rounded-lg border border-slate-200 bg-white/90 p-4">
        <span class="material-symbols-outlined inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-3xl text-white"><i class="fa-solid fa-users"></i></span>
        <div>
          <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900"><?php echo count($clientes); ?></strong>
          <small class="mt-1 block text-base font-bold text-slate-500">clientes registrados</small>
        </div>
      </div>
    </header>

    <section class="flex flex-col items-stretch gap-3 mb-6 rounded-lg border border-slate-200 bg-white p-4 sm:flex-row sm:flex-wrap sm:items-center">
      <button id="crearCliente" class="btnDialog btnDialog_primary" type="button">
        <i class="fa-solid fa-user-plus"></i>
        Crear cliente
      </button>

      <button id="crearDireccion" class="btnDialog btnDialog_secondary" type="button">
        <i class="fa-solid fa-location-dot"></i>
        Crear direcci&oacute;n
      </button>

      <a id="marketing" href="/admin/clientes/marketing" class="btnDialog btnDialog_light">
        <i class="fa-solid fa-bullhorn"></i>
        Marketing
      </a>
    </section>

    <section class="datatable-card config-table-card overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6 [@media(max-width:991px)]:[&_.dt-paging]:!p-3">
      <div class="flex flex-col items-start justify-between gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center">
        <div>
          <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Directorio de clientes</h2>
          <p class="mt-1 text-base text-slate-500" >Consulta datos de contacto y gestiona acciones r&aacute;pidas por cliente.</p>
        </div>
      </div>

      <table id="tablaClientes" class="display responsive nowrap tabla datatable-table" width="100%">
        <thead>
          <tr>
            <th>id</th>
            <th>Documento</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Tel&eacute;fono</th>
            <th>Email</th>
            <th class="accionesth">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($clientes as $cliente): ?>
            <?php if($cliente->id != 1): ?>
              <tr>
                <td><?php echo $cliente->id; ?></td>
                <td><span class="table-badge table-badge--neutral"><?php echo $cliente->identificacion; ?></span></td>
                <td>
                  <span class="table-entity">
                    <span class="table-entity__icon"><i class="fa-solid fa-user"></i></span>
                    <span><?php echo $cliente->nombre; ?></span>
                  </span>
                </td>
                <td><?php echo $cliente->apellido; ?></td>
                <td>
                  <span class="table-badge table-badge--info"><?php echo $cliente->telefono; ?></span>
                </td>
                <td>
                  <span class="table-badge table-badge--neutral"><?php echo $cliente->email; ?></span>
                </td>
                <td class="accionestd">
                  <div class="acciones-btns" id="<?php echo $cliente->id;?>">
                    <button class="table-action bg-cyan-500 text-white hover:text-white editarClientes" type="button" title="Actualizar datos del cliente"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="table-action table-action--print editarEliminarDireccion" type="button" title="Actualizar direcci&oacute;n del cliente"><i class="fa-solid fa-location-dot"></i></button>
                    <a class="table-action table-action--view" href="/admin/clientes/detalle?id=<?php echo $cliente->id;?>" title="Ver estad&iacute;sticas del cliente"><i class="fa-solid fa-chart-simple"></i></a>
                    <a class="table-action bg-indigo-600 text-white hover:text-white" href="/admin/clientes/preciosXCliente?id=<?php echo $cliente->id;?>" title="Precios personalizados"><i class="fa-solid fa-dollar-sign"></i></a>
                    <button class="table-action table-action--danger eliminarClientes" type="button" title="Eliminar cliente"><i class="fa-solid fa-trash-can"></i></button>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <dialog class="max-h-[92vh] w-[min(94vw,72rem)] max-w-[72rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]" id="miDialogoCliente">
      <div class="flex items-center gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-user-plus"></i></span>
        <div class="min-w-0 flex-1">
          <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Cliente</p>
          <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalCliente">Crear cliente</h4>
          <small class="mt-1 block text-base leading-snug text-slate-500">Registra los datos principales y de contacto del cliente.</small>
        </div>
      </div>

      <div id="divmsjalerta1"></div>

      <form id="formCrearUpdateCliente" class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6" action="/admin/clientes/crear" method="POST">
        <div class="form-field">
          <label for="nombre">Nombre</label>
          <div class="form-input">
            <span><i class="fa-solid fa-user"></i></span>
            <input type="text" placeholder="Nombre del cliente" id="nombre" name="nombre" value="<?php echo $crearcliente->nombre ?? ''; ?>" required>
          </div>
        </div>

        <div class="form-field">
          <label for="apellido">Apellido</label>
          <div class="form-input">
            <span><i class="fa-solid fa-user-tag"></i></span>
            <input type="text" placeholder="Apellido del cliente" id="apellido" name="apellido" value="<?php echo $crearcliente->apellido ?? ''; ?>">
          </div>
        </div>

        <div class="form-field">
          <label for="tipodocumento">Tipo de documento</label>
          <div class="form-input">
            <span><i class="fa-solid fa-id-card"></i></span>
            <select id="tipodocumento" name="tipodocumento" required>
              <option value="1">Registro civil</option>
              <option value="2">Tarjeta de identidad</option>
              <option value="3" selected>C&eacute;dula de ciudadan&iacute;a</option>
              <option value="4">Tarjeta de extranjer&iacute;a</option>
              <option value="5">C&eacute;dula de extranjer&iacute;a</option>
              <option value="6">NIT</option>
              <option value="7">Pasaporte</option>
              <option value="8">Documento de identificaci&oacute;n extranjero</option>
              <option value="9">NIT de otro pa&iacute;s</option>
              <option value="10">NUIP</option>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="identificacion">Identificaci&oacute;n</label>
          <div class="form-input">
            <span><i class="fa-solid fa-fingerprint"></i></span>
            <input type="text" min="0" placeholder="Identificaci&oacute;n del cliente" id="identificacion" name="identificacion" value="<?php echo $crearcliente->identificacion ?? ''; ?>">
          </div>
        </div>

        <div class="form-field">
          <label for="telefono">Tel&eacute;fono</label>
          <div class="form-input">
            <span><i class="fa-solid fa-mobile-screen-button"></i></span>
            <input type="text" minlength="7" placeholder="Tel&eacute;fono del cliente" id="telefono" name="telefono" value="<?php echo $crearcliente->telefono ?? ''; ?>" required>
          </div>
        </div>

        <div class="form-field">
          <label for="email">Correo electr&oacute;nico</label>
          <div class="form-input">
            <span><i class="fa-solid fa-at"></i></span>
            <input type="email" placeholder="Ingresa correo electr&oacute;nico" id="email" name="email" value="<?php echo $crearcliente->email ?? ''; ?>">
          </div>
        </div>

        <div class="form-field sm:col-span-2">
          <label for="fecha_nacimiento">Fecha de nacimiento</label>
          <div class="form-input">
            <span><i class="fa-solid fa-cake-candles"></i></span>
            <input type="date" placeholder="Fecha de nacimiento del cliente" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $crearcliente->fecha_nacimiento ?? ''; ?>">
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-5 sm:col-span-2 sm:grid-cols-2">
          <button class="btnDialog btnDialog_light w-full" type="button" value="salir">Salir</button>
          <input id="btnEditarCrearCliente" class="btnDialog btnDialog_primary w-full" type="submit" value="Crear">
        </div>
      </form>
    </dialog>

    <dialog class="max-h-[92vh] w-[min(94vw,72rem)] max-w-[72rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]" id="miDialogoCrearDireccion">
      <div class="flex items-center gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-map-location-dot"></i></span>
        <div class="min-w-0 flex-1">
          <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Direcci&oacute;n</p>
          <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalDireccion">Crear direcci&oacute;n</h4>
          <small class="mt-1 block text-base leading-snug text-slate-500">Asocia una direcci&oacute;n, ciudad y tarifa a un cliente.</small>
        </div>
      </div>

      <div id="divmsjalerta2"></div>

      <form id="formCrearUpdateDireccion" class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6" action="/admin/direcciones/crear" method="POST">
        <div class="form-field sm:col-span-2">
          <label for="selectcliente">Seleccionar cliente</label>
          <div class="select2-field select2-field--standalone">
            <select id="selectcliente" name="idcliente" required>
              <?php foreach($clientes as $cliente): ?>
                <?php if($cliente->id > 1): ?>
                  <option value="<?php echo $cliente->id;?>"><?php echo $cliente->nombre.' '.$cliente->apellido;?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="tarifa">Tarifa</label>
          <div class="form-input">
            <span><i class="fa-solid fa-tags"></i></span>
            <select id="tarifa" name="idtarifa" required>
              <?php foreach($tarifas as $tarifa): ?>
                <option value="<?php echo $tarifa->id;?>"><?php echo $tarifa->nombre;?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="departamento">Departamento</label>
          <div class="form-input">
            <span><i class="fa-solid fa-map"></i></span>
            <input type="text" placeholder="Departamento o regi&oacute;n" id="departamento" name="departamento" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="ciudad">Ciudad</label>
          <div class="form-input">
            <span><i class="fa-solid fa-city"></i></span>
            <input type="text" placeholder="Ciudad de residencia" id="ciudad" name="ciudad" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="direccion">Direcci&oacute;n</label>
          <div class="form-input">
            <span><i class="fa-solid fa-location-dot"></i></span>
            <input type="text" placeholder="Direcci&oacute;n de vivienda" id="direccion" name="direccion" value="">
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-5 sm:col-span-2 sm:grid-cols-2">
          <button class="btnDialog btnDialog_light w-full" type="button" value="salir">Salir</button>
          <input id="btnEditarCrearDireccion" class="btnDialog btnDialog_primary w-full" type="submit" value="Crear">
        </div>
      </form>
    </dialog>

    <dialog class="max-h-[92vh] w-[min(94vw,72rem)] max-w-[72rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]" id="miDialogoUpDireccion">
      <div class="flex items-start gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-location-crosshairs"></i></span>
        <div class="min-w-0 flex-1">
          <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Direcci&oacute;n</p>
          <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalUpDireccion">Actualizar direcci&oacute;n</h4>
          <small class="mt-1 block text-base leading-snug text-slate-500">Edita o elimina una direcci&oacute;n asociada al cliente seleccionado.</small>
        </div>
        <button id="btnCerrarUpDireccion" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" type="button" title="Cerrar">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div id="divmsjalerta3"></div>

      <form id="formUpDireccion" class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6" action="/admin/direccions/actualizar" method="POST">
        <div class="form-field sm:col-span-2">
          <label for="selectdirecciones">Seleccionar direcciones</label>
          <div class="form-input">
            <span><i class="fa-solid fa-route"></i></span>
            <select id="selectdirecciones" name="direcciones" required>
              <option value="" disabled selected>-Seleccionar-</option>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="uptarifa">Tarifa</label>
          <div class="form-input">
            <span><i class="fa-solid fa-tags"></i></span>
            <select id="uptarifa" name="tarifa" required>
              <option value="" disabled selected>-Seleccionar-</option>
              <?php foreach($tarifas as $tarifa): ?>
                <option value="<?php echo $tarifa->id;?>"><?php echo $tarifa->nombre;?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="updepartamento">Departamento</label>
          <div class="form-input">
            <span><i class="fa-solid fa-map"></i></span>
            <input type="text" placeholder="Departamento o regi&oacute;n" id="updepartamento" name="departamento" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="upciudad">Ciudad</label>
          <div class="form-input">
            <span><i class="fa-solid fa-city"></i></span>
            <input type="text" placeholder="Ciudad de residencia" id="upciudad" name="ciudad" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="updireccion">Direcci&oacute;n</label>
          <div class="form-input">
            <span><i class="fa-solid fa-location-dot"></i></span>
            <input type="text" placeholder="Direcci&oacute;n de vivienda" id="updireccion" name="direccion" value="">
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-5 sm:col-span-2 sm:grid-cols-2">
          <button id="btnRemoveDireccion" class="btnDialog w-full border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" type="submit" value="Eliminar">Eliminar</button>
          <input id="btnUpDireccion" class="btnDialog btnDialog_primary w-full" type="submit" value="Actualizar">
        </div>
      </form>
    </dialog>

</div>
