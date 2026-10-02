<div class="gestioncajas">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-cash-register"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de cajas facturadoras</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra las cajas, facturadores y emisores asociados a la operaci&oacute;n.</p>
      </div>
    </div>
    <button id="crearCaja" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear caja
    </button>
  </div>

  <div class="datatable-card config-table-card">
    <table id="tablaCajas" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
        <tr>
          <th>N.</th>
          <th>Caja</th>
          <th>Facturador automatico</th>
          <th>Sede</th>
          <th>Emisor</th>
          <th class="accionesth">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($cajas as $index => $value): ?>
          <tr>
            <td><?php echo $index+1;?></td>
            <td>
              <span class="table-entity">
                <span class="table-entity__icon"><i class="fa-solid fa-cash-register"></i></span>
                <span><?php echo $value->nombre; ?></span>
              </span>
            </td>
            <td>
              <span class="table-badge table-badge--neutral"><?php echo $value->nombreconsecutivo;?></span>
            </td>
            <td>
              <span class="table-badge table-badge--info !whitespace-normal break-words"><?php echo $value->negocio;?></span>
            </td>
            <td>
              <span class="table-badge table-badge--primary !whitespace-normal break-words"><?php echo isset($nombreEmisores[$value->idemisor])? $nombreEmisores[$value->idemisor]: $negocio->negocio;?></span>
            </td>
            <td class="accionestd">
              <div class="acciones-btns" id="<?php echo $value->id;?>" data-caja="<?php echo $value->nombre;?>">
                <button class="btn-md btn-turquoise editarCaja"><i class="fa-solid fa-pen-to-square" title="Actualizar datos de caja"></i></button>
                <?php if($value->editable == 1): ?>
                  <button class="btn-md btn-red eliminarCaja" title="Eliminar caja"><i class="fa-solid fa-trash-can"></i></button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <dialog id="miDialogoCaja" class="detalledialog_xs">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600"><i class="fa-solid fa-cash-register"></i></span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Caja</p>
        <h4 id="modalCaja" class="text-3xl font-bold leading-6 text-slate-900">Crear caja</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Configura la caja, su facturador autom&aacute;tico, sede y emisor asociado.</small>
      </div>
    </div>

    <form id="formCrearUpdateCaja" class="pb-8" action="/admin/config/crear_caja" method="POST">
      <div id="divmsjalertacaja"></div>

      <div class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
        <div class="form-field sm:col-span-2">
          <label for="nombrecaja">Nombre</label>
          <div class="form-input">
            <span><i class="fa-solid fa-cash-register"></i></span>
            <input id="nombrecaja" type="text" placeholder="Nombre de la caja" name="nombre" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="idtipoconsecutivo">Facturador automatico</label>
          <div class="form-input">
            <span><i class="fa-solid fa-receipt"></i></span>
            <select id="idtipoconsecutivo" name="idtipoconsecutivo" required>
              <option value="" disabled selected>-Seleccionar-</option>
              <?php foreach($facturadores as $value): ?>
                <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="negociogestioncaja">Sede</label>
          <div class="form-input">
            <span><i class="fa-solid fa-store"></i></span>
            <select id="negociogestioncaja" name="negocio" required>
              <option value="" disabled selected>-Seleccionar-</option>
              <option value="<?php echo $negocio->id;?>"><?php echo $negocio->nombre;?></option>
            </select>
          </div>
        </div>

        <div class="form-field sm:col-span-2">
          <label for="idEmisorCaja">Emisor</label>
          <div class="form-input">
            <span><i class="fa-solid fa-id-card-clip"></i></span>
            <select id="idEmisorCaja" name="idEmisorCaja">
              <option value="" selected> <?php echo $negocio->negocio;?> </option>
              <?php foreach($emisores as $value): ?>
                <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="formulario__contenedorBtns--gridfull px-6">
        <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
        <input id="btnEditarCrearCaja" class="btnDialog btnDialog_primary" type="submit" value="Crear">
      </div>
    </form>
  </dialog><!--fin crear/editar caja-->
</div>
