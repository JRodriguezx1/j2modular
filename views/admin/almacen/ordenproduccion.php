
    <div class="max-w-4xl mx-auto text-center">

        <h4 class="font-extrabold leading-9 text-gray-900">Ingresar orden de produccion</h4>

    

        <div class="mt-10 mb-8 text-start">
            <label for="itemAproducir" class="block text-2xl font-medium text-gray-600">Producto/Insumo</label>
            <div class="mt-2 grid grid-cols-1">
                <select id="itemAproducir" name="itemAproducir" autocomplete="itemAproducir-name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" multiple="multiple" required>
                    <?php foreach($productos as $value):
                        if($value->tipoproducto == 1 && $value->tipoproduccion):?>
                            <option 
                                data-idund="<?php echo $value->idunidadmedida;?>"
                                data-nombreund="<?php echo $value->unidadmedida;?>"
                                data-stock="<?php echo $value->stock;?>"
                                data-precio_compra="<?php echo $value->precio_compra;?>"
                                data-precio_venta="<?php echo $value->precio_venta;?>"
                                value="<?php echo $value->productoid;?>">
                                <?php echo $value->nombre;?>
                            </option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg flex flex-wrap gap-4 mb-4 p-8">
            <button id="ingresarProduccion" class="btnproduccion btn-command !border-0 shadow-lg"><span class="material-symbols-outlined text-green-500">assignment_add</span>Ingresar Produccion</button>
            <button id="descontarProduccion" class="btnproduccion btn-command !border-0 shadow-lg"><span class="material-symbols-outlined text-red-500">playlist_remove</span>Descontar Cantidad</button>
            <button id="ajustarProduccion" class="btnproduccion btn-command !border-0 shadow-lg"><span class="material-symbols-outlined text-cyan-500">checkbook</span>Ajustar Cantidad</button>
        </div>    

    </div>

    <div class="max-w-6xl mx-auto">
            <dl class="bg-white rounded-lg shadow-lg sm:grid sm:grid-cols-3">
                <div class="flex flex-col p-6 text-center border-b border-gray-100 sm:border-0 sm:border-r">
                    <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
                        Disponibilidad
                    </dt>
                    <dd class="order-1 text-4xl font-extrabold leading-none text-indigo-600"
                        aria-describedby="item-1" id="stock">
                        0
                    </dd>
                </div>
                <div class="flex flex-col p-6 text-center border-t border-b border-gray-100 sm:border-0 sm:border-l sm:border-r">
                    <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
                        Costo produccion
                    </dt>
                    <dd class="order-1 text-4xl font-extrabold leading-none text-indigo-600"
                        id="costoProduccion">
                        $0
                    </dd>
                </div>
                <div class="flex flex-col p-6 text-center border-t border-gray-100 sm:border-0 sm:border-l">
                    <dt class="order-2 mt-2 text-lg font-medium leading-6 text-gray-500">
                        Precio de venta
                    </dt>
                    <dd class="order-1 text-4xl font-extrabold leading-none text-indigo-600"
                        id="precioVenta">
                        $0
                    </dd>
                </div>
            </dl>
        </div>
    

<dialog id="miDialogoIngresarProduccion" class="midialog-sm !p-12">
    <h4 id="modalIngresarProduccion" class="font-semibold text-gray-600 mb-4">
        Ingreasar produccion a inventario
    </h4>

    <div id="divmsjalerta1"></div>

    <form id="formIngresarProduccion" class="formulario" action="/" method="POST">

        <p id="nombreItemAProducir" class="inline-block mt-2 px-4 py-2 text-gray-900 text-2xl font-bold self-center rounded-lg shadow-lg">
        </p>

        <div class="formulario__campo">
            <label class="formulario__label" for="selectIngresarProduccionUnidadmedida">
                Unidad de medida
            </label>

            <select
                class="formulario__select bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                id="selectIngresarProduccionUnidadmedida"
                name="selectIngresarProduccionUnidadmedida"
                required>
                <option value="" disabled selected>-Seleccionar-</option>
            </select>
        </div>

        <div class="formulario__campo">
            <label class="formulario__label" for="stockIngresarProduccion">
                Cantidad
            </label>

            <div class="formulario__dato">
                <input
                    class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                    id="stockIngresarProduccion"
                    type="number"
                    min="0"
                    placeholder="Precio de venta"
                    value=""
                    required>
            </div>
        </div>

        <div class="text-right">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[140px]"
                    type="button"
                    value="salir">
                Salir
            </button>

            <input id="btnIngresarProduccion"
                   class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[140px]"
                   type="submit"
                   value="Ingresar">
        </div>
    </form>
</dialog>
