<!-- MODAL PARA AJUSTAR CREDITO-->
  <dialog id="miDialogoAjustarCredito" class="detalledialog_xs">
    <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 sm:items-center sm:p-6">
        <div class="flex min-w-0 items-start gap-4 sm:items-center">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600 sm:size-16">
                <i class="fa-solid fa-sliders"></i>
            </span>
            <div>
                <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Credito</p>
                <h4 id="modalAjustarCredito" class="m-0 text-xl font-extrabold text-slate-900 sm:text-2xl">Ajustar credito</h4>
                <span class="mt-1 block text-sm text-slate-500 sm:text-base">Actualiza recargos, abonos anteriores y fecha inicial.</span>
            </div>
        </div>
        <button type="button" class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-xl text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 sm:size-14" aria-label="Cerrar ajuste de credito">
            <i id="btnXCerrarModalAjustarCredito" class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta3"></div>
    <form id="formAjustarCredito" class="grid gap-4 p-4 sm:p-6" >
        <input id="capital" class="hidden" type="text" name="capital" value="<?php echo $credito->capital??'';?>">
        <input id="abonoinicial" class="hidden" type="text" name="abonoinicial" value="<?php echo $credito->abonoinicial??'';?>">
        <input id="saldopendiente" class="hidden" type="text" name="saldopendiente" value="<?php echo $credito->saldopendiente??'';?>">
        <input id="montototal" class="hidden" type="text" name="montototal" value="<?php echo $credito->montototal??'';?>">

        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-slate-50 to-cyan-50 p-4">
            <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <div class="min-w-0">
                <label class="block text-sm font-extrabold uppercase text-slate-500">Credito actual</label>
                <strong class="block text-2xl font-black leading-tight text-slate-900">$ <?php echo number_format($credito->saldopendiente??'0', '2', ',', '.');?></strong>
            </div>
        </div>
        
        <div class="recargo"></div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="detalle-abono-dialog__field grid gap-2">
            <label for="recargo">Recargo interes</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-percent"></i></span>
                <input 
                    id="recargo" 
                    type="text"
                    placeholder="Recargo de interes al credito"
                    value="<?php echo $credito->valorinterestotal??'';?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                >
            </div>
        </div>

        <div class="detalle-abono-dialog__field grid gap-2">
            <label for="abonoTotalAntiguo">Abono total antiguo</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-hand-holding-dollar"></i></span>
                <input 
                    id="abonoTotalAntiguo" 
                    type="text"
                    placeholder="Monto antiguo pagado hasta la fecha"
                    value="<?php echo $credito->abonototalantiguo??'';?>"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                >
            </div>
        </div>

        <div class="detalle-abono-dialog__field col-span-full grid gap-2">
            <label for="ajustarFechaInicio">Ajustar fecha de inicio</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-calendar-days"></i></span>
                <input 
                    id="ajustarFechaInicio" 
                    type="date"
                    value="<?php echo $credito->fechainicio??'';?>"
                >
            </div>
        </div>

        <div class="detalle-abono-dialog__field col-span-full grid gap-2">
            <label for="inputPasswordAjustarCredito">Clave de autorizacion</label>
            <div class="detalle-abono-dialog__control">
                <span><i class="fa-solid fa-key"></i></span>
                <input id="inputPasswordAjustarCredito" type="password" name="ajustarCreditoClave" class="miles" placeholder="Ingresa la clave para ajustar el credito">
            </div>
            <div id="divmsjalertaClaveAjustarCredito"></div>
        </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-4 sm:grid-cols-2">
            <button class="btnDialog btnDialog_secondary" type="button" value="salir">Salir</button>
            <input id="btnEditarCrearAjustarCredito" class="btnDialog btnDialog_primary" type="submit" value="Confirmar">
        </div>
    </form>
  </dialog><!--fin modal AjustarCredito-->
