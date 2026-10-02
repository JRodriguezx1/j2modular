<div class="tarifas">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-percent"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de tarifas</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra valores de tarifas disponibles para ventas y operaciones.</p>
      </div>
    </div>
    <button id="crearTarifa" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear tarifa
    </button>
  </div>
  <div class="datatable-card config-table-card">
  <table id="tablaTarifas" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
          <tr>
              <th>N.</th>
              <th>Nombre</th>
              <th>Valor tarifa</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($tarifas as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>        
              <td>
                <span class="table-entity">
                  <span class="table-entity__icon"><i class="fa-solid fa-percent"></i></span>
                  <span><?php echo $value->nombre; ?></span>
                </span>
              </td> 
              <td><span class="table-badge table-badge--success">$<?php echo number_format($value->valor??0, '2', ',', '.');?></span></td>
              <td class="accionestd">
                <div class="acciones-btns" id="<?php echo $value->id;?>" data-tarifa="<?php echo $value->nombre;?>">
                    <button class="btn-md btn-turquoise editarTarifa"><i class="fa-solid fa-pen-to-square" title="Actualizar datos del tarifa"></i></button>
                    <button class="btn-md btn-red eliminarTarifa" title="Eliminar tarifa"><i class="fa-solid fa-trash-can"></i></button>
                </div>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  </div>

  <dialog id="miDialogoTarifa" class="detalledialog_xs">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">
        <i class="fa-solid fa-percent"></i>
      </span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Tarifa</p>
        <h4 id="modalTarifa" class="text-3xl font-bold leading-6 text-slate-900">Crear tarifa</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Define el nombre y valor que se usar&aacute; en ventas y operaciones.</small>
      </div>
    </div>

    <form id="formCrearUpdateTarifa" class="pb-8" action="/admin/config/crear_Tarifa" method="POST">
        <div id="divmsjalertaTarifa"></div>

        <div class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
            <div class="form-field">
                <label for="nombreTarifa">Nombre</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-tag"></i></span>
                  <input id="nombreTarifa" type="text" placeholder="Nombre de la tarifa" name="nombre" value="" required>
                </div>
            </div>
            <div class="form-field">
                <label for="valorTarifa">Valor tarifa</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-dollar-sign"></i></span>
                  <input id="valorTarifa" type="text" placeholder="Valor de la tarifa" name="valor" value="" required>
                </div>
            </div>
        </div>  
        
        <div class="formulario__contenedorBtns--gridfull px-6">
            <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearTarifa" class="btnDialog btnDialog_primary" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar Tarifa-->
  
</div>
