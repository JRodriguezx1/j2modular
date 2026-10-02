# Traslados de inventario: contratos de servicios

Estado del documento: contrato técnico definido.

Este documento traduce las reglas confirmadas en
`traslados-inventario-reglas-negocio.md` a responsabilidades y firmas de
servicios. No modifica todavía el comportamiento del módulo.

## 1. Criterios de diseño

La migración seguirá estas decisiones:

- PHP 8.1 y clases `final`.
- Los servicios usan el namespace `App\services\trasladosinventario`.
- La política usa el namespace `App\Policies`, siguiendo la ubicación existente
  para políticas de autorización.
- El controlador conserva la lectura de `$_GET`, `$_POST` y `$_SESSION`.
- Los servicios reciben explícitamente la sucursal y el usuario que ejecutan la
  operación.
- Los servicios no llaman `id_sucursal()`, `isadmin()` ni leen variables
  globales.
- Se mantienen inicialmente las rutas y la forma general de las respuestas JSON.
- Los errores esperados de negocio se representan internamente con
  `DomainException` y se devuelven como `['error'=>['mensaje']]`.
- Los errores inesperados se registran en el log, revierten la transacción si
  corresponde y devuelven un mensaje genérico.
- No se crearán interfaces, DTOs ni repositorios específicos mientras no exista
  una segunda implementación que los justifique.

## 2. Estructura propuesta

```text
app/
├── Policies/
│   └── TrasladoPolicy.php
└── services/
    └── trasladosinventario/
        ├── TrasladosConsultaService.php
        ├── TrasladosOrdenService.php
        └── TrasladosInventarioService.php
```

`trasladosinventario` queda al mismo nivel que `caja`, `configuracion` e
`inventario`. El nombre completo evita confundir este módulo con los traslados
de dinero que también existen en el proyecto.

## 3. Forma común de las respuestas

Los comandos devuelven arreglos compatibles con el frontend actual.

Éxito:

```php
[
    'exito' => ['Mensaje para el usuario.'],
    'data' => [
        'id' => 123,
        'tipo' => 'Salida',
        'estado' => 'pendiente',
    ],
]
```

Error de validación, autorización o estado:

```php
[
    'error' => ['Mensaje comprensible para el usuario.'],
]
```

Reglas del contrato:

1. Un resultado no contiene simultáneamente `exito` y `error`.
2. `exito` y `error` siempre son arreglos de mensajes para conservar
   compatibilidad.
3. `data` es opcional y contiene valores necesarios para actualizar la interfaz.
4. Los servicios no imprimen JSON, no establecen códigos HTTP y no redirigen.
5. El controlador es responsable de convertir el resultado a HTTP/JSON.

## 4. Entrada normalizada de una orden

El controlador adapta los nombres del formulario actual a esta estructura antes
de invocar el servicio:

```php
[
    'sucursal_destino_id' => 2,
    'observacion' => 'Texto opcional',
    'items' => [
        [
            'tipo' => 'producto',
            'item_id' => 10,
            'cantidad' => 3.5,
        ],
        [
            'tipo' => 'insumo',
            'item_id' => 7,
            'cantidad' => 2,
        ],
    ],
]
```

El servicio no recibe `id_sucursalorigen`: siempre utiliza la sucursal actual
entregada como argumento. Tampoco recibe `estado`, `fkusuario`, cantidades
recibidas o cantidades rechazadas desde el cliente.

Para editar se utiliza la misma estructura. `items` representa el estado final
completo que deben tener los detalles de la orden; el servicio calcula qué
insertar, actualizar o retirar.

## 5. `TrasladoPolicy`

Responsabilidad: resolver participantes, dirección física y permisos sin leer
HTTP, sesión o base de datos.

Firma propuesta:

```php
namespace App\Policies;

use App\Models\inventario\traslado_inv;

final class TrasladoPolicy
{
    public function esTipoValido(string $tipo): bool;
    public function esParticipante(traslado_inv $orden, int $sucursalId): bool;
    public function obtenerSucursalDespacho(traslado_inv $orden): int;
    public function obtenerSucursalRecepcion(traslado_inv $orden): int;
    public function puedeEditar(traslado_inv $orden, int $sucursalId): bool;
    public function puedeCancelar(traslado_inv $orden, int $sucursalId): bool;
    public function puedeRechazar(traslado_inv $orden, int $sucursalId): bool;
    public function puedeDespachar(traslado_inv $orden, int $sucursalId): bool;
    public function puedeRecibir(traslado_inv $orden, int $sucursalId): bool;
}
```

La política es una dependencia instanciable. Su ubicación en `app/Policies` no
obliga a que tenga el mismo diseño estático de otras políticas del proyecto.

Contrato de cada permiso:

| Método | Regla |
|---|---|
| `esParticipante` | La sucursal es origen o destino administrativo |
| `puedeEditar` | Estado `pendiente` y sucursal origen |
| `puedeCancelar` | Estado `pendiente` y sucursal origen |
| `puedeRechazar` | Estado `pendiente` y sucursal destino |
| `puedeDespachar` | Estado `pendiente` y sucursal física de despacho |
| `puedeRecibir` | Estado `entransito` y sucursal física receptora |

