
<div class="relative pb-20">
  <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>
  <div class="box cerrarcaja">
    <a href="/admin/caja" class="inline-flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-700 text-white hover:bg-indigo-800">
        <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
        <span class="sr-only">Atrás</span>
    </a>
    <div class="mt-3 rounded-lg border border-slate-200 bg-white p-5">
    <h4 class="m-0 text-4xl font-semibold text-slate-700">Cierre de caja</h4>
    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 border-b border-slate-200 pb-4">
        <div>
            <p class="m-0 text-slate-600 text-xl font-semibold">Caja: </p>
            <p class="m-0 text-slate-600 text-xl font-semibold">Fecha: </p>
            <p class="m-0 text-slate-600 text-xl font-semibold">Cajero: </p>
        </div>
        <div>
            <p id="nombreCaja" class="m-0 text-slate-600 text-xl">Caja principal</p>
            <p class="m-0 text-slate-600 text-xl"><?php echo date('Y-m-d');?></p>
            <p class="m-0 text-slate-600 text-xl"><?php echo $user['nombre'];?></p>
        </div>
    </div>
    <div class="grid gap-4 mt-5 xlg:grid-cols-[minmax(34rem,1fr)_minmax(28rem,.85fr)]">
        <div class="border border-slate-200 bg-slate-50 p-4 declaracionvalores rounded-lg">
            <div class="formulario__campo">
                <label class="formulario__label !text-xl !font-semibold !text-slate-700" for="EF">Efectivo en caja</label>
                <div class="formulario__dato gap-x-[0.7rem]">
                    <input id="Efectivo" 
                        class="formulario__input inputmediopago bg-white border border-slate-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-16 text-2xl focus:outline-none focus:ring-1" 
                        type="text" 
                        placeholder="Dinero en caja" 
                        name="Efectivo" 
                        data-idmediopago="1" 
                        value=""
                        oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()"
                        required>
                    <button class="inline-flex h-16 min-w-36 shrink-0 items-center justify-center rounded-lg bg-teal-500 px-7 text-xl font-semibold text-white hover:bg-teal-600" id="btnArqueocaja">Arqueo</button>
                </div>
            </div>
            <?php foreach($mediospagos as $index => $value): 
                if($index>0):  ////////// declaracion de valores /////////?>
                <div class="formulario__campo">
                <label class="formulario__label !text-xl !font-semibold !text-slate-700" for="<?php echo $value->nick??'';?>"><?php echo $value->mediopago??'';?></label>
                
                <input class="formulario__input inputmediopago bg-white border border-slate-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-16 text-2xl focus:outline-none focus:ring-1" 
                    type="text" 
                    placeholder="Dinero en <?php echo $value->mediopago??'';?>" 
                    id="<?php echo $value->nick??'';?>" 
                    name="<?php echo $value->mediopago??'';?>" 
                    value=""
                    data-idmediopago="<?php echo $value->id;?>"
                    oninput="this.value = parseInt(this.value.replace(/[^\d.,]/g, '').replace(/[,.]/g, '')||0).toLocaleString()"
                    required
                >

                </div>
            <?php endif; endforeach; ?>
        </div> <!-- Fin col 1 -->
        <div class="grid gap-4 lg:grid-cols-[minmax(22rem,1fr)_minmax(18rem,.8fr)]">
            <div class="flex gap-5 rounded-lg border border-slate-200 bg-white p-4">
                <div class="text-start">
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">ID:</p>
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">Base en caja:</p>
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">Gastos:</p>
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">Domicilios:</p>
                    <?php if($conflocal['permitir_ver_resumen_cierre_de_caja']->valor_final == 1 || userPerfil() < 3 ): ?>
                    <p class="m-0 mb-3 text-slate-700 text-2xl font-semibold">Ventas Total:</p>
                    <?php endif; ?>
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">Nº Facturas:</p>
                    <p class="m-0 mb-3 text-slate-500 text-2xl font-normal">Cotizaciones:</p>
                </div>
                <div>
                    <p id="idCierrecaja" class="m-0 mb-3 text-slate-800 text-2xl font-medium"><?php echo $ultimocierre->id??'id';?></p>
                    <p id="basecajaResumen" class="m-0 mb-3 text-slate-800 text-2xl font-medium">$<?php echo number_format($ultimocierre->basecaja??0, "0", ",", ".");?></p>
                    <p id="gastoscajaResumen" class="m-0 mb-3 text-slate-800 text-2xl font-medium">$<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></p>
                    <p id="domiciliosResumen" class="m-0 mb-3 text-slate-800 text-2xl font-medium">$<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></p>
                    <?php if($conflocal['permitir_ver_resumen_cierre_de_caja']->valor_final == 1 || userPerfil() < 3 ): ?>
                    <p id="ingresoventasResumen" class="m-0 mb-3 text-slate-900 text-2xl font-bold">$<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->creditocapital??0), "0", ",", ".");?></p>
                    <?php endif; ?>
                    <p id="totalfacturasResumen" class="m-0 mb-3 text-slate-800 text-2xl font-medium"><?php echo $ultimocierre->totalfacturas??0;?></p>
                    <p id="totalcotizacionesResumen" class="m-0 mb-3 text-slate-800 text-2xl font-medium"><?php echo $ultimocierre->totalcotizaciones??0;?></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 rounded-lg border border-slate-200 bg-white p-4">
                <?php if(tienePermiso('Cerrar caja y generar arqueo')&&userPerfil()>3 || userPerfil()<4): ?>
                    <button id="btnCerrarcaja" class="cierre-action cierre-action--primary"><span class="material-symbols-outlined">keyboard_lock</span>Cerrar caja</button>
                <?php endif; ?>
                <?php if($conflocal['permitir_ver_resumen_cierre_de_caja']->valor_final == 1 || userPerfil() < 4 ): ?>
                <button id="btnImprimirDetalleCaja" class="cierre-action"><span class="material-symbols-outlined">print</span>Imprimir cierre</button>
                <?php endif; ?>
                <button id="btnCambiarCaja" class="cierre-action"><span class="material-symbols-outlined">change_circle</span>Cambiar caja</button>
                <?php if($conflocal['permitir_ver_resumen_cierre_de_caja']->valor_final == 1 || userPerfil() < 4 ): ?>
                <button id="btnVerCierreWeb" class="cierre-action"><span class="material-symbols-outlined">developer_mode_tv</span>Visualizar cierre</button>
                <?php endif; ?>
            </div>
        </div> <!-- Fin col 2 -->
    </div>
