<div class="box conversionUnidades !pb-20">
    <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
    </a>
    <h4 class="text-gray-600 mb-12 mt-12 ">Conversion de unidades</h4>
    
    
    <table id="tablaSubProductos" class="display responsive nowrap tabla" width="100%">
      <thead>
          <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Unidad base</th>
              <th>Codigo</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($subproductos as $index => $value): ?>
          <tr> 
              <td class=""><?php echo $value->id;?></td>
              <td class=""><?php echo $value->nombre;?></td>
              <td class=""><?php echo $value->unidadmedida;?></td>
              <td class=""><?php echo $value->sku;?></td>
              <td class="accionestd"><div class="acciones-btns" id="<?php echo $value->id;?>" data-unidadmedida="<?php echo $value->unidadmedida;?>">
                <button class="btn-md btn-turquoise editarUnidadMedida" title="Actualizar unidad de medida"><i class="fa-solid fa-pen-to-square"></i></button>
              </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
    </table>


    <dialog id="miDialogoConversionUnidad" class="midialog-sm p-8 rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                <i class="fa-solid fa-ruler-combined text-indigo-600 text-2xl"></i>
            </div>
            <h4 id="modalConversionUnidad" class="text-4xl font-bold text-slate-700">Conversion de unidades de medida</h4>
            <p class="text-slate-500 text-xl mt-2">Agregue una nueva unidad de medida equivalente al insumo.</p>
        </div>

        <hr class="my-6 border-slate-200">
        <div id="divmsjalerta1"></div>

        <form id="formConversionUnidad" class="formulario">
            <h5 id="nombreInsumo" class="font-medium text-gray-600 text-center">xxx</h5>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mt-6">

                <div class="formulario__campo">
                    <label class="block text-xl font-semibold text-slate-700 text-left" for="unidadesMedidas">Nueva unidad de medida</label>
                    <select id="unidadesMedidas" class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required>
                        
                    </select>
                </div>
                
                <div class="formulario__campo">
                    <label class="block text-xl font-semibold text-slate-700 text-left" for="equivalente">Equivalente A:</label>
                    <div class="flex gap-4">
                        <input
                            id="equivalente" 
                            class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" 
                            type="text"
                            placeholder="equivalente de la deuda"
                            name="valorpagado"
                            value=""
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                            required
                        >
                        <input id="unidadMedidaBase2" class="border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" value="" readonly>
                    </div>
                </div>
            </div>
            

            <div class="text-right mt-6">
                <button class="btn-md btn-turquoise !py-4 !px-6 !w-[136px]" type="button" value="Salir">Salir</button>
                <input class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[136px]" type="submit" value="Confirmar">
            </div>
        </form>


        <div id="contenedor">
            
        </div>
        
        
    </dialog><!--fin editar conversion unidad-->
</div>