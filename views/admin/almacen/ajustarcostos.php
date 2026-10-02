<div class="box ajustarcostos !pb-20">
  <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2 ">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
    </a>
  <h4 class="text-gray-600 mb-4 mt-4">Actualizar costos de inventario</h4>
  <?php include __DIR__. "/../../templates/alertas.php"; ?>
  
  
  <table class="display responsive nowrap tabla" width="100%" id="tablaAjustarCostos">
      <thead>
          <tr>
              <th>Nº</th>
              <th>Producto</th>
              <th>Categoria</th>
              <th>Sku</th>
              <th>Cantidad</th>
              <th>Unidad</th>
              <th>Costo</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($productos as $index => $value): ?>
            <?php if($value->tipoproducto == 0): ?> <!-- tipoproducto=0 es simple -->
            <tr class="fila producto" data-idproducto="<?php echo $value->id;?>" data-costo="<?php echo $value->precio_compra;?>"> 
                <td class=""><?php echo $index+1;?></td>
                <td class=""><div class="w-80 whitespace-normal"><?php echo $value->nombre;?></div></td> 
                <td class="" ><?php echo $value->categoria;?></td>
                <td class=""><?php echo $value->sku;?></td>
                <td class="cantidad">1</td>
                <td class="" > <select class="formulario__select" required>
                                  <!--<option value="" disabled selected>-Seleccionar-</option>-->
                                  <option data-factor="1" value="" ><?php echo $value->unidadmedida;?></option>
                               </select>
                </td>
                <td class=""><input 
                                type="text" 
                                class="inputAjustarCosto rounded-md px-3 py-1.5 text-xl text-gray-500 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:outline-indigo-600" 
                                value="<?php echo $value->precio_compra;?>"
                                maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                            >
                </td>
            </tr>
            <?php endif; ?>
          <?php endforeach; ?>


          <!-- subproductos -->
          <?php foreach($subproductos as $index => $value): ?>  
            <tr class="fila subproducto" data-idsubproducto="<?php echo $value->id;?>" data-costo="<?php echo $value->precio_compra;?>"> 
                <td class=""><?php echo $index+1;?></td>        
                <td class=""><div class="w-80 whitespace-normal">(s) <?php echo $value->nombre;?></div></td> 
                <td class="" ><?php echo $value->categoria??'';?></td> 
                <td class=""><?php echo $value->sku;?></td>
                <td class="cantidad">1</td>
                <td class="" > <select class="formulario__select" required>
                                  <option data-factor="1" value="" ><?php echo $value->unidadmedida;?></option>
                               </select>
                </td>
                <td class=""><input 
                                type="text" 
                                class="inputAjustarCosto rounded-md px-3 py-1.5 text-xl text-gray-500 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:outline-indigo-600" 
                                value="<?php echo $value->precio_compra;?>"
                                maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                            >
                </td>
            </tr>
          <?php endforeach; ?>

      </tbody>
  </table>
</div>