<?php 

namespace App\services;

use App\Models\clientes\clientes;
use App\Models\configuraciones\consecutivos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\ventas\venta_insumos;
use App\Models\ventas\ventas;
use App\services\inventario\StockMinimoService;
use stdClass;
use Throwable;


//**SERVICIO DE VENTAS

class ventasService {

    /**
     * Construye el movimiento de inventario a partir de cada linea configurada
     * del carrito. Los productos compuestos inmediatos NO se agrupan antes de
     * leer sus variaciones: cada configuracion aporta solo sus insumos activos.
     */
    public static function prepararInventarioXVenta(array $carrito, int $sucursalId):array{
        $productosSimples = [];
        $idsProductos = [];
        $lineasCompuestas = [];
        $idsCompuestos = [];

        foreach($carrito as $linea){
            if(!is_object($linea))continue;

            $idProducto = (int)($linea->idproducto ?? 0);
            $cantidad = (float)($linea->stock ?? $linea->cantidad ?? 0);
            if($idProducto <= 0 || $cantidad <= 0)continue; // excluye "Otros"

            $tipoProducto = (int)($linea->tipoproducto ?? 0);
            $tipoProduccion = (int)($linea->tipoproduccion ?? 0);

            if($tipoProducto === 0 || ($tipoProducto === 1 && $tipoProduccion === 1)){
                $linea->insumos_resueltos = [];
                if(!isset($productosSimples[$idProducto])){
                    $producto = clone $linea;
                    $producto->id = $idProducto;
                    $producto->stock = 0.0;
                    $producto->stockaux = 0.0;
                    $productosSimples[$idProducto] = $producto;
                    $idsProductos[] = $idProducto;
                }

                $promedio = (float)($linea->promediostock ?? 0);
                $productosSimples[$idProducto]->stock += $cantidad;
                $productosSimples[$idProducto]->stockaux += $promedio > 0 ? $cantidad / $promedio : 0;
                continue;
            }

            if($tipoProducto === 1 && $tipoProduccion === 0){
                $lineasCompuestas[] = $linea;
                $idsCompuestos[] = $idProducto;
            }
        }

        $recetas = productos_sub::recetasParaVenta($idsCompuestos, $sucursalId);
        $recetasPorProducto = [];
        foreach($recetas as $receta)
            $recetasPorProducto[(int)$receta->id_producto][] = $receta;

        $insumos = [];
        $idsInsumos = [];

        foreach($lineasCompuestas as $linea){
            $idProducto = (int)$linea->idproducto;
            $cantidadProducto = (float)($linea->stock ?? $linea->cantidad ?? 0);
            $configuracionPresente = property_exists($linea, 'insumos')
                && $linea->insumos !== null
                && $linea->insumos !== ''
                && empty($linea->insumos_legacy);
            $configuracion = $linea->insumos ?? [];
            $configuracionPorId = [];
            $configuracionResuelta = [];

            foreach($configuracion as $insumoConfigurado){
                if(!is_object($insumoConfigurado))continue;
                $idInsumo = (int)($insumoConfigurado->id_subproducto ?? 0);
                if($idInsumo > 0)$configuracionPorId[$idInsumo] = $insumoConfigurado;
            }

            foreach($recetasPorProducto[$idProducto] ?? [] as $receta){
                $idInsumo = (int)$receta->id_subproducto;
                $esFijo = $receta->grupos_insumos === null || $receta->grupos_insumos === '' || (int)$receta->grupos_insumos === 0;

                // Para registros anteriores a venta_insumos se conserva la
                // seleccion por defecto. En carritos nuevos manda la configuracion.
                $estaSeleccionado = $esFijo || ($configuracionPresente ? isset($configuracionPorId[$idInsumo]) : (int)$receta->seleccionado === 1);

                if(!$estaSeleccionado)continue;

                $cantidadReceta = (float)$receta->cantidadsubproducto;
                $configurado = $configuracionPorId[$idInsumo] ?? null;

                // El cliente solo puede aumentar una cantidad cuando la receta
                // lo permite; nunca reducirla por debajo del valor configurado.
                if((int)$receta->permite_aumentar === 1 && $configurado){
                    $cantidadSolicitada = (float)($configurado->cantidadsubproducto ?? 0);
                    if($cantidadSolicitada > $cantidadReceta)
                        $cantidadReceta = $cantidadSolicitada;
                }

                $rendimiento = (float)($receta->rendimiento_producto ?? 1);
                if($rendimiento <= 0)$rendimiento = 1;
                $cantidadDescontar = $cantidadReceta * ($cantidadProducto / $rendimiento);
                if($cantidadDescontar <= 0)continue;

                $promedio = (float)($receta->promediostock ?? 0);
                $stockauxDescontar = $promedio > 0 ? $cantidadDescontar / $promedio : 0;

                // Se conservan tanto los nombres del detalle relacional como
                // los alias que consume el carrito TypeScript.
                $configuracionResuelta[] = (object)[
                    'subproducto_id' => $idInsumo,
                    'id_subproducto' => $idInsumo,
                    'grupo_insumo_id' => $esFijo ? null : (int)$receta->grupos_insumos,
                    'nombre_insumo' => (string)($receta->nombre_insumo ?? ''),
                    'nombre' => (string)($receta->nombre_insumo ?? ''),
                    'cantidad_configurada' => round($cantidadReceta, 4),
                    'cantidadsubproducto' => round($cantidadReceta, 4),
                    'cantidad_consumida' => round($cantidadDescontar, 4),
                    'stockaux_consumido' => round($stockauxDescontar, 4),
                    'es_fijo' => $esFijo ? 1 : 0,
                    'tipo_variacion' => $esFijo ? null : (int)($receta->tipo_variacion ?? 0),
                    'seleccionado' => 1,
                ];

                if(!isset($insumos[$idInsumo])){
                    $movimiento = new stdClass();
                    $movimiento->id = $idInsumo;
                    $movimiento->id_subproducto = $idInsumo;
                    $movimiento->stock = 0.0;
                    $movimiento->stockaux = 0.0;
                    $insumos[$idInsumo] = $movimiento;
                    $idsInsumos[] = $idInsumo;
                }

                $insumos[$idInsumo]->stock += $cantidadDescontar;
                $insumos[$idInsumo]->stockaux += $stockauxDescontar;
            }

            // Se persiste la configuracion ya validada contra la receta del
            // servidor, no el contenido crudo recibido desde el navegador.
            $linea->insumos_resueltos = $configuracionResuelta;
            $linea->insumos_legacy = false;
        }

        return [
            'productosSimples' => $productosSimples,
            'soloIdproductos' => array_values(array_unique($idsProductos)),
            'insumos' => $insumos,
            'soloIdInsumos' => array_values(array_unique($idsInsumos)),
        ];
    }

