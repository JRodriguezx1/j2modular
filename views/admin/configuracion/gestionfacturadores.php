<div class="gestionfacturadores">

  <div class="mb-5 flex flex-col justify-between gap-3 border-b border-slate-200 pb-5 md:flex-row md:items-center">
    <div class="flex items-center gap-4">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl font-medium text-indigo-600">
      <i class="fa-solid fa-receipt"></i>
      </span>
      <div>
        <h2 class="text-3xl font-bold text-slate-900">Gesti&oacute;n de facturadores</h2>
        <p class="my-0 text-lg leading-snug text-slate-500">Administra consecutivos, rangos, fechas y estado de facturaci&oacute;n.</p>
      </div>
    </div>
    <button id="crearFacturador" class="btnDialog btnDialog_primary" type="button">
      <i class="fa-solid fa-plus"></i>
      Crear facturador
    </button>
  </div>

  <div class="datatable-card config-table-card">
    <table id="tablaFacturadores" class="display responsive nowrap tabla datatable-table" width="100%">
      <thead>
        <tr>
          <th>N.</th>
          <th>Nombre</th>
          <th>Tipo</th>
          <th>Rango</th>
          <th>Siguiente</th>
          <th>Expira</th>
          <th>Estado</th>
          <th class="accionesth">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($facturadores as $index => $value): 
          if($value->estado==1)?>
        <tr>
          <td><?php echo $index+1;?></td>
          <td>
            <span class="table-entity">
              <span class="table-entity__icon"><i class="fa-solid fa-receipt"></i></span>
              <span><?php echo $value->nombre; ?></span>
            </span>
          </td>
          <td><span class="table-badge table-badge--neutral"><?php echo $value->nombretipofacturador;?></span></td>
          <td><span class="table-badge table-badge--primary"><?php echo $value->rangoinicial.' - '.$value->rangofinal; ?></span></td>
          <td><span class="table-badge table-badge--success"><?php echo $value->siguientevalor;?></span></td>
          <td><span class="table-badge table-badge--warning"><?php echo $value->fechafin; ?></span></td>
          <td><span class="table-status <?php echo $value->estado==1?'table-status--success':'table-status--danger';?>"><?php echo $value->estado==1?'Activo':'Expirada';?></span></td>
          <td class="accionestd">
            <div class="acciones-btns" id="<?php echo $value->id;?>" data-facturador="<?php echo $value->nombre;?>">
              <button class="btn-md btn-turquoise editarFacturador" title="Actualizar facturador"><i class="fa-solid fa-pen-to-square"></i></button>
              <?php if($value->id > 1): ?>
              <button class="btn-md btn-red eliminarFacturador" title="Eliminar facturador"><i class="fa-solid fa-trash-can"></i></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <dialog id="miDialogoFacturador" class="detalledialog_xs">
    <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600"><i class="fa-solid fa-receipt"></i></span>
      <div>
        <p class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">Facturador</p>
        <h4 id="modalFacturador" class="text-3xl font-bold leading-6 text-slate-900">Crear facturador</h4>
        <small class="mt-1 block text-lg leading-snug text-slate-500">Configura el consecutivo, rango, vigencia y negocio asociado.</small>
      </div>
    </div>

    <form id="formCrearUpdateFacturador" class="pb-8" action="/admin/config/crear_facturador" method="POST">
      <div id="divmsjalertafacturador"></div>

      <div class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
        <div class="form-field">
          <label for="nombrefacturador">Nombre</label>
          <div class="form-input">
            <span><i class="fa-solid fa-file-lines"></i></span>
            <input id="nombrefacturador" type="text" placeholder="Nombre del facturador" name="nombre" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="idtipofacturador">Tipo facturador</label>
          <div class="form-input">
            <span><i class="fa-solid fa-layer-group"></i></span>
            <select id="idtipofacturador" name="idtipofacturador" required>
              <option value="" disabled selected>-Seleccionar-</option>
              <?php foreach($tipofacturadores as $value): ?>
                <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-field">
          <label for="rangoinicial">Consecutivo inicial</label>
          <div class="form-input">
            <span><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
            <input id="rangoinicial" type="text" placeholder="Consecutivo inicial" name="rangoinicial" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="rangofinal">Consecutivo final</label>
          <div class="form-input">
            <span><i class="fa-solid fa-arrow-right-to-bracket"></i></span>
            <input id="rangofinal" type="text" placeholder="Consecutivo final" name="rangofinal" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="siguientevalor">Siguiente consecutivo</label>
          <div class="form-input">
            <span><i class="fa-solid fa-hashtag"></i></span>
            <input id="siguientevalor" type="text" placeholder="Siguiente consecutivo" name="siguientevalor" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="prefijo">Prefijo</label>
          <div class="form-input">
            <span><i class="fa-solid fa-tag"></i></span>
            <input id="prefijo" type="text" placeholder="Prefijo de la resolucion" name="prefijo" value="" required>
          </div>
        </div>

        <div class="form-field">
          <label for="fechainicio">Fecha inicio</label>
          <div class="form-input">
            <span><i class="fa-solid fa-calendar-day"></i></span>
            <input id="fechainicio" type="date" name="fechainicio" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="fechafin">Fecha fin</label>
          <div class="form-input">
            <span><i class="fa-solid fa-calendar-check"></i></span>
            <input id="fechafin" type="date" name="fechafin" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="resolucion">Numero de resolucion</label>
          <div class="form-input">
            <span><i class="fa-solid fa-certificate"></i></span>
            <input id="resolucion" type="text" placeholder="Numero de resolucion" name="resolucion" value="">
          </div>
        </div>

        <div class="form-field">
          <label for="negociofacturador">Negocio</label>
          <div class="form-input">
            <span><i class="fa-solid fa-store"></i></span>
            <select id="negociofacturador" name="negocio" required>
              <option value="" disabled selected>-Seleccionar-</option>
              <option value="<?php echo $negocio->id;?>"><?php echo $negocio->nombre;?></option>
            </select>
          </div>
        </div>
      </div>

      <div class="formulario__contenedorBtns--gridfull px-6">
        <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
        <input id="btnEditarCrearFacturador" class="btnDialog btnDialog_primary" type="submit" value="Crear">
      </div>
    </form>
  </dialog><!--fin crear/editar facturador-->
</div>
