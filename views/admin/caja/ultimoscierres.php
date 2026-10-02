<div class="box">
    <style>
        .cierres-page,
        .cierres-page * {
            box-sizing: border-box;
        }

        .cierres-page {
            max-width: 100%;
            overflow-x: hidden;
            padding: 18px 20px 28px;
        }

        .cierres-back {
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

        .cierres-back:hover {
            background: #382db8;
            transform: translateY(-1px);
        }

        .cierres-header {
            border-bottom: 2px solid #5145f0;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .cierres-title {
            color: #1f2f46;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.15;
            margin: 0;
        }

        .cierres-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 4px;
        }

        .cierres-table-wrap {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            max-width: 100%;
            overflow-x: auto;
        }

        .cierres-table {
            color: #1f2f46;
            font-size: 15px;
            margin: 0 !important;
            min-width: 880px;
            width: 100%;
        }

        .cierres-table thead th {
            background: #f6f8fb;
            color: #1f2f46;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .cierres-table tbody td {
            border-top: 1px solid #e2e8f0;
            padding: 12px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .cierres-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .cierres-number,
        .cierres-money {
            color: #1f2f46;
            text-align: right !important;
            white-space: nowrap;
        }

        .cierres-number {
            font-weight: 600;
        }

        .cierres-money {
            font-weight: 700;
        }

        .cierres-action-cell {
            min-width: 86px;
        }

        .cierres-action {
            align-items: center;
            background: #10bfae;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            min-height: 34px;
            min-width: 56px;
            padding: 7px 12px;
        }

        .cierres-action:hover {
            background: #0da697;
        }

        @media (max-width: 768px) {
            .cierres-page {
                padding: 14px 10px 86px;
            }

            .cierres-title {
                font-size: 22px;
            }

            .cierres-table {
                font-size: 14px;
                min-width: 760px;
            }

            .cierres-table thead th,
            .cierres-table tbody td {
                padding: 10px 12px;
            }
        }
    </style>

    <div class="cierres-page">
        <a href="/admin/caja" class="cierres-back" title="Volver a Caja">
            <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
            <span class="sr-only">Atrás</span>
        </a>

        <div class="cierres-header">
            <h4 class="cierres-title">Últimos cierres</h4>
            <p class="cierres-subtitle">Consulte los cierres recientes de caja y acceda al detalle de cada corte.</p>
        </div>

        <div class="cierres-table-wrap">
            <table class="display responsive nowrap tabla cierres-table" width="100%" id="">
                <thead>
                    <tr>
                        <th class="cierres-number">N. Cierre</th>
                        <th>Caja</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th class="cierres-money">Total Ventas</th>
                        <th>Usuario</th>
                        <th class="accionesth cierres-action-cell">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ultimoscierres as $index => $value): ?>
                    <tr>
                        <td class="cierres-number"><?php echo $index + 1;?></td>
                        <td><?php echo $value->idcaja?$value->nombrecaja:'Caja eliminada';?></td>
                        <td><?php echo $value->fechainicio;?></td>
                        <td><?php echo $value->fechacierre;?></td>
                        <td class="cierres-money">$<?php echo number_format($value->ingresoventas + $value->creditocapital, "0", ",", ".");?></td>
                        <td><?php echo $value->nombreusuario;?></td>
                        <td class="accionestd cierres-action-cell">
                            <div class="acciones-btns" id="<?php echo $value->id;?>">
                                <a class="cierres-action" href="/admin/caja/detallecierrecaja?id=<?php echo $value->id;?>">Ver</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>