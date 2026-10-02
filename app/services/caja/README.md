# Inventario y cierre de extracción de `cajacontrolador`

> **Estado de la fase: estabilizada.** La extracción acordada para Caja está
> cerrada y sus flujos críticos fueron validados manualmente sobre la base de
> pruebas. Las exclusiones y deudas aceptadas se enumeran al final; la suite de
> pruebas automatizadas corresponde a la siguiente fase.

## Alcance de esta estructura

Esta carpeta concentra la separación de responsabilidades de
`App\Controllers\cajacontrolador`, conservando sus contratos HTTP actuales.

En esta fase:

- `cajacontrolador.php` continúa siendo el único controlador de caja.
- No se modifican rutas ni archivos de entrada.
- `CajaConsultasService` ya está implementado y conectado a las acciones de
  lectura inventariadas.
- `app/services/cajaService.php` conserva su API pública y delega temporalmente
  la consulta de impresión del cierre al servicio nuevo.
- `CajaCierreService` ya está implementado y conectado a las tres acciones que
  modifican la declaración, el arqueo y el cierre del período.
- `CajaMovimientosService` ya registra ingresos y gastos, abre el cierre cuando
  hace falta y actualiza sus acumulados dentro de una transacción.
- `CategoriasGastoService` ya concentra el listado y los comandos de creación,
  edición y eliminación del catálogo de gastos.
- `CajaReportesService` ya prepara el índice Z, el consolidado actual y el
  detalle de un cierre histórico.
- `CajaDocumentosService` ya prepara factura carta, cotización, detalle de
  cierre y el DTO de impresión POS con validación de sucursal.
- `CajaOrdenesService` concentra consultas de órdenes, pagos, eliminación de
  cotizaciones y despacho transaccional.
- Los servicios de esta carpeta ya están conectados a sus consumidores
  inventariados. Las excepciones aplazadas permanecen en el controlador o en
  `cajaService` para conservar compatibilidad.

## Responsabilidades encontradas

El controlador contiene 29 acciones distribuidas en siete grupos de lógica:

| Grupo | Responsabilidad | Servicio futuro |
|---|---|---|
| Consultas | Preparar panel, cierres, discriminaciones y detalle de caja | `CajaConsultasService` |
| Cierre | Declaración, arqueo y confirmación del cierre | `CajaCierreService` |
| Movimientos | Apertura por ingreso, ingresos, gastos, retiros y traslados | `CajaMovimientosService` |
| Categorías | Crear, editar, listar y eliminar categorías de gasto | `CategoriasGastoService` |
| Reportes | Z diario y consultas por fecha | `CajaReportesService` |
| Órdenes | Pedidos, pagos, despachos y cambio de emisor | `CajaOrdenesService` |
| Documentos | Impresión, formato de factura y correo | `CajaDocumentosService` |

## Estado de migración

### `CajaConsultasService` — completado

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `obtenerPanelCaja` | `cajacontrolador::index` | `GET /admin/caja` |
| `obtenerCierrePrincipal` | `cajacontrolador::cerrarcaja` | `GET /admin/caja/cerrarcaja` |
| `listarCierresFinalizados` | `cajacontrolador::ultimoscierres` | `GET /admin/caja/ultimoscierres` |
| `obtenerDetalleCierreFinalizado` | `cajacontrolador::detallecierrecaja` | `GET /admin/caja/detallecierrecaja` |
| `obtenerCajaSeleccionada` | `cajacontrolador::datoscajaseleccionada` | `POST /admin/api/datoscajaseleccionada` |
| `obtenerCierreParaImpresion` | `CajaDocumentosService::prepararDetalleCierre` | `GET /printdetallecierre` |

Los métodos privados `construirResumenCierre`, `agruparMediosPago`,
`calcularDiferencial` y `cruzarDeclaraciones` concentran el cálculo que estaba
repetido en cuatro lugares. El controlador conserva autenticación, entrada HTTP,
render de vistas y serialización JSON.

