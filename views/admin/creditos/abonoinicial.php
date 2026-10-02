<!-- MODAL PARA ABONAR-->
  <dialog id="miDialogoAbono" class="detalledialog_xs">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 sm:items-center sm:p-6">
        <div class="flex min-w-0 items-start gap-4 sm:items-center">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 sm:size-16">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </span>
            <div>
                <p class="m-0 text-base font-bold uppercase text-indigo-600">Credito</p>
                <h4 id="modalAbono" class="m-0 text-xl font-bold text-slate-900 sm:text-2xl">Registrar abono</h4>
                <span class="mt-1 block text-sm text-slate-500 sm:text-base">Aplica un pago parcial al saldo pendiente.</span>
            </div>
        </div>
        <button type="button" class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-xl text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 sm:size-14" aria-label="Cerrar registro de abono">
            <i id="btnXCerrarModalAbono" class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta2"></div>
    <form id="formCrearUpdateAbono" class="grid gap-4 p-4 sm:p-6" action="/admin/creditos/registrarAbono" method="POST">
        <!-- El monto de la cuota se calcula atomaticamente segun la cantidad de cuotas-->
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-slate-50 to-cyan-50 p-4">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-receipt"></i></span>
            <div class="min-w-0">
                <label for="montocuota" class="block text-sm font-bold uppercase text-slate-500">Valor de la cuota</label>
                <input id="montocuota" class="h-auto w-full border-0 bg-transparent p-0 text-2xl font-semibold text-slate-900 outline-none" type="text" placeholder="Valor de la cuota" name="montocuota" value="$<?php echo number_format($credito->montocuota??'0', '2', ',', '.');?>" readonly required>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="detalle-abono-dialog__field grid gap-2">
            <label for="caja">Caja</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-cash-register"></i></span>
            <select 
                id="caja" 
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
            <label for="mediopago">Medio de pago</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-credit-card"></i></span>
            <select id="mediopago" name="mediopagoid" required>
                <?php foreach($mediospago as $value):  ?>
                      <option value="<?php echo $value->id;?>" ><?php echo $value->mediopago;?></option>
                <?php endforeach; ?>
            </select>
            </div>
        </div>
        <div class="detalle-abono-dialog__field col-span-full grid gap-2">
            <label for="abono">Abono</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-dollar-sign"></i></span>
                <input
                    id="abono" 
                    type="text"
                    placeholder="Abono de la deuda"
                    name="valorpagado"
                    value="<?php echo $cuota->valorpagado??'';?>"
                    oninput="formatearMoneda(this)"
                    required
                >
            </div>
        </div>

        <div class="detalle-abono-dialog__field col-span-full grid gap-2">
            <label for="detalle">Observacion</label>
            <textarea id="detalle" name="detalle" placeholder="Observacion" rows="4"></textarea>
        </div>
        </div>
        
        <label for="imprimirComprobanteAbonoinicial" class="detalle-abono-dialog__switch items-start sm:items-center">
            <span class="detalle-abono-dialog__switch-copy">
                <strong>Imprimir comprobante</strong>
                <small>Genera el soporte del abono al confirmar.</small>
            </span>
            
            <input 
                id="imprimirComprobanteAbonoinicial" 
                name="imprimirComprobanteAbonoinicial" 
                value="1" 
                type="checkbox" 
                class="sr-only peer"
                <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?>
                >
            <div class="detalle-abono-dialog__toggle">
                <small class="detalle-abono-dialog__toggle-state detalle-abono-dialog__toggle-state--off">OFF</small>
                <small class="detalle-abono-dialog__toggle-state detalle-abono-dialog__toggle-state--on">ON</small>
                <span></span>
            </div>
        </label>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-4 sm:grid-cols-2">
            <button class="btnDialog btnDialog_secondary" type="button" value="salir">Salir</button>
            <input id="btnEditarCrearAbono" class="btnDialog btnDialog_primary" type="submit" value="Confirmar">
        </div>
    </form>
  </dialog><!--fin modal Abonoar-->
