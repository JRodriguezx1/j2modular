<dialog id="miDialogoUpdateTarifa" class="midialog-sm !p-12">
    <div class="flex items-start gap-3 mb-4">
      <div class="text-indigo-600 mt-1"><i class="fa-solid fa-car text-[4rem] leading-[2.5rem]"></i></div>
      <div>
        <h2 class="text-3xl font-semibold text-gray-900">Tarifas de Vehiculos</h2>
        <p class="text-lg text-gray-600">Ingresar valor de la tarifa segun vehiculo</p>
      </div>
    </div>

    <div id="divmsjalertaTipoVehiculo"></div>

    <!-- <h4 class="text-gray-700 font-semibold">Facturar A:</h4> -->
    <form id="formTipoVehiculo" class="formulario">
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 border-b border-gray-900/10 pb-10 mb-3">
          <!-- Tipo de vehiculo -->
          <div>
            <label for="type_car" class="block text-2xl font-medium text-gray-600">Tipo Vehiculo</label>
            <div class="mt-2">
              <select id="type_car" name="type_car" required
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                <option value="" disabled selected>Seleccione tipo de vehiculo</option>
                <option value="1">Vehiculo pequño</option>
                <option value="2">Moto</option>
                <option value="3">Camion</option>
                <option value="4">Bus</option>
                <option value="5">Tractor</option>
                <option value="6">Otro</option>
              </select>
            </div>
          </div>

          <!-- tarifa por hora -->
          <div>
            <label for="tarifa_hora" class="block text-2xl font-medium text-gray-600">Tarifa por hora</label>
            <div class="mt-2">
              <input id="tarifa_hora" type="text" name="tarifa_hora" oninput="formatearMoneda(this)" required
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1"
                placeholder="Ingresar valor tarifa por hora">
            </div>
          </div>

           <!-- tarifa por dia -->
          <div>
            <label for="tarifa_dia" class="block text-2xl font-medium text-gray-600">Tarifa por dia</label>
            <div class="mt-2">
              <input id="tarifa_dia" type="text" name="tarifa_dia" oninput="formatearMoneda(this)" required
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5     h-14 text-xl focus:outline-none focus:ring-1"
                placeholder="Ingresar Valor tarifa por dia">
            </div>
          </div>
        </div>
      
        <!-- Botones -->
        <div class="text-right mt-6">
            <button class="btn-md btn-turquoise !py-4 !px-6 !w-[125px] md:!w-[145px] !mr-3" type="button" value="Cancelar">Cancelar</button>
            <input class="btn-md btn-indigo !mb-4 !py-4 px-6 !w-[125px] md:!w-[145px]" type="submit" value="Confirmar">
        </div>
    </form>
</dialog>