### `CajaCierreService` — completado

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `registrarDeclaracion` | `cajacontrolador::declaracionDinero` | `POST /admin/api/declaracionDinero` |
| `registrarArqueo` | `cajacontrolador::arqueocaja` | `POST /admin/api/arqueocaja` |
| `confirmarCierre` | `cajacontrolador::cierrecajaconfirmado` | `POST /admin/api/cierrecajaconfirmado` |

`confirmarCierre` bloquea el cierre abierto y guarda en una única transacción
el cierre actual, el siguiente período y la base automática. La notificación de
WhatsApp se ejecuta después del `commit`, por lo que una falla externa no
revierte un cierre ya persistido. `registrarArqueo` corrige además la edición de
un arqueo existente copiando las nuevas denominaciones antes de actualizar.

### `CajaMovimientosService` — completado para ingresos y gastos

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `registrarMovimiento` | `cajacontrolador::ingresoGastoCaja` | `POST /admin/caja/ingresoGastoCaja` |

`registrarMovimiento` valida la caja y la sucursal, crea el cierre si todavía
no existe y registra el ingreso o gasto junto con el acumulado correspondiente.
El controlador conserva la validación y almacenamiento del comprobante porque
es infraestructura HTTP/archivos, y reutiliza `CajaConsultasService` para volver
a construir el panel después del comando.

### `CategoriasGastoService` — completado

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `listarCategorias` | render compartido de categorías | `GET|POST /admin/caja/categoriaGasto` y respuestas de crear/editar |
| `crearCategoria` | `cajacontrolador::crear_categoriaGasto` | `POST /admin/caja/crear_categoriaGasto` |
| `editarCategoria` | `cajacontrolador::editarcategoriagasto` | `POST /admin/caja/editarcategoriagasto` |
| `eliminarCategoria` | `cajacontrolador::categoriaGasto` | `POST /admin/caja/categoriaGasto` |

El servicio protege las categorías base con ids 1 a 11, incluida la categoría
11 utilizada por los pagos de comisiones; evita nombres duplicados y rechaza
la eliminación cuando existen gastos relacionados. El
controlador reutiliza un único método privado para reconstruir la vista.

### `CajaReportesService` — completado para Z diario

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `obtenerIndiceZ` | `cajacontrolador::zetadiario` | `GET /admin/caja/zetadiario` |
| `obtenerDetalleZ` | `cajacontrolador::fechazetadiario` | `GET /admin/caja/fechazetadiario?id={selector}` |
| `consultarZPorRango` | `reportescontrolador::consultafechazetadiario` | `POST /admin/api/consultafechazetadiario` |

`obtenerDetalleZ` diferencia tres contratos: `-1` consolida todas las cajas
abiertas, `0` prepara la consulta interactiva por fechas y un id positivo carga
un cierre histórico de la sucursal. Este último enlace existía en la vista,
pero el controlador anterior no cargaba sus datos. `consultarZPorRango` valida
el formato y orden de las fechas, normaliza los IDs y comprueba que cajas y
facturadores activos pertenezcan a la sucursal antes de ejecutar las consultas.

### `CajaDocumentosService` — completado en el alcance actual

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `prepararFacturaCarta` | `cajacontrolador::printfacturacarta` | `GET /printfacturacarta?id={factura}` |
| `prepararCotizacion` | `cajacontrolador::printcotizacion` | `GET /printcotizacion?id={factura}` |
| `prepararDetalleCierre` | `cajacontrolador::printdetallecierre` y adaptador `cajaService` | `GET /printdetallecierre?id={cierre}` |
| `obtenerDetalleVenta` | adaptador temporal `cajaService::detalleVenta` | Compatibilidad interna |
| `prepararInvoiceParaImpresion` | `cajacontrolador::getInvoice` | `GET /admin/api/getInvoice?id={factura}` |

Factura y cotización comparten una única consulta de detalle. Las facturas y
cierres se validan contra la sucursal activa; una dirección histórica ausente
se representa con un objeto vacío en lugar de reutilizar la dirección global
con id 1. El controlador conserva autorización, parámetros, respuesta 404 y
selección de plantilla.

