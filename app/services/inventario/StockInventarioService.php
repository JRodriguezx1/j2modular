<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\movimientos_insumos;
use App\Models\inventario\movimientos_productos;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\productos;
use App\Models\inventario\productos_sub;
use DomainException;
use RuntimeException;
use Throwable;

final class StockInventarioService{

    public function cambiarestadoproducto(array $datos, int $sucursalId): array{
        $productoId = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        if($productoId === false || $productoId <= 0)
            return ['error'=>['El producto no es valido.']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // Mantiene el orden de bloqueo producto -> stock usado por produccion.
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new DomainException('El producto no existe.');

            $stock = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>$productoId, 'sucursalid'=>$sucursalId]);
            if(!$stock)throw new DomainException('El producto no tiene inventario configurado en esta sucursal.');

            $stock->habilitarventa = (int)$stock->habilitarventa === 1 ? 0 : 1;
            $stock->actualizar();
            $db->commit();

            return [
                'exito'=>['Estado del producto actualizado'],
                'estado'=>[$stock->habilitarventa],
                'tipoproducto'=>[$producto->tipoproducto]
            ];
        }catch(DomainException $error){
            $db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al cambiar estado de venta del producto: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }


    public function ajustarStock(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array {
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try {
            [$tipoItem, $itemId, $cantidad, $stockAuxiliar, $promedioStock] = $this->validarDatos($datos, $sucursalId, $usuarioId);
            if(!$db->begin_transaction())
                throw new \RuntimeException('No fue posible iniciar la transaccion.');
            $transaccionIniciada = true;

            if($tipoItem === 0){
                $stock = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>(int)$itemId, 'sucursalid'=>(int)$sucursalId]);
                if(!$stock)throw new DomainException('El producto no tiene inventario configurado en esta sucursal.');

                $movimiento = new movimientos_productos([
                    'idfksucursal'=>$sucursalId,
                    'idproducto_id'=>$itemId,
                    'id_usuarioid'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'ajuste',
                    'referencia'=>"reinicio/ajuste de stock, aux=$stockAuxiliar",
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stock->stock,
                    'stocknuevo'=>$cantidad,
                    'comentario'=>'Stock ajustado manualmente'
                ]);
                $mensaje = 'Stock del producto actualizado con exito';
            }else{
                $stock = stockinsumossucursal::uniquewhereArrayForUpdate(['subproductoid'=>(int)$itemId, 'sucursalid'=>(int)$sucursalId]);
                if(!$stock)throw new DomainException('El insumo no tiene inventario configurado en esta sucursal.');

                $movimiento = new movimientos_insumos([
                    'fksucursal_id'=>$sucursalId,
                    'id_subproductoid'=>$itemId,
                    'idusuario_id'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'ajuste',
                    'referencia'=>"reinicio/ajuste de stock, aux=$stockAuxiliar",
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stock->stock,
                    'stocknuevo'=>$cantidad,
                    'comentario'=>'Stock ajustado manualmente'
                ]);
                $mensaje = 'Stock del insumo - subproducto actualizado con exito';
            }

            $stock->stock = $cantidad;
            $stock->stockaux = $stockAuxiliar;
            $stock->promediostock = $promedioStock;

            if(!$stock->actualizar())throw new \RuntimeException('No fue posible actualizar el stock.');
            $resultadoMovimiento = $movimiento->crear_guardar();
            if(empty($resultadoMovimiento[0]))throw new \RuntimeException('No fue posible registrar el movimiento.');

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el ajuste.');
            $transaccionIniciada = false;

            return ['exito'=>[$mensaje], 'item'=>[$stock]];
        } catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        } catch (Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al ajustar inventario: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }


    //// descontar stock
    public function descontarStock(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try {
            [$tipoItem, $itemId, $cantidad, $stockAuxiliar, $promedioStock] = $this->validarDatos($datos, $sucursalId, $usuarioId, true);
            if(!$db->begin_transaction())
                throw new \RuntimeException('No fue posible iniciar la transaccion.');
            $transaccionIniciada = true;

            if($tipoItem === 0){
                $stock = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>(int)$itemId, 'sucursalid'=>(int)$sucursalId]);
                if(!$stock)throw new DomainException('El producto no tiene inventario configurado en esta sucursal.');

                $stockAnterior = (float)$stock->stock;
                $stockNuevo = $stockAnterior-$cantidad;
                $movimiento = new movimientos_productos([
                    'idfksucursal'=>$sucursalId,
                    'idproducto_id'=>$itemId,
                    'id_usuarioid'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'descuento de unidades',
                    'referencia'=>'descuento manual de unidades',
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stockAnterior,
                    'stocknuevo'=>$stockNuevo,
                    'comentario'=>'descuento manual de unidades de inventario'
                ]);
                $mensaje = 'Stock del producto actualizado con exito';
            }else{
                $stock = stockinsumossucursal::uniquewhereArrayForUpdate(['subproductoid'=>(int)$itemId, 'sucursalid'=>(int)$sucursalId]);
                if(!$stock)throw new DomainException('El insumo no tiene inventario configurado en esta sucursal.');

                $stockAnterior = (float)$stock->stock;
                $stockNuevo = $stockAnterior-$cantidad;
                $movimiento = new movimientos_insumos([
                    'fksucursal_id'=>$sucursalId,
                    'id_subproductoid'=>$itemId,
                    'idusuario_id'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'descuento de unidades',
                    'referencia'=>'descuento manual de unidades a inventario',
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stockAnterior,
                    'stocknuevo'=>$stockNuevo,
                    'comentario'=>'descuento manual de unidades a inventario'
                ]);
                $mensaje = 'Stock del insumo - subproducto actualizado con exito';
            }

            $stock->stock = $stockNuevo;
            $stock->stockaux = (float)$stock->stockaux-$stockAuxiliar;
            $stock->promediostock = $promedioStock;

            if(!$stock->actualizar())throw new \RuntimeException('No fue posible descontar el stock.');
            $resultadoMovimiento = $movimiento->crear_guardar();
            if(empty($resultadoMovimiento[0]))throw new \RuntimeException('No fue posible registrar el movimiento.');

            if(!$db->commit())throw new \RuntimeException('No fue posible confirmar el descuento.');
            $transaccionIniciada = false;

            return ['exito'=>[$mensaje], 'item'=>[$stock]];
        } catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        } catch (Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al descontar inventario: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }

    
    ////////////  Aumentar stock
    public function aumentarStock(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try {
            [$tipoItem, $itemId, $cantidad, $stockAuxiliar, $promedioStock] = $this->validarDatos($datos, $sucursalId, $usuarioId, true);
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la transaccion.');
            $transaccionIniciada = true;

            if($tipoItem === 0){
                $stock = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>(int)$itemId, 'sucursalid'=>(int)$sucursalId]);
                if(!$stock)throw new DomainException('El producto no tiene inventario configurado en esta sucursal.');

                $stockAnterior = (float)$stock->stock;
                $stockNuevo = $stockAnterior + $cantidad;
                $movimiento = new movimientos_productos([
                    'idfksucursal'=>$sucursalId,
                    'idproducto_id'=>$itemId,
                    'id_usuarioid'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'ingreso de unidades',
                    'referencia'=>"sumar unidades a inventario, aux=$stockAuxiliar",
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stockAnterior,
                    'stocknuevo'=>$stockNuevo,
                    'comentario'=>'ingresando nuevas unidades a inventario'
                ]);
                $mensaje = 'Stock del producto actualizado con exito';
            }else{
                $stock = stockinsumossucursal::uniquewhereArrayForUpdate(['subproductoid'=>$itemId, 'sucursalid'=>$sucursalId]);
                if(!$stock)throw new DomainException('El insumo no tiene inventario configurado en esta sucursal.');

                $stockAnterior = (float)$stock->stock;
                $stockNuevo = $stockAnterior + $cantidad;
                $movimiento = new movimientos_insumos([
                    'fksucursal_id'=>$sucursalId,
                    'id_subproductoid'=>$itemId,
                    'idusuario_id'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario,
                    'tipo'=>'ingreso de unidades',
                    'referencia'=>"ingreso manual de unidades a inventario, aux=$stockAuxiliar",
                    'cantidad'=>$cantidad,
                    'stockanterior'=>$stockAnterior,
                    'stocknuevo'=>$stockNuevo,
                    'comentario'=>'ingresando nuevas unidades a inventario'
                ]);
                $mensaje = 'Stock del insumo - subproducto actualizado con exito';
            }

            $stock->stock = $stockNuevo;
            $stock->stockaux = (float)$stock->stockaux + $stockAuxiliar;
            $stock->promediostock = $promedioStock;

            if(!$stock->actualizar())throw new RuntimeException('No fue posible aumentar el stock.');
            $resultadoMovimiento = $movimiento->crear_guardar();
            if(empty($resultadoMovimiento[0]))throw new RuntimeException('No fue posible registrar el movimiento.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar el aumento de stock.');
            $transaccionIniciada = false;

            return ['exito'=>[$mensaje], 'item'=>[$stock]];
        } catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        } catch (Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al aumentar inventario: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }

    
    ///////////////   registrar produccion
    public function registrarProduccion(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try {
            [$tipoItem, $itemId, $cantidad, $stockAuxiliar, $promedioStock] = $this->validarDatos($datos, $sucursalId, $usuarioId, true);
            if($tipoItem !== 0)throw new DomainException('La produccion solo aplica a productos.');
            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la transaccion.');
            $transaccionIniciada = true;

            $producto = productos::findForUpdate('id', $itemId);
            if(!$producto)throw new DomainException('El producto no existe.');
            if((int)$producto->tipoproducto !== 1 || (int)$producto->tipoproduccion !== 1)
                throw new DomainException('El producto no esta configurado para produccion por construccion.');

            $rendimiento = $this->rendimientoValido($producto->rendimientoestandar);
            $stockProducto = $this->bloquearStockProducto($itemId, $sucursalId);
            $consumos = $this->prepararConsumos($itemId, $cantidad / $rendimiento);
            $stocksInsumos = $this->bloquearStocksInsumos($consumos, $sucursalId);

            $stockAnteriorProducto = (float)$stockProducto->stock;
            $stockProducto->stock = $stockAnteriorProducto + $cantidad;
            $stockProducto->stockaux = (float)$stockProducto->stockaux + $stockAuxiliar;
            $stockProducto->promediostock = $promedioStock;
            if(!$stockProducto->actualizar())
                throw new RuntimeException('No fue posible aumentar el stock del producto.');

            if($consumos && !stockinsumossucursal::reducirMultiplesColumnas($consumos,['stock', 'stockaux'], 'subproductoid', "sucursalid = $sucursalId"))
                throw new RuntimeException('No fue posible descontar los insumos.');

            $movimientoProducto = new movimientos_productos([
                'idfksucursal'=>$sucursalId,
                'idproducto_id'=>$itemId,
                'id_usuarioid'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>'ingreso de unidades',
                'referencia'=>"sumar unidades a inventario, aux=$stockAuxiliar",
                'cantidad'=>$cantidad,
                'stockanterior'=>$stockAnteriorProducto,
                'stocknuevo'=>$stockProducto->stock,
                'comentario'=>'ingresando nuevas unidades a inventario'
            ]);
            $resultadoMovimiento = $movimientoProducto->crear_guardar();
            if(empty($resultadoMovimiento[0]))
                throw new RuntimeException('No fue posible registrar el movimiento del producto.');

            $stocksActualizados = $this->actualizarStocksYCrearMovimientos($consumos, $stocksInsumos, $sucursalId, $usuarioId, $nombreUsuario);

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la produccion.');
            $transaccionIniciada = false;

            $producto->stock = $stockProducto->stock;
            if(!$consumos){
                return [
                    'exito'=>['Se realizo produccion, sin embargo no hay insumos asociados a descontar'],
                    'item'=>[$producto]
                ];
            }

            return [
                'exito'=>['Se realizo produccion con exito'],
                'item'=>[$producto],
                'insumos'=>[$stocksActualizados]
            ];
        } catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        } catch (Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al registrar produccion de inventario: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente']];
        }
    }

    private function bloquearStockProducto(int $productoId, int $sucursalId): stockproductossucursal{
        $stock = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>(int)$productoId,'sucursalid'=>(int)$sucursalId]);
        if(!$stock)throw new DomainException('El producto no tiene inventario configurado en esta sucursal.');
        return $stock;
    }

    private function prepararConsumos(int $productoId, float $porcion): array{
        if(!is_finite($porcion) || $porcion <= 0)
            throw new DomainException('La proporcion de la produccion no es valida.');

        $recetas = productos_sub::idregistros('id_producto', $productoId);
        if(!$recetas)return [];

        $idsReceta = array_map(static fn($receta):int => (int)$receta->id, $recetas);
        $recetas = productos_sub::findManyForUpdate($idsReceta);
        if(count($recetas) !== count($idsReceta))
            throw new DomainException('La receta del producto cambio durante la produccion.');

        $consumos = [];
        foreach($recetas as $receta){
            if((int)$receta->id_producto !== $productoId)
                throw new DomainException('La receta del producto cambio durante la produccion.');
            $insumoId = (int)$receta->id_subproducto;
            if($insumoId <= 0 || !is_numeric($receta->cantidadsubproducto))
                throw new DomainException('La receta del producto contiene un insumo no valido.');

            $cantidadReceta = (float)$receta->cantidadsubproducto;
            if(!is_finite($cantidadReceta) || $cantidadReceta < 0)
                throw new DomainException('La receta del producto contiene una cantidad no valida.');
            if($cantidadReceta === 0.0)continue;

            $cantidadConsumo = $cantidadReceta * $porcion;
            if(!is_finite($cantidadConsumo) || $cantidadConsumo <= 0)
                throw new DomainException('No fue posible calcular el consumo de la receta.');

            if(!isset($consumos[$insumoId])){
                $consumos[$insumoId] = (object)[
                    'id'=>$insumoId,
                    'stock'=>0.0,
                    'stockaux'=>0.0
                ];
            }
            $consumos[$insumoId]->stock += $cantidadConsumo;
            if(!is_finite($consumos[$insumoId]->stock))
                throw new DomainException('El consumo total de un insumo no es valido.');
        }
        return array_values($consumos);
    }

    private function bloquearStocksInsumos(array $consumos, int $sucursalId): array{
        if(!$consumos)return [];

        $idsStock = [];
        foreach($consumos as $consumo){
            $stock = stockinsumossucursal::uniquewhereArray(['subproductoid'=>(int)$consumo->id, 'sucursalid'=>$sucursalId]);
            if(!$stock)throw new DomainException("El insumo {$consumo->id} no tiene inventario configurado en esta sucursal.");
            $idsStock[] = (int)$stock->id;
        }

        $stocks = stockinsumossucursal::findManyForUpdate($idsStock);
        if(count($stocks) !== count($consumos))
            throw new DomainException('No fue posible bloquear todos los insumos de la produccion.');

        $stocksPorInsumo = [];
        foreach($stocks as $stock){
            if((int)$stock->sucursalid !== $sucursalId)
                throw new DomainException('Se encontro inventario de una sucursal diferente.');
            $stocksPorInsumo[(int)$stock->subproductoid] = $stock;
        }

        foreach($consumos as $consumo){
            $stock = $stocksPorInsumo[(int)$consumo->id] ?? null;
            if(!$stock)throw new DomainException("No fue posible bloquear el insumo {$consumo->id}.");
            $promedio = (float)$stock->promediostock;
            $consumo->stockaux = $promedio > 0 ? (float)$consumo->stock / $promedio : 0.0;
            if(!is_finite($consumo->stockaux))
                throw new DomainException("No fue posible calcular el stock auxiliar del insumo {$consumo->id}.");
        }
        return $stocksPorInsumo;
    }


    private function actualizarStocksYCrearMovimientos(array $consumos, array $stocksPorInsumo, int $sucursalId, int $usuarioId, string $nombreUsuario): array {
        if(!$consumos)return [];

        $movimientos = [];
        $stocksActualizados = [];
        foreach($consumos as $consumo){
            $stock = $stocksPorInsumo[(int)$consumo->id];
            $stockAnterior = (float)$stock->stock;
            $stock->stock = $stockAnterior - (float)$consumo->stock;
            $stock->stockaux = (float)$stock->stockaux - (float)$consumo->stockaux;
            $stocksActualizados[] = $stock;

            $movimientos[] = new movimientos_insumos([
                'fksucursal_id'=>$sucursalId,
                'id_subproductoid'=>(int)$consumo->id,
                'idusuario_id'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>'descuento por produccion',
                'referencia'=>'Descuento de insumos por produccion',
                'cantidad'=>(float)$consumo->stock,
                'stockanterior'=>$stockAnterior,
                'stocknuevo'=>$stock->stock,
                'comentario'=>'Descuento de insumos por produccion'
            ]);
        }

        $resultado = (new movimientos_insumos())->crear_varios_reg_arrayobj($movimientos);
        if(empty($resultado[0]))throw new RuntimeException('No fue posible registrar los movimientos de los insumos.');
        return $stocksActualizados;
    }


    private function validarDatos(array $datos, int $sucursalId, int $usuarioId, bool $cantidadPositiva = false):array{
        $tipoItem = filter_var($datos['tipoitem'] ?? null, FILTER_VALIDATE_INT);
        $itemId = filter_var($datos['iditem'] ?? null, FILTER_VALIDATE_INT);
        $cantidad = $cantidadPositiva ? $this->numeroPositivo($datos, 'cantidad') : $this->numeroNoNegativo($datos, 'cantidad');
        $stockAuxiliar = $this->numeroNoNegativo($datos, 'stockaux', 0);
        $promedioStock = $this->numeroNoNegativo($datos, 'promediostock', 1);

        if($tipoItem === false || !in_array($tipoItem, [0, 1], true))
            throw new DomainException('El tipo de item no es valido.');
        if($itemId === false || $itemId <= 0)
            throw new DomainException('El item de inventario no es valido.');
        if($sucursalId <= 0 || $usuarioId <= 0)
            throw new DomainException('No fue posible identificar la sucursal o el usuario.');
        
        return [$tipoItem, $itemId, $cantidad, $stockAuxiliar, $promedioStock];
    }

    
    private function rendimientoValido(mixed $valor): float{
        if(!is_numeric($valor))throw new DomainException('El rendimiento estandar del producto no es valido.');
        $rendimiento = (float)$valor;
        if(!is_finite($rendimiento) || $rendimiento <= 0)
            throw new DomainException('El rendimiento estandar del producto debe ser mayor que cero.');
        return $rendimiento;
    }

    private function numeroPositivo(array $datos, string $campo): float{
        $numero = $this->numeroNoNegativo($datos, $campo);
        if($numero <= 0)throw new DomainException("El campo $campo debe ser mayor que cero.");
        return $numero;
    }

    private function numeroNoNegativo(array $datos, string $campo, int|float|null $predeterminado = null): float{
        $valor = $datos[$campo] ?? $predeterminado;
        if(!is_numeric($valor))throw new DomainException("El campo $campo no es valido.");
        $numero = (float)$valor;
        if(!is_finite($numero) || $numero < 0)throw new DomainException("El campo $campo no puede ser negativo.");
        return $numero;
    }

}
