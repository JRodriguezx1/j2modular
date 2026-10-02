<dialog id="miDialogoRemision" class="midialog-md !max-w-[75rem] rounded-2xl backdrop:bg-black/60">
    <div class="py-4 print:bg-white print:py-0 print:px-0">
        <div class="w-full max-w-[1100px] mx-auto bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="text-center pb-2 print:hidden">
                <button class="rounded-lg hover:bg-gray-100 transition">
                    <i id="btnXCerrarRemision" class="p-2 fa-solid fa-xmark text-gray-600 text-4xl"></i>
                </button>
            </div>
            <!-- Encabezado / Branding -->
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-700 px-10 py-8 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 print:bg-transparent print:text-slate-900 print:px-0 print:py-6 print:border-b print:border-slate-200">
                <div>
                    <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold tracking-tight"><?php echo $factura->nombrecompania??'';?></span>
                    </div>
                    <p class="text-base text-slate-400 mt-2 print:text-slate-500">NIT: <?php echo $factura->nit??'';?></p>
                </div>
                
                <div class="text-left sm:text-right">
                    <span class="inline-flex items-center mb-4 px-3 py-1 rounded-full text-sm font-medium bg-indigo-500/10 text-indigo-300 print:border print:border-slate-300 print:text-slate-700">Documento No Contable</span>
                    <h1 class="text-5xl font-bold tracking-tight mt-2">ORDEN DE REMISIÓN</h1>
                    <p class="text-indigo-400 font-mono text-lg mt-1 print:text-indigo-600"># REM-<?php echo date('Y')."-".$factura->num_orden??'';?></p>
                </div>
            </div>

            <!-- Información de Fechas y Partes -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-slate-100 print:px-0 print:py-6">
                <!-- Detalles del documento -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition-all">
                    <h3 class="text-lg font-semibold text-indigo-600 uppercase tracking-wider">Detalles del Envío</h3>
                    <dl class="mt-2 space-y-2 text-base text-slate-600">
                        <div class="flex justify-between md:block">
                            <dt class="font-medium text-slate-900">Fecha de Emisión:</dt>
                            <dd class="font-mono"><?php echo $factura->fechacreacion??'';?></dd>
                        </div>
                        <div class="flex justify-between md:block">
                            <dt class="font-medium text-slate-900">Fecha de Entrega:</dt>
                            <dd class="font-mono"><?php echo $factura->fechaentrega??'';?></dd>
                        </div>
                    </dl>
                </div>

                <!-- De (Remitente) -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition-all">
                    <h3 class="text-lg font-semibold text-indigo-600 uppercase tracking-wider">Origen / Despachado por</h3>
                    <div class="mt-2 text-sm text-slate-600 space-y-1">
                        <p class="font-semibold text-slate-900 text-base"><?php echo $sucursal->nombre??$factura->nombrecompania??' - ';?></p>
                        <p><?php echo $sucursal->direccion??'';?></p>
                        <p><?php echo $sucursal->ciudad??'';?></p>
                        <p class="text-slate-400"><?php echo $sucursal->email??'';?></p>
                    </div>
                </div>

                <!-- Para (Destinatario) -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition-all">
                    <h3 class="text-lg font-semibold text-indigo-600 uppercase tracking-wider print:text-slate-500">Destinatario / Entregar a</h3>
                    <div class="mt-2 text-sm text-slate-600 space-y-1">
                        <p class="font-semibold text-slate-900 text-base">Pixel Craft Studio S.A.S.</p>
                        <p>NIT: <?php echo $cliente->identificacion??'';?></p>
                        <p><?php echo $direccion->direccion??'';?></p>
                        <p>Contacto: <?php echo $cliente->telefono??'';?></p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Artículos -->
            <div class="p-8 print:px-0 print:py-4">
                <h3 class="text-base font-semibold text-slate-400 uppercase tracking-wider mb-4">Ítems Remitidos</h3>
            
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-100 print:bg-transparent">
                            <th scope="col" class="px-4 py-3 text-left text-base font-semibold text-slate-700 uppercase tracking-wider w-16">Item</th>
                            <th scope="col" class="px-4 py-3 text-left text-base font-semibold text-slate-700 uppercase tracking-wider">Descripción del Producto / Servicio</th>
                            <th scope="col" class="px-4 py-3 text-center text-base font-semibold text-slate-700 uppercase tracking-wider w-24">Cant.</th>
                            <!--<th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider w-32">Estado</th>-->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php foreach($productos as $index=>$value): $unidadesDistintas = $index; ?>
                            <tr class="odd:bg-white even:bg-slate-50 hover:bg-indigo-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap text-base font-mono text-slate-400"><?php echo $index+1 ?? '';?></td>
                                <td class="px-4 py-4 text-base">
                                    <div class="font-semibold text-slate-900 text-base"><?php echo $value->nombreproducto ?? '';?></div>
                                    <div class="text-base text-slate-500 mt-1">Code: <?php echo $value->sku ??'';?></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-base text-indigo-700 text-center font-bold bg-slate-50/50 print:bg-transparent"><?php echo $value->cantidad??0;?></td>
                                <!--<td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 print:border print:border-slate-300 print:bg-transparent print:text-slate-800">Nuevo / Sellado</span>
                                </td>-->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Resumen de Cantidades totales -->
                <div class="mt-4 flex justify-end border-t border-slate-100 pt-4">
                    <div class="w-full sm:w-96 bg-indigo-50 rounded-2xl p-6 border border-indigo-100 space-y-3">
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Total Ítems Distintos:</span>
                            <span class="font-mono"><?php echo $unidadesDistintas+1??0;?></span>
                        </div>
                        <div class="flex justify-between text-indigo-700 font-bold text-2xl bg-white p-3 rounded-xl border border-indigo-100 print:bg-transparent print:border print:border-slate-200">
                            <span>Total Unidades:</span>
                            <span class="font-mono">10</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notas y Firmas -->
            <div class="p-8 border-t border-slate-100 bg-slate-50/50 grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-8 print:bg-transparent print:px-0 print:py-6">
            <!-- Observaciones -->
                <div class="space-y-3 bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <h4 class="text-base font-bold text-indigo-700 uppercase tracking-wider">Observaciones / Términos</h4>
                    <p class="text-base text-slate-600 leading-relaxed">
                        Esta remisión no posee valor fiscal. La mercancía aquí descrita viaja por cuenta y riesgo del comprador. 
                        Favor verificar el empaque o sellos de seguridad de los productos estén intactos al recibir. Cualquier reclamo debe realizarse dentro de las próximas 24 horas.
                    </p>
                </div>

                <!-- Firmas -->
                <div class="grid grid-cols-2 gap-4 pt-4 md:pt-0">
                    <div class="flex flex-col justify-end items-center">
                        <div class="w-full border-b-2 border-slate-400 h-24"></div>
                        <p class="text-center text-sm font-medium text-slate-500 uppercase tracking-wider mt-3">Entregado por (Firma/Cédula)</p>
                    </div>
                    <div class="flex flex-col justify-end items-center">
                        <div class="w-full border-b-2 border-slate-400 h-24"></div>
                        <p class="text-center text-sm font-medium text-slate-500 uppercase tracking-wider mt-3">Recibido a Conformidad</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Botones de Acción (Se ocultan al imprimir automáticamente) -->
        <div class="max-w-[1100px] mx-auto mt-6 flex justify-end gap-4 print:hidden">

            <button
                onclick="window.print()"
                class="inline-flex items-center px-6 py-3 border border-slate-300 shadow-sm text-base font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">

                <svg class="-ml-1 mr-2 h-5 w-5 text-slate-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>

                Imprimir / PDF
            </button>

            <button
                value="Salir"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">

                Salir
            </button>

        </div>
    </div>
</dialog>