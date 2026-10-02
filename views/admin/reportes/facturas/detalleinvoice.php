<!-- CONTENEDOR GENERAL -->
<div class="space-y-10 bg-white p-8 mb-20  rounded-xl detalleinvoice">
    <a href="/admin/caja" class="text-white bg-indigo-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-4 text-center inline-flex items-center me-2">
    <svg class="w-6 h-6 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
    </svg>
    <span class="sr-only">Atrás</span>
  </a>
  <div class="relative">
    <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>

    <!-- ENCABEZADO -->
    <div class="bg-white shadow rounded-xl p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-8 mb-5">
        <!-- Izquierda -->
        <div>
             <!-- id de la factura electronica de venta -->
            <input id="idfe" class="hidden" type="text" value="<?php echo $ultimaFacturaElectronica?->id;?>">
            <input id="idfeState" class="hidden" type="text" value="<?php echo $ultimaFacturaElectronica?->id_estadoelectronica;?>">
            <!-- id de la factura general -->
            <input id="idfactura" class="hidden" type="text" value="<?php echo $factura->id;?>">

            <h2 id="prefixNumber" class="text-2xl font-semibold text-gray-900">
                Factura #<?php echo $factura->prefijo.'-'.$factura->num_consecutivo; ?> 
            </h2>
            <p class="text-gray-500 text-base"><?php
                $fecha = new DateTime('now', new DateTimeZone('America/Bogota'));
                echo $fecha->format('d M Y').' - '.date("h:i A", strtotime(substr(($ultimaFacturaElectronica?->fecha_ultimo_intento)??$factura->fechapago, 12)));
             ?></p>

            <!-- Badge Estado DIAN -->
            <span id="estadoFactura" class="inline-block mt-4 px-4 py-1.5 text-base font-semibold rounded-full <?php echo $ultimaFacturaElectronica==null?'bg-slate-100 text-gray-700':($ultimaFacturaElectronica?->id_estadoelectronica == 2?' bg-green-500 text-white':'bg-yellow-100 text-yellow-700');?>">
                <?php echo $ultimaFacturaElectronica == null?'Factura POS':($ultimaFacturaElectronica?->id_estadoelectronica == 2?'Aceptada DIAN':'Pendiente DIAN' )?>
            </span>
        </div>

        <!-- Acciones -->
        <div class="w-full grid grid-cols-2 gap-3 
            md:flex md:flex-row md:justify-end md:w-auto">

            <!-- ENVIAR A DIAN -->
            <button id="btnEnvarDian" class="flex items-center justify-center gap-2 w-full md:w-auto md:px-4 px-5 py-3 bg-indigo-600 text-white rounded-lg text-lg font-medium hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Enviar a DIAN
            </button>
        
            <!-- ENVIAR CORREO -->
            <button id="btnEnviarCorreo" class="flex items-center justify-center gap-2 w-full md:w-auto md:px-4 px-5 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg text-lg font-medium hover:bg-gray-50 hover:border-gray-400 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 12H8m0 0l4-4m-4 4l4 4m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Enviar por correo
            </button>


            <!-- NOTA CRÉDITO -->
            <button id="btnModalNotaCredito" class="flex items-center justify-center gap-2 w-full md:w-auto md:px-4 px-5 py-3 bg-red-600 text-white rounded-lg text-lg font-medium hover:bg-red-700 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 9l-6 6m0-6l6 6M19 12a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Nota crédito
            </button>

            <!-- NUEVA FACTURA -->
            <button id="btnNuevaFactura" class="flex items-center justify-center gap-2 w-full md:w-auto md:px-4 px-5 py-3 bg-white border border-indigo-500 text-indigo-600 rounded-lg text-lg font-medium hover:bg-indigo-50 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 6v12m6-6H6" />
                </svg>
                Nueva factura
            </button>
        </div>
    </div>

    <!-- DATOS DEL ADQUIRIENTE -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-8 mb-5">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21v-2a4 4 0 00-3-3.87M4 21v-2a4 4 0 013-3.87"/>
                </svg>
                Datos del adquiriente
            </h3>

            <span class="bg-red-50 text-red-600 text-sm font-semibold px-3 py-1 rounded-full border border-red-200">
                No asignado
            </span>
        </div>

        <!-- Campos -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-gray-700 text-lg">
            <div>
                <p class="font-bold text-gray-900">Nombre</p>
                <p id="nombreAdquiriente"><?php echo $adquiriente->business_name; ?></p>
            </div>
            <div>
                <p class="font-bold text-gray-900">Identificación</p>
                <p id="identificacionAdquiriente"><?php echo $adquiriente->identification_number; ?></p>
            </div>
            <div>
                <p class="font-bold text-gray-900">Correo</p>
                <p id="emailAdquiriente"><?php echo $adquiriente->email?$adquiriente->email:' - '; ?></p>
            </div>
        </div>

        <!-- Acción derecha -->
        <div class="flex justify-end mt-6">
            <button id="facturarA" class="px-6 py-3.5 bg-indigo-600 text-white rounded-lg text-xl font-medium hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Asignar / actualizar cliente
            </button>
        </div>
    </div>

    <!-- DETALLE DOCUMENTOS ELECTRONICOS -->
    <div class="bg-white rounded-xl shadow p-8 overflow-x-auto mb-5">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6">Detalle documentos electronicos</h3>

        <table class="w-full text-lg">
            <thead class="bg-gray-100 text-gray-600 font-semibold">
                <tr>
                    <th class="py-3 px-4 text-left">Id</th>
                    <th class="py-3 px-4 text-center">Id Factura</th>
                    <th class="py-3 px-4 text-center">F. Electronica/NC</th>
                    <th class="py-3 px-4 text-center">Estado</th>
                    <th class="py-3 px-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="detalleDocumentos" class="divide-y">
                <?php foreach($facturasElectronicas as $value): 
                    if($value->id_estadoelectronica != 4): ?>
                    <tr class="detalleDocumento" data-idfe="<?php echo $value->id;?>">
                        <td class="py-3 px-4"><?php echo $value->id;?></td>
                        <td class="text-center"><?php echo $value->id_facturaid;?></td>
                        <td class="text-center"><?php echo $value->num_factura; echo $value->id_estadonota==2?' / '.$value->prefixnc.' - '.$value->num_nota:''; ?></td>
                        <td class="text-center">
                            <div>
                                <a 
                                class="btn-xs <?php echo $value->id_estadoelectronica==2?'btn-lima':($value->id_estadoelectronica==1?'btn-blue':'btn-red');?>" 
                                href="<?php echo $value->link??'/';?>" target="_blank"
                                >
                                    <?php echo $value->id_estadoelectronica==2?'Aceptada':($value->id_estadoelectronica==1?'Pendiente':'Error');?>
                                </a>
                                <?php if($value->id_estadoelectronica==2):?>
                                    <a class="btn-xs btn-blueintense" href="https://apidianj2.com/j2softwarepos/download/<?php echo $value->filename;?>">PDF</a>
                                <?php endif;?>
                            </div>
                            <a 
                                class="btn-xs <?php echo $value->id_estadonota==2?'btn-orange':($value->nota_credito==1&&$value->id_estadonota==1?'btn-blue':($value->nota_credito==1&&$value->id_estadonota==3?'btn-red':''));?>" 
                                href="<?php echo $value->linknc??'/';?>" target="_blank"
                            >
                                <?php echo $value->id_estadonota==2&&$value->nota_credito==1?'Aceptada NC':($value->id_estadonota==1&&$value->nota_credito==1?'Pendiente NC':($value->id_estadonota==3&&$value->nota_credito==1?'Error NC':''));?>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php if($value->id_estadoelectronica!=2): ?><span class="editarResolution material-symbols-outlined cursor-pointer">app_registration</span><?php endif; ?>
                            <?php if($value->id_estadoelectronica!=2): ?><span class="cursor-pointer material-symbols-outlined eliminarFactura">delete</span><?php endif; ?>
                        </td>
                    </tr>
                <?php endif; endforeach;  ?>
            </tbody>
        </table>
    </div>

    <!-- RESUMEN -->
    <div class="bg-white rounded-xl shadow p-8 max-w-xs ml-auto border border-gray-100">
        <div class="flex justify-between text-lg text-gray-700 mb-2">
            <span>Subtotal</span><span><?php echo $factura->base;?></span>
        </div>
        <div class="flex justify-between text-lg text-gray-700 mb-2">
            <span>IVA-Imp</span><span>$<?php echo $factura->valorimpuestototal;?></span>
        </div>
        <div class="border-t border-gray-200 my-4"></div>
        <div class="flex justify-between text-xl font-extrabold text-gray-900">
            <span>Total</span><span>$<?php echo $factura->total;?></span>
        </div>
    </div>

    <!-- MODAL ENVIAR POR CORREO -->
    <dialog id="modalEnviarCorreo"
        class="rounded-2xl border border-gray-200 w-[95%] max-w-2xl p-10 bg-white backdrop:bg-black/40 shadow-2xl
            transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out
            backdrop:backdrop-blur-sm">

        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
                📧 Enviar factura por correo
            </h4>

            <button id="btnCerrarEnviarCorreo"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                onclick="document.getElementById('modalEnviarCorreo').close()">
                <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
            </button>
        </div>

        <form method="dialog" class="grid grid-cols-1 gap-6">

            <!-- Correo del cliente -->
            <div>
                <label class="font-medium text-gray-800">Correo del cliente</label>
                <input type="email" id="correoCliente"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-14 text-lg focus:outline-none focus:ring-1"
                    placeholder="cliente@ejemplo.com" required>
            </div>

            <!-- Asunto -->
            <div>
                <label class="font-medium text-gray-800">Asunto del correo</label>
                <input type="text" id="asuntoCorreo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-14 text-lg focus:outline-none focus:ring-1"
                    value="Factura electrónica FE-000123" required>
            </div>

            <!-- Mensaje -->
            <div>
                <label class="font-medium text-gray-800">Mensaje</label>
                <textarea id="mensajeCorreo"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-40 text-lg focus:outline-none focus:ring-1"
                >Estimado cliente, adjuntamos su factura electrónica.</textarea>
            </div>

            <!-- Adjuntar archivos -->
            <div class="flex items-center gap-3 text-lg">
                <input type="checkbox" id="adjuntarArchivos" class="scale-125" checked>
                <label for="adjuntarArchivos" class="text-gray-700">Adjuntar PDF y XML</label>
            </div>

            <!-- Botones -->
            <div class="text-right pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" id="btnCancelarModal"
                    class="btn-md bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg !py-4 !px-6 !w-[135px]"
                    onclick="document.getElementById('modalEnviarCorreo').close()">
                    Cancelar
                </button>

                <button id="btnConfirmarEnviarCorreo"
                    class="btn-md bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg !py-4 !px-6 !w-[135px]">
                    Enviar
                </button>
            </div>
        </form>
    </dialog>

    <!-- MODAL NOTA CRÉDITO -->
    <dialog id="miDialogoNC"
        class="rounded-2xl border border-gray-200 w-[95%] max-w-2xl p-10 bg-white backdrop:bg-black/40 shadow-2xl
        transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out
        backdrop:backdrop-blur-sm">

        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-2xl font-bold text-red-700 flex items-center gap-2">🧾 Generar Nota Crédito</h4>

            <button id="btnCerrarNotaCredito"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                onclick="document.getElementById('miDialogoNC').close()">
                <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
            </button>
        </div>
        <div id="divmsjalertaNC"></div>
        <form id="formNC" class="grid grid-cols-1 gap-6">
            <div>
                <label class="font-medium text-gray-800">Seleccionar factura electronica de venta</label>
                <select id="selectInvoice" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2" required>
                    <option value="" selected disabled>- Seleccionar -</option>
                    <?php foreach($facturasElectronicas as $value):
                        if($value->id_estadoelectronica==2&&$value->nota_credito==0 || ($value->id_estadoelectronica==2&&($value->id_estadonota==1 || $value->id_estadonota==3))): ?>
                            <option data-idfactura="<?php echo $value->id_facturaid;?>" data-estadoFE="<?php echo $value->id_estadoelectronica;?>" data-estadoNC="<?php echo $value->id_estadonota;?>" value="<?php echo $value->id;?>"><?php echo $value->num_factura;?></option>
                    <?php endif; endforeach;  ?>
                </select>
            </div>
            <!-- MOTIVO -->
            <div>
                <label class="font-medium text-gray-800">Motivo de la nota crédito</label>
                <select id="motivoNota"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2">
                    <option value="devolucion">Devolución de productos</option>
                    <option value="descuento">Aplicación de descuento</option>
                    <option value="anulacion">Anulación de factura</option>
                    <option value="otros">Otros</option>
                </select>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="selectSetConsecutivo">Seleccionar consecutivo</label>
                <select id="selectSetConsecutivo" class="formulario__select bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white h-14 text-xl focus:outline-none focus:ring-1" name="selectSetConsecutivo" required>
                    <option value="0">Siguiente consecutivo</option>
                    <option value="1">Consecutivo personalizado</option>
                </select>
            </div>
            <div class="formulario__campo habilitaconsecutivo" style="display: none;">
                <label class="formulario__label" for="consecutivoPersonalizado">Consecutivo personalizado</label>
                <input id="consecutivoPersonalizado" class="formulario__input bg-gray-50 border border-gray-300 text-gray-900 !rounded-lg focus:border-indigo-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white h-14 text-xl focus:outline-none focus:ring-1" type="text" placeholder="Digite numero del consecutivo" name="consecutivoPersonalizado" value="">
            </div>

            <!-- DESCRIPCIÓN -->
            <div>
                <label class="font-medium text-gray-800">Descripción</label>
                <textarea id="descripcionNota" rows="4"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 text-xl focus:outline-none focus:ring-1 mt-2 h-32"
                    placeholder="Escribe una breve descripción..."></textarea>
            </div>

            <!-- Botones -->
            <div class="text-right pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" id="btnCancelarNota" class="btn-md bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg !py-4 !px-6 !w-[135px]" value="Cancelar">
                    Cancelar
                </button>

                <input id="btnEnviarNotaCredito"
                    class="btn-md bg-red-600 hover:bg-red-700 text-white rounded-lg !py-4 !px-6 !w-[135px]" type="submit" value="Generar">
                </input>
            </div>
        </form>
    </dialog>

    <!-- MODAL NUEVA FACTURA EJ: POS A ELECTRONICA -->
    <dialog id="modalNuevaFactura"
        class="rounded-2xl border border-gray-200 w-[95%] max-w-2xl p-10 bg-white backdrop:bg-black/40 shadow-2xl
            transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out
            backdrop:backdrop-blur-sm">

        <!-- Encabezado -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-2xl font-bold text-indigo-700 flex items-center gap-3">
                <i class="fa-solid fa-file-invoice-dollar text-indigo-700 text-3xl"></i>
                Generar Nueva Factura
            </h4>

            <button id="btnCerrarNuevaFactura"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                onclick="document.getElementById('modalNuevaFactura').close()">
                <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
            </button>
        </div>

        <form id="formNuevaFactura" method="POST" class="grid grid-cols-1 gap-6">
            <!-- Seleccionar Resolución -->
            <div>
                <label class="font-medium text-gray-800">Resolución en uso</label>
                <select id="selectResolucion"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-14 text-lg focus:outline-none focus:ring-1"
                    required>
                    <option value="" selected disabled>- Seleccionar -</option>
                    <?php foreach($resoluciones as $value): ?>
                    <option value="<?= $value->id ?>" data-prefijo="<?= $value->prefijo ?>" data-actual="<?= $value->siguientevalor ?>">
                        <?= $value->nombre; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo de consecutivo -->
            <div>
                <label class="font-medium text-gray-800 block">Tipo de consecutivo</label>
                <div class="space-y-3 mt-2">

                    <label class="flex items-center gap-3 bg-gray-50 border border-gray-300 p-3 rounded-lg cursor-pointer hover:border-indigo-600 transition">
                        <input type="radio" name="tipoConsecutivo" value="automatico" checked class="scale-125">
                        <span class="text-gray-800 text-lg">Siguiente consecutivo automático</span>
                    </label>

                    <label class="flex items-center gap-3 bg-gray-50 border border-gray-300 p-3 rounded-lg cursor-pointer hover:border-indigo-600 transition">
                        <input type="radio" name="tipoConsecutivo" value="manual" class="scale-125">
                        <span class="text-gray-800 text-lg">Ingresar consecutivo manual</span>
                    </label>

                    <input id="consecutivoManual" type="number"
                        class="hidden w-full p-3 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 focus:ring-1"
                        placeholder="Ej: 125">
                </div>
            </div>

            <!-- Vista previa -->
            <div class="text-center py-5 bg-indigo-50 border border-indigo-200 rounded-xl shadow-sm">
                <p class="text-lg font-medium text-indigo-700">Factura resultante</p>
                <p id="previewFactura"
                class="text-3xl font-black text-indigo-900 tracking-wide mt-1">
                ---
                </p>
            </div>

            <!-- Botones -->
            <div class="text-right pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" id="btnCancelarNuevaFactura"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg px-6 py-4 font-medium"
                    onclick="document.getElementById('modalNuevaFactura').close()">
                    Cancelar
                </button>

                <button id="btnGenerarNuevaFactura"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-6 py-4 font-semibold shadow-md">
                    Generar
                </button>
            </div>

        </form>
    </dialog>


    <!-- MODAL ADQUIRIENTE -->                
    <dialog id="miDialogoFacturarA" class="rounded-2xl backdrop:bg-black/40 p-0 w-[98vw] max-w-4xl">
        <form id="formFacturarA"
            class="bg-white rounded-2xl p-10 space-y-8 w-full" method="POST">

            <!-- Header -->
            <div class="flex items-center gap-4 pb-4 border-b">
                <div class="text-indigo-600 mt-1"><i class="fas fa-user-circle text-[4rem] leading-[2.5rem]"></i></div>
                <h3 class="text-3xl font-bold text-indigo-800">Datos Factura Electrónica</h3> 
            </div>
            <p class="text-lg text-gray-600">Ingresar datos del adquiriente</p>

            <!-- Cuerpo del formulario -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6">

                    <!-- Tipo Documento -->
                    <div>
                        <label class="font-semibold text-gray-700 mb-3">Tipo Documento</label>
                        <select id="type_document_identification_id" name="type_document_identification_id" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2">
                            <option value="" disabled selected>Seleccionar tipo de documento</option>
                            <option value="1" <?php echo $adquiriente->type_document_identification_id==1?'selected':'';?>>Registro civil</option>
                            <option value="2" <?php echo $adquiriente->type_document_identification_id==2?'selected':'';?>>Tarjeta de identidad</option>
                            <option value="3" <?php echo $adquiriente->type_document_identification_id==3?'selected':'';?>>Cédula de ciudadanía</option>
                            <option value="4" <?php echo $adquiriente->type_document_identification_id==4?'selected':'';?>>Tarjeta de extranjería</option>
                            <option value="5" <?php echo $adquiriente->type_document_identification_id==5?'selected':'';?>>Cédula de extranjería</option>
                            <option value="6" <?php echo $adquiriente->type_document_identification_id==6?'selected':'';?>>NIT</option>
                            <option value="7" <?php echo $adquiriente->type_document_identification_id==7?'selected':'';?>>Pasaporte</option>
                            <option value="8" <?php echo $adquiriente->type_document_identification_id==8?'selected':'';?>>Documento de identificación extranjero</option>
                            <option value="9" <?php echo $adquiriente->type_document_identification_id==9?'selected':'';?>>NIT de otro país</option>
                            <option value="10" <?php echo $adquiriente->type_document_identification_id==20?'selected':'';?>>NUIP *</option>
                            <option value="11" <?php echo $adquiriente->type_document_identification_id==11?'selected':'';?>>PEP (Permiso Especial de Permanencia)</option>
                            <option value="12" <?php echo $adquiriente->type_document_identification_id==12?'selected':'';?>>PPT (Permiso Protección Temporal)</option>
                        </select>
                    </div>

                    <!-- Número + Buscar -->
                    <div>
                        <label class="font-semibold text-gray-700">Número de Documento <span class="text-gray-700 text-lg font-normal">(Sin el dígito de verificación)</span> </label>
                        <div class="flex gap-3">
                            <input id="identification_number"
                                name="identification_number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2"
                                value="<?php echo $adquiriente->identification_number; ?>"
                                required>
                            <button id="btnBuscarAdquiriente" type="button"
                                class="px-4 bg-indigo-100 text-indigo-700 rounded-lg flex items-center gap-2 hover:bg-indigo-200 mt-2">
                                <i class="fa-solid fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label class="font-semibold text-gray-700">Nombre / Razón Social</label>
                        <input id="business_name" name="business_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2"
                            value="<?php echo $adquiriente->business_name; ?>"
                            >
                    </div>

                    <!-- Correo -->
                    <div>
                        <label class="font-semibold text-gray-700">Correo Electrónico</label>
                        <input id="email" name="email" type="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2"
                            value="<?php echo $adquiriente->email; ?>"
                        >
                    </div>

                </div>

                <!-- Opciones adicionales -->
                <div class="mt-2">
                    <input id="toggleOpcionesAdq" type="checkbox" class="peer sr-only">
                    <label for="toggleOpcionesAdq"
                        class="flex justify-center items-center gap-2 cursor-pointer text-gray-600 hover:text-indigo-700">
                        Más opciones
                        <span class="peer-checked:hidden">+</span>
                        <span class="hidden peer-checked:inline">–</span>
                    </label>

                    <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 peer-checked:max-h-[1000px] mt-4 space-y-5">
                        
                        <div class="grid grid-cols-1 gap-6">
                            
                            <!-- Dirección -->
                            <div>
                                <label class="font-semibold text-gray-700">Dirección</label>
                                <input id="address" name="address"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2"
                                    value="<?php $adquiriente->address; ?>"
                                >
                            </div>

                            <!-- Departamento -->
                            <div>
                                <label class="font-semibold text-gray-700">Departamento</label>
                                <select id="department_id" name="department_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2">
                                    <option disabled selected>Seleccionar departamento</option>
                                    <?php foreach($departments as $value): ?>
                                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Municipio -->
                            <div>
                                <label class="font-semibold text-gray-700">Ciudad / Municipio</label>
                                <select id="municipality_id" name="municipality_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2">
                                    <option disabled selected>Seleccionar...</option>
                                </select>
                            </div>

                            <!-- Tipo de Organización -->
                            <div>
                                <label for="type_organization_id" class="block text-2xl font-medium text-gray-600">Tipo de Organización</label>
                                <select id="type_organization_id" name="type_organization_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                                <option value="" disabled selected>Seleccione tipo de organización</option>
                                <option value="1" <?php echo $adquiriente->type_organization_id==1?'selected':'';?> >Persona Jurídica y asimiladas</option>
                                <option value="2" <?php echo $adquiriente->type_organization_id==2?'selected':'';?> >Persona Natural y asimiladas</option>
                                </select>
                            </div>

                            <!-- Tipo Régimen -->
                            <div>
                                <label for="type_regime_id" class="block text-2xl font-medium text-gray-600">Tipo Régimen</label>
                                <select id="type_regime_id" name="type_regime_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1">
                                <option value="" disabled selected>Seleccione tipo de régimen</option>
                                <option value="1" <?php echo $adquiriente->type_regime_id==1?'selected':'';?> >Responsable de IVA</option>
                                <option value="2" <?php echo $adquiriente->type_regime_id==1?'selected':'';?>>No responsable de IVA</option>
                                </select>
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="phone" class="block text-2xl font-medium text-gray-600">Teléfono</label>
                                <input id="phone" type="tel" name="phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1"
                                placeholder="Teléfono de contacto" value="<?php echo $adquiriente->phone; ?>">
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4 pt-6 border-t">
                <button type="button" class="px-6 py-4 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium" onclick="miDialogoFacturarA.close()">Cancelar</button>
                <button type="submit" class="px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md">Confirmar</button>
            </div>

        </form>
    </dialog>


    <!-- MODAL EDITAR RESOLUCION DE FE -->
    <dialog 
        id="modalEditarResolution"
        class="rounded-2xl border border-gray-200 w-[95%] max-w-2xl p-10 bg-white backdrop:bg-black/40 shadow-2xl
                transition-all scale-95 opacity-0 open:scale-100 open:opacity-100 duration-300 ease-out backdrop:backdrop-blur-sm"
        >

        <!-- Encabezado -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <h4 class="text-2xl font-bold text-indigo-700 flex items-center gap-3"><i class="fa-solid fa-file-pen text-indigo-700 text-3xl"></i>Datos de resolucion</h4>

            <button id="btnCerrarEditarResolution"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                onclick="document.getElementById('modalEditarResolution').close()">
                <i class="fa-solid fa-xmark text-gray-600 text-2xl"></i>
            </button>
        </div>

        <form id="formEditarResolution" method="POST" class="grid grid-cols-1 gap-6">
            <!-- Seleccionar Resolución -->
            
            <label class="font-medium text-gray-800">Resolución en uso</label>
            <select id="selectEditarResolucion" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-3 h-14 text-lg focus:outline-none focus:ring-1" required>
                <option value="" selected disabled>- Seleccionar -</option>
                <?php foreach($resoluciones as $value): ?>
                    <option value="<?= $value->id ?>" data-prefijo="<?= $value->prefijo ?>" data-actual="<?= $value->siguientevalor ?>"><?= $value->nombre; ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Consecutivo -->
            <div>
                <label class="font-semibold text-gray-700">Consecutivo</label>
                <input id="consecutivoEditarResolution" name="consecutivoEditarResolution" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:border-indigo-600 block w-full p-2.5 h-14 text-xl focus:outline-none focus:ring-1 mt-2" value="">
            </div>

            <!-- Botones -->
            <div class="text-right pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" id="btnEditarResolution"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg px-6 py-4 font-medium"
                    onclick="document.getElementById('modalEditarResolution').close()">
                    Cancelar
                </button>

                <button id="btnGenerarEditarResolution" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-6 py-4 font-semibold shadow-md">Aplicar</button>
            </div>
            
        </form>
    </dialog>

  </div>
</div>

