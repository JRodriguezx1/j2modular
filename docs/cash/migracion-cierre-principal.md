# Segundo corte de Cash: vista de cierre principal

**Ruta:** `GET /admin/caja/cerrarcaja`  
**Estado:** migrada y verificada el 8 de octubre de 2026.

La ruta ahora apunta a `CajaController::cerrarcaja` en `app/Modules/Cash/routes.php`. Conserva la vista `admin/caja/cerrarcaja`, la autorización administrativa y el permiso `Habilitar modulo de caja` para perfiles mayores que 3. La preparación del resumen sigue en `CajaConsultasService::obtenerCierrePrincipal`. `cajacontrolador::cerrarcaja` permanece en el código antiguo, pero su registro de ruta se retiró de `public/index.php` para evitar duplicados.

La vista recibe las mismas variables que antes: cajas, configuración, medios de pago, cierre abierto, facturas, discriminaciones, gastos, diferencias, ventas por usuario y datos del layout. Cuando no hay cierre abierto, `resumenVacio()` ahora incluye `costo_total=0`; esto evita una advertencia PHP al calcular el total en la vista.

## Verificación

```powershell
php tests/cash-close-baseline.php
```

La prueba compara la acción antigua con la nueva ruta y verifica las variables entregadas a la vista y la denegación a un perfil 4 sin permiso. **Pasó en `cliente` (`contapos`) y `cliente1` (`j2a1`)**. `cliente` tiene cuatro cajas y un cierre principal abierto; `cliente1` tiene una caja y ningún cierre abierto. La base local de `cliente2` se omitió por estar desactualizada; se asume el esquema de `contapos`.

También se renderizó el HTML completo desde el enrutador activo en ambas bases: se mostró el título «Cierre de caja» y no hubo advertencias PHP. La prueba es de lectura y no confirma un cierre, arqueo ni declaración de dinero. Esos comandos HTTP se migraron después en `migracion-acciones-cierre.md`.
