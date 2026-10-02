<div class="box inventarioxproducto">
  <a href="/admin/reportes" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  <h4 class="text-gray-600 mb-8 mt-4">Inventario por producto</h4>
  
  <div>
    <table id="tablaStockRapido" class="display responsive nowrap tabla text-xl" width="100%">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Producto</th>
                <th>Categoria</th>
                <th>sku</th>
                <th>tipo</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Unidad</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $index => $value): ?>
                
                <tr class="fila producto" data-idproducto="<?php echo $value->productoid;?>"> 
                    <td class=""><?php echo $index+1;?></td>
                    <td class=""><div class="w-72 whitespace-normal"><?php echo $value->nombre;?></div></td>
                    <td class="" ><div class="w-28 whitespace-normal"><?php echo $value->categoria;?></div></td>
                    <td class="" ><?php echo $value->sku;?></td>
                    <td class="" ><?php echo $value->tipoproducto==1?'Compuesto':'Simple';?></td>
                    <td class="font-semibold" >$<?php echo number_format($value->precio_compra, 2); ?></td>
                    <td class="font-semibold" >$<?php echo number_format($value->precio_venta, 2); ?></td>
                    <td class=""><div class="text-center px-3 py-4 rounded-lg <?php echo $value->stock<=$value->stockminimo?'text-red-800 bg-red-50':'text-cyan-600 bg-cyan-50';?>"><?php echo $value->stock;?></div></td>
                    <td class=""><?php echo $value->unidadmedida;?></td>
                    <td class=""><?php echo $value->fecha_ingreso;?></td>
                </tr>
                
            <?php endforeach; ?>

            <!-- subproductos -->
            <?php foreach($subproductos as $index => $value): ?>  
                <tr class="fila subproducto" data-idsubproducto="<?php echo $value->subproductoid;?>"> 
                    <td class=""><?php echo $index+1;?></td>
                    <td class=""><div class="w-80 whitespace-normal">* <?php echo $value->nombre;?></div></td>
                    <td class="" ><?php echo $value->categoria??'';?></td>
                    <td class="" ><?php echo $value->sku;?></td>
                    <td class="" >Insumo</td>
                    <td class="font-semibold" >$<?php echo number_format($value->precio_compra, 2); ?></td>
                    <td class="font-semibold" >$<?php echo number_format($value->precio_venta, 2); ?></td>
                    <td class=""><div class="text-center px-3 py-4 rounded-lg <?php echo $value->stock<=$value->stockminimo?'text-red-800 bg-red-50':'text-cyan-600 bg-cyan-50';?>"><?php echo $value->stock;?></div></td>
                    <td class=""><?php echo $value->unidadmedida;?></td>
                    <td class=""><?php echo $value->fecha_ingreso;?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>

</div>