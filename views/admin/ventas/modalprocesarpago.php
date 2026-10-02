<!--///////////////////// Modal procesar el pago boton facturar /////////////////////////-->
<dialog id="miDialogoFacturar" class="midialog-md !p-0">
  <form id="formfacturar" class="formulario flex flex-col" method="POST">
    <div class="venta-pago-modal__body overflow-y-auto px-8 pt-6 pb-0">
      <div class="flex flex-col gap-4 pb-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="m-0 text-sm font-bold uppercase tracking-wide text-indigo-600">Venta</p>
          <h4 class="m-0 text-3xl font-bold leading-tight text-slate-900">
            Procesar venta
          </h4>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-6 pt-3 pb-2 text-right min-w-[18rem]">
          <p class="m-0 text-xs font-bold uppercase tracking-[0.22em] text-emerald-700">
            Total de la venta
          </p>
          <p id="totalPagar"
            class="m-0 text-4xl font-bold text-emerald-600"
            style="font-family:'Tektur', serif;">
              $0
          </p>
        </div>
      </div>

      <div id="divmsjalertaprocesarpago" class="mt-4"></div>

      <div class="mt-4 grid grid-cols-1 gap-4">
        <div class="space-y-4">
          <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="mb-4 flex items-start gap-3">
              <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-600">
                <span class="material-symbols-outlined text-2xl">shopping_cart</span>
              </div>
              <div>
                <h5 class="m-0 text-xl font-bold text-slate-900">Informaci&oacute;n de la venta</h5>
                <p class="m-0 text-base text-slate-500">Caja, facturador y canal principal.</p>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="caja">Caja</label>
                <select id="caja" class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500" name="caja" required>
                  <?php foreach($cajas as $index => $value):?>
                    <option value="<?php echo $value->id;?>" data-idfacturador="<?php echo $value->idtipoconsecutivo;?>" data-idemisor="<?php echo $value->idemisor;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="facturador">Facturador</label>
                <select id="facturador" class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500" name="facturador" required>
                  <?php foreach($consecutivos as $index => $value):?>
                    <option data-idtipofacturador="<?php echo $value->idtipofacturador;?>" value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <?php if($conflocal['habilitar_canal_de_venta']->valor_final): ?>
              <div id="contenedorCanalVenta" class="formulario__campo !mb-0 md:col-span-2">
                <label class="formulario__label !text-lg" for="canalVenta">Canal de venta</label>
                <select id="canalVenta" class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500" name="canalventa" required>
                  <?php foreach($canalesVenta as $index => $value):?>
                    <option data-idCanalVenta="<?php echo $value->id;?>" value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
            </div>

            <div id="inputscreditos" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div id="campoabonoinicial" class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="abonoinicial">Abono inicial</label>
                <input id="abonoinicial" name="abonoinicial" type="text" placeholder="0" value="0"
                  class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500"
                  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                >
              </div>

              <div class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="interes">Aplicar inter&eacute;s</label>
                <select id="interes" class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500" name="interes">
                  <option value="" disabled selected>-Seleccionar-</option>
                  <option value="1">S&iacute;</option>
                  <option value="0">No</option>
                </select>
              </div>

              <div id="campocantidadcuotas" class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="cantidadcuotas">Plazo (cuotas)</label>
                <input id="cantidadcuotas" name="cantidadcuotas" type="text" min="1" placeholder="Cantidad de cuotas" value="1"
                  class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500"
                  oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, ''); if(this.value === '' || this.value === '0'){this.value = '';}"
                >
              </div>

              <div id="campomontocuota" class="formulario__campo !mb-0">
                <label class="formulario__label !text-lg" for="montocuota">Valor de la cuota</label>
                <input id="montocuota" name="montocuota" type="text" readonly
                  class="bg-white border border-slate-300 text-slate-900 rounded-xl block w-full p-2 h-13 text-lg focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500">
              </div>
            </div>
          </section>

          <p id="abonoTotal" class="hidden m-0 text-center text-2xl font-light text-slate-600">Abono inicial: <span id="valorAbono" class="font-semibold text-slate-800">0</span></p>

          <section class="accordion payment-methods-panel rounded-2xl border border-slate-200 bg-slate-50 px-4 pb-4">
            <input type="checkbox" id="first">
            <label class="etiqueta !flex cursor-pointer items-center justify-between gap-4 !pb-0 pr-3" for="first">
              <div class="flex items-start gap-3">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-600">
                  <span class="material-symbols-outlined text-2xl">payments</span>
                </div>
                <div>
                  <h5 class="m-0 text-xl leading-5 font-bold text-slate-900">M&eacute;todos de pago</h5>
                  <p class="m-0 text-base text-slate-500">Registre uno o varios medios de pago.</p>
                </div>
              </div>

              <span id="btnTogglePaymentMethods" class="payment-methods-toggle ml-auto mr-2 inline-flex h-11 min-w-[10rem] shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-lg font-semibold text-white shadow-sm" role="button" tabindex="0">
                <span class="material-symbols-outlined text-xl">add</span>
                Agregar
              </span>

              

            </label>


            <div class="wrapper">
              
              <div id="paymentMethodsRequiredMessage" class="payment-methods-required-message" role="alert" aria-live="polite">
                <span class="material-symbols-outlined">error</span>
                <div>
                  <strong>Falta asignar el abono</strong>
                  <p>Selecciona un medio de pago y registra el valor correspondiente al abono inicial.</p>
                </div>
              </div>

              <div class="wrapper-content">
                <div id="mediospagos" class="content grid grid-cols-1 gap-4 pt-5 md:grid-cols-2">
                  <?php foreach($mediospago as $index => $value):?>
                    <div class="flex flex-col">
                      <label class="mb-2 text-lg font-semibold text-slate-700">
                        <?php echo $value->mediopago ?? ''; ?>
                      </label>

                      <input
                        id="<?php echo $value->id ?? ''; ?>"
                        class="mediopago <?php echo $value->mediopago ?? ''; ?> block h-13 w-full rounded-xl border border-slate-300 bg-white p-3 text-right text-xl text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        type="text"
                        value="0"
                        <?php echo $value->mediopago == 'Efectivo' ? 'readonly' : ''; ?>
                        oninput="formatearMoneda(this)">
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </section>
        </div>

        <aside class="space-y-4">
          <section class="rounded-2xl border border-slate-200 bg-slate-50 px-4 pb-4 pt-3">
            <div class="grid gap-3 sm:grid-cols-2">
              <div>
                <label class="mb-0 flex items-center gap-2 text-lg font-semibold text-slate-700" for="recibio">
                  <span class="material-symbols-outlined text-emerald-600">payments</span>
                  Efectivo recibido
                </label>
                <input
                  id="recibio"
                  class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-right text-xl font-medium text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                  type="text"
                  placeholder="0"
                  oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()">
              </div>

              <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                <p class="m-0 text-sm font-semibold uppercase tracking-wide text-emerald-700">
                  Cambio a entregar
                </p>
                <p id="cambio" class="m-0 mt-1 text-3xl font-bold text-emerald-600">
                  $0
                </p>
              </div>
            </div>
          
            <div class="mb-1 mt-6 flex items-center gap-3">
              <span class="material-symbols-outlined text-indigo-600">print</span>
              <p id="textPrint" class="m-0 text-xl font-bold text-slate-800">
                &iquest;Imprimir factura?
              </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <label class="cursor-pointer">
                <input type="radio" name="imprimir" value="1" class="peer sr-only" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?> >
                <span class="flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white text-lg font-semibold text-slate-700 transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">S&iacute;</span>
              </label>

              <label class="cursor-pointer">
                <input type="radio" name="imprimir" value="0" class="peer sr-only" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 0?'checked':'';?> >
                <span class="flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white text-lg font-semibold text-slate-700 transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">No</span>
              </label>
            </div>
          </section>

          <div id="confirmarDespacho" class="hidden">
            <label for="despachar" class="mb-6 flex cursor-pointer flex-col items-center">
              <span class="formulario__label mb-2 block text-center">Despachar orden</span>
              <input
                id="despachar"
                name="despachar"
                value="1"
                type="checkbox"
                class="peer sr-only"
                <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?>
                >
              <div class="relative h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-indigo-600">
                <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5"></div>
              </div>
            </label>
          </div>

          <section class="rounded-2xl border border-slate-200 bg-slate-50 px-4 pb-3 pt-2">
            <label for="observacion" class="flex items-center gap-2 text-xl font-bold text-slate-800">
              <span class="material-symbols-outlined text-indigo-600">edit_note</span>
              Observaci&oacute;n
            </label>

            <textarea
              id="observacion"
              name="observacion"
              rows="3"
              placeholder="Escriba una observaci&oacute;n (opcional)..."
              class="w-full resize-none rounded-xl border border-slate-300 bg-white p-3 text-lg text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
          </section>
        </aside>
      </div>

    </div>

    <div class="venta-pago-modal__footer flex shrink-0 justify-end gap-4 bg-white px-6 pb-6 sm:px-8">
      <button class="btn-md btn-turquoise transition-all duration-200 hover:scale-105 !py-4 !px-6 !w-[160px]" type="button" value="Cancelar">Cancelar</button>
      <input id="btnPagar" class="btn-md btn-indigo transition-all duration-200 hover:scale-105 !py-4 px-6 !w-[160px]" type="submit" value="Pagar">
    </div>
  </form>
</dialog>

<style>
  @media (max-width: 640px) {
    #miDialogoFacturar {
      height: auto !important;
      max-height: calc(100dvh - 2rem) !important;
      overflow: hidden !important;
    }

    #miDialogoFacturar #formfacturar {
      height: auto !important;
      max-height: calc(100dvh - 2rem) !important;
      min-height: 0 !important;
      overflow: hidden !important;
    }

    #miDialogoFacturar .venta-pago-modal__body {
      flex: 0 1 auto;
      min-height: 0;
      overflow-y: auto;
      padding-bottom: 1rem !important;
    }

    #miDialogoFacturar .venta-pago-modal__footer {
      bottom: 0;
      margin: 0 !important;
      position: sticky;
      z-index: 2;
    }

    #miDialogoFacturar #confirmarDespacho label {
      margin-bottom: 1rem !important;
    }
  }
</style>

