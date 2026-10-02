<div class="box caja !pb-20 [@media(max-width:991px)]:!pb-[calc(10rem+env(safe-area-inset-bottom))]">
  <?php include __DIR__. "/../../templates/alertas.php"; ?>
  <div class="mb-5 flex flex-wrap items-end justify-between gap-3 border-b-2 border-indigo-600 pb-2">
    <div>
      <h4 class="m-0 text-3xl font-semibold text-slate-700">Gestion de Caja</h4>
      <p class="m-0 mt-1 text-base text-slate-500">Ordenes, cierres y movimientos de caja.</p>
    </div>
    <span class="inline-flex items-center rounded-full bg-indigo-600 px-5 py-2 text-lg font-bold uppercase tracking-wide text-white shadow-md">
      <?php echo $sucursal; ?>
    </span>
  </div>

  <div class="mb-5 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
    <?php if(tienePermiso('Ver detalle de la caja')&&userPerfil()>3 || userPerfil()<4): ?>
        <a class="caja-action" href="/admin/caja/cerrarcaja"><span class="material-symbols-outlined">hard_drive</span>Cerrar Caja</a>
    <?php endif; ?>
    <button class="caja-action caja-action--primary" id="btnGastosingresos">
        <span class="material-symbols-outlined">paid</span>Gastos / Ingresos</button>
    <?php if($conflocal['permitir_ver_zeta_diario']->valor_final == 1):?>
        <a class="caja-action" href="/admin/caja/zetadiario"><span class="material-symbols-outlined">document_search</span>Zeta Diario</a>
    <?php endif; ?>
    <?php if($conflocal['permitir_ver_cierres_de_cajas_anteriores']->valor_final == 1):?>
        <a class="caja-action" href="/admin/caja/ultimoscierres"><span class="material-symbols-outlined">list_alt</span>Ultimos Cierres</a>
     <?php endif; ?>
    <button id="btnAbrirCajon" class="caja-action"><span class="material-symbols-outlined">lock_open</span>Abrir Cajon</button>
    <a class="caja-action caja-action--primary" href="/admin/caja/pedidosguardados"><span class="material-symbols-outlined">folder_check_2</span>Cotizaciones</a>
    <!--<a class="btn-command text-center" href="/admin/caja/trasladosRetirosDinero"><span class="material-symbols-outlined">assured_workload</span>Traslados y retiros</a>-->
    <a class="caja-action" href="/admin/caja/despachosPendientes"><span class="material-symbols-outlined">delivery_truck_speed</span>Despachos pendientes</a>
    <a class="caja-action" href="/admin/reportes/remisiones"><span class="material-symbols-outlined">format_list_bulleted</span>Remisiones</a>
  </div>
    <h5 class="mb-4 flex items-center gap-3 text-2xl font-semibold text-slate-900">
        Lista de Ordenes
    </h5>

  <div class="overflow-hidden rounded-lg border border-slate-200 bg-white [@media(max-width:991px)]:[&_.dt-paging]:!p-3">
  <table id="tablaListaPedidos" class="display responsive nowrap tabla caja-table" width="100%">
      <thead>
          <tr>
              <th>N.</th>
              <th title="Fecha de pago">Fecha</th>
              <th>Caja</th>
              <th>Entrega</th>
              <th>Orden</th>
              <th>Factura</th>
              <th>Medio Pago</th>
              <th>Estado</th>
              <th>Subtotal</th>
              <th>Total</th>
              <th class="accionesth">Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($facturas as $index => $value): ?>
            <tr class="hover:bg-slate-50"> 
              <td class="font-medium text-slate-600"><?php echo $index+1;?></td>
              <td><div class="w-36 whitespace-normal leading-6 text-slate-700"><?php echo $value->fechapago;?></div></td> 
              <td><div class="w-28 whitespace-normal text-slate-700"><?php echo $value->nombrecaja ?? $value->caja ?? 'Caja principal';?></div></td>
              <td>
                <span class="inline-flex min-w-[8.8rem] justify-center rounded-full px-4 py-2 text-lg font-semibold <?php echo ($value->entrega=='Domicilio' && ($value->estado == 'Paga' || $value->estado == 'Remision') && $value->entregado == 1)?'bg-emerald-50 text-emerald-700 border border-emerald-200':(($value->entrega=='Domicilio'&& ($value->estado == 'Paga' || $value->estado == 'Remision') && $value->entregado == 0)?'bg-amber-50 text-amber-700 border border-amber-200':'bg-slate-100 text-slate-700 border border-slate-200');?>">
                  <?php echo $value->entrega;?>
                </span>
              </td>
              <td class="font-medium text-slate-700"><?php echo $value->num_orden;?></td>
              <td class="text-slate-700"><?php echo $value->prefijo.''.$value->num_consecutivo;?></td>
              <td>
                <div data-estado="<?php echo $value->estado;?>" data-totalpagado="<?php echo $value->total;?>" id="<?php echo $value->id;?>" class="mediosdepago max-w-full flex flex-wrap gap-2">
                    <?php foreach($value->mediosdepago as $idx => $element): ?>
                    <button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-lg font-medium text-slate-700 hover:border-indigo-300 hover:text-indigo-700"><?php echo $element->mediopago;?></button>
                    <?php endforeach; ?>
                </div>
              </td>
              <td>
                <div class="inline-flex min-w-[7.8rem] justify-center rounded-md px-4 py-2 text-lg font-semibold <?php echo $value->estado=='Paga'&&$value->tipoventa=='Contado'?'bg-emerald-500 text-white':($value->estado=='Paga'&& $value->tipoventa=='Credito'?'bg-blue-600 text-white':($value->estado=='Guardado'?'bg-cyan-500 text-white':($value->estado=='Remision' || $value->estado=='Paga'&&$value->remision==1?'bg-indigo-600 text-white':'bg-slate-100 text-slate-700')));?>">
                  <?php echo ($value->tipoventa =='Contado'||$value->tipoventa =='')?$value->estado:(($value->tipoventa =='Credito' && $value->estado == 'Paga')?'Credito':'Credito elim..');?>
                </div>
              </td>
              <td class="text-right font-normal text-slate-700"><span>$ </span><?php echo number_format($value->subtotal??0, "2", ",", ".");?></td>
              <td class="text-right font-semibold text-slate-900"><strong>$ </strong><?php echo number_format($value->total??0, "2", ",", ".");?></td>
              <td class="accionestd"><div class="acciones-btns !gap-2 !py-0" id="<?php echo $value->id;?>" data-cotizacion="<?php echo $value->cotizacion;?>" >
                    <a class="inline-flex h-11 min-w-[5.2rem] items-center justify-center rounded-lg bg-teal-500 px-4 text-base font-semibold text-white shadow-sm hover:bg-teal-600" title="Ver detalles del pedido" href="/admin/caja/ordenresumen?id=<?php echo $value->id;?>">Ver</a>
                    <?php if($value->estado=='Paga'): ?>
                        <button class="inline-grid h-11 w-11 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 shadow-sm hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 printPOS" title="Imprimir en PDF POS"><i class="fa-solid fa-print text-lg"></i></button>
                    <?php endif; ?>
                    <button class="inline-grid h-11 w-11 place-items-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm hover:bg-red-100 printPDF" title="Imprimir en PDF carta"><i class="fa-solid fa-file-pdf text-lg"></i></button>
                  </div>
              </td>
            </tr>
          <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="font-semibold text-gray-900">
            <td colspan="7" class="px-6 py-3 text-right">Total Dia:</td>
            <td colspan="4" class="px-6 py-3 text-left">$<?php echo number_format($datacierrescajas??0, "2", ",", ".");?></td>
        </tr>
      </tfoot>
  </table>
  </div>

  <style>
    .caja-action {
      align-items: center;
      background: #fff;
      border: 1px solid #cbd5e1;
      border-radius: .65rem;
      color: #334155;
      display: inline-flex;
      flex-direction: column;
      font-size: 1.35rem;
      font-weight: 500;
      gap: .35rem;
      justify-content: center;
      min-height: 7.6rem;
      min-width: 11rem;
      padding: .8rem 1rem;
      text-align: center;
      transition: background-color .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
    }
    .caja-action:hover {
      background: #f8fafc;
      border-color: #6366f1;
      color: #4338ca;
      transform: translateY(-1px);
    }
    .caja-action .material-symbols-outlined {
      font-size: 2.8rem;
    }
    .caja-action--primary {
      background: #4338ca;
      border-color: #4338ca;
      color: #fff;
      font-weight: 600;
    }
    .caja-action--primary:hover {
      background: #3730a3;
      border-color: #3730a3;
      color: #fff;
    }
    .caja-table thead th {
      background: #f8fafc;
      color: #334155;
      font-size: 1.35rem;
      font-weight: 700;
      padding: .95rem .75rem;
    }
    .caja-table tbody td {
      color: #334155;
      font-size: 1.35rem;
      padding: .85rem .75rem;
      vertical-align: middle;
    }
    .caja-table tbody td div,
    .caja-table tbody td span,
    .caja-table tbody td button,
    .caja-table tbody td a {
      font-size: 1.2rem !important;
    }
    .caja-table tbody td .acciones-btns a {
      font-size: 1.15rem !important;
    }
    .caja-table tbody td .acciones-btns button i {
      font-size: 1.25rem !important;
    }
    .caja-table tbody td .acciones-btns a,
    .caja-table tbody td .acciones-btns button {
      height: 3.6rem !important;
    }
    @media (max-width: 640px) {
      .caja-action {
        min-height: 7.4rem;
        min-width: 0;
        width: 100%;
      }
      .caja-action .material-symbols-outlined {
        font-size: 2.6rem;
      }
    }
    .caja-table tfoot th,
    .caja-table tfoot td {
      background: #f8fafc;
      font-size: 1.4rem;
    }
  </style>

    <!-- MODAL GASTOS E INGRESOS -->
    <dialog id="gastosIngresos"
        class="rounded-2xl border border-slate-200 w-[94vw] max-w-[54rem] max-h-[92vh] overflow-hidden p-0 bg-white backdrop:bg-slate-900/55 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out">

        <div class="flex max-h-[92vh] flex-col">
            <div class="flex items-start justify-between gap-5 border-b border-slate-200 px-8 py-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                        <span class="material-symbols-outlined text-4xl">paid</span>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-[.28em] text-indigo-600">Movimiento de caja</p>
                        <h4 class="m-0 text-3xl font-bold leading-tight text-slate-900">Gastos e ingresos</h4>
                        <p class="m-0 mt-2 text-lg leading-7 text-slate-500">Registre entradas o salidas de dinero de la caja.</p>
                    </div>
                </div>
                <button id="btnCerrarGastosIngresos"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                    onclick="document.getElementById('gastosIngresos').close()">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="formGastosingresos" class="formulario flex min-h-0 flex-1 flex-col" action="/admin/caja/ingresoGastoCaja" method="POST" enctype="multipart/form-data">
                <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-8 py-7">
                    <div id="divmsjalerta1"></div>

                    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-700">
                                <span class="material-symbols-outlined text-3xl">swap_vert</span>
                            </div>
                            <div>
                                <h5 class="m-0 text-xl font-bold text-slate-900">Tipo de movimiento</h5>
                                <p class="m-0 mt-1 text-base leading-6 text-slate-500">Seleccione si el registro corresponde a un ingreso o a un gasto.</p>
                            </div>
                        </div>

                        <label class="mb-2 block text-lg font-semibold text-slate-700" for="operacion">Operaci&oacute;n</label>
                        <select id="operacion"
                            class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-lg text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                            name="operacion" required>
                            <option value="" disabled selected>-Seleccionar-</option>
                            <option value="ingreso">Ingreso a caja</option>
                            <option value="gasto">Gasto</option>
                        </select>
                    </section>

                    <div id="origengasto" class="hidden grid-cols-1 gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-2 sm:grid-cols-2">
                        <label for="gastocaja"
                            class="flex min-h-[4.6rem] cursor-pointer select-none items-center justify-center rounded-xl border border-transparent bg-white px-4 text-lg font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50">
                            <input id="gastocaja" type="radio" name="origengasto" class="hidden peer" value="gastocaja" checked>
                            <span class="mr-3 h-4 w-4 rounded-full border border-indigo-300 bg-white transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600"></span>
                            <span>Gastos de la caja</span>
                        </label>

                        <label for="gastobanco"
                            class="flex min-h-[4.6rem] cursor-pointer select-none items-center justify-center rounded-xl border border-transparent bg-white px-4 text-lg font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50">
                            <input id="gastobanco" type="radio" name="origengasto" class="hidden peer" value="gastobanco">
                            <span class="mr-3 h-4 w-4 rounded-full border border-indigo-300 bg-white transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600"></span>
                            <span>Gastos transaccionales</span>
                        </label>
                    </div>

                    <section class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div id="showbancos" class="hidden sm:col-span-2">
                                <label class="mb-2 block text-lg font-semibold text-slate-700" for="banco">Banco</label>
                                <select id="banco"
                                    class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-lg text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    name="id_banco">
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($bancos as $value): ?>
                                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="showcajas" class="hidden sm:col-span-2">
                                <label class="mb-2 block text-lg font-semibold text-slate-700" for="caja">Caja</label>
                                <select id="caja"
                                    class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-lg text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    name="id_caja" required>
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($cajas as $value): ?>
                                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="formulario__campo tipodegasto hidden sm:col-span-2">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label class="block text-lg font-semibold text-slate-700" for="tipodegasto">Tipo de gasto</label>
                                    <a href="/admin/caja/categoriaGasto"
                                        class="text-base font-bold text-indigo-600 transition hover:text-indigo-800">Agregar categor&iacute;a</a>
                                </div>
                                <select id="tipodegasto"
                                    class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-lg text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    name="idcategoriagastos">
                                    <option value="" disabled selected>-Seleccionar-</option>
                                    <?php foreach($categoriasgastos as $value): ?>
                                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="soporteGasto" class="hidden sm:col-span-2">
                                <label class="mb-2 block text-lg font-semibold text-slate-700" for="soporte">Soporte f&iacute;sico</label>
                                <input id="soporte" type="file" name="imgcomprobante" accept="image/*"
                                    class="block w-full rounded-xl border border-slate-300 bg-white p-3 text-lg text-slate-900 transition file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:font-bold file:text-indigo-700 focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                <p class="m-0 mt-2 text-base leading-6 text-slate-500">Formatos permitidos: JPG, PNG, JPEG.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-lg font-semibold text-slate-700" for="dinero">Valor</label>
                                <input id="dinero"
                                    class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-lg font-semibold text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    type="text" placeholder="Ingrese el dinero" name="valor" value=""
                                    oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString('es-CO')"
                                    required>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-lg font-semibold text-slate-700" for="descripcion">Descripci&oacute;n</label>
                                <textarea id="descripcion"
                                    class="block h-36 w-full rounded-xl border border-slate-300 bg-white p-4 text-lg text-slate-900 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    name="descripcion" rows="4" placeholder="Detalle breve del movimiento..."></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-200 bg-white px-8 py-6">
                    <button type="button" class="h-14 rounded-xl bg-teal-500 px-6 text-lg font-bold text-white transition hover:bg-teal-600"
                        onclick="document.getElementById('gastosIngresos').close()">Cancelar</button>
                    <input id="btnEnviargastosingresos" type="submit" value="Aplicar"
                        class="h-14 cursor-pointer rounded-xl border-0 bg-indigo-600 px-6 text-lg font-bold text-white transition hover:bg-indigo-700">
                </div>
            </form>
        </div>
    </dialog>

    <!-- Modal Abrir cajon monedero -->
    <dialog class="rounded-2xl border border-slate-200 w-[92vw] max-w-[48rem] overflow-hidden p-0 bg-white backdrop:bg-slate-900/55 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out" id="miDialogoAbrirCaja">
        <div class="flex items-start justify-between gap-5 border-b border-slate-200 px-8 py-7">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                    <i class="fa-solid fa-cash-register text-3xl"></i>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-[.28em] text-indigo-600">Autorizacion</p>
                    <h2 class="m-0 text-3xl font-bold leading-tight text-slate-900">Abrir caj&oacute;n monedero</h2>
                    <p class="m-0 mt-2 text-lg leading-7 text-slate-500">Ingrese la clave para autorizar esta operaci&oacute;n.</p>
                </div>
            </div>
            <button type="button" class="noAbrirCajon grid h-10 w-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="space-y-6 px-8 py-7">
            <div id="divmsjalerta3"></div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="mb-4 flex items-center gap-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-700">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <div>
                        <label for="inputAbrirCaja" class="m-0 block text-xl font-bold text-slate-900">Clave de autorizaci&oacute;n</label>
                        <p class="m-0 mt-1 text-base leading-6 text-slate-500">Solicite la clave al usuario autorizado.</p>
                    </div>
                </div>

                <input
                    id="inputAbrirCaja"
                    type="password"
                    class="block h-16 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl font-semibold text-slate-800 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    placeholder="Ingrese la clave">
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-200 pt-6">
                <button
                    type="button"
                    class="noAbrirCajon h-14 rounded-xl border border-slate-300 bg-white px-6 text-lg font-bold text-slate-700 transition hover:bg-slate-50">
                    Cancelar
                </button>

                <button
                    type="button"
                    class="siAbrirCajon h-14 rounded-xl bg-indigo-600 px-6 text-lg font-bold text-white shadow-sm transition hover:bg-indigo-700">
                    <i class="fa-solid fa-lock-open mr-2"></i>
                    Confirmar
                </button>
            </div>
        </div>
    </dialog>


    <!-- MODAL CAMBIO MEDIO DE PAGO -->
    <dialog id="cambioMedioPago"
        class="rounded-2xl border border-slate-200 w-[94vw] max-w-[60rem] max-h-[92vh] overflow-hidden p-0 bg-white backdrop:bg-slate-900/55 shadow-2xl transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out">

        <div class="flex max-h-[92vh] flex-col">
            <!-- Encabezado -->
            <div class="flex items-start justify-between gap-5 border-b border-slate-200 px-8 py-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                        <span class="material-symbols-outlined text-4xl">payments</span>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-bold uppercase tracking-[0.18em] text-indigo-600">Pagos de factura</p>
                        <h4 class="m-0 text-3xl font-extrabold leading-tight text-slate-900">Cambiar medio de pago</h4>
                        <p id="numfactura" class="mt-2 text-lg font-semibold text-slate-500"></p>
                    </div>
                </div>

                <button
                    type="button"
                    id="btnCerrarCambioMedioPago"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                    onclick="document.getElementById('cambioMedioPago').close()">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <div id="divmsjalerta2"></div>

            <form id="formCambioMedioPago"
                class="formulario flex min-h-0 flex-1 flex-col"
                action="/admin/caja/cambioMedioPago"
                method="POST">

                <div class="min-h-0 flex-1 overflow-y-auto px-8 py-7">
                    <!-- Total pagado + Diferencia -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-5">
                            <p class="m-0 text-sm font-bold uppercase tracking-[0.2em] text-emerald-700">Total pagado</p>
                            <p class="m-0 mt-3 text-5xl font-extrabold leading-none text-emerald-700">
                                $<span id="totalPagado">0</span>
                            </p>
                        </div>

                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="m-0 text-sm font-bold uppercase tracking-[0.2em] text-indigo-600">Diferencia</p>
                                    <p id="diferencia" class="m-0 mt-3 text-5xl font-extrabold leading-none text-indigo-600">0</p>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-sm font-bold text-slate-500">Debe ser 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Medios de pago -->
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <div class="mb-5 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                <span class="material-symbols-outlined text-3xl">account_balance_wallet</span>
                            </div>
                            <div>
                                <h5 class="m-0 text-2xl font-extrabold text-slate-900">Medios de pago</h5>
                                <p class="m-0 mt-1 text-lg leading-relaxed text-slate-500">Ajuste los valores y confirme que la diferencia quede en $0.</p>
                            </div>
                        </div>

                        <div
                            id="ayudaCambioMedioPago"
                            class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-lg leading-relaxed text-amber-800">
                            <span class="material-symbols-outlined mt-0.5 text-3xl text-amber-600">info</span>
                            <div>
                                <strong>Importante:</strong> reduzca el valor del medio actual y registre ese mismo monto en el nuevo medio de pago.
                            </div>
                        </div>

                        <div
                            id="mediospagos"
                            class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <?php foreach($mediospago as $index => $value): ?>
                                <div class="contenedor-medio-pago relative rounded-xl border border-slate-200 bg-white p-4">

                                    <!-- Esta etiqueta la mostrara luego el JavaScript -->
                                    <span
                                        class="medio-actual hidden mb-2 w-fit rounded-full bg-indigo-100 px-3 py-1 text-sm font-bold leading-none text-indigo-700">
                                        Pago original
                                    </span>

                                    <label
                                        class="mb-2 block min-h-[1.7rem] text-lg font-bold leading-tight text-slate-700">

                                        <?php echo $value->mediopago ?? ''; ?>
                                    </label>

                                    <input
                                        id="<?php echo $value->id ?? ''; ?>"
                                        class="mediopago input-medio-pago <?php echo $value->mediopago ?? ''; ?> block h-16 w-full rounded-xl border border-slate-300 bg-white p-3 text-center text-2xl font-bold text-slate-800 transition focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                        type="text"
                                        value="0"
                                        oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="cambio-pago-actions grid grid-cols-2 border-t border-slate-200 bg-white">
                    <button
                        type="button"
                        class="transition hover:bg-teal-600"
                        onclick="document.getElementById('cambioMedioPago').close()">
                        Cancelar
                    </button>

                    <input
                        id="btnEnviarCambioMedioPago"
                        class="transition hover:bg-indigo-700"
                        type="submit"
                        value="Aplicar">
                </div>
            </form>
        </div>
    </dialog>

    
