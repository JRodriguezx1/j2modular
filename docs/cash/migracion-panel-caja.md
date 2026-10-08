# Primer corte de migración a Cash: panel de caja

**Ruta:** `GET /admin/caja`  
**Estado:** migrada y verificada el 8 de octubre de 2026.

## Contrato conservado

La ruta continúa en `/admin/caja` y renderiza `views/admin/caja/index.php` dentro del layout administrativo. Requiere sesión administrativa; un perfil mayor que 3 necesita el permiso `Habilitar modulo de caja`.

El controlador entrega a la vista las mismas claves y en el mismo orden que `cajacontrolador::index`: `conflocal`, `datacierrescajas`, `categoriasgastos`, `cajas`, `bancos`, `facturas`, `mediospago`, `titulo`, `sucursal`, `alertas`, `sucursales` y `user`. La preparación de los datos sigue a cargo de `CajaConsultasService::obtenerPanelCaja`.

## Cambios de enrutamiento

- Este primer corte añadió `GET /admin/caja` a `app/Modules/Cash/routes.php` hacia `CajaController::index`. La vista de cierre principal se migró en `migracion-cierre-principal.md`; los reportes Z y cierres históricos, en `migracion-reportes-cierres.md`.
- Cash está habilitado para `cliente`, `cliente1` y `cliente2` en `config/tenants.php`.
- Se retiró el registro antiguo de esa misma ruta en `public/index.php`. `app/Controllers/cajacontrolador.php` permanece íntegro para las demás rutas y como referencia de la migración.
- El nuevo controlador usa `App\Core\Routing\Router`, que es el tipo entregado por el despachador activo. El controlador antiguo exige `MVC\Router`; una llamada directa desde el despachador activo producía `TypeError` antes de este corte.

## Esquema de referencia

`contapos` es el esquema de referencia. Se conserva la lectura original de `factmediospago.cierrecajaid` en `facturas::facturasConMediosPago`. La base local de `cliente2` (`j2a2`) está desactualizada y se omite de las pruebas; se asume que su esquema vigente será igual al de `cliente`. No se modificaron las bases de datos.

## Verificación

```powershell
php tests/cash-index-baseline.php
```

El script consulta `contapos` y `j2a1` sin escribir datos; omite la base local `j2a2`. Compara los datos preparados por el controlador antiguo, invocado con su tipo de router declarado, frente a la nueva ruta despachada por `App\Core\Routing\Router`. Comprueba la vista, el contenido de todas las variables y el rechazo de un perfil 4 sin permiso.

Resultado: **2 tenants comprobados**. `cliente` mostró 4 cajas y 20 facturas; `cliente1`, 1 caja y 0 facturas. `cliente2` quedó omitido por la condición indicada arriba. También se renderizó el HTML completo de la ruta nueva en las dos bases comprobadas: título `Gestion de Caja` presente y **0 advertencias PHP**.

Los movimientos siguen registrados en `public/index.php`; los comandos HTTP de cierre se migraron después en `migracion-acciones-cierre.md`. Las acciones antiguas que declaran `MVC\Router` conservan la misma incompatibilidad con el despachador activo y deben priorizarse en esos cortes. Esta prueba se ejecutó en PHP CLI y no comprueba JavaScript ni navegación en un navegador autenticado.
