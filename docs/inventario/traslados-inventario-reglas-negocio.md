# Traslados de inventario: reglas de negocio

Estado del documento: reglas de negocio confirmadas.

Este documento define el comportamiento que debe conservar la migración de
`trasladosinvcontrolador` hacia servicios. Las rutas HTTP y los formatos de
respuesta no forman parte de estas reglas; pertenecen a la capa de controlador.

## 1. Significado de la orden

Una fila de `traslado_inv` representa una orden entre dos sucursales.

- `id_sucursalorigen`: sucursal que crea o inicia la orden.
- `id_sucursaldestino`: sucursal contraparte a la que se dirige la orden.
- `fkusuario`: usuario que creó la orden.
- `tipo`: intención de la orden. Los únicos valores admitidos por el nuevo flujo
  son `Solicitud` y `Salida`, respetando mayúsculas y minúsculas.
- `estado`: situación actual de la orden.
- `observacion`: nota opcional de la orden.

Los nombres `id_sucursalorigen` e `id_sucursaldestino` expresan quién inicia la
orden y a quién se dirige. Cuando el tipo es `Solicitud`, no representan el
origen y destino físicos de la mercancía.

## 2. Dirección física de la mercancía

| Tipo | Sucursal que despacha | Sucursal que recibe |
|---|---|---|
| `Salida` | `id_sucursalorigen` | `id_sucursaldestino` |
| `Solicitud` | `id_sucursaldestino` | `id_sucursalorigen` |

Estas reglas son la única fuente para determinar qué inventario se descuenta y
qué inventario se incrementa.

## 3. Consultas desde la sucursal actual

### Órdenes en las que soy destino administrativo

Filtro:

```text
id_sucursaldestino = id_sucursal_actual
```

- Una `Solicitud` indica que otra sucursal está solicitando mercancía a la
  sucursal actual. La sucursal actual debe despachar.
- Una `Salida` indica que otra sucursal está enviando mercancía a la sucursal
  actual. La sucursal actual debe recibir.

### Órdenes en las que soy origen administrativo

Filtro:

```text
id_sucursalorigen = id_sucursal_actual
```

- Una `Solicitud` indica que la sucursal actual solicitó mercancía a la sucursal
  destino. Debe esperar el despacho y después confirmar la recepción.
- Una `Salida` indica que la sucursal actual enviará mercancía a la sucursal
  destino. La sucursal actual debe despachar.

Una sucursal que no sea origen ni destino de la orden no puede consultarla.

## 4. Estados y transiciones

El flujo operativo confirmado es:

```text
creación                  despacho                 recepción
   └──> pendiente ─────────────────> entransito ───────────────> entregada
```

Reglas:

1. Toda orden nueva se crea en `pendiente`.
2. Solamente una orden `pendiente` puede despacharse.
3. El despacho completo cambia la orden a `entransito`.
4. Solamente una orden `entransito` puede recibirse.
5. La recepción completa cambia la orden a `entregada`.
6. No se permiten saltos directos de `pendiente` a `entregada`.
7. Una orden `entregada` es final y no puede editarse, despacharse, recibirse ni
   eliminarse.
8. El valor `aprobada` existe en la base de datos, pero el flujo actual no lo
   genera ni le asigna una operación. Queda fuera del nuevo flujo hasta que se
   defina una necesidad de negocio.

La cancelación o el rechazo de una orden `pendiente` cambia su estado a
`rechazada`, de acuerdo con las reglas de la sección 10.

## 5. Autorización por operación

| Operación | Estado requerido | Sucursal autorizada |
|---|---|---|
| Crear `Salida` | Nueva orden | La sucursal actual queda registrada como origen |
| Crear `Solicitud` | Nueva orden | La sucursal actual queda registrada como origen |
| Consultar | Cualquiera | Origen o destino de la orden |
| Editar | `pendiente` | Origen de la orden |
| Cancelar | `pendiente` | Origen de la orden |
| Rechazar | `pendiente` | Destino de la orden |
| Despachar `Salida` | `pendiente` | Origen de la orden |
| Despachar `Solicitud` | `pendiente` | Destino de la orden |
| Recibir `Salida` | `entransito` | Destino de la orden |
| Recibir `Solicitud` | `entransito` | Origen de la orden |

La autorización debe comprobarse en el servidor. Los identificadores enviados
por el navegador y la visibilidad de botones no constituyen autorización.

## 6. Creación de una orden

Una orden nueva debe cumplir todas estas condiciones:

1. La sucursal de origen existe y coincide con la sucursal de la sesión.
2. La sucursal destino existe y está habilitada.
3. Origen y destino son diferentes.
4. El tipo es exactamente `Solicitud` o `Salida`.
5. El usuario creador existe y corresponde al usuario autenticado.
6. La orden contiene por lo menos un detalle válido.
7. La cabecera y todos los detalles se guardan como una sola operación atómica.
8. Si falla cualquier detalle, no se conserva la cabecera ni ningún otro
   detalle de esa orden.

