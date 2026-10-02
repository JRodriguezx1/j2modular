<div class="fechazetadiario relative bg-white rounded-lg shadow">
    <style>
        .fechazetadiario,
        .fechazetadiario * {
            box-sizing: border-box;
        }

        .fechazetadiario {
            max-width: 100%;
            overflow-x: hidden;
        }

        .zeta-detail-page {
            padding: 18px 20px 28px;
        }

        .zeta-detail-back {
            align-items: center;
            background: #4a3bd8;
            border-radius: 8px;
            color: #ffffff;
            display: inline-flex;
            height: 42px;
            justify-content: center;
            margin-bottom: 14px;
            transition: background .18s ease, transform .18s ease;
            width: 42px;
        }

        .zeta-detail-back:hover {
            background: #382db8;
            transform: translateY(-1px);
        }

        .zeta-detail-header {
            align-items: flex-end;
            border-bottom: 2px solid #5145f0;
            display: flex;
            gap: 18px;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .zeta-detail-title {
            color: #1f2f46;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.15;
            margin: 0;
        }

        .zeta-detail-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 4px;
        }

        .zeta-filter-panel,
        .zeta-info-panel,
        .zeta-summary-card {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            box-shadow: 0 8px 18px rgba(31, 47, 70, .04);
        }

        .zeta-filter-panel {
            margin-bottom: 16px;
            min-width: 0;
            padding: 14px;
        }

        .zeta-filter-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(240px, 1.1fr) minmax(220px, .9fr) minmax(220px, .9fr) minmax(140px, auto);
            min-width: 0;
        }

        .zeta-filter-field {
            min-width: 0;
            position: relative;
        }

        .zeta-filter-label {
            color: #1f2f46;
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .zeta-date-input,
        .zeta-filter-select .btnmultiselect {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 7px !important;
            color: #1f2f46;
            font-size: 15px;
            height: 46px;
            outline: none;
            padding: 10px 12px;
            width: 100%;
        }

        .zeta-filter-select .btnmultiselect {
            align-items: center;
            cursor: pointer;
            display: flex !important;
            justify-content: space-between;
            max-width: 100%;
            min-width: 0;
        }

        .zeta-filter-select .arrow {
            align-items: center;
            background: #4936cf !important;
            border-radius: 6px;
            color: #ffffff;
            display: inline-flex;
            height: 28px;
            justify-content: center;
            margin-left: 10px;
            width: 30px;
        }

        .zeta-filter-select .list-items {
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            max-height: 260px;
            overflow-y: auto;
            padding: 8px !important;
            width: 100% !important;
        }

        .zeta-filter-select .list-items li {
            border-radius: 6px;
            color: #1f2f46;
            font-size: 14px !important;
            padding: 9px 8px !important;
        }

        .zeta-filter-select .list-items label {
            color: #1f2f46;
            font-size: 14px !important;
            margin: 0;
        }

        .zeta-consult-btn {
            align-items: center;
            align-self: end;
            background: #4936cf;
            border: 1px solid #4936cf;
            border-radius: 7px;
            color: #ffffff;
            display: inline-flex;
            font-size: 15px;
            font-weight: 700;
            gap: 8px;
            height: 46px;
            justify-content: center;
            padding: 10px 18px;
            transition: background .18s ease, transform .18s ease;
            width: 100%;
        }

        .zeta-consult-btn:hover {
            background: #3f2fc2;
            transform: translateY(-1px);
        }

        .zeta-toolbar {
            border-top: 1px solid #d9e1ea;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            padding-top: 14px;
        }

        .zeta-toolbar-btn {
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            color: #1f2f46;
            display: inline-flex;
            font-size: 15px;
            font-weight: 600;
            gap: 8px;
            min-height: 44px;
            padding: 9px 14px;
        }

        .zeta-toolbar-btn--primary {
            background: #4936cf;
            border-color: #4936cf;
            color: #ffffff;
        }

        .zeta-info-panel {
            margin-bottom: 18px;
            padding: 14px 16px;
        }

        .zeta-info-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .zeta-info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 10px 12px;
        }

        .zeta-info-label {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .zeta-info-value {
            color: #1f2f46;
            font-size: 15px;
            font-weight: 600;
        }

        .zeta-section-title {
            border-bottom: 1px solid #d9e1ea;
            color: #1f2f46;
            font-size: 20px;
            font-weight: 700;
            margin: 22px 0 12px;
            padding-bottom: 8px;
        }

        .zeta-summary-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            min-width: 0;
        }

        .zeta-summary-grid > div {
            min-width: 0;
        }

        .zeta-summary-card {
            max-width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .zeta-summary-card + .zeta-summary-card {
            margin-top: 14px;
        }

        .zeta-card-title {
            background: #f6f8fb;
            border-bottom: 1px solid #d9e1ea;
            color: #1f2f46;
            font-size: 17px;
            font-weight: 700;
            margin: 0;
            padding: 12px 14px;
        }

        .zeta-summary-table {
            color: #1f2f46;
            font-size: 15px;
            margin: 0;
            width: 100%;
        }

        .zeta-summary-table td,
        .zeta-summary-table th {
            border-bottom: 1px solid #e2e8f0;
            padding: 11px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .zeta-summary-table th {
            background: #f6f8fb;
            font-weight: 700;
        }

        .zeta-summary-table tr:last-child td {
            border-bottom: 0;
        }

        .zeta-summary-table td:last-child,
        .zeta-summary-table th:last-child {
            text-align: right;
        }

        .zeta-value {
            color: #1f2f46;
            font-weight: 600;
            white-space: nowrap;
        }

        .zeta-highlight td {
            background: #eef6ff;
            color: #1d4ed8;
            font-size: 16px;
            font-weight: 700;
        }

        .zeta-positive td {
            background: #ecfdf5;
            color: #047857;
            font-size: 16px;
            font-weight: 700;
        }

        .zeta-tax-title {
            color: #1f2f46;
            font-size: 18px;
            font-weight: 700;
            margin: 18px 0 10px;
        }

        @media (max-width: 1100px) {
            .zeta-filter-grid,
            .zeta-summary-grid {
                grid-template-columns: 1fr;
            }

            .zeta-consult-btn {
                align-self: stretch;
            }
        }

        @media (max-width: 768px) {
            .zeta-detail-page {
                max-width: 100%;
                overflow-x: hidden;
                padding: 14px 10px 86px;
            }

            .zeta-detail-title {
                font-size: 22px;
            }

            .zeta-toolbar-btn {
                flex: 1 1 100%;
                justify-content: center;
                min-height: 52px;
            }

            .zeta-info-grid {
                grid-template-columns: 1fr;
            }
            .zeta-summary-card {
                overflow-x: hidden;
                width: 100%;
            }

            .zeta-summary-table {
                min-width: 0;
                table-layout: fixed;
                width: 100%;
            }

            .zeta-summary-table td,
            .zeta-summary-table th {
                font-size: 14px;
                line-height: 1.25;
                padding: 10px 12px;
                white-space: normal;
                word-break: break-word;
            }

            .zeta-summary-table td:first-child,
            .zeta-summary-table th:first-child {
                width: 54%;
            }

            .zeta-summary-table td:last-child,
            .zeta-summary-table th:last-child {
                width: 46%;
            }


            .zeta-summary-table--three {
                table-layout: auto;
            }

            .zeta-summary-table--three td,
            .zeta-summary-table--three th {
                font-size: 13px;
                overflow-wrap: normal;
                padding-left: 8px;
                padding-right: 8px;
                white-space: nowrap;
                word-break: normal;
            }

            .zeta-summary-table--three td:first-child,
            .zeta-summary-table--three th:first-child,
            .zeta-summary-table--three td:last-child,
            .zeta-summary-table--three th:last-child {
                width: auto;
            }
            .zeta-value {
                white-space: normal;
            }

            .zeta-filter-panel,
            .zeta-info-panel,
            .zeta-summary-card {
                width: 100%;
            }
        }
    </style>

    <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>

    <div class="zeta-detail-page">
        <a href="/admin/caja/zetadiario" class="zeta-detail-back" title="Volver a Zeta diarios">
            <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
            <span class="sr-only">Atrás</span>
        </a>

        <div class="zeta-detail-header">
            <div>
                <h4 class="zeta-detail-title">Detalle del zeta diario</h4>
                <p class="zeta-detail-subtitle">Filtre por rango de fechas, caja y facturador para consultar el resumen del cierre.</p>
            </div>
        </div>

        <div class="zeta-filter-panel">
            <div class="zeta-filter-grid">
                <label class="zeta-filter-field">
                    <span class="zeta-filter-label">Rango de fechas</span>
                    <input type="text" class="zeta-date-input" name="datetimes" />
                </label>

                <div class="zeta-filter-field zeta-filter-select content-dropdawn">
                    <span class="zeta-filter-label">Caja</span>
                    <div class="btnmultiselect">
                        <span class="btnmultiselect-text">Seleccionar Caja</span>
                        <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span>
                    </div>
                    <ul class="list-items z-10 hidden bg-white shadow-sm">
                        <?php foreach($cajas as $value): ?>
                        <li class="flex items-center gap-2">
                            <input class="selectedcajas scale-125" type="checkbox" id="caja<?php echo $value->id;?>" value="<?php echo $value->id;?>" checked>
                            <label for="caja<?php echo $value->id;?>"><?php echo $value->nombre;?></label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="zeta-filter-field zeta-filter-select content-dropdawn">
                    <span class="zeta-filter-label">Facturador</span>
                    <div class="btnmultiselect">
                        <span class="btnmultiselect-text">Seleccionar Facturador</span>
                        <span class="arrow"><i class="fa-solid fa-chevron-down"></i></span>
                    </div>
                    <ul class="list-items z-10 hidden bg-white shadow-sm">
                        <?php foreach($consecutivos as $value): ?>
                        <li class="flex items-center gap-2">
                            <input class="facturador scale-125" type="checkbox" id="<?php echo $value->id;?>" value="<?php echo $value->id;?>" checked>
                            <label for="<?php echo $value->id;?>"><?php echo $value->nombre;?></label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <button id="consultarZDiario" class="zeta-consult-btn">
                    <span class="material-symbols-outlined">search</span>
                    <span>Consultar</span>
                </button>
            </div>
        </div>

        <div class="zeta-toolbar">
            <button class="zeta-toolbar-btn zeta-toolbar-btn--primary"><span class="material-symbols-outlined">print</span>Imprimir cierre</button>
            <button class="zeta-toolbar-btn"><span class="material-symbols-outlined">email</span>Enviar notificación</button>
        </div>

        <div class="zeta-info-panel">
            <div class="zeta-info-grid">
                <div class="zeta-info-item">
                    <span class="zeta-info-label">Caja</span>
                    <span id="cajastext" class="zeta-info-value"><?php echo $cajaselected;?><span class="text-transparent">.</span></span>
                </div>
                <div class="zeta-info-item">
                    <span class="zeta-info-label">Fecha inicio</span>
                    <span id="fechainicio" class="zeta-info-value"><?php echo $cierreselected->fechainicio??''; ?></span>
                </div>
                <div class="zeta-info-item">
                    <span class="zeta-info-label">Fecha cierre</span>
                    <span id="fechafin" class="zeta-info-value"><?php echo $cierreselected->fechacierre??''; ?></span>
                </div>
            </div>
        </div>

        <h5 class="zeta-section-title">Resumen</h5>

        <div class="zeta-summary-grid">
            <div>
                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Medios de pago</h6>
                    <table class="zeta-summary-table" width="100%" id="tablaMediosPago">
                        <tbody>
                            <?php foreach($discriminarmediospagos as $value):  ?>
                                <tr>
                                    <td><?php echo $value['mediopago']; ?></td>
                                    <td class="zeta-value">$<?php echo number_format($value['valor'], '0', ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Detalle tributario</h6>
                    <table class="zeta-summary-table zeta-summary-table--three" width="100%">
                        <thead>
                            <tr>
                                <th>Tarifa</th>
                                <th>Base Gravable</th>
                                <th>Impuesto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($discriminarimpuestos as $index => $value): ?>
                            <tr>
                                <td><?php echo $value['tarifa']!=null?$value['tarifa'].'%':'Excluido';?></td>
                                <td class="zeta-value">$ <?php echo number_format($value['basegravable'], '2', ',', '.');?></td>
                                <td class="zeta-value">$ <?php echo number_format($value['valorimpuesto'], "2", ",", ".");?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Consolidado de impuesto</h6>
                    <table class="zeta-summary-table" width="100%">
                        <tbody>
                            <tr>
                                <td>Base Total</td>
                                <td id="base" class="zeta-value">$<?php echo $cierreselected?number_format($cierreselected->ingresoventas-$cierreselected->valorimpuestototal, '0', ',', '.'):'0'; ?></td>
                            </tr>
                            <tr>
                                <td>Impuesto Total</td>
                                <td id="valorImpuestoTotal" class="zeta-value">$<?php echo number_format($cierreselected->valorimpuestototal??'0','0', ',', '.');?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Datos de venta</h6>
                    <table class="zeta-summary-table" width="100%">
                        <tbody>
                            <tr>
                                <td>Ingreso de ventas</td>
                                <td id="ingresoVentas" class="zeta-value">+ $<?php echo number_format($cierreselected->ingresoventas??'0', '0', ',', '.');?></td>
                            </tr>
                            <tr>
                                <td>Total descuentos</td>
                                <td id="totalDescuentos" class="zeta-value">- $<?php echo number_format($cierreselected->totaldescuentos??'0', '0', ',', '.');?></td>
                            </tr>
                            <tr class="zeta-highlight">
                                <td>Real de ventas</td>
                                <td id="realVentas">= $<?php echo number_format($cierreselected->realventas??'0', '0', ',', '.');?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Tipo de facturas</h6>
                    <table class="zeta-summary-table" width="100%">
                        <tbody>
                            <tr>
                                <td>Facturas electrónicas</td>
                                <td id="cantidadElectronicas" class="zeta-value"><?php echo $cierreselected->facturaselectronicas??'0';?></td>
                            </tr>
                            <tr>
                                <td>Facturas POS</td>
                                <td id="cantidadPOS" class="zeta-value"><?php echo $cierreselected->facturaspos??'0';?></td>
                            </tr>
                            <tr class="zeta-positive">
                                <td>Total facturas</td>
                                <td><?php echo $cierreselected?number_format($cierreselected->facturaspos+$cierreselected->facturaselectronicas, '0', ',', '.'):'0';?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="zeta-summary-card">
                    <h6 class="zeta-card-title">Ingreso por tipo de facturas</h6>
                    <table class="zeta-summary-table" width="100%">
                        <tbody>
                            <tr>
                                <td>Facturas electrónicas</td>
                                <td id="valorElectronicas" class="zeta-value">$<?php echo number_format($cierreselected->valorfe??0, '0', ',', '.');?></td>
                            </tr>
                            <tr>
                                <td>Facturas POS</td>
                                <td id="valorPOS" class="zeta-value">$<?php echo number_format($cierreselected->valorpos??0, '0', ',', '.');?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>