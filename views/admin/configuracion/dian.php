<div class="gestionDian">
    <div class="flex items-center gap-4 border-b border-slate-200 mb-5 pb-5">
        <div class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-3xl text-indigo-600 font-medium">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div>
            <h4 class="text-slate-900 text-3xl font-bold">Gestionar DIAN</h4>
            <p class="my-0 text-lg leading-snug text-slate-500">Administra companias, resoluciones y procesos de facturacion electronica.</p>
        </div>
    </div>
    <div class="mb-4 flex flex-wrap gap-4">
        <button id="btnAdquirirCompañia" class="btnDialog btnDialog_light min-h-[5.2rem] flex-1 basis-52 shadow-sm" type="button"><span class="material-symbols-outlined text-3xl text-indigo-600">arrow_and_edge</span>Adquirir compañia</button>
        <button id="btnCrearCompañia" class="btnDialog btnDialog_light min-h-[5.2rem] flex-1 basis-52 shadow-sm" type="button"><span class="material-symbols-outlined text-3xl text-indigo-600">data_saver_on</span>Crear compañia</button>
        <button id="btnObtenerresolucion" class="btnDialog btnDialog_primary min-h-[5.2rem] flex-1 basis-52" type="button"><span class="material-symbols-outlined text-3xl text-white">install_desktop</span>Obtener resolucion</button>
        <button id="BtnSetpruebas" class="btnDialog btnDialog_light min-h-[5.2rem] flex-1 basis-52 shadow-sm" type="button"><span class="material-symbols-outlined text-3xl text-indigo-600">component_exchange</span>Set pruebas</button>
        <button id="btnDocumentos" class="btnDialog btnDialog_light min-h-[5.2rem] flex-1 basis-52 shadow-sm" type="button"><span class="material-symbols-outlined text-3xl text-indigo-600">inbox</span>Recepción documentos</button>
    </div>

    <div class="border border-slate-200 p-4 rounded-xl">
      <div>
          <h5 class="text-slate-900 text-2xl font-bold">Lista de compañias</h5>
          <p class="my-0 text-lg leading-snug text-slate-500 mb-4">Companias configuradas para procesos DIAN y resoluciones.</p>
      </div>
      <div class="w-full min-w-0 max-w-full overflow-x-auto">
        <table class="datatable-table tabla" width="100%" id="tablaCompañias">
        <thead>
            <tr>
                <th>id</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>software</th>
                <th class="accionesth">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($companias as $index => $value): ?>
            <tr id="company<?php echo $value->identification_number;?>">
                <td><?php echo $value->id;?></td>
                <td>
                    <span class="table-entity">
                    <span class="table-entity__icon"><i class="fa-solid fa-building-user"></i></span>
                    <span><?php echo $value->business_name; ?></span>
                    </span>
                </td>
                <td><span class="table-badge table-badge--primary"><?php echo $value->identification_number;?></span></td>
                <td><span class="table-badge table-badge--neutral max-w-md !whitespace-normal break-all font-mono"><?php echo $value->idsoftware; ?></span></td>
                <td class="accionestd"><div class="acciones-btns"> <button id="<?php echo $value->id;?>" class="table-action table-action--danger" type="button" title="Eliminar compañia"><span class="material-symbols-outlined eliminarcompañia">delete</span></button></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
      </div>
    </div>

    <dialog id="miDialogoCompañia" class="midialog-md">
        <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
            <div class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">
                <i class="fa-solid fa-building-user"></i>
            </div>
            <div>
                <span class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">Compania</span>
                <h4 id="modalCompañia" class="text-slate-900 text-3xl leading-6 font-bold">Crear compañia</h4>
                <p class="mt-1 text-lg leading-snug text-slate-500">Registra la informacion fiscal y tecnica para facturacion electronica.</p>
            </div>
        </div>
        <div id="divmsjalertaCompañia" class="px-6"></div>
        <form id="formCrearUpdateCompañia" class="formulario--grid" method="POST">

            <div class="formulario__campo">
                <label class="formulario__label" for="type_document_identification_id">Tipo Documento</label>
                <select id="type_document_identification_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="type_document_identification_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <option value="1">Registro civil</option>
                    <option value="2">Tarjeta de identidad</option>
                    <option value="3">Cedula de ciudadania</option>
                    <option value="4">Tarjeta de extranjeria</option>
                    <option value="5">Cedula de extrangeria</option>
                    <option value="6">NIT</option>
                    <option value="7">Pasaporte</option>
                    <option value="8">Documento de identificacion extranjero</option>
                    <option value="9">NIT de otro pais</option>
                    <option value="10">NUIP</option>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="numero_documento">Numero de Documento</label>
                <input id="identification_number" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="number" placeholder="Numero del documento" name="identification_number" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="certificate">Certificado Digital .p12</label>
                <input type="file" id="certificate" name="certificate" accept=".p12" class="formulario__input formulario__input--filelogo focus:border-indigo-600 focus:outline-none focus:ring-1" required />
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="password">Contraseña</label>
                <input id="password" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="password" placeholder="Contraseña del certificado digital" name="password" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="idsoftware">ID Software</label>
                <input id="idsoftware" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="ID del Software" name="idsoftware" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="pinsoftware">Pin Software</label>
                <input id="pinsoftware" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="number" placeholder="Pin del Software" name="pinsoftware" value="" required>
            </div>
            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="nombrerazonsocial">Nombre/Razón Social</label>
                <input id="business_name" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="Nombre/Razon Social" name="business_name" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="tipoorganizacion">Tipo de Organización</label>
                <select id="type_organization_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="type_organization_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <option value="1">Persona Juridica</option>
                    <option value="2">Persona Natural</option>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="obligaciones">Obligaciones</label>
                <select id="type_liability_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="type_liability_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <option value="7">Gran contribuyente</option>
                    <option value="9">Autorretenedor</option>
                    <option value="14">Agente de retencion en el impuesto sobre las ventas</option>
                    <option value="112">Regimen simple de tributacion - Simple</option>
                    <option value="117">No Responsable</option>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="impuesto">Impuesto</label>
                <select id="tax_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="tax_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <option value="">Ninguno</option>
                    <option value="1">IVA</option>
                    <option value="4">Impuesto Nacional al consumo</option>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="regimen">Régimen</label>
                <select id="type_regime_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="type_regime_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <option value="1">Responsable de IVA</option>
                    <option value="2">No Responsable de IVA</option>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="email">Correo electrónico</label>
                <input id="email" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="email" placeholder="Email que aparece en el RUT" name="email" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="telefono">Teléfono</label>
                <input id="phone" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="number" placeholder="Telefono de contacto" name="phone" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="departamento">Departamento</label>
                <select id="department_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="department_id">
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($departments as $value): ?>
                        <option value="<?php echo $value->id;?>"><?php echo $value->name;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="ciudad">Ciudad</label>
                <select id="municipality_id" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="municipality_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                </select>
            </div>
            <div class="formulario__campo col-span-full">
                <label class="formulario__label" for="direccion">Dirección</label>
                <input id="address" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="Direccion comercial o de residencia" name="address" value="" required>
            </div>

            <div class="formulario__contenedorBtns--gridfull col-span-full">
                <button class="btnDialog btnDialog_light" type="button" value="Salir">Salir</button>
                <input id="btnEditarCrearCompañia" class="btnDialog btnDialog_primary" type="submit" value="Crear">
            </div>
        </form>
    </dialog><!--fin crear/editar compañia-->

    <dialog id="miDialogoAdquirirCompañia" class="midialog-sm">
        <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
            <div class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">
                <i class="fa-solid fa-building-circle-arrow-right"></i>
            </div>
            <div>
                <span class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">DIAN</span>
                <h4 class="text-slate-900 text-3xl leading-6 font-bold">Adquirir compañia</h4>
                <p class="mt-1 text-lg leading-snug text-slate-500">Consulta y vincula la informacion fiscal desde el certificado digital.</p>
            </div>
        </div>
        <div id="divmsjalertaAdquirirCompañia" class="px-8"></div>
        <form id="formAdquirirCompañia" class="formulario p-8" action="/admin/config/AdquirirCompañia" method="POST">

            <div class="formulario__campo">
                <label class="formulario__label" for="nitcompany">Numero de RUT</label>
                <input id="nitcompany" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="Nit sin digito de verificacion" name="nitcompany" value="" required>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="adquirirCompañiaPassword">Contraseña</label>
                <input id="adquirirCompañiaPassword" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="password" placeholder="Contraseña del certificado digital" name="adquirirCompañiaPassword" value="" required>
            </div>
            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_esmeralda" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEnviarAdquirirCompañia" class="btnDialog btnDialog_primary" type="submit" value="Enviar">
            </div>
        </form>
    </dialog><!--fin adquirir compañia-->

    <dialog id="miDialogoGetResolucion" class="midialog-sm">
        <div class="flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10 p-6">
            <span class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl border border-indigo-100 bg-white text-4xl font-medium text-indigo-600"><i class="fa-solid fa-file-signature"></i></span>
            <div>
                <span class="my-0 text-base font-extrabold uppercase leading-5 text-indigo-600">DIAN</span>
                <h4 class="text-3xl font-bold leading-6 text-slate-900">Obtener resolución</h4>
                <p class="mt-1 text-lg leading-snug text-slate-500">Consulta las resoluciones disponibles y asocia una al facturador.</p>
            </div>
        </div>
        <div id="divmsjalertaGetResolucion" class="px-8"></div>
        <form id="formGetResolucion" class="formulario relative p-8" action="/admin/config/GetResolucion" method="POST">
            <div class="content-spinner1" style="display: none;"><div class="spinner1"></div></div>
            <div class="formulario__campo">
                <label class="formulario__label" for="selectResolucioncompañia">Seleccionar compañia</label>
                <select id="selectResolucioncompañia" class="formulario__select  focus:border-indigo-600 focus:outline-none focus:ring-1" name="getresolucioncompañia" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($companias as $value): ?>
                        <option data-token="" value="<?php echo $value->id;?>"><?php echo $value->business_name;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="overflow-hidden rounded-xl border border-slate-200"><!--lista de resoluciones-->
                <h3 class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 p-4 text-xl font-semibold text-slate-900"><i class="fa-solid fa-file-signature text-indigo-600"></i> Lista de resoluciones</h3>
                <div class="w-full min-w-0 max-w-full overflow-x-auto px-3 pb-3">
                <table id="tablaListResolutions" class="datatable-table tabla">
                    <thead>
                        <tr>
                            <th>Prefijo</th>
                            <th>N° Resolucion</th>
                            <th>Rango</th>
                            <th>Fecha fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                </div>
            </div>
            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_light" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEnviarGetResolucion" class="btnDialog btnDialog_primary" type="submit" value="Consultar">
            </div>
        </form>
    </dialog><!--fin obtener resolucion-->

    <dialog id="miDialogosetpruebas" class="midialog-sm">
        <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
            <div class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">
                <i class="fa-solid fa-network-wired"></i>
            </div>
            <div>
                <span class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">DIAN</span>
                <h4 class="text-slate-900 text-3xl leading-6 font-bold">Set de pruebas</h4>
                <p class="mt-1 text-lg leading-snug text-slate-500">Asocia una compania al identificador de pruebas autorizado.</p>
            </div>
        </div>
        <div id="divmsjalertasetpruebas" class="px-8"></div>
        <form id="formSetPruebas" class="formulario p-8" action="/admin/config/setpruebas" method="POST">
            <div class="formulario__campo">
                <label class="formulario__label" for="selectSetCompañia">Seleccionar compañia</label>
                <select id="selectSetCompañia" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="setcompañia" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($companias as $value): ?>
                        <option data-token="" value="<?php echo $value->id;?>"><?php echo $value->business_name;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formulario__campo">
                <label class="formulario__label" for="idsetpruebas">ID set de pruebas</label>
                <input id="idsetpruebas" class="formulario__input focus:border-indigo-600 focus:outline-none focus:ring-1" type="text" placeholder="ID del set de pruebas" name="idsetpruebas" value="" required>
            </div>
            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_light" type="button" value="Cancelar">Cancelar</button>
                <input id="btnEnviarSetPruebas" class="btnDialog btnDialog_primary" type="submit" value="Enviar">
            </div>
        </form>
    </dialog><!--fin set pruebas-->

    <dialog id="miDialogoRecepcionDocumentos" class="detalledialog_lg">
        <div class="p-6 flex items-center gap-4 bg-gradient-to-br from-indigo-600/15 to-cyan-300/10">
            <div class="inline-flex size-20 shrink-0 items-center justify-center rounded-xl bg-white text-4xl text-indigo-600 font-medium border border-indigo-100">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <span class="my-0 text-base leading-5 font-extrabold uppercase text-indigo-600">DIAN</span>
                <h4 class="text-slate-900 text-3xl leading-6 font-bold">Recepcion de documentos</h4>
                <p class="mt-1 text-lg leading-snug text-slate-500">Centraliza los soportes recibidos del cliente para el proceso de facturacion electronica.</p>
            </div>
        </div>
        <div id="divmsjalertaRecepcionDocumentos" class="px-8"></div>
        <form id="formRecepcionDocumentos" class="formulario p-8" method="POST" enctype="multipart/form-data">
            <div class="formulario__campo">
                <label class="formulario__label" for="recepcionDocumentosCompania">Compania</label>
                <select id="recepcionDocumentosCompania" class="formulario__select focus:border-indigo-600 focus:outline-none focus:ring-1" name="compania_id" required>
                    <option value="" disabled selected>-Seleccionar-</option>
                    <?php foreach($companias as $value): ?>
                        <option value="<?php echo $value->id;?>"><?php echo $value->business_name;?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="border border-slate-200 rounded-xl p-4">
                <div>
                    <h5 class="text-slate-900 font-bold text-xl">Documentos fiscales</h5>
                    <p class="text-slate-500 text-lg">Archivos base para validar la informacion tributaria y habilitacion DIAN.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="config-dian-document-upload" for="documentoRut">
                        <span><i class="fa-solid fa-file-invoice"></i></span>
                        <strong>RUT</strong>
                        <small>PDF o imagen</small>
                        <input id="documentoRut" type="file" name="documento_rut" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </label>
                    <label class="config-dian-document-upload" for="documentoResolucion">
                        <span><i class="fa-solid fa-file-signature"></i></span>
                        <strong>Resolucion FE</strong>
                        <small>Documento DIAN</small>
                        <input id="documentoResolucion" type="file" name="documento_resolucion" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </label>
                    <label class="config-dian-document-upload" for="documentoCamaraComercio">
                        <span><i class="fa-solid fa-building-columns"></i></span>
                        <strong>Camara de comercio</strong>
                        <small>Certificado vigente</small>
                        <input id="documentoCamaraComercio" type="file" name="documento_camara_comercio" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </label>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl p-4">
                <div>
                    <h5 class="text-slate-900 font-bold text-xl">Identidad y marca</h5>
                    <p class="text-slate-500 text-lg">Soportes de contacto, representante y recursos graficos del cliente.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="config-dian-document-upload" for="documentoCedula">
                        <span><i class="fa-solid fa-id-card"></i></span>
                        <strong>Cedula</strong>
                        <small>Representante legal</small>
                        <input id="documentoCedula" type="file" name="documento_cedula" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    </label>
                    <label class="config-dian-document-upload" for="documentoLogo">
                        <span><i class="fa-solid fa-image"></i></span>
                        <strong>Logo</strong>
                        <small>PNG, JPG o WEBP</small>
                        <input id="documentoLogo" type="file" name="documento_logo" accept=".jpg,.jpeg,.png,.webp,.svg">
                    </label>
                    <label class="config-dian-document-upload" for="documentoCartaProductos">
                        <span><i class="fa-solid fa-list-check"></i></span>
                        <strong>Carta productos/servicios</strong>
                        <small>PDF, Excel o Word</small>
                        <input id="documentoCartaProductos" type="file" name="documento_carta_productos" accept=".pdf,.xlsx,.xls,.doc,.docx,.jpg,.jpeg,.png,.webp">
                    </label>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl p-4">
                <div>
                    <h5 class="text-slate-900 font-bold text-xl">Anexos adicionales</h5>
                    <p class="text-slate-500 text-lg">Incluye contrato, poder, cuenta bancaria, certificado de responsabilidad u otros soportes recibidos.</p>
                </div>
                <label class="config-dian-document-upload md:min-h-0 md:grid-cols-[auto_1fr] md:text-left md:[&>span]:row-span-3 md:[&>input]:mt-0" for="documentosAdicionales">
                    <span><i class="fa-solid fa-paperclip"></i></span>
                    <strong>Otros documentos</strong>
                    <small>Seleccion multiple de archivos</small>
                    <input id="documentosAdicionales" type="file" name="documentos_adicionales[]" accept=".pdf,.jpg,.jpeg,.png,.webp,.xlsx,.xls,.doc,.docx,.zip,.rar" multiple>
                </label>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="observacionesRecepcionDocumentos">Observaciones</label>
                <textarea id="observacionesRecepcionDocumentos" class="formulario__textarea !p-4" name="observaciones" rows="3" placeholder="Notas internas sobre documentos pendientes, vigencias o aclaraciones del cliente."></textarea>
            </div>

            <div class="formulario__contenedorBtns--gridfull">
                <button class="btnDialog btnDialog_esmeralda" type="button" value="Cancelar">Cancelar</button>
                <button id="btnGuardarRecepcionDocumentos" class="btnDialog btnDialog_primary" type="submit">Guardar documentos</button>
            </div>
        </form>
    </dialog><!--fin recepcion documentos-->

</div>
