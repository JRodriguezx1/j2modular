<div class="gestionimpresoras">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-print"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de impresoras</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra puntos de impresi&oacute;n, estaciones y ancho de papel.</p>
      </div>
    </div>
    <button id="crearImpresora" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear punto
    </button>
  </div>
  <div class="datatable-card config-table-card">
  <table id="tablaImpresoras" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
          <tr>
              <th>N.</th>
              <th>Nombre</th>
              <th>Nombre compartido</th>
              <th>Estacion</th>
              <th>Mm</th>
              <th>Estado</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($impresoras as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>        
              <td>
                <span class="table-entity">
                  <span class="table-entity__icon"><i class="fa-solid fa-print"></i></span>
                  <span><?php echo htmlspecialchars((string)($value->nombre ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                </span>
              </td> 
              <td><span class="table-badge table-badge--primary !whitespace-normal break-words"><?php echo htmlspecialchars((string)($value->nombrecompartido ?? ''), ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td><span class="table-badge table-badge--neutral"><?php echo htmlspecialchars((string)($value->estacion ?? ''), ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td><span class="table-badge table-badge--warning"><?php echo htmlspecialchars((string)($value->mm ?? ''), ENT_QUOTES, 'UTF-8'); ?> mm</span></td>
              <td><span class="table-status <?php echo $value->estado==1?'table-status--success':'table-status--danger';?>"><?php echo $value->estado==1?'Activa':'Inactiva';?></span></td>
              <td class="accionestd">
                <div class="acciones-btns" id="<?php echo htmlspecialchars((string)($value->id ?? ''), ENT_QUOTES, 'UTF-8'); ?>" data-impresora="<?php echo htmlspecialchars((string)($value->nombre ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="btn-md btn-turquoise editarImpresora"><i class="fa-solid fa-pen-to-square" title="Actualizar punto de impresion"></i></button>
                    <button class="btn-md btn-red eliminarImpresora" title="Eliminar Impresora"><i class="fa-solid fa-trash-can"></i></button>
                </div>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  </div>

  <dialog id="miDialogoIMpresora" class="detalledialog_xs">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">
        <i class="fa-solid fa-print"></i>
      </span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Impresora</p>
        <h4 id="modalIMpresora" class="text-3xl font-bold leading-6 text-slate-900">Crear punto de impresora</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Configura el punto de impresi&oacute;n, nombre compartido y ancho del papel.</small>
      </div>
    </div>

    <form id="formCrearUpdateIMpresora" class="pb-8">
        <div id="divmsjalertaIMpresora"></div>

        <div class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
            <div class="form-field">
                <label for="nombreImpresora">Nombre de la impresora</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-print"></i></span>
                  <input id="nombreImpresora" type="text" placeholder="Nombre del espacio de trabajo" name="nombre" value="" required>
                </div>
            </div>
            <div class="form-field">
                <label for="nombreCompartido">Nombre compartido</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-share-nodes"></i></span>
                  <input id="nombreCompartido" type="text" placeholder="Nombre compartido de la impresora" name="nombrecompartido" value="" required>
                </div>
            </div>
            <div class="form-field">
                <label for="anchoPapel">Ancho del papel (mm)</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-ruler-horizontal"></i></span>
                  <input id="anchoPapel" type="text" placeholder="Ejemplo: 58" name="anchoPapel" value="" oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0)" required>
                </div>
            </div>
            <div class="form-field">
                <label for="estacion">Estacion</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-desktop"></i></span>
                  <input id="estacion" type="text" placeholder="Estacion de trabajo" name="estacion" value="" required>
                </div>
            </div>
        </div>
        
        <div class="formulario__contenedorBtns--gridfull px-6">
            <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearImpresora" class="btnDialog btnDialog_primary" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar Impresora-->
  
</div>
