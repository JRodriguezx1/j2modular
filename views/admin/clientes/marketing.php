<?php
// ejemplo: marketing.php (parte relevante)
// $campanias vendrían de tu BD; esto es solo demo
$campanias = [
  ["nombre" => "Promo Octubre", "canal" => "Email", "segmento" => "Todos", "estado" => "Enviada", "fecha" => "10/10/2025 3:00 PM"],
  ["nombre" => "Black Friday", "canal" => "SMS", "segmento" => "Sucursal A", "estado" => "Programada", "fecha" => "25/11/2025 9:00 AM"]
];
?>

<div class="p-6 bg-white rounded-lg shadow-md">
  <div class="box ventasxtransaccion">
  <a href="/admin/clientes" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2    mb-6">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  <!-- Encabezado -->
  <div class="flex items-center justify-between mb-6">
    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">Gestión de Campañas de Marketing</h3>

    <!-- Botón Crear Campaña (TYPE=button para evitar submit inesperado) -->
    <a id="btnNuevaCampania" href="/admin/clientes/marketing/crearcampania" class="text-white bg-gradient-to-br from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300  font-semibold rounded-lg text-lg px-6 py-3 text-center shadow-lg transition transform hover:scale-105">
      ➕ Crear Campaña
    </a>
  </div>

  <!-- Tabla -->
  <div class="mt-6 overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-lg">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Nombre</th>
          <th class="px-4 py-2 text-left">Canal</th>
          <th class="px-4 py-2 text-left">Segmento</th>
          <th class="px-4 py-2 text-left">Estado</th>
          <th class="px-4 py-2 text-left">Fecha envío</th>
          <th class="px-4 py-3 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($campanias as $c): ?>
          <tr class="border-t hover:bg-gray-50  odd:bg-gray-50 even:bg-white  ">
            <td class="px-4 py-3 font-medium text-gray-900 "><?= htmlspecialchars($c["nombre"]) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($c["canal"]) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($c["segmento"]) ?></td>
            <td class="px-4 py-3 font-semibold <?= $c["estado"] === "Enviada" ? 'text-green-600' : 'text-yellow-600' ?>">
              <?= htmlspecialchars($c["estado"]) ?>
            </td>
            <td class="px-4 py-3"><?= htmlspecialchars($c["fecha"]) ?></td>
            <td class="px-4 py-3 text-center">
              <button type="button" class="text-indigo-600 hover:underline font-medium">Ver</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Crear Campaña -->
 <!--
<div id="modalCampania" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-8 relative">-->
    <!-- Botón cerrar (opcional) -->
     <!--
    <button id="btnCerrarModal" type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800">
      ✕
    </button>

    <h3 class="text-xl font-bold text-gray-800 mb-4">Nueva Campaña</h3>
    <form class="space-y-4" method="POST" action="guardar_campania.php">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Nombre de campaña</label>
        <input type="text" name="nombre" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Canal</label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2"><input type="checkbox" name="canal[]" value="SMS"> SMS</label>
          <label class="flex items-center gap-2"><input type="checkbox" name="canal[]" value="Email"> Email</label>
        </div>
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Segmento</label>
        <select name="segmento" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
          <option>Todos los clientes</option>
          <option>Por sucursal</option>
          <option>Por ciudad</option>
          <option>Por historial de compras</option>
        </select>
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Mensaje</label>
        <textarea name="mensaje" rows="4" maxlength="1000" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" required></textarea>
        <p class="text-xs text-gray-500 mt-1">Máx. 160 caracteres para SMS</p>
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Programar envío</label>
        <input type="datetime-local" name="programacion" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button id="btnCancelar" type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Cancelar</button>
        <button type="submit" class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-lg">Guardar</button>
      </div>
    </form>
  </div>
</div>-->

<!-- JS para abrir/cerrar modal (seguro: se ejecuta cuando el DOM esté cargado) -->
<script>
  /*
  document.addEventListener('DOMContentLoaded', function () {
    const btnNueva = document.getElementById('btnNuevaCampania');
    const modal = document.getElementById('modalCampania');
    const btnCancelar = document.getElementById('btnCancelar');
    const btnCerrar = document.getElementById('btnCerrarModal');

    if (!btnNueva || !modal) {
      console.warn('Elemento modal o botón Crear Campaña no encontrado en el DOM.');
      return;
    }

    function openModal() {
      modal.classList.remove('hidden');
      // opcional: prevenir scroll de fondo
      document.body.style.overflow = 'hidden';
      // mover foco al primer input
      const primerInput = modal.querySelector('input, textarea, select, button');
      if (primerInput) primerInput.focus();
    }

    function closeModal() {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    btnNueva.addEventListener('click', function (e) {
      e.preventDefault(); // por si acaso
      openModal();
    });

    if (btnCancelar) btnCancelar.addEventListener('click', closeModal);
    if (btnCerrar) btnCerrar.addEventListener('click', closeModal);

    // Cerrar cuando se hace click fuera del cuadro (sobre el overlay)
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeModal();
      }
    });
  });*/
</script>
