<?php
  $valorPlan = (float)($negocio->valorplan ?? 0);
  $descuento = (float)($negocio->descuento ?? 0);
  $cargo = (float)($negocio->cargo ?? 0);
  $totalSuscripcion = $valorPlan + $cargo - $descuento;
  $estadoActivo = (int)($negocio->estado ?? 0) === 1;
?>

<div class="contenido10 configsuscripcion accordion_tab_content grid min-w-0 w-full gap-6 rounded-lg border border-gray-200 p-6">

  <section class="flex items-center flex-wrap border border-slate-200 rounded-xl p-4 shadow-lg bg-gradient-to-br from-indigo-600/10 to-cyan-400/5">
    <div class="flex items-center gap-4 flex-1">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium"><i class="fa-solid fa-id-card"></i></span>
      <div>
        <small class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">SUSCRIPCION</small>
        <h2 class="m-0 break-words text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Control de suscripcion</h2>
        <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Consulta el estado de la cuenta, cobros vigentes e historial de pagos.</p>
      </div>
    </div>
    <div class="flex flex-wrap w-full md:w-auto justify-center gap-3">
      <button id="btnDetalleSuscriptor" class="btnDialog btnDialog_light" type="button">
        <i class="fa-solid fa-user-gear"></i>
        Informacion del suscriptor
      </button>
      <button id="btnRegistrarPago" class="btnDialog btnDialog_primary" type="button">
        <i class="fa-solid fa-receipt"></i>
        Registrar pago
      </button>
    </div>
  </section>

  <section class="grid grid-cols-2 xl:grid-cols-3 gap-4">
    <article class="border border-slate-200 bg-white rounded-xl p-4 xl:col-span-2">
      <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-200">
        <div>
          <span class="block text-lg text-slate-600 font-semibold">Cuenta</span>
          <h3 class="font-bold text-xl text-slate-900 mt-1"><?php echo $negocio->negocio ?? ''; ?></h3>
          <p class="m-0 text-lg text-slate-500"><?php echo $negocio->nombre ?? ''; ?></p>
        </div>
        <span id="estadoText" class="rounded-full border px-3 py-1 text-lg font-semibold <?php echo $estadoActivo ? 'border-green-300 bg-green-100 text-green-700' : 'border-red-300 bg-red-100 text-red-700'; ?>">
          <?php echo $estadoActivo ? 'Activo' : 'Suspendido'; ?>
        </span>
      </div>

      <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
          <span class="block text-slate-600 font-semibold text-lg">Fecha de inicio</span>
          <strong class="block text-slate-900 font-semibold text-xl"><?php echo $negocio->created_at ?? ''; ?></strong>
        </div>
        <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
          <span class="block text-slate-600 font-semibold text-lg">Proximo pago</span>
          <strong class="block text-slate-900 font-semibold text-xl" id="fecha_corteText"><?php echo $negocio->fecha_corte ?? ''; ?></strong>
        </div>
        <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
          <span class="block text-slate-600 font-semibold text-lg">Monto mensual</span>
          <strong class="block text-slate-900 font-semibold text-xl" id="valorplanText">$<?php echo number_format($valorPlan, 0, ',', '.'); ?></strong>
        </div>
        <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
          <span class="block text-slate-600 font-semibold text-lg">Dias restantes</span>
          <strong class="block text-slate-900 font-semibold text-xl" id="diasRestantesText">-</strong>
        </div>
      </div>
    </article>

    <article class="border border-slate-200 bg-white rounded-xl p-4 xl:col-span-1">
      <div class="flex items-center text-slate-900 font-bold gap-3 mb-4">
        <i class="fa-solid fa-calculator flex justify-center items-center size-12 rounded-lg bg-indigo-50 text-indigo-600"></i>
        Resumen de cobros
      </div>
      <div class="">
        <div class="flex items-center justify-between">
          <span class="text-slate-900 font-normal text-lg">Valor base</span>
          <strong id="valorplanResumen" class="text-slate-900 font-bold text-lg">$<?php echo number_format($valorPlan, 0, ',', '.'); ?></strong>
        </div>
        <div class="flex items-center justify-between">
          <span class="font-normal text-lg text-green-600">Descuento aplicado</span>
          <strong class="font-bold text-lg text-green-600">- $<?php echo number_format($descuento, 0, ',', '.'); ?></strong>
        </div>
        <div class="flex items-center justify-between">
          <span class="font-normal text-lg text-red-600">Cargo adicional</span>
          <strong class="font-bold text-lg text-red-600">+ $<?php echo number_format($cargo, 0, ',', '.'); ?></strong>
        </div>
      </div>
      <div class="flex items-center justify-between border-t border-slate-200 mt-4 pt-4">
        <span class="text-slate-900 font-bold text-lg">Total a pagar</span>
        <strong class="font-bold text-3xl text-indigo-700" id="totalSuscripcionText">$<?php echo number_format($totalSuscripcion, 0, ',', '.'); ?></strong>
      </div>
    </article>
  </section>

  <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
    <div class="flex items-center justify-between p-4">
      <div class="flex items-center gap-3">
        <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600 font-medium"><i class="fa-solid fa-clock-rotate-left"></i></span>
        <div>
          <h3 class="m-0 break-words text-2xl font-extrabold leading-tight text-slate-900">Historial de pagos</h3>
          <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Pagos registrados para la suscripcion actual.</p>
        </div>
      </div>
      <small class="text-base font-bold rounded-full py-1 px-3 text-indigo-600 border border-indigo-200 bg-indigo-50"><?php echo count($suscripcionPagos ?? []); ?> registro<?php echo count($suscripcionPagos ?? []) === 1 ? '' : 's'; ?></small>
    </div>

    <div class="w-full min-w-0 max-w-full overflow-x-auto">
      <table class="datatable-table tabla">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Metodo</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($suscripcionPagos as $value): ?>
            <tr>
              <td><?php echo $value->fecha_pago; ?></td>
              <td><span class="table-badge table-badge--info">$<?php echo number_format($value->valor_pagado, 0, ',', '.'); ?></span></td>
              <td><?php echo $value->mediopago; ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($suscripcionPagos)): ?>
            <tr>
              <td colspan="3" class="text-center !text-slate-500">Sin pagos registrados.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <dialog id="miDialogoDetalleSuscripcion" class="detalledialog_md">
    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-xl bg-white text-2xl text-indigo-600 font-medium border border-indigo-100"><i class="fa-solid fa-id-card"></i></span>
      <div>
        <small class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">SUSCRIPCION</small>
        <h4 class="text-slate-900 text-3xl leading-6 font-bold">Detalle de la suscripcion</h4>
        <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Actualiza el plan, estado y fecha de corte de la cuenta.</p>
      </div>
      <button class="btndialog__close btnXCerrarRegistroPago" type="button" aria-label="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="formDetalleSuscripcion" class="formulario--grid">
        <label class="formulario__campo">
          <span class="formulario__label">Estado</span>
          <select id="estado" class="formulario__select" name="estado" required>
            <option value="1" <?php echo $estadoActivo ? 'selected' : ''; ?>>Activa</option>
            <option value="0" <?php echo !$estadoActivo ? 'selected' : ''; ?>>Suspendida</option>
          </select>
        </label>

        <label class="formulario__campo">
          <span class="formulario__label">Plan</span>
          <select id="idplan" class="formulario__select" name="idplan" required>
            <option value="2" <?php echo ($negocio->idplan ?? '') == 2 ? 'selected' : ''; ?>>Plan mensual</option>
            <option value="1" <?php echo ($negocio->idplan ?? '') == 1 ? 'selected' : ''; ?>>Plan anual</option>
            <option value="3" <?php echo ($negocio->idplan ?? '') == 3 ? 'selected' : ''; ?>>Plan diario</option>
          </select>
        </label>

        <label class="formulario__campo">
          <span class="formulario__label">Fecha de corte</span>
          <input id="fecha_corte" class="formulario__input" type="date" name="fecha_corte" value="<?php echo $negocio->fecha_corte; ?>" min="<?php echo date('Y-m-d');?>" required>
        </label>

        <label class="formulario__campo">
          <span class="formulario__label">Valor del plan</span>
          <input id="valorplan" class="formulario__input" type="text" placeholder="Ingresa el monto" name="valorplan" value="<?php echo $negocio->valorplan; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
        </label>

      <div class="formulario__contenedorBtns--gridfull col-span-full">
        <button type="button" value="Cancelar" class="btnDialog btnDialog_light">Cancelar</button>
        <input id="btnEnviarDetalleSuscripcion" type="submit" value="Aplicar" class="btnDialog btnDialog_primary">
      </div>
    </form>
  </dialog>

  <dialog id="miDialogoRegistrarPago" class="detalledialog_md">
    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
      <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-xl bg-white text-2xl text-indigo-600 font-medium border border-indigo-100"><i class="fa-solid fa-receipt"></i></span>
      <div>
        <small class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">PAGO</small>
        <h4 class="text-slate-900 text-3xl leading-6 font-bold">Registrar pago</h4>
        <p class="mt-1 mb-0 text-lg leading-snug text-slate-500">Guarda el pago recibido y actualiza la suscripcion.</p>
      </div>
      <button class="btndialog__close btnXCerrarRegistroPago" type="button" aria-label="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="formRegistrarPago" class="formulario--grid">
        <label class="formulario__campo">
          <span class="formulario__label">Valor a registrar</span>
          <input id="valor_pagado" class="formulario__input" type="text" placeholder="Ingresa el monto" name="valor_pagado" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
        </label>

        <label class="formulario__campo">
          <span class="formulario__label">Cantidad del plan pago</span>
          <input id="cantidad_plan" class="formulario__input" type="text" placeholder="Renovaciones del plan" name="cantidad_plan" value="1" oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, ''); if(this.value === '' || this.value === '0'){this.value = '';}" required>
        </label>

        <label class="formulario__campo col-span-full">
          <span class="formulario__label">Medio de pago</span>
          <input id="medio_pago" class="formulario__input" type="text" placeholder="Descripcion del medio de pago" name="medio_pago" value="" required>
        </label>

        <label class="formulario__campo col-span-full">
          <span class="formulario__label">Observacion</span>
          <textarea id="descripcion" class="formulario__textarea formulario__textarea--textarea" name="descripcion" rows="4"></textarea>
        </label>

      <div class="formulario__contenedorBtns--gridfull col-span-full">
        <button type="button" value="Cancelar" class="btnDialog btnDialog_light">Cancelar</button>
        <input id="btnEnviarRegistrarPago" type="submit" value="Aplicar" class="btnDialog btnDialog_primary">
      </div>
    </form>
  </dialog>
</div>