    public static function validarDisponibilidadInventario(array $inventario, int $sucursalId):array{
        $errores = [];
        $productos = $inventario['productosSimples'] ?? [];
        $insumos = $inventario['insumos'] ?? [];

        $stockProductos = stockproductossucursal::IN_Where('productoid', $inventario['soloIdproductos']??[], ['sucursalid', $sucursalId] );
        $stockProductos = array_column($stockProductos, null, 'productoid');
        foreach($productos as $id => $producto)
            if(!isset($stockProductos[$id]) || (float)$stockProductos[$id]->stock < (float)$producto->stock)
                $errores[] = "Stock insuficiente para el producto #$id";

        $stockInsumos = stockinsumossucursal::IN_Where('subproductoid', $inventario['soloIdInsumos'] ?? [], ['sucursalid', $sucursalId]);
        $stockInsumos = array_column($stockInsumos, null, 'subproductoid');
        foreach($insumos as $id => $insumo)
            if(!isset($stockInsumos[$id]) || (float)$stockInsumos[$id]->stock < (float)$insumo->stock)
                $errores[] = "Stock insuficiente para el insumo #$id";

        return $errores;
    }

    public static function descontarInventarioXVenta(array $inventario, int $sucursalId, string $tipo = 'venta', string $referencia = 'descuento de unidades por venta', bool $manejarTransaccion = true, ?array &$itemsStockMinimo = null):bool {
        return self::aplicarInventario($inventario, $sucursalId, false, $tipo, $referencia, $manejarTransaccion, $itemsStockMinimo);
    }

    public static function devolverInventarioXVenta(array $inventario, int $sucursalId, string $tipo = 'devolucion', string $referencia = 'retorno de unidades por anulacion de venta', bool $manejarTransaccion = true):bool {
        return self::aplicarInventario($inventario, $sucursalId, true, $tipo, $referencia, $manejarTransaccion);
    }

    /**
     * Inserta todas las lineas con un solo INSERT. MySQL retorna el primer id
     * generado y, al ser un INSERT ... VALUES simple, los siguientes se
     * calculan respetando auto_increment_increment. Luego se hace un segundo
     * INSERT masivo con todos los insumos relacionados por posicion.
     */
    public static function guardarLineasVenta(array $lineas, bool $manejarTransaccion = true):array{
        if ($lineas === [])return [true, []];
        $db = ventas::getDB();
        if($manejarTransaccion)$db->begin_transaction();

        try {
            $resultadoPaso = $db->query('SELECT @@SESSION.auto_increment_increment AS incremento');
            $incremento = (int)($resultadoPaso->fetch_object()->incremento ?? 1);
            if($incremento <= 0)$incremento = 1;

            $modeloVenta = new ventas();
            [$creado, $primerId] = $modeloVenta->crear_varios_reg_arrayobj($lineas);
            $filasInsertadas = (int)$db->affected_rows;
            if(!$creado || !$primerId || $filasInsertadas !== count($lineas))
                throw new \RuntimeException('No fue posible guardar todas las lineas de venta.');

            $idsVenta = [];
            for($i = 0; $i < count($lineas); $i++)
                $idsVenta[] = (int)$primerId + ($i * $incremento);

            self::guardarInsumosLineas($lineas, $idsVenta);
            if($manejarTransaccion)$db->commit();
            return [true, $idsVenta];
        }catch(Throwable $e){
            if($manejarTransaccion)$db->rollback();
            throw $e;
        }
    }

