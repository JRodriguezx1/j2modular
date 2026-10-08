# Tercer corte de Cash: reportes Z y cierres históricos

**Rutas migradas el 8 de octubre de 2026:**

| Ruta GET | Acción en `Modules/Cash` | Servicio de lectura |
|---|---|---|
| `/admin/caja/zetadiario` | `CajaController::zetadiario` | `CajaReportesService::obtenerIndiceZ` |
| `/admin/caja/fechazetadiario?id={selector}` | `CajaController::fechazetadiario` | `CajaReportesService::obtenerDetalleZ` |
| `/admin/caja/ultimoscierres` | `CajaController::ultimoscierres` | `CajaConsultasService::listarCierresFinalizados` |
| `/admin/caja/detallecierrecaja?id={id}` | `CajaController::detallecierrecaja` | `CajaConsultasService::obtenerDetalleCierreFinalizado` |

Las cuatro rutas están registradas en `app/Modules/Cash/routes.php` y se
retiraron sus registros antiguos de `public/index.php` para evitar duplicados.
Las acciones de `app/Controllers/cajacontrolador.php` permanecen intactas como
referencia. Se conservan las vistas y el permiso `Habilitar modulo de caja`.
`fechazetadiario` acepta `-1` (cajas abiertas), `0` (consulta por rango) y un
ID positivo (cierre histórico de la sucursal). Los selectores inválidos no
renderizan. `detallecierrecaja` tampoco renderiza con un ID inválido o ajeno.

## Verificación

```powershell
php tests/cash-reports-baseline.php
php tests/cash-reports-render.php cliente zetadiario
php tests/cash-reports-render.php cliente fechazetadiario -1
php tests/cash-reports-render.php cliente fechazetadiario 0
php tests/cash-reports-render.php cliente fechazetadiario finalizado
php tests/cash-reports-render.php cliente ultimoscierres
php tests/cash-reports-render.php cliente detallecierrecaja finalizado
```

La comparación de controladores pasó con 11 casos en `cliente` (`contapos`) y
9 en `cliente1` (`j2a1`): misma vista, mismos datos y rechazo de un perfil 4
sin permiso. Incluyó selectores inválidos e IDs inexistentes. `cliente` tiene
144 cierres finalizados; `cliente1` no tiene ninguno. El render completo de las
vistas se verificó en ambos tenants para los casos disponibles, sin advertencias
PHP. Estas pruebas solo leen la base de datos. La base local de `cliente2`
se omitió por estar desactualizada; se asume el esquema de `contapos`.

La consulta por rango enviada desde la vista sigue en
`reportescontrolador::consultafechazetadiario`. Los comandos de cierre y arqueo
se migraron después en `migracion-acciones-cierre.md`. Los movimientos
continúan en las rutas antiguas.
