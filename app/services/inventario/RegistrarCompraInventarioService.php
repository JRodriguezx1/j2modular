<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\caja\cierrescajas;
use App\Models\compras;
use App\Models\configuraciones\bancos;
use App\Models\configuraciones\caja;
use App\Models\detallecompra;
use App\Models\gastos;
use App\Models\inventario\costosinsumos;
use App\Models\inventario\costosproductos;
use App\Models\inventario\movimientos_insumos;
use App\Models\inventario\movimientos_productos;
use App\Models\inventario\productos;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\subproductos;
use DomainException;
use RuntimeException;
use Throwable;

final class RegistrarCompraInventarioService{
    
    public function ejecutar(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try{
            [$compra, $lineas] = $this->prepararCompra($datos, $sucursalId, $usuarioId, $nombreUsuario);
            $errores = $compra->validar();
            if(!empty($errores))return $errores;

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la transaccion de la compra.');
            $transaccionIniciada = true;

            $cajaOrigen = caja::findForUpdate('id', (int)$compra->idorigencaja);
            if(!$cajaOrigen || (int)$cajaOrigen->idsucursalid !== $sucursalId || (int)$cajaOrigen->estado !== 1)
                throw new DomainException('La caja seleccionada no pertenece a la sucursal o esta inactiva.');

            $proveedor = proveedores::find('id', (int)$compra->idproveedor);
            if(!$proveedor)throw new DomainException('El proveedor seleccionado no existe.');
            $compra->nombreproveedor = (string)$proveedor->nombre;
            $compra->nombreorigencaja = (string)$cajaOrigen->nombre;

            if((int)$compra->origenpago === 1){
                $banco = bancos::find('id', (int)$compra->idorigenbanco);
                if(!$banco)throw new DomainException('El banco seleccionado no existe.');
                $compra->nombreorigenbanco = (string)$banco->nombre;
                $compra->nombreorigenpago = 'Banco';
            }else{
                $compra->idorigenbanco = '';
                $compra->nombreorigenbanco = '';
                $compra->nombreorigenpago = 'Caja';
            }

            $costosCompuestos = (new RecalcularCostosFormulasService())->ejecutar($lineas['subproductos']);
            $this->bloquearItemsComprados($lineas);
            $stocks = $this->bloquearStocks($lineas, $sucursalId);
            $idCompra = $this->guardarCompraYDetalle($compra, $lineas['carrito']);
            $this->actualizarInventario($lineas, $stocks, $costosCompuestos, $sucursalId, $usuarioId, $nombreUsuario);

            $cierre = $this->obtenerOCrearCierre($cajaOrigen, $sucursalId, $usuarioId, $nombreUsuario);
            $this->registrarGasto($compra, $idCompra, $cierre, $sucursalId, $usuarioId);

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la compra.');
            $transaccionIniciada = false;
            return [
                'exito'=>['Compra realizada con exito.', 'Gasto de compra registrado correctamente'],
                'idcompra'=>$idCompra
            ];
        }catch(DomainException $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al registrar compra de inventario: '.$error->getMessage());
            return ['error'=>['Hubo un error, intentalo nuevamente. '.$error->getMessage()]];
        }
    }

    private function prepararCompra(array $datos, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $carrito = json_decode((string)($datos['carrito'] ?? ''));
        if(!is_array($carrito) || empty($carrito))throw new DomainException('Debe agregar al menos un item para comprar.');

        $lineas = ['carrito'=>[], 'productos'=>[], 'subproductos'=>[]];
        $base = 0.0;
        $cantidadItems = 0.0;
        foreach($carrito as $item){
            if(!is_object($item))throw new DomainException('La lista de articulos no es valida.');
            $id = (int)($item->iditem ?? 0);
            $tipo = (int)($item->tipo ?? -1);
            $cantidad = (float)($item->cantidad ?? 0);
            $valorCompra = (float)($item->valorcompra ?? 0);
            if($id <= 0 || !in_array($tipo, [0, 1], true) || $cantidad <= 0 || $valorCompra <= 0)
                throw new DomainException('Uno de los articulos no es valido.');

            // El frontend entrega la cantidad en unidad base y el valor total de la linea.
            $item->iditem = $id;
            $item->id = $id;
            $item->tipo = $tipo;
            $item->cantidad = $cantidad;
            $item->stock = $cantidad;
            $item->valorcompra = $valorCompra;
            $item->precio_compra = $valorCompra / $cantidad;
            $item->valorunidad = $item->precio_compra;
            $item->subtotal = $valorCompra;
            $item->tipocosto = 1;

            if($tipo === 0){
                $item->idpx = $id;
                $item->productofk = $id;
                $lineas['productos'][] = $item;
            }else{
                $item->idsx = $id;
                $item->idsubproductoid = $id;
                $lineas['subproductos'][] = $item;
            }
            $lineas['carrito'][] = $item;
            $base += $valorCompra;
            $cantidadItems += $cantidad;
        }

        $valorImpuesto = max(0.0, (float)($datos['valorimp'] ?? 0));
        if(!is_finite($base) || !is_finite($cantidadItems) || !is_finite($base + $valorImpuesto))
            throw new DomainException('Los totales de la compra no son validos.');

        $compra = new compras($datos);
        $compra->idusuario = $usuarioId;
        $compra->nombreusuario = $nombreUsuario;
        $compra->id_sucursal_id = $sucursalId;
        $compra->idorigencaja = (int)($datos['idorigencaja'] ?? 0);
        $compra->idproveedor = (int)($datos['idproveedor'] ?? 0);
        $origenPago = (int)($datos['origenpago'] ?? -1);
        if($compra->idorigencaja <= 0 || $compra->idproveedor <= 0 || !in_array($origenPago, [0, 1], true))
            throw new DomainException('El proveedor, la caja o el origen del pago no son validos.');
        $compra->origenpago = $origenPago;
        if($origenPago === 1){
            $compra->idorigenbanco = (int)($datos['idorigenbanco'] ?? 0);
            if($compra->idorigenbanco <= 0)throw new DomainException('Debe seleccionar un banco.');
        }
        $compra->base = $base;
        $compra->subtotal = $base;
        $compra->valorimp = $valorImpuesto;
        $compra->impuesto = $base > 0 ? $valorImpuesto * 100 / $base : 0;
        $compra->cantidaditems = $cantidadItems;
        $compra->valortotal = $base + $valorImpuesto;
        return [$compra, $lineas];
    }

