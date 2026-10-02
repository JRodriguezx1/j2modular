<!-- MODAL OTROS PRODUCTOS -->
<dialog id="miDialogoOtrosProductos" class="midialog-md !max-w-4xl p-0 overflow-hidden">
  <div class="p-8 sm:p-10 lg:p-12">
    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-6 mb-7">
      <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 shadow-sm">
        <i class="fa-solid fa-box-open text-3xl"></i>
      </div>

      <div class="min-w-0">
        <p class="text-base font-bold uppercase tracking-wide text-indigo-600">Producto libre</p>
        <h4 class="text-3xl sm:text-4xl font-bold text-slate-900 leading-tight">
          Producto personalizado
        </h4>
        <p class="mt-2 text-lg text-slate-500">
          Agregue un producto o servicio que no exista en el cat&aacute;logo.
        </p>
      </div>
    </div>

    <form id="formOtrosProductos" class="formulario">
      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-7">
        <div class="mb-5 flex items-start gap-3 border-b border-slate-200 pb-4">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
            <i class="fa-solid fa-pen-to-square text-xl"></i>
          </div>
          <div>
            <h5 class="text-2xl font-bold text-slate-900">Datos del producto</h5>
            <p class="text-base text-slate-500">Ingrese el nombre, impuesto, cantidad y valor final a cobrar.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
          <div class="md:col-span-12">
            <label for="nombreotros" class="block text-lg font-semibold text-slate-700">Nombre</label>
            <input
              id="nombreotros"
              type="text"
              name="nombreotros"
              autocomplete="off"
              class="mt-2 block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              placeholder="Ej. Servicio t&eacute;cnico, transporte, instalaci&oacute;n..."
              required
            >
          </div>

          <div class="md:col-span-6">
            <label class="block text-lg font-semibold text-slate-700" for="impuesto">Impuesto</label>
            <select
              id="impuesto"
              name="porcentaje_de_impuesto"
              class="mt-2 block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            >
              <optgroup label="IVA">
                <option value="0">Exento - 0%</option>
                <option value="5">Bienes / Servicios al 5%</option>
                <option value="16">Contratos antes Ley 1819 - 16%</option>
                <option value="19">Tarifa general - 19%</option>
                <option value="">Excluido de IVA</option>
              </optgroup>
              <optgroup label="INC">
                <option value="8">Impuesto Nacional al Consumo - 8%</option>
              </optgroup>
            </select>
          </div>

          <div class="md:col-span-3">
            <label for="cantidadotros" class="block text-lg font-semibold text-slate-700">Cantidad</label>
            <input
              id="cantidadotros"
              type="number"
              name="cantidadotros"
              min="1"
              step="0.01"
              class="mt-2 block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              placeholder="1"
              required
            >
          </div>

          <div class="md:col-span-3">
            <label for="preciootros" class="block text-lg font-semibold text-slate-700">Precio total</label>
            <input
              id="preciootros"
              type="number"
              name="preciootros"
              min="0"
              class="mt-2 block h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-xl text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              placeholder="Valor"
              required
            >
            <p class="mt-2 text-sm text-slate-500">Valor final de la l&iacute;nea, antes de calcular la unidad.</p>
          </div>
        </div>
      </div>

      <div class="mt-7 grid grid-cols-2 gap-3 border-t border-slate-200 pt-5">
        <button class="btn-md btn-turquoise !m-0 !w-full !py-4" type="button" value="Cancelar">Cancelar</button>
        <input class="btn-md btn-indigo !m-0 !w-full !py-4" type="submit" value="Agregar">
      </div>
    </form>
  </div>
</dialog>