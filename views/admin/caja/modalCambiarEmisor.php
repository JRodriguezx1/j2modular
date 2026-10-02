<!-- MODAL PARA CAMBIAR EMISOR-->
  <dialog id="miDialogoSelectEmisor" class="midialog-xs p-12">
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 id="modalSelectEmisor" class="font-semibold text-gray-700 mb-4">Cambiar Emisor facturador</h4>
        <button class="rounded-lg hover:bg-gray-100 transition">
            <i id="btnXCerrarModalSelectEmisor" class="p-2 fa-solid fa-xmark text-gray-600 text-3xl"></i>
        </button>
    </div>
    <div id="divmsjalertaSelectEmisor"></div>
    <form id="formUpdateSelectEmisor" class="formulario" method="POST">
        <div class="formulario__campo">
            <label class="formulario__label" for="selectEmisor">Emisor</label>
            <select id="selectEmisor" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                <option value="0"><?php echo $sucursal->negocio;?></option>
                <?php foreach($emisores as $value):  ?>
                      <option 
                        value="<?php echo $value->id;?>" 
                        <?php if($factura->idemisor == $value->id)echo 'selected'?> 
                      >
                        <?php echo $value->nombre;?>
                      </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="formulario__campo">
            <label class="formulario__label" for="selectCaja">Caja Facturadora</label>
            <select id="selectCaja" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" disabled>
                <?php foreach($cajas as $value):  ?>
                      <option value="<?php echo $value->id;?>" data-emisor="<?php echo $value->idemisor??0;?>" <?php echo ($factura->idemisor==$value->idemisor)?'selected':'';?> >
                        <?php echo $value->nombre;?>
                      </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="bg-red-50 border border-red-100 rounded-2xl p-5 mb-6">
            <label for="inputcambiarEmisor" class="block text-xl font-semibold text-red-700 mb-3">
                Confirmación de seguridad
            </label>
            <input
                id="inputcambiarEmisor"
                type="password"
                min="0"
                class="bg-white border border-red-200 text-gray-900 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 block w-full p-3 h-14 text-xl focus:outline-none"
                placeholder="Ingrese la clave"
            >
        </div>

        <div class="text-right border-t border-gray-200 pt-12 mt-8">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-48" type="button" value="Salir">Salir</button>
            <input id="btnEditarSelectEmisor" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-48" type="submit" value="Confirmar">
        </div>
    </form>
  </dialog><!--fin modal SelectEmisor-->