    /**
     * Actualiza, inserta y elimina lineas de una cotizacion atomicamente.
     * $manejarTransaccion=false permite participar en una transaccion superior,
     * como la coordinada por facturacionService.
     */
    public static function actualizarLineasCotizacion(array $lineasActualizar, array $lineasInsertar, int $idFactura, bool $manejarTransaccion = true):bool{
        $db = ventas::getDB();
        if($manejarTransaccion)$db->begin_transaction();
        try {
            $idsExistentes = array_map('intval', ventas::multicampos('idfactura', $idFactura, 'id'));
            $idsActualizar = [];
            foreach($lineasActualizar as $linea){
                $idVenta = (int)($linea->id ?? 0);
                if(!in_array($idVenta, $idsExistentes, true))
                    throw new \RuntimeException('Una linea no pertenece a la cotizacion indicada.');
                
                $idsActualizar[] = $idVenta;
            }
            $idsEliminar = array_values(array_diff($idsExistentes, $idsActualizar));

            if(!empty($lineasActualizar)){
                $actualizado = ventas::updatemultiregobj($lineasActualizar, ['valorunidad', 'cantidad', 'subtotal', 'base', 'impuesto', 'valorimp', 'descuento', 'total']);
                if(!$actualizado)throw new \RuntimeException('No fue posible actualizar la cotizacion.');
                self::reemplazarInsumosLineas($lineasActualizar);
            }
            
            if(!empty($lineasInsertar))self::guardarLineasVenta($lineasInsertar, false);
            
            if(!empty($idsEliminar) && !ventas::eliminar_idregistros('id', $idsEliminar))
                throw new \RuntimeException('No fue posible eliminar lineas retiradas de la cotizacion.');
            
            if($manejarTransaccion)$db->commit();
            return true;
        }catch(Throwable $e){
            if($manejarTransaccion)$db->rollback();
            throw $e;
        }
    }

    /** Reemplaza el detalle de lineas existentes al editar una cotizacion. */
    private static function reemplazarInsumosLineas(array $lineas):void{
        $lineasValidas = [];
        $idsVenta = [];
        foreach($lineas as $linea){
            $lineasValidas[] = $linea;
            $idsVenta[] = $linea->id;
        }

        if(empty($idsVenta))return;
        if(!venta_insumos::eliminar_idregistros('venta_id', $idsVenta))
            throw new \RuntimeException('No fue posible actualizar los insumos de la cotizacion.');
        
        self::guardarInsumosLineas($lineasValidas, $idsVenta);
    }

    /** Adjunta a cada linea el detalle relacional en el formato del carrito. */
    public static function adjuntarInsumos(array $productos):array{
        $idsVenta = [];
        foreach($productos as $producto)
            if(is_object($producto) && (int)($producto->id ?? 0) > 0)
                $idsVenta[] = (int)$producto->id;

        $detallesPorVenta = [];
        foreach(venta_insumos::detallesPorVentas($idsVenta) as $detalle){
            $grupo = null;
            if((int)($detalle->grupo_insumo_id ?? 0) > 0){
                $grupo = (object)[
                    'id' => (string)$detalle->grupo_insumo_id,
                    'nombre' => (string)($detalle->nombre_grupo ?? ''),
                    'minimo' => (string)($detalle->minimo_grupo ?? 0),
                    'maximo' => (string)($detalle->maximo_grupo ?? 0),
                    'tipo' => (string)($detalle->tipo_variacion ?? 0),
                ];
            }
            $detallesPorVenta[(int)$detalle->venta_id][] = (object)[
                'subproducto_id' => (int)($detalle->subproducto_id ?? 0),
                'id_subproducto' => (string)($detalle->subproducto_id ?? 0),
                'grupo_insumo_id' => (int)($detalle->grupo_insumo_id ?? 0) ?: null,
                'nombre_insumo' => (string)($detalle->nombre_insumo ?? ''),
                'cantidadsubproducto' => (string)$detalle->cantidad_configurada,
                'cantidad_configurada' => (float)$detalle->cantidad_configurada,
                'nombre' => (string)($detalle->nombre_insumo ?? ''),
                'seleccionado' => '1',
                'grupos_insumos' => $grupo,
                'cantidad_consumida' => (float)$detalle->cantidad_consumida,
                'stockaux_consumido' => (float)$detalle->stockaux_consumido,
                'es_fijo' => (int)$detalle->es_fijo,
                'tipo_variacion' => $detalle->tipo_variacion,
            ];
        }

        foreach($productos as $producto){
            if(!is_object($producto))continue;
            $producto->insumos = $detallesPorVenta[(int)($producto->id ?? 0)] ?? [];
            $producto->insumos_resueltos = $producto->insumos;
            $producto->insumos_legacy = (int)($producto->tipoproducto ?? 0) === 1
                && (int)($producto->tipoproduccion ?? 0) === 0
                && empty($producto->insumos);
        }
        return $productos;
    }

    /**
     * Reconstruye inventario desde el consumo historico. Sirve para despacho y
     * anulacion aun cuando la receta del producto haya cambiado despues.
     */
    public static function prepararInventarioPersistido(array $productos, int $sucursalId):array{
        return self::construirInventarioPersistido(self::adjuntarInsumos($productos), $sucursalId);
    }

