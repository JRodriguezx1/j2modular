<!-- MODAL PARA PAGO TOTAL-->
  <dialog id="miDialogoPagoTotal" class="detalledialog_xs">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 sm:items-center sm:p-6">
        <div class="flex min-w-0 items-start gap-4 sm:items-center">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-2xl text-teal-700 sm:size-16">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="m-0 text-base font-bold uppercase text-indigo-600">Credito</p>
                <h4 id="modalPagoTotal" class="m-0 text-xl font-bold text-slate-900 sm:text-2xl">Pago total</h4>
                <span class="mt-1 block text-sm text-slate-500 sm:text-base">Cierra la deuda pendiente del cliente en una sola operacion.</span>
            </div>
        </div>
        <button id="btnXCerrarModalPagoTotal" type="button" class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-xl text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 sm:size-14" aria-label="Cerrar pago total">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta3"></div>
    <form id="formCrearUpdatePagoTotal" class="grid gap-4 p-4 sm:p-6" action="/admin/creditos/pagoTotal" method="POST">
        <!-- El monto de la cuota se calcula atomaticamente segun la cantidad de cuotas-->
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-slate-50 to-cyan-50 p-4">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-xl text-teal-700"><i class="fa-solid fa-wallet"></i></span>
            <div class="min-w-0">
                <label class="block text-sm font-bold uppercase text-slate-500">Total a pagar</label>
                <strong id="PagoTotal_abono_text" class="block text-2xl font-semibold leading-tight text-slate-900">$ <?php echo number_format($credito->saldopendiente??'0', '2', ',', '.');?></strong>
            </div>
        </div>
        
        <input id="PagoTotal_montocuota" class="hidden" type="text" name="montocuota" value="$<?php echo number_format($credito->montocuota??'0', '2', ',', '.');?>" readonly required>    
       <input id="PagoTotal_abono" class="hidden" type="text" name="valorpagado" value="<?php echo number_format($credito->saldopendiente??'0', '2', ',', '.');?>">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="detalle-abono-dialog__field grid gap-2">
                <label for="PagoTotal_caja">Caja</label>
                <div class="detalle-abono-dialog__control">
                    <span><i class="fa-solid fa-cash-register"></i></span>
                <select
                    id="PagoTotal_caja"
                    name="cajaid"
                    <?= $conflocal['restringir_caja_facturadora_a_caja_inicial_del_credito']->valor_final==1?'disabled':''; ?>
                    required
                >
                    <?php foreach($cajas as $value):  ?>
                        <option value="<?php echo $value->id;?>" data-idemisor="<?= $value->idemisor ?>" <?=($credito->idemisor==$value->idemisor)?'selected':'';?> ><?php echo $value->nombre;?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <div class="detalle-abono-dialog__field grid gap-2">
                <label for="PagoTotal_mediopago">Medio de pago</label>
                <div class="detalle-abono-dialog__control">
                    <span><i class="fa-solid fa-credit-card"></i></span>
                <select id="PagoTotal_mediopago" name="mediopagoid" required>
                    <?php foreach($mediospago as $value):  ?>
                        <option value="<?php echo $value->id;?>" ><?php echo $value->mediopago;?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>

            <input class="hidden" type="text" name="concepto" value="PAGO DEUDA TOTAL A FACTURA">
            <div class="detalle-abono-dialog__field col-span-full grid gap-2">
                <label for="pagoTotal_detalle">Observacion</label>
                <textarea id="pagoTotal_detalle" name="detalle" placeholder="Observacion" rows="4"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-4 sm:grid-cols-2">
            <button class="btnDialog btnDialog_secondary" type="button" value="salir">Salir</button>
            <input id="btnEditarCrearPagoTotal" class="btnDialog btnDialog_primary" type="submit" value="Confirmar">
        </div>
    </form>
  </dialog><!--fin modal PagoTotal-->