## 7. Detalles de la orden

Cada fila de `detalletrasladoinv` debe cumplir:

1. Pertenece a la orden que se está procesando.
2. Representa exactamente un artículo:
   - `fkproducto` informado e `idsubproducto_id` nulo; o
   - `idsubproducto_id` informado y `fkproducto` nulo.
3. El artículo existe.
4. `cantidad` es numérica y mayor que cero.
5. Al crear la orden, `cantidadrecibida` y `cantidadrechazada` comienzan en cero.
6. No se confía en `id_trasladoinv` ni en el identificador del detalle enviado
   por el cliente: el servidor establece y valida esa relación.

Durante la edición solamente pueden modificarse los detalles de una orden
`pendiente`. Un identificador de detalle perteneciente a otra orden debe ser
rechazado.

## 8. Despacho

El despacho es una única operación de negocio:

1. Se obtiene y bloquea la orden.
2. Se comprueba que siga en `pendiente`.
3. Se determina la sucursal que despacha utilizando la tabla de la sección 2.
4. Se comprueba que esa sucursal sea la sucursal actual.
5. Se bloquean las existencias de todos los artículos involucrados.
6. Se valida que exista stock suficiente para cada artículo.
7. Se descuentan todas las cantidades.
8. Se registra un movimiento de salida por cada modificación de stock.
9. La orden cambia a `entransito`.
10. Todos los cambios se confirman juntos.

Si cualquiera de estos pasos falla, no se descuenta inventario, no se crean
movimientos y la orden permanece en `pendiente`.

Una orden no puede despacharse dos veces.

## 9. Recepción

La recepción es una única operación de negocio:

1. Se obtiene y bloquea la orden.
2. Se comprueba que siga en `entransito`.
3. Se determina la sucursal receptora utilizando la tabla de la sección 2.
4. Se comprueba que esa sucursal sea la sucursal actual.
5. Se bloquean o crean de forma segura las existencias correspondientes.
6. Se suman todas las cantidades recibidas.
7. Se registra un movimiento de ingreso por cada modificación de stock.
8. La orden cambia a `entregada`.
9. Todos los cambios se confirman juntos.

Si cualquiera de estos pasos falla, no se incrementa inventario, no se crean
movimientos y la orden permanece en `entransito`.

Una orden no puede recibirse dos veces.

Las notificaciones externas, como WhatsApp, se envían únicamente después de
confirmar la operación. Un fallo de notificación no revierte el inventario.

## 10. Cancelación, recepción parcial y aprobación

### Cancelación y rechazo

- El origen puede cancelar una orden mientras esté `pendiente`.
- El destino puede rechazar una orden mientras esté `pendiente`, tanto si es
  `Solicitud` como si es `Salida`.
- Ambas acciones conservan la orden y cambian su estado a `rechazada`.
- No se elimina físicamente la cabecera ni sus detalles.
- Una orden `entransito`, `entregada` o `rechazada` no puede cancelarse ni
  rechazarse.

### Recepción parcial

La tabla contiene `cantidadrecibida` y `cantidadrechazada`, pero esta migración
conserva únicamente la recepción total. La recepción parcial queda fuera del
alcance inicial.

### Estado `aprobada`

El valor se conserva por compatibilidad con la base de datos, pero no se utiliza
en los nuevos servicios. Cualquier uso futuro requiere definir previamente qué
actor aprueba y qué efecto produce la aprobación.

## 11. Invariantes del módulo

Después de cada operación siempre debe cumplirse:

- Una orden tiene dos sucursales distintas y existentes.
- Cada detalle pertenece a una única orden y representa un único artículo.
- Toda cantidad ordenada es mayor que cero.
- Todo descuento o incremento de stock tiene su movimiento correspondiente.
- Una operación fallida no deja cambios parciales.
- El estado de la orden concuerda con los movimientos realizados.
- Solo una sucursal participante puede consultar la orden.
- Solo la sucursal físicamente responsable puede despachar o recibir.

## 12. Alcance de la migración inicial

La primera migración conservará:

- Los nombres `Solicitud` y `Salida`.
- Los valores actuales `pendiente`, `entransito` y `entregada`.
- Las rutas HTTP existentes.
- El formato de respuesta esperado por las pantallas actuales, salvo errores
  evidentes que se acuerde corregir.

No se incluirán inicialmente:

- Recepciones parciales.
- Nuevas etapas de aprobación.
- Traslados entre más de dos sucursales.
- Cambios generales al resto del módulo de inventario.
