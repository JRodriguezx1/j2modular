<!--///////////////////// Modal procesar el pago boton facturar /////////////////////////-->
<dialog id="miDialogoFacturar" class="j2-dialog j2-dialog--lg" aria-labelledby="j2PaymentTitle">
  <form id="formfacturar" class="formulario j2-dialog__layout !gap-0" method="POST">
    <header class="j2-dialog__header flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="m-0 text-sm font-bold uppercase tracking-wide text-indigo-600">Venta</p>
          <h4 id="j2PaymentTitle" class="j2-dialog__title">
            Procesar venta
          </h4>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-6 pt-3 pb-2 text-right w-full sm:w-auto sm:min-w-[18rem]">
          <p class="m-0 text-xs font-bold uppercase tracking-[0.22em] text-emerald-700">
            Total de la venta
          </p>
          <p id="totalPagar"
            class="m-0 text-4xl font-bold text-emerald-600"
            style="font-family:'Tektur', serif;">
              $0
          </p>
        </div>
    </header>
    <div class="venta-pago-modal__body j2-dialog__body">
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
              <div class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="caja">Caja</label>
                <select id="caja" class="j2-select" name="caja" required>
                  <?php foreach($cajas as $index => $value):?>
                    <option value="<?php echo $value->id;?>" data-idfacturador="<?php echo $value->idtipoconsecutivo;?>" data-idemisor="<?php echo $value->idemisor;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="facturador">Facturador</label>
                <select id="facturador" class="j2-select" name="facturador" required>
                  <?php foreach($consecutivos as $index => $value):?>
                    <option data-idtipofacturador="<?php echo $value->idtipofacturador;?>" value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <?php if($conflocal['habilitar_canal_de_venta']->valor_final): ?>
              <div id="contenedorCanalVenta" class="formulario__campo j2-field md:col-span-2">
                <label class="formulario__label j2-field__label" for="canalVenta">Canal de venta</label>
                <select id="canalVenta" class="j2-select" name="canalventa" required>
                  <?php foreach($canalesVenta as $index => $value):?>
                    <option data-idCanalVenta="<?php echo $value->id;?>" value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
            </div>

            <div id="inputscreditos" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div id="campoabonoinicial" class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="abonoinicial">Abono inicial</label>
                <input id="abonoinicial" name="abonoinicial" type="text" placeholder="0" value="0"
                  class="j2-input"
                  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                >
              </div>

              <div class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="interes">Aplicar inter&eacute;s</label>
                <select id="interes" class="j2-select" name="interes">
                  <option value="" disabled selected>-Seleccionar-</option>
                  <option value="1">S&iacute;</option>
                  <option value="0">No</option>
                </select>
              </div>

              <div id="campocantidadcuotas" class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="cantidadcuotas">Plazo (cuotas)</label>
                <input id="cantidadcuotas" name="cantidadcuotas" type="text" min="1" placeholder="Cantidad de cuotas" value="1"
                  class="j2-input"
                  oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, ''); if(this.value === '' || this.value === '0'){this.value = '';}"
                >
              </div>

              <div id="campomontocuota" class="formulario__campo j2-field">
                <label class="formulario__label j2-field__label" for="montocuota">Valor de la cuota</label>
                <input id="montocuota" name="montocuota" type="text" readonly
                  class="j2-input">
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
                      <label class="j2-field__label" for="<?php echo htmlspecialchars((string)($value->id ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $value->mediopago ?? ''; ?>
                      </label>

                      <input
                        id="<?php echo $value->id ?? ''; ?>"
                        class="mediopago <?php echo $value->mediopago ?? ''; ?> j2-input j2-input--money"
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
                  class="j2-input j2-input--money"
                  type="text"
                  placeholder="0"
                  oninput="formatearMoneda(this)"
                  >
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

            <div class="j2-segmented" role="group" aria-labelledby="textPrint">
              <label class="j2-segmented__option">
                <input type="radio" name="imprimir" value="1" class="j2-segmented__input" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?> >
                <span class="j2-segmented__text">S&iacute;</span>
              </label>

              <label class="j2-segmented__option">
                <input type="radio" name="imprimir" value="0" class="j2-segmented__input" <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 0?'checked':'';?> >
                <span class="j2-segmented__text">No</span>
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
              class="j2-textarea"></textarea>
          </section>
        </aside>
      </div>

    </div>

    <div class="venta-pago-modal__footer j2-dialog__footer">
      <button class="j2-btn j2-btn--secondary j2-btn--lg" type="button" value="Cancelar">Cancelar</button>
      <input id="btnPagar" class="j2-btn j2-btn--primary j2-btn--lg" type="submit" value="Pagar">
    </div>
  </form>
</dialog>

