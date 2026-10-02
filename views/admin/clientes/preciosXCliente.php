<div class="box preciosXCliente !pb-12">
  <div class="mx-auto max-w-[128rem] rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <header class="mb-6 flex flex-col items-stretch gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-4 sm:p-6 lg:flex-row lg:items-center">
      <a href="/admin/clientes" class="inline-flex size-16 shrink-0 items-center justify-center self-start rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-xl text-white shadow-md transition hover:-translate-y-px hover:text-white hover:shadow-lg" title="Volver a clientes">
        <i class="fa-solid fa-arrow-left"></i>
      </a>

      <div class="min-w-0 flex-1">
        <p class="mb-1 text-base font-extrabold uppercase text-indigo-600">Precios personalizados</p>
        <h1 class="m-0 break-words text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl"><?php echo $cliente->nombre.' '.$cliente->apellido;?></h1>
        <p class="mt-1 text-lg leading-snug text-slate-500">Define precios especiales por producto para este cliente.</p>
      </div>

      <div class="flex min-w-0 shrink-0 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 lg:min-w-80">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-2xl text-white"><i class="fa-solid fa-user-tag"></i></span>
        <div>
          <strong class="block text-3xl font-black leading-none text-slate-900"><?php echo count($arrayPreciosPorCliente); ?></strong>
          <small class="mt-1 block text-base font-bold text-slate-500">precios activos</small>
        </div>
      </div>
    </header>

    <form id="formAddProducto" class="formulario grid gap-6" action="/" method="POST">
      <input id="idcliente" type="hidden" value="<?php echo $cliente->id;?>">

      <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-tags"></i></span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Asociar producto</h2>
            <p class="mt-1 text-base leading-snug text-slate-500">Selecciona un producto y registra el precio de venta especial.</p>
          </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,2fr)_minmax(20rem,1fr)]">
          <div class="form-field">
            <label for="productos">Producto</label>
            <div class="select2-field select2-field--standalone">
              <select id="productos" name="productos" autocomplete="productos-name" multiple="multiple" required>
                <?php foreach($productos as $value): ?>
                  <option
                    value="<?php echo $value->id;?>"
                    data-producto="<?php echo $value->nombre;?>"
                  >
                    <?php echo $value->nombre.', Unidad: '.$value->unidadmedida;?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-field">
            <label for="precioPersonalizado">Precio de venta</label>
            <div class="form-input">
              <span><i class="fa-solid fa-dollar-sign"></i></span>
              <input id="precioPersonalizado"
                     name="precioPersonalizado"
                     type="text"
                     autocomplete="precioPersonalizado ID"
                     maxlength="7"
                     inputmode="numeric"
                     oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                     placeholder="Ej: 25000"
                     required>
            </div>
          </div>
        </div>

        <div class="mt-5 flex flex-col-reverse gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
          <a href="/admin/clientes" class="btnDialog btnDialog_light salir w-full sm:w-auto">
            <i class="fa-solid fa-arrow-left"></i>
            Salir
          </a>
          <input id="btnCrearAddProducto" class="btnDialog btnDialog_primary crearAddSubproducto w-full sm:w-auto" type="submit" value="Asociar">
        </div>
      </section>

      <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
          <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-list-check"></i></span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Lista de precios personalizados</h2>
            <p class="mt-1 text-base leading-snug text-slate-500">Productos con precio especial asignado para este cliente.</p>
          </div>
        </div>

        <div class="listaProductos grid gap-3 empty:min-h-32 empty:rounded-lg empty:border empty:border-dashed empty:border-slate-300 empty:bg-slate-50">
          <?php foreach($arrayPreciosPorCliente as $value): ?>
            <div id="<?php echo $value->idproducto;?>" class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 shadow-sm" role="alert">
              <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-box"></i></span>
              <div class="min-w-0 flex-1">
                <strong class="block text-xl font-black leading-tight text-indigo-600">$<?php echo number_format($value->precioxcliente ?? 0, 0, ',', '.');?></strong>
                <p class="mt-1 break-words text-lg font-bold leading-snug text-slate-900"><?php echo $value->nombre;?></p>
              </div>
              <button type="button" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:border-rose-400 hover:bg-rose-100" title="Eliminar precio personalizado">
                <span id="<?php echo $value->idproducto;?>" class="material-symbols-outlined">cancel</span>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </form>
  </div>
</div>
