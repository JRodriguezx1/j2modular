<div class="gestionbancos">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-building-columns"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de bancos</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra cuentas bancarias disponibles para pagos y movimientos.</p>
      </div>
    </div>
    <button id="crearBanco" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear banco
    </button>
  </div>
  <div class="datatable-card config-table-card">
  <table id="tablaBancos" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
          <tr>
              <th>N.</th>
              <th>Nombre</th>
              <th>Numero de cuenta</th>
              <th>Fecha</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($bancos as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>        
              <td>
                <span class="table-entity">
                  <span class="table-entity__icon"><i class="fa-solid fa-building-columns"></i></span>
                  <span><?php echo $value->nombre; ?></span>
                </span>
              </td> 
              <td><span class="table-badge table-badge--info !whitespace-normal break-all"><?php echo $value->numerocuenta;?></span></td>
              <td><span class="table-badge table-badge--warning"><?php echo $value->created_at;?></span></td>
              <td class="accionestd">
                <div class="acciones-btns" id="<?php echo $value->id;?>" data-banco="<?php echo $value->nombre;?>">
                    <button class="btn-md btn-turquoise editarBanco"><i class="fa-solid fa-pen-to-square" title="Actualizar datos del banco"></i></button>
                    <button class="btn-md btn-red eliminarBanco" title="Eliminar banco"><i class="fa-solid fa-trash-can"></i></button>
                </div>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>
  </div>

  <dialog id="miDialogoBanco" class="detalledialog_xs">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600">
        <i class="fa-solid fa-building-columns"></i>
      </span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Banco</p>
        <h4 id="modalBanco" class="text-3xl font-bold leading-6 text-slate-900">Crear banco</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Registra la cuenta bancaria disponible para pagos y movimientos.</small>
      </div>
    </div>

    <form id="formCrearUpdateBanco" class="pb-8" action="/admin/config/crear_Banco" method="POST">
        <div id="divmsjalertaBanco"></div>

        <div class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
            <div class="form-field">
                <label for="nombreBanco">Nombre</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-building-columns"></i></span>
                  <input id="nombreBanco" type="text" placeholder="Nombre del banco" name="nombre" value="" required>
                </div>
            </div>
            <div class="form-field">
                <label for="numeroCuenta">Numero de cuenta</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-credit-card"></i></span>
                  <input id="numeroCuenta" type="text" placeholder="Numero de cuenta" name="numerocuenta" value="">
                </div>
            </div>
        </div>
        
        <div class="formulario__contenedorBtns--gridfull px-6">
            <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearBanco" class="btnDialog btnDialog_primary" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar Banco-->
  
</div>
