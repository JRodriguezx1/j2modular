<div class="box remisiones relative reporte-remisiones">
  <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>
  <section class="rm-top-card">
    <div class="rm-title-row">
      <a href="/admin/reportes" class="rm-back" aria-label="Volver a reportes">
        <svg viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </a>
      <div class="rm-title-copy">
        <span class="rm-eyebrow">Reportes</span>
        <h1>Remisiones</h1>
        <p>Consulte remisiones, fechas de entrega y estados de despacho.</p>
      </div>
    </div>

    <div class="rm-period-chip">
      <span class="rm-period-card__icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M7 3v3m10-3v3M4.5 9h15M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
      </span>
      <div>
        <span>Periodo</span>
        <strong><span id="fecha1">-</span> al <span id="fecha2">-</span></strong>
      </div>
    </div>
  </section>

  <section class="rm-filter-bar">
    <div class="rm-filter-copy">
      <span class="rm-section-heading__icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
      </span>
      <div>
        <h2>Filtros</h2>
        <p>Elija un rango rapido o use una fecha personalizada.</p>
      </div>
    </div>

    <div class="rm-filter-controls">
      <div class="rm-quick-actions" aria-label="Rangos rapidos">
        <button type="button" id="btnmesactual" class="rm-period-btn">Mes actual</button>
        <button type="button" id="btnmesanterior" class="rm-period-btn">Mes anterior</button>
        <button type="button" id="btnhoy" class="rm-period-btn">Hoy</button>
        <button type="button" id="btnayer" class="rm-period-btn">Ayer</button>
      </div>

      <div class="rm-date-actions">
        <input type="text" name="datetimes" class="rm-date-input" placeholder="Seleccionar fecha" />
        <button type="button" id="consultarFechaPersonalizada" class="rm-primary-btn">Consultar</button>
      </div>
    </div>
  </section>

  <section class="rm-panel rm-table-card">
    <div class="rm-section-heading rm-section-heading--table">
      <span class="rm-section-heading__icon">
        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 6h13M8 12h13M8 18h13"/>
          <path d="M3 6h.01M3 12h.01M3 18h.01"/>
        </svg>
      </span>
      <div>
        <h2>Lista de remisiones</h2>
        <p>Remisiones filtradas por el periodo seleccionado.</p>
      </div>
    </div>

    <div class="rm-table-wrap">
      <table id="tablaRemisiones" class="display responsive nowrap tabla rm-table" width="100%">
        <thead>
          <tr>
            <th>Id</th>
            <th>Fecha</th>
            <th>Fecha Entrega</th>
            <th>Usuario</th>
            <th>Cliente</th>
            <th>Orden</th>
            <th>Entrega</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>
</div>

