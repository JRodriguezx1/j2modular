<div class="box pedidosguardados reporte-pedidos">
  <section class="pg-top-card">
    <div class="pg-title-row">
        <a href="/admin/caja" class="pg-back" title="Volver a caja">
          <i class="fa-solid fa-arrow-left"></i>
          <span class="sr-only">Atras</span>
        </a>

        <div class="pg-title-copy">
          <span class="pg-eyebrow">Caja</span>
          <h1>Cotizaciones y pedidos guardados</h1>
          <p>Consulte, retome o elimine las ordenes pendientes de facturar.</p>
        </div>
    </div>

      <div class="pg-count-chip">
        <span class="pg-count-chip__icon">
          <i class="fa-solid fa-folder-open"></i>
        </span>
        <div>
          <span>Registros</span>
          <strong><?php echo count($pedidosguardados ?? []); ?></strong>
        </div>
      </div>
  </section>

  <section class="pg-panel pg-table-card">
    <div class="pg-section-heading pg-section-heading--table">
      <span class="pg-section-heading__icon">
        <i class="fa-solid fa-list-check"></i>
      </span>
      <div>
        <h2>Lista de pedidos</h2>
        <p>Ordenes guardadas, cotizaciones y remisiones pendientes.</p>
      </div>
    </div>

    <div class="pg-table-wrap">
      <table class="display responsive nowrap tabla pedidos-table pg-table" width="100%" id="tablaPedidosGuardados">
          <thead>
              <tr>
                  <th class="all dtr-control">N.</th>
                  <th class="all">Fecha</th>
                  <th>Orden</th>
                  <th>Cliente</th>
                  <th>Zona</th>
                  <th>Vendedor</th>
                  <th>Estado</th>
                  <th>Subtotal</th>
                  <th>Total</th>
                  <th class="accionesth">Acciones</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($pedidosguardados as $index => $value): ?>
              <tr>
                  <td class="dtr-control"><?php echo $index+1;?></td>
                  <td><?php echo $value->fechacreacion; ?></td>
                  <td><?php echo $value->num_orden;?></td>
                  <td><?php echo $value->cliente ?: 'N/A';?></td>
                  <td>Direccion - zona</td>
                  <td><?php echo $value->vendedor;?></td>
                  <td>
                    <div data-estado="<?php echo $value->estado;?>" id="<?php echo $value->id;?>">
                        <span class="pg-status"><?php echo $value->estado;?></span>
                    </div>
                  </td>
                  <td>$<?php echo number_format($value->subtotal, '0', ',', '.');?></td>
                  <td><strong>$<?php echo number_format($value->total, '0', ',', '.');?></strong></td>
                  <td class="accionestd">
                    <div class="pg-actions" id="<?php echo $value->id;?>">
                        <a class="pg-action-open" href="/admin/caja/ordenresumen?id=<?php echo $value->id;?>" title="Ver detalles">Ver</a>
                        <button class="eliminarPedidoGuardado pg-action-delete" title="Eliminar pedido guardado"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    </div>
  </section>
</div>

