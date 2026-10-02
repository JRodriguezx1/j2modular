
<div class="box productos mb-20">
  <a href="/admin/almacen" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>

  <h4 class="text-gray-600 mb-12 mt-4">Productos</h4>
  <div class="divmsjalerta0"><?php include __DIR__. "/../../templates/alertas.php"; ?></div>
  
  <div class="flex flex-wrap gap-2">
    <button id="crearProducto" class="btn-md btn-blueintense mb-4 !py-4 px-6 !bg-indigo-600">Crear producto</button>
    <a class="btn-md btn-turquoise mb-4 !py-4 !px-6" href="/admin/almacen/categorias">Ir a categorias</a>
    <form action="/admin/almacen/downexcelproducts" method="POST">
        <button class="btn-md btn-light mb-4 !py-2 !px-4" name="downexcel" title="Descargar en excel"><span class="material-symbols-outlined text-[24px] leading-none">download</span></button>
    </form>
    <button id="btnUploadExcel" class="btn-md btn-light mb-4 !py-2 !px-4" name="upexcel"><span class="material-symbols-outlined text-[24px] leading-none">upload</span></button>
  </div>

  <table class="display responsive nowrap tabla" width="100%" id="tablaProductos">
      <thead>
          <tr>
              <th>Nº</th>
              <th>Imagen</th>
              <th>Producto</th>
              <th>Categoria</th>
              <th>Marca</th>
              <th>Codigo</th>
              <th>Precio venta</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($productos as $index => $value): 
            if($value->visible == 1):?>
          <tr>
              <td class=""><?php echo $index+1;?></td>
              <td class=""><div class=" text-center "><img class="inline" style="width: 50px;" src="/build/img/<?php echo $value->foto;?>" alt=""></div></td>
              <td class=""><div class="w-80 whitespace-normal"><?php echo $value->nombre;?></div></td> 
              <td class="" ><?php echo $value->nombrecategoria;?></td>
              <td class=""><?php echo $value->marca;?></td>
              <td class=""><?php echo $value->sku;?></td>
              <td class="">$<?php echo number_format($value->precio_venta, "2", ",", ".");?></td>
              <td class="accionestd"><div class="acciones-btns my-[0.7rem]" id="<?php echo $value->id;?>">
                    <?php if($value->tipoproducto == '1'): //0=simple,   1=compuesto ?> 
                        <a class="btn-xs btn-blue" title="Agregar Materia Prima" href="/admin/almacen/componer?id=<?php echo $value->id;?>"><i class="fa-solid fa-subscript text-[17px] leading-none"></i></a>
                    <?php endif; ?>
                    <button class="btn-xs btn-lima" title="Más opciones"><i class="fa-solid fa-circle-plus text-[17px] leading-none"></i></button>
                    <button class="btn-xs btn-turquoise editarProductos" title="Actualizar Producto"><i class="fa-solid fa-pen-to-square text-[17px] leading-none"></i></button>
                    <button class="btn-xs <?php echo $value->habilitarventa?'btn-light':'btn-orange';?> bloquearProductos" title="Bloquear Producto"><span class="material-symbols-outlined text-[18px] leading-none">hide_source</span></button>
                    <button class="btn-xs btn-red eliminarProductos" title="Eliminar Producto"><i class="fa-solid fa-trash-can text-[17px] leading-none"></i></button>
              </div></td>
          </tr>
          <?php endif; endforeach; ?>
      </tbody>
  </table>

  <!-- MODAL PARA CREAR/ACTUALIZAR PRODUCTOS-->
  <dialog id="miDialogoProducto" class="midialog-sm p-12">
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <h4 id="modalProducto" class="font-semibold text-gray-700 mb-4">Crear producto</h4>
        <button id="btnXCerrarModalproducto" class="p-2 rounded-lg hover:bg-gray-100 transition">
            <i class="fa-solid fa-xmark text-gray-600 text-3xl"></i>
        </button>
    </div>
    <div id="divmsjalerta1"></div>
    <form id="formCrearUpdateProducto" class="formulario" action="/admin/almacen/crear_producto" enctype="multipart/form-data" method="POST">
        
        <div class="formulario__campo">
            <label class="formulario__label" for="categoria">Categoria</label>
            <select id="categoria" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="idcategoria" required>
                <option value="" disabled selected>-Seleccionar-</option>
                <?php foreach($categorias as $categoria): 
                    if($categoria->visible == 1):    ?>
                        <option value="<?php echo $categoria->id;?>" ><?php echo $categoria->nombre;?></option>
                <?php endif; endforeach; ?>
            </select>             
        </div>
        <div class="formulario__campo">
            <label class="formulario__label" for="nombre">Nombre</label>
            <div class="formulario__dato focus-within:!border-indigo-600 border border-gray-300 rounded-lg flex items-center h-14 overflow-hidden">
                <input id="nombre" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Nombre del producto" name="nombre" value="<?php echo $producto->nombre??'';?>" required>
                <!--<label data-num="46" class="count-charts" for="">46</label>-->
            </div>
        </div>
        <div class="formulario__campo">
            <label class="formulario__label" for="tipoproducto">Tipo de producto</label>
            <select id="tipoproducto" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="tipoproducto" required>
                <option value="0">Simple</option>
                <option value="1">Compuesto</option>
            </select>          
        </div>

        <div class="formulario__campo">
            <label class="formulario__label" for="idunidadmedida">Unidad de medida</label>
            <select id="idunidadmedida" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="idunidadmedida" required>
                <?php foreach($unidadesmedida as $unidadmedida): ?>
                <option value="<?php echo $unidadmedida->id;?>" <?php echo $unidadmedida->id==1?'selected':'';?>><?php echo $unidadmedida->nombre;?></option>
                <?php endforeach; ?>
            </select>  
        </div>

        <div class="formulario__campo">
            <label class="formulario__label" for="precioventa">Precio venta incluido impuesto</label>
            <div class="flex gap-2">
                <input 
                    id="precioventa" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" 
                    type="text" 
                    placeholder="Precio de venta incluido el impuesto" 
                    name="precio_venta" 
                    value="<?php echo $producto->precio_venta??'';?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                    required
                >
                <button id="btnAddNewPrice" type="button" class="btn-md btn-blue !text-3xl">+</button>
            </div>
            <div id="contentnuevosprecios" class=" space-y-4">

            </div>
        </div>
        
        <div class="formulario__campo habtipoproduccion" style="display: none;">
            <label class="formulario__label" for="tipoproduccion">Tipo de produccion</label>
            <select id="tipoproduccion" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="tipoproduccion">
                <option disabled selected>-Seleccionar-</option>
                <option value="0">Inmediato</option>
                <option value="1">Construccion</option>
            </select>          
        </div>


        <div class="formulario__campo">
            <div class="formulario__contentinputfile formulario__contentinputfile--sm">
                <div class="formulario__imginputfile"><img id="imginputfile" src="" alt=""></div>
                <p class="text-greymouse">Subir imagen</p>
            </div>
            <input id="upImage" class="formulario__inputfile" type="file" accept="image/*" name="foto" hidden>
            <button id="customUpImage" class="text-white bg-gradient-to-br from-indigo-700 to-[#00CFCF] hover:bg-gradient-to-bl hover:from-[#00CFCF] hover:to-indigo-700 focus:ring-4 focus:outline-none focus:ring-[#99fafa] font-medium rounded-lg text-sm px-5 py-2.5 text-center !w-[23%] !mx-auto mb-2" type="button">Cargar Imagen</button>
        </div>
        
        <div class="accordion md:px-12 !mt-4">
            <input type="checkbox" id="first">
            <label class="etiqueta flex items-center justify-center gap-2 cursor-pointer text-gray-500 hover:text-indigo-600 select-none" for="first">Mostrar/Ocultar mas opciones</label>

            <!-- <div class="formulario__campo">
                <label class="formulario__label" for="idunidadmedida">Unidad de medida</label>
                <select id="idunidadmedida" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" name="idunidadmedida" required>
                    <?php //foreach($unidadesmedida as $unidadmedida): ?>
                    <option value="<?php //echo $unidadmedida->id;?>" <?php //echo $unidadmedida->id==$producto->idunidadmedida?'selected':'';?>><?php //echo $unidadmedida->nombre;?></option>
                    <?php //endforeach; ?>
                </select>  
            </div> -->
            
            
            
            <div class="wrapper">
              <div class="wrapper-content">
                <div id="otrosopciones" class="content flex flex-col w-full mx-auto">  
                    
                    
                    <div class="formulario__campo stock">
                        <!--<label class="formulario__label" for="stock">Cantidad</label>-->
                        <div class="formulario__dato">
                            <input id="stock" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 hidden" 
                                type="text" placeholder="Precio de venta" 
                                name="stock" 
                                oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, '')|| 0"
                                value="">
                        </div>
                    </div>
                    

                    <div class="formulario__campo preciocompra">
                        <label class="formulario__label" for="preciocompra">Precio compra</label>
                        <div class="formulario__dato">
                            <input id="preciocompra" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" 
                                type="text" 
                                placeholder="Precio de venta" 
                                name="precio_compra" 
                                oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, '')|| 0"
                                value="">
                        </div>
                    </div>

                    <div class="formulario__campo">
                        <label class="formulario__label" for="marca">Marca</label>
                        <div class="formulario__dato">
                            <input id="marca" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Marca del producto" name="marca" value="<?php echo $producto->marca??'';?>">
                            <!--<label data-num="36" class="count-charts" for="">36</label>-->
                        </div>
                    </div>
                
                    <div class="mb-4">
                      <div class="formulario__campo">
                        <label class="formulario__label" for="stockminimo">Stock minimo</label>
                        <input id="stockminimo" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5e h-14 text-xl focus:outline-none focus:ring-1" type="number" min="0" step="0.01" placeholder="Establecer el stock minimo" name="stockminimo" value="<?php echo $producto->stockminimo??'';?>">      
                      </div>  
                    </div>

                    <div class="formulario__campo">
                        <label class="formulario__label" for="impuesto">Impuesto</label>
                        <div class="formulario__dato">
                            <!--<input 
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1" 
                                type="text" placeholder="Impuesto del producto en %" 
                                id="impuesto" 
                                name="impuesto" 
                                value=""
                                oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, '')|| 0"
                            >-->
                            <select
                            id="impuesto"
                            name="porcentaje_de_impuesto"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                            >
                                <span class="text-indigo-600 font-bold">
                                    <optgroup label="IVA">
                                </span>
                                <option value="0" >Exento – 0%</option>
                                <option value="5" >Bienes / Servicios al 5%</option>
                                <option value="16" >Contratos antes Ley 1819 – 16%</option>
                                <option value="19" >Tarifa general – 19%</option>
                                <option value="" >Excluido de IVA</option> <!-- valor por defecto -->
                                </optgroup>
                                <span class="text-indigo-600 font-bold">
                                    <optgroup class="text-indigo-60" label="INC">
                                </span>
                                <option value="8">Impuesto Nacional al Consumo – 8%</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="formulario__campo">
                        <label class="formulario__label" for="sku">SKU Producto</label>
                        <div class="formulario__dato">
                            <input id="sku" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Codigo del producto" name="sku" value="<?php echo $producto->sku??'';?>">
                            <!--<label data-num="36" class="count-charts" for="">36</label>-->
                        </div>
                    </div>

                    <div class="mb-4">
                      <div class="formulario__campo">
                        <label class="formulario__label" for="percentcomision">Comision</label>
                        <input id="percentcomision" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5e h-14 text-xl focus:outline-none focus:ring-1" type="number" min="0" step="0.01" placeholder="Porcentaje de la comision" name="percentcomision" value="<?php echo $producto->percentcomision??'';?>">      
                      </div>  
                    </div>

                    <div class="formulario__campo">
                        <label class="formulario__label" for="prioridadcomision">Prioridad de comision</label>
                        <select id="prioridadcomision" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="prioridadcomision" required>
                            <option value="0">Usuario</option>
                            <option value="1">Producto</option>
                        </select>         
                    </div>


                </div>
              </div>
            </div>
        </div> <!-- fin accordion  -->
        
        <div class="text-right">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[136px]" type="button" value="salir">Salir</button>
            <input id="btnEditarCrearProducto" class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[136px]" type="submit" value="Crear">
        </div>
    </form>
  </dialog><!--fin crear/editar producto-->

  <!-- MODAL IMPORTAR EXCEL -->
  <dialog id="miDialogoImportarExcel" class="midialog-sm p-12">
    <h4 class=" text-gray-700 font-semibold">Importar:</h4>
    <form id="formImportarExcel" class="formulario" action="/admin/almacen/uploadExcel" enctype="multipart/form-data" method="POST">  
      <div class="border-b border-gray-900/10 pb-10 mb-3">
        
        <p class="mt-2 text-xl text-gray-600">Subir productos por medio de archvio de excel.</p>

        <div class="mt-6 grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-6">

          <div class="sm:col-span-6">
            <label for="archivoexcel" class="block text-2xl font-medium text-gray-600">Archivo Excel</label>
            <div class="mt-2">
              <input type="file" id="archivoexcel" name="archivoexcel" accept=".xlsx,.xls" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1" required />
            </div> 
          </div>

          <div class="sm:col-span-3">
              <a class="btn-md btn-lima" href="/admin/descarga/plantillaimportarproductos" target="_blank" rel="noopener noreferrer">Descargar plantilla</a> 
          </div>
          <div class="sm:col-span-3">
              <a class="btn-md btn-blue" href="/admin/descarga/instruccionesimportarproductos" target="_blank" rel="noopener noreferrer">Instrucciones</a>
          </div>

        </div>

      </div>
        
      <div class="text-right">
          <button class="btn-md btn-turquoise !py-4 !px-6 !w-[125px]" type="button" value="Cancelar">Cancelar</button>
          <input class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[125px]" type="submit" value="Agregar">
      </div>
    </form>
  </dialog>
</div>