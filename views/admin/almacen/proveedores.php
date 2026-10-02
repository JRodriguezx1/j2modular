<div class="gestionproveedores">

  <script id="proveedoresIniciales" type="application/json"><?php echo json_encode($proveedores, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>

  <h4 class="text-gray-600 mb-8 mt-12">Gestion de proveedores</h4>
  <button id="crearProveedor" class="btn-md btn-indigo !mb-4 !py-4 px-6 ">Crear proveedor</button>
  <table class="display responsive nowrap tabla" width="100%" id="tablaProveedores">
      <thead>
          <tr>
              <th>N.</th>
              <th>Nit</th>
              <th>Nombre</th>
              <th>Fecha creacion</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($proveedores as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>
              <td class="" ><?php echo $value->nit; ?></td>     
              <td class="" ><?php echo $value->nombre; ?></td>
              <td class="" ><?php echo $value->created_at; ?></td> 
              <td class="accionestd">
                <div class="acciones-btns" id="<?php echo $value->id;?>" data-proveedor="<?php echo $value->nombre;?>">
                    <button class="btn-md btn-turquoise editarProveedor"><i class="fa-solid fa-pen-to-square" title="Actualizar datos del proveedor"></i></button>
                    <button class="btn-md btn-red eliminarProveedor" title="Eliminar proveedor"><i class="fa-solid fa-trash-can"></i></button>
                </div>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
  </table>

  <dialog id="miDialogoProveedor" class="midialog-sm rounded-lg shadow-lg p-12">
    <h4 id="modalProveedor" class="font-semibold text-gray-700 mb-4 mt-10">Crear proveedor</h4>
    <div id="divmsjalertaProveedor"></div>
    <form id="formCrearUpdateProveedor" class="formulario" action="/admin/config/crear_Proveedor" method="POST">
        <div class="empleado-grid">
            <div class="formulario__campo">
                <label class="formulario__label" for="nit">Nit</label>
                <input id="nit" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Numero de cuenta" name="nit" value="">
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="nombreProveedor">Nombre</label>
                <input id="nombreProveedor" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Nombre del proveedor" name="nombre" value="" required>
            </div>
            
        </div>  
        
        <div class="text-right">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[136px]" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearProveedor" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[136px]" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar Proveedor-->
  
</div>
