<!-- MODAL PARA REDIMIR O CANJEAR PUNTOS -->
<dialog id="miDialogoRedimir" class="midialog-sm" aria-labelledby="tituloRedimir">
    <div class="max-h-[88vh] overflow-y-auto p-5 sm:p-8">
        <div class="mb-5 flex items-start gap-4 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-slate-50 p-5">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <i class="fa-solid fa-gift text-2xl"></i>
            </div>
            <div class="min-w-0">
                <h4 id="tituloRedimir" class="!m-0 text-3xl font-bold leading-tight text-slate-900">Redimir puntos</h4>
                <p class="!mb-0 mt-1 text-lg text-slate-500">Canjea tus puntos por productos o servicios de la tienda.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
            <div class="mb-4 flex items-start gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="!m-0 text-base font-semibold text-slate-500">Cliente</p>
                    <h5 id="clienteRedimir" class="!m-0 break-words text-xl font-bold text-slate-900">Cliente</h5>
                </div>
            </div>

            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                <p class="!m-0 text-lg font-semibold text-indigo-700">Puntos disponibles</p>
                <p id="viewPtsCli" class="!mb-0 mt-2 break-words text-3xl font-bold leading-tight text-slate-900"> - </p>
                <small id="valorTotalPts" class="mt-1 block text-base font-medium text-slate-600"> - </small>
            </div>

            <div class="mt-5 grid grid-cols-1 items-end gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                <div class="form-field">
                    <label for="inputCantidadPuntos">Puntos a redimir</label>
                    <div class="form-input">
                        <input
                            id="inputCantidadPuntos"
                            type="text"
                            name="cantidadPuntos"
                            autocomplete="off"
                            aria-describedby="ayudaValorPuntos"
                            placeholder="Ej: 350"
                            required
                            oninput="formatearMoneda(this)"
                        >
                    </div>
                </div>
                <button id="aplicarPtsFactura" type="button" class="btnDialog btnDialog_secondary !h-[4.6rem] w-full sm:w-auto">Aplicar a factura</button>
            </div>
            <p id="ayudaValorPuntos" class="!mb-0 mt-2 text-base text-slate-500">Valor equivalente: <small id="puntosValor" class="text-base font-semibold text-slate-700">$0</small></p>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-200 pt-4">
            <button type="button" class="btnDialog btnDialog_light salir !m-0 !w-full !text-xl !font-semibold">Cancelar</button>
            <input id="btnAplicarRedimir" type="submit" value="Redimir" class="redimir btnDialog btnDialog_primary !m-0 !w-full !text-xl !font-semibold">
        </div>
    </div>
</dialog>