`obtenerSucursalDespacho()` y `obtenerSucursalRecepcion()` lanzan
`DomainException` si la orden tiene un tipo diferente de `Solicitud` o `Salida`.

La política no consulta permisos generales del módulo. `isadmin()` y
`tienePermiso('Habilitar modulo de inventario')` continúan siendo responsabilidad
de la entrada HTTP antes de llamar al servicio.

## 6. `TrasladosConsultaService`

Responsabilidad: lecturas del módulo, siempre limitadas a una sucursal
participante. No modifica datos y no inicia transacciones de escritura.

Firma propuesta:

```php
namespace App\services\trasladosinventario;

use App\Policies\TrasladoPolicy;

final class TrasladosConsultaService
{
    public function __construct(?TrasladoPolicy $policy = null);

    public function listarComoDestino(int $sucursalId): array;
    public function listarComoOrigen(int $sucursalId): array;
    public function obtenerDetalle(int $trasladoId, int $sucursalId): ?object;
    public function obtenerParaEditar(int $trasladoId, int $sucursalId): ?object;
}
```

### `listarComoDestino()`

Devuelve los datos utilizados por la pantalla de órdenes recibidas:

```php
[
    'ordenes' => [],
    'contadores' => [
        'pendiente' => 0,
        'aprobada' => 0,
        'entransito' => 0,
        'entregada' => 0,
        'rechazada' => 0,
    ],
]
```

Las órdenes se obtienen mediante `idregistros()` y sus usuarios y sucursales se
cargan por lotes mediante `IN_Where()`. La cantidad de consultas permanece fija
y no crece por cada orden. Si el usuario creador fue eliminado, su nombre queda
vacío y la orden continúa siendo visible.

### `listarComoOrigen()`

Tiene el mismo contrato de salida, filtrando por `id_sucursalorigen`.

### `obtenerDetalle()`

- Valida que los identificadores sean positivos.
- Devuelve `null` si la orden no existe o la sucursal no participa.
- Devuelve un objeto con la cabecera, `observacion` y todos los detalles en la
  propiedad `detalletrasladoinv`.
- Nunca devuelve una orden perteneciente exclusivamente a otras sucursales.

### `obtenerParaEditar()`

- Devuelve un objeto o `null` si la orden no existe, no está `pendiente` o la
  sucursal actual no es su origen.
- Incluye los datos de cabecera necesarios por el formulario de edición; los
  detalles continúan obteniéndose mediante `obtenerDetalle()`.

## 7. `TrasladosOrdenService`

Responsabilidad: crear órdenes, editar sus detalles y ejecutar la transición
`pendiente -> rechazada` sin modificar existencias.

Firma propuesta:

```php
namespace App\services\trasladosinventario;

use App\Policies\TrasladoPolicy;

final class TrasladosOrdenService
{
    public function __construct(?TrasladoPolicy $policy = null);

    public function crearSolicitud(
        array $datos,
        int $sucursalId,
        int $usuarioId
    ): array;

    public function crearSalida(
        array $datos,
        int $sucursalId,
        int $usuarioId
    ): array;

    public function editar(
        int $trasladoId,
        array $datos,
        int $sucursalId,
        int $usuarioId
    ): array;

    public function cancelar(
        int $trasladoId,
        int $sucursalId,
        int $usuarioId
    ): array;

    public function rechazar(
        int $trasladoId,
        int $sucursalId,
        int $usuarioId
    ): array;
}
```

### Creación

`crearSolicitud()` y `crearSalida()` comparten internamente una implementación,
pero se exponen por separado para que el controlador no pueda intercambiar el
tipo usando un valor enviado por el cliente.

Ambos métodos:

1. Validan sucursal, usuario, observación e items.
2. Fuerzan origen, usuario, tipo y estado desde argumentos confiables.
3. Guardan cabecera y detalles en una transacción.
4. Devuelven el ID, tipo y estado de la nueva orden.

### Edición

`editar()`:

1. Bloquea la orden.
2. Aplica `$this->policy->puedeEditar()`.
3. Valida que cada detalle existente corresponda a esa orden.
4. Sincroniza la lista completa dentro de una transacción.
5. No permite cambiar origen, tipo, estado ni usuario creador.
6. Puede actualizar destino y observación únicamente mientras la orden siga
   `pendiente`.

El usuario que edita se recibe para trazabilidad futura, aunque el esquema
actual no posee columnas de actualización.

### Cancelación y rechazo

- `cancelar()` solamente puede ser ejecutado por la sucursal origen.
- `rechazar()` solamente puede ser ejecutado por la sucursal destino.
- Ambos bloquean la orden y exigen estado `pendiente`.
- Ambos cambian el estado a `rechazada` y conservan cabecera y detalles.
- Ninguno modifica inventario.

## 8. `TrasladosInventarioService`

Responsabilidad: ejecutar las operaciones atómicas que modifican orden, stock y
movimientos de inventario.

