<!-- MODAL PARA ABONAR-->
  <dialog id="miDialogoSelectUser" class="midialog-sm p-12">
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 id="modalSelectUser" class="font-semibold text-gray-700 mb-4">Cambiar usuario de venta</h4>
        <button class="rounded-lg hover:bg-gray-100 transition">
            <i id="btnXCerrarModalSelectUser" class="p-2 fa-solid fa-xmark text-gray-600 text-3xl"></i>
        </button>
    </div>
    <div id="divmsjalertaSelectUser"></div>
    <form id="formCrearUpdateSelectUser" class="formulario" method="POST">
        
        <div class="formulario__campo">
            <label class="formulario__label" for="selectUser">Usuario</label>
            <select id="selectUser" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                <?php foreach($usuarios as $value):  ?>
                      <option 
                        value="<?php echo $value->id;?>" 
                        <?php if($factura->idvendedor == $value->id)echo 'selected'?> 
                      >
                        <?php echo $value->nombre.' '.$value->apellido;?>
                      </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="formulario__campo">
            <label class="formulario__label" for="percentComision">% Porcentaje de comision</label>
            <input id="percentComision" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" value="<?php echo $factura->porcentgananciauser;?>" required>
        </div>

        <div class="text-right border-t border-gray-200 pt-12 mt-8">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[136px]" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearSelectUser" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[136px]" type="submit" value="Confirmar">
        </div>
    </form>
  </dialog><!--fin modal SelectUser-->