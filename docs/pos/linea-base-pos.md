# Línea base del POS antes de migrar el MVC antiguo

**Fecha de verificación:** 8 de octubre de 2026.  
**Alcance:** las seis rutas de `app/Modules/POS/routes.php` y sus efectos inmediatos en ventas, caja e inventario.  
**Estado:** comportamiento descrito a partir del código y contrastado parcialmente con pruebas integradas sobre una copia temporal de `contapos`.

## Punto de entrada y dependencias

`public/index.php` carga los módulos habilitados en `config/tenants.php` antes de registrar las rutas antiguas. `POS/routes.php` apunta a `App\Modules\POS\Controllers\ventascontrolador`. El archivo antiguo `app/Controllers/ventascontrolador.php` permanece en el repositorio; al momento de esta línea base, ambos controladores difieren solo en el namespace.

El controlador modular todavía utiliza modelos de `app/Models`, `App\services\facturacionService`, `App\services\ventasService`, vistas de `views/admin/ventas` y TypeScript de `src/ts/ventas` y `src/ts/caja/ordenresumen.ts`. `Router::render()` resuelve la vista en el directorio raíz `views`.

```text
Navegador / TypeScript
  -> rutas POS -> ventascontrolador
  -> facturacionService -> facturas y ventas
                         -> cierre y movimientos de caja
                         -> stock y movimientos de inventario
                         -> crédito, impuestos, comisión y datos fiscales según el caso
```

`facturacionService` abre una transacción `mysqli` para crear o convertir una orden y otra para anularla. `ventasService` participa en esa misma conexión cuando recibe `manejarTransaccion=false`. Una futura separación entre POS, Cash e Inventory debe conservar una sola operación atómica para factura, pago, cierre, líneas y existencias.

## Contratos HTTP actuales

Todas las rutas requieren una sesión administrativa mediante `isadmin()`. `index`, `facturar`, `facturarCotizacion` y `eliminarOrden` también verifican el permiso `Habilitar modulo de venta` para perfiles superiores a 3. Las dos consultas GET de API solo verifican `isadmin()`.

| Método y ruta | Entrada | Respuesta y efectos | Verificación |
|---|---|---|---|
| `GET /admin/ventas` | `id` opcional de cotización o remisión recuperable | Renderiza `admin/ventas/index`; prepara productos, categorías, clientes, medios de pago, cajas, consecutivos, vendedores, canales y alertas de resolución. Con `id`, carga la orden y sus líneas. | Vista y datos principales preparados mediante controlador real y `Router` espía. |
| `POST /admin/api/facturar` | Formulario de venta descrito abajo | JSON con `exito` o `error`. Puede guardar cotización/remisión, editar orden, redimir puntos o facturar. Una factura pagada añade `idfactura`, `dataInvoice` y, en crédito, posiblemente `idcuota`. | Cotización, venta directa de contado y carrito vacío probados. Otros estados pendientes. |
| `POST /admin/api/facturarCotizacion` | `id`, `idcaja`, `idconsecutivo`, `mediosPago`; además `recibido`, `transaccion`, `observacion` y datos de caja enviados por `ordenresumen.ts` | Fuerza `estado=Paga` y `tipoventa=Contado`, recarga líneas de la orden desde BD y devuelve el contrato de factura pagada. La cotización origen pasa a `Aceptada` con `cambioaventa=1`; crea factura, pago y líneas. | Conversión real y rechazo de `estado=Guardado` probados. |
| `POST /admin/api/eliminarOrden` | `id`, `observacioneliminacion`, `devolverinv` (`0` o `1`), `inv` como lista JSON si devuelve inventario | JSON `exito` o `error`. Cambia la orden a `Eliminada`; para factura pagada revierte indicadores y movimiento de caja, y opcionalmente existencias. Exige cierre abierto. | Anulación de factura con devolución completa y rechazo de `id=0` probados. |
| `GET /admin/api/getcotizacion_venta?id=...` | `id` numérico | JSON con `exito`, `factura`, `productos` cuando la orden es cotización/remisión no convertida de la sucursal actual; en otros estados devuelve `error` o cuerpo vacío para ID no numérico. | Lectura de cotización recién creada probada. |
| `GET /admin/api/ventas/detalleProductoCompuesto?idproducto=...&idfactura=...` | Dos identificadores numéricos | JSON con una lista de insumos: `id`, `id_producto`, `id_subproducto`, `cantidadcalculada`, `costo`, `nombre`, `sku`, `precio_compra`, `unidadmedida`, `simbolo`, `disponibilidad`, `stockminimo`. | Lectura de una línea compuesta existente probada. |

### Formulario principal de `facturar`

`src/ts/ventas/ventas.ts::procesarpedido` envía `FormData`. Sus grupos relevantes son:

