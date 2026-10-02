<div class="inicio dashboard-home">
  <div class="dashboard-home__inner">

    <header class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
      <div>
        <p class="text-indigo-600 text-lg font-bold m-0 uppercase">Panel principal</p>
        <h1 class="text-slate-900 font-bold text-5xl">Dashboard</h1>
        <p class="text-slate-500 m-0 text-xl">
          Resumen general del negocio. &Uacute;ltima actualizaci&oacute;n:
          <span id="ultimaActualizacion">--</span>
        </p>
      </div>

      <div class="flex w-full flex-wrap items-center gap-4 md:w-auto md:flex-nowrap">
        <button class="btnDialog btnDialog_light" type="button">
          <span class="material-symbols-outlined">download</span>
          Exportar
        </button>
        <button class="btnDialog btnDialog_primary" type="button">
          <span class="material-symbols-outlined">refresh</span>
          Actualizar
        </button>
      </div>
    </header>

    <section class="dashboard-kpis dashboard-kpis--primary">
      <article class="dashboard-kpi dashboard-kpi--success">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">payments</span>
          <span class="dashboard-kpi__label">Efectivo facturado hoy</span>
        </div>
        <strong id="efectivoFacturado">$<?php echo number_format($indicadoreseconomicos[0]->efectivofacturado??'0', '0', ',', '.');?></strong>
        <small id="metaEfectivo">Meta: --</small>
      </article>

      <article class="dashboard-kpi dashboard-kpi--primary">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">point_of_sale</span>
          <span class="dashboard-kpi__label">Total ingreso facturado</span>
        </div>
        <strong id="totalIngresosFacturados">$<?php echo number_format($indicadoreseconomicos[0]->totalingreso??'0', '0', ',', '.');?></strong>
        <small id="metaFacturado">Periodo: --</small>
      </article>

      <article class="dashboard-kpi dashboard-kpi--danger">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">receipt_long</span>
          <span class="dashboard-kpi__label">Facturas</span>
        </div>
        <div class="dashboard-kpi__pair">
          <div>
            <strong id="facturasEmitidas"><?php echo number_format($indicadoreseconomicos[0]->totalfacturas??'0', '0', ',', '.');?></strong>
            <small>emitidas</small>
          </div>
          <div>
            <strong id="facturasEliminadas"><?php echo number_format($indicadoreseconomicos[0]->totalfacturaseliminadas??'0', '0', ',', '.');?></strong>
            <small>eliminadas</small>
          </div>
        </div>
      </article>

      <article class="dashboard-kpi dashboard-kpi--warning">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">sell</span>
          <span class="dashboard-kpi__label">Descuentos aplicados</span>
        </div>
        <strong id="descuentosAplicados">$<?php echo number_format($indicadoreseconomicos[0]->totaldescuentos??'0', '0', ',', '.');?></strong>
        <small>Total descuentos</small>
      </article>
    </section>

    <section class="dashboard-kpis dashboard-kpis--secondary">
      <article class="dashboard-kpi dashboard-kpi--danger">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">trending_down</span>
          <span class="dashboard-kpi__label">Indicador de gastos hoy</span>
        </div>
        <strong id="gastosHoy">$<?php echo number_format($indicadoreseconomicos[0]->gastoscaja??'0', '0', ',', '.');?></strong>
        <small>Gastos por transacci&oacute;n promedio: <span id="gastoPromedio">$0</span></small>
      </article>

      <article class="dashboard-kpi dashboard-kpi--neutral">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">inventory_2</span>
          <span class="dashboard-kpi__label">Productos vendidos</span>
        </div>
        <strong id="productosVendidos"><?php echo $cantidadesproductos[0]->totalproductosvendidos??'0';?></strong>
        <small id="metaProductos">Periodo: &uacute;ltimos 30 d&iacute;as</small>
      </article>

      <article class="dashboard-kpi dashboard-kpi--neutral">
        <div class="dashboard-kpi__top">
          <span class="dashboard-kpi__icon material-symbols-outlined">workspace_premium</span>
          <span class="dashboard-kpi__label">Producto m&aacute;s vendido</span>
        </div>
        <strong id="productoMasVendido" class="dashboard-kpi__product"><?php echo $cantidadesproductos[0]->nombre??'--';?></strong>
        <small>&Uacute;ltimos 30 d&iacute;as / Unidades: <?php echo $cantidadesproductos[0]->topunidadesvendidas??'0';?></small>
      </article>
    </section>

    <section class="dashboard-charts">
      <article class="dashboard-panel">
        <div class="dashboard-panel__head">
          <div>
            <h2>Hist&oacute;rico: Ventas vs Gastos</h2>
            <p>&Uacute;ltimos 6 meses</p>
          </div>
          <span class="material-symbols-outlined">monitoring</span>
        </div>
        <div class="dashboard-chart">
          <canvas id="chartVentasGastos"></canvas>
        </div>
      </article>

      <article class="dashboard-panel">
        <div class="dashboard-panel__head">
          <div>
            <h2>Ingresos por d&iacute;a</h2>
            <p>&Uacute;ltimos 7 d&iacute;as. Total: <span id="ingresos7diasTotal">ventas</span></p>
          </div>
          <span class="material-symbols-outlined">bar_chart</span>
        </div>
        <div class="dashboard-chart">
          <canvas id="chartIngresosDias"></canvas>
        </div>
      </article>
    </section>

    <section class="dashboard-bottom">
      <article class="dashboard-panel">
        <div class="dashboard-panel__head">
          <div>
            <h2>Top 8 productos m&aacute;s vendidos</h2>
            <p>Periodo: <span id="periodoTop">&Uacute;ltimos 30 d&iacute;as</span></p>
          </div>
          <span class="material-symbols-outlined">leaderboard</span>
        </div>

        <div class="dashboard-table-wrap">
          <table class="dashboard-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Unidades</th>
                <th>Ingresos</th>
                <th>Porcentaje</th>
              </tr>
            </thead>
            <tbody id="tablaTopProductos">
              <?php foreach($cantidadesproductos as $value): ?>
                <tr>
                  <td><?php echo $value->nombre??'';?></td>
                  <td><?php echo $value->topunidadesvendidas??0;?></td>
                  <td>$ <?php echo number_format($value->totaldinero??0, '2', ',', '.');?></td>
                  <td>% <?php echo $value->porcentaje??0;?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </article>

      <article class="dashboard-panel">
        <div class="dashboard-panel__head">
          <div>
            <h2>Stock m&iacute;nimo</h2>
            <p>6 productos en alerta</p>
          </div>
          <span class="material-symbols-outlined">warning</span>
        </div>

        <ul id="listaStockMinimo" class="dashboard-stock__list">
          <?php foreach($productosSotckMin as $value): ?>
            <li>
              <div>
                <strong><?php echo $value->nombre??'';?></strong>
                <small>Min: <?php echo $value->stockminimo??'';?></small>
              </div>
              <div>
                <b><?php echo $value->stock??'';?></b>
                <small><?php echo $value->unidadmedida??'';?></small>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </article>
    </section>

  </div>
</div>
