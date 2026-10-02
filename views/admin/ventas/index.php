<div class="box ventas !pb-28">
  <div class="flex flex-col tlg:flex-row">
    <div class="basis-2/3 min-w-0 px-4 pb-4 pt-0">
      <div id="divmsjalerta1"></div>
      <?php
        // =========================
        // MOSTRAR ALERTA (UI PRO)
        // =========================
          $estilos = [
            "warning" => [
              "bg" => "bg-gradient-to-r from-yellow-50 to-yellow-100",
              "border" => "border-yellow-300",
              "iconBg" => "bg-yellow-200",
              "iconColor" => "text-yellow-700",
              "text" => "text-yellow-900",
              "badge" => "bg-yellow-200 text-yellow-800"
            ],
            "danger" => [
              "bg" => "bg-gradient-to-r from-red-50 to-red-100",
              "border" => "border-red-300",
              "iconBg" => "bg-red-200",
              "iconColor" => "text-red-700",
              "text" => "text-red-900",
              "badge" => "bg-red-200 text-red-800"
            ]
          ];

      ?>
      
      <?php foreach($resolucionesVencidas as $value): 
        ($value->vencido??null)?$tipoAlerta = "danger":$tipoAlerta = "warning";
        $ui = $estilos[$tipoAlerta];
        $nombreResolucion = htmlspecialchars($value->nombre ?? '', ENT_QUOTES, 'UTF-8');
        $tipoFacturador = $value->idtipofacturador == 1 ? 'Electr&oacute;nica' : 'POS';
        $estadoResolucion = $tipoAlerta === "danger" ? "vencida" : "por vencer";
      ?>
        <div class="mb-2 animate-fadeSlide">
          <div class="flex items-start gap-3 border <?= $ui["border"] ?> <?= $ui["bg"] ?> rounded-2xl px-4 py-3 shadow-sm overflow-hidden">

            <!-- ICONO -->
            <div class="flex items-center justify-center w-10 h-10 rounded-xl <?= $ui["iconBg"] ?> shrink-0">
              <span class="material-symbols-outlined <?= $ui["iconColor"] ?> text-2xl"><?= $tipoAlerta === "danger" ? "error" : "warning" ?></span>
            </div>
            <!-- CONTENIDO -->
            <div class="flex flex-col min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold px-3 py-1 rounded-full <?= $ui["badge"] ?>"><?= $tipoAlerta === "danger" ? "VENCIDO" : "POR VENCER" ?></span>
                <span class="text-sm text-gray-500 uppercase tracking-wide break-words"><?= $nombreResolucion ?></span>
              </div>
              <p class="text-base font-semibold mb-0 leading-5 break-words <?= $ui["text"] ?>">Tu resoluci&oacute;n de facturaci&oacute;n <?= $tipoFacturador ?> est&aacute; <?= $estadoResolucion ?>.</p>
            </div>

          </div>
        </div>
        
      <?php endforeach; ?>
      <!-- FIN MENSALE DE VENCIMIENTO DE RESOLUCION -->
      
      <div class="mb-4">
        <button id="addcliente" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-3 shadow-sm hover:shadow-md hover:border-indigo-500 transition-all flex items-center justify-between">

          <!-- IZQUIERDA -->
          <div class="flex items-center gap-3 min-w-0">
            <div id="iconCliente" class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
              <span class="material-symbols-outlined text-3xl">person</span>
            </div>

            <div class="text-left min-w-0">
              <p class="text-sm text-gray-500 m-0 mb-1 uppercase font-semibold tracking-wide">Cliente</p>
              <p id="resumenCliente" class="m-0 text-xl font-semibold text-gray-900 leading-tight truncate">Seleccionar cliente</p>
            </div>
          </div>
          <!-- BADGE ESTADO -->
          <div class="flex items-center gap-2 shrink-0">
            <span id="badgeEstado" class="text-sm font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-700">SIN CLIENTE</span>
            <span class="material-symbols-outlined text-gray-400 text-xl">chevron_right</span>
          </div>
        </button>
      </div>

      <div id="hacker-list" class="paginadorventas">
        <div class="formulario__dato justify-center">
            <input id="buscarproducto" class="search bg-white border border-slate-300 text-gray-900 rounded-l-lg focus:border-indigo-600 block w-full p-3 h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Buscar producto, SKU o escanear codigo" name="buscarproducto" value="" required>
            <div class="grid place-items-center w-12 rounded-r-lg border-solid border border-slate-300 !border-l-0 hover:cursor-pointer bg-white">
              <span class="material-symbols-outlined text-2xl">search</span>
            </div>
        </div>
        
        <div class="mt-3 grid grid-cols-2 gap-2 xlg:grid-cols-3">
          <div class="relative min-w-0">
            <button id="btnCategorias" class="btn-md btn-indigo !mb-0 !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 !py-0 px-4 !text-xl !font-semibold !normal-case !leading-none">Categorias</button>
            <div id="menuCategorias" class="absolute left-0 top-full z-30 mt-2 hidden w-80 max-w-[calc(100vw-3rem)] max-h-[24rem] overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-xl ring-1 ring-slate-900/5">
              <a data-categoria="Todos" class="filtrocategorias categoria-activa flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg px-4 py-3.5 text-left text-xl font-semibold text-indigo-700 bg-indigo-50 transition-colors hover:bg-indigo-50 hover:text-indigo-700">
                <span class="truncate">Todos</span>
                <i class="fa-solid fa-check text-sm text-indigo-600"></i>
              </a>
              <?php foreach($categorias as $categoria): if($categoria->visible > 0): ?>
                <a data-categoria="<?= $categoria->nombre ?>" class="filtrocategorias flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg px-4 py-3.5 text-left text-xl font-medium text-slate-700 transition-colors hover:bg-indigo-50 hover:text-indigo-700">
                  <span class="truncate"><?= $categoria->nombre ?></span>
                  <i class="fa-solid fa-check hidden text-sm text-indigo-600"></i>
                </a>
              <?php endif; endforeach; ?>
            </div>
          </div>


          <!-- Boton Otros -->
          <button id="btnotros" class="btn-md btn-turquoise !mb-0 !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 !py-0 px-4 !text-xl !font-semibold !normal-case !leading-none">
            <i class="fas fa-th-large !text-2xl !leading-none"></i>
            Otros
          </button>

          <!-- Boton Adquiriente -->
          <button id="facturarA" class="btn-md col-span-2 rounded-lg border border-slate-300 bg-white !mb-0 !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 !py-0 px-4 !text-xl !font-semibold !normal-case !leading-none text-slate-900 shadow-[0_2px_6px_rgba(15,23,42,0.08)] hover:border-slate-400 hover:bg-slate-50 hover:shadow-[0_8px_18px_rgba(15,23,42,0.10)] focus:outline-none focus:ring-2 focus:ring-indigo-400 xlg:col-span-1">
            <i class="fas fa-user !text-2xl !leading-none"></i>
            Adquiriente
          </button>
        </div>

        <p class="mt-4 mb-0 text-base font-medium text-slate-500 uppercase tracking-wide">Categoria: <strong id="categorySelect" class="text-slate-900 normal-case text-lg">Todos</strong></p>

        <div id="productos" class="list grid gap-3 grid-cols-1 sm:grid-cols-2 tlg:grid-cols-1 xlg:grid-cols-2 2xlg:grid-cols-3 mt-3 border-solid border-t border-slate-300 pt-3"> <!-- contenedor de los productos -->
          <?php foreach($productos as $producto): 
            if($producto->visible==1&&$producto->estado==1):?>
            <div data-categoria="<?php echo $producto->categoria;?>" data-code="<?php echo $producto->sku;?>" class="producto rounded-lg bg-slate-100 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-sm transition-all grid grid-cols-[4.75rem_minmax(0,1fr)_3rem] grid-rows-[1fr_auto] items-center gap-x-3 px-3 py-3 min-h-[106px] group cursor-pointer" data-id="<?php echo $producto->ID;?>">
                <img
                    loading="lazy"
                    src="/build/img/<?php echo ($producto->foto!=null&&$producto->foto!='null'&&$producto->foto!='undefined')?$producto->foto:'default-product.png';?>" 
                    onerror="this.onerror=null;this.src='/build/img/default-product.png';"
                    class="row-span-2 block object-contain h-20 min-w-20 w-20 rounded-md bg-white border border-slate-100"
                    alt="Imagen de <?php echo $producto->nombre; ?>">
                
                <div class="flex flex-col justify-center gap-1.5 min-w-0 overflow-hidden">
                    <p class="card-producto m-0 text-lg leading-6 text-slate-700 font-semibold line-clamp-2"><?php echo $producto->nombre;?></p>
                    
                    <p class="precioVenta m-0 text-blue-600 text-xl font-bold">$<?php echo number_format($producto->precio_venta, '2', ',', '.'); ?></p>
                </div>
                <button id="precioadicional" title="Precio personalizado" class="grid h-11 w-11 place-items-center justify-self-end self-start rounded-lg text-indigo-600 bg-white border border-indigo-200 shadow-sm hover:text-white hover:bg-indigo-600 hover:border-indigo-600 transition-colors"><i class="fa-solid fa-pen-to-square text-2xl"></i></button>
                <p class="col-start-2 col-span-2 m-0 text-sm text-right font-medium text-slate-500">stock: 
                  <span class="stockProduct font-semibold text-lg text-indigo-600"><?php echo $producto->tipoproducto == 1 && $producto->tipoproduccion == 0 ? ' - ' : $producto->stock; ?></span>
                </p>
            </div>

          <?php endif; endforeach; ?>
        </div> <!-- fin contenedor de productos -->
        <div id="hacker-list" class="paginadorventas">
          <ul class="list">
            <!-- items aqui -->
          </ul>

          <!-- List.js inyectar -->
          <ul class="pagination mx-auto mt-4 w-fit max-w-full flex-wrap items-center justify-center !gap-1 rounded-full border border-slate-200 bg-gradient-to-br from-slate-50 to-white !p-2 shadow-sm empty:hidden [&_a]:!inline-flex [&_a]:h-[3.4rem] [&_a]:min-w-[3.4rem] [&_a]:items-center [&_a]:justify-center [&_a]:!rounded-full [&_a]:!border-0 [&_a]:!px-3 [&_a]:!py-0 [&_a]:text-lg [&_a]:font-extrabold [&_a]:!text-slate-600 [&_a]:transition-colors [&_a:hover]:!bg-indigo-50 [&_a:focus-visible]:outline [&_a:focus-visible]:outline-2 [&_a:focus-visible]:outline-indigo-600 [&_.active_a]:bg-gradient-to-br [&_.active_a]:from-[#5b52f0] [&_.active_a]:to-indigo-700 [&_.active_a]:!text-white [&_.active_a]:shadow-[0_0.55rem_1.2rem_rgba(79,70,229,0.24)]"></ul>
        </div>
      </div>

      <!-- Boton movil -->
      </style>
      
      <button id="btnCarritoMovil" 
        class="transition-shadow duration-300 hover:shadow-xl shadow-lg shadow-indigo-500/50 bottom-[82px] right-6 text-white text-lg px-4 py-4 text-center w-24 h-24 rounded-full tlg:hidden fixed z-51 bg-gradient-to-br from-indigo-700 to-[#00CFCF] hover:bg-gradient-to-bl hover:from-[#00CFCF] hover:to-indigo-700 focus:ring-4 focus:outline-none focus:ring-[#99fafa]  font-medium">
        <span class="material-symbols-outlined">leak_add</span>
        <span id="carritoMovilBadge">0</span>
      </button> 

      <div id="ventaCarritoToast" role="status" aria-live="polite" class=" tlg:hidden">
        <span class="venta-carrito-toast__icon"><i class="fa-solid fa-check"></i></span>
        <span>
          <span id="ventaCarritoToastTitle" class="venta-carrito-toast__title">Producto agregado</span>
          <span id="ventaCarritoToastMeta" class="venta-carrito-toast__meta">Cantidad: 1</span>
        </span>
      </div>
    </div> <!-- fin primera columna -->

    <!-- fondo oscuro para version movil cuando abre el drawe lateral del carrito -->
    <div id="overlayCarrito" class="hidden fixed inset-0 bg-black/50 z-30 tlg:hidden"></div>

    <div id="contenedorDesktop" class="p-4 tlg:p-0 fixed top-3 right-0 bottom-3 w-11/12 sm:max-w-3xl bg-white z-40 rounded-2xl shadow-2xl translate-x-full transition-transform duration-300 overflow-y-auto tlg:translate-x-0 tlg:sticky tlg:top-2 tlg:w-auto tlg:max-w-none tlg:rounded-none tlg:shadow-none tlg:overflow-visible tlg:basis-1/3 tlg:min-w-0">
      <div class="flex justify-between items-center tlg:hidden">
        <h4 id="modalCarritoMovil" class="font-semibold text-gray-700 mb-4">Lista de productos</h4>
        <button id="btnCerrarCarritoMovil" class="btn-md btn-indigo"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div id="contenidocarrito" class="space-y-4">
          <div class="formulario__campo">
              <div class="rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 p-4 shadow-sm">
                  <div class="flex items-center gap-2 mb-3">
                      <span class="material-symbols-outlined text-sky-600 text-3xl">badge</span>
                      <div>
                          <h5 class="text-2xl font-bold text-slate-900 m-0">Datos de venta</h5>
                           <p class="text-slate-500 text-lg leading-6 m-0">Vendedor, orden y domicilio.</p>
                      </div>
                  </div>
                  <label class="formulario__label !mb-1 !text-[1.25rem] !font-bold !tracking-normal !text-slate-700" for="vendedor">Vendedor</label>
                  <div class="ventas-datos-field !mb-2">
                    <span class="material-symbols-outlined text-xl">person</span>
                    <select
                      id="vendedor"
                      class="ventas-datos-control"
                      <?php  if($user['perfil']>3)echo 'disabled';?>
                    >
                      <?php foreach($usuarios as $value): ?>
                        <option
                          value="<?php echo $value->id;?>"
                          data-comision="<?php echo $value->porcentajeganancia??0; ?>"
                          <?php if($value->id === $user['id'])echo 'selected'; ?>
                        ><?php echo $value->nombre.' '.$value->apellido;?></option>
                      <?php endforeach;  ?>
                    </select>

                  </div>

                <div class="grid grid-cols-5 gap-3">
                  <div class="formulario__campo col-span-2 !mb-0">
                       <label class="formulario__label !mb-1 !text-[1.25rem] !font-bold !tracking-normal !text-slate-700" for="npedido">Orden</label>
                       <div class="ventas-datos-field">
                         <span class="material-symbols-outlined text-xl">tag</span>
                         <input id="npedido" class="ventas-datos-control" type="number" placeholder="N. orden" name="pedido" value="<?php echo $num_orden;?>" readonly>
                       </div>
                   </div>
                   <div class="formulario__campo col-span-3 !mb-0">
                    <label class="formulario__label !mb-1 !text-[1.25rem] !font-bold !tracking-normal !text-slate-700" for="valorDomicilio">Domicilio</label>
                    <div class="ventas-datos-field">
                      <span class="material-symbols-outlined text-xl">local_shipping</span>
                      <input
                        id="valorDomicilio"
                        class="ventas-datos-control"
                        type="text"
                        placeholder="Valor del domicilio"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex flex-col items-stretch justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3">
                <div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-indigo-600 text-3xl">shopping_cart</span>
                        <div>
                             <h4 class="text-3xl font-bold text-slate-900 m-0">Carrito</h4>
                            <div class="mt-2 inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 shadow-sm" aria-label="Tipo de entrega">
                                <button
                                    id="btnPresencial"
                                    type="button"
                                    class="tipo-entrega-btn inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-lg bg-white text-indigo-700 font-bold shadow-sm transition">
                                    <span class="material-symbols-outlined text-xl">storefront</span>
                                    Presencial
                                </button>

                                <button
                                    id="btnEntrega"
                                    type="button"
                                    class="tipo-entrega-btn inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-lg text-slate-600 font-semibold transition">
                                    <span class="material-symbols-outlined text-xl">local_shipping</span>
                                    Domicilio
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="text-slate-500 text-lg font-bold uppercase tracking-wide">Productos</span>
                    <span
                        id="totalunidades"
                        class="flex shrink-0 items-center justify-center min-w-9 h-9 whitespace-nowrap px-2 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200 text-2xl font-bold"
                    >
                        0
                    </span>
                </div>
            </div>

        <!-- Apilamiento de productos -->
        <div class="mt-3 rounded-lg border border-slate-200 bg-white min-w-0 overflow-x-auto [scrollbar-color:rgba(79,70,229,.5)_rgba(238,242,255,.85)] [scrollbar-width:thin]">
          <table id="tablaventa" class="w-full min-w-max border-separate border-spacing-0" width="100%">
              <thead class="bg-slate-50">
                  <tr class="rounded-t-xl overflow-hidden">
                       <th class="py-3 text-left pl-4 text-slate-700 font-semibold text-xl">Producto</th>
                       <th class="py-3 w-24 text-slate-700 font-semibold text-xl">Cant</th>
                       <th class="py-3 w-24 text-slate-700 font-semibold text-xl">Und</th>
                       <th class="py-3 w-28 text-slate-700 font-semibold text-xl">Total</th>
                       <th class="accionesth !py-3 w-12">
                          <span class="material-symbols-outlined text-red-500 text-xl">delete</span>
                      </th>
                  </tr>
              </thead>
              <tbody>
                  <!-- productos seleccionados a vender
                  <td class="!px-0 !py-2 text-xl text-gray-500 leading-5">lupe lulu</td> 
                  <td class="!px-0 !py-2"><div class="flex"><button><span class="menos material-symbols-outlined">remove</span></button><input type="text" class=" w-20 px-2 text-center" value="11" oninput="this.value = parseInt(this.value.replace(/[,.]/g, '')||1)"><button><span class="mas material-symbols-outlined">add</span></button></div></td>
                  <td class="!p-2 text-xl text-gray-500 leading-5">56000</td>
                  <td class="!p-2 text-xl text-gray-500 leading-5">56880</td>
                  <td class="accionestd"><div class="acciones-btns"><button class="btn-md btn-red eliminarEmpleado"><i class="fa-solid fa-trash-can"></i></button></div></td>-->
              </tbody>
          </table>
          <div id="carritoVacio" class="flex flex-col items-center justify-center gap-2 px-4 py-8 text-center text-slate-500 text-xl">
            <span class="material-symbols-outlined text-5xl text-slate-300">shopping_cart</span>
            <span>Sin productos en el carrito.</span>
          </div>
        </div> <!-- FIn Apilamiento de productos -->
        <div class="rounded-lg border border-slate-200 bg-white p-4 mt-3">
            <!-- Encabezado -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-2xl">receipt_long</span>
                     <h4 class="text-3xl font-bold text-slate-900 m-0">Resumen</h4>
                </div>

                <button
                    id="btndescuento"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-50 border border-amber-300 text-amber-700text-xl font-bold hover:bg-amber-100 transition-all"
                >
                    <span class="material-symbols-outlined text-base">sell</span>
                    Descuento
                </button>
            </div>

            <!-- Totales -->
            <div class="flex justify-between items-start pt-4">
                <div class="space-y-2 text-slate-600">
                     <p class="text-2xl font-normal">Sub Total:</p>
                     <p class="text-2xl font-normal">Impuesto:</p>
                     <p class="text-2xl font-normal">Descuento:</p>
                     <p class="text-2xl font-normal">Tarifa Env&iacute;o:</p>
                    <div class="pt-3 mt-3 border-t border-slate-300">
                        <p class="uppercase tracking-[0.2em] text-lg font-bold text-slate-500">
                            Total
                        </p>
                    </div>

                </div>

                <div class="space-y-2 text-right">
                    <p id="subTotal" class="text-2xl font-semibold text-slate-800">$0</p>
                    <p id="impuesto" class="text-2xl font-semibold text-slate-800">$0</p>
                    <p id="descuento" class="text-2xl font-semibold text-slate-800">$0</p>
                    <p id="valorTarifa" class="text-2xl font-semibold text-slate-800">$0</p>
                    <hr class="my-2 border-slate-300">
                    <p
                        id="total"
                        class="mt-3 !mb-3 text-right text-5xl font-extrabold text-emerald-600 leading-none"
                        style="font-family:'Tektur', serif;"
                    >
                        $0
                    </p>
                    
                </div>
            </div>
            <?php if($conflocal['mostrar_tasa_de_cambio_de_divisa']->valor_final == 1):  ?>
              <div class="mt-2 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-2">
                  <p class="!m-0 text-lg font-semibold text-slate-600">Equivalente</p>
                  <div class="ml-auto flex flex-wrap items-baseline justify-end gap-2">
                      <p id="equivalente" class="!m-0 text-xl font-semibold text-slate-800">$0</p>
                      <p id="monedaCodigo" class="!m-0 text-base font-semibold text-slate-600"></p>
                  </div>
              </div>
            <?php endif;  ?>
        </div>
        <div class="grid grid-cols-2 gap-x-2 gap-y-4 p-0">
          
          <button id="btnguardar" class="btn-md btn-turquoise !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 !px-4 !py-0">
            <span class="material-symbols-outlined text-2xl !leading-none">save</span>
            <span class="!text-xl !font-semibold !normal-case !leading-none">Orden</span>
          </button>
          
          <button id="btnfacturar" class="btn-md btn-indigo !mt-0 !mb-0 !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 !py-0 px-4">
            <span class="material-symbols-outlined text-2xl !leading-none">receipt_long</span>
            <span class="!text-xl !font-semibold !normal-case !leading-none">Facturar</span>
          </button>
          
          <button id="btnaplicarcredito" class="<?php echo $conflocal['valor_por_punto']->valor_final ? '':'col-span-2';  ?> mx-auto !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 rounded-md border border-gray-300 !px-6 !py-0 text-gray-800 shadow-sm hover:bg-gray-100 focus:ring-2 focus:ring-indigo-400">
            <span class="material-symbols-outlined text-2xl !leading-none">payments</span>
            <span class="!text-xl !font-semibold !normal-case !leading-none">Cr&eacute;dito</span>
          </button>

          <?php if($conflocal['valor_por_punto']->valor_final):  ?>
            <button id="btnredimir" class=" mx-auto !inline-flex !h-14 !min-h-[3.5rem] !w-full items-center justify-center gap-2 rounded-md border border-gray-300 !px-6 !py-0 text-gray-800 shadow-sm hover:bg-gray-100 focus:ring-2 focus:ring-indigo-400">
              <span class="material-symbols-outlined text-2xl text-indigo-600">featured_seasonal_and_gifts</span>
              <span class="!text-xl !font-semibold !normal-case !leading-none">Redimir</span>
            </button>
          <?php endif;  ?>
        </div>
      </div>
    </div> <!-- fin segunda columna o contenedor carrito desktop -->
  </div>

  <!-- MODAL PARA AGREGAR DESCUENTO -->
  <dialog id="miDialogoDescuento" class="midialog-xs w-[92vw] max-w-xl overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/50">
    <div class="px-7 py-6">
      <div class="mb-5 border-b border-slate-200 pb-5 text-center">
        <div class="mx-auto mb-3 grid h-16 w-16 place-items-center rounded-2xl bg-indigo-100 text-indigo-700">
          <i class="fa-solid fa-percent text-3xl"></i>
        </div>

        <h4 class="text-4xl font-bold leading-tight text-slate-900">Aplicar descuento</h4>
        <p class="mx-auto mt-2 max-w-md text-lg leading-relaxed text-slate-500">Aplica un descuento al subtotal del pedido.</p>
      </div>

      <form id="formDescuento" class="text-center">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
          <div class="mb-6">
            <h5 class="mb-3 block text-lg font-semibold text-slate-700">Tipo de descuento</h5>

            <div class="inline-flex max-w-full rounded-xl border-[3px] border-indigo-600 bg-white p-1 select-none">
              <label class="flex cursor-pointer">
                <input type="radio" name="tipodescuento" value="valor" class="peer hidden" checked />
                <span class="rounded-lg px-7 py-3 text-lg font-semibold text-slate-700 transition-all duration-200 peer-checked:bg-indigo-600 peer-checked:text-white">
                  Valor
                </span>
              </label>

              <label class="flex cursor-pointer">
                <input type="radio" name="tipodescuento" value="porcentaje" class="peer hidden" />
                <span class="rounded-lg px-7 py-3 text-lg font-semibold text-slate-700 transition-all duration-200 peer-checked:bg-indigo-600 peer-checked:text-white">
                  Porcentaje
                </span>
              </label>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <label for="inputDescuento" class="mb-2 block text-lg font-semibold text-slate-700">Descuento</label>
              <input
                id="inputDescuento"
                type="number"
                min="0"
                name="descuento"
                data-descuento=""
                class="miles block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                required>
            </div>

            <div>
              <label for="inputDescuentoClave" class="mb-2 block text-lg font-semibold leading-snug text-slate-700">Clave de autorizaci&oacute;n</label>
              <input
                id="inputDescuentoClave"
                type="password"
                name="descuentoclave"
                class="miles block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-100">

              <div id="divmsjalertaClaveDcto"></div>
            </div>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
          <button type="button" class="btn-md btn-turquoise salir w-full !px-6 !py-5 !text-2xl">Salir</button>
          <button id="btnCrearAddDir" type="submit" class="btn-md btn-indigo crearAddDir w-full !px-6 !py-5 !text-2xl">Aplicar</button>
        </div>
      </form>
    </div>
  </dialog>

