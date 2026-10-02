<div class="mediosPagos">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-credit-card"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de medios de pago</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra los m&eacute;todos disponibles para registrar pagos.</p>
      </div>
    </div>
    <button id="crearMedioPago" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear medio
    </button>
  </div>
  <div class="datatable-card config-table-card">
  <table id="tablamediosPagos" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
          <tr>
              <th>N.</th>
              <th>Nombre</th>
              <th>Estado</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($mediospago as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>        
              <td>
                <span class="table-entity">
                  <span class="table-entity__icon"><i class="fa-solid fa-credit-card"></i></span>
                  <span><?php echo $value->mediopago;?></span>
                </span>
              </td> 
              <td class="">
                <?php if($value->id != 1):?>
                    <button id="<?php echo $value->id;?>" data-state="<?php echo $value->estado;?>" class="statemediopago table-status cursor-pointer justify-center border-0 transition hover:-translate-y-px hover:shadow-md <?php echo $value->estado==1?'table-status--success':'table-status--danger';?>"><?php echo $value->estado==1?'Activo':'Inactivo';?></button>
                <?php endif;?>
            </td>
              <td class="accionestd">
                <?php if($value->id != 1):?>
                <div class="acciones-btns" id="<?php echo $value->id;?>" data-mediopago="<?php echo $value->mediopago;?>">
                    <button class="btn-md btn-turquoise editarMedioPago"><i class="fa-solid fa-pen-to-square" title="Actualizar el mediopago"></i></button>
                    <button class="btn-md btn-red eliminarMedioPago" title="Eliminar mediopago"><i class="fa-solid fa-trash-can"></i></button>
                </div>
                <?php endif;?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  </div>

  <dialog id="miDialogoMedioPago" class="detalledialog_sm">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">
        <i class="fa-solid fa-credit-card"></i>
      </span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Medio de pago</p>
        <h4 id="modalMedioPago" class="text-3xl font-bold leading-6 text-slate-900">Crear medio de pago</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Registra el m&eacute;todo que estar&aacute; disponible para recibir pagos.</small>
      </div>
    </div>

    <form id="formCrearUpdateMedioPago" class="pb-8" action="/admin/config/crear_MedioPago" method="POST">
        <div id="divmsjalertaMedioPago"></div>

        <div class="form-field px-6 py-6">
            <label for="nombreMedioPago">Nombre</label>
            <div class="form-input">
              <span><i class="fa-solid fa-wallet"></i></span>
              <input id="nombreMedioPago" type="text" placeholder="Nombre del medio de pago" name="nombre" value="" required>
            </div>
        </div> 
        
        <div class="formulario__contenedorBtns--gridfull px-6">
            <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearMedioPago" class="btnDialog btnDialog_primary" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar medio de pago-->
  
</div>