<style>
    #cambioMedioPago .cambio-pago-actions {
        gap: 1.4rem;
        padding: 1.6rem 2.4rem;
    }

    #cambioMedioPago .cambio-pago-actions button,
    #cambioMedioPago .cambio-pago-actions input {
        border: 0;
        border-radius: .8rem;
        cursor: pointer;
        display: block;
        font-size: 1.65rem;
        font-weight: 700;
        height: 4.8rem;
        line-height: 1;
        min-height: 4.8rem;
        padding: 0 1.8rem;
        text-align: center;
        width: 100%;
    }

    #cambioMedioPago .cambio-pago-actions button {
        background: #14b8a6;
        color: #fff;
    }

    #cambioMedioPago .cambio-pago-actions input {
        background: #4f46e5;
        color: #fff;
    }

    @media (max-width: 640px) {
        #cambioMedioPago .cambio-pago-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
    <script>
        document.getElementById('operacion').addEventListener('change', function () {
            const esGasto = this.value === 'gasto';
            //document.getElementById('origengasto').classList.toggle('hidden', !esGasto);
            //document.querySelector('.tipodegasto').classList.toggle('hidden', !esGasto);
            document.getElementById('soporteGasto').classList.toggle('hidden', !esGasto);
        });

        const getParam = <?= json_encode($conflocal) ?>;
    </script>
</div>









