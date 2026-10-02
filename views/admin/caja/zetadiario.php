<div class="box">
    <style>
        .zeta-page {
            padding: 18px 20px 26px;
        }

        .zeta-back {
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

        .zeta-back:hover {
            background: #382db8;
            transform: translateY(-1px);
        }

        .zeta-header {
            align-items: flex-end;
            border-bottom: 2px solid #5145f0;
            display: flex;
            gap: 18px;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .zeta-title {
            color: #1f2f46;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.15;
            margin: 0;
        }

        .zeta-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 4px;
        }

        .zeta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .zeta-action {
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            color: #1f2f46;
            display: inline-flex;
            font-size: 15px;
            font-weight: 600;
            gap: 8px;
            min-height: 48px;
            padding: 10px 16px;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .zeta-action:hover {
            border-color: #5145f0;
            box-shadow: 0 8px 18px rgba(31, 47, 70, .08);
            transform: translateY(-1px);
        }

        .zeta-action--primary {
            background: #4936cf;
            border-color: #4936cf;
            color: #ffffff;
        }

        .zeta-action--primary:hover {
            background: #3f2fc2;
            border-color: #3f2fc2;
        }

        .zeta-action .material-symbols-outlined {
            font-size: 24px;
            line-height: 1;
        }

        .zeta-table-wrap {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            overflow-x: auto;
        }

        .zeta-table {
            color: #1f2f46;
            font-size: 15px;
            margin: 0 !important;
        }

        .zeta-table thead th {
            background: #f6f8fb;
            color: #1f2f46;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .zeta-table tbody td {
            border-top: 1px solid #e2e8f0;
            padding: 12px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .zeta-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .zeta-number {
            color: #1f2f46;
            font-weight: 600;
            text-align: right !important;
            width: 90px;
        }

        .zeta-action-table {
            align-items: center;
            background: #10bfae;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            min-height: 34px;
            min-width: 54px;
            padding: 7px 12px;
        }

        .zeta-action-table:hover {
            background: #0da697;
        }

        @media (max-width: 768px) {
            .zeta-page {
                padding: 14px 12px 86px;
            }

            .zeta-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .zeta-title {
                font-size: 22px;
            }

            .zeta-action {
                flex: 1 1 100%;
                justify-content: center;
                min-height: 54px;
            }

            .zeta-table {
                font-size: 14px;
            }
        }
    </style>

    <div class="zeta-page">
        <a href="/admin/caja" class="zeta-back" title="Volver a Caja">
            <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
            <span class="sr-only">Atrás</span>
        </a>

        <div class="zeta-header">
            <div>
                <h4 class="zeta-title">Zeta diarios</h4>
                <p class="zeta-subtitle">Consulte el zeta del día o revise cierres anteriores por fecha.</p>
            </div>
        </div>

        <div class="zeta-actions">
            <a title="Zeta diario de las cajas abiertas" class="zeta-action zeta-action--primary" href="/admin/caja/fechazetadiario?id=<?php echo $idultimocierreabierto; ?>">
                <span class="material-symbols-outlined">subject</span>
                <span>Zeta diario de hoy</span>
            </a>
            <a id="zcalendario" href="/admin/caja/fechazetadiario?id=0" class="zeta-action">
                <span class="material-symbols-outlined">calendar_month</span>
                <span>Zeta diario por fecha</span>
            </a>
        </div>

        <div class="zeta-table-wrap">
            <table class="display responsive nowrap tabla zeta-table min-w-[700px]" width="100%" id="tablaempleados">
                <thead>
                    <tr>
                        <th class="zeta-number">Nº</th>
                        <th class="zeta-number">N. Cierre</th>
                        <th>Caja</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th class="accionesth">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ultimoscierres as $index => $value): ?>
                        <tr>
                            <td class="zeta-number"><?php echo $index + 1;?></td>
                            <td class="zeta-number"><?php echo $value->id;?></td>
                            <td><?php echo $value->nombrecaja;?></td>
                            <td><?php echo $value->fechainicio;?></td>
                            <td><?php echo $value->fechacierre;?></td>
                            <td class="accionestd">
                                <div class="acciones-btns" id="<?php echo $value->id;?>">
                                    <a class="zeta-action-table" href="/admin/caja/fechazetadiario?id=<?php echo $value->id;?>">Ver</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>