</div>

<style>
    .cierre-action {
        align-items: center;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        color: #475569;
        display: flex;
        flex-direction: column;
        font-size: 1.35rem;
        font-weight: 500;
        gap: .35rem;
        justify-content: center;
        line-height: 1.15;
        min-height: 7.2rem;
        padding: 1rem;
        text-align: center;
        transition: border-color .2s ease, box-shadow .2s ease, color .2s ease, transform .2s ease;
    }

    .cierre-action:hover {
        border-color: #4f46e5;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        color: #312e81;
        transform: translateY(-1px);
    }

    .cierre-action--primary {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
        font-weight: 600;
    }

    .cierre-action--primary:hover {
        background: #4338ca;
        color: #fff;
    }

    .cierre-action .material-symbols-outlined {
        font-size: 2.8rem;
        line-height: 1;
    }

    .resumen-toggle {
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        display: flex;
        font-size: 2.2rem;
        font-weight: 600;
        gap: 1rem;
        justify-content: space-between;
        margin-top: 3rem;
        padding: 0 0 1.2rem;
        text-transform: none;
    }

    .resumen-toggle::after {
        color: #64748b;
        content: "Cuadre, pagos y datos de venta";
        font-size: 1.35rem;
        font-weight: 500;
    }

    .resumen-grid {
        display: grid;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 1024px) {
        .resumen-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }
    }

    .resumen-stack {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .resumen-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-collapse: separate;
        border-radius: .75rem;
        border-spacing: 0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        overflow: hidden;
        width: 100%;
    }

    .resumen-panel thead th {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b !important;
        font-size: 1.65rem;
        font-weight: 600;
        padding: 1.25rem 1.6rem !important;
        text-align: left !important;
    }

    .resumen-panel td {
        border-bottom: 1px solid #e5e7eb;
        color: #334155;
        font-size: 1.45rem;
        padding: 1.15rem 1.6rem !important;
        text-align: left !important;
    }

    .resumen-panel tr:last-child td {
        border-bottom: 0;
    }

    .resumen-panel td:last-child {
        font-weight: 500;
        text-align: right !important;
        white-space: nowrap;
    }

    .resumen-key-label,
    .resumen-key-value {
        background: #f0fdf4;
        color: #047857 !important;
        font-size: 1.65rem !important;
        font-weight: 700 !important;
    }

    .resumen-key-label--blue,
    .resumen-key-value--blue {
        background: #eff6ff;
        color: #1d4ed8 !important;
    }

    .resumen-note {
        color: #64748b;
        display: block;
        font-size: 1.15rem;
        font-weight: 400;
        margin-top: .2rem;
    }

    .cierre-section-title {
        color: #334155;
        font-size: 2rem;
        font-weight: 600;
        margin: 2rem 0 1rem;
    }

    .cierre-detail-block {
        margin: 2rem 0;
    }

    .cierre-data-table {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-collapse: separate !important;
        border-radius: .75rem;
        border-spacing: 0;
        overflow: hidden;
        width: 100%;
    }

    .cierre-data-table thead tr {
        background: #f8fafc !important;
        color: #1e293b !important;
        text-align: left !important;
    }

    .cierre-data-table th,
    .cierre-data-table td {
        border-bottom: 1px solid #e5e7eb;
        color: #334155;
        font-size: 1.35rem;
        padding: 1rem 1.2rem !important;
        text-align: left !important;
        vertical-align: middle;
    }

    .cierre-data-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .cierre-number {
        font-variant-numeric: tabular-nums;
        text-align: right !important;
        white-space: nowrap;
    }

    .cierre-badge {
        align-items: center;
        border-radius: 999px;
        display: inline-flex;
        font-size: 1.2rem;
        font-weight: 600;
        justify-content: center;
        line-height: 1;
        min-height: 2.8rem;
        padding: .55rem 1rem;
        white-space: nowrap;
    }

    .cierre-badge--neutral {
        background: #f1f5f9;
        color: #334155;
    }

    .cierre-badge--success {
        background: #dcfce7;
        color: #047857;
    }

    .cierre-badge--warning {
        background: #fef3c7;
        color: #b45309;
    }

    .cierre-badge--info {
        background: #cffafe;
        color: #0e7490;
    }

    .cierre-badge--indigo {
        background: #e0e7ff;
        color: #4338ca;
    }

    .cierre-table-action {
        align-items: center;
        background: #14b8a6;
        border-radius: .55rem;
        color: #fff;
        display: inline-flex;
        font-size: 1.25rem;
        font-weight: 700;
        justify-content: center;
        min-height: 3.4rem;
        min-width: 5.4rem;
        padding: .7rem 1.1rem;
    }

    .resumen-grid,
    .resumen-stack,
    .accordion .wrapper,
    .accordion .wrapper-content,
    .accordion .content {
        max-width: 100%;
        min-width: 0;
    }

    .resumen-stack,
    .cierre-detail-block {
        -webkit-overflow-scrolling: touch;
        overflow-x: auto;
    }

    .resumen-stack::-webkit-scrollbar,
    .cierre-detail-block::-webkit-scrollbar {
        height: .6rem;
    }

    .resumen-stack::-webkit-scrollbar-thumb,
    .cierre-detail-block::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    @media (max-width: 767px) {
        .resumen-toggle {
            align-items: flex-start;
            flex-direction: column;
            font-size: 2rem;
            line-height: 1.15;
        }

        .resumen-toggle::after {
            font-size: 1.2rem;
        }

        .resumen-panel,
        .cierre-data-table {
            min-width: 44rem;
        }

        .resumen-panel thead th,
        .resumen-panel td,
        .cierre-data-table th,
        .cierre-data-table td {
            font-size: 1.25rem;
            padding: .9rem 1rem !important;
        }

        .resumen-key-label,
        .resumen-key-value {
            font-size: 1.35rem !important;
        }

        .cierre-section-title {
            font-size: 1.75rem;
        }
    }
    .cierre-table-action:hover {
        background: #0f766e;
        color: #fff;
    }

    #Modalcerrarcaja.cierre-confirm-modal {
        width: min(92vw, 58rem) !important;
        max-width: min(92vw, 58rem) !important;
    }

    #Modalcerrarcaja.cierre-confirm-modal .cierre-confirm-body {
        padding: 3rem 3.5rem 2.6rem;
    }

    #Modalcerrarcaja.cierre-confirm-modal .cierre-confirm-actions {
        padding: 2.2rem 3.5rem;
    }

    @media (max-width: 640px) {
        #Modalcerrarcaja.cierre-confirm-modal {
            width: min(94vw, 42rem) !important;
        }

        #Modalcerrarcaja.cierre-confirm-modal .cierre-confirm-body {
            padding: 2.4rem 2rem 2rem;
        }

        #Modalcerrarcaja.cierre-confirm-modal .cierre-confirm-actions {
            grid-template-columns: 1fr;
            padding: 1.8rem 2rem;
        }
    }
