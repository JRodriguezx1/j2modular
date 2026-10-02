<div class="crearseparado w-full p-3 pb-56 text-slate-900 md:p-0 md:pb-12">
  <?php include __DIR__. "/../../templates/alertas.php"; ?>

  <div class="mx-auto grid max-w-[150rem] gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50/60 via-white to-white p-4 shadow-sm md:p-6">
    <section class="grid grid-cols-1 items-center gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-4 md:grid-cols-[auto_minmax(0,1fr)_auto] md:p-6">
      <a href="/admin/creditos" class="inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-2xl text-white shadow-lg transition hover:-translate-y-0.5 hover:text-white" aria-label="Volver a creditos">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
      <div class="min-w-0">
        <p class="mb-1 text-base font-extrabold uppercase text-indigo-600">Cartera</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 md:text-4xl">Crear separado</h1>
        <p class="mt-1 max-w-3xl text-lg leading-snug text-slate-500">Selecciona cliente, productos, abono inicial y plazo para generar el separado.</p>
      </div>
      <div class="inline-flex w-full min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 md:w-auto md:min-w-80">
        <span class="material-symbols-outlined inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 text-3xl text-white">inventory_2</span>
        <div>
          <strong class="block text-2xl font-black leading-none text-slate-900">Nuevo</strong>
          <small class="mt-1 block text-base font-bold text-slate-500">registro de separado</small>
        </div>
      </div>
    </section>

    <div id="divmsjalerta"></div>

    <form id="formCrearUpdateCredito" class="flex flex-col tlg:flex-row gap-4" action="" method="POST">
      <section class="basis-1/2 overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 md:items-center">
          <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600">person_add</span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Datos del separado</h2>
            <p class="mt-1 text-base text-slate-500">Informacion del cliente, abono inicial y condiciones de pago.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 [@media(min-width:761px)]:grid-cols-2">
          <div class="form-field col-span-full">
            <label for="cliente">Cliente</label>
            <div class="form-input select2-field select2-field--with-icon select2-field--custom-placeholder" data-placeholder="Buscar cliente">
              <span><i class="fa-solid fa-magnifying-glass"></i></span>
              <select id="cliente" name="cliente_id" multiple="multiple" required>
                <?php foreach($clientes as $cliente): ?>
                  <?php if($cliente->id>1): ?>
                    <option value="<?php echo $cliente->id;?>"><?php echo $cliente->nombre.' '.$cliente->apellido;?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-field">
            <label for="abonoinicial">Abono inicial</label>
            <div class="form-input">
              <span><i class="fa-solid fa-dollar-sign"></i></span>
              <input
                id="abonoinicial"
                type="text"
                placeholder="Abono inicial al capital"
                name="abonoinicial"
                value="<?php echo $credito->abonoinicial??'0';?>"
                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/^(\.)/, ''); if(this.value === '')this.value = '';"
                required
              >
            </div>
          </div>

          <div class="form-field">
            <label for="cantidadcuotas">Cantidad de cuotas</label>
            <div class="form-input">
              <span><i class="fa-solid fa-calendar-check"></i></span>
              <input
                id="cantidadcuotas"
                type="text"
                placeholder="Cantidad de cuotas"
                name="cantidadcuotas"
                value="<?php echo $credito->cantidadcuotas??'1';?>"
                oninput="this.value = this.value.replace(/[,.]/g, '').replace(/\D/g, ''); if(this.value === '' || this.value === '0'){this.value = '';}"
                required
              >
            </div>
          </div>

          <div class="form-field">
            <label for="montocuota">Valor de la cuota</label>
            <div class="form-input">
              <span><i class="fa-solid fa-receipt"></i></span>
              <input id="montocuota" type="text" placeholder="Valor de la cuota" name="montocuota" value="<?php echo $credito->montocuota??'';?>" readonly required>
            </div>
          </div>

          <div class="form-field">
            <label for="frecuenciapago">Dia de pago</label>
            <div class="form-input select2-field select2-field--with-icon select2-field--custom-placeholder" data-placeholder="Seleccionar dia de pago">
              <span><i class="fa-solid fa-calendar-day"></i></span>
              <select id="frecuenciapago" name="frecuenciapago" multiple="multiple" required>
                <option></option>
                <?php for($dia = 1; $dia <= 30; $dia++): ?>
                  <option value="<?php echo $dia; ?>"><?php echo $dia; ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>

          <div class="form-field col-span-full">
            <label for="nota">Nota</label>
            <div class="form-input">
              <span><i class="fa-regular fa-note-sticky"></i></span>
              <input
                id="nota"
                type="text"
                placeholder="Nota del separado"
                name="nota"
                value="Plazo maximo de entrega: 1 mes"
              >
            </div>
          </div>
        </div>
      </section>

      <section class="basis-1/2 overflow-hidden rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:p-6">
        <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 md:items-center">
          <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600">shopping_bag</span>
          <div>
            <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Productos</h2>
            <p class="mt-1 text-base text-slate-500">Agrega los articulos que quedaran reservados en el separado.</p>
          </div>
        </div>

        <div class="form-field col-span-full">
          <label for="articulo">Articulo</label>
          <div class="form-input select2-field select2-field--with-icon select2-field--custom-placeholder" data-placeholder="Buscar articulo">
            <span><i class="fa-solid fa-magnifying-glass"></i></span>
            <select id="articulo" name="articulo" autocomplete="articulo-name" multiple="multiple" required></select>
          </div>
        </div>

        <div class="mt-3 rounded-lg border border-slate-200 bg-white overflow-x-auto md:overflow-x-visible">
          <table id="tablaventa" class="w-full border-separate border-spacing-0" width="100%">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th class="accionesth"><i class="fa-solid fa-x"></i></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <div id="carritoVacio" class="flex flex-col items-center justify-center gap-2 px-4 py-8 text-center text-slate-500 text-xl">
            <span class="material-symbols-outlined text-5xl text-slate-300">shopping_cart</span>
            <span>Sin productos en el carrito.</span>
          </div>
        </div>

        <div class="mt-[1.2rem] grid grid-cols-1 gap-4 [@media(min-width:761px)]:grid-cols-[minmax(16rem,.55fr)_minmax(28rem,1fr)]">
          <button id="btndescuento" class="inline-flex min-h-16 w-full items-center justify-center gap-3 rounded-lg border border-cyan-300 bg-cyan-50 px-5 text-lg font-extrabold text-cyan-700" type="button">
            <i class="fa-solid fa-tag"></i>
            Descuento
          </button>

          <div class="grid gap-2 rounded-lg border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
            <div class="flex items-center justify-between">
              <span class="text-base font-bold text-slate-500">Sub total</span>
              <strong id="subTotal" class="text-lg font-extrabold text-slate-900">$ 0</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-base font-bold text-slate-500">Impuesto</span>
              <strong id="impuesto" class="text-lg font-extrabold text-slate-900">% 0</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-base font-bold text-slate-500">Descuento</span>
              <strong id="descuento" class="text-lg font-extrabold text-slate-900">$ 0</strong>
            </div>
            <div class="flex items-center justify-between border-t border-slate-200 pt-2">
              <span class="text-base font-black text-indigo-600">Total</span>
              <strong id="total" class="text-xl font-black text-emerald-700">$ 0</strong>
            </div>
          </div>
        </div>

        <div class="mt-[1.2rem] grid grid-cols-1 gap-4 [@media(min-width:761px)]:grid-cols-2 [@media(max-width:760px)]:mb-[5.5rem]">
          <button class="btnDialog btnDialog_secondary" type="button" value="salir">
            Salir
          </button>
          <input id="btnCrearSeparado" class="btnDialog btnDialog_primary" type="submit" value="Crear">
        </div>
      </section>
    </form>
  </div>

  <dialog id="miDialogoDescuento" class="detalledialog_sm">
    <div class="flex items-center gap-4 border-b border-slate-200 bg-gradient-to-br from-indigo-50 to-cyan-50 p-6">
      <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-3xl text-indigo-600">sell</span>
      <div>
        <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Descuento</p>
        <h3 class="m-0 text-2xl font-extrabold text-slate-900">Aplicar descuento</h3>
        <small class="mt-1 block text-base text-slate-500">Define un valor o porcentaje para descontar del separado.</small>
      </div>
    </div>

    <form id="formDescuento" class="grid gap-4 p-6">
      <div class="separado-segmented">
        <label>
          <input type="radio" name="tipodescuento" value="valor" checked>
          <span>Valor</span>
        </label>
        <label>
          <input type="radio" name="tipodescuento" value="porcentaje">
          <span>Porcentaje</span>
        </label>
      </div>

      <div class="separado-field grid gap-2">
        <label for="inputDescuento">Descuento</label>
        <div class="separado-input">
          <span><i class="fa-solid fa-percent"></i></span>
          <input id="inputDescuento" type="number" min="0" name="descuento" data-descuento="" required>
        </div>
      </div>

      <div class="separado-field grid gap-2">
        <label for="inputDescuentoClave">Ingresar clave</label>
        <div class="separado-input">
          <span><i class="fa-solid fa-key"></i></span>
          <input id="inputDescuentoClave" type="password" name="descuentoclave">
        </div>
        <div id="divmsjalertaClaveDcto"></div>
      </div>

      <div class="grid grid-cols-1 gap-4 pt-[.4rem] [@media(min-width:761px)]:grid-cols-2">
        <button type="button" class="btnDialog btnDialog_secondary salir">Salir</button>
        <button id="btnCrearAddDir" type="submit" class="btnDialog btnDialog_primary">Aplicar</button>
      </div>
    </form>
  </dialog>

  <?php include __DIR__. "/../ventas/modalprocesarpago.php"; ?>

  <script>
    const getParam = <?= json_encode($conflocal) ?>;
  </script>
</div>
