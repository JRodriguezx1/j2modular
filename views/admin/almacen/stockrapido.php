<p class="text-xl mt-0 text-gray-600">Stock de productos</p>
<div class="flex items-center gap-2 bg-white text-gray-800 font-semibold text-2xl rounded-md border border-gray-300 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-400 !mb-4 !py-4 px-6 !w-[200px]">
    <i class="fas fa-sync-alt text-2xl text-gray-600"></i>
    <button id="reinciarinv" class="text-2xl text-gray-600">Reiniciar Inventario</button>
</div>
<div class="overflow-x-auto">
    <table id="tablaStockRapido" class="display responsive nowrap tabla min-w-[500px]" width="100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Producto</th>
                <th>Categoria</th>
                <th>Stock</th>
                <th>Unidad</th>
                <th>Aux</th>
                <th>Agregado</th>
                <th class="accionesth">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $index => $value): ?>
                <?php if(($value->tipoproducto == '0' || ($value->tipoproducto == '1' && $value->tipoproduccion == '1'))&&$value->visible == 1): ?>  <!-- productos simple o compuesto tipo construccion -->
                <tr class="fila producto" data-idproducto="<?php echo $value->productoid;?>"> 
                    <td class=""><?php echo $index+1;?></td>
                    <td class=""><div class="w-72 whitespace-normal"><?php echo $value->nombre;?></div></td> 
                    <td class=""><div class="w-52 whitespace-normal"><?php echo $value->categoria;?></div></td>
                    <td class=""><div class="text-center px-3 py-4 rounded-lg <?php echo $value->stock<=$value->stockminimo?'text-red-800 bg-red-50':'text-cyan-600 bg-cyan-50';?>"><?php echo $value->stock;?></div></td>
                    <td class=""><?php echo $value->unidadmedida;?></td>
                    <td class=""><?php echo $value->stockaux;?></td>
                    <td class=""><?php echo $value->fecha_ingreso;?></td>
                    <td class="accionestd">
                        <?php if($value->tipoproducto == '0'): ?>
                        <div class="acciones-btns btnsproducto" id="<?php echo $value->productoid;?>" data-nombre="<?php echo $value->nombre;?>" data-stock="<?php echo $value->stock;?>">
                            <button class="btn-xs btn-bluelight descontarStock"><i class="fa-solid fa-minus"></i></button>
                            <button class="btn-xs btn-blue aumentarStock"><i class="fa-solid fa-plus"></i></button>
                            <button class="btn-xs btn-turquoise ajustarStock"><i class="fa-solid fa-wrench"></i></button>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- subproductos -->
            <?php foreach($subproductos as $index => $value): ?>  
                <tr class="fila subproducto" data-idsubproducto="<?php echo $value->subproductoid;?>"> 
                    <td class=""><?php echo $index+1;?></td>
                    <td class=""><div class="w-80 whitespace-normal">(I) <?php echo $value->nombre;?></div></td> 
                    <td class="" ><?php echo $value->categoria??'';?></td> 
                    <td class=""><div class="text-center px-3 py-4 rounded-lg <?php echo $value->stock<=$value->stockminimo?'text-red-800 bg-red-50':'text-cyan-600 bg-cyan-50';?>"><?php echo $value->stock;?></div></td>
                    <td class=""><?php echo $value->unidadmedida;?></td>
                    <td class=""><?php echo $value->stockaux;?></td>
                    <td class=""><?php echo $value->fecha_ingreso;?></td>
                    <td class="accionestd">
                        <div class="acciones-btns btnssubproducto" id="<?php echo $value->subproductoid;?>" data-nombre="<?php echo $value->nombre;?>" data-stock="<?php echo $value->stock;?>">
                            <button class="btn-xs btn-bluelight descontarStock"><i class="fa-solid fa-minus"></i></button>
                            <button class="btn-xs btn-blue aumentarStock"><i class="fa-solid fa-plus"></i></button>
                            <button class="btn-xs btn-turquoise ajustarStock"><i class="fa-solid fa-wrench"></i></button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- MODAL PARA AJUSTAR SUMAR, PRODUCIR O DESCONTAR UNUDADES-->
  <dialog class="midialog-sm !p-12" id="miDialogoStock">
      <h4 id="modalStock" class="font-semibold text-gray-600 mb-4">Ingresar cantidad a inventario</h4>
      <div id="divmsjalertaStock"></div>
      <form id="formStock" class="formulario" action="/" method="POST">

          
          <p id="nombreItemstockrapido" class="inline-block mt-2 px-4 py-2 text-gray-900 text-2xl font-bold self-center rounded-lg shadow-lg"></p>

          <div class="formulario__campo">
              <label class="formulario__label" for="selectStockRapidoUndmedida">Unidad de medida vendida</label>
              <select id="selectStockRapidoUndmedida" class="formulario__select bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="selectStockRapidoUndmedida" required>
                  <option value="" disabled selected>-Seleccionar-</option>
                    <!-- se inserta por ts en almacen.ts -->
              </select>
          </div>

          <div class="formulario__campo">
              <label class="formulario__label" for="cantidadStockRapido">Stock</label>
              <div class="formulario__dato">
                  <input id="cantidadStockRapido" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="number" min="0" step="0.01" placeholder="Ingresar cantidad" name="cantidadStockRapido" value="" required>
              </div>
          </div>

          <div class="formulario__campo">
              <label class="formulario__label" for="stockAux">Stock Auxiliar</label>
              <div class="formulario__dato">
                  <input id="stockAux" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="number" min="0" step="0.01" placeholder="Ingresar cantidad" name="stockAux" value="">
              </div>
          </div>

          <div class="text-right">
              <button class="btn-md btn-turquoise !py-4 !px-6 !w-[140px]" type="button" value="salir">Salir</button>
              <input id="btnAjusteStock" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[140px]" type="submit" value="Confirmar">
          </div>
      </form>
  </dialog>