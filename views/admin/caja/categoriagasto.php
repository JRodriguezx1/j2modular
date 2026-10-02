<div class="box categoriasgastos !pb-20">
    <a href="/admin/caja" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
    </a>
    <h4 class="text-gray-600 mb-6 mt-10">Categoria de gastos</h4>
    <?php include __DIR__. "/../../templates/alertas.php"; ?>
    <button id="btnCrearCategoriaGasto" class="btn-md btn-turquoise !py-4 !px-6 mb-2">+ Crear</button>
    <table id="tablaCategoriasGastos" class="display responsive nowrap tabla" width="100%">
      <thead>
          <tr>
              <th>Nº</th>
              <th>Unidad</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($categoriasgastos as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>
              <td class=""><?php echo $value->nombre;?></td>
              <td class="accionestd"><div class="acciones-btns" id="<?php echo $value->id;?>" data-categoriagasto="<?php echo $value->nombre;?>">
                <?php if($value->id>11){ ?>
                <button class="btn-md btn-turquoise editarCategoriaGasto" title="Actualizar categoria de los gastos"><i class="fa-solid fa-pen-to-square"></i></button>
                <!--<button class="btn-md btn-red eliminarCategoriaGasto"><i class="fa-solid fa-trash-can"></i></button></div>-->
                <form method="POST" class="formEliminarCategoriaGasto" action="/admin/caja/categoriaGasto">
                    <input type="hidden" name="id" value="<?php echo $value->id;?>"> 
                    <button class="btn-md btn-red eliminarCategoriaGasto" title="Eliminar categoria de los gastos" type="button">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
                <?php }else{ echo "No Action"; } ?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
    </table>


    <dialog id="miDialogoCategoriaGasto" class="w-[500px] h-[195px] p-6 rounded-lg shadow-lg">
        <h4 id="modalCategoriaGasto" class="font-semibold text-gray-700 mb-4 mt-3">Crear categoria de los gastos</h4>
        <div id="divmsjalerta1"></div>
        <form id="formCrearUpdateCategoriaGasto" class="formulario" action="/admin/caja/crear_categoriaGasto" method="POST">
            <input type="hidden" id="idCategoriaGasto" name="id" value="">
            <div class="formulario__campo">
                <label class="formulario__label" for="nombre">Nombre</label>
                <div class="formulario__dato">
                    <input id="nombre" class="formulario__input focus-within:!border-indigo-600 border border-gray-300 rounded-lg flex items-center h-14 overflow-hidden" type="text" placeholder="Nombre de la categoria del gasto" name="nombre" value="" required>
                    <!-- <label data-num="28" class="count-charts" for="">28</label> -->
                </div>
            </div>
            <div class="text-right">
                <button class="btn-md btn-turquoise !py-4 !px-6 !w-[100px]" type="button" value="salir">Salir</button>
                <input id="btnEditarCrearCategoriaGasto" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[100px]" type="submit" value="Crear">
            </div>
        </form>
    </dialog><!--fin crear/editar categoria gasto-->
</div>