`prepararInvoiceParaImpresion` conserva el contrato `DataInvoice` usado por
las impresoras Bluetooth y de servidor. El controlador devuelve JSON 400 para
un identificador inválido y JSON 404 cuando la factura no existe, no tiene un
consecutivo válido o pertenece a otra sucursal.

### `CajaOrdenesService` — estabilizado en el alcance actual

| Método del servicio | Consumidor actual | Origen HTTP |
|---|---|---|
| `listarPedidosGuardados` | `cajacontrolador::pedidosguardados` | `GET /admin/caja/pedidosguardados` |
| `listarDespachosPendientes` | `cajacontrolador::despachosPendientes` | `GET /admin/caja/despachosPendientes` |
| `prepararResumenOrden` | `cajacontrolador::ordenresumen` | `GET /admin/caja/ordenresumen?id={factura}` |
| `obtenerMediosPagoFactura` | `cajacontrolador::mediospagoXfactura` | `GET /admin/api/mediospagoXfactura?id={factura}` |
| `cambiarMediosPagoFactura` | `cajacontrolador::cambioMedioPago` | `POST /admin/api/cambioMedioPago` |
| `eliminarPedidoGuardado` | `cajacontrolador::eliminarPedidoGuardado` | `POST /admin/api/eliminarPedidoGuardado` |
| `despacharOrden` | `cajacontrolador::despacharOrden` | `GET /admin/api/caja/despacharOrden?id={factura}` (temporal) |

La respuesta exitosa conserva el arreglo de modelos `factmediospago` esperado
por `caja.ts`. `listarPedidosGuardados` y `listarDespachosPendientes` conservan
las colecciones usadas por sus vistas. El controlador valida autenticación e
ID; el servicio comprueba que cada consulta quede limitada a la sucursal
activa.

`prepararResumenOrden` reutiliza el detalle validado de
`CajaDocumentosService` y agrega los catálogos requeridos por los modales. La
referencia de crédito se resuelve de forma segura y una factura inexistente o
de otra sucursal produce una respuesta 404 controlada desde el controlador.

`cambiarMediosPagoFactura` valida factura pagada, sucursal, cierre abierto,
medios existentes, valores positivos, ausencia de duplicados y conservación
del total pagado. Factura, cierre y relaciones de pago se bloquean y todas las
escrituras comparten una transacción. El ajuste de efectivo se calcula con los
pagos persistidos; los campos `efectivoDB` y `nuevoEfectivo` enviados todavía
por `caja.ts` se ignoran deliberadamente y podrán retirarse del frontend luego.

`eliminarPedidoGuardado` realiza una baja lógica exclusivamente sobre una
cotización que siga en estado `Guardado` y no haya sido convertida. Bloquea la
orden y su cierre, marca la orden como `Eliminada`, registra la fecha de
anulación y decrementa `totalcotizaciones` sin permitir valores negativos si
el período continúa abierto. Los cierres históricos se validan pero no se
reescriben, por lo que una cotización antigua no queda atrapada.

`despacharOrden` admite los estados históricos `Paga` y `Remision`, valida la
sucursal y bloquea la factura antes de comprobar si ya fue entregada. El
descuento de productos e insumos, sus movimientos y la fecha de entrega se
confirman en una sola transacción. Esto impide que dos solicitudes sobre la
misma orden descuenten inventario dos veces. La respuesta mantiene los arreglos
`exito` y `error` consumidos por `ordenresumen.ts`.

## Inventario de acciones