    /**
     * Construye el arreglo consumido por aplicarInventario() a partir de lineas
     * que ya tienen adjuntado su detalle historico de insumos.
     */
    private static function construirInventarioPersistido(array $productos, int $sucursalId):array
    {
        $inventario = [
            'productosSimples' => [],
            'soloIdproductos' => [],
            'insumos' => [],
            'soloIdInsumos' => [],
        ];
        $compuestosLegacy = [];

        foreach($productos as $linea){
            if(!is_object($linea))continue;
            $idProducto = (int)($linea->idproducto ?? 0);
            $cantidad = (float)($linea->cantidad ?? $linea->stock ?? 0);
            if($idProducto <= 0 || $cantidad <= 0)continue;
            $tipo = (int)($linea->tipoproducto ?? 0);
            $produccion = (int)($linea->tipoproduccion ?? 0);

            if($tipo === 0 || ($tipo === 1 && $produccion === 1)){
                if(!isset($inventario['productosSimples'][$idProducto])){
                    $movimiento = clone $linea;
                    $movimiento->id = $idProducto;
                    $movimiento->stock = 0.0;
                    $movimiento->stockaux = 0.0;
                    $inventario['productosSimples'][$idProducto] = $movimiento;
                    $inventario['soloIdproductos'][] = $idProducto;
                }
                $promedio = (float)($linea->promediostock ?? 0);
                $inventario['productosSimples'][$idProducto]->stock += $cantidad;
                $inventario['productosSimples'][$idProducto]->stockaux += $promedio > 0 ? $cantidad / $promedio : 0;
                continue;
            }

            if(empty($linea->insumos)){
                $linea->stock = $cantidad;
                $linea->insumos_legacy = true;
                $compuestosLegacy[] = $linea;
                continue;
            }

            foreach($linea->insumos as $detalle){
                $idInsumo = (int)($detalle->id_subproducto ?? 0);
                if($idInsumo <= 0)continue;
                if(!isset($inventario['insumos'][$idInsumo])){
                    $movimiento = new stdClass();
                    $movimiento->id = $idInsumo;
                    $movimiento->id_subproducto = $idInsumo;
                    $movimiento->stock = 0.0;
                    $movimiento->stockaux = 0.0;
                    $inventario['insumos'][$idInsumo] = $movimiento;
                    $inventario['soloIdInsumos'][] = $idInsumo;
                }
                $inventario['insumos'][$idInsumo]->stock += (float)$detalle->cantidad_consumida;
                $inventario['insumos'][$idInsumo]->stockaux += (float)$detalle->stockaux_consumido;
            }
        }

        if(!empty($compuestosLegacy)){
            $legacy = self::prepararInventarioXVenta($compuestosLegacy, $sucursalId);
            foreach($legacy['insumos'] as $idInsumo => $movimiento){
                if(!isset($inventario['insumos'][$idInsumo])){
                    $inventario['insumos'][$idInsumo] = $movimiento;
                }else{
                    $inventario['insumos'][$idInsumo]->stock += $movimiento->stock;
                    $inventario['insumos'][$idInsumo]->stockaux += $movimiento->stockaux;
                }
            }
            $inventario['soloIdInsumos'] = array_values(array_unique(array_merge(
                $inventario['soloIdInsumos'],
                $legacy['soloIdInsumos']
            )));
        }

        $inventario['soloIdproductos'] = array_values(array_unique($inventario['soloIdproductos']));
        return $inventario;
    }


    /**
     * Prepara una devolucion elegida por el usuario usando las lineas e insumos
     * realmente persistidos en la factura. La cantidad solicitada puede superar
     * la vendida por regla de negocio; en ese caso el factor de devolucion sera
     * mayor que uno y los insumos se incrementaran en la misma proporcion.
     */
    public static function prepararDevolucionParcialPersistida(int $idFactura, array $seleccion, int $sucursalId):array{
        if($idFactura <= 0)throw new \InvalidArgumentException('La factura indicada no es valida.');
        if($sucursalId <= 0)throw new \InvalidArgumentException('La sucursal indicada no es valida.');

        $lineasFactura = ventas::idregistros('idfactura', $idFactura);
        $lineasPorId = [];
        foreach($lineasFactura as $linea){
            if(!is_object($linea))continue;
            $idVenta = (int)($linea->id ?? 0);
            if($idVenta > 0)$lineasPorId[$idVenta] = $linea;
        }

        $lineasSeleccionadas = [];
        $factoresPorVenta = [];
        $idsProcesados = [];

        foreach($seleccion as $item){
            if(!is_object($item))throw new \InvalidArgumentException('La seleccion de productos no es valida.');

            // idventa identifica una linea concreta; idproducto no es suficiente
            // porque una factura puede contener varias lineas del mismo producto.
            $idVenta = (int)($item->idventa ?? $item->id ?? 0);
            if($idVenta <= 0)
                throw new \InvalidArgumentException('Una linea seleccionada no tiene un identificador valido.');
            if(isset($idsProcesados[$idVenta]))
                throw new \InvalidArgumentException("La linea de venta #{$idVenta} esta repetida en la devolucion.");
            $idsProcesados[$idVenta] = true;

            if(!isset($lineasPorId[$idVenta]))
                throw new \InvalidArgumentException("La linea de venta #{$idVenta} no pertenece a la factura indicada.");

            $cantidadRecibida = $item->cantidad ?? null;
            if(!is_numeric($cantidadRecibida))
                throw new \InvalidArgumentException("La cantidad de la linea #{$idVenta} no es valida.");

            $cantidadDevolver = (float)$cantidadRecibida;
            if(!is_finite($cantidadDevolver) || $cantidadDevolver < 0)
                throw new \InvalidArgumentException("La cantidad de la linea #{$idVenta} no es valida.");

            // Cero significa que esta linea no participa en la devolucion.
            if($cantidadDevolver === 0.0)continue;

            $lineaPersistida = $lineasPorId[$idVenta];
            $cantidadVendida = (float)($lineaPersistida->cantidad ?? 0);
            if(!is_finite($cantidadVendida) || $cantidadVendida <= 0)
                throw new \RuntimeException("La linea de venta #{$idVenta} no tiene una cantidad vendida valida.");

            $lineaDevolver = clone $lineaPersistida;
            $lineaDevolver->cantidad = $cantidadDevolver;
            $lineaDevolver->stock = $cantidadDevolver;
            $lineasSeleccionadas[] = $lineaDevolver;
            $factoresPorVenta[$idVenta] = $cantidadDevolver / $cantidadVendida;
        }

        if(empty($lineasSeleccionadas))return self::construirInventarioPersistido([], $sucursalId);

        // Se parte del detalle historico, no de la receta actual. Para registros
        // antiguos sin venta_insumos, construirInventarioPersistido conserva el
        // comportamiento legacy y calcula la receta con la cantidad seleccionada.
        $lineasSeleccionadas = self::adjuntarInsumos($lineasSeleccionadas);
        foreach($lineasSeleccionadas as $linea){
            if(!is_object($linea) || empty($linea->insumos))continue;

            $factor = $factoresPorVenta[(int)($linea->id ?? 0)] ?? null;
            if($factor === null)continue;

            $insumosProrrateados = [];
            foreach($linea->insumos as $detalle){
                if(!is_object($detalle))continue;
                $insumo = clone $detalle;
                $insumo->cantidad_consumida = round((float)($detalle->cantidad_consumida ?? 0) * $factor, 4);
                $insumo->stockaux_consumido = round((float)($detalle->stockaux_consumido ?? 0) * $factor, 4);
                $insumosProrrateados[] = $insumo;
            }
            $linea->insumos = $insumosProrrateados;
            $linea->insumos_resueltos = $insumosProrrateados;
        }

        return self::construirInventarioPersistido($lineasSeleccionadas, $sucursalId);
    }

