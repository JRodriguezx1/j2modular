<div class="box !pb-16">
    <!-- Botón atrás -->
    <a href="/admin/campanas" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2   ">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
    </a>

    <div class="p-6  rounded-2xl">
        <!-- Barra de progreso -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="step-circle w-10 h-10 rounded-full bg-indigo-600 text-white font-semibold flex items-center justify-center">1</div>
                <span class="text-sm font-medium text-gray-700">Origen</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">2</div>
                <span class="text-sm font-medium text-gray-500">Campaña</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">3</div>
                <span class="text-sm font-medium text-gray-500">Audiencia</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">4</div>
                <span class="text-sm font-medium text-gray-500">Mensaje</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="step-circle w-10 h-10 rounded-full border-2 border-gray-300 text-gray-400 font-semibold flex items-center justify-center">5</div>
                <span class="text-sm font-medium text-gray-500">Confirmación</span>
            </div>
        </div>

        <!-- Paso 1 -->
        <section class="space-y-4 step-section step-1">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 ">Número de origen</h2>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3     h-14 text-lg focus:outline-none focus:ring-1">
                <option value="">-- Selecciona --</option>
                <option value="+573135421983">Atención al cliente (+57 313 5421983)</option>
                <option value="+573001234567">Ventas (+57 300 1234567)</option>
            </select>
        </section>

        <!-- Paso 2 -->
        <section class="space-y-4 mt-8 step-section step-2 hidden">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 ">Nombre campaña</h2>
            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3     h-14 text-lg focus:outline-none focus:ring-1" placeholder="Ej: Promoción septiembre" />
        </section>

        <!-- Paso 3 -->
        <section class="space-y-4 mt-8 step-section step-3 hidden">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 ">Audiencia</h2>
            <div class="flex flex-col md:flex-row gap-4">
                <button type="button" class="px-6 py-3 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50 ">Seleccionar contactos</button>
                <button type="button" class="px-6 py-3 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50 ">Subir archivo</button>
            </div>
        </section>

        <!-- Paso 4 -->
        <section class="space-y-4 mt-8 step-section step-4 hidden">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 ">Mensaje</h2>
            <textarea rows="5" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3     text-lg focus:outline-none focus:ring-1"></textarea>
            <p class="text-sm text-gray-500 mt-2">Vista previa:</p>
            <div class="border rounded-lg p-3 bg-gray-50 text-gray-700  ">
                Aquí se mostrará la vista previa del mensaje.
            </div>
        </section>

        <!-- Paso 5 -->
        <section class="space-y-4 mt-8 step-section step-5 hidden">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 ">Confirmación</h2>
            <div class="border rounded-lg p-4  ">
                <p class="text-gray-700 ">Aquí se mostrarán los datos finales de la campaña antes de enviarla.</p>
            </div>
        </section>

        <!-- Navegación -->
        <div class="flex justify-between mt-6">
            <button id="btnAnteriorWizard" class="px-6 py-3 border rounded-lg hover:bg-gray-100  ">Anterior</button>
            <button id="btnSiguienteWizard" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Siguiente</button>
        </div>
    </div>
</div>

<script>
    const steps = document.querySelectorAll(".step-section");
    const stepCircles = document.querySelectorAll(".step-circle");
    let currentStep = 0;

    function showStep(i) {
        steps.forEach((s, idx) => s.classList.toggle("hidden", idx !== i));
        stepCircles.forEach((circle, idx) => {
            if (idx <= i) {
                circle.classList.remove("border-2", "border-gray-300", "text-gray-400");
                circle.classList.add("bg-indigo-600", "text-white");
            } else {
                circle.classList.remove("bg-indigo-600", "text-white");
                circle.classList.add("border-2", "border-gray-300", "text-gray-400");
            }
        });
    }

    document.getElementById("btnSiguienteWizard").addEventListener("click", () => {
        if (currentStep < steps.length - 1) currentStep++;
        showStep(currentStep);
    });

    document.getElementById("btnAnteriorWizard").addEventListener("click", () => {
        if (currentStep > 0) currentStep--;
        showStep(currentStep);
    });

    showStep(currentStep);
</script>
