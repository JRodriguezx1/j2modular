<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\costosinsumos;
use App\Models\inventario\costosproductos;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\proveedores;
use App\Models\inventario\proveedor_costos;
use RuntimeException;
use Throwable;

final class ActualizarCostoInventarioService{

    public function ejecutar(array $datos, int $sucursalId): array
    {
        $tipoElemento = (int)($datos['tipoelemento'] ?? 0);
        $elementoId = (int)($datos['idelemento'] ?? 0);
        if($elementoId <= 0 || !isset($datos['precio_compra']) || !is_numeric($datos['precio_compra']))
            return ['error'=>['Error, intenta nuevamente']];

        $precioCompra = (float)$datos['precio_compra'];
        $db = ActiveRecord::getDB();
        $db->begin_transaction();

        try{
            if($tipoElemento === 1){
                // Mantiene el mismo orden de bloqueos que el registro de compras:
                // productos compuestos, formulas y finalmente el insumo.
                $insumo = subproductos::find('id', $elementoId);
                if(!$insumo)throw new RuntimeException('El elemento seleccionado no existe.');
                $insumo->precio_compra = $precioCompra;
                $productosAfectados = (new RecalcularCostosFormulasService())->ejecutar([$insumo]);

                $insumo = subproductos::findForUpdate('id', $elementoId);
                if(!$insumo)throw new RuntimeException('El elemento seleccionado no existe.');
                $insumo->precio_compra = $precioCompra;
                $insumo->actualizar();
                $this->registrarHistoricosDelInsumo($insumo, $productosAfectados, $sucursalId);
            }else{
                $producto = productos::findForUpdate('id', $elementoId);
                if(!$producto)throw new RuntimeException('El elemento seleccionado no existe.');
                $producto->precio_compra = $precioCompra;
                $producto->actualizar();

                // Registra el ajuste manual del costo del producto simple.
                (new costosproductos(['sucursalfk_id'=>$sucursalId, 'productofk'=>$producto->id, 'tipocosto'=>0, 'precio_compra'=>$producto->precio_compra]))->crear_guardar();
            }
            $db->commit();
            return ['exito'=>[1]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al actualizar costos de inventario: '.$error->getMessage());
            return ['error'=>['Error, intenta nuevamente']];
        }
    }

    private function registrarHistoricosDelInsumo(subproductos $insumo, array $productosAfectados, int $sucursalId): void{
        // Guarda el historico del ajuste realizado al insumo.
        (new costosinsumos(['sucursal_fkid'=>$sucursalId, 'idsubproductoid'=>$insumo->id, 'precio_compra'=>$insumo->precio_compra]))->crear_guardar();
        if(empty($productosAfectados))return;
        // Inserta en conjunto el historico de los productos compuestos recalculados.
        $historicos = array_map(
            static fn(productos $producto): costosproductos => new costosproductos([
                'sucursalfk_id'=>$sucursalId,
                'productofk'=>$producto->id,
                'tipocosto'=>0,
                'precio_compra'=>$producto->precio_compra
            ]),
            $productosAfectados
        );
        (new costosproductos())->crear_varios_reg_arrayobj($historicos);
    }


    //metodo para registrar costo del producto segun proveedor
    /**
     * Guarda el costo vigente de un proveedor para un producto o insumo.
     * No modifica el costo general del artículo.
     */
    public function guardarCostoProveedor(array $datos, int $sucursalId, bool $manejarTransaccion = true): array{
        $proveedorId = filter_var($datos['proveedor_id'] ?? null, FILTER_VALIDATE_INT);
        $elementoId = filter_var($datos['idelemento'] ?? null, FILTER_VALIDATE_INT);
        $tipoElemento = filter_var($datos['tipoelemento'] ?? null, FILTER_VALIDATE_INT);
        $costoRecibido = $datos['costo'] ?? null;

        if($sucursalId <= 0 || $proveedorId === false || $proveedorId <= 0 || $elementoId === false || $elementoId <= 0 || !in_array($tipoElemento, [0, 1], true) || !is_numeric($costoRecibido))
            return ['error' => ['Los datos del costo no son válidos.']];

        $costo = (float) $costoRecibido;
        if(!is_finite($costo) || $costo < 0)
            return ['error' => ['El costo no puede ser negativo.']];

        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;
        try{
            if($manejarTransaccion){
                $db->begin_transaction();
                $transaccionIniciada = true;
            }

            // Bloquear el proveedor evita dos inserciones simultáneas
            // para la misma combinación de proveedor y artículo.
            if(!proveedores::findForUpdate('id', $proveedorId))
                throw new RuntimeException('El proveedor no existe.');

            if($tipoElemento === 0 && !productos::find('id', $elementoId))
                throw new RuntimeException('El producto no existe.');

            if ($tipoElemento === 1 && !subproductos::find('id', $elementoId))
                throw new RuntimeException('El insumo no existe.');

            // 0 = producto; 1 = insumo de la tabla subproductos.
            $columnaArticulo = $tipoElemento === 0 ? 'producto_id' : 'subproducto_id';
            $costoProveedor = proveedor_costos::uniquewhereArrayForUpdate(['sucursal_id' => $sucursalId, 'proveedor_id' => $proveedorId, $columnaArticulo => $elementoId]);

            // Conservar el valor como texto evita que ActiveRecord trate 0 como null.
            $costoTexto = (string)$costoRecibido;
            $fecha = date('Y-m-d H:i:s');

            if($costoProveedor){
                $costoProveedor->costo = $costoTexto;
                $costoProveedor->fecha_actualizacion = $fecha;
                $costoProveedor->activo = 1;
                $costoProveedor->actualizar();
            }else{
                $costoProveedor = new proveedor_costos([
                    'sucursal_id' => $sucursalId,
                    'proveedor_id' => $proveedorId,
                    'producto_id' => $tipoElemento === 0 ? $elementoId : null,
                    'subproducto_id' => $tipoElemento === 1 ? $elementoId : null,
                    'costo' => $costoTexto,
                    'fecha_actualizacion' => $fecha,
                    'activo' => 1
                ]);
                $costoProveedor->crear_guardar();
            }

            if($manejarTransaccion){
                $db->commit();
                $transaccionIniciada = false;
            }

            return ['exito' => ['Costo del proveedor guardado correctamente.']];
        }catch (Throwable $error){
            if($transaccionIniciada)
                $db->rollback();
            error_log('Error al guardar costo por proveedor: ' . $error->getMessage());
            return ['error' => ['No fue posible guardar el costo del proveedor.']];
        }
    }

}
