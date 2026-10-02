<div class="contenido9 configNotificationWS accordion_tab_content mt-6 grid min-w-0 w-full gap-6 rounded-lg border border-gray-200 bg-white p-6 shadow-md">

  <div class="border border-slate-200 rounded-xl p-4 shadow-lg bg-gradient-to-br from-indigo-600/10 to-cyan-400/5">
    <div class="flex items-center gap-4 flex-1">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-green-100 text-3xl text-green-600 font-medium"><i class="fa-brands fa-whatsapp"></i></span>
      <div>
        <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600 leading-4">Notificaciones</p>
        <h1 class="m-0 break-words text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">WhatsApp</h1>
        <small class="mt-1 mb-0 text-lg leading-snug text-slate-500">Configura destinos y alertas automaticas del sistema.</small>
      </div>
    </div>
  </div>

  <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
    <div class="flex justify-between items-center border-b border-slate-200 pb-4 mb-4">
      <div>
        <h2 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Destinos de notificaci&oacute;n</h2>
        <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Registra los contactos o grupos que recibir&aacute;n los avisos.</p>
      </div>
      <span class="text-base font-bold rounded-full py-1 px-3 text-indigo-600 border border-indigo-200 bg-indigo-50"><?php echo count($contactsNotificationWS);?> <?php echo count($contactsNotificationWS)>1?' Registros':' Registro';?></span>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
      <form id="formCreateContactNotifcationWs" class="formulario gap-3 p-4 border border-slate-200 rounded-xl bg-slate-50 xl:col-span-1">
        <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium"><i class="fa-solid fa-user-plus"></i></span>
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Agregar destino</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Contacto individual o grupo.</p>
          </div>
        </div>

        <div class="formulario__campo">
          <label class="formulario__label" for="nombreWS">Nombre</label>
          <input id="nombreWS" class="formulario__input" type="text" placeholder="Nombre del destino" required>
        </div>

        <div class="formulario__campo">
          <label class="formulario__label" for="movilWS">Tel&eacute;fono</label>
          <input id="movilWS" class="formulario__input" type="text" placeholder="Ej: 573001234567" required>
        </div>

        <div class="formulario__campo">
          <label class="formulario__label" for="tipoWS">Tipo</label>
          <select id="tipoWS" class="formulario__select" required>
            <option value="individual">Individual</option>
            <option value="grupo">Grupo</option>
          </select>
        </div>

        <button type="submit" class="btnDialog btnDialog_esmeralda">
          <i class="fa-solid fa-plus"></i>
          Agregar destino
        </button>
      </form>

      <div class="min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 xl:col-span-2">
        <div class="p-4">
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Destinos configurados</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Prueba o elimina los destinos activos.</p>
          </div>
        </div>

        <div class="w-full min-w-0 max-w-full overflow-x-auto">
          <table id="tablaNumbersWS" class="datatable-table tabla">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Tel&eacute;fono</th>
                <th>Tipo</th>
                <th>Enviar test</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach($contactsNotificationWS as $index => $value): ?>
              <tr id="<?php echo $value->id;?>">
                <td>
                  <span class="table-entity">
                    <span class="table-entity__icon !h-10 !bg-green-100 !text-green-600"><i class="fa-brands fa-whatsapp"></i></span>
                    <span><?php echo $value->nombre;?></span>
                  </span>
                </td>
                <td><span class="table-badge table-badge--info"><?php echo $value->movil;?></span></td>
                <td><span class="table-badge table-badge--primary"><?php echo $value->tipo;?></span></td>
                <td><button class="test inline-flex h-12 items-center justify-center rounded-md bg-indigo-50 px-4 text-base font-extrabold text-indigo-700 transition hover:bg-indigo-100" type="button">Test</button></td>
                <td><span class="table-status table-status--success">Activo</span></td>
                <td>
                  <button class="table-action table-action--danger eliminarContacto" type="button" title="Eliminar contacto">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <section class="border border-slate-200 p-4 rounded-xl bg-white">
    <div class="border-b border-slate-200 pb-4 mb-4">
      <div>
        <h2 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Eventos del sistema</h2>
        <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Activa los avisos que quieres enviar por WhatsApp API.</p>
      </div>
    </div>

    <div class="config-whatsapp-events">
      <div class="config-whatsapp-event">
        <div class="flex items-center gap-3">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium"><i class="fa-solid fa-file-circle-xmark"></i></span>
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Factura eliminada</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Notifica cuando se elimina una factura.</p>
          </div>
        </div>

        <div class="config-whatsapp-options">
          <label for="notificacion_por_whatsApp_eliminacion_de_factura_si">
            <input id="notificacion_por_whatsApp_eliminacion_de_factura_si" type="radio" name="notificacion_por_whatsApp_eliminacion_de_factura" class="hidden peer" value="1" <?php echo $conflocal['notificacion_por_whatsApp_eliminacion_de_factura']->valor_final == 1?'checked':'';?>>
            <div></div>
            <span>S&iacute;</span>
          </label>

          <label for="notificacion_por_whatsApp_eliminacion_de_factura_no">
            <input id="notificacion_por_whatsApp_eliminacion_de_factura_no" type="radio" name="notificacion_por_whatsApp_eliminacion_de_factura" class="hidden peer" value="0" <?php echo $conflocal['notificacion_por_whatsApp_eliminacion_de_factura']->valor_final == 0?'checked':'';?>>
            <div></div>
            <span>No</span>
          </label>
        </div>
      </div>

      <div class="config-whatsapp-event">
        <div class="flex items-center gap-3">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium"><i class="fa-solid fa-cash-register"></i></span>
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Cierre de caja</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Envia el resumen al cerrar caja.</p>
          </div>
        </div>

        <div class="config-whatsapp-options">
          <label for="notificacion_por_whatsApp_cierre_caja_si">
            <input id="notificacion_por_whatsApp_cierre_caja_si" type="radio" name="notificacion_por_whatsApp_cierre_caja" class="hidden peer" value="1" <?php echo $conflocal['notificacion_por_whatsApp_cierre_caja']->valor_final == 1?'checked':'';?>>
            <div></div>
            <span>S&iacute;</span>
          </label>

          <label for="notificacion_por_whatsApp_cierre_caja_no">
            <input id="notificacion_por_whatsApp_cierre_caja_no" type="radio" name="notificacion_por_whatsApp_cierre_caja" class="hidden peer" value="0" <?php echo $conflocal['notificacion_por_whatsApp_cierre_caja']->valor_final == 0?'checked':'';?>>
            <div></div>
            <span>No</span>
          </label>
        </div>
      </div>

      <div class="config-whatsapp-event">
        <div class="flex items-center gap-3">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium"><i class="fa-solid fa-boxes-stacked"></i></span>
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Stock m&iacute;nimo</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Alerta cuando el inventario est&aacute; bajo.</p>
          </div>
        </div>

        <div class="config-whatsapp-options">
          <label for="notificacion_por_whatsApp_stock_bajo_si">
            <input id="notificacion_por_whatsApp_stock_bajo_si" type="radio" name="notificacion_por_whatsApp_stock_bajo" class="hidden peer" value="1" <?php echo $conflocal['notificacion_por_whatsApp_stock_bajo']->valor_final == 1?'checked':'';?>>
            <div></div>
            <span>S&iacute;</span>
          </label>

          <label for="notificacion_por_whatsApp_stock_bajo_no">
            <input id="notificacion_por_whatsApp_stock_bajo_no" type="radio" name="notificacion_por_whatsApp_stock_bajo" class="hidden peer" value="0" <?php echo $conflocal['notificacion_por_whatsApp_stock_bajo']->valor_final == 0?'checked':'';?>>
            <div></div>
            <span>No</span>
          </label>
        </div>
      </div>

      <div class="config-whatsapp-event">
        <div class="flex items-center gap-3">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 font-medium"><i class="fa-solid fa-truck-fast"></i></span>
          <div>
            <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Env&iacute;o de mercanc&iacute;a</h3>
            <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Alerta traslados o envios entre sucursales.</p>
          </div>
        </div>

        <div class="config-whatsapp-options">
          <label for="notificacion_por_whatsApp_envio_mercancia_si">
            <input id="notificacion_por_whatsApp_envio_mercancia_si" type="radio" name="notificacion_por_whatsApp_envio_mercancia" class="hidden peer" value="1" <?php echo $conflocal['notificacion_por_whatsApp_envio_mercancia']->valor_final == 1?'checked':'';?>>
            <div></div>
            <span>S&iacute;</span>
          </label>

          <label for="notificacion_por_whatsApp_envio_mercancia_no">
            <input id="notificacion_por_whatsApp_envio_mercancia_no" type="radio" name="notificacion_por_whatsApp_envio_mercancia" class="hidden peer" value="0" <?php echo $conflocal['notificacion_por_whatsApp_envio_mercancia']->valor_final == 0?'checked':'';?>>
            <div></div>
            <span>No</span>
          </label>
        </div>
      </div>
    </div>
  </section>

</div>