    /** Construye y guarda en un solo INSERT los insumos de varias lineas. */
    private static function guardarInsumosLineas(array $lineas, array $idsVenta):void{
        if(count($lineas) !== count($idsVenta))
            throw new \RuntimeException('No coinciden las lineas de venta con sus identificadores.');

        $registrosInsumos = [];
        foreach($lineas as $indice => $linea){
            foreach(($linea->insumos_resueltos ?? []) as $detalle){
                if(!is_object($detalle))continue;
                $idInsumo = (int)($detalle->subproducto_id ?? $detalle->id_subproducto ?? 0);
                if($idInsumo <= 0)continue;

                $grupoId = (int)($detalle->grupo_insumo_id ?? 0);
                $tipoVariacion = $detalle->tipo_variacion ?? null;
                $registrosInsumos[] = (object)[
                    'venta_id' => (int)$idsVenta[$indice],
                    'subproducto_id' => $idInsumo,
                    // crear_varios_reg_arrayobj convierte el texto NULL en SQL NULL.
                    'grupo_insumo_id' => $grupoId > 0 ? $grupoId : 'NULL',
                    'nombre_insumo' => (string)($detalle->nombre_insumo ?? $detalle->nombre ?? ''),
                    'cantidad_configurada' => (float)($detalle->cantidad_configurada ?? $detalle->cantidadsubproducto ?? 0),
                    'cantidad_consumida' => (float)($detalle->cantidad_consumida ?? 0),
                    'stockaux_consumido' => (float)($detalle->stockaux_consumido ?? 0),
                    'es_fijo' => (int)($detalle->es_fijo ?? 0),
                    'tipo_variacion' => $tipoVariacion !== null?(int)$tipoVariacion:'NULL',
                ];
            }
        }

        if(empty($registrosInsumos))return;
        $modeloInsumo = new venta_insumos();
        [$creado] = $modeloInsumo->crear_varios_reg_arrayobj($registrosInsumos);
        $filasInsertadas = (int)venta_insumos::getDB()->affected_rows;
        if(!$creado || $filasInsertadas !== count($registrosInsumos)){
            throw new \RuntimeException('No fue posible guardar todos los insumos de la venta.');
        }
    }



    private static function aplicarInventario(array $inventario, int $sucursalId, bool $sumar, string $tipo, string $referencia, bool $manejarTransaccion, ?array &$itemsStockMinimo = null):bool {
        $stockProductosActual = [];
        $stockInsumosActual = [];
        $db = stockproductossucursal::getDB();
        if($manejarTransaccion)$db->begin_transaction();

        try {
            $productos = $inventario['productosSimples'] ?? [];
            $idsProductos = $inventario['soloIdproductos'] ?? [];
            if(!empty($productos)){
                $resultado = $sumar
                    ? stockproductossucursal::aumentarMultiplesColumnas($productos, ['stock', 'stockaux'], 'productoid', "sucursalid = $sucursalId")
                    : stockproductossucursal::reducirMultiplesColumnas($productos, ['stock', 'stockaux'], 'productoid', "sucursalid = $sucursalId");
                if(!$resultado)throw new \RuntimeException('No fue posible actualizar el stock de productos.');

                $query = "SELECT * FROM stockproductossucursal WHERE productoid IN(".implode(', ', $idsProductos).") AND sucursalid = $sucursalId;";
                $stockProductosActual = stockproductossucursal::camposJoinObj($query);
                $movimiento = $sumar
                    ? stockService::upStock_movimientoProductos($productos, $stockProductosActual, $tipo, $referencia)
                    : stockService::downStock_movimientoProductos($productos, $stockProductosActual, $tipo, $referencia);
                if(!$movimiento)throw new \RuntimeException('No fue posible registrar el movimiento de productos.');
            }

            $insumos = $inventario['insumos'] ?? [];
            $idsInsumos = $inventario['soloIdInsumos'] ?? [];
            if(!empty($insumos)){
                $resultado = $sumar
                    ? stockinsumossucursal::aumentarMultiplesColumnas($insumos, ['stock', 'stockaux'], 'subproductoid', "sucursalid = $sucursalId")
                    : stockinsumossucursal::reducirMultiplesColumnas($insumos, ['stock', 'stockaux'], 'subproductoid', "sucursalid = $sucursalId");
                if(!$resultado)throw new \RuntimeException('No fue posible actualizar el stock de insumos.');

                $query = "SELECT * FROM stockinsumossucursal WHERE subproductoid IN(".implode(', ', $idsInsumos).") AND sucursalid = $sucursalId;";
                $stockInsumosActual = stockinsumossucursal::camposJoinObj($query);
                $movimiento = $sumar
                    ? stockService::upStock_movimientoInsumos($insumos, $stockInsumosActual, $tipo, $referencia)
                    : stockService::downStock_movimientoInsumos($insumos, $stockInsumosActual, $tipo, $referencia);
                if(!$movimiento)throw new \RuntimeException('No fue posible registrar el movimiento de insumos.');
            }

            if(!$sumar && $itemsStockMinimo !== null)
                $itemsStockMinimo = (new StockMinimoService())->detectarCrucesDespuesDeDescuento($inventario, $stockProductosActual, $stockInsumosActual, $sucursalId);
            if($manejarTransaccion)$db->commit();
            return true;
        } catch(Throwable $e) {
            if($manejarTransaccion){
                $db->rollback();
                return false;
            }
            throw $e;
        }
    }

