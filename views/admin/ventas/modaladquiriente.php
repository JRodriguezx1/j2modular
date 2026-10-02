<!-- MODAL DATOS DEL ADQUIRIENTE -->
<dialog id="miDialogoFacturarA" class="midialog-sm !p-0">
  <div class="p-7 md:p-9">
    <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
      <div class="flex items-center gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 shadow-sm">
          <span class="material-symbols-outlined text-[3rem] leading-none">person</span>
        </div>
        <div class="min-w-0">
          <p class="mb-1 text-sm font-bold uppercase tracking-[.18em] text-indigo-600">Facturaci&oacute;n electr&oacute;nica</p>
          <h2 class="m-0 text-3xl font-bold leading-tight text-slate-900">Datos del adquiriente</h2>
          <p class="mt-1 text-lg text-slate-500">Complete los datos requeridos para emitir la factura.</p>
        </div>
      </div>
    </div>

    <div id="divmsjalertanoclienteDian"></div>

    <form id="formFacturarA" class="formulario">
      <div class="border-b border-slate-200 pb-6">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <div>
            <label for="type_document_identification_id" class="mb-2 block text-lg font-semibold text-slate-700">Tipo documento</label>
            <select id="type_document_identification_id" name="type_document_identification_id" required
              class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
              <option value="" disabled selected>Seleccione tipo de documento</option>
              <option value="1">Registro civil</option>
              <option value="2">Tarjeta de identidad</option>
              <option value="3">C&eacute;dula de ciudadan&iacute;a</option>
              <option value="4">Tarjeta de extranjer&iacute;a</option>
              <option value="5">C&eacute;dula de extranjer&iacute;a</option>
              <option value="6">NIT</option>
              <option value="7">Pasaporte</option>
              <option value="8">Documento de identificaci&oacute;n extranjero</option>
              <option value="9">NIT de otro pa&iacute;s</option>
              <option value="10">NUIP *</option>
              <option value="11">PEP (Permiso Especial de Permanencia)</option>
              <option value="12">PPT (Permiso Protecci&oacute;n Temporal)</option>
            </select>
          </div>

          <div>
            <label for="identification_number" class="mb-2 block text-lg font-semibold text-slate-700">N&uacute;mero de documento</label>
            <div class="flex gap-2">
              <input id="identification_number" type="text" name="identification_number" required
                class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition h-14 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                placeholder="Sin d&iacute;gito de verificaci&oacute;n"
                oninput="this.value = this.value.replace(/[^0-9]/g, '');">

              <button id="btnBuscarAdquiriente" type="button"
                class="inline-flex h-14 shrink-0 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-white px-4 text-lg font-semibold text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <span class="material-symbols-outlined text-2xl">search</span>
                <span class="hidden sm:inline">Buscar</span>
              </button>
            </div>
          </div>

          <div class="md:col-span-2">
            <label for="business_name" class="mb-2 block text-lg font-semibold text-slate-700">Nombre o raz&oacute;n social</label>
            <input id="business_name" type="text" name="business_name" required
              class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              placeholder="Nombre del cliente o empresa">
          </div>

          <div class="md:col-span-2">
            <label for="email" class="mb-2 block text-lg font-semibold text-slate-700">Correo electr&oacute;nico</label>
            <input id="email" type="email" name="email"
              class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              placeholder="correo@ejemplo.com">
          </div>
        </div>
      </div>

      <div class="accordion !mt-5">
        <input id="toggleOpcionesAdq" type="checkbox" class="peer sr-only">

        <label for="toggleOpcionesAdq" class="mx-auto flex w-full max-w-sm cursor-pointer items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-lg font-semibold text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 select-none">
          <span>Opciones adicionales</span>
          <span class="material-symbols-outlined text-2xl peer-checked:hidden">expand_more</span>
          <span class="material-symbols-outlined hidden text-2xl peer-checked:inline">expand_less</span>
        </label>

        <div class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out peer-checked:max-h-[120rem]">
          <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
              <label for="address" class="mb-2 block text-lg font-semibold text-slate-700">Direcci&oacute;n</label>
              <input id="address" type="text" name="address" class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Direcci&oacute;n del adquiriente">
            </div>

            <div>
              <label for="department_id" class="mb-2 block text-lg font-semibold text-slate-700">Departamento</label>
              <select id="department_id" name="department_id"
                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                <option value="" disabled selected>Seleccionar departamento</option>
                <?php foreach($departments as $value): ?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->name;?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="municipality_id" class="mb-2 block text-lg font-semibold text-slate-700">Ciudad / municipio</label>
              <select id="municipality_id" name="municipality_id"
                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                <option value="" disabled selected>Seleccionar ciudad o municipio</option>
              </select>
            </div>

            <div>
              <label for="type_organization_id" class="mb-2 block text-lg font-semibold text-slate-700">Tipo de organizaci&oacute;n</label>
              <select id="type_organization_id" name="type_organization_id"
                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                <option value="" disabled selected>Seleccione tipo de organizaci&oacute;n</option>
                <option value="1">Persona Jur&iacute;dica y asimiladas</option>
                <option value="2">Persona Natural y asimiladas</option>
              </select>
            </div>

            <div>
              <label for="type_regime_id" class="mb-2 block text-lg font-semibold text-slate-700">Tipo r&eacute;gimen</label>
              <select id="type_regime_id" name="type_regime_id"
                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                <option value="" disabled selected>Seleccione tipo de r&eacute;gimen</option>
                <option value="1">Responsable de IVA</option>
                <option value="2">No responsable de IVA</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label for="phone" class="mb-2 block text-lg font-semibold text-slate-700">Tel&eacute;fono</label>
              <input id="phone" type="tel" name="phone"
                class="block h-14 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                placeholder="Tel&eacute;fono de contacto">
            </div>
          </div>
        </div>

        <div class="mt-7 grid grid-cols-2 gap-3 border-t border-slate-200 pt-5">
          <button class="btn-md btn-turquoise !m-0 !w-full !py-4 salir" type="button" value="Cancelar">Cancelar</button>
          <input class="btn-md btn-indigo !m-0 !w-full !py-4" type="submit" value="Confirmar">
        </div>
      </div>
    </form>
  </div>
</dialog>