<style>
  .reporte-remisiones {
    padding: 1.5rem;
    color: #0f172a;
  }

  .reporte-remisiones * {
    letter-spacing: 0;
  }

  .rm-top-card,
  .rm-filter-bar,
  .rm-panel {
    background: #fff;
    border: 1px solid #dbe3f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, .04);
  }

  .rm-top-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.1rem;
    margin-bottom: .85rem;
    background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
  }

  .rm-title-row {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
  }

  .rm-title-copy {
    min-width: 0;
  }

  .rm-back {
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

  .rm-back:hover {
    background: #4338ca;
    transform: translateY(-1px);
  }

  .rm-back svg {
    width: 22px;
    height: 22px;
    transform: rotate(180deg);
  }

  .rm-eyebrow {
    display: block;
    color: #4f46e5;
    font-size: .82rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: .12rem;
  }

  .rm-top-card h1 {
    margin: 0;
    color: #0f172a;
    font-size: 1.8rem;
    line-height: 1.12;
    font-weight: 800;
  }

  .rm-top-card p {
    margin: .25rem 0 0;
    color: #64748b;
    font-size: 1rem;
    line-height: 1.45;
  }

  .rm-period-chip {
    min-width: 300px;
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem .9rem;
    border: 1px solid #c7d2fe;
    border-radius: 8px;
    background: #eef2ff;
  }

  .rm-period-card__icon,
  .rm-section-heading__icon {
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

  .rm-period-card__icon svg,
  .rm-section-heading__icon svg {
    width: 21px;
    height: 21px;
  }

  .rm-period-chip span:not(.rm-period-card__icon) {
    display: block;
    color: #475569;
    font-size: .82rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .rm-period-chip strong {
    display: block;
    margin-top: .12rem;
    color: #4338ca;
    font-size: .98rem;
    line-height: 1.35;
  }

  .rm-filter-bar {
    display: grid;
    grid-template-columns: minmax(240px, .55fr) minmax(540px, 1.45fr);
    align-items: center;
    gap: 1rem;
    padding: .85rem 1rem;
    margin-bottom: 1rem;
  }

  .rm-filter-copy {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-width: 0;
  }

  .rm-filter-copy h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.14rem;
    line-height: 1.2;
    font-weight: 800;
  }

  .rm-filter-copy p {
    margin: .18rem 0 0;
    color: #64748b;
    font-size: .93rem;
    line-height: 1.45;
  }

  .rm-filter-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .75rem;
    flex-wrap: wrap;
  }

  .rm-quick-actions,
  .rm-date-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .55rem;
  }

  .rm-date-actions {
    margin-left: auto;
  }

  .rm-period-btn,
  .rm-primary-btn,
  .rm-date-input {
    min-height: 44px;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
  }

  .rm-period-btn {
    padding: 0 1rem;
    color: #334155;
    background: #fff;
    border: 1px solid #cbd5e1;
    font-weight: 700;
    cursor: pointer;
    transition: border-color .2s ease, color .2s ease, background .2s ease;
  }

  .rm-period-btn:hover,
  .rm-period-btn:focus {
    border-color: #818cf8;
    color: #4338ca;
    background: #eef2ff;
  }

  .rm-date-input {
    width: 280px;
    padding: 0 .9rem;
    color: #334155;
    border: 1px solid #cbd5e1;
    background: #fff;
  }

  .rm-primary-btn {
    padding: 0 1.15rem;
    border: 0;
    color: #fff;
    background: #4f46e5;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 8px 16px rgba(79, 70, 229, .18);
  }

  .rm-primary-btn:hover,
  .rm-primary-btn:focus {
    background: #4338ca;
  }

  .rm-panel {
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .rm-section-heading {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding-bottom: .85rem;
    margin-bottom: .9rem;
    border-bottom: 1px solid #dbe3f0;
  }

  .rm-section-heading--table {
    margin-bottom: .75rem;
  }

  .rm-section-heading h2 {
    margin: 0;
    color: #0f172a;
    font-size: 1.22rem;
    line-height: 1.2;
    font-weight: 800;
  }

  .rm-section-heading p {
    margin: .15rem 0 0;
    color: #64748b;
    font-size: .93rem;
    line-height: 1.45;
  }
  .rm-table-wrap {
    overflow-x: auto;
    padding-top: .9rem;
  }

  .rm-table {
    min-width: 1050px;
    border-collapse: collapse !important;
  }

  .reporte-remisiones .dataTables_wrapper .dt-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
    margin: .85rem 0;
  }

  .reporte-remisiones .dataTables_wrapper .dt-button,
  .reporte-remisiones .dataTables_wrapper .buttons-colvis {
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

  .reporte-remisiones .dataTables_wrapper .dt-button:hover,
  .reporte-remisiones .dataTables_wrapper .buttons-colvis:hover {
    color: #4338ca !important;
    border-color: #818cf8 !important;
    background: #eef2ff !important;
  }

  .reporte-remisiones .dataTables_length,
  .reporte-remisiones .dataTables_filter,
  .reporte-remisiones .dt-length,
  .reporte-remisiones .dt-search {
    margin: .75rem 0;
    font-size: 1.12rem;
    color: #0f172a;
  }

  .reporte-remisiones .dataTables_length select,
  .reporte-remisiones .dataTables_filter input,
  .reporte-remisiones .dt-length select,
  .reporte-remisiones .dt-search input {
    min-height: 42px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0 .75rem;
    font-size: 1.12rem;
    color: #0f172a;
    background: #fff;
  }

  .reporte-remisiones #tablaRemisiones thead th {
    vertical-align: middle;
    padding: 1.05rem .9rem;
    color: #1e293b;
    background: #f8fafc;
    font-size: 1.16rem;
    font-weight: 800;
  }

  .reporte-remisiones #tablaRemisiones tbody td {
    vertical-align: middle;
    padding: 1.1rem .9rem;
    color: #24324a;
    font-size: 1.14rem;
    font-weight: 400;
  }

  .rm-status,
  .rm-action-open,
  .rm-delivery {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
  }

  .rm-status {
    min-width: 112px;
    min-height: 34px;
    padding: 0 .85rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 800;
  }

  .rm-status--done {
    color: #047857;
    background: #dcfce7;
  }

  .rm-status--pending {
    color: #c2410c;
    background: #fff7ed;
  }

  .rm-delivery {
    min-height: 30px;
    padding: 0 .7rem;
    border-radius: 999px;
    color: #4338ca;
    background: #eef2ff;
    font-size: .98rem;
    font-weight: 700;
  }

  .rm-action-open {
    min-height: 42px;
    padding: 0 1.05rem;
    border-radius: 8px;
    color: #fff !important;
    background: #14b8a6;
    font-size: 1.02rem;
    font-weight: 800;
    text-decoration: none !important;
    box-shadow: 0 8px 14px rgba(20, 184, 166, .18);
  }

  .rm-action-open:hover {
    background: #0f9f93;
  }

  @media (max-width: 900px) {
    .rm-top-card {
      align-items: stretch;
      flex-direction: column;
    }

    .rm-period-chip {
      width: 100%;
      min-width: 0;
    }

    .rm-filter-bar {
      grid-template-columns: 1fr;
    }

    .rm-filter-controls {
      justify-content: flex-start;
    }

    .rm-date-actions {
      width: 100%;
      margin-left: 0;
    }
  }

  @media (max-width: 768px) {
    .reporte-remisiones {
      padding: 1rem;
    }

    .rm-quick-actions {
      display: grid;
      width: 100%;
      grid-template-columns: 1fr 1fr;
    }

    .rm-date-actions {
      align-items: stretch;
      flex-direction: column;
    }

    .rm-date-input,
    .rm-primary-btn {
      width: 100%;
    }

    .reporte-remisiones .dataTables_wrapper .dataTables_filter,
    .reporte-remisiones .dataTables_wrapper .dataTables_length,
    .reporte-remisiones .dataTables_wrapper .dt-search,
    .reporte-remisiones .dataTables_wrapper .dt-length {
      float: none;
      text-align: left;
      width: 100%;
    }

    .reporte-remisiones .dataTables_filter input,
    .reporte-remisiones .dt-search input {
      width: 100%;
      margin: .35rem 0 0;
    }
  }

  /* Typography scale adjustment */
  .reporte-remisiones {
    font-size: 1rem;
  }

  .rm-top-card h1 {
    font-size: 2.08rem;
    line-height: 1.08;
  }

  .rm-top-card p,
  .rm-filter-copy p,
  .rm-section-heading p {
    font-size: 1.02rem;
    line-height: 1.5;
  }

  .rm-eyebrow,
  .rm-period-chip span:not(.rm-period-card__icon) {
    font-size: .88rem;
  }

  .rm-period-chip strong {
    font-size: 1.05rem;
    line-height: 1.35;
  }

  .rm-filter-copy h2,
  .rm-section-heading h2 {
    font-size: 1.28rem;
    line-height: 1.18;
  }

  .rm-period-btn,
  .rm-primary-btn,
  .rm-date-input,
  .reporte-remisiones .dt-button,
  .reporte-remisiones .dataTables_length,
  .reporte-remisiones .dataTables_filter,
  .reporte-remisiones .dataTables_info,
  .reporte-remisiones .dataTables_paginate,
  .reporte-remisiones .dataTables_length select,
  .reporte-remisiones .dataTables_filter input {
    font-size: 1.02rem;
  }

  .rm-period-btn,
  .rm-primary-btn,
  .rm-date-input,
  .reporte-remisiones .dt-button,
  .reporte-remisiones .dataTables_length select,
  .reporte-remisiones .dataTables_filter input {
    min-height: 42px;
  }

  #tablaRemisiones thead th {
    font-size: .98rem;
  }

  #tablaRemisiones tbody td {
    font-size: 1rem;
    line-height: 1.35;
  }

  #tablaRemisiones tbody .badge,
  #tablaRemisiones tbody .rm-badge,
  #tablaRemisiones tbody .delivery-badge,
  #tablaRemisiones tbody .status-badge,
  #tablaRemisiones tbody a,
  #tablaRemisiones tbody button {
    font-size: .94rem;
  }

  @media (max-width: 768px) {
    .rm-top-card h1 {
      font-size: 1.78rem;
    }

    .rm-filter-copy h2,
    .rm-section-heading h2 {
      font-size: 1.18rem;
    }

    #tablaRemisiones thead th {
      font-size: .94rem;
    }

    #tablaRemisiones tbody td {
      font-size: .98rem;
    }
  }
  @media (max-width: 768px) {
    .reporte-remisiones {
      padding: .75rem .75rem 8.5rem;
    }

    .reporte-remisiones .rm-top-card,
    .reporte-remisiones .rm-filter-bar,
    .reporte-remisiones .rm-panel {
      box-shadow: none;
    }

    .reporte-remisiones .rm-table-wrap,
    .reporte-remisiones .dataTables_wrapper {
      width: 100%;
      max-width: 100%;
      overflow: visible;
    }

    .reporte-remisiones .dataTables_wrapper .dt-buttons {
      gap: .5rem;
      margin: .75rem 0 1rem;
    }

    .reporte-remisiones .dataTables_filter,
    .reporte-remisiones .dt-search {
      overflow: hidden;
    }

    .reporte-remisiones .dataTables_filter label,
    .reporte-remisiones .dt-search label {
      width: 100%;
    }

    .reporte-remisiones .dataTables_filter input,
    .reporte-remisiones .dt-search input {
      box-sizing: border-box;
      display: block;
      max-width: 100%;
      min-width: 0;
      width: 100% !important;
    }

    .reporte-remisiones .dataTables_wrapper .dt-button,
    .reporte-remisiones .dataTables_wrapper .buttons-colvis {
      flex: 1 1 calc(50% - .5rem);
      min-width: 0;
      padding: 0 .65rem !important;
      font-size: .88rem !important;
    }

    .reporte-remisiones #tablaRemisiones,
    .reporte-remisiones #tablaRemisiones tbody,
    .reporte-remisiones #tablaRemisiones tr,
    .reporte-remisiones #tablaRemisiones td {
      display: block !important;
      width: 100% !important;
      box-sizing: border-box;
    }

    .reporte-remisiones #tablaRemisiones {
      min-width: 0 !important;
      border: 0 !important;
      background: transparent;
    }

    .reporte-remisiones #tablaRemisiones thead,
    .reporte-remisiones #tablaRemisiones colgroup,
    .reporte-remisiones .dataTables_scrollHead,
    .reporte-remisiones #tablaRemisiones tr.child,
    .reporte-remisiones #tablaRemisiones td.child,
    .reporte-remisiones #tablaRemisiones .dtr-details {
      display: none !important;
    }

    .reporte-remisiones #tablaRemisiones tbody {
      display: grid !important;
      gap: .85rem;
    }

    .reporte-remisiones #tablaRemisiones tbody tr {
      border: 1px solid #dbe4f0 !important;
      border-radius: 8px !important;
      overflow: hidden;
      background: #fff !important;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
    }

    .reporte-remisiones #tablaRemisiones tbody td {
      display: grid !important;
      grid-template-columns: minmax(104px, 36%) minmax(0, 1fr);
      align-items: center;
      gap: .65rem;
      min-height: 42px;
      padding: .65rem .75rem !important;
      border: 0 !important;
      border-bottom: 1px solid #eef2f7 !important;
      color: #0f172a;
      font-size: .92rem !important;
      line-height: 1.35;
      text-align: right !important;
      white-space: normal !important;
      overflow-wrap: anywhere;
    }

    .reporte-remisiones #tablaRemisiones tbody td:last-child {
      border-bottom: 0 !important;
    }

    .reporte-remisiones #tablaRemisiones tbody td::before {
      content: attr(data-rm-label);
      color: #64748b;
      font-size: .85rem;
      font-weight: 800;
      text-align: left;
    }

    .reporte-remisiones #tablaRemisiones tbody td:nth-child(1)::before { content: "Id"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(2)::before { content: "Fecha"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(3)::before { content: "Fecha entrega"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(4)::before { content: "Usuario"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(5)::before { content: "Cliente"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(6)::before { content: "Orden"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(7)::before { content: "Entrega"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(8)::before { content: "Estado"; }
    .reporte-remisiones #tablaRemisiones tbody td:nth-child(9)::before { content: "Acciones"; }

    .reporte-remisiones #tablaRemisiones tbody td:empty::after {
      content: '-';
      color: #94a3b8;
    }

    .reporte-remisiones #tablaRemisiones tbody td[data-rm-label="Id"],
    .reporte-remisiones #tablaRemisiones tbody td[data-rm-label="Orden"] {
      background: #f8fafc;
      font-weight: 700;
    }

    .reporte-remisiones #tablaRemisiones tbody td[data-rm-label="Acciones"] {
      grid-template-columns: 1fr;
      padding: .75rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody td[data-rm-label="Acciones"]::before {
      display: none;
    }

    .reporte-remisiones .rm-status,
    .reporte-remisiones .rm-delivery,
    .reporte-remisiones .rm-action-open {
      justify-self: end;
      max-width: 100%;
    }

    .reporte-remisiones .rm-action-open {
      width: 100%;
    }

    .barra-mobile {
      display: flex !important;
      overflow: visible !important;
      position: relative;
      z-index: 50;
    }

    .barra-mobile.ocultarmenu {
      display: none !important;
    }

    .barra-mobile > .fixed {
      display: block !important;
      position: fixed !important;
      left: 50% !important;
      right: auto !important;
      bottom: 1rem !important;
      transform: translateX(-50%) !important;
      z-index: 9999 !important;
      max-width: min(33rem, calc(100vw - 2rem)) !important;
      overflow: hidden !important;
    }

    .reporte-remisiones .rm-table-wrap {
      overflow: hidden;
      border: 1px solid #dbe4f0;
      border-radius: 8px;
      padding-top: 0;
    }

    .reporte-remisiones #tablaRemisiones,
    .reporte-remisiones #tablaRemisiones tbody,
    .reporte-remisiones #tablaRemisiones tr,
    .reporte-remisiones #tablaRemisiones td {
      display: revert !important;
      width: auto !important;
    }

    .reporte-remisiones #tablaRemisiones {
      width: 100% !important;
      min-width: 0 !important;
      border-collapse: collapse !important;
      background: #fff;
    }

    .reporte-remisiones #tablaRemisiones thead {
      display: table-header-group !important;
    }

    .reporte-remisiones #tablaRemisiones thead th {
      display: table-cell !important;
      padding: .7rem .6rem !important;
      font-size: 1rem !important;
      text-align: right;
    }

    .reporte-remisiones #tablaRemisiones thead th:first-child {
      width: 34%;
    }

    .reporte-remisiones #tablaRemisiones thead th:nth-child(n+3) {
      display: none !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr {
      display: table-row !important;
      border: 0 !important;
      border-radius: 0 !important;
      box-shadow: none !important;
      background: transparent !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td {
      display: table-cell !important;
      min-height: 0;
      padding: .75rem .65rem !important;
      border: 1px solid #e2e8f0 !important;
      color: #0f172a;
      font-size: 1rem !important;
      line-height: 1.3;
      text-align: right !important;
      white-space: nowrap !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td:nth-child(n+3) {
      display: none !important;
    }

    .reporte-remisiones #tablaRemisiones tbody td::before,
    .reporte-remisiones #tablaRemisiones tbody td::after {
      content: none !important;
      display: none !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:nth-child(even) td,
    .reporte-remisiones #tablaRemisiones tbody tr.parent td {
      background: #eef2ff !important;
    }

    .reporte-remisiones table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
    .reporte-remisiones table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
      border: 0 !important;
      box-shadow: none !important;
      color: #4f46e5 !important;
      content: "Ver  \25BE" !important;
      font-size: .74rem !important;
      font-weight: 800;
      height: 2rem !important;
      left: .45rem !important;
      line-height: 2rem !important;
      margin: 0 !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 4.2rem !important;
      border-radius: 999px !important;
      background: #eef2ff !important;
      text-align: center !important;
      text-transform: uppercase;
    }

    .reporte-remisiones table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control::before,
    .reporte-remisiones table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control::before {
      color: #0f766e !important;
      content: "Ocultar  \25B4" !important;
      background: #ccfbf1 !important;
      width: 6.1rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody td.dtr-control {
      padding-left: 5.1rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.parent td.dtr-control {
      padding-left: 6.9rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child td.child {
      display: table-cell !important;
      padding: .7rem 1rem 1rem !important;
      text-align: center !important;
      white-space: normal !important;
      background: #f8fafc !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child {
      display: table-row !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child ul.dtr-details {
      display: inline-block !important;
      width: auto !important;
      min-width: 70%;
      margin: 0 auto !important;
      text-align: left;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child ul.dtr-details > li {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .8rem;
      padding: .55rem 0;
      border-bottom: 1px solid #e2e8f0;
      font-size: 1rem;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child span.dtr-title {
      min-width: 7.5rem;
      color: #1e293b;
      font-weight: 800;
      text-align: right;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child span.dtr-data {
      color: #0f172a;
      text-align: left;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.child .rm-action-open {
      min-height: 34px;
      padding: 0 .9rem;
      box-shadow: none;
    }

    .reporte-remisiones .rm-panel,
    .reporte-remisiones .rm-table-card,
    .reporte-remisiones .dataTables_wrapper {
      max-width: 100% !important;
      overflow-x: hidden !important;
    }

    .reporte-remisiones .dataTables_filter,
    .reporte-remisiones .dt-search {
      box-sizing: border-box;
      max-width: 100% !important;
      overflow: hidden !important;
      padding: 0 .35rem;
      width: 100% !important;
    }

    .reporte-remisiones .dataTables_filter label,
    .reporte-remisiones .dt-search label {
      align-items: stretch !important;
      box-sizing: border-box;
      display: flex !important;
      flex-direction: column !important;
      gap: .35rem;
      max-width: 100% !important;
      width: 100% !important;
    }

    .reporte-remisiones .dataTables_filter input,
    .reporte-remisiones .dt-search input {
      box-sizing: border-box !important;
      display: block !important;
      margin: 0 !important;
      max-width: 100% !important;
      min-width: 0 !important;
      width: 100% !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td:first-child {
      padding-left: 5.2rem !important;
      position: relative;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td.rm-expand-cell {
      cursor: pointer;
      padding-left: 5.2rem !important;
      position: relative;
    }

    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td:first-child::before,
    .reporte-remisiones #tablaRemisiones tbody tr:not(.child) > td.rm-expand-cell::before {
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
      text-align: center;
      text-transform: uppercase;
      top: 50%;
      transform: translateY(-50%);
      width: 4.2rem;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.parent:not(.child) > td:first-child {
      padding-left: 6.9rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.parent:not(.child) > td.rm-expand-cell {
      padding-left: 6.9rem !important;
    }

    .reporte-remisiones #tablaRemisiones tbody tr.parent:not(.child) > td:first-child::before,
    .reporte-remisiones #tablaRemisiones tbody tr.parent:not(.child) > td.rm-expand-cell::before {
      background: #ccfbf1;
      color: #0f766e;
      content: "Ocultar  \25B4" !important;
      width: 6.1rem;
    }
  }
</style>
<script>
(function () {
  var labels = ['Id', 'Fecha', 'Fecha entrega', 'Usuario', 'Cliente', 'Orden', 'Entrega', 'Estado', 'Acciones'];

  function prepararRemisionesMobile() {
    var tabla = document.getElementById('tablaRemisiones');
    if (!tabla) return;

    tabla.querySelectorAll('tbody tr:not(.child)').forEach(function (fila) {
      Array.prototype.forEach.call(fila.children, function (celda, index) {
        celda.setAttribute('data-rm-label', labels[index] || 'Dato');
        if (index === 0) celda.classList.add('rm-expand-cell');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', prepararRemisionesMobile);
  window.addEventListener('load', prepararRemisionesMobile);
  window.addEventListener('resize', prepararRemisionesMobile);
  document.addEventListener('draw.dt', prepararRemisionesMobile, true);
  if (window.jQuery) {
    window.jQuery(document).on('draw.dt responsive-display.dt responsive-resize.dt', prepararRemisionesMobile);
  }
  setTimeout(prepararRemisionesMobile, 300);
  setTimeout(prepararRemisionesMobile, 900);
  setTimeout(prepararRemisionesMobile, 1600);
}());
</script>
