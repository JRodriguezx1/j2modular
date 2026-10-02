<div class="box reportes mb-20 w-full pb-12">
  <div class="mx-auto grid min-w-0 max-w-screen-2xl gap-6 rounded-lg border border-slate-200 bg-gradient-to-b from-indigo-50 via-white to-white p-4 shadow-sm sm:p-6">

    <section class="flex flex-col items-stretch gap-5 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 mb-6 p-4 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Analitica</p>
        <h1 class="m-0 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">Centro de reportes</h1>
        <p class="mt-1 text-lg leading-snug text-slate-500">Consulta ventas, cartera, facturacion, inventario y rentabilidad desde una vista mas clara.</p>
      </div>
      <div class="flex justify-start gap-4 lg:justify-end rounded-lg border border-slate-200 bg-white/90 p-4">
        <span class="material-symbols-outlined inline-flex size-16 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-3xl text-white">query_stats</span>
        <div class="flex flex-col justify-between">
          <strong class="block whitespace-nowrap text-2xl font-black leading-none text-slate-900">Panel</strong>
          <small class="mt-1 block text-lg font-bold text-slate-500">reportes disponibles</small>
        </div>
      </div>
    </section>

    <section class="grid min-w-0 gap-6 lg:grid-cols-3">
      <article class="min-w-0 rounded-lg border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-4 shadow-sm sm:p-6 lg:col-span-2">
        <div class="flex flex-col items-stretch justify-between gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center">
          <div>
            <span class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Ventas</span>
            <h2 class="text-slate-900 text-3xl font-bold">Representacion grafica de ventas</h2>
          </div>
          <div class="reportes-dashboard__switch grid grid-cols-2 gap-1 rounded-lg border border-slate-200 bg-slate-100 p-1 sm:inline-flex">
            <button id="graficaVentaMensual" class="graficaventa min-h-14 rounded-md px-5 text-lg font-extrabold text-slate-500 transition hover:bg-white hover:text-indigo-600 focus:bg-white focus:text-indigo-600" type="button">Mensual</button>
            <button id="graficaVentaDiario" class="graficaventa min-h-14 rounded-md px-5 text-lg font-extrabold text-slate-500 transition hover:bg-white hover:text-indigo-600 focus:bg-white focus:text-indigo-600" type="button">Diario</button>
          </div>
        </div>
        <div class="reportes-dashboard__canvas mt-4 h-[30rem] rounded-lg border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-4">
          <canvas id="chartventas"></canvas>
        </div>
      </article>

      <article class="min-w-0 rounded-lg border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-4 shadow-sm sm:p-6">
        <div class="flex flex-col items-stretch justify-between gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-center">
          <div>
            <span class="mb-1 mt-0 text-base font-extrabold uppercase text-indigo-600">Inventario</span>
            <h2 class="text-slate-900 text-3xl font-bold">Productos principales</h2>
          </div>
        </div>
        <div class="reportes-dashboard__canvas mx-auto mt-4  h-[30rem] w-full max-w-3xl rounded-lg border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-4">
          <canvas id="chartutilidad"></canvas>
        </div>
      </article>
    </section>

    <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
      <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
        <span class="material-symbols-outlined inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium">payments</span>
        <div>
          <h2 class="text-slate-900 text-3xl font-bold">Reportes de ventas</h2>
          <p class="my-0 text-lg leading-snug text-slate-500">Seguimiento comercial, cartera y operaciones de caja.</p>
        </div>
      </div>
      <div class="grid grid-cols-[repeat(auto-fit,minmax(18rem,1fr))] gap-4 pt-5">
        <a href="/admin/reportes/ventasgenerales" class="btn_card btn_card--primary">
          <span class="material-symbols-outlined">payments</span>
          <strong>Ventas generales</strong>
          <small>Resumen de ventas</small>
        </a>
        <a href="/admin/reportes/creditos" class="btn_card">
          <span class="material-symbols-outlined">account_balance_wallet</span>
          <strong>Estados creditos</strong>
          <small>Cartera activa</small>
        </a>
        <a href="/admin/caja/ultimoscierres" class="btn_card btn_card--primary">
          <span class="material-symbols-outlined">point_of_sale</span>
          <strong>Cierres de caja</strong>
          <small>Control de caja</small>
        </a>
        <a href="/admin/caja/zetadiario" class="btn_card">
          <i class="fa-solid fa-z"></i>
          <strong>Zeta diario</strong>
          <small>Corte diario</small>
        </a>
        <a href="/admin/reportes/ventasxtransaccion" class="btn_card">
          <span class="material-symbols-outlined">receipt_long</span>
          <strong>Ventas por transaccion</strong>
          <small>Detalle de movimientos</small>
        </a>
        <a href="/admin/reportes/ventasxcliente" class="btn_card">
          <span class="material-symbols-outlined">person_search</span>
          <strong>Ventas por cliente</strong>
          <small>Consumo por cliente</small>
        </a>
        <a href="/admin/reportes/ventaProductosUsuarios" class="btn_card">
          <span class="material-symbols-outlined">package_2</span>
          <strong>Productos por usuario</strong>
          <small>Gestion por vendedor</small>
        </a>
        <a href="/admin/reportes/reporteEmisores" class="btn_card">
          <span class="material-symbols-outlined">business_center</span>
          <strong>Reporte de emisores</strong>
          <small>Operacion por emisor</small>
        </a>
      </div>
    </section>

    <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
      <div class="flex items-center gap-4 border-b border-slate-200 pb-4 [&>span]:inline-flex [&>span]:size-16 [&>span]:shrink-0 [&>span]:items-center [&>span]:justify-center [&>span]:rounded-lg [&>span]:bg-indigo-50 [&>span]:text-3xl [&>span]:text-indigo-600 [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_p]:my-0 [&_p]:text-lg [&_p]:leading-snug [&_p]:text-slate-500">
        <span class="material-symbols-outlined">request_quote</span>
        <div>
          <h2>Reportes de facturas</h2>
          <p>Consulta facturas, recibos y documentos electronicos.</p>
        </div>
      </div>
      <div class="grid grid-cols-[repeat(auto-fit,minmax(18rem,1fr))] gap-4 pt-5">
        <a href="/admin/reportes/facturaspagas" class="btn_card">
          <span class="material-symbols-outlined">request_quote</span>
          <strong>Facturas pagas</strong>
          <small>Documentos pagados</small>
        </a>
        <a href="/admin/caja/pedidosguardados" class="btn_card">
          <span class="material-symbols-outlined">receipt_long</span>
          <strong>Cotizaciones</strong>
          <small>Pedidos guardados</small>
        </a>
        <a href="/admin/reportes/facturasanuladas" class="btn_card">
          <span class="material-symbols-outlined">contract_delete</span>
          <strong>Facturas anuladas</strong>
          <small>Documentos anulados</small>
        </a>
        <a href="/admin/reportes/facturaselectronicas" class="btn_card">
          <span class="material-symbols-outlined">description</span>
          <strong>Electronicas generadas</strong>
          <small>Facturacion electronica</small>
        </a>
        <a href="/admin/reportes/facturaselectronicaspendientes" class="btn_card">
          <span class="material-symbols-outlined">pending_actions</span>
          <strong>Electronicas pendientes</strong>
          <small>Por gestionar</small>
        </a>
        <a href="/admin/reportes/recibosCaja" class="btn_card">
          <span class="material-symbols-outlined">point_of_sale</span>
          <strong>Recibos de caja</strong>
          <small>Ingresos registrados</small>
        </a>
      </div>
    </section>

    <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
      <div class="flex items-center gap-4 border-b border-slate-200 pb-4 [&>span]:inline-flex [&>span]:size-16 [&>span]:shrink-0 [&>span]:items-center [&>span]:justify-center [&>span]:rounded-lg [&>span]:bg-indigo-50 [&>span]:text-3xl [&>span]:text-indigo-600 [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_p]:my-0 [&_p]:text-lg [&_p]:leading-snug [&_p]:text-slate-500">
        <span class="material-symbols-outlined">inventory_2</span>
        <div>
          <h2>Reportes de inventario</h2>
          <p>Movimientos, compras y control de existencias.</p>
        </div>
      </div>
      <div class="grid grid-cols-[repeat(auto-fit,minmax(18rem,1fr))] gap-4 pt-5">
        <a href="/admin/reportes/inventarioxproducto" class="btn_card">
          <span class="material-symbols-outlined">category</span>
          <strong>Inventario por producto</strong>
          <small>Existencias por articulo</small>
        </a>
        <button class="btn_card" type="button">
          <span class="material-symbols-outlined">splitscreen_bottom</span>
          <strong>Inventario por categoria</strong>
          <small>Vista agrupada</small>
        </button>
        <button class="btn_card" type="button">
          <span class="material-symbols-outlined">storefront</span>
          <strong>Inventario por sede</strong>
          <small>Stock por ubicacion</small>
        </button>
        <button class="btn_card" type="button">
          <span class="material-symbols-outlined">inventory_2</span>
          <strong>Inventario general</strong>
          <small>Balance completo</small>
        </button>
        <a href="/admin/reportes/movimientosinventarios" class="btn_card">
          <span class="material-symbols-outlined">sync_alt</span>
          <strong>Movimientos de inventario</strong>
          <small>Entradas y salidas</small>
        </a>
        <a href="/admin/reportes/compras" class="btn_card btn_card--primary">
          <span class="material-symbols-outlined">shopping_cart</span>
          <strong>Compras</strong>
          <small>Ordenes y compras</small>
        </a>
        <a href="/admin/reportes/productosComprados" class="btn_card">
          <span class="material-symbols-outlined">shopping_cart</span>
          <strong>Productos Comprados</strong>
          <small>Lista de productos comprados</small>
        </a>
        <button class="btn_card btn_card--primary" type="button">
          <span class="material-symbols-outlined">move_up</span>
          <strong>Rotacion de inventario</strong>
          <small>Indicadores de salida</small>
        </button>
      </div>
    </section>

    <div class="grid min-w-0 gap-6 xl:grid-cols-3">
      <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6 xl:col-span-2">
        <div class="flex items-center gap-4 border-b border-slate-200 pb-4 [&>span]:inline-flex [&>span]:size-16 [&>span]:shrink-0 [&>span]:items-center [&>span]:justify-center [&>span]:rounded-lg [&>span]:bg-indigo-50 [&>span]:text-3xl [&>span]:text-indigo-600 [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_p]:my-0 [&_p]:text-lg [&_p]:leading-snug [&_p]:text-slate-500">
          <span class="material-symbols-outlined">monitoring</span>
          <div>
            <h2>Utilidad y crecimiento</h2>
            <p>Rentabilidad, gastos y comparativos.</p>
          </div>
        </div>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(17rem,1fr))] gap-4 pt-5">
          <a href="/admin/reportes/utilidadRentabilidad" class="btn_card">
            <span class="material-symbols-outlined">monitoring</span>
            <strong>Utilidad rentabilidad</strong>
            <small>Margenes del negocio</small>
          </a>
          <a href="/admin/reportes/utilidadxproducto" class="btn_card">
            <span class="material-symbols-outlined">chart_data</span>
            <strong>Utilidad por producto</strong>
            <small>Margen por articulo</small>
          </a>
          <button class="btn_card" type="button">
            <span class="material-symbols-outlined">category</span>
            <strong>Utilidad por categoria</strong>
            <small>Agrupacion por familia</small>
          </button>
          <a href="/admin/reportes/gastoseingresos" class="btn_card">
            <span class="material-symbols-outlined">fact_check</span>
            <strong>Gastos e ingresos</strong>
            <small>Flujo operativo</small>
          </a>
          <button class="btn_card" type="button">
            <span class="material-symbols-outlined">query_stats</span>
            <strong>Comparacion interanual</strong>
            <small>Evolucion por anos</small>
          </button>
          <button class="btn_card" type="button">
            <span class="material-symbols-outlined">deployed_code_update</span>
            <strong>Tasa de retorno</strong>
            <small>Indicador de inversion</small>
          </button>
        </div>
      </section>

      <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="flex items-center gap-4 border-b border-slate-200 pb-4 [&>span]:inline-flex [&>span]:size-16 [&>span]:shrink-0 [&>span]:items-center [&>span]:justify-center [&>span]:rounded-lg [&>span]:bg-indigo-50 [&>span]:text-3xl [&>span]:text-indigo-600 [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_p]:my-0 [&_p]:text-lg [&_p]:leading-snug [&_p]:text-slate-500">
          <span class="material-symbols-outlined">group</span>
          <div>
            <h2>Otros reportes</h2>
            <p>Clientes y actividad del sistema.</p>
          </div>
        </div>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(17rem,1fr))] gap-4 pt-5">
          <a href="/admin/reportes/clientesnuevos" class="btn_card">
            <span class="material-symbols-outlined">person_add</span>
            <strong>Clientes nuevos</strong>
            <small>Altas recientes</small>
          </a>
          <a href="/admin/reportes/clientesrecurrentes" class="btn_card btn_card--primary">
            <span class="material-symbols-outlined">person_check</span>
            <strong>Clientes recurrentes</strong>
            <small>Frecuencia de compra</small>
          </a>
          <button class="btn_card btn_card--primary" type="button">
            <span class="material-symbols-outlined">vpn_key_alert</span>
            <strong>Registro de actividad</strong>
            <small>Auditoria interna</small>
          </button>
        </div>
      </section>
    </div>
  </div>
</div>
