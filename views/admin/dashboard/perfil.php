<div class="perfil">
  <div class="p-6 border border-slate-200 rounded-xl bg-gradient-to-b from-violet-50 via-white to-white to-10%">
    <header class="perfil-hero">
      <div>
        <p class="text-indigo-600 uppercase text-lg font-bold m-0">Mi cuenta</p>
        <h1 class="text-slate-900 text-4xl font-extrabold leading-8">Perfil de usuario</h1>
        <p class="text-slate-500 text-xl m-0 mt-2 leading-6">Actualiza tu informaci&oacute;n de contacto, correo electr&oacute;nico y contrase&ntilde;a de acceso.</p>
      </div>

      <div class="perfil-hero__identity">
        <div class="perfil-avatar">
          <span><?php echo strtoupper(substr($usuario->nombre ?? $user['nombre'] ?? 'U', 0, 1)); ?></span>
        </div>
        <div>
          <strong><?php echo $usuario->nombre ?? $user['nombre'] ?? 'Usuario'; ?></strong>
          <small><?php echo $usuario->email ?? 'No tiene email'; ?></small>
        </div>
      </div>
    </header>

    <?php include __DIR__. "/../../templates/alertas.php"; ?>

    <div class="perfil-grid">
      <aside class="perfil-summary">
        <div class="perfil-summary__avatar">
          <span><?php echo strtoupper(substr($usuario->nombre ?? $user['nombre'] ?? 'U', 0, 1)); ?></span>
        </div>

        <h2><?php echo $usuario->nombre ?? $user['nombre'] ?? 'Usuario'; ?></h2>
        <p>Datos principales de la cuenta activa.</p>

        <div class="perfil-summary__list">
          <div>
            <span><i class="fa-solid fa-envelope"></i></span>
            <div>
              <small>Correo</small>
              <strong><?php echo $usuario->email ?? 'No tiene email'; ?></strong>
            </div>
          </div>
          <div>
            <span><i class="fa-solid fa-mobile-screen-button"></i></span>
            <div>
              <small>Celular</small>
              <strong><?php echo $usuario->movil ?? 'Sin registrar'; ?></strong>
            </div>
          </div>
          <div>
            <span><i class="fa-solid fa-location-dot"></i></span>
            <div>
              <small>Ciudad</small>
              <strong><?php echo $usuario->ciudad ?? 'Sin registrar'; ?></strong>
            </div>
          </div>
        </div>
      </aside>

      <main class="grid gap-5">
        <section class="border border-slate-200 p-6 rounded-xl">
          <div class="flex items-center gap-4 border-b border-slate-200 mb-4 pb-4">
            <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium"><i class="fa-solid fa-at"></i></span>
            <div>
              <h2 class="text-slate-900 text-3xl font-bold">Correo electr&oacute;nico</h2>
              <p class="my-0 text-lg leading-snug text-slate-500">Este correo se usa para notificaciones y recuperaci&oacute;n de cuenta.</p>
            </div>
          </div>

          <div class="flex justify-between items-center border border-slate-200 p-4 rounded-xl bg-slate-50">
            <div>
              <small class="text-slate-500 leading-4 uppercase text-lg font-bold block">Correo actual</small>
              <strong class="text-slate-900 text-xl font-bold"><?php echo $usuario->email ?? 'No tiene email'; ?></strong>
            </div>
            <button id="btnCambiarEmail" class="btnDialog btnDialog_secondary" type="button">
              <i class="fa-solid fa-pen-to-square"></i>
              Cambiar
            </button>
          </div>
        </section>

        <form class="formulario" action="/admin/perfil" method="POST">
          <section class="border border-slate-200 p-6 rounded-xl">
            <div class="flex items-center gap-4 border-b border-slate-200 mb-4 pb-4">
              <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium"><i class="fa-solid fa-address-card"></i></span>
              <div>
                <h2 class="text-slate-900 text-3xl font-bold">Informaci&oacute;n personal</h2>
                <p class="my-0 text-lg leading-snug text-slate-500">Mant&eacute;n actualizados tus datos de contacto y ubicaci&oacute;n.</p>
              </div>
            </div>

            <div class="formulario--grid !p-0">
              <div class="form-field">
                <label for="movil">Celular actual</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-mobile-screen-button"></i></span>
                  <input id="movil" type="number" name="movil" placeholder="Tu celular" value="<?php echo $usuario->movil ?? ''; ?>" required>
                </div>
              </div>

              <div class="form-field">
                <label for="ciudad">Ciudad</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-city"></i></span>
                  <input id="ciudad" type="text" name="ciudad" placeholder="Ciudad de residencia" value="<?php echo $usuario->ciudad ?? ''; ?>" required>
                </div>
              </div>

              <div class="form-field col-span-full">
                <label for="direccion">Direcci&oacute;n</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-location-dot"></i></span>
                  <input id="direccion" type="text" name="direccion" placeholder="Direcci&oacute;n de residencia" value="<?php echo $usuario->direccion ?? ''; ?>" required>
                </div>
              </div>
            </div>
          </section>

          <section class="border border-slate-200 p-6 rounded-xl">
            <div class="flex items-center gap-4 border-b border-slate-200 mb-4 pb-4">
              <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium"><i class="fa-solid fa-lock"></i></span>
              <div>
                <h2 class="text-slate-900 text-3xl font-bold">Contrase&ntilde;a</h2>
                <p class="my-0 text-lg leading-snug text-slate-500">Ingresa tu contrase&ntilde;a actual para confirmar cambios.</p>
              </div>
            </div>

            <div class="formulario--grid !p-0">
              <div class="form-field">
                <label for="password">Contrase&ntilde;a actual</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-key"></i></span>
                  <input id="password" type="password" name="password" placeholder="***********" required>
                </div>
              </div>

              <div class="form-field">
                <label for="password2">Nueva contrase&ntilde;a</label>
                <div class="form-input">
                  <span><i class="fa-solid fa-shield-halved"></i></span>
                  <input id="password2" type="password" name="password2" placeholder="***********">
                </div>
              </div>
            </div>

            <div class="perfil-help">
              <i class="fa-solid fa-circle-info"></i>
              <p>No puedes recordar tu contrase&ntilde;a actual. <a href="#">Recuperar cuenta</a></p>
            </div>
          </section>

          <div class="text-right">
            <button class="btnDialog btnDialog_primary" type="submit">Guardar cambios</button>
          </div>
        </form>

        <section class="border border-red-200 p-6 rounded-xl">
          <div class="flex items-center gap-4 border-b border-slate-200 mb-4 pb-4">
            <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-red-50 text-3xl text-red-600 font-medium"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
              <h2 class="text-slate-900 text-3xl font-bold">Eliminar cuenta</h2>
              <p class="my-0 text-lg leading-snug text-slate-500">Borraremos completamente tus datos. No podr&aacute;s acceder a tu cuenta despu&eacute;s de esta acci&oacute;n.</p>
            </div>
          </div>

          <div class="perfil-danger">
            <span><i class="fa-solid fa-circle-exclamation"></i> Procede con precauci&oacute;n</span>
            <button class="perfil-danger__button" type="button">Continuar con la eliminaci&oacute;n</button>
          </div>
        </section>
      </main>
    </div>
  </div>

  <dialog class="detalledialog_sm" id="miDialogoUpEmail">
    <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
      <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100"><i class="fa-solid fa-envelope-open-text"></i></span>
      <div>
        <p class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Correo</p>
        <h4 id="modalUpEmail" class="text-slate-900 text-3xl leading-6 font-bold">Actualizar correo electr&oacute;nico</h4>
        <small class="mt-1 text-lg leading-snug text-slate-500">Registra el nuevo correo asociado a tu cuenta.</small>
      </div>
    </div>

    <div id="divmsjalerta1"></div>

    <form id="formUpEmail" class="formulario p-8" action="/admin/actualizaremail" method="POST">
      <div class="form-field">
        <label for="email">Email</label>
        <div class="form-input">
          <span><i class="fa-solid fa-at"></i></span>
          <input type="text" placeholder="Tu email actual" id="email" name="email" value="">
        </div>
      </div>

      <div class="formulario__contenedorBtns--gridfull">
        <button class="btnDialog btnDialog_light" type="button" value="salir">Salir</button>
        <input id="btnUpEmail" class="btnDialog btnDialog_primary" type="submit" value="Actualizar">
      </div>
    </form>
  </dialog>
</div>