</style>


    <?php if($conflocal['permitir_ver_resumen_cierre_de_caja']->valor_final == 1 || userPerfil() < 4 ): ?>
    <div class="accordion">
        <input type="checkbox" id="first" checked>
        <label class="etiqueta resumen-toggle" for="first">Resumen</label>
        <div class="wrapper flex flex-col lg:flex-row gap-8">
            <div class="wrapper-content">
                <div class="content">

                    <div class="resumen-grid">
                        <div class="resumen-stack">
                            <table class="tabla2 resumen-panel" width="100%" id="">
                                <thead>
                                    <tr>
                                        <th colspan="2">Cuadre de caja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>        
                                        <td class="">Base + ingresos de caja</td> 
                                        <td id="baseIngresoCaja" class="">+ $<?php echo number_format($ultimocierre->basecaja??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="">Ventas en efectivo</td> 
                                        <td id="ventasEfectivo" class="">+ $<?php echo number_format($ultimocierre->ventasenefectivo??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="">Abonos en efectivo</td> 
                                        <td id="abonosEfectivo" class="">+ $<?php echo number_format($ultimocierre->abonosenefectivo??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="">Gastos de la caja</td> 
                                        <td id="gastosCaja" class="">- $<?php echo number_format($ultimocierre->gastoscaja??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="resumen-key-label">Dinero en caja</td> 
                                        <td id="dineroCaja" class="resumen-key-value">= $<?php echo number_format(($ultimocierre->basecaja??0)+($ultimocierre->ventasenefectivo??0)+($ultimocierre->abonosenefectivo??0)-($ultimocierre->gastoscaja??0), "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="">Domicilios</td> 
                                        <td id="domicilios" class="">- $<?php echo number_format($ultimocierre->domicilios??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="resumen-key-label resumen-key-label--blue">Real en caja</td> 
                                        <td id="realCaja" class="resumen-key-value resumen-key-value--blue">= $<?php echo number_format(($ultimocierre->basecaja??0)+($ultimocierre->ventasenefectivo??0)+($ultimocierre->abonosenefectivo??0)-($ultimocierre->gastoscaja??0)-($ultimocierre->domicilios??0), "0", ",", ".");?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- DETALLE DE IMPUESTOS-->
                            <div class="cierre-detail-block">
                                <p class="cierre-section-title">Detalle de Impuestos</p>
                                <table id="tablaDetalleImpuestos" class="tabla2 cierre-data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Tarifa</th>
                                            <th class="cierre-number">Base Gravable</th>
                                            <th class="cierre-number">Impuesto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($discriminarimpuesto as $index => $value): ?>
                                        <tr>
                                            <td class=""><?php echo $value['tarifa']!=null?$value['tarifa'].'%':'Excluido';?></td>     
                                            <td class="cierre-number"><strong>$ </strong><?php echo number_format($value['basegravable'], '2', ',', '.');?></td>
                                            <td class="cierre-number"><strong>$ </strong><?php echo number_format($value['valorimpuesto'], "2", ",", ".");?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- DETALLE GASTOS DE LA CAJA-->
                            <div class="cierre-detail-block">
                                <p class="cierre-section-title">Detalle gastos de la caja</p>
                                <table id="tablaDetalleCaja" class="tabla2 cierre-data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Categoria gasto</th>
                                            <th class="cierre-number">Valor gasto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($discriminargastos as $index => $value): ?>
                                        <tr>
                                            <td class=""><strong><?php echo $value['nombre'];?></td>
                                            <td class="cierre-number"><strong>$ </strong><?php echo number_format($value['valorgasto'], "2", ",", ".");?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- DETALLE ABONOS-->
                            <div class="cierre-detail-block">
                                <table id="tablaAbonos" class="tabla2 cierre-data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Abonos creditos</th>
                                            <th>Abonos separados</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="abonoscreditos" class="cierre-number"><strong>$ </strong><?php echo number_format($ultimocierre->abonoscreditos??0, "0", ",", ".");?></td>
                                            <td id="abonosseparados" class="cierre-number"> + <strong>$ </strong><?php echo number_format($ultimocierre->abonosseparados??0, "0", ",", ".");?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="resumen-stack">
                            <table id="tablaMediosPago" class="tabla2 resumen-panel" width="100%">
                                <thead>
                                    <tr>
                                        <th colspan="2">Medios de pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($discriminarmediospagos as $index => $value): ?>
                                    <tr>        
                                        <td class=""><?php echo $value['mediopago'];?></td>
                                        <td class=""> + <strong>$ </strong><?php echo number_format($value['valor'], "0", ",", ".");?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            
                            <table class="tabla2 resumen-panel" width="100%" id="tablaDatosVenta">
                                <thead>
                                    <tr>
                                        <th colspan="2">Datos de venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="">Ventas de contado</td>
                                        <td id="ingresoVentasTotal" class=""> + $<?php echo number_format(($ultimocierre->ingresoventas??0), "0", ",", ".");?></td>
                                    </tr>
                                    <tr>
                                        <td class="">Ventas a credito</td>
                                        <td id="creditos" class=""> + $<?php echo number_format($ultimocierre->creditocapital??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>
                                        <td class="">Total descuentos:</td>
                                        <td id="totalDescuentos" class=""> $<?php echo number_format($ultimocierre->totaldescuentos??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>
                                        <td class="resumen-key-label resumen-key-label--blue">Ingreso total de ventas:<span class="resumen-note">(Ventas de contado + ventas a credito)</span></td>
                                        <td id="totaldeventas" class="resumen-key-value resumen-key-value--blue"> $<?php echo number_format(($ultimocierre->ingresoventas??0)+($ultimocierre->creditocapital??0), "0", ",", ".");?></td>
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
                                        <td class="text-gray-800 font-medium" title="dinero que realmente entró o dinero recibido durante el día.">Ingreso de caja del dia: <p class=" text-base m-0 text-gray-500">(Ventas de contado + Abonos recibidos)</p></td>
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
                            
                            <table class="tabla2 cierre-data-table mb-12" width="100%" id="">
                                <thead>
                                    <tr>
                                        <th colspan="2">Tipo de facturas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>        
                                        <td class="">Facturas electronicas</td> 
                                        <td id="cantidadFacturasFE" class="cierre-number"><?php echo number_format($ultimocierre->facturaselectronicas??0, "0", ",", ".");?></td>
                                    </tr>
                                    <tr>        
                                        <td class="">Facturas POS</td> 
                                        <td id="cantidadFacturasPOS" class="cierre-number"><?php echo number_format($ultimocierre->facturaspos??0, "0", ",", ".");?></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="overflow-x-auto">
                                <p class="cierre-section-title">Analisis Sobrantes y Faltantes</p>
                                <table id="sobranteFaltante" class="tabla2 cierre-data-table w-full min-w-[500px] mb-12" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Medios de pago</th>
                                            <th class="cierre-number">Sistema</th>
                                            <th class="cierre-number">Valor declarado</th>
                                            <th class="cierre-number">Diferencia</th>
                                        </tr>
                                    </thead>
                                    <tbody class="cuerpoanalisis">
                                        <?php foreach($sobrantefaltante as $index => $value): ?>
                                        <tr class="<?php echo $value->nombremediopago=='Efectivo'?'!border-2 !border-indigo-600':'';?>">        
                                            <td><?php echo $value->nombremediopago;?></td> 
                                            <td class="colsistem cierre-number"><?php echo number_format($value->valorsistema, "0", ",", ".");?></td>
                                            <td class="coldeclarado cierre-number" data-mediopagoid="<?php echo $value->id_mediopago;?>"><?php echo number_format($value->valordeclarado, "0", ",", ".");?></td>
                                            <td class="coldif cierre-number"><?php echo number_format($value->valordeclarado-$value->valorsistema, "0", ",", ".");?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <table id="ventasXUsuario" class="tabla2 cierre-data-table mb-12" width="100%">
                                <thead>
                                    <tr>
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
                                    <?php if(userPerfil()<4):  ?>
                                        <tr>        
                                            <td class=""><?php echo $value['Nombre'];?></td> 
                                            <td class="cierre-number"><?php echo $value['N_ventas'];?></td>
                                            <td class="cierre-number"><strong>$ </strong><?php echo number_format($value['ventas'], 0, ",", ".");?></td>
                                            <td class="cierre-number"><strong>$ </strong><?php echo number_format($value[$comision], 0, ",", ".");?></td>
                                        </tr>
                                    <?php endif; ?>    
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        
                        </div>
                    </div>

                    <h5 class="cierre-section-title">Ventas del dia</h5>
                    <!-- Facturas del dia -->
                    <div class="overflow-x-auto">
                        <table id="tablaVentas" class="display responsive nowrap tabla cierre-data-table w-full min-w-[700px]">
                            <thead>
                            <tr>
                                <th>N.</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Entrega</th>
                                <th>Factura</th>
                                <th>Medio pago</th>
                                <th>Estado</th>
                                <th class="cierre-number">Subtotal</th>
                                <th class="cierre-number">Total</th>
                                <th class="accionesth">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($facturas as $index => $value): ?>
                            <tr>
                                <td><?php echo $index+1;?></td>
                                <td><div class="w-32 whitespace-normal"><?php echo $value->fechapago;?></div></td>
                                <td><div class=" w-48 whitespace-normal"><?php echo $value->cliente;?></div></td>
                                <td><span class="cierre-badge <?php echo $value->entrega=='Domicilio'?(($value->estado == 'Paga' || $value->estado == 'Remision') && $value->entregado == 1?'cierre-badge--success':'cierre-badge--warning'):'cierre-badge--neutral';?>"><?php echo $value->entrega;?></span></td>
                                <td><?php echo $value->prefijo.''.$value->num_consecutivo;?></td>
                                <td>
                                    <div data-estado="<?php echo $value->estado;?>" data-totalpagado="<?php echo $value->total;?>" id="<?php echo $value->id;?>" class="mediosdepago max-w-full flex flex-wrap gap-2">
                                        <?php foreach($value->mediosdepago as $idx => $element): ?>
                                        <button class="cierre-badge cierre-badge--neutral"><?php echo $element->mediopago;?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><span class="cierre-badge <?php echo $value->estado=='Paga'&&$value->tipoventa=='Contado'?'cierre-badge--success':($value->estado=='Paga'&& $value->tipoventa=='Credito'?'cierre-badge--info':($value->estado=='Guardado'?'cierre-badge--neutral':($value->estado=='Remision' || $value->estado=='Paga'&&$value->remision==1?'cierre-badge--indigo':'cierre-badge--neutral')));?>"><?php echo ($value->tipoventa =='Contado'||$value->tipoventa =='')?$value->estado:'Credito';?></span></td>
                                <td class="cierre-number">$ <?php echo number_format($value->subtotal??0, "0", ",", ".");?></td>
                                <td class="cierre-number">$ <?php echo number_format($value->total??0, "0", ",", ".");?></td>
                                <td class="accionestd">
                                    <div class="acciones-btns" id="<?php echo $value->id;?>">
                                        <a class="cierre-table-action" href="/admin/caja/ordenresumen?id=<?php echo $value->id;?>">Ver</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>    
                </div> <!--fin content -->
            </div> <!--fin wrpper-content-->
        </div> <!--fin wrapper -->
        
    </div> <!-- fin accordion-->
    <?php endif; ?>

    <!-- Ventana Modal Arqueo de caja -->
    <dialog id="modalArqueocaja" class="w-[820px] max-w-[94vw] rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/40">
        <div class="border-b border-slate-200 bg-slate-50 px-8 py-6">
            <h4 class="m-0 text-4xl font-semibold text-slate-800">Arqueo de caja</h4>
            <p class="m-0 mt-2 text-xl text-slate-500">Cuente las unidades por denominacion. El total se calcula automaticamente.</p>
        </div>
        <div id="divmsjalerta2"></div>
        <form id="formArqueocaja" class="formulario px-8 py-6" action="/" method="POST">
            <div class="overflow-hidden rounded-lg border border-slate-200">
                <div class="grid grid-cols-[1fr_11rem_1fr] items-center gap-3 bg-slate-100 px-5 py-3 text-lg font-semibold text-slate-600">
                    <span>Denominacion</span>
                    <span class="text-center">Cantidad</span>
                    <span class="text-right">Subtotal</span>
                </div>
                <div class="divide-y divide-slate-200">
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="cienmil">$100.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="cienmil" name="cienmil" value="0" data-moneda="100000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="cincuentamil">$50.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="cincuentamil" name="cincuentamil" value="0" data-moneda="50000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="veintemil">$20.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="veintemil" name="veintemil" value="0" data-moneda="20000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="diezmil">$10.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="diezmil" name="diezmil" value="0" data-moneda="10000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="cincomil">$5.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="cincomil" name="cincomil" value="0" data-moneda="5000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="dosmil">$2.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="dosmil" name="dosmil" value="0" data-moneda="2000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="mil">$1.000</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="mil" name="mil" value="0" data-moneda="1000">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="quinientos">$500</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="quinientos" name="quinientos" value="0" data-moneda="500">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
            <div class="arqueo-row grid grid-cols-[1fr_11rem_1fr] items-center gap-3 px-5 py-3">
                <label class="m-0 text-2xl font-semibold text-slate-800" for="docientos">$200</label>
                <input class="denominacion h-14 rounded-lg border border-slate-300 bg-white p-3 text-center text-2xl text-gray-900 focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="0" id="docientos" name="docientos" value="0" data-moneda="200">
                <span class="arqueo-row-total text-right text-2xl font-semibold text-slate-700">$0</span>
            </div>
                </div>
            </div>
            
            <div class="mt-5 flex flex-col gap-4 rounded-lg bg-slate-50 p-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="m-0 text-lg font-semibold uppercase tracking-wide text-slate-500">Total arqueado</p>
                    <p id="arqueoTotal" class="m-0 mt-1 text-4xl font-bold text-emerald-600">$0</p>
                </div>
                <div class="flex justify-end gap-3">
                <button class="inline-flex h-14 min-w-40 items-center justify-center rounded-lg bg-slate-100 px-6 text-xl font-semibold text-slate-700 hover:bg-slate-200" type="button" value="Cancelar">Cancelar</button>
                <input id="btnAPlicararqueocaja" class="inline-flex h-14 min-w-40 cursor-pointer items-center justify-center rounded-lg bg-indigo-600 px-6 text-xl font-semibold text-white hover:bg-indigo-700" type="submit" value="Aplicar">
                </div>
            </div>
        </form>
    </dialog>
  
    <!-- MODAL ventana para cerrar caja-->
    <dialog id="Modalcerrarcaja" class="midialog-xs cierre-confirm-modal overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/55">
        <div class="cierre-confirm-body">
            <div class="mb-7 flex items-start gap-5">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <span class="material-symbols-outlined text-5xl">lock</span>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold uppercase tracking-[0.2em] text-amber-600">Confirmar cierre</p>
                    <h4 class="m-0 text-4xl font-extrabold leading-tight text-slate-900">Caja principal</h4>
                    <p class="mt-3 text-xl leading-relaxed text-slate-600">Esta caja quedara cerrada y no se podra modificar despues.</p>
                </div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-6 py-5 text-xl font-semibold text-amber-800">
                Verifique que el arqueo y los totales esten correctos antes de confirmar.
            </div>
        </div>
        <div class="cerrarcaja cierre-confirm-actions grid grid-cols-2 gap-5 border-t border-slate-200">
            <button type="button" class="salircerrarcaja inline-flex min-h-[4.5rem] items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-5 text-xl font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                <span class="material-symbols-outlined text-3xl">close</span>
                Cancelar
            </button>
            <button type="button" class="finCerrarcaja inline-flex min-h-[4.5rem] items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 text-xl font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
                Confirmar
            </button>
        </div>
    </dialog>

    <!-- MODAL cambiar caja-->
    <dialog id="modalCambiarCaja" class="midialog-sm w-[92vw] max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/55">
        <div class="border-b border-slate-200 bg-gradient-to-br from-white via-white to-indigo-50/60 px-8 py-7">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 shadow-sm">
                    <span class="material-symbols-outlined text-4xl">change_circle</span>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold uppercase tracking-[0.2em] text-indigo-700">Cambio de caja</p>
                    <h4 class="m-0 text-4xl font-extrabold leading-tight text-slate-900">Cambiar caja</h4>
                    <p class="mt-3 text-xl leading-relaxed text-slate-600">Seleccione la caja destino para continuar con la operacion.</p>
                </div>
            </div>
        </div>
        <div id="divmsjalertaCambiarCaja" class="px-8 pt-5"></div>
        <form id="formCambiarCaja" class="formulario px-8 pb-8 pt-5" action="/admin/caja" method="POST">
            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-5">
                <div class="mb-4 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                        <span class="material-symbols-outlined text-3xl">point_of_sale</span>
                    </div>
                    <div>
                        <label class="m-0 block text-2xl font-bold text-slate-900" for="CambiarCaja">Caja destino</label>
                        <p class="mt-1 text-lg leading-relaxed text-slate-600">El cierre actual quedara asociado a la caja seleccionada.</p>
                    </div>
                </div>
                <select id="CambiarCaja" class="formulario__select block h-[4.5rem] w-full rounded-xl border border-slate-300 bg-white px-5 text-2xl font-semibold text-slate-800 shadow-sm outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200" name="CambiarCaja" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($cajas as $index => $value): ?>
                        <option value="<?php echo $value->id;?>"><?php echo $value->nombre;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-200 pt-6">
                <button class="btn-md btn-turquoise !m-0 !w-full !py-5 !text-2xl" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEnviarCambiarCaja" class="btn-md btn-indigo !m-0 !w-full !py-5 !text-2xl" type="submit" value="Aplicar">
            </div>
        </form>
    </dialog>

    <div><a href="https://www.j2softwarepos.com" target="_blank" class="text-gray-500 text-center block text-lg">J2 Software POS MultiSucursal</a></div>

  </div>
</div>










