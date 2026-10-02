<div class="min-h-screen w-screen overflow-hidden bg-slate-100 px-5 py-5 md:px-8 md:py-8">
  <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full items-center justify-center" style="max-width: 1320px;">
    <div class="grid w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 lg:grid-cols-[1fr_1.05fr]">
      <section class="relative flex min-h-[18rem] flex-col justify-between overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-sky-500 px-6 py-6 text-white md:min-h-[22rem] md:px-10 md:py-8 lg:min-h-[72rem] lg:px-12 lg:py-12">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_20%,rgba(255,255,255,.22),transparent_32%),linear-gradient(135deg,transparent_0%,transparent_45%,rgba(255,255,255,.12)_45%,rgba(255,255,255,.12)_100%)]"></div>

        <div class="relative z-10">
          <img src="/build/img/Logoj2blanco.png" class="h-auto w-52 max-w-full md:w-80 lg:w-96" alt="Logo J2 Software POS">
        </div>

        <div class="relative z-10 mt-5 max-w-xl md:mt-8">
          <p class="mb-2 text-xs font-black uppercase tracking-[.2em] text-white/70 md:mb-3 md:text-sm lg:text-base">Sistema POS multisucursal</p>
          <h1 class="m-0 text-3xl font-bold leading-tight md:text-5xl lg:text-6xl">Ingresa a tu punto de venta</h1>
          <p class="mb-0 mt-3 text-base font-medium leading-snug text-white/85 md:mt-4 md:text-lg md:leading-relaxed lg:text-xl">
            Administra ventas, caja, reportes y operaciones desde una experiencia clara y segura.
          </p>
        </div>

        <div class="relative z-10 mt-5 hidden gap-3 text-base font-semibold text-white/90 sm:grid sm:grid-cols-3 md:mt-8 lg:grid-cols-1 lg:text-lg xl:grid-cols-3">
          <div class="rounded-2xl bg-white/12 px-4 py-3 ring-1 ring-white/15">
            <i class="fa-solid fa-store mr-2"></i>Multisucursal
          </div>
          <div class="rounded-2xl bg-white/12 px-4 py-3 ring-1 ring-white/15">
            <i class="fa-solid fa-cash-register mr-2"></i>Caja
          </div>
          <div class="rounded-2xl bg-white/12 px-4 py-3 ring-1 ring-white/15">
            <i class="fa-solid fa-chart-line mr-2"></i>Reportes
          </div>
        </div>
      </section>

      <section class="flex items-center justify-center px-6 py-6 md:px-10 md:py-8 lg:px-16">
        <div class="w-full max-w-xl lg:max-w-2xl">
          <?php include __DIR__. "/../templates/alertas.php"; ?>

          <div class="mb-6 md:mb-7">
            <p class="mb-2 text-base font-bold uppercase tracking-[.22em] text-indigo-600 lg:text-base">Acceso seguro</p>
            <h2 class="m-0 text-4xl font-bold text-slate-900 lg:text-5xl">Iniciar sesi&oacute;n</h2>
            <p class="mb-0 mt-2 text-lg font-medium text-slate-500 lg:text-xl">Selecciona tu sede e ingresa tus credenciales.</p>
          </div>

          <form action="/login" method="post" class="space-y-4 md:space-y-5">
            <div>
              <label for="nickname" class="mb-2 block text-lg font-bold text-slate-700 lg:text-lg">Usuario</label>
              <div class="flex h-[4.5rem] items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 transition focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-100 md:h-20 lg:h-24 lg:px-5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-indigo-600 md:h-12 md:w-12 lg:h-14 lg:w-14">
                  <i class="fa-solid fa-user"></i>
                </span>
                <input
                  id="nickname"
                  name="nickname"
                  type="text"
                  required
                  placeholder="Ingresa tu usuario"
                  class="h-full min-w-0 flex-1 border-0 bg-transparent text-lg font-semibold text-slate-900 outline-none placeholder:text-slate-400 lg:text-xl"
                >
              </div>
            </div>

            <div>
              <label for="sucursal" class="mb-2 block text-lg font-bold text-slate-700 lg:text-lg">Sede</label>
              <div class="flex h-[4.5rem] items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 transition focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-100 md:h-20 lg:h-24 lg:px-5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-indigo-600 md:h-12 md:w-12 lg:h-14 lg:w-14">
                  <i class="fa-solid fa-building"></i>
                </span>
                <select
                  id="sucursal"
                  name="idsucursal"
                  class="h-full min-w-0 flex-1 border-0 bg-transparent text-lg font-semibold text-slate-900 outline-none lg:text-xl"
                >
                  <?php foreach($sucursales as $value): ?>
                    <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between gap-3">
                <label for="password" class="block text-lg font-bold text-slate-700 lg:text-lg">Contrase&ntilde;a</label>
                <a href="/olvide" class="text-base font-bold text-indigo-600 transition hover:text-indigo-500 lg:text-lg">
                  &iquest;Olvidaste tu contrase&ntilde;a?
                </a>
              </div>

              <div class="flex h-[4.5rem] items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 transition focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-100 md:h-20 lg:h-24 lg:px-5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-indigo-600 md:h-12 md:w-12 lg:h-14 lg:w-14">
                  <i class="fa-solid fa-lock"></i>
                </span>
                <input
                  id="password"
                  name="password"
                  type="password"
                  required
                  placeholder="Ingresa tu contrase&ntilde;a"
                  class="h-full min-w-0 flex-1 border-0 bg-transparent text-lg font-semibold text-slate-900 outline-none placeholder:text-slate-400 lg:text-xl"
                >
              </div>
            </div>

            <button
              type="submit"
              class="inline-flex h-20 w-full items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-6 text-lg font-black uppercase tracking-wide text-white shadow-xl shadow-indigo-500/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 lg:h-24 lg:text-xl"
            >
              <i class="fa-solid fa-right-to-bracket"></i>
              Iniciar sesi&oacute;n
            </button>
          </form>

          <div class="mt-8 border-t border-slate-200 pt-7 text-center">
            <p class="m-0 text-base font-semibold text-slate-500 lg:text-lg">
              &iquest;No tienes una cuenta?
              <a href="/registro" class="font-black text-indigo-600 transition hover:text-indigo-500">Reg&iacute;strate</a>
            </p>

            <p class="mx-auto mb-0 mt-4 max-w-lg text-sm font-medium leading-relaxed text-slate-400 lg:text-base">
              Al iniciar sesi&oacute;n, aceptas nuestros
              <a href="#" class="font-bold text-indigo-600 transition hover:text-indigo-500">T&eacute;rminos</a>
              y
              <a href="#" class="font-bold text-indigo-600 transition hover:text-indigo-500">Pol&iacute;tica de privacidad</a>.
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
