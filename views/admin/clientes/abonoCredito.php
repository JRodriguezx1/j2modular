<!-- MODAL PARA ABONAR-->
<dialog id="miDialogoAbono" class="max-h-[92vh] w-[min(94vw,58rem)] max-w-[58rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]">
    <div class="flex items-start gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-hand-holding-dollar"></i></span>
        <div class="min-w-0 flex-1">
            <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Credito</p>
            <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalAbono">Registrar abono</h4>
            <small class="mt-1 block text-base leading-snug text-slate-500">Aplica un pago parcial al saldo pendiente.</small>
        </div>
        <button type="button" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" title="Cerrar">
            <i id="btnXCerrarModalAbono" class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta2"></div>
    <form id="formrealizarAbono" class="grid grid-cols-1 gap-5 p-5 sm:p-6">
        <div class="rounded-lg border border-slate-200 bg-gradient-to-br from-violet-50 to-cyan-50 p-4 text-center">
            <h5 class="m-0 text-xl font-black text-slate-900" id="numCredito">Credito No.: </h5>
            <p class="mt-1 text-lg font-extrabold text-red-600" id="saldopendiente">Saldo pendiente: $500.000</p>
        </div>

        <div class="form-field">
            <label for="abono_caja">Caja</label>
            <div class="form-input">
                <span><i class="fa-solid fa-cash-register"></i></span>
                <select id="abono_caja" name="cajaid" required>
                    <?php foreach($cajas as $value): ?>
                    <option value="<?php echo $value->id; ?>"><?php echo htmlspecialchars($value->nombre, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-field">
            <label for="abono_mediopago">Medio de pago</label>
            <div class="form-input">
                <span><i class="fa-solid fa-credit-card"></i></span>
                <select id="abono_mediopago" name="mediopagoid" required>
                    <?php foreach($mediospago as $value): ?>
                    <option value="<?php echo $value->id; ?>"><?php echo htmlspecialchars($value->mediopago, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-field">
            <label for="abono">Abono</label>
            <div class="form-input">
                <span><i class="fa-solid fa-dollar-sign"></i></span>
                <input
                    id="abono"
                    type="text"
                    placeholder="Abono de la deuda"
                    name="valorpagado"
                    value="<?php echo $cuota->valorpagado??'';?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '0';"
                    required
                >
            </div>
        </div>

        <label for="imprimirAbono" class="cliente-detail-switch">
            <span>
                <strong>Imprimir comprobante</strong>
                <small>Genera el soporte del abono al confirmar.</small>
            </span>
            <input
                id="imprimirAbono"
                name="imprimirAbono"
                value="1"
                type="checkbox"
                <?php echo $conflocal['imprimir_factura_automaticamente']->valor_final == 1?'checked':'';?>
            >
            <i aria-hidden="true"></i>
        </label>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-5 sm:grid-cols-2">
            <button class="btnDialog btnDialog_secondary w-full" type="button" value="Salir">Salir</button>
            <button id="btnEditarCrearAbono" class="btnDialog btnDialog_primary w-full" type="submit">Confirmar</button>
        </div>
    </form>
</dialog><!--fin modal Abonar-->