Firma propuesta:

```php
namespace App\services\trasladosinventario;

use App\Policies\TrasladoPolicy;

final class TrasladosInventarioService
{
    public function __construct(?TrasladoPolicy $policy = null);

    public function despachar(
        int $trasladoId,
        int $sucursalId,
        int $usuarioId,
        string $nombreUsuario
    ): array;

    public function recibir(
        int $trasladoId,
        int $sucursalId,
        int $usuarioId,
        string $nombreUsuario
    ): array;
}
```

### `despachar()`

Garantiza dentro de una transacción:

- Bloqueo de la orden antes de validar su estado.
- Autorización mediante `puedeDespachar()`.
- Bloqueo de los artículos y sus stocks en orden estable.
- Existencia de stock configurado para cada artículo.
- Stock suficiente antes de hacer cualquier descuento.
- Descuento completo de productos e insumos.
- Movimiento de salida por cada stock modificado.
- Transición final de `pendiente` a `entransito`.

Devuelve como mínimo:

```php
[
    'exito' => ['Orden despachada correctamente.'],
    'data' => [
        'id' => 123,
        'tipo' => 'Salida',
        'estado' => 'entransito',
        'notificar_despacho' => true,
    ],
]
```

El indicador de notificación solo se devuelve después del `commit`.

### `recibir()`

Garantiza dentro de una transacción:

- Bloqueo de la orden antes de validar su estado.
- Autorización mediante `puedeRecibir()`.
- Bloqueo seguro de los artículos y stocks receptores.
- Incremento completo de productos e insumos.
- Movimiento de ingreso por cada stock modificado.
- Actualización de `cantidadrecibida` con la cantidad total y
  `cantidadrechazada` en cero.
- Transición final de `entransito` a `entregada`.

La primera migración considera error que un artículo no tenga stock configurado
en la sucursal receptora. No creará esa configuración implícitamente.

## 9. Notificaciones externas

WhatsApp no forma parte de la transacción de inventario.

Flujo acordado:

```text
controlador
    -> TrasladosInventarioService::despachar()
    -> commit exitoso
    -> notificación de WhatsApp
```

El controlador puede mantener temporalmente la llamada actual a
`whatsAppService`, ejecutándola únicamente cuando el resultado indique
`notificar_despacho = true`. En una etapa posterior podrá extraerse un adaptador
de notificaciones sin alterar el servicio de inventario.

Un fallo de WhatsApp se registra, pero no cambia una respuesta exitosa de
despacho ni vuelve a ejecutar el movimiento.

## 10. Responsabilidad final del controlador

Después de la migración, `trasladosinvcontrolador` únicamente debe:

1. Comprobar autenticación y permiso general del módulo.
2. Validar el método HTTP.
3. Leer la petición.
4. Convertir el formulario actual a la entrada normalizada.
5. Entregar sucursal y usuario de sesión al servicio.
6. Convertir el resultado a JSON o preparar los datos de la vista.
7. Ejecutar la notificación posterior al `commit` cuando corresponda.

No debe:

- Construir SQL.
- Determinar la dirección física de la mercancía.
- Validar transiciones de estado.
- Actualizar existencias.
- Crear movimientos.
- Iniciar o confirmar transacciones.

## 11. Mapeo de métodos actuales

| Método actual | Destino |
|---|---|
| `solicitudesrecibidas` | `TrasladosConsultaService::listarComoDestino()` |
| `trasladarinventario` | `TrasladosConsultaService::listarComoOrigen()` |
| `editartrasladoinv` | `TrasladosConsultaService::obtenerParaEditar()` |
| `idOrdenTrasladoSolicitudInv` | `TrasladosConsultaService::obtenerDetalle()` |
| `apisolicitarinventario` | `TrasladosOrdenService::crearSolicitud()` |
| `apinuevotrasladoinv` | `TrasladosOrdenService::crearSalida()` |
| `editarOrdenTransferencia` | `TrasladosOrdenService::editar()` |
| `anularnuevotrasladoinv` | `cancelar()` o `rechazar()`, según la sucursal actual |
| `confirmarnuevotrasladoinv` | `TrasladosInventarioService::despachar()` |
| `confirmaringresoinv` | `TrasladosInventarioService::recibir()` |

Las páginas que solo preparan catálogos para formularios pueden continuar en el
controlador hasta migrar las consultas, siempre que no contengan reglas de
traslado.

## 12. Orden de implementación

1. Crear y verificar `TrasladoPolicy`.
2. Crear `TrasladosConsultaService` y migrar las cuatro lecturas.
3. Crear `TrasladosOrdenService` y migrar creación/edición.
4. Sustituir el borrado actual por cancelación/rechazo lógico.
5. Crear `TrasladosInventarioService` y migrar despacho.
6. Migrar recepción.
7. Mover la notificación para que ocurra después del `commit`.
8. Retirar del controlador los imports y bloques que hayan quedado sin uso.

Cada etapa conservará las rutas actuales y será revisada de forma manual antes
de comenzar la siguiente.