    private function guardarCompraYDetalle(compras $compra, array $carrito): int{
        $resultado = $compra->crear_guardar();
        if(!$resultado[0])throw new RuntimeException('No fue posible registrar la compra.');
        foreach($carrito as $item)$item->idcompra = (int)($resultado[1] ?? 0);
        $this->insertarVarios(new detallecompra(), $carrito, 'No fue posible registrar el detalle de la compra.');
        return (int)($resultado[1] ?? 0);
    }

    private function bloquearItemsComprados(array $lineas): void{
        $idsProductos = array_map(static fn($item):int => (int)$item->id, $lineas['productos']);
        $idsInsumos = array_map(static fn($item):int => (int)$item->id, $lineas['subproductos']);
        // Los IDs vienen del selector del frontend; solo se bloquean antes de actualizarlos.
        productos::findManyForUpdate($idsProductos);
        subproductos::findManyForUpdate($idsInsumos);
    }

    private function bloquearStocks(array $lineas, int $sucursalId): array{
        $idsProductos = array_column($lineas['productos'], 'id');
        $idsInsumos = array_column($lineas['subproductos'], 'id');
        // Consulta en bloque los registros de stock pertenecientes a la sucursal.
        $stocksProductos = stockproductossucursal::IN_Where('productoid', $idsProductos, ['sucursalid', $sucursalId]);
        $stocksInsumos = stockinsumossucursal::IN_Where('subproductoid', $idsInsumos, ['sucursalid', $sucursalId]);
        // Bloquea las filas encontradas y las indexa por el ID del articulo.
        $stocksProductos = stockproductossucursal::findManyForUpdate(array_column($stocksProductos, 'id'));
        $stocksInsumos = stockinsumossucursal::findManyForUpdate(array_column($stocksInsumos, 'id'));
        if(count($stocksProductos) !== count(array_unique($idsProductos)) || count($stocksInsumos) !== count(array_unique($idsInsumos)))
            throw new DomainException('Uno de los articulos no tiene stock configurado en esta sucursal.');

        return [0=>array_column($stocksProductos, null, 'productoid'), 1=>array_column($stocksInsumos, null, 'subproductoid')];
    }

