<div class="box ensamblaje !pb-10">
   <a href="/admin/almacen/productos" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  <div class="w-full tlg:w-5/6 mx-auto rounded-lg shadow-lg px-6 pt-8">
    <div class="flex items-start gap-4">

        <span class="material-symbols-outlined text-indigo-600 text-6xl">
            precision_manufacturing
        </span>

        <div>
            <h2 class="text-4xl font-bold text-slate-800">
                <?php echo $producto->nombre;?>
            </h2>

            <span class="inline-flex mt-3 px-4 py-2 rounded-full bg-indigo-100 text-indigo-700 text-lg font-semibold mb-4">
                Unidad: <?php echo $producto->unidadmedida;?>
            </span>
        </div>

    </div>

    <form id="formAddSubproducto" class="formulario" action="/" method="POST">
      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 mb-8">
        <p class="mt-4 text-xl text-slate-500 leading-relaxed">
            Configure los insumos necesarios para fabricar este producto compuesto.
        </p>
        
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <input id="idproducto" type="hidden" value="<?php echo $producto->id;?>">  
          <div class="sm:col-span-6">
            <label for="subproducto" class="block text-2xl font-medium text-gray-600">Subproducto</label>
            <div class="mt-2">
              <select id="subproducto" name="subproducto" autocomplete="subproducto-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" multiple="multiple" required>
                <?php foreach($subproductos as $value): ?>
                  <option 
                    value="<?php echo $value->id;?>" 
                    data-subproducto="<?php echo $value->nombre;?>" 
                    data-costo="<?php echo $value->precio_compra;?>">
                    <?php echo $value->nombre.', Unidad: '.$value->unidadmedida;?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="sm:col-span-2 md:col-span-3 tlg:col-span-2">
            <label for="unidadmedida" class="block text-2xl font-medium text-gray-600">Unidad de medida</label>
            <div class="mt-2 grid grid-cols-1">
              <select id="unidadmedida" name="unidadmedida" autocomplete="unidadmedida-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                <!-- se carga las unidades de medida del subproducto en ensamblar.ts -->
              </select>
              <!-- <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
              </svg> -->
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="cantidad" class="block text-2xl font-medium text-gray-600">Cantidad</label>
            <div class="mt-2">
              <input id="cantidad"
                     name="cantidad"
                     type="text"
                     autocomplete="cantidad ID"
                     class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                     maxlength="7"
                     oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                     required>
            </div>
          </div>

          <div class="sm:col-span-2">
            <label for="rendimientoestandar" class="block text-2xl font-medium text-gray-600">Rendimiento estandar</label>
            <div class="mt-2">
              <input id="rendimientoestandar"
                     type="text"
                     autocomplete="rendimientoestandar ID"
                     class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                     maxlength="7"
                     value="<?php echo $producto->rendimientoestandar;?>"
                     oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                     required>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-200">
          <div class="flex items-center justify-center gap-3 mb-6">
              <span class="material-symbols-outlined text-indigo-600">
                  tune
              </span>

              <p class="font-semibold text-2xl text-slate-700 uppercase tracking-wide">
                  Variaciones
              </p>
          </div>
          <div class="flex flex-wrap gap-4">
            <div class="flex-1 basis-56">
                <label class="block text-xl font-semibold text-slate-700 text-left" for="tipoGrupo">Tipo de grupo</label>
                <select id="tipoGrupo" class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                  <option value="0">Insumo por defecto</option>
                  <option value="1">Seleccion multiple</option>
                  <option value="2">Seleecion unica</option>
                </select>
            </div>
            <div class="flex-1 basis-56">
                <label class="block text-xl font-semibold text-slate-700 text-left" for="marcadoDefecto">Marcado por defecto</label>
                <select id="marcadoDefecto" class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                  <option value="0">No</option>
                  <option value="1" selected>Si</option>
                </select>
            </div>
            <div class="flex-1 basis-56">
                <label class="block text-xl font-semibold text-slate-700 text-left" for="permitirAumentar">Permitir aumentar</label>
                <select id="permitirAumentar" class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                  <option value="0" selected>No</option>
                  <option value="1">Si</option>
                </select>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 pt-6 mt-6 border-t border-slate-200">
          <button class="btn-md btn-turquoise !py-4 !px-6 !w-[125px] salir" type="button" value="salir">Salir</button>
          <input id="btnCrearAddSubproducto" class="btn-md btn-indigo !py-4 px-6 !w-[125px] crearAddSubproducto" type="submit" value="Asociar">
      </div>

      <div>
        <h5 class="mb-2 mt-4 text-slate-600 font-medium">Lista de insumos de produccion</h5>
          <div class="w-full pt-4 pb-14 listaSubproductos">
            <?php foreach($subproductosenlazados as $value): ?>
              <div id="<?php echo $value->id_subproducto;?>"
                  class="mb-5 flex justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-all duration-200 items-center"
                  role="alert">
                <div class="flex-1 pr-6">  
                  <div>
                    <div class="mb-3">
                        <p class="text-2xl font-semibold text-slate-800">
                            <?php echo $value->nombre;?>
                        </p>

                        <p class="mt-2 text-indigo-600 font-semibold text-lg">
                            <?php echo $value->cantidadsubproducto." ".$value->unidadmedida;?>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4 mt-4 text-lg">
                        <span class="rounded-md px-4 py-1.5 bg-slate-100 text-slate-700">

                            📦 <?php echo $value->grupos_insumos;?>

                        </span>

                        <span class="rounded-md px-4 py-1.5 font-medium
                            <?= $value->seleccionado
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-rose-100 text-rose-700'; ?>">

                            <?= $value->seleccionado
                                ? '✔ Seleccionado'
                                : '✖ No seleccionado'; ?>

                        </span>

                        <span class="rounded-md px-4 py-1.5 font-medium
                            <?= $value->permite_aumentar
                                ? 'bg-indigo-100 text-indigo-700'
                                : 'bg-amber-100 text-amber-700'; ?>">

                            <?= $value->permite_aumentar
                                ? '➕ Permite aumentar'
                                : '🚫 Cantidad fija'; ?>

                        </span>
                    </div>
                  </div>
                </div>
                <button
                    type="button"
                    class="self-start mt-1 rounded-full p-2 text-slate-400 transition-all duration-200 hover:bg-red-100 hover:text-red-600">

                    <span
                        id="<?php echo $value->id_subproducto;?>"
                        class="material-symbols-outlined">

                        close

                    </span>
                </button>
              </div>
            <?php endforeach; ?>
            <!--        
            <div class="flex items-center justify-between p-4 text-blue-600 bg-blue-100 rounded-lg shadow-md shadow-blue-500/30" role="alert">
              <p class="m-0"><strong>34 Kilogramos</strong>.  Arena media de rio</p>
              <button><span class="material-symbols-outlined">cancel</span></button>
            </div>
            <div class="mt-4 flex items-center justify-between p-4 text-blue-600 bg-blue-100 rounded-lg shadow-md shadow-blue-500/30" role="alert">
              <p class="m-0"><strong>34 Kilogramos</strong>.  Arena media de rio</p>
              <button><span class="material-symbols-outlined">cancel</span></button>
            </div>-->

          </div>

      </div>
    </form>
  </div> 

</div>