<style>
  .reporte-pedidos {
    padding: 1.5rem;
    color: #0f172a;
  }

  .reporte-pedidos * {
    letter-spacing: 0;
  }

  .pg-top-card,
  .pg-panel {
    background: #fff;
    border: 1px solid #dbe3f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, .04);
  }

  .pg-top-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.1rem;
    margin-bottom: .85rem;
    background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
  }

  .pg-title-row {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
  }

  .pg-back {
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: #4f46e5;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(79, 70, 229, .22);
    transition: background .2s ease, transform .2s ease;
  }

  .pg-back:hover {
    background: #4338ca;
    transform: translateY(-1px);
  }

  .pg-back i {
    font-size: 1.45rem;
  }

  .pg-eyebrow {
    display: block;
    color: #4f46e5;
    font-size: .88rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: .12rem;
  }

  .pg-top-card h1 {
    margin: 0;
    color: #0f172a;
    font-size: 2.08rem;
    line-height: 1.08;
    font-weight: 800;
  }

  .pg-top-card p {
    margin: .25rem 0 0;
    color: #64748b;
    font-size: 1.02rem;
    line-height: 1.5;
  }

  .pg-count-chip {
    min-width: 190px;
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem .9rem;
    border: 1px solid #c7d2fe;
    border-radius: 8px;
    background: #eef2ff;
  }

  .pg-count-chip__icon,
  .pg-section-heading__icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: #4f46e5;
    background: #e0e7ff;
  }

  .pg-count-chip span:not(.pg-count-chip__icon) {
    display: block;
    color: #475569;
    font-size: .88rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .pg-count-chip strong {
    display: block;
    margin-top: .12rem;
    color: #4338ca;
    font-size: 1.25rem;
    line-height: 1.15;
  }

  .pg-panel {
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .pg-section-heading {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding-bottom: .85rem;
    margin-bottom: .9rem;
    border-bottom: 1px solid #dbe3f0;
  }

  .pg-section-heading h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.28rem;
    line-height: 1.18;
    font-weight: 800;
  }

  .pg-section-heading p {
    margin: .15rem 0 0;
    color: #64748b;
    font-size: 1.02rem;
    line-height: 1.5;
  }

  .pg-table-wrap {
    overflow-x: auto;
    padding-top: .9rem;
  }

  .reporte-pedidos .dataTables_wrapper .dt-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
    margin: .85rem 0;
  }

  .reporte-pedidos .dataTables_wrapper .dt-button,
  .reporte-pedidos .dataTables_wrapper .buttons-colvis {
    min-height: 42px;
    padding: 0 1rem !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #0f172a !important;
    font-size: 1.08rem !important;
    font-weight: 700 !important;
    box-shadow: none !important;
  }

  .reporte-pedidos .dataTables_length,
  .reporte-pedidos .dataTables_filter,
  .reporte-pedidos .dt-length,
  .reporte-pedidos .dt-search {
    margin: .75rem 0;
    font-size: 1.12rem;
    color: #0f172a;
  }

  .reporte-pedidos .dataTables_length select,
  .reporte-pedidos .dataTables_filter input,
  .reporte-pedidos .dt-length select,
  .reporte-pedidos .dt-search input {
    min-height: 42px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0 .75rem;
    font-size: 1.12rem;
    color: #0f172a;
    background: #fff;
  }

  .pg-table {
    min-width: 1050px;
    border-collapse: collapse !important;
  }

  .pg-table thead th {
    vertical-align: middle;
    padding: 1.05rem .9rem;
    color: #1e293b;
    background: #f8fafc;
    font-size: 1.16rem;
    font-weight: 800;
  }

  .pg-table tbody td {
    vertical-align: middle;
    padding: 1.1rem .9rem;
    color: #24324a;
    font-size: 1.14rem;
    font-weight: 400;
    border-bottom: 1px solid #e2e8f0;
  }

  .pg-table tbody tr:nth-child(even) td {
    background: #f8fafc;
  }

  .pg-table tbody tr:hover td {
    background: #eef2ff;
  }

  .pg-status {
    min-width: 112px;
    min-height: 34px;
    padding: 0 .85rem;
    border-radius: 8px;
    color: #047857;
    background: #dcfce7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 800;
  }

  .pg-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
  }

  .pg-action-open {
    min-height: 42px;
    padding: 0 1.05rem;
    border-radius: 8px;
    color: #fff !important;
    background: #14b8a6;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.02rem;
    font-weight: 800;
    text-decoration: none !important;
  }

  .pg-action-delete {
    width: 42px;
    height: 42px;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #dc2626;
    background: #fff1f2;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .reporte-pedidos .dataTables_info,
  .reporte-pedidos .dataTables_paginate {
    font-size: 1.12rem;
    margin-top: 1rem;
  }

  @media (max-width: 900px) {
    .pg-top-card {
      align-items: stretch;
      flex-direction: column;
    }

    .pg-count-chip {
      width: 100%;
      min-width: 0;
    }
  }

  @media (max-width: 768px) {
    .reporte-pedidos {
      padding: .75rem .75rem 8.5rem;
    }

    .reporte-pedidos .pg-top-card,
    .reporte-pedidos .pg-panel {
      max-width: 100%;
      overflow: hidden;
      box-shadow: none;
    }

    .reporte-pedidos .pg-table-wrap {
      overflow: hidden;
      border: 1px solid #dbe4f0;
      border-radius: 8px;
      padding-top: 0;
    }

    .reporte-pedidos .dataTables_wrapper {
      max-width: 100% !important;
      overflow-x: hidden !important;
    }

    .reporte-pedidos .dataTables_length,
    .reporte-pedidos .dataTables_filter,
    .reporte-pedidos .dt-length,
    .reporte-pedidos .dt-search {
      float: none !important;
      clear: both !important;
      display: block !important;
      max-width: 100% !important;
      overflow: hidden !important;
      text-align: center !important;
      width: 100% !important;
    }

    .reporte-pedidos .dataTables_length label,
    .reporte-pedidos .dataTables_filter label,
    .reporte-pedidos .dt-length label,
    .reporte-pedidos .dt-search label {
      align-items: stretch !important;
      display: flex !important;
      flex-direction: column;
      gap: .35rem;
      margin: 0 !important;
      max-width: 100% !important;
      text-align: center !important;
      width: 100% !important;
    }

    .reporte-pedidos .dataTables_filter input,
    .reporte-pedidos .dataTables_length select,
    .reporte-pedidos .dt-search input,
    .reporte-pedidos .dt-length select {
      box-sizing: border-box !important;
      display: block !important;
      margin: 0 auto !important;
      max-width: 100% !important;
      min-width: 0;
      width: 100% !important;
    }

    .reporte-pedidos .dataTables_length select,
    .reporte-pedidos .dt-length select {
      text-align: left;
    }

    .reporte-pedidos .pg-table,
    .reporte-pedidos .pg-table tbody,
    .reporte-pedidos .pg-table tr,
    .reporte-pedidos .pg-table td {
      display: revert !important;
      width: auto !important;
    }

    .reporte-pedidos .pg-table {
      width: 100% !important;
      min-width: 0 !important;
      border-collapse: collapse !important;
      background: #fff;
    }

    .reporte-pedidos .pg-table thead {
      display: table-header-group !important;
    }

    .reporte-pedidos .pg-table thead th {
      display: table-cell !important;
      padding: .7rem .6rem !important;
      font-size: 1rem !important;
      text-align: right;
    }

    .reporte-pedidos .pg-table thead th:nth-child(n+3) {
      display: none !important;
    }

    .reporte-pedidos .pg-table tbody tr {
      display: table-row !important;
      box-shadow: none !important;
      background: transparent !important;
    }

    .reporte-pedidos .pg-table tbody tr:not(.child) > td {
      display: table-cell !important;
      padding: .75rem .65rem !important;
      border: 1px solid #e2e8f0 !important;
      color: #0f172a;
      font-size: 1rem !important;
      line-height: 1.3;
      text-align: right !important;
      white-space: nowrap !important;
    }

    .reporte-pedidos .pg-table tbody tr:not(.child) > td:nth-child(n+3) {
      display: none !important;
    }

    .reporte-pedidos .pg-table tbody tr:not(.child) > td:first-child {
      padding-left: 5.2rem !important;
      position: relative;
    }

    .reporte-pedidos .pg-table tbody tr:not(.child) > td:first-child::before {
      align-items: center;
      background: #eef2ff;
      border-radius: 999px;
      color: #4f46e5;
      content: "Ver  \25BE" !important;
      display: inline-flex !important;
      font-size: .74rem;
      font-weight: 800;
      height: 2rem;
      justify-content: center;
      left: .45rem;
      line-height: 1;
      position: absolute;
      text-transform: uppercase;
      top: 50%;
      transform: translateY(-50%);
      width: 4.2rem;
    }

    .reporte-pedidos .pg-table tbody tr.parent:not(.child) > td:first-child {
      padding-left: 6.9rem !important;
    }

    .reporte-pedidos .pg-table tbody tr.parent:not(.child) > td:first-child::before {
      background: #ccfbf1;
      color: #0f766e;
      content: "Ocultar  \25B4" !important;
      width: 6.1rem;
    }

    .reporte-pedidos .pg-table tbody tr.child,
    .reporte-pedidos .pg-table tbody tr.child td.child {
      display: table-row !important;
    }

    .reporte-pedidos .pg-table tbody tr.child td.child {
      display: table-cell !important;
      padding: .7rem 1rem 1rem !important;
      text-align: center !important;
      white-space: normal !important;
      background: #f8fafc !important;
    }

    .reporte-pedidos .pg-table tbody tr.child ul.dtr-details {
      display: inline-block !important;
      width: auto !important;
      min-width: 70%;
      margin: 0 auto !important;
      text-align: left;
    }

    .reporte-pedidos .pg-table tbody tr.child ul.dtr-details > li {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .8rem;
      padding: .55rem 0;
      border-bottom: 1px solid #e2e8f0;
      font-size: 1rem;
    }

    .reporte-pedidos .pg-table tbody tr.child span.dtr-title {
      min-width: 7.5rem;
      color: #1e293b;
      font-weight: 800;
      text-align: right;
    }

    .reporte-pedidos .pg-table tbody tr.child span.dtr-data {
      color: #0f172a;
      text-align: left;
    }
  }
</style>
