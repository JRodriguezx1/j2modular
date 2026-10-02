<div class="h-full overflow-hidden modorapido">
  <div class="h-full flex bg-gray-100 overflow-hidden">

    <!-- ================= COLUMNA IZQUIERDA ================= -->
    <div class="flex-1 flex flex-col overflow-hidden">

      <!-- HEADER -->
      <header class="bg-indigo-600 text-white px-6 py-3 mb-2 flex justify-between items-center flex-none rounded">
        <h1 class="text-xl font-semibold">Venta Rápida · Supermercado</h1>
        <div class="text-base">Cajero: <strong id="vendedor" data-idvendedor="<?php echo $user['id']; ?>"><?php echo $user['nombre']; ?></strong></div>
      </header>

      <?php
        // =========================
        // MOSTRAR ALERTA (UI PRO)
        // =========================
          $estilos = [
            "warning" => [
              "bg" => "bg-gradient-to-r from-yellow-50 to-yellow-100",
              "border" => "border-yellow-300",
              "iconBg" => "bg-yellow-200",
              "iconColor" => "text-yellow-700",
              "text" => "text-yellow-900",
              "badge" => "bg-yellow-200 text-yellow-800"
            ],
            "danger" => [
              "bg" => "bg-gradient-to-r from-red-50 to-red-100",
              "border" => "border-red-300",
              "iconBg" => "bg-red-200",
              "iconColor" => "text-red-700",
              "text" => "text-red-900",
              "badge" => "bg-red-200 text-red-800"
            ]
          ];

      ?>
      
      <?php foreach($resolucionesVencidas as $value): 
        ($value->vencido??null)?$tipoAlerta = "danger":$tipoAlerta = "warning";
        $ui = $estilos[$tipoAlerta];
      ?>
        <div class="mb-2 animate-fadeSlide">
          <div class="flex items-center gap-4 border <?= $ui["border"] ?> <?= $ui["bg"] ?> rounded-2xl p-4 shadow-sm">

            <!-- ICONO -->
            <div class="flex items-center justify-center w-12 h-12 rounded-xl <?= $ui["iconBg"] ?>">
              <span class="material-symbols-outlined <?= $ui["iconColor"] ?> text-3xl"><?= $tipoAlerta === "danger" ? "error" : "warning" ?></span>
            </div>
            <!-- CONTENIDO -->
            <div class="flex flex-col">
              <div class="flex items-center gap-2">
                <span class="text-sm font-semibold px-3 py-1 rounded-full <?= $ui["badge"] ?>"><?= $tipoAlerta === "danger" ? "VENCIDO" : "POR VENCER" ?></span>
                <span class="text-base text-gray-500 uppercase tracking-wide"><?= strtoupper($value->nombre) ?></span>
              </div>
              <p class="text-lg font-semibold mb-0 <?= $ui["text"] ?>">Tu resolución de facturación <?php echo $value->idtipofacturador == 1?'Electronica':'Pos'; ?> está <?= $tipoAlerta === "danger" ? "VENCIDA" : "POR VENCER" ?></p>
            </div>

          </div>
        </div>
        
      <?php endforeach; ?>
      <!-- FIN MENSALE DE VENCIMIENTO DE RESOLUCION -->

      <div class="my-2">
        <button id="addcliente" class="w-full bg-white border border-gray-200 rounded-2xl px-6 py-5 shadow-sm hover:shadow-md hover:border-indigo-500 transition-all flex items-center justify-between">

          <!-- IZQUIERDA -->
          <div class="flex items-center gap-5">
            <div id="iconCliente" class="bg-indigo-100 text-indigo-600 p-3 rounded-xl">
              <span class="material-symbols-outlined text-4xl">person</span>
            </div>

            <div class="text-left">
              <p class="text-base text-gray-500 m-0 mb-3">Cliente</p>
              <p id="resumenCliente" class="m-0 text-xl font-semibold text-gray-800 leading-tight">Seleccionar cliente</p>
            </div>
          </div>
          <!-- BADGE ESTADO -->
          <div class="flex items-center gap-3">
            <span id="badgeEstado" class="text-base font-semibold px-4 py-2 rounded-full bg-gray-100 text-gray-700">SIN CLIENTE</span>
            <span class="material-symbols-outlined text-gray-400 text-3xl">chevron_right</span>
          </div>
        </button>
      </div>

      <!-- INPUT -->
      <section class="bg-white px-6 py-3 border-b flex gap-4">
        <button type="button" id="btnScanner" class="btn-xs btn-blue whitespace-nowrap">Ctl . Escáner</button>
        
        <!--<input
          id="inputScanner"
          type="text"
          placeholder="Escanee código de barras o escriba SKU"
          class="w-full h-14 text-2xl px-4 border-2 border-indigo-600 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-indigo-300"
          autofocus
        >-->
         <select id="articulo" name=""
          class="w-full rounded-lg border border-gray-300 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600    h-14 text-base px-2"
          multiple="multiple">
          <?php //foreach($productos as $value): ?>
            <!--<option value="<?php //echo $value->id;?>"><?php //echo $value->nombre;?></option>-->
          <?php //endforeach; ?>
        </select> 
        
      </section>

      <!-- TABLA -->
      <section class="flex-1 min-h-0 bg-white overflow-hidden">
        <div class="h-full px-6 overflow-y-auto">
          <table class="w-full border-collapse text-xl">
            <thead class="sticky top-0 bg-gray-200 z-10">
              <tr>
                <th class="py-3 pl-4 text-left rounded-tl-lg">Producto</th>
                <th class="py-3 w-24 text-center">Cant</th>
                <th class="py-3 w-32 text-right">Precio</th>
                <th class="py-3 w-32 text-right">Total</th>
                <th class="py-3 w-16 text-center rounded-tr-lg">✖</th>
              </tr>
            </thead>
            <tbody id="tablaVenta"></tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ================= RESUMEN ================= -->
    <aside class="w-[28rem] bg-white ml-3 rounded shadow flex flex-col h-full">

      <div class="border-b px-4 py-3 font-semibold">Resumen</div>

      <div class="flex-1 px-4 py-4 space-y-3 text-lg">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span id="subTotal">$0</span>
        </div>
        <div class="flex justify-between">
          <span>IVA</span>
          <span id="impuesto">$0</span>
        </div>
        <div class="flex justify-between">
          <span>Unidades</span>
          <span id="totalUnidades">0</span>
        </div>
      </div>
      
      <div class="border-t px-4 py-4 mt-auto flex flex-col gap-3">
        <button id="btnguardar" class="w-full btn-turquoise py-3 text-2xl font-semibold">
          Generar Cotizacion
        </button>
        <button id="facturarA" class="w-full btn-bluedark py-3 text-2xl font-semibold mb-4">
          F8 · Factura Electronica
        </button>
        <div class="flex justify-between items-center text-2xl font-bold">
          <span>TOTAL</span>
          <span id="total" class="text-3xl">$0</span>
        </div>

        <button id="btnfacturar"
          class="w-full btn-lima py-3 rounded text-3xl font-semibold hover:btn-lima flex justify-center items-center">
          ⏎ <strong class="text-xl mr-4 ml-2">ENTER</strong> · COBRAR
        </button>
      </div>
    </aside>
  </div>

  <!-- MODAL PARA CREAR AÑADIR CLIENTE-->
  <?php include __DIR__. "/../ventas/modalCreateAddCli.php"; ?>
  <!-- MODAL PARA GUARDAR EL PEDIDO-->
  <?php include __DIR__. "/../ventas/modalguardarpedido.php"; ?>
  <!--///////////////////// Modal procesar el pago boton facturar /////////////////////////-->
  <?php include __DIR__. "/../ventas/modalprocesarpago.php"; ?>
  <!-- MODAL DATOS DEL ADQUIRIENTE -->
  <?php include __DIR__. "/../ventas/modaladquiriente.php"; ?>

  <script>
    const mediosPagoDB = <?= json_encode($mediospago) ?>;  //se inyecta el array de medios de pago desde PHP a JavaScript y se utiliza en ventas.ts
    const clientesDB = <?= json_encode($clientes) ?>;
    const getParam = <?= json_encode($conflocal) ?>;  //se inyecta el array de parametros de caja desde PHP a JavaScript y se utiliza en separados.ts junto con ahelper.modalpagar.ts
    const percentComisionUser = <?= json_encode($user['porcentajeganancia']); ?>
  </script>
  
</div>