    //********** */


    /*public static function reducirIventarioXVenta(array $carrito):array{
        //$invSub = true;
        $invPro = true;
        //////////  SEPARAR LOS PRODUCTOS COMPUESTOS DE PRODUCTOS SIMPLES  ////////////
        $resultArray = array_reduce($carrito, function($acumulador, $objeto){
            $obj = clone $objeto;
            $obj->id = $objeto->idproducto;
            //unset($objeto->iditem);
            if($objeto->tipoproducto == 0 || ($objeto->tipoproducto == 1 && $objeto->tipoproduccion == 1)){  //producto simple o producto compuesto de tipo produccion construccion, solo se descuenta sus cantidades, y sus insumos cuando se hace produccion en almacen del producto compuesto
                if(!isset($acumulador['productosSimples'][$objeto->idproducto])){
                $acumulador['productosSimples'][$objeto->idproducto] = $obj;
                $acumulador['soloIdproductos'][] = $obj->id;
                }else{
                $acumulador['productosSimples'][$objeto->idproducto]->stock += $obj->stock;
                }
            }elseif($objeto->tipoproducto == 1 && $objeto->tipoproduccion == 0){  //producto compuesto e inmediato es decir por cada venta se descuenta sus insumos
                if(!isset($acumulador['productosCompuestos'][$objeto->idproducto])){
                $acumulador['productosCompuestos'][$objeto->idproducto] = $obj;
                }else{
                $acumulador['productosCompuestos'][$objeto->idproducto]->stock += $obj->stock;
                }
                $acumulador['productosCompuestos'][$objeto->idproducto]->porcion = round((float)$acumulador['productosCompuestos'][$objeto->idproducto]->stock/(float)$objeto->rendimientoestandar, 4);
            }
            return $acumulador;
        }, ['productosSimples'=>[], 'productosCompuestos'=>[]]);

        
        //////// Selecciona y trae la cantidad subproductos del producto compuesto a descontar del inventario
        $descontarSubproductos = productos_sub::cantidadSubproductosXventa($resultArray['productosCompuestos']);
        //////// sumar los subproductos repetidos
        $reduceSub = [];
        $soloIdInsumos =[];
        foreach($descontarSubproductos as $idx => $obj){
            if(!isset($reduceSub[$obj->id_subproducto])){
                $obj->id = $obj->id_subproducto;
                $obj->stockaux = $obj->promediostock>0?$obj->stock/$obj->promediostock:0;
                $reduceSub[$obj->id_subproducto] = $obj;
                $soloIdInsumos[] = $obj->id;
            }else{
            $reduceSub[$obj->id_subproducto]->stock += $obj->stock;
            $reduceSub[$obj->id_subproducto]->stockaux += $obj->promediostock>0?$obj->stock/$obj->promediostock:0;
            }
        }

        if(!empty($resultArray['productosSimples'])){
            //$invPro = stockproductossucursal::reduceinv1condicion($resultArray['productosSimples'], 'stock', 'productoid', "sucursalid = ".id_sucursal());
            $invPro = stockproductossucursal::reducirMultiplesColumnas($resultArray['productosSimples'], ['stock', 'stockaux'], 'productoid', "sucursalid = ".id_sucursal());
            //registrar descuento de movimiento de invnetario
            $query = "SELECT * FROM stockproductossucursal WHERE productoid IN(".join(', ', $resultArray['soloIdproductos']).") AND sucursalid = ".id_sucursal().";";
            $returnProductos = stockproductossucursal::camposJoinObj($query);
            stockService::downStock_movimientoProductos($resultArray['productosSimples'], $returnProductos, 'venta', 'descuento de unidades por venta');
        }
        //////// descontar del inventario la variable reduceSub que es el total de subproductos a descontar
        if($invPro && !empty($reduceSub)){
            //$invSub = subproductos::updatereduceinv($reduceSub, 'stock');
            //$invSub = stockinsumossucursal::reduceinv1condicion($reduceSub, 'stock', 'subproductoid', "sucursalid = ".id_sucursal());
            $invSub = stockinsumossucursal::reducirMultiplesColumnas($reduceSub, ['stock', 'stockaux'], 'subproductoid', "sucursalid = ".id_sucursal());
            //registrar descuento de movimiento de invnetario
            $query = "SELECT * FROM stockinsumossucursal WHERE subproductoid IN(".join(', ', $soloIdInsumos).") AND sucursalid = ".id_sucursal().";";
            $returnInsumos = stockinsumossucursal::camposJoinObj($query);
            stockService::downStock_movimientoInsumos($reduceSub, $returnInsumos, 'venta', 'descuento de unidades por venta');
        }

        return $resultArray;
    }
    

    public static function addIventarioXVenta(array $carrito):array{
        //$invSub = true;
        $invPro = true;
        //////////  SEPARAR LOS PRODUCTOS COMPUESTOS DE PRODUCTOS SIMPLES  ////////////
        $resultArray = array_reduce($carrito, function($acumulador, $objeto){
            $obj = clone $objeto;
            $obj->id = $objeto->idproducto;
            //unset($objeto->iditem);
            $obj->stock = $objeto->cantidad;
            if($objeto->tipoproducto == 0 || ($objeto->tipoproducto == 1 && $objeto->tipoproduccion == 1)){  //producto simple o producto compuesto de tipo produccion construccion, solo se descuenta sus cantidades, y sus insumos cuando se hace produccion en almacen del producto compuesto
                if(!isset($acumulador['productosSimples'][$objeto->idproducto])){
                    $acumulador['productosSimples'][$objeto->idproducto] = $obj;
                    $acumulador['soloIdproductos'][] = $obj->id;
                }else{
                    $acumulador['productosSimples'][$objeto->idproducto]->stock += $obj->stock;
                }
                $objeto->stockaux = $objeto->promediostock>0?$objeto->stock/$objeto->promediostock:0;
            }elseif($objeto->tipoproducto == 1 && $objeto->tipoproduccion == 0){  //producto compuesto e inmediato es decir por cada venta se descuenta sus insumos
                if(!isset($acumulador['productosCompuestos'][$objeto->idproducto])){
                    $acumulador['productosCompuestos'][$objeto->idproducto] = $obj;
                }else{
                    $acumulador['productosCompuestos'][$objeto->idproducto]->stock += $obj->stock;
                }
                $acumulador['productosCompuestos'][$objeto->idproducto]->porcion = round((float)$acumulador['productosCompuestos'][$objeto->idproducto]->cantidad/(float)$objeto->rendimientoestandar, 4);
            }
            return $acumulador;
        }, ['productosSimples'=>[], 'productosCompuestos'=>[]]);

        
        //////// Selecciona y trae la cantidad subproductos del producto compuesto a descontar del inventario
        $descontarSubproductos = productos_sub::cantidadSubproductosXventa($resultArray['productosCompuestos']);
        //////// sumar los subproductos repetidos
        $reduceSub = [];
        $soloIdInsumos =[];
        foreach($descontarSubproductos as $idx => $obj){
            if(!isset($reduceSub[$obj->id_subproducto])){
                $obj->id = $obj->id_subproducto;
                $obj->stockaux = $obj->promediostock>0?$obj->stock/$obj->promediostock:0;
                $reduceSub[$obj->id_subproducto] = $obj;
                $soloIdInsumos[] = $obj->id;
            }else{
                $reduceSub[$obj->id_subproducto]->stock += $obj->stock;
                $reduceSub[$obj->id_subproducto]->stockaux += $obj->promediostock>0?$obj->stock/$obj->promediostock:0;
            }
        }

        //////// sumar del inventario los productos simples ////////
        if(!empty($resultArray['productosSimples'])){//$invPro = productos::addinv($resultArray['productosSimples'], 'stock');
            //$invPro = stockproductossucursal::addinv1condicion($resultArray['productosSimples'], 'stock', 'productoid', "sucursalid = ".id_sucursal());
            $invPro = stockproductossucursal::aumentarMultiplesColumnas($resultArray['productosSimples'], ['stock', 'stockaux'], 'productoid', "sucursalid = ".id_sucursal());
            //registrar suma de movimiento de invnetario
            $query = "SELECT * FROM stockproductossucursal WHERE productoid IN(".join(', ', $resultArray['soloIdproductos']).") AND sucursalid = ".id_sucursal().";";
            $returnProductos = stockproductossucursal::camposJoinObj($query);
            stockService::upStock_movimientoProductos($resultArray['productosSimples'], $returnProductos, 'devolucion', 'retorno de unidades por anulacion de venta');
        }
            //////// sumar del inventario la variable reduceSub que es el total de subproductos a descontar
        if($invPro && !empty($reduceSub)){//$invSub = subproductos::addinv($reduceSub, 'stock');
            //$invSub = stockinsumossucursal::addinv1condicion($reduceSub, 'stock', 'subproductoid', "sucursalid = ".id_sucursal());
            $invSub = stockinsumossucursal::aumentarMultiplesColumnas($reduceSub, ['stock', 'stockaux'], 'subproductoid', "sucursalid = ".id_sucursal());
            //registrar suma de movimiento de invnetario
            $query = "SELECT * FROM stockinsumossucursal WHERE subproductoid IN(".join(', ', $soloIdInsumos).") AND sucursalid = ".id_sucursal().";";
            $returnInsumos = stockinsumossucursal::camposJoinObj($query);
            stockService::upStock_movimientoInsumos($reduceSub, $returnInsumos, 'devolucion', 'retorno de unidades por anulacion de venta');
        }

        return $resultArray;
    }*/


