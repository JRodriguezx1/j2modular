# Revisión de la migración del controlador de almacén

**Fecha:** 11 de octubre de 2026.

`app/Modules/Inventory/routes.php` registra 24 rutas GET y 31 POST. Los 54
métodos públicos de `almacencontrolador` tienen método y ruta equivalentes
en `InventoryController` o `ApiInventoryController`. El código de sus acciones
se trasladó sin cambios de lógica, salvo la autorización añadida a las dos
exportaciones Excel. El controlador antiguo permanece en el proyecto. El
archivo anterior `app/Modules/Inventory/productosInvController.php` no tiene
referencias activas y se retiró durante el traslado.

## Verificación

```powershell
php tests/inventory-migration-audit.php
php tests/inventory-read-baseline.php
php tests/inventory-read-baseline.php cliente1
php tests/inventory-render.php index
php tests/inventory-render.php productos
php tests/inventory-render.php subproductos
php tests/inventory-render.php compras
php tests/inventory-render.php unidadesmedida
```

La auditoría estática encontró cobertura completa y ninguna diferencia de
cuerpo imprevista. La comparación de 22 consultas en `cliente` (`contapos`)
produjo las mismas vistas, variables, respuestas y códigos HTTP que el
controlador anterior. En `cliente1`, la misma comparación directa de 22 casos
también pasó al cargar las rutas de Inventory de forma aislada; esto no habilita
el módulo en el enrutamiento normal de ese tenant. Las cinco pantallas
principales se renderizaron con el router activo sin advertencias PHP. Las
pruebas fueron de lectura; no se ejecutaron compras, ajustes de existencias,
importaciones ni exportaciones.
La base local de `cliente2` se omitió por estar desactualizada.

## Alcance por tenant

`Inventory` está habilitado en `config/tenants.php` solo para `cliente`.
Como las rutas antiguas de almacén están comentadas globalmente en
`public/index.php`, `cliente1` y `cliente2` no registran `/admin/almacen`
ni sus API de inventario. Se confirmó que `cliente1` tiene las tablas básicas
de inventario. El alcance confirmado para esta etapa es únicamente `cliente`;
por ello no se cambió la configuración de tenants en esta revisión.

## Puntos pendientes

- Los modelos siguen en `app/Models/inventario` y los servicios en
  `app/services/inventario`; esta fase trasladó controladores y rutas.
- Las rutas GET `desasociarsubproducto` y `eliminarConversionUnidad` modifican
  datos. La migración conservó el contrato antiguo; corregirlo exige cambiar
  también las llamadas del cliente.
- Algunas API heredadas, como `allproveedores`, no llaman a `isadmin()`.
  Conviene revisar autenticación y permisos de todas las API de lectura.
- Los caminos de escritura exitosa, carga de archivos y exportación Excel
  requieren pruebas en un entorno aislado antes de dar por cerrado el flujo.

Las dos exportaciones Excel ahora exigen sesión administrativa y permiso de
inventario. Al migrar al router activo, quedaban invocables sin esas
comprobaciones; la auditoría comprueba la denegación a un perfil 4 sin permiso.