| Línea | Acción actual | Tipo | Destino propuesto | Observación de migración |
|---:|---|---|---|---|
| 41 | `index` | Consulta/vista | `CajaConsultasService` | Extraer la preparación del panel y facturas de cierres abiertos. |
| 79 | `cerrarcaja` | Consulta/vista | `CajaConsultasService` | Reutilizar un único resumen de cierre; hoy repite cálculos de otros métodos. |
| 161 | `ingresoGastoCaja` | Comando + vista | `CajaMovimientosService` | Separar el comando de ingreso/gasto de la reconstrucción posterior del panel. |
| 278 | `categoriaGasto` | Consulta + eliminación | `CategoriasGastoService` | Separar listar de eliminar. |
| 303 | `crear_categoriaGasto` | Comando | `CategoriasGastoService` | Crear categoría y devolver un resultado independiente de la vista. |
| 325 | `editarcategoriagasto` | Comando | `CategoriasGastoService` | Validar pertenencia y actualizar. |
| 348 | `zetadiario` | Consulta/vista | `CajaReportesService` | Listado de cierres para el reporte Z. |
| 366 | `fechazetadiario` | Consulta/vista | `CajaReportesService` | Consolidación del reporte Z actual o por cierres. |
| 421 | `ultimoscierres` | Consulta/vista | `CajaConsultasService` | Listado de cierres finalizados. |
| 431 | `detallecierrecaja` | Consulta/vista | `CajaConsultasService` | Compartir el constructor del resumen con `cerrarcaja` y `datoscajaseleccionada`. |
| 496 | `pedidosguardados` | Consulta/vista | `CajaOrdenesService` | Migrado: consulta cotizaciones guardadas por sucursal. |
| 506 | `trasladosRetirosDinero` | Solo vista | Controlador por ahora | No contiene todavía un caso de uso que extraer. |
| 514 | `despachosPendientes` | Consulta/vista | `CajaOrdenesService` | Migrado: obtiene órdenes pendientes por sucursal. |
| 523 | `ordenresumen` | Consulta/vista | `CajaOrdenesService` | Migrado: construye el detalle operativo completo de una orden. |
| 564 | `detalleorden` | Solo vista | Controlador por ahora | Es un placeholder sin lógica de aplicación. |
| 576 | `printfacturacarta` | Documento/vista | `CajaDocumentosService` | Preparar datos de factura y medios de pago. |
| 588 | `printcotizacion` | Documento/vista | `CajaDocumentosService` | Preparar datos de cotización. |
| 597 | `printdetallecierre` | Documento/vista | `CajaDocumentosService` | Consumirá el resumen generado por consultas. |
| 614 | `declaracionDinero` | Comando/API | `CajaCierreService` | Crear, actualizar o eliminar la declaración por medio de pago. |
| 647 | `arqueocaja` | Comando/API | `CajaCierreService` | Crear o actualizar el arqueo del cierre abierto. |
| 680 | `cierrecajaconfirmado` | Comando/API | `CajaCierreService` | Operación crítica; debe quedar transaccional antes de restaurante. |
| 747 | `datoscajaseleccionada` | Consulta/API | `CajaConsultasService` | Tercera variante del mismo resumen financiero de cierre. |
| 817 | `mediospagoXfactura` | Consulta/API | `CajaOrdenesService` | Migrado: aplica autenticación y valida factura y sucursal. |
| 824 | `cambioMedioPago` | Comando/API | `CajaOrdenesService` | Migrado: transaccional y recalcula el efectivo desde datos persistidos. |
| 917 | `eliminarPedidoGuardado` | Comando/API | `CajaOrdenesService` | Migrado: baja lógica transaccional con validación de sucursal y estado. |
| 939 | `sendOrdenEmailToCustemer` | Integración/API | Aplazado | El proveedor actual no permite correo ni SMTP; se conserva sin migrar. |
| 977 | `getInvoice` | Consulta/API | `CajaDocumentosService` | Migrado: construye el DTO sin conocer HTTP y valida la sucursal. |
| 988 | `despacharOrden` | Comando/API | `CajaOrdenesService` | Migrado: valida sucursal y estado; bloquea la orden y despacha transaccionalmente. |
| 998 | `cambiarEmisor` | Comando/API | Aplazado | Permanece en `cajaService` por decisión del propietario. |

Las líneas corresponden al estado de `cajacontrolador.php` al crear este
inventario y pueden desplazarse durante migraciones posteriores.

## Destino del `cajaService` actual

| Método actual | Destino futuro |
|---|---|
| `printdetallecierre` | Ya delega en `CajaDocumentosService`, que reutiliza los cálculos de `CajaConsultasService` |
| `detalleVenta` | Ya delega en `CajaDocumentosService` para conservar compatibilidad interna |
| `despacharOrden` | Ya delega en `CajaOrdenesService` como adaptador temporal |
| `cambiarEmisor` | Aplazado; conserva su implementación actual en `cajaService` |