    public static function datosDelCierreCajaXVenta(object $ultimocierre, object $factura, array $mediospago, $factimpuestos, array $r, $valoresCredito, ?string $tipoVenta = null):bool{
        // El parametro elimina la dependencia HTTP para los nuevos servicios.
        // Se conserva el fallback para llamadas existentes como modo rapido.
        $tipoVenta ??= (string)($_POST['tipoventa'] ?? 'Contado');
        /////////// calcular cantidad de facturas y discriminar por tipo
        $ultimocierre->totalfacturas = $ultimocierre->totalfacturas + 1;  //total de facturas
        if(consecutivos::uncampo('id', $factura->idconsecutivo, 'idtipofacturador')==1){
          $ultimocierre->facturaselectronicas = $ultimocierre->facturaselectronicas + 1;  //total de facturas electronicas
          $ultimocierre->valorfe += $factura->total;
          $ultimocierre->descuentofe += $factura->descuento;
        }else{
          $ultimocierre->facturaspos = $ultimocierre->facturaspos + 1;   //total de facturas pos
          $ultimocierre->valorpos += $factura->total;
          $ultimocierre->descuentopos += $factura->descuento;
        }
        ///////// calcular ventas en efectivo, total descuentos, total ingreso de ventas
        foreach($mediospago as $obj){
          $obj->id_factura = $r[1];
          $obj->cierrecajaid = $ultimocierre->id;
          $obj->idcuota = 'NULL';
          if($obj->idmediopago == 1){
            $ultimocierre->ventasenefectivo +=  ($tipoVenta=='Contado'?$obj->valor:0);
            $ultimocierre->abonosenefectivo += ($tipoVenta=='Credito'?$obj->valor:0);
          }
        }
        //////// establecer el id de factura para factimpuestos ////////////
        foreach($factimpuestos as $obj)$obj->facturaid = $r[1];

        $ultimocierre->creditocapital += $valoresCredito->capital;  //acumulado de los creditos total
        $ultimocierre->creditos += $valoresCredito->capital-$valoresCredito->abonoinicial;  //acumulados de los creditos menos el abono incial
        $ultimocierre->abonoscreditos += $valoresCredito->abonoinicial; //acumulado de los abonos de solo creditos
        $ultimocierre->abonostotales += $valoresCredito->abonoinicial;
        $ultimocierre->domicilios = $ultimocierre->domicilios + $factura->valortarifa;
        //tarifas::tableAJoin2TablesWhereId('direcciones', 'idtarifa', $factura->iddireccion)->valor;
        
        $ultimocierre->ingresoventas =  $ultimocierre->ingresoventas + ($tipoVenta=='Credito'?0:$factura->total);
        $ultimocierre->totaldescuentos = $ultimocierre->totaldescuentos + $factura->descuento;
        $ultimocierre->valorimpuestototal = $ultimocierre->valorimpuestototal + $factura->valorimpuestototal;
        $ultimocierre->basegravable += $factura->base;
        $r = $ultimocierre->actualizar();
        return $r;
    }


