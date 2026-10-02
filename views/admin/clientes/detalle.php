<?php
    $nombreCliente = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
    $emailCliente = $cliente->email ?: 'Sin correo registrado';
    $telefonoCliente = $cliente->telefono ?: 'NA';
    $totalCompras = $indicadores->cantidad_ventas ?? 0;
    $montoComprado = $indicadores->total_ventas_cliente ?? 0;
    $ticketPromedio = $indicadores->ticket_promedio ?? 0;
    $deudaCliente = $cliente->totaldebe ?? 0;
    $puntos = $cliente->puntos ?? 0;
?>

<div class="detallecliente !pb-12 text-slate-900">
    <div class="mx-auto grid max-w-[140rem] gap-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <section class="flex flex-col items-stretch gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-4 sm:p-6 tlg:flex-row tlg:items-center">
            <div class="flex items-center gap-4 flex-1">
                <a href="/admin/clientes" class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-xl text-white shadow-md transition hover:-translate-y-px hover:text-white hover:shadow-lg" title="Volver a clientes">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="sr-only">Atras</span>
                </a>
                <div class="min-w-0 flex-1">
                    <p class="mb-1 mt-0 text-base font-bold uppercase text-indigo-600">CRM</p>
                    <h1 class="m-0 break-words text-3xl font-bold leading-tight text-slate-900 sm:text-4xl"><?php echo htmlspecialchars($nombreCliente ?: 'Cliente', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="mt-1 text-lg leading-snug text-slate-500">Consulta perfil, compras, creditos y deuda del cliente desde una vista mas clara.</p>
                </div>
            </div>

            <div class="flex min-w-0 shrink-0 items-center gap-4 rounded-lg border border-slate-200 bg-white/90 p-4 lg:min-w-80">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 text-2xl text-white"><i class="fa-solid fa-user-check"></i></span>
                <div>
                    <strong class="block text-2xl font-bold leading-none text-slate-900">Activo</strong>
                    <small class="mt-1 block text-base font-bold text-slate-500">estado del cliente</small>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-address-card"></i></span>
                <div>
                    <h2 class="m-0 text-2xl font-bold leading-tight text-slate-900">Perfil del cliente</h2>
                    <p class="mt-1 text-base leading-snug text-slate-500">Datos principales de contacto y relacion comercial.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-envelope"></i></span>
                    <div>
                        <small class="block text-base font-bold text-slate-500">Correo</small>
                        <strong class="mt-1 block break-words text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($emailCliente, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </article>
                <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-phone"></i></span>
                    <div>
                        <small class="block text-base font-bold text-slate-500">Telefono</small>
                        <strong class="mt-1 block break-words text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($telefonoCliente, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </article>
                <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-bag-shopping"></i></span>
                    <div>
                        <small class="block text-base font-bold text-slate-500">Ultima compra</small>
                        <strong class="mt-1 block text-lg font-semibold text-slate-900">-</strong>
                    </div>
                </article>
                <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-xl text-indigo-600"><i class="fa-solid fa-calendar-check"></i></span>
                    <div>
                        <small class="block text-base font-bold text-slate-500">Cliente desde</small>
                        <strong class="mt-1 block text-lg font-semibold text-slate-900">-</strong>
                    </div>
                </article>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-2xl text-emerald-600"><i class="fa-solid fa-receipt"></i></span>
                <div>
                    <small class="block text-base font-bold text-slate-500">Total de compras</small>
                    <strong class="mt-1 block break-words text-2xl font-bold leading-tight text-slate-900"><?php echo number_format($totalCompras, 0, ',', '.'); ?></strong>
                </div>
            </article>
            <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-coins"></i></span>
                <div>
                    <small class="block text-base font-bold text-slate-500">Monto total comprado</small>
                    <strong class="mt-1 block break-words text-2xl font-bold leading-tight text-slate-900">$ <?php echo number_format($montoComprado, 2, ',', '.'); ?></strong>
                </div>
            </article>
            <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-2xl text-cyan-700"><i class="fa-solid fa-chart-line"></i></span>
                <div>
                    <small class="block text-base font-bold text-slate-500">Ticket promedio</small>
                    <strong class="mt-1 block break-words text-2xl font-bold leading-tight text-slate-900">$ <?php echo number_format($ticketPromedio, 2, ',', '.'); ?></strong>
                </div>
            </article>
            <article class="flex min-w-0 items-center gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-2xl text-rose-600"><i class="fa-solid fa-gift"></i></span>
                <div>
                    <small class="block text-base font-bold text-slate-500">Puntos acumulados</small>
                    <strong class="mt-1 block break-words text-2xl font-bold leading-tight text-slate-900"><?php echo number_format($puntos, 2, ',', '.'); ?> Pts</strong>
                </div>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
                    <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-chart-area"></i></span>
                    <div>
                        <h2 class="m-0 text-2xl font-bold leading-tight text-slate-900">Compras por mes</h2>
                        <p class="mt-1 text-base leading-snug text-slate-500">Evolucion de ventas asociadas al cliente.</p>
                    </div>
                </div>
                <div class="relative h-96 min-w-0 sm:h-[30rem]">
                    <canvas class="max-h-full" id="chartComprasMes"></canvas>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
                    <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-chart-pie"></i></span>
                    <div>
                        <h2 class="m-0 text-2xl font-bold leading-tight text-slate-900">Categorias mas compradas</h2>
                        <p class="mt-1 text-base leading-snug text-slate-500">Participacion por categoria en unidades vendidas.</p>
                    </div>
                </div>
                <div class="relative mx-auto h-96 min-w-0 max-w-[34rem] sm:h-[30rem]">
                    <canvas class="max-h-full" id="chartCategorias"></canvas>
                </div>
            </article>
        </section>

        <section class="flex flex-col flex-wrap gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:flex-row">
            <button id="btnDeudaTotal" class="inline-flex min-h-20 items-center justify-start gap-3 rounded-lg border border-slate-300 bg-white p-4 text-lg font-extrabold text-slate-900 transition hover:-translate-y-px sm:justify-center" type="button">
                <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-wallet"></i></span>
                <span>Deuda total <strong id="totalDeudaText">$<?php echo number_format($deudaCliente, 0, ',', '.'); ?></strong></span>
            </button>
            <button id="btnPagoDeudaTotal" class="inline-flex min-h-20 items-center justify-start gap-3 rounded-lg border-0 bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-lg font-extrabold text-white shadow-md transition hover:-translate-y-px sm:justify-center" type="button">
                <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-white/15"><i class="fa-solid fa-check"></i></span>
                <span>Pago total</span>
            </button>
            <button id="btnTotalCuotas" class="inline-flex min-h-20 items-center justify-start gap-3 rounded-lg border border-cyan-300 bg-cyan-50 p-4 text-lg font-extrabold text-cyan-700 transition hover:-translate-y-px sm:justify-center" type="button">
                <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg bg-cyan-100"><i class="fa-solid fa-list-check"></i></span>
                <span>Cuotas</span>
            </button>
        </section>

        <section class="min-w-0 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex items-start gap-4 border-b border-slate-200 pb-4 sm:items-center">
                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-2xl text-indigo-600"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                <div>
                    <h2 class="m-0 text-2xl font-extrabold leading-tight text-slate-900">Historial de creditos</h2>
                    <p class="mt-1 text-base leading-snug text-slate-500">Consulta saldos, estado y acciones disponibles por credito.</p>
                </div>
            </div>

            <div class="w-full min-w-0 max-w-full overflow-x-auto">
                <table id="tablaCreditos" class="datatable-table tabla">
                    <thead>
                        <tr>
                            <th>Emisor</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Credito</th>
                            <th>No. cuota</th>
                            <th>Saldo pendiente</th>
                            <th>Estado</th>
                            <th class="accionesth">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($creditos as $value): ?>
                        <?php
                            $estadoTexto = $value->idestadocreditos==1 ? 'Finalizado' : ($value->idestadocreditos==2 ? 'Abierto' : 'Anulado');
                            $estadoClase = $value->idestadocreditos==1 ? 'is-done' : ($value->idestadocreditos==2 ? 'is-open pendiente' : 'is-cancelled');
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($value->nombreEmisor ?? 'Negocio', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($value->created_at, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $value->idtipofinanciacion==1 ? 'Credito' : 'Separado'; ?></td>
                            <td>$<?php echo number_format($value->capital, 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($value->numcuota, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="<?php echo $value->saldopendiente>0 ? 'table-amount !text-red-600' : 'table-amount'; ?>">$<?php echo number_format($value->saldopendiente, 2, ',', '.'); ?></td>
                            <td class="<?php echo $estadoClase; ?>"><span class="table-status table-status--<?php echo $value->idestadocreditos==1 ? 'success' : ($value->idestadocreditos==2 ? 'warning' : 'danger'); ?>"><?php echo $estadoTexto; ?></span></td>
                            <td class="accionestd">
                                <div class="acciones-btns" id="<?php echo $value->id; ?>" data-saldopendiente="<?php echo $value->saldopendiente; ?>" data-montocuota="<?php echo $value->montocuota; ?>">
                                    <button class="table-action bg-cyan-500 text-white hover:text-white abonarCredito" type="button" title="Abonar al credito"><i class="fa-solid fa-dollar-sign"></i></button>
                                    <a class="table-action table-action--view" href="/admin/creditos/detallecredito?id=<?php echo $value->id; ?>" target="_blank" title="Ver detalle del credito"><i class="fa-solid fa-chart-simple"></i></a>
                                    <?php if($value->idtipofinanciacion==2&&$value->idestadocreditos==2): ?>
                                    <button class="table-action table-action--danger anularCredito" type="button" title="Eliminar el credito"><i class="fa-solid fa-trash-can"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . "/abonoCredito.php"; ?>

<dialog id="miDialogoTotalCuotas" class="max-h-[92vh] w-[min(94vw,96rem)] max-w-[96rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]">
    <div class="flex items-start gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-calendar-days"></i></span>
        <div class="min-w-0 flex-1">
            <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Credito</p>
            <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalTotalCuotas">Todas las cuotas</h4>
            <small class="mt-1 block text-base leading-snug text-slate-500">Detalle completo de cuotas asociadas al cliente.</small>
        </div>
        <button type="button" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" title="Cerrar">
            <i id="btnCerrarTotalCuotas" class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta"></div>
    <div class="w-full overflow-x-auto p-5 sm:p-6">
        <table id="tablaCuotas" class="datatable-table min-w-[72rem]">
            <thead>
                <tr>
                    <th>No. credito</th>
                    <th>Credito total</th>
                    <th>No. cuota</th>
                    <th>Valor cuota</th>
                    <th>Fecha pago</th>
                    <th>Estado credito</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</dialog>

<dialog id="miDialogoPagoTotal" class="max-h-[92vh] w-[min(94vw,58rem)] max-w-[58rem] overflow-x-hidden overflow-y-auto rounded-xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-[1px]">
    <div class="flex items-start gap-4 border-b border-slate-200 bg-gradient-to-br from-violet-100 to-cyan-50 p-5 sm:p-6">
        <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white text-2xl text-indigo-600"><i class="fa-solid fa-circle-check"></i></span>
        <div class="min-w-0 flex-1">
            <p class="m-0 text-base font-extrabold uppercase text-indigo-600">Pago</p>
            <h4 class="m-0 text-2xl font-extrabold leading-tight text-slate-900" id="modalPagoTotal">Pago total</h4>
            <small class="mt-1 block text-base leading-snug text-slate-500">Registra el pago completo de la deuda del cliente.</small>
        </div>
        <button type="button" class="inline-flex size-14 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600" title="Cerrar">
            <i id="btnCerrarPagoTotal" class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div id="divmsjalerta1"></div>
    <form id="formPagoTotalDeuda" class="grid grid-cols-1 gap-5 p-5 sm:p-6">
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-gradient-to-br from-violet-50 to-cyan-50 p-4">
            <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-2xl text-white"><i class="fa-solid fa-money-bill-wave"></i></span>
            <div>
                <small class="block text-base font-bold text-slate-500">Total a pagar</small>
                <strong class="mt-1 block text-3xl font-black leading-none text-slate-900">$<?php echo number_format($deudaCliente, 0, ',', '.'); ?></strong>
            </div>
        </div>

        <div class="form-field">
            <label for="PagoTotal_caja">Caja</label>
            <div class="form-input">
                <span><i class="fa-solid fa-cash-register"></i></span>
                <select id="PagoTotal_caja" name="cajaid" required>
                    <?php foreach($cajas as $value): ?>
                    <option value="<?php echo $value->id; ?>"><?php echo htmlspecialchars($value->nombre, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-field">
            <label for="PagoTotal_mediopago">Medio de pago</label>
            <div class="form-input">
                <span><i class="fa-solid fa-credit-card"></i></span>
                <select id="PagoTotal_mediopago" name="mediopagoid" required>
                    <?php foreach($mediospago as $value): ?>
                    <option value="<?php echo $value->id; ?>"><?php echo htmlspecialchars($value->mediopago, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-t border-slate-200 pt-5 sm:grid-cols-2">
            <button class="btnDialog btnDialog_secondary w-full" type="button" value="Salir">Salir</button>
            <button id="btnFormPagoTotalDeuda" class="btnDialog btnDialog_primary w-full" type="submit">Confirmar</button>
        </div>
    </form>
</dialog>

<script>
    const getParam = <?= json_encode($conflocal) ?>;
    let deudatotalCiente = <?= json_encode($cliente->totaldebe) ?>
</script>