- **Identidad y contexto:** `id` opcional de orden previa, `idcliente`, `idvendedor`, `idcaja`, `idconsecutivo`, `idcanaldeventa`, `idemisor`, `iddireccion`, `idtarifazona` y campos descriptivos de cliente, vendedor, caja, facturador, dirección y tarifa.
- **Detalle:** `carrito` es un arreglo JSON de líneas con producto, tipo, cantidad (`stock`), precio, impuesto, base, descuento, total e insumos configurados; `factimpuestos` es un arreglo JSON.
- **Pago:** `mediosPago` es un arreglo JSON de `{idmediopago, id_factura, valor}`; también llegan `recibido`, `transaccion`, `tipoventa` y `valoresCredito` como objeto JSON.
- **Tipo de operación:** `estado` puede ser `Paga`, `Guardado`, `Remision` o `Redimido`; llegan `cotizacion`, `remision`, `entrega`, `entregado` y `puntos_descontados`.
- **Importes y auditoría:** `subtotal`, `base`, `valorimpuestototal`, `descuento`, `total`, `totalunidades`, comisión, `observacion` y `datosAdquiriente` como objeto JSON.

El servicio valida los discriminadores y la forma JSON, prepara inventario, comprueba disponibilidad según la configuración de la sucursal y luego selecciona el flujo transaccional. El navegador considera éxito cuando existe `exito`; cuando existe `error`, muestra el primer mensaje. Para facturas pagadas utiliza `idfactura` y `dataInvoice` para imprimir y, según el facturador, iniciar el envío electrónico desde el frontend.

### Efectos de negocio comprobados

1. `Guardado` crea un registro en `facturas` y su línea en `ventas`, incrementa `cierrescajas.totalcotizaciones` y no descuenta stock.
2. Convertir esa cotización crea una factura `Paga`, marca el origen `Aceptada`, registra un medio de pago y una línea nueva, y descuenta una unidad del producto simple utilizado.
3. Anular la factura pagada marca su estado `Eliminada` y, con `devolverinv=1` y una línea `{idventa, cantidad}`, restituye la unidad al inventario. La orden origen continúa identificable como orden convertida.
4. La venta directa de contado crea una factura `Paga` con pago y línea, devuelve `idfactura` y `dataInvoice`, y descuenta una unidad. Su anulación con devolución restaura las existencias.

## Prueba repetible y resultado

Ejecutar desde la raíz del proyecto:

```powershell
php tests/pos-baseline.php
```

El script lee las credenciales de `includes/.env`, clona las 109 tablas actuales de `contapos` a una base con nombre aleatorio `codex_pos_baseline_*`, desactiva solo en esa copia las notificaciones WhatsApp de stock bajo y anulación, ejecuta los controladores reales desde PHP CLI y elimina la copia en `finally`. **No modifica `contapos`**. Necesita permisos MySQL para crear y eliminar la base temporal. No ejecuta el servidor HTTP, el navegador, la impresión ni el envío a DIAN.

Resultado del 8 de octubre de 2026: **22 comprobaciones correctas, 0 fallidas**. Se registraron las 3 rutas GET y 3 POST. La vista preparó 51 productos y 4 cajas. La cotización incrementó el contador de 4 a 5 sin mover el stock (11 a 11). La conversión descontó una unidad (11 a 10), creó factura, línea y pago; la anulación devolvió el stock a 11. Una venta directa y su anulación repitieron el ciclo de inventario 11 → 10 → 11. También se comprobó un detalle compuesto con 3 insumos y el rechazo de carrito vacío, estado inválido al convertir e identificador inválido al anular.

Los IDs de factura y los conteos concretos pueden cambiar si cambian los datos de origen. Las afirmaciones importantes para futuras comparaciones son las transiciones, la forma de las respuestas y los efectos relacionados.

## Límites y hallazgos para la siguiente fase

- La prueba integrada cubre cotización, conversión a pago de contado, venta directa y anulación con devolución. Aún faltan remisión, crédito, redención, edición de cotización, anulación parcial, facturación electrónica, concurrencia y recorrido completo por navegador/HTTP.
- `getcotizacion_venta` e `index?id=...` acceden a propiedades de la orden sin comprobar primero si la consulta devolvió `null`. Un identificador numérico inexistente puede producir advertencias PHP en vez de una respuesta limpia.
- `detalleProductoCompuesto` verifica que los parámetros sean numéricos, pero no comprueba que la factura consultada pertenezca a la sucursal de la sesión. Esto se detectó por inspección del código; no se usó como prueba de acceso entre sucursales.
- Al mover rutas futuras desde `public/index.php` a módulos, conservar los controladores antiguos como respaldo de código, pero registrar cada URL y método HTTP una sola vez. El enrutador sobrescribe una ruta si se vuelve a declarar más adelante.
