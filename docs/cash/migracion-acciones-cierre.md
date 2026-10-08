# Cuarto corte de Cash: acciones HTTP del cierre

**Rutas POST migradas el 8 de octubre de 2026:**

| Ruta | Acción en `Modules/Cash` | Servicio |
|---|---|---|
| `/admin/api/declaracionDinero` | `CierreController::declaracionDinero` | `CajaCierreService::registrarDeclaracion` |
| `/admin/api/arqueocaja` | `CierreController::arqueocaja` | `CajaCierreService::registrarArqueo` |
| `/admin/api/cierrecajaconfirmado` | `CierreController::cierrecajaconfirmado` | `CajaCierreService::confirmarCierre` |
| `/admin/api/datoscajaseleccionada` | `CierreController::datoscajaseleccionada` | `CajaConsultasService::obtenerCajaSeleccionada` |

`app/Modules/Cash/routes.php` registra estas rutas; se retiraron sus registros
duplicados de `public/index.php`. Los cuatro métodos originales permanecen en
`app/Controllers/cajacontrolador.php`. El nuevo controlador conserva la entrada
`$_POST`, las respuestas JSON y la autenticación de cada acción antigua:
`isadmin()` para declaración, arqueo y selección de caja; `isauth()` para
confirmación del cierre. No se añadió una regla de permiso en este traslado.

## Verificación

```powershell
php tests/cash-close-actions-baseline.php
```

La prueba pasó con 7 casos en `cliente` (`contapos`) y 6 en `cliente1`
(`j2a1`). Compara la salida JSON de las acciones originales con la de las
rutas nuevas, incluidos errores de validación, caja inexistente y selección
de una caja abierta cuando está disponible. Los casos de escritura usan datos
inválidos para no modificar la base. La base local de `cliente2` se omitió por
estar desactualizada; se asume el esquema de `contapos`.

La prueba no ejercita una declaración, un arqueo o un cierre exitosos ni la
notificación de WhatsApp. Esas operaciones siguen delegadas al mismo
`CajaCierreService` que usaba el controlador antiguo. Antes de considerar
validado el flujo completo, hace falta una prueba de éxito en un entorno
aislado que permita comprobar los cambios persistidos y la notificación.
