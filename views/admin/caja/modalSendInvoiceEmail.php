<dialog id="miDialogoEnviarEmailCliente" class="w-[95%] max-w-lg overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 transition-all duration-300 ease-out open:scale-100 open:opacity-100">

    <div class="px-8 pb-8 pt-7 text-center">
        <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-lg shadow-indigo-600/10 ring-1 ring-indigo-100">
            <i class="fa-solid fa-envelope text-4xl"></i>
        </div>

        <h4 id="modalEnviarEmail" class="m-0 text-3xl font-bold leading-tight text-slate-900">Enviar por correo</h4>

        <p class="mx-auto mt-2 max-w-sm text-lg font-medium leading-7 text-slate-500">
            Envie el detalle de la orden al correo electronico del cliente.
        </p>
    </div>

    <div class="border-t border-slate-200 px-8 pb-8 pt-6">
        <div id="divmsjalertaEnviarEmail"></div>

        <form id="formEnviarEmailCliente" class="formulario" method="POST">

            <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-5">
                <div class="mb-3 flex items-center gap-3 text-left">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-indigo-600 shadow-sm">
                        <i class="fa-solid fa-at text-lg"></i>
                    </span>

                    <label
                        for="inputEmail"
                        class="m-0 block text-lg font-semibold text-slate-800">
                        Correo del cliente
                    </label>
                </div>

                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl bg-indigo-50 text-indigo-500">
                        <i class="fa-solid fa-envelope text-base"></i>
                    </span>

                    <input
                        id="inputEmail"
                        class="h-16 w-full rounded-2xl border border-slate-300 bg-white py-3 pl-16 pr-4 text-lg font-semibold text-slate-900 placeholder:font-medium placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        type="email"
                        placeholder="cliente@email.com"
                        required>
                </div>

                <p class="m-0 mt-3 text-left text-sm font-medium leading-6 text-slate-500">
                    Se enviara una copia de la orden a este correo.
                </p>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 border-t border-slate-200 pt-6">
                <button
                    class="inline-flex h-16 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-xl font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                    type="button"
                    value="Salir">
                    Cancelar
                </button>

                <button
                    id="btnEnviarEmailCliente"
                    class="inline-flex h-16 items-center justify-center rounded-xl bg-indigo-600 px-5 text-xl font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700"
                    type="submit">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    Enviar
                </button>
            </div>
        </form>
    </div>
</dialog>