    private function actualizarInventario(array $lineas, array $stocks, array $costosCompuestos, int $sucursalId, int $usuarioId, string $nombreUsuario): void{
        $movimientosProductos = [];
        $movimientosInsumos = [];
        $historicoProductos = array_map(
            static fn(productos $producto): costosproductos => new costosproductos([
                'sucursalfk_id'=>$sucursalId,
                'productofk'=>$producto->id,
                'tipocosto'=>1,
                'precio_compra'=>$producto->precio_compra
            ]),
            $costosCompuestos
        );
        $historicoInsumos = [];

        if($lineas['productos']){
            if(!productos::camposaddinv($lineas['productos'], ['stock', 'precio_compra']))
                throw new RuntimeException('No fue posible actualizar los productos.');
            if(!stockproductossucursal::aumentarMultiplesColumnas($lineas['productos'], ['stock'], 'productoid', "sucursalid = $sucursalId"))
                throw new RuntimeException('No fue posible actualizar el stock de productos.');

            foreach($lineas['productos'] as $item){
                $anterior = (float)$stocks[0][$item->id]->stock;
                $movimientosProductos[] = new movimientos_productos([
                    'idfksucursal'=>$sucursalId, 'idproducto_id'=>$item->id, 'id_usuarioid'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario, 'tipo'=>'compra', 'referencia'=>'ingreso de unidades por compra',
                    'cantidad'=>$item->cantidad, 'stockanterior'=>$anterior, 'stocknuevo'=>$anterior + $item->cantidad,
                    'comentario'=>'ingreso de unidades por compra'
                ]);
                $historicoProductos[] = new costosproductos(['sucursalfk_id'=>$sucursalId, 'productofk'=>$item->id, 'tipocosto'=>1, 'precio_compra'=>$item->precio_compra]);
            }
        }

        if($lineas['subproductos']){
            if(!subproductos::camposaddinv($lineas['subproductos'], ['stock', 'precio_compra']))
                throw new RuntimeException('No fue posible actualizar los insumos.');
            if(!stockinsumossucursal::aumentarMultiplesColumnas($lineas['subproductos'], ['stock'], 'subproductoid', "sucursalid = $sucursalId"))
                throw new RuntimeException('No fue posible actualizar el stock de insumos.');

            foreach($lineas['subproductos'] as $item){
                $anterior = (float)$stocks[1][$item->id]->stock;
                $movimientosInsumos[] = new movimientos_insumos([
                    'fksucursal_id'=>$sucursalId, 'id_subproductoid'=>$item->id, 'idusuario_id'=>$usuarioId,
                    'nombreusuario'=>$nombreUsuario, 'tipo'=>'compra', 'referencia'=>'ingreso de unidades por compra',
                    'cantidad'=>$item->cantidad, 'stockanterior'=>$anterior, 'stocknuevo'=>$anterior + $item->cantidad,
                    'comentario'=>'ingreso de unidades por compra'
                ]);
                $historicoInsumos[] = new costosinsumos(['sucursal_fkid'=>$sucursalId, 'idsubproductoid'=>$item->id,'tipocosto'=>1, 'precio_compra'=>$item->precio_compra]);
            }
        }

        if($movimientosProductos)$this->insertarVarios(new movimientos_productos(), $movimientosProductos, 'No fue posible registrar los movimientos de productos.');
        if($movimientosInsumos)$this->insertarVarios(new movimientos_insumos(), $movimientosInsumos, 'No fue posible registrar los movimientos de insumos.');
        if($historicoProductos)$this->insertarVarios(new costosproductos(), $historicoProductos, 'No fue posible registrar los costos de productos.');
        if($historicoInsumos)$this->insertarVarios(new costosinsumos(), $historicoInsumos, 'No fue posible registrar los costos de insumos.');
    }

    private function obtenerOCrearCierre(caja $cajaOrigen, int $sucursalId, int $usuarioId, string $nombreUsuario): cierrescajas{
        $cierre = cierrescajas::uniquewhereArrayForUpdate(['estado'=>0, 'idcaja'=>(int)$cajaOrigen->id, 'idsucursal_id'=>$sucursalId]);
        if($cierre)return $cierre;

        $cierre = new cierrescajas(['idcaja'=>(int)$cajaOrigen->id, 'nombrecaja'=>(string)$cajaOrigen->nombre, 'estado'=>0, 'idsucursal_id'=>$sucursalId, 'id_usuario'=>$usuarioId, 'nombreusuario'=>$nombreUsuario]);
        $resultado = $cierre->crear_guardar();
        $cierre->id = (int)($resultado[1] ?? 0);
        if(empty($resultado[0]) || $cierre->id === 0)throw new RuntimeException('No fue posible abrir el cierre de caja.');
        return $cierre;
    }

    private function registrarGasto(compras $compra, int $idCompra, cierrescajas $cierre, int $sucursalId, int $usuarioId): void{
        $gasto = new gastos([
            'idg_usuario'=>$usuarioId, 'id_compra'=>$idCompra,
            'id_banco'=>(int)$compra->origenpago === 1 ? $compra->idorigenbanco : null,
            'idg_caja'=>(int)$compra->idorigencaja, 'idg_cierrecaja'=>(int)$cierre->id,
            'idcategoriagastos'=>1, 'tipo_origen'=>(int)$compra->origenpago,
            'operacion'=>'compra', 'valor'=>$compra->valortotal
        ]);
        $gasto->id_sucursalfk = $sucursalId;
        $errores = $gasto->validar();
        if(!empty($errores))throw new DomainException($errores['error'][0] ?? 'El gasto de la compra no es valido.');
        $gasto->crear_guardar();

        if((int)$compra->origenpago === 0)
            $cierre->gastoscaja = (float)$cierre->gastoscaja + (float)$compra->valortotal;
        else
            $cierre->gastosbanco = (float)$cierre->gastosbanco + (float)$compra->valortotal;
        if(!$cierre->actualizar())throw new RuntimeException('No fue posible actualizar el cierre de caja.');
    }

    private function insertarVarios(ActiveRecord $modelo, array $registros, string $mensaje): void{
        $resultado = $modelo->crear_varios_reg_arrayobj($registros);
        if(empty($resultado[0]) || ActiveRecord::getDB()->affected_rows !== count($registros))throw new RuntimeException($mensaje);
    }

}
