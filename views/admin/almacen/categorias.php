<div class="box categorias mb-20">
  <!-- <a class="btn-xs" href="/admin/almacen">Atras</a> -->

  <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
    </a>


  <h4 class="text-gray-600 mb-12 mt-4">Categorias</h4>
  <div class="divmsjalerta0"><?php include __DIR__. "/../../templates/alertas.php"; ?></div>
  <button id="crearCategoria" class="btn-md btn-blueintense !mb-4 !py-4 px-6 !bg-indigo-600">Crear categoria</button>
  <a class="btn-md btn-turquoise !py-4 !px-6" href="/admin/almacen/productos">Ir a productos</a>
  <table class="display responsive nowrap tabla" width="100%" id="tablaCategorias">
      <thead>
          <tr>
              <th>Nº</th>
              <th>ID</th>
              <th>Categoria</th>
              <!--<th>N. productos</th>-->
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($categorias as $index => $value): 
            if($value->visible == 1):?>
          <tr> 
              <td class=""><?php echo $index;?></td>
              <td class=""><?php echo $value->id;?></td>
              <td class=""><?php echo $value->nombre; ?></td> 
              <!--<td class=""><?php //echo $value->totalproductos;?></td>-->
              <td class="accionestd"><div class="acciones-btns" id="<?php echo $value->id;?>" data-categoria="<?php echo $value->nombre;?>"><button class="btn-md btn-turquoise editarCategoria" title="Actualizar categoria"><i class="fa-solid fa-pen-to-square"></i></button><button class="btn-md btn-red eliminarCategoria" title="Eliminar categoria"><i class="fa-solid fa-trash-can"></i></button></div></td>
          </tr>
          <?php endif; endforeach; ?>
      </tbody>
  </table>

  <dialog id="miDialogoCategoria" class="w-[95%] max-w-lg p-8 rounded-2xl border border-slate-200 bg-white shadow-xl">
    <div class="text-center border-b border-slate-200 pb-5 mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
            <i class="fa-solid fa-layer-group text-indigo-600 text-2xl"></i>
        </div>

        <h4 id="modalCategoria" class="text-4xl font-bold text-slate-700">
            Crear categoría
        </h4>

        <p class="mt-2 text-lg text-slate-500">
            Agregue una nueva categoría para organizar sus productos.
        </p>
    </div>

    <div id="divmsjalerta1"></div>
    <form id="formCrearUpdateCategoria" class="formulario" action="/admin/almacen/crear_categoria" method="POST">
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mt-4">
            <input type="hidden" id="idcategoria" value="0">
            <div class="formulario__campo">
                <label
                    for="categoria"
                    class="block text-lg font-semibold text-slate-700 mb-3 text-left">
                    Nombre de la categoría
                </label>

                <div class="formulario__dato focus-within:!border-indigo-600 border border-slate-300 rounded-xl flex items-center h-14 overflow-hidden bg-white">
                    <input
                        id="categoria"
                        class="formulario__input !border-0"
                        type="text"
                        placeholder="Nombre de la categoría"
                        name="nombre"
                        required>
                </div>
            </div>
        </div>
        <div class="text-right mt-8">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[100px]" type="button" value="Salir">Salir</button>
            <input id="btnEditarCrearCategoria" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[100px]" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar categoria-->
</div>