El archivo existente no debe eliminarse hasta que cada consumidor haya sido
migrado y cubierto por pruebas.

## Pendientes explícitamente aplazados

| Pendiente | Estado y motivo |
|---|---|
| `cambiarEmisor()` | Aplazado por decisión del propietario. Conserva su implementación en `cajaService` y no se modifica en esta fase. |
| `sendOrdenEmailToCustemer()` | Aplazado porque el proveedor de nube actual no permite correo o SMTP. Se conserva sin refactorizar. |
| Despacho mediante `GET` | Aplazado hasta autorizar el cambio coordinado de ruta y consumidor TypeScript. Debe migrarse a `POST`. |

Estos puntos no impiden considerar estabilizada la extracción actual. Tampoco
se elimina `cajaService`, porque `cambiarEmisor()` continúa dependiendo de él.

## Hallazgos cerrados y deuda conservada

1. ~~`cerrarcaja`, `detallecierrecaja`, `datoscajaseleccionada` y
   `cajaService::printdetallecierre` duplican el armado del resumen.~~ La lógica
   compartida ya está concentrada en `CajaConsultasService`.
2. ~~`ingresoGastoCaja` mezcla escritura, carga de archivos y render de vista.~~
   El movimiento ya está extraído; la carga del comprobante y el render se
   mantienen deliberadamente en el controlador. El `debuguear($facturas)` fue
   eliminado antes de esta etapa.
3. ~~`cierrecajaconfirmado` usaba compensaciones manuales.~~ El cierre actual,
   el siguiente período y la base automática ya comparten una transacción.
4. ~~`cambioMedioPago` actualizaba múltiples tablas sin una frontera
   transaccional.~~ Ahora bloquea factura, cierre y pagos y confirma todo como
   una única operación.
5. ~~`mediospagoXfactura` no aplicaba autenticación ni validaba la sucursal.~~
   La consulta, el cambio de pagos y la eliminación de pedidos guardados ya
   están protegidos por autenticación y validación de sucursal.
6. `despacharOrden` ya es transaccional. Su migración de GET a POST queda en la
   tabla de pendientes explícitos.
7. Los servicios futuros deben comprobar siempre la pertenencia de caja,
   cierre, factura y orden a la sucursal activa.

## Reglas para la extracción

- El controlador conserva `Router`, `$_GET`, `$_POST`, `$_FILES`, `$_SESSION`,
  `echo`, `json_encode`, `header` y selección de vistas.
- Los servicios no pueden depender de esas variables o funciones HTTP.
- Cada comando recibe datos normalizados más el identificador de sucursal y
  usuario necesarios.
- Los servicios devuelven resultados PHP; el controlador decide el formato
  HTTP.
- Toda operación que modifica más de una tabla debe tener una sola transacción.
- Los repositorios y ActiveRecord quedan como infraestructura de persistencia.
- La extracción debe mantener la respuesta actual antes de corregir reglas o
  rutas; los cambios funcionales se realizan en una fase posterior.

## Cierre de la fase

1. Las pruebas automatizadas de caracterización quedan como siguiente mejora;
   los flujos migrados cuentan por ahora con validación manual y estática.
2. ~~Extraer el resumen duplicado a `CajaConsultasService`.~~ Completado.
3. ~~Extraer `declaracionDinero`, `arqueocaja` y `cierrecajaconfirmado`.~~ Completado.
4. ~~Extraer ingresos y gastos.~~ Completado.
5. Los adaptadores compatibles fueron conservados en `cajaService`; sólo
   `cambiarEmisor()` mantiene allí lógica aplazada.
6. Extraer categorías, reportes y documentos. Categorías, Z diario, las tres
   vistas imprimibles y el DTO de impresión están completados; el correo quedó
   aplazado por la restricción actual del proveedor.
7. La división futura del controlador y las rutas queda fuera de esta fase. El
   controlador actual permanece como una frontera HTTP única y más delgada.
