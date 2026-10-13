# Revisión de las acciones restantes de Cash

**Fecha:** 11 de octubre de 2026.

Las 29 acciones públicas de `app/Controllers/cajacontrolador.php` tienen ahora
registro en `app/Modules/Cash/routes.php`. Los controladores nuevos están
separados por pantalla principal, cierre, movimientos y documentos/órdenes.
El controlador antiguo permanece intacto. Las rutas antiguas comentadas en
`public/index.php` no se despachan.

## Correcciones de la revisión

- La plantilla usada por `DocumentsCajaController::sendOrdenEmailToCustemer`
  debe resolverse desde la raíz del proyecto. La ruta copiada del controlador
  antiguo apuntaba a `app/Modules/views`, que no existe.
- El directorio predeterminado de comprobantes en
  `IngresosGastosController::guardarComprobanteGasto` también debe resolverse
  desde la raíz cuando `DOCUMENT_ROOT` no está definido.

Ambas rutas se corrigieron con `dirname(__DIR__, 4)`. Se comprobó que apuntan
a la plantilla existente y a `public/`.

## Pruebas

```powershell
php tests/cash-index-baseline.php
php tests/cash-close-baseline.php
php tests/cash-reports-baseline.php
php tests/cash-close-actions-baseline.php
php tests/cash-remaining-baseline.php
```

Las cuatro pruebas anteriores siguen pasando. La nueva prueba confirma que
ninguna acción pública quedó sin ruta, y compara vista, datos, salida y código
HTTP de 24 casos en `cliente` (`contapos`) y 18 en `cliente1` (`j2a1`). Incluye
factura y cierre reales cuando existen, junto con entradas inválidas para
las acciones de escritura. No modifica datos ni envía correos. La base local
de `cliente2` se omitió por estar desactualizada; se asume el esquema de
`contapos`.

## Límites y trabajo posterior

La comparación no ejercita escrituras exitosas ni envío de correo o WhatsApp.
La navegación real del navegador y las llamadas JavaScript tampoco se probaron
en esta revisión.

Se heredaron tres decisiones del controlador antiguo que requieren una revisión
funcional separada:

1. `cierrecajaconfirmado` exige solo `isauth()`, sin permiso específico de caja.
2. `sendOrdenEmailToCustemer` busca la factura por ID sin comprobar su sucursal
   y acepta el destinatario enviado en `POST`.
3. `despacharOrden` modifica datos mediante una ruta `GET`.

Estas reglas no se cambiaron durante el traslado para conservar el contrato
actual; deben corregirse junto con sus clientes TypeScript y pruebas propias.
