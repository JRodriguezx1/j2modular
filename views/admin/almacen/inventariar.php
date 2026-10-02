<div class="box inventariar">
    <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
        </a>
    <h4 class="text-gray-600 mb-12 mt-4">inventariar en construccion</h4>
    <div>
        <table class="display responsive nowrap tabla" width="100%" id="">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Producto</th>
                    <th>impuesto</th>
                    <th>Costo</th>
                    <th>Precio venta</th>
                    <th>Utilidad</th>
                    <th>Rentabilidad</th>
                    <th class="accionesth">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $index => $value): ?>
                <tr> 
                    <td class=""><?php echo $index+1;?></td>        
                    <td class=""><?php echo $value->nombre;?></td> 
                    <td class=""><?php echo $value->impuesto;?>%</td>
                    <td class="" ><strong>$ </strong><?php echo $value->precio_compra;?></td> 
                    <td class=""><strong>$ </strong><?php echo number_format($value->precio_venta, '0', ',', '.');?></td>
                    <td class="text-blue-600 text-xl bg-blue-50 px-3 py-1.5 tracking-wide rounded-lg">$<?php echo number_format($value->precio_venta - $value->precio_compra, '0', ',', '.');?></td>
                    <td class=" flex items-center justify-center text-purple-600 text-xl bg-purple-50 px-3 py-1.5 tracking-wide rounded-lg">%<?php echo number_format((($value->precio_venta - $value->precio_compra)/$value->precio_venta)*100, '1', ',', '.')?></td>
                    <td class="accionestd"><div class="acciones-btns" id="<?php echo $value->id;?>"><button class="btn-xs btn-turquoise">Mas</button></div></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>