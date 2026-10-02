<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\productos;
use RuntimeException;
use Throwable;

final class ActualizarPrecioVentaInventarioService{

    public function ejecutar(array $datos): array{
        $productoId = (int)($datos['idelemento'] ?? 0);
        $precioVenta = $datos['precio_venta'] ?? null;

        if($productoId <= 0 || !is_numeric($precioVenta) || !is_finite((float)$precioVenta) || (float)$precioVenta < 0)
            return ['error'=>['El precio de venta no es valido']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new RuntimeException('El producto no existe.');

            $producto->precio_venta = (float)$precioVenta;
            $producto->actualizar();
            $db->commit();
            return ['exito'=>[1]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al actualizar precio de venta: '.$error->getMessage());
            return ['error'=>['Error, intenta nuevamente']];
        }
    }
}
