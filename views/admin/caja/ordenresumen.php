<div class="ordenresumen ordenresumen-page">
    <button onclick="history.back()" class="ordenresumen-back" title="Volver">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atr&aacute;s</span>
    </button>
    <div class="ordenresumen-actions">
        <?php if($factura->cambioaventa == 0 && ($factura->estado=='Guardado' || $factura->estado=='Remision')):?>
            <button id="btnfacturar" title="Procesar pago de cotizaciones y remisiones. Solo para las cotizaciones se descuenta de inventario" class="btn-command"><span class="material-symbols-outlined">attach_money</span>Procesar pago</button>
        <?php endif; ?>
        <?php if($factura->estado=='Paga' || $factura->estado=='Remision'):?>
            <button id="btneliminarorden" class="btn-command"><span class="material-symbols-outlined">delete</span>Eliminar orden</button>
        <?php endif; ?>
        <?php if($factura->estado=='Paga' || $factura->estado=='Eliminada'):?>
            <button id="printcarta" class="btn-command"><span class="material-symbols-outlined">print</span>Imprimir factura</button>
        <?php endif; ?>
        <?php if($factura->estado=='Guardado'):?>
            <button id="printcotizacion" class="btn-command"><span class="material-symbols-outlined">print</span>Imprimir cotizaci&oacute;n</button>
        <?php endif; ?>
        <?php if($factura->tipoventa=='Credito'):?>
            <a class="btn-command" href="/admin/creditos/detallecredito?id=<?php echo $factura->ref_creditoid;?>"><span class="material-symbols-outlined">format_list_bulleted</span>Detalle cr&eacute;dito</a>
        <?php endif; ?>
        <?php if($factura->estado=='Paga'):?>
            <button id="enviarEmail" class="btn-command"><span class="material-symbols-outlined">mail</span>Enviar factura</button>
        <?php endif; ?>
        <?php if($factura->cambioaventa == 0 && ($factura->estado=='Guardado' || $factura->estado == 'Remision')):?>
            <a id="abrirOrden" class="btn-command" href="/admin/ventas?id=<?php echo $factura->id;?>"><span class="material-symbols-outlined">app_registration</span>Abrir</a>
        <?php endif; ?>
        <?php if($factura->estado=='Paga'):?>
            <a class="btn-command" href="/admin/reportes/detalleInvoice?id=<?php echo $factura->id;?>"><span class="material-symbols-outlined">article_shortcut</span>Factura electr&oacute;nica</a>
        <?php endif; ?>
        <?php if($factura->entregado == 0 && ($factura->estado=='Paga'&&$factura->entrega == 'Domicilio' || $factura->estado == 'Remision')):?>
            <button id="btnDespachar" title="Solo para facturas pendientes de despachar y remisiones" class="btn-command"><span class="material-symbols-outlined">delivery_truck_speed</span>Marcar despachado</button>
        <?php endif; ?>
        <button id="btnMasOpciones" class="btn-command"><span class="material-symbols-outlined">apps</span>M&aacute;s</button>
    </div>
    
    
    <div class="mb-4 flex flex-wrap gap-[.8rem]">
        <span id="numOrden" class="inline-flex rounded-full bg-slate-100 px-[1.2rem] py-[.85rem] text-lg font-semibold leading-none text-slate-700">
            Orden #<?php echo $factura->num_orden??'';?>
        </span>
        <span id="referenciaFactura" class="inline-flex rounded-full bg-indigo-50 px-[1.2rem] py-[.85rem] text-lg font-semibold leading-none text-indigo-700">
            Referencia: Orden-<?php echo $factura->referencia??'';?>
        </span>
        <span id="textEstado" class="inline-flex rounded-full bg-orange-50 px-[1.2rem] py-[.85rem] text-lg font-semibold leading-none text-orange-700">
            <?php echo (($factura->entrega=='Domicilio'||$factura->entrega=='Presencial') && $factura->entregado==0)
                ? 'Pendiente de despacho'
                : ($factura->entrega=='Presencial' && $factura->entregado==1? 'Presencial entregado':'Domicilio/Presencial entregado'); 
            ?>
        </span>
    </div>

    <div class="mb-6 flex flex-wrap items-center gap-[.8rem] border-b border-slate-200 pb-4 text-[1.45rem] text-slate-600">
        <button id="btnEmisor" class="btn-xs btn-light">Emisor</button>
        <span id="nitEmisor">NIT: <?php echo $factura->nit??' - '; ?></span>, 
        <span id="nombreEmisor"><?= $factura->nombrecompania??' - '; ?></span>
    </div>

    <div class="ordenresumen-metrics">
        <div class="border border-slate-200 rounded-xl px-4 py-3">
            <p class="text-slate-500 font-bold text-lg flex items-center gap-4 mt-0 mb-3 uppercase">
                <span class="material-symbols-outlined text-indigo-600 text-3xl">
                    calendar_month
                </span>
                Fecha Orden
            </p>
            <p class="text-slate-900 text-xl font-semibold"><?php echo $factura->fechacreacion??'';?></p>
        </div>
        <div class="border border-slate-200 rounded-xl px-4 py-3">
            <p class="text-slate-500 font-bold text-lg flex items-center gap-4 mt-0 mb-3 uppercase">
                <span class="material-symbols-outlined text-indigo-600 text-3xl">
                    payments
                </span>
                Fecha Pago
            </p>
            <p class="text-slate-900 text-xl font-semibold"><?php echo $factura->fechapago??'';?></p>
        </div>
        <div class="border border-slate-200 rounded-xl px-4 py-3">
            <p class="text-slate-500 font-bold text-lg flex items-center gap-4 mt-0 mb-3 uppercase">
                <span class="material-symbols-outlined text-indigo-600 text-3xl">
                    badge
                </span>
                Vendedor
            </p>
            <button id="btnSelectVendedor" class="btn-xs btn-light"><?php echo $factura->vendedor??'';?></button>
        </div>
        <div class="border border-slate-200 rounded-xl px-4 py-3">
            <p class="text-slate-500 font-bold text-lg flex items-center gap-4 mt-0 mb-3 uppercase">
                <span class="material-symbols-outlined text-indigo-600 text-3xl">inventory_2</span>
                Estado Orden
            </p>
            <p id="estadoOrden" class="m-0 text-2xl font-bold text-teal-700"><?php echo (($factura->tipoventa =='Contado'|| $factura->tipoventa =='')&&$factura->remision==0)?$factura->estado:($factura->remision==1 && ($factura->estado == 'Paga' || $factura->estado == 'Aceptada')?'Remision - '.$factura->estado:($factura->remision==1&&$factura->estado=='Remision'?$factura->estado:"Credito - F. $factura->estado"));?></p>
            <p class="m-0 text-gray-600 text-lg font-medium"> - Factura: <?php echo ($factura->prefijo??'') . $factura->num_consecutivo;?></p>
        </div>
    </div>

    <div id="idorden" class="hidden" data-idorden="<?php echo $factura->id;?>"></div>

    <div class="ordenresumen-main-grid">
        <div class="ordenresumen-products-card">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="text-slate-900 text-3xl font-bold flex items-center gap-4">
                    <span class="material-symbols-outlined text-indigo-600 text-4xl">inventory_2</span>
                    Productos de la orden
                </h3>

                <span class="whitespace-nowrap rounded-full bg-indigo-50 px-[.9rem] py-[.55rem] text-[1.2rem] font-bold text-indigo-700">
                    <?php echo count($productos); ?> productos
                </span>
            </div>
            <table class="ordenresumen-table">
                <thead>
                    <tr>
                        <th scope="col"> Nombre producto</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Unidad</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody id="tablaDetalleProductos">
                    <?php foreach($productos as $index=>$value): ?>
                        <tr>
                            <td scope="row" class="ordenresumen-product-cell">
                                <div class="ordenresumen-product-info">
                                    <img 
                                        class="ordenresumen-product-img" 
                                        src="/build/img/<?php echo $value->foto;?>"
                                        onerror="this.onerror=null;this.src='/build/img/default-product.png';"
                                        alt="J2SoftwarePOS" 
                                    />
                                    <?php if($value->tipoproducto == 0): ?> 
                                        <span><?php echo $value->nombreproducto ?? ''; ?></span>
                                    <?php else: ?>
                                        <button id="<?php echo $value->idproducto;?>" class="productoCompuesto btn-xs btn-light"><?php echo $value->nombreproducto ?? '';?></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo $value->cantidad??'';?></td>
                            <td>$<?php echo number_format($value->valorunidad??0, "2", ",", ".");?></td>
                            <td>$<?php echo number_format($value->total??0, "2", ",", ".");?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row">Total</th>
                        <td><?php echo $factura->totalunidades;?></td>
                        <td> - </td>
                        <td>$<?php echo number_format($factura->subtotal??0, "2", ",", ".");?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    
    
        <aside class="ordenresumen-side">
            <div class="border border-slate-200 rounded-xl p-4">
                <p class="flex items-center text-slate-900 font-semibold gap-4 text-2xl mt-0">
                    <span class="material-symbols-outlined text-indigo-600">person</span>
                    Cliente
                </p>
                <p class="flex items-center gap-4 text-slate-500 mb-0 mt-1 text-lg"><span class="material-symbols-outlined text-3xl">person</span><?php echo $factura->cliente??'';?></p>
                <p class="flex items-center gap-4 text-slate-500 mb-0 mt-1 text-lg"><span class="material-symbols-outlined text-3xl">person</span><?php echo $cliente->identificacion??'';?></p>
                <p class="flex items-center gap-4 text-slate-500 mb-0 mt-1 text-lg"><span class="material-symbols-outlined text-3xl">mail</span><?php echo $cliente->email??'';?></p>
                <p class="flex items-center gap-4 text-slate-500 mb-0 mt-1 text-lg"><span class="material-symbols-outlined text-3xl">phone_in_talk</span><?php echo $cliente->telefono??'';?></p>
            </div>
            <div class="border border-slate-200 rounded-xl p-4">
                <p class="flex items-center text-slate-900 font-semibold gap-4 text-2xl mt-0">
                    <span class="material-symbols-outlined text-indigo-600">local_shipping</span>
                    Direcci&oacute;n de entrega
                </p>
                <p class="text-slate-500 mb-0 text-lg mt-1">Tipo entrega: <?php echo $factura->entrega??'';?></p>
                <p class="text-slate-500 mb-0 text-lg mt-1"><?php echo $direccion->ciudad.'-'.$direccion->direccion??'';?></p>
                <p class="text-slate-500 mb-0 text-lg mt-1">Tarifa env&iacute;o: $<?php echo number_format($factura->valortarifa??'0', 0, ',', '.');?></p>
            </div>
            <div class="border border-slate-200 rounded-xl p-4">
                <p class="flex items-center text-slate-900 font-semibold gap-4 text-2xl mt-0">
                    <span class="material-symbols-outlined text-indigo-600">receipt_long</span>
                    Medios de pago
                </p>
                <?php foreach($mediosPago as $medio): ?>
                    <p class="text-slate-500 mb-0 text-xl mt-2"> - <?php echo $medio['mediopago']??'';?>: $<?php echo number_format($medio['valor']??'0', 2, ',', '.');?></p>
                <?php endforeach; ?>
            </div>
        </aside>     
    </div>

    <div class="border border-slate-200 rounded-xl p-4 mt-6">
        <div class="grid lg:grid-cols-3 gap-4">
            <!-- OBSERVACIONES -->
            <section class="border border-slate-200 bg-slate-50 p-4 rounded-xl lg:col-span-2">
                <p class="font-semibold text-slate-800 text-xl mb-4 mt-0">Observaciones</p>
                <?php if(empty(trim($factura->observacion ?? ''))): ?>
                    <div class="flex items-center gap-2 mt-6 text-slate-500 italic">
                        <span class="material-symbols-outlined text-xl">info</span>
                        <span>Sin observaciones registradas.</span>
                    </div>
                <?php else: ?>
                    <p class="text-slate-600 text-lg mb-4"><?php echo $factura->observacion;?></p>
                <?php endif; ?>

                <?php if(!empty($factura->observacioneliminacion)): ?>
                    <div class="border-t border-slate-200 pt-4 mt-4">
                        <p class="font-medium text-red-600 mb-2">Observaci&oacute;n de eliminaci&oacute;n</p>
                        <p class="text-slate-600 text-lg"><?php echo $factura->observacioneliminacion;?></p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- RESUMEN DE PAGO -->
            <section class="border border-slate-200 bg-slate-50 p-4 rounded-xl lg:col-span-1">
                <p class="font-semibold text-slate-800 text-xl mb-5 mt-0">Resumen de pago</p>
                <div class="flex justify-between">
                    <div class="text-start">
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">Sub Total:</p>
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">Abono:</p>
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">Impuesto:</p>
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">Descuento:</p>
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">Tarifa Env&iacute;o:</p>
                    </div>

                    <div class="text-end">
                        <p id="subTotal" class="m-0 mb-2 text-slate-600 text-xl font-normal">
                            $<?php echo number_format($factura->subtotal ?? 0, 2, ',', '.');?>
                        </p>
                        <p class="m-0 mb-2 text-slate-600 text-xl font-normal">
                            $<?php echo number_format($factura->abono ?? 0, 2, ',', '.');?>
                        </p>
                        <p id="impuesto" class="m-0 mb-2 text-slate-600 text-xl font-normal">
                            $<?php echo number_format($factura->valorimpuestototal ?? 0, 2, ',', '.');?>
                        </p>
                        <p id="descuento" class="m-0 mb-2 text-slate-600 text-xl font-normal">
                            <?php echo $factura->dctox100;?>%
                            $<?php echo number_format($factura->descuento ?? 0, 2, ',', '.');?>
                        </p>
                        <p id="valorTarifa" class="m-0 mb-2 text-slate-600 text-xl font-normal">
                            $<?php echo number_format($factura->valortarifa ?? 0, 2, ',', '.');?>
                        </p>
                    </div>
                </div>

                <!-- TOTAL DESTACADO -->
                <div class="mt-6 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-700 text-2xl font-semibold">Total:</span>
                        <span id="total"
                            class="whitespace-nowrap text-6xl font-bold text-emerald-600"
                            style="font-family:'Tektur', serif;">
                            $ <?php echo number_format($factura->total ?? 0, 2, ',', '.');?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="miDialogoFacturar" class="midialog-md !p-12">
      <h4 class="text-3xl font-semibold m-0 text-neutral-800">Registro de pago</h4>
      <hr class="my-4 border-t border-neutral-300">
      <form id="formfacturarCotizacion" class="formulario" method="POST">
          <input id="idcita" name="id" type="hidden">
          <p class="text-gray-600 text-3xl text-center font-light m-0">Total a pagar $: </br><span id="totalPagar" class="text-gray-700 font-semibold">0</span></p>
          <div class="flex justify-center gap-12 mt-8">
            <div class="formulario__campo w-1/2">
              <label class="formulario__label" for="caja">Caja</label>
              <select id="caja" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="caja" required>
                  <?php foreach($cajas as $index => $value):?>
                    <option value="<?php echo $value->id;?>" data-idfacturador="<?php echo $value->idtipoconsecutivo;?>" data-idemisor="<?php echo $value->idemisor;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div class="formulario__campo w-1/2">
              <label class="formulario__label" for="facturador">Facturador</label>
              <select id="facturador" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block p-2.5 h-14 text-xl focus:outline-none focus:ring-1" name="facturador" required>
                <?php foreach($consecutivos as $index => $value):?>
                  <option data-idtipofacturador="<?php echo $value->idtipofacturador;?>" value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>
          <div class="accordion md:px-12 !mt-4">
            <input type="checkbox" id="first">
            <label class="etiqueta text-indigo-700" for="first">Elegir metodo de pago</label>
            <div class="wrapper">
              <div class="wrapper-content">
                <div id="mediospagos" class="content flex flex-col items-center w-1/2 mx-auto text-center">
                  <?php foreach($mediospago as $index => $value):?>
                    <div class="mb-4 text-center">
                      <label class="text-gray-700 text-xl text-center leading-relaxed"><?php echo $value->mediopago??'';?>: </label>
                      <input 
                        id="<?php echo $value->id??'';?>" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block p-2.5 h-14 text-xl focus:outline-none focus:ring-1 text-center mediopago <?php echo $value->mediopago??'';?>" 
                        type="text" 
                        value="0" 
                        <?php echo $value->mediopago=='Efectivo'?'readonly':'';?> 
                        oninput="formatearMoneda(this)"
                    >
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div> <!-- fin accordion  -->

          <div class="mx-auto">
            <div class="formulario__campo w-80 mx-auto">
                <label class="formulario__label leading-relaxed text-center" for="recibio">Efectivo Recibido</label>
                <input id="recibio" class="formulario__input !text-2xl !border-0 !border-b-2 !border-indigo-500 !rounded-none text-center" name="" type="text" placeholder="0" oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()">
            </div>
            <div class="flex flex-col items-center">
                <p id="cambio" class="text-center formulario__label">Devolver: <span class="text-gray-700 font-semibold text-2xl">$0</span></p>
            </div>
          </div>

        <!-- Opci&oacute;n imprimir factura -->
        <div class="formulario__campo md:px-12 mb-6">
          <label class="formulario__label block text-center mb-2">&iquest;Desea imprimir factura?</label>
          <div class="flex justify-center gap-8">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="imprimir" value="1" class="w-5 h-5" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?>>
              <span class="text-gray-700 text-lg">S&iacute;</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="imprimir" value="0" class="w-5 h-5" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 0?'checked':'';?>>
              <span class="text-gray-700 text-lg">No</span>
            </label>
          </div>
        </div>
        <!-- Fin opci&oacute;n imprimir factura -->

          <div class="formulario__campo md:px-12">
              <textarea id="observacion" class="formulario__textarea" name="observacion" placeholder="Observaci&oacute;n" rows="4"></textarea>
          </div>

          <div class="self-end">
              <button class="btn-md btn-turquoise !py-4 !px-6 !w-[145px]" type="button" value="Cancelar">Cancelar</button>
              <input class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[145px]" type="submit" value="Pagar">
          </div> 
      </form>
    </dialog>


     <!-- MODAL DE MAS OPCIONES -->
    <dialog id="miDialogoMasOpciones" class="w-[95%] max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-black/40">
        <div class="flex items-start justify-between gap-6 border-b border-slate-200 px-10 pb-7 pt-8 max-sm:gap-4 max-sm:px-7 max-sm:pb-6 max-sm:pt-6">
            <div class="flex items-start gap-5 max-sm:gap-4">
                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-sm ring-1 ring-indigo-100 max-sm:h-14 max-sm:w-14">
                    <span class="material-symbols-outlined text-5xl max-sm:text-4xl">apps</span>
                </span>

                <div>
                    <p class="m-0 text-base font-semibold uppercase tracking-[.22em] text-indigo-600 max-sm:text-sm">
                        Orden
                    </p>
                    <h4 id="modalMasOpciones" class="m-0 mt-1 text-4xl font-bold leading-tight text-slate-900 max-sm:text-3xl">
                        M&aacute;s opciones
                    </h4>
                    <p class="m-0 mt-2 text-lg font-medium leading-7 text-slate-500 max-sm:text-sm max-sm:leading-5">
                        Acciones adicionales disponibles para esta orden.
                    </p>
                </div>
            </div>

            <button class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 max-sm:h-11 max-sm:w-11">
                <i id="btnXCerrarMasOpciones" class="fa-solid fa-xmark text-3xl max-sm:text-2xl"></i>
            </button>
        </div>

        <div class="flex flex-col gap-5 px-10 py-8 max-sm:gap-4 max-sm:px-7 max-sm:py-6">
            <button id="btnImprimirTirilla" class="group flex items-center justify-between gap-5 rounded-2xl border border-slate-200 bg-slate-50/70 px-6 py-5 text-left transition hover:border-indigo-200 hover:bg-indigo-50 hover:shadow-lg hover:shadow-indigo-600/10  max-sm:gap-4 max-sm:px-5 max-sm:py-4">
                <span class="flex min-w-0 items-center gap-5 max-sm:gap-4">
                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100 max-sm:h-14 max-sm:w-14">
                        <span class="material-symbols-outlined text-5xl max-sm:text-4xl">receipt_long</span>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-2xl font-semibold leading-tight text-slate-900 max-sm:text-xl">
                            Imprimir factura tirilla
                        </span>
                        <span class="mt-1 block text-base font-medium leading-6 text-slate-500 max-sm:text-sm max-sm:leading-5">
                            Genera el comprobante POS de esta orden.
                        </span>
                    </span>
                </span>
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-white text-slate-400 shadow-sm transition group-hover:text-indigo-600 max-sm:h-10 max-sm:w-10">
                    <i class="fa-solid fa-chevron-right text-xl max-sm:text-base"></i>
                </span>
            </button>

            <?php if($factura->estado=='Paga' || $factura->remision == 1):?>
            <button id="btnOrdenEnvio" class="group flex items-center justify-between gap-5 rounded-2xl border border-slate-200 bg-slate-50/70 px-6 py-5 text-left transition hover:border-teal-200 hover:bg-teal-50 hover:shadow-lg hover:shadow-teal-600/10  max-sm:gap-4 max-sm:px-5 max-sm:py-4">
                <span class="flex min-w-0 items-center gap-5 max-sm:gap-4">
                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-xl bg-white text-teal-600 shadow-sm ring-1 ring-teal-100 max-sm:h-14 max-sm:w-14">
                        <span class="material-symbols-outlined text-5xl max-sm:text-4xl">local_shipping</span>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-2xl font-semibold leading-tight text-slate-900 max-sm:text-xl">
                            Imprimir orden de entrega
                        </span>
                        <span class="mt-1 block text-base font-medium leading-6 text-slate-500 max-sm:text-sm max-sm:leading-5">
                            Abre el formato para preparar el despacho.
                        </span>
                    </span>
                </span>
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-white text-slate-400 shadow-sm transition group-hover:text-teal-600 max-sm:h-10 max-sm:w-10">
                    <i class="fa-solid fa-chevron-right text-xl max-sm:text-base"></i>
                </span>
            </button>
            <?php endif; ?>
        </div>
    </dialog>


    <!-- MODAL PARA ELIMINAR ORDEN-->
    <?php include __DIR__. "/modalEliminarOrden.php"; ?>
    <!-- MODAL REMISION -->
    <?php include __DIR__. "/remision.php"; ?>
    <!-- MODAL REMISION -->
    <?php include __DIR__. "/modalCambiarEmisor.php"; ?>
    <!-- MODAL DETALLE DE PRODUCTO COMPUESTO-->
    <?php include __DIR__. "/modalProductoCompuesto.php"; ?>
    <!-- MODAL PARA CAMBIAR USUARIO Y COMSION DE VENTA -->
    <?php include __DIR__. "/modalCambiarUsuario.php"; ?>
    <!-- MODAL PARA ENVIAR FACTURA POR EMAIL -->
    <?php include __DIR__. "/modalSendInvoiceEmail.php"; ?>
</div>


