<div class="gestionEmisores">

  <section class="">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-3 border-b border-slate-200 mb-5 pb-5">
      <div class="flex items-center gap-4">
        <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-4xl text-indigo-600 font-medium">badge</span>
        <div>
          <h2 class="text-slate-900 text-3xl font-bold">Gesti&oacute;n de emisores</h2>
          <p class="my-0 text-lg leading-snug text-slate-500">Administra los emisores disponibles para facturaci&oacute;n y operaci&oacute;n.</p>
        </div>
      </div>
      <button id="crearEmisor" class="btnDialog btnDialog_primary">
        <i class="fa-solid fa-plus"></i>
        Crear emisor
      </button>
    </div>

    <div class="datatable-card config-table-card">
      <table id="tablaEmisores" class="display responsive nowrap tabla datatable-table" width="100%">
          <thead>
              <tr>
                  <th>N.</th>
                  <th>Nombre</th>
                  <th>NIT</th>
                  <th>Movil</th>
                  <th>Estado</th>
                  <th class="accionesth">Acciones</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($emisores as $index => $value): ?>
              <tr> 
                  <td class=""><?php echo $index+1;?></td>        
                  <td class=""><?php echo $value->nombre;?></td>
                  <td class=""><?php echo $value->nit;?></td>
                  <td class=""><?php echo $value->telefono;?></td>
                  <td class="">
                    <button id="<?php echo $value->id;?>" data-state="<?php echo $value->estado;?>" class="stateEmisor btn-xs <?php echo $value->estado==1?'btn-lima':'btn-red';?>"><?php echo $value->estado==1?'Activo':'Inactivo';?></button>
                  </td>
                  <td class="accionestd">
                    <div class="acciones-btns" id="<?php echo $value->id;?>" data-emisor="<?php echo $value->nombre;?>">
                        <button class="btn-md btn-turquoise editarEmisor"><i class="fa-solid fa-pen-to-square" title="Actualizar datos del emisor"></i></button>
                        <button class="btn-md btn-red eliminarEmisor" title="Eliminar Emisor"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    </div>
  </section>

  <dialog id="miDialogoEmisor" class="detalledialog_xs">
    <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
        <span class="material-symbols-outlined inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">badge</span>
        <div>
            <p class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Emisor</p>
            <h4 id="modalEmisor" class="text-slate-900 text-3xl leading-6 font-bold">Crear emisor</h4>
            <small class="mt-1 text-lg leading-snug text-slate-500">Registra los datos fiscales y de contacto del emisor.</small>
        </div>
    </div>
    <div id="divmsjalertaEmisor"></div>
    <form id="formCrearUpdateEmisor" class="pb-8" action="/admin/config/crear_Emisor" method="POST">
        <div class="formulario--grid">
            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="sucursalEmisor">Sucursal</label>
                <select id="sucursalEmisor" class="formulario__select" name="sucursalEmisor" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($sucursales as $value): ?>
                        <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                    <?php endforeach; ?>    
                </select>                   
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="nombreEmisor">Nombre del emisor</label>
                <input id="nombreEmisor" class="formulario__input" type="text" placeholder="Nombre del emisor" name="nombreEmisor" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="nitEmisor">Nit</label>
                <input id="nitEmisor" class="formulario__input" type="text" placeholder="Nit del emisor" name="nitEmisor" value="" required>
            </div>
            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="datosencabezadosEmisor">Datos del Rut</label>
                <div class="formulario__dato">
                    <textarea id="datosencabezadosEmisor" class="formulario__textarea formulario__textarea--textarea !min-h-32" name="datosencabezadosEmisor" placeholder="datos de encabezado de la factura" rows="4"></textarea>
                </div>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="movilEmisor">Movil</label>
                <input id="movilEmisor" class="formulario__input" type="text" placeholder="Contacto del emisor" name="movilEmisor" value="" oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0)">
            </div>
        </div>
        
        <div class="formulario__contenedorBtns--gridfull px-8">
            <button class="btnDialog btnDialog_esmeralda" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearEmisor" class="btnDialog btnDialog_light" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar Emisor-->
  
</div>
