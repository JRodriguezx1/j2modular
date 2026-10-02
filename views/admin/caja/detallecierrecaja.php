<div class="bg-white p-6 rounded-lg shadow detallecierrecaja">
    <style>
        .detallecierrecaja,
        .detallecierrecaja * {
            box-sizing: border-box;
        }

        .detallecierrecaja {
            color: #1f2f46;
            max-width: 100%;
            overflow-x: hidden;
            padding: 18px 20px 28px !important;
        }

        .detallecierrecaja > a:first-of-type {
            align-items: center;
            background: #4a3bd8 !important;
            border-radius: 8px !important;
            color: #ffffff;
            display: inline-flex;
            height: 42px;
            justify-content: center;
            margin-bottom: 14px;
            padding: 0 !important;
            transition: background .18s ease, transform .18s ease;
            width: 42px;
        }

        .detallecierrecaja > a:first-of-type:hover {
            background: #382db8 !important;
            transform: translateY(-1px);
        }

        .detallecierrecaja > h4 {
            border-bottom: 2px solid #5145f0;
            color: #1f2f46 !important;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.15;
            margin: 0 0 16px !important;
            padding-bottom: 10px;
        }

        .detallecierrecaja .btn-command {
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            color: #1f2f46;
            display: inline-flex;
            font-size: 15px;
            font-weight: 600;
            gap: 8px;
            justify-content: center;
            min-height: 48px;
            padding: 10px 15px;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .detallecierrecaja .btn-command:hover {
            border-color: #5145f0;
            box-shadow: 0 8px 18px rgba(31, 47, 70, .08);
            transform: translateY(-1px);
        }

        .detallecierrecaja .btn-command:first-child {
            background: #4936cf !important;
            border-color: #4936cf;
            color: #ffffff !important;
        }


        .detalle-actions {
            border-top: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            padding-top: 2px;
        }

        .detalle-action {
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: .65rem;
            color: #334155;
            display: inline-flex;
            flex-direction: column;
            font-size: 15px;
            font-weight: 600;
            gap: 7px;
            justify-content: center;
            line-height: 1.15;
            min-height: 78px;
            min-width: 118px;
            padding: 10px 13px;
            text-align: center;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .detalle-action:hover {
            background: #f8fafc;
            border-color: #6366f1;
            color: #4338ca;
            transform: translateY(-1px);
        }

        .detalle-action .material-symbols-outlined,
        .detalle-action .fa-brands {
            font-size: 28px;
            line-height: 1;
        }

        .detalle-action--primary {
            background: #4338ca;
            border-color: #4338ca;
            color: #ffffff;
        }

        .detalle-action--primary:hover {
            background: #3730a3;
            border-color: #3730a3;
            color: #ffffff;
        }
        .detallecierrecaja .accordion > label.etiqueta,
        .detallecierrecaja h5 {
            border-bottom: 1px solid #d9e1ea;
            color: #1f2f46 !important;
            display: block;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0;
            margin: 22px 0 12px !important;
            padding-bottom: 8px;
            text-align: left !important;
            text-transform: none !important;
        }

        .detallecierrecaja .flex.gap-4.mb-4 {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            box-shadow: 0 8px 18px rgba(31, 47, 70, .04);
            margin-bottom: 16px !important;
            padding: 14px 16px;
        }

        .detallecierrecaja .flex.gap-4.mb-4 p {
            color: #1f2f46 !important;
            font-size: 15px !important;
            line-height: 1.65 !important;
        }

        .detallecierrecaja .flex.gap-4.mb-4 p.font-semibold {
            color: #64748b !important;
            font-weight: 700 !important;
        }

        .detallecierrecaja .tabla2 {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-collapse: separate;
            border-radius: 7px;
            border-spacing: 0;
            color: #1f2f46;
            font-size: 15px;
            margin-bottom: 14px;
            overflow: hidden;
            width: 100%;
        }

        .detallecierrecaja .tabla2 thead th {
            background: #f6f8fb !important;
            border-bottom: 1px solid #d9e1ea;
            color: #1f2f46 !important;
            font-size: 16px;
            font-weight: 700;
            padding: 12px 14px !important;
            text-align: left !important;
        }

        .detallecierrecaja .tabla2 tbody td {
            border-bottom: 1px solid #e2e8f0;
            color: #1f2f46;
            padding: 11px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .detallecierrecaja .tabla2 tbody tr:last-child td {
            border-bottom: 0;
        }

        .detallecierrecaja .tabla2 td:last-child,
        .detallecierrecaja .tabla2 th:last-child {
            text-align: right !important;
        }

        .detallecierrecaja .tabla2 strong {
            font-weight: 600;
        }

        .detallecierrecaja .mt-32 {
            margin-top: 22px !important;
        }

        .detallecierrecaja .mb-12 {
            margin-bottom: 14px !important;
        }

        .detallecierrecaja .text-sky-400.font-bold,
        .detallecierrecaja .text-sky-400.font-medium {
            color: #1f2f46 !important;
            font-size: 18px;
            font-weight: 700;
            margin: 18px 0 10px;
            text-align: left !important;
        }

        .detallecierrecaja #tablaListaPedidos {
            color: #1f2f46;
            font-size: 15px;
            min-width: 980px;
        }

        .detallecierrecaja #tablaListaPedidos thead th {
            background: #f6f8fb !important;
            color: #1f2f46 !important;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 14px !important;
            text-align: left !important;
        }

        .detallecierrecaja #tablaListaPedidos tbody td {
            border-top: 1px solid #e2e8f0;
            padding: 10px 13px;
            vertical-align: middle;
        }

        .detallecierrecaja #tablaListaPedidos tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .detallecierrecaja .btn-xs {
            align-items: center;
            border-radius: 6px;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            min-height: 32px;
            min-width: 44px;
            padding: 6px 10px;
        }

        .detallecierrecaja .accionestd .acciones-btns {
            display: flex;
            gap: 6px;
            justify-content: center;
        }



        .detalle-analysis-table {
            min-width: 620px;
            table-layout: fixed !important;
        }

        .detalle-analysis-table th:nth-child(1),
        .detalle-analysis-table td:nth-child(1) {
            width: 24%;
        }

        .detalle-analysis-table th:nth-child(2),
        .detalle-analysis-table td:nth-child(2) {
            width: 24%;
            text-align: right !important;
            white-space: nowrap;
        }

        .detalle-analysis-table th:nth-child(3),
        .detalle-analysis-table td:nth-child(3) {
            width: 29%;
            text-align: right !important;
            white-space: nowrap;
        }

        .detalle-analysis-table th:nth-child(4),
        .detalle-analysis-table td:nth-child(4) {
            width: 23%;
            text-align: right !important;
            white-space: nowrap;
        }
        .detalle-user-sales-wrap {
            max-width: 100%;
            overflow-x: auto;
        }

        .detalle-user-sales-table {
            min-width: 560px;
            table-layout: auto !important;
        }

        .detalle-user-sales-table th,
        .detalle-user-sales-table td {
            white-space: normal;
            word-break: normal;
        }

        .detalle-user-sales-table th:nth-child(2),
        .detalle-user-sales-table td:nth-child(2),
        .detalle-user-sales-table th:nth-child(3),
        .detalle-user-sales-table td:nth-child(3),
        .detalle-user-sales-table th:nth-child(4),
        .detalle-user-sales-table td:nth-child(4) {
            text-align: right !important;
            white-space: nowrap;
        }

        .detalle-ventas-title {
            border-bottom: 1px solid #d9e1ea;
            color: #1f2f46;
            font-size: 20px;
            font-weight: 700;
            margin: 24px 0 12px;
            padding-bottom: 8px;
            text-align: left;
        }

        .detalle-ventas-wrap {
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 7px;
            max-width: 100%;
            overflow-x: auto;
        }

        .detalle-sales-table {
            color: #1f2f46;
            font-size: 15px;
            margin: 0 !important;
            min-width: 1040px;
            width: 100%;
        }

        .detalle-sales-table thead th {
            background: #f6f8fb !important;
            color: #1f2f46 !important;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 14px !important;
            text-align: left !important;
            vertical-align: middle;
        }

        .detalle-sales-table tbody td {
            border-top: 1px solid #e2e8f0;
            padding: 14px 14px !important;
            text-align: left;
            vertical-align: middle;
        }

        .detalle-sales-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .detalle-sales-badge {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 700;
            justify-content: center;
            line-height: 1;
            min-height: 28px;
            padding: 7px 11px;
            white-space: nowrap;
        }

        .detalle-sales-badge--neutral { background: #f1f5f9; color: #334155; }
        .detalle-sales-badge--success { background: #dcfce7; color: #047857; }
        .detalle-sales-badge--warning { background: #fef3c7; color: #b45309; }
        .detalle-sales-badge--info { background: #cffafe; color: #0e7490; }
        .detalle-sales-badge--indigo { background: #e0e7ff; color: #4338ca; }

        .detalle-sales-money {
            color: #1f2f46;
            font-weight: 600;
            text-align: right !important;
            white-space: nowrap;
        }

        .detalle-sales-action {
            align-items: center;
            background: #14b8a6;
            border: 0;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            min-height: 34px;
            min-width: 52px;
            padding: 7px 12px;
        }

        .detalle-sales-action:hover { background: #0f766e; color: #ffffff !important; }

        .detalle-sales-action--icon {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1f2f46 !important;
            min-width: 36px;
            padding-left: 9px;
            padding-right: 9px;
        }

        .detalle-sales-action--icon:hover { background: #f8fafc; color: #1f2f46 !important; }
        @media (max-width: 768px) {
            .detallecierrecaja {
                padding: 14px 10px 86px !important;
            }

            .detallecierrecaja > h4 {
                font-size: 22px;
            }

            .detalle-actions {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .detalle-action {
                min-height: 70px;
                min-width: 0;
                width: 100%;
            }

            .detalle-action .material-symbols-outlined,
            .detalle-action .fa-brands {
                font-size: 25px;
            }
            .detallecierrecaja .btn-command {
                flex: 1 1 100%;
                min-height: 52px;
            }

            .detallecierrecaja .flex.gap-4.mb-4 {
                gap: 10px;
                overflow-wrap: anywhere;
            }

            .detallecierrecaja .flex.gap-4.mb-4 p {
                font-size: 14px !important;
            }

            .detallecierrecaja .tabla2 {
                table-layout: fixed;
            }

            .detallecierrecaja .tabla2 tbody td,
            .detallecierrecaja .tabla2 thead th {
                font-size: 14px;
                line-height: 1.25;
                padding: 10px 12px !important;
                white-space: normal;
                word-break: break-word;
            }

            .detallecierrecaja .tabla2 td:last-child,
            .detallecierrecaja .tabla2 th:last-child {
                width: 44%;
            }
        }
    </style>
    <a href="/admin/caja" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
    </a>
    <h4 class="text-gray-600 my-2">Detalle del cierre de caja</h4>
    
    <?php if($conflocal['permitir_ver_resumen_cierre_de_caja_confirmado']->valor_final == 1 || userPerfil() < 4 ){ ?>
    <div class="detalle-actions">
        <button id="btnImprimirDetalleCaja" class="detalle-action detalle-action--primary"><span class="material-symbols-outlined">print</span>Imprimir cierre</button>
        <button id="btnVerCierreWeb" class="detalle-action"><span class="material-symbols-outlined">developer_mode_tv</span>Visualizar cierre</button>
        <button id="btnVerCierreWs" class="detalle-action"><i class="fa-brands fa-whatsapp"></i>Whatsapp</button>
        <button class="detalle-action"><span class="material-symbols-outlined">email</span>Enviar notificación</button>
    </div>
    
    <div class="accordion pb-20">
       <input type="checkbox" id="first">
       <label class="etiqueta text-sky-400 text-center  font-bold uppercase" for="first">Resumen</label>  

        <div class="flex flex-col tlg:flex-row gap-12 mb-8">
            <div class="tlg:basis-1/2">

                <div class="flex gap-4 mb-4">
                    <div>
                        <p class="m-0 text-slate-500 text-xl font-semibold leading-loose">Cierre de caja: </p>
                        <p class="m-0 text-slate-500 text-xl font-semibold leading-loose">Caja: </p>
                        <p class="m-0 text-slate-500 text-xl font-semibold leading-loose">Cajero: </p>
                        <p class="m-0 text-slate-500 text-xl font-semibold leading-loose">Fecha inicio: </p>
                        <p class="m-0 text-slate-500 text-xl font-semibold leading-loose">Fecha cierre: </p>
                    </div>
                    <div>
                        <p class="m-0 text-slate-500 text-xl leading-loose">ID - <?php echo $ultimocierre->id;?></p>
                        <p class="m-0 text-slate-500 text-xl leading-loose"><?php echo $ultimocierre->nombrecaja;?></p>
                        <p class="m-0 text-slate-500 text-xl leading-loose"><?php echo $user['nombre'];?></p>
                        <p class="m-0 text-slate-500 text-xl leading-loose"><?php echo $ultimocierre->fechainicio;?></p>
                        <p class="m-0 text-slate-500 text-xl leading-loose"><?php echo $ultimocierre->fechacierre;?></p>
                    </div>
                </div>

                <table class="tabla2" width="100%" id="">
                    <thead>
                        <tr>
                            <th colspan="2" class="w-full bg-gray-100 text-gray-700 p-3 text-center">Cuadre de caja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>        
                            <td class="">Base + ingresos de caja</td> 
                            <td class="">+ $<?php echo number_format($ultimocierre->basecaja??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="">Ventas en efectivo</td> 
                            <td class="">+ $<?php echo number_format($ultimocierre->ventasenefectivo??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="">Abonos en efectivo</td> 
                            <td id="abonosEfectivo" class="">+ $<?php echo number_format($ultimocierre->abonosenefectivo??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="">Gastos de la caja</td> 
                            <td class="">- $<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="text-blue-400 font-medium">Dinero en caja</td> 
                            <td class="text-blue-400 font-medium">= $<?php echo number_format($ultimocierre->basecaja+$ultimocierre->ventasenefectivo+($ultimocierre->abonosenefectivo??0)-$ultimocierre->gastoscaja??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="">Domicilios</td> 
                            <td class="">- $<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="text-blue-600 font-medium">Real en caja</td> 
                            <td class="text-blue-600 font-medium">= $<?php echo number_format($ultimocierre->basecaja+$ultimocierre->ventasenefectivo+($ultimocierre->abonosenefectivo??0)-$ultimocierre->gastoscaja-$ultimocierre->domicilios??0, "0", ",", ".");?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- DETALLE DE IMPUESTOS-->
                <div class="mt-32 mb-12">
                    <p class="text-sky-400 text-center  font-bold">Detalle de Impuestos</p>
                    <table class="tabla2" width="100%" id="tablaMediosPago">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 p-3 text-center">
                                <th>Tarifa</th>
                                <th>Base Gravable</th>
                                <th>Impuesto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($discriminarimpuesto as $index => $value): ?>
                            <tr>
                                <td class=""><?php echo $value['tarifa'];?>%</td>     
                                <td class=""><strong>$ </strong><?php echo number_format($value['basegravable'], '2', ',', '.');?></td>
                                <td class=""><strong>$ </strong><?php echo number_format($value['valorimpuesto'], "2", ",", ".");?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- DETALLE GASTOS DE LA CAJA-->
                <div class="mt-32 mb-12">
                    <p class="text-sky-400 text-center  font-bold">Detalle gastos de la caja</p>
                    <table class="tabla2" width="100%" id="tablaMediosPago">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 p-3 text-center">
                                <th>Categoria gasto</th>
                                <th>Valor gasto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($discriminargastos as $index => $value): ?>
                            <tr>
                                <td class=""><strong><?php echo $value['nombre'];?></td>
                                <td class=""><strong>$ </strong><?php echo number_format($value['valorgasto'], "2", ",", ".");?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- DETALLE ABONOS-->
                <div class="mt-32 mb-12">
                    <table class="tabla2" width="100%" id="tablaAbonos">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 p-3 text-center">
                                <th>Abonos</th>
                                <th>Abonos separados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class=""><strong>$ </strong><?php echo number_format($ultimocierre->abonos??0, "0", ",", ".");?></td>
                                <td class=""><strong>$ </strong><?php echo number_format($ultimocierre->abonosseparados??0, "0", ",", ".");?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tlg:basis-1/2">
                <table class="tabla2 mb-12" width="100%" id="">
                    <thead>
                        <tr>
                            <th colspan="2" class="w-full bg-gray-100 text-gray-700 p-3 text-center">Medios de pagos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($discriminarmediospagos as $index => $value): ?>
                        <tr>        
                            <td class=""><?php echo $value['mediopago'];?></td> 
                            <td class="">$ <?php echo number_format($value['valor'], "0", ",", ".");?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                
                <table class="tabla2" width="100%" id="">
                    <thead>
                        <tr>
                            <th colspan="2" class="w-full bg-gray-100 text-gray-700 p-3 text-center">Datos de venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>  
                            <td class="">Ventas de contado</td> 
                            <td id="ingresoVentasTotal" class=""> + $<?php echo number_format(($ultimocierre->ingresoventas??0), "0", ",", ".");?></td>
                        </tr>
                       <tr>
                            <td class="">Ventas a crédito</td>
                            <td id="creditos" class=""> + $<?php echo number_format($ultimocierre->creditocapital??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="">Total descuentos:</td>
                            <td id="totalDescuentos" class=""> $<?php echo number_format($ultimocierre->totaldescuentos??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="text-gray-800 font-medium">Ingreso total de ventas:<p class=" text-sm m-0 text-gray-500">(Ventas de contado + ventas a crédito)</p></td>
                            <td id="totaldeventas" class=""> $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->creditocapital??0), "0", ",", ".");?></td>
                        </tr>
                        <tr> 
                            <td 
                                class="" 
                                title="los abonos no aumentan las ventas, porque la venta ya fue registrada cuando se realizó el crédito, únicamente disminuyen la cartera pendiente y aumentan el dinero que entra a la caja."
                            >
                                Total abonos
                            </td> 
                            <td id="abonosTotales" class=""> + $<?php echo number_format($ultimocierre->abonostotales??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="text-gray-800 font-medium" title="dinero que realmente entrÃ³ o dinero recibido durante el día.">Ingreso de caja del dia: <p class=" text-base m-0 text-gray-500">(Ventas de contado + Abonos recibidos)</p></td>
                            <td id="ingresoCajaDelDia" class=""> $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->abonostotales??0), "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="">Total gastos de caja</td> 
                            <td id="totalGastosCaja" class=""> - $<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>  
                            <td class="">Total domicilios</td> 
                            <td id="totalDomicilios" class=""> - $<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></td>
                        </tr>
                        <tr> 
                            <td class="text-blue-400 font-medium">Utilidad del dia <p class=" text-base m-0 text-gray-500">(Ingreso total ventas - Gastos - Domicilios)</p></td> 
                            <td id="realVentas" class="text-blue-400 font-medium"> = $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->creditocapital??0)-($ultimocierre->domicilios??0)-($ultimocierre->gastoscaja??0), "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="text-blue-600 font-medium">Base gravable</td>
                            <td id="totalBaseGravable" class="text-blue-600 font-medium"> = $<?php echo number_format($ultimocierre->basegravable??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>
                            <td class="">Impuesto Total</td> 
                            <td id="impuestoTotal" class=""> - $<?php echo number_format($ultimocierre->valorimpuestototal??0, "2", ",", ".");?></td>
                        </tr>
                        <tr>     
                            <td class="text-gray-700 font-medium">Gastos otros/bancarios</td> 
                            <td id="otrosGastosBancarios" class="text-gray-700 font-medium"> - $<?php echo number_format($ultimocierre->gastosbanco??0, "0", ",", ".");?></td>
                        </tr>
                    </tbody>
                </table>
                
                <table class="tabla2 mb-12" width="100%" id="">
                    <thead>
                        <tr>
                            <th colspan="2" class="w-full bg-gray-100 text-gray-700 p-3 text-center">Tipo de facturas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>        
                            <td class="">Facturas electrónicas</td> 
                            <td class=""><?php echo number_format($ultimocierre->facturaselectronicas??0, "0", ",", ".");?></td>
                        </tr>
                        <tr>        
                            <td class="">Facturas POS</td> 
                            <td class=""><?php echo number_format($ultimocierre->facturaspos??0, "0", ",", ".");?></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="overflow-x-auto">
                    <p class="text-sky-400 font-bold text-center">Análisis Sobrantes y Faltantes</p>
                    <table class="tabla2 mb-12 min-w-[500px] detalle-analysis-table" width="100%" id="">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 p-3 text-center">
                                <th>Medios de pago</th>
                                <th> Sistema </th>
                                <th> Valor declarado </th>
                                <th> Diferencia </th>
                            </tr>
                        </thead>
                        <tbody class="cuerpoanalisis">
                            <?php foreach($sobrantefaltante as $index => $value): ?>
                            <tr class="<?php echo $value->nombremediopago=='Efectivo'?'!border-2 !border-indigo-600':'';?>">        
                                <td class=""><?php echo $value->nombremediopago;?></td> 
                                <td class="colsistem"><?php echo number_format($value->valorsistema, "0", ",", ".");?></td>
                                <td class="coldeclarado" data-mediopagoid="<?php echo $value->id_mediopago;?>"><?php echo number_format($value->valordeclarado, "0", ",", ".");?></td>
                                <td class="coldif"><?php echo number_format($value->valordeclarado-$value->valorsistema, "0", ",", ".");?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="detalle-user-sales-wrap">
                <table class="tabla2 mb-12 detalle-user-sales-table" width="100%" id="">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 p-3 text-center">
                            <th>Ventas por usuario</th>
                            <th>N°</th>
                            <th>Total</th>
                            <th>Comision</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ventasxusuarios as $index => $value):
                            $comision = "comision";
                            if(array_key_last($ventasxusuarios) == $index)$comision = "comision_negocio";
                        ?>
                        <tr>
                            <td class=""><?php echo $value['Nombre'];?></td> 
                            <td class=""><?php echo $value['N_ventas'];?></td>
                            <td class=""><strong>$ </strong><?php echo number_format($value['ventas'], 0, ",", ".");?></td>
                            <td class=""><strong>$ </strong><?php echo number_format($value[$comision], 0, ",", ".");?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            
            </div>
        </div>
        <h5 class="detalle-ventas-title">Ventas del día</h5>
        <!-- Facturas del día -->
        <div class="detalle-ventas-wrap">
            <table id="tablaListaPedidos" class="display responsive nowrap tabla detalle-sales-table w-full" width="100%">
                <thead>
                    <tr>
                        <th>N.</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Entrega</th>
                        <th>Factura</th>
                        <th>Medio pago</th>
                        <th>Estado</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                        <th class="accionesth">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($facturas as $index => $value): ?>
                    <tr>
                        <td><?php echo $index + 1;?></td>
                        <td><div class="w-32 whitespace-normal"><?php echo $value->fechapago;?></div></td>
                        <td><div class="w-48 whitespace-normal"><?php echo $value->cliente;?></div></td>
                        <td><span class="detalle-sales-badge <?php echo $value->entrega=='Domicilio'?(($value->estado == 'Paga' || $value->estado == 'Remision') && $value->entregado == 1?'detalle-sales-badge--success':'detalle-sales-badge--warning'):'detalle-sales-badge--neutral';?>"><?php echo $value->entrega;?></span></td>
                        <td><?php echo $value->id;?></td>
                        <td>
                            <div data-estado="<?php echo $value->estado;?>" data-totalpagado="<?php echo $value->total;?>" id="<?php echo $value->id;?>" class="mediosdepago max-w-full flex flex-wrap gap-2">
                                <?php foreach($value->mediosdepago as $idx => $element): ?>
                                <button class="detalle-sales-badge detalle-sales-badge--neutral"><?php echo $element->mediopago;?></button>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><span class="detalle-sales-badge <?php echo $value->estado=='Paga'&&$value->tipoventa=='Contado'?'detalle-sales-badge--success':($value->estado=='Paga'&& $value->tipoventa=='Credito'?'detalle-sales-badge--info':($value->estado=='Guardado'?'detalle-sales-badge--neutral':($value->estado=='Remision' || $value->estado=='Paga'&&$value->remision==1?'detalle-sales-badge--indigo':'detalle-sales-badge--neutral')));?>"><?php echo ($value->tipoventa =='Contado'||$value->tipoventa =='')?$value->estado:'Credito';?></span></td>
                        <td class="detalle-sales-money">$ <?php echo number_format($value->subtotal??0, "0", ",", ".");?></td>
                        <td class="detalle-sales-money">$ <?php echo number_format($value->total??0, "0", ",", ".");?></td>
                        <td class="accionestd">
                            <div class="acciones-btns" id="<?php echo $value->id;?>">
                                <a class="detalle-sales-action" href="/admin/caja/ordenresumen?id=<?php echo $value->id;?>">Ver</a>
                                <?php if($value->estado=='Paga'): ?>
                                    <button class="detalle-sales-action detalle-sales-action--icon printPOS"><i class="fa-solid fa-print"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div></div> <!-- fin accordion-->
    <?php }else{ ?>
        <label class="block mt-8 etiqueta text-sky-400 text-center  font-bold uppercase" for="first">Cierre de caja realizado</label>
     <?php }; ?>

     <script>
        const getParam = <?= json_encode($conflocal) ?>;
    </script>
</div>