    public static function dataInvoiceForPrinterServer(object $datosAdquiriente, object $factura, object $consecutivo):array{
        $customer = [
            "identification_number" => $datosAdquiriente->identification_number??"222222222222",  //obligatorio
            "name" => $datosAdquiriente->business_name??"Consumidor Final",  //obligatorio
            "phone" => $datosAdquiriente->phone??null,
            "address" => $datosAdquiriente->address??null,
            "email" => $datosAdquiriente->email??null,
            "municipality_id" => $datosAdquiriente->municipality_id??null
        ];

        return $dataInvoice['dataInvoice'] = [
            'host' => negocionSucursal()->host,
            'negocio' => negocionSucursal()->negocio,
            'sucursal' => negocionSucursal()->nombre,
            'nit' => negocionSucursal()->nit,
            'direccion' => negocionSucursal()->direccion.' - '.negocionSucursal()->ciudad,
            'telefono' => negocionSucursal()->telefono.' '.negocionSucursal()->movil,
            'email' => negocionSucursal()->email,
            'www' => negocionSucursal()->www,
            'logo' => negocionSucursal()->logo,
            'num_orden' => $factura->num_orden,
            'tipoFactura' => $consecutivo->idtipofacturador,
            'textFactura' => $consecutivo->idtipofacturador == 1?'FACTURA ELECTRONICA DE VENTA':'COMPROBANTE DE VENTA',
            'prefijo' => $factura->prefijo,
            'consecutivo' => $factura->num_consecutivo,
            'fechaPago' => $factura->fechapago,
            'caja' => $factura->caja,
            'vendedor' => $factura->vendedor,
            'consumidorFinal' => $customer,
            'cliente' =>clientes::find('id', $factura->idcliente),
            'tipoventa' =>$factura->tipoventa,
            'subtotal' =>$factura->subtotal,
            'base' => $factura->base,
            'valorimpuestototal' =>$factura->valorimpuestototal,
            'descuento' =>$factura->descuento,
            'total' =>$factura->total,
            'observacion' =>$factura->observacion,
            'resolucion' => $consecutivo,
        ];
    }

}