<!-- MODAL PARA VACIAR EL CARRITO-->
  <dialog id="miDialogoVaciar" class="bg-white rounded-xl shadow-lg p-8 max-w-lg w-full relative z-50">
    <div class="text-center">
      <p class="text-2xl font-semibold text-gray-600 mb-6">¿Desea vaciar el carrito de venta?</p>
      <div class="flex justify-around w-full border-t border-gray-300 pt-6">
        <div class="sivaciar flex items-center cursor-pointer transition-transform hover:scale-110 text-blue-500 font-semibold">
          <i class="fa-regular fa-pen-to-square"></i>
          <p class="ml-2">Si­</p>
        </div>
        <div class="novaciar flex items-center cursor-pointer transition-transform hover:scale-110 text-red-500 font-semibold">
          <i class="fa-regular fa-trash-can"></i>
          <p class="ml-2">No</p>
        </div>
      </div>
    </div>
  </dialog>


  <!-- MODAL PARA CALCULADORA-->
  <dialog id="miDialogoCalculadora" class="rounded-2xl border border-gray-200 w-[95%] max-w-xl md:max-w-2xl p-0 bg-white backdrop:bg-black/40 shadow-2xl overflow-hidden transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out">
    <form id="formMerma" class="p-0 text-center">
      <div class="px-7 py-6 sm:px-8">
        <div class="mb-5 border-b border-slate-200 pb-5">
          <div class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-indigo-100 text-indigo-700">
            <span class="material-symbols-outlined text-4xl">inventory_2</span>
          </div>
          <h4 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Registrar merma</h4>
          <p class="mx-auto mt-2 max-w-md text-lg leading-relaxed text-slate-500">
            Ingresa la cantidad que se descontara del inventario de este producto.
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
          <label for="inputMerma" class="mb-3 block text-center text-sm font-extrabold uppercase tracking-[.16em] text-slate-500">
            Cantidad a descontar
          </label>
          <input
            id="inputMerma"
            type="number"
            min="0"
            step="0.01"
            inputmode="decimal"
            name="merma"
            class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-center text-2xl font-semibold text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
          >
          <p class="mx-auto mt-4 max-w-md text-center text-base leading-relaxed text-slate-500">
            La cantidad ingresada se restara de la cantidad actual del producto en el carrito. El sistema recalculara el total automaticamente.
          </p>
        </div>

        <div class="mt-6 flex justify-center gap-4 border-t border-gray-200 pt-5">
          <button type="button" class="btn-md btn-turquoise !py-4 !px-6 !w-[140px] md:!w-[160px] salir">Cancelar</button>
          <button id="btnMermaCantidad" type="button" class="btn-md btn-indigo !py-4 !px-6 !w-[140px] md:!w-[160px]">Aplicar</button>
        </div>
      </div>
    </form>
  </dialog>

  <!-- MODAL PARA CREAR CLIENTE-->
  <?php include __DIR__. "/modalCreateAddCli.php"; ?>
  <!-- MODAL PARA GUARDAR EL PEDIDO-->
  <?php include __DIR__. "/modalguardarpedido.php"; ?>
  <!--///////////////////// Modal procesar el pago boton facturar /////////////////////////-->
  <?php include __DIR__. "/modalprocesarpago.php"; ?>
  <!--///////////////////// Modal procesar credito boton facturar /////////////////////////-->
  <?php //include __DIR__. "/modalprocesarcredito.php"; ?>
  <!--///////////////////// Modal procesar puntos a redimir /////////////////////////-->
  <?php include __DIR__. "/modalRedimir.php"; ?>
  <!-- MODAL DATOS DEL ADQUIRIENTE -->
  <?php include __DIR__. "/modaladquiriente.php"; ?>
  <!-- MODAL OTROS PRODUCTOS -->
  <?php include __DIR__. "/modalotrosproductos.php"; ?>
  <!-- MODAL PRECIOS ADICIONALES -->
  <?php include __DIR__. "/modalpreciosadicionales.php"; ?>

  <script>
    const mediosPagoDB = <?= json_encode($mediospago) ?>;  //se inyecta el array de medios de pago desde PHP a JavaScript y se utiliza en ventas.ts
    const clientesDB = <?= json_encode($clientes) ?>;
    const getParam = <?= json_encode($conflocal) ?>;
    const percentComisionUser = <?= json_encode($user['porcentajeganancia']); ?> //porcentaje de comision del usuario logueado
    const sucursal = <?= json_encode(negocionSucursal()) ?>;
  </script>

</div>
