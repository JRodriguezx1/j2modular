<div class="box unidadesmedida !pb-20">
    <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
    </a>
    <h4 class="text-gray-600 mb-12 mt-12 ">Unidades de Medida</h4>
    <?php include __DIR__. "/../../templates/alertas.php"; ?>
    <button id="btnCrearUnidadMedida" class="btn-md btn-turquoise !py-4 !px-6">Crear unidad</button>
    <table id="tablaUnidadesMedida" class="display responsive nowrap tabla" width="100%">
      <thead>
          <tr>
              <th>Nº</th>
              <th>ID</th>
              <th>Unidad</th>
              <th>Fecha creacion</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($unidadesmedida as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $index+1;?></td>
              <td class=""><?php echo $value->id;?></td>
              <td class=""><?php echo $value->nombre;?></td>
              <td class=""><?php echo $value->fechacreacion??'';?></td>
              <td class="accionestd"><div class="acciones-btns" id="<?php echo $value->id;?>" data-unidadmedida="<?php echo $value->nombre;?>">
                <?php if($value->editable == '1'){ ?>
                <button class="btn-md btn-turquoise editarUnidadMedida" title="Actualizar unidad de medida"><i class="fa-solid fa-pen-to-square"></i></button>
                <!--<button class="btn-md btn-red eliminarUnidadMedida"><i class="fa-solid fa-trash-can"></i></button></div>-->
                <form method="POST" class="formEliminarUnidadMedida" action="/admin/almacen/unidadesmedida">
                    <input type="hidden" name="id" value="<?php echo $value->id;?>"> 
                    <button class="btn-md btn-red eliminarUnidadMedida" title="Eliminar unidad de medida" type="button">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
                <?php }else{ echo "No Action"; } ?>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
    </table>


    <dialog id="miDialogoUnidadMedida" class="w-[95%] max-w-xl p-8 rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                <i class="fa-solid fa-ruler-combined text-indigo-600 text-2xl"></i>
            </div>

            <h4 id="modalUnidadMedida" class="text-4xl font-bold text-slate-700">
                Crear unidad de medida
            </h4>

            <p class="text-slate-500 text-xl mt-2">
                Agregue una nueva unidad para organizar sus productos.
            </p>
        </div>

        <hr class="my-6 border-slate-200">

        <div id="divmsjalerta1"></div>
        
        <form id="formCrearUpdateUnidad" class="formulario" action="/admin/almacen/crear_unidadmedida" enctype="multipart/form-data" method="POST">
            <input type="hidden" id="idunidadmedida" name="idunidad" value="">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mt-6">
                <div class="formulario__campo">
                    <label
                        for="unidad"
                        class="block text-xl font-semibold text-slate-700 mb-2 text-left">
                        Nombre de la unidad
                    </label>
                    <div class="formulario__dato focus-within:!border-indigo-600 border border-slate-300 rounded-xl flex items-center h-14 overflow-hidden bg-whit">
                        <input class="formulario__input focus-within:!border-indigo-600 border border-gray-300 rounded-lg flex items-center h-14 overflow-hidden" type="text" placeholder="Nombre de la unidad de medida" id="unidad" name="nombre" value="<?php echo $unidadmedida->nombre??'';?>" required>
                        <!-- <label data-num="28" class="count-charts" for="">28</label> -->
                    </div>
                </div>
            </div>
            <div class="text-right mt-6">
                <button class="btn-md btn-turquoise !py-4 !px-6 !w-[100px]" type="button" value="Salir">Salir</button>
                <input id="btnEditarCrearUnidadMedida" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[100px]" type="submit" value="Crear">
            </div>
        </form>
    </dialog><!--fin crear/editar unidad-->
</div>