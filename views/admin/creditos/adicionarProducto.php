<div class="adicionarProducto w-full p-3 pb-24 text-slate-900 md:p-0 md:pb-12">
  <div class="mx-auto grid max-w-[150rem] gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50/60 via-white to-white p-4 shadow-sm md:p-6">
    <section class="grid grid-cols-1 items-center gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 md:grid-cols-[auto_minmax(0,1fr)_auto] md:p-6">
      <a href="/admin/creditos/detallecredito?id=<?php echo $credito->id;?>" class="inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-2xl text-white shadow-lg transition hover:-translate-y-0.5 hover:text-white" aria-label="Volver al detalle">
        <i class="fa-solid fa-arrow-left"></i>
      </a>

      <div class="min-w-0">
        <p class="mb-1 text-base font-extrabold uppercase text-indigo-600">Cartera</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 md:text-4xl">Adicionar productos</h1>
        <p class="mt-1 text-lg leading-snug text-slate-500">Agrega articulos al credito o separado y actualiza los valores asociados.</p>
      </div>

      <div class="inline-flex w-full min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 md:w-auto md:min-w-80">
        <span class="inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 text-3xl text-white"><i class="fa-solid fa-file-invoice-dollar"></i></span>
        <div>
          <strong id="numOrden" class="block text-2xl font-black leading-none text-slate-900"><?php echo $credito->id;?></strong>
          <small class="mt-1 block text-base font-bold text-slate-500">credito actual</small>
        </div>
      </div>
    </section>

    <section class="grid items-start gap-6 lg:grid-cols-[minmax(32rem,.75fr)_minmax(0,1.25fr)]">
      <article class="overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <header class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 md:items-center">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-cart-plus"></i></span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Nuevo articulo</h2>
            <p class="mt-1 text-base text-slate-500">Selecciona el producto, confirma cantidad y unidad para agregarlo al separado.</p>
          </div>
        </header>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
          <div class="form-field col-span-full">
            <label for="articulo">Articulo</label>
            <div class="form-input select2-field select2-field--with-icon select2-field--custom-placeholder" data-placeholder="Buscar articulo">
              <span><i class="fa-solid fa-magnifying-glass"></i></span>
              <select id="articulo" multiple="multiple">
                <?php foreach($totalitems as $value): ?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-field">
            <label for="cantidad">Cantidad</label>
            <div class="form-input">
              <span><i class="fa-solid fa-hashtag"></i></span>
              <input id="cantidad" type="text" placeholder="Cantidad" value="1"
                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '0';">
            </div>
          </div>

          <div class="form-field">
            <label for="unidadmedida">Unidad</label>
            <div class="form-input">
              <span><i class="fa-solid fa-ruler-combined"></i></span>
              <input id="unidadmedida" type="text" placeholder="Unidad de medida" readonly>
            </div>
          </div>
        </div>

        <button id="btnAddItem" class="btnDialog btnDialog_secondary w-full" type="button">
          <i class="fa-solid fa-plus"></i>
          Agregar articulo
        </button>

        <div class="mt-4 grid gap-2 rounded-lg border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
          <div class="flex items-center justify-between">
            <span class="text-base font-bold text-slate-500">Sub total</span>
            <strong id="subTotal" class="text-lg font-extrabold text-slate-900">$ <?php echo number_format($credito->capital, 2, ',', '.');?></strong>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-base font-bold text-slate-500">Recargo interes</span>
            <strong id="interes" class="text-lg font-extrabold text-slate-900">$ <?php echo number_format($credito->interes, 2, ',', '.');?></strong>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-base font-bold text-slate-500">Impuesto</span>
            <strong id="impuesto" class="text-lg font-extrabold text-slate-900">$ <?php echo number_format($credito->valorimpuestototal,2, ',', '.');?></strong>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-base font-bold text-slate-500">Abono inicial</span>
            <strong id="abonoinicial" class="text-lg font-extrabold text-slate-900">$ <?php echo number_format($credito->abonoinicial,2, ',', '.');?></strong>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-base font-bold text-slate-500">Descuento</span>
            <strong id="descuento" class="text-lg font-extrabold text-slate-900">$ <?php echo number_format($credito->descuento,2, ',', '.');?></strong>
          </div>
          <div class="flex items-center justify-between border-t border-slate-200 pt-3">
            <span class="text-base font-black text-indigo-600">Total</span>
            <strong id="total" class="text-3xl font-black text-emerald-700">$ <?php echo number_format($credito->montototal,2, ',', '.');?></strong>
          </div>
        </div>
      </article>

      <article class="overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <header class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 md:items-center">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-boxes-stacked"></i></span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Productos del credito</h2>
            <p class="mt-1 text-base text-slate-500">Revisa los articulos incluidos antes de guardar los cambios.</p>
          </div>
        </header>

        <div class="adicionar-producto-table-wrap">
          <table id="tablaItems" class="adicionar-producto-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Valor</th>
                <th>Total</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <footer class="mt-[1.2rem] grid grid-cols-1 gap-4 [@media(min-width:761px)]:grid-cols-2">
          <a href="/admin/creditos/detallecredito?id=<?php echo $credito->id;?>" class="btnDialog btnDialog_secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Salir
          </a>
          <button id="btnUpdateCreditoSeparado" class="btnDialog btnDialog_primary" type="button">
            <i class="fa-solid fa-floppy-disk"></i>
            Actualizar
          </button>
        </footer>
      </article>
    </section>
  </div>
</div>
