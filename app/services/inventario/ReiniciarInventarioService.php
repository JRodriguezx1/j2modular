<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\movimientos_insumos;
use App\Models\inventario\movimientos_productos;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use DomainException;
use RuntimeException;
use Throwable;

final class ReiniciarInventarioService
{
    public function ejecutar(int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = ActiveRecord::getDB();
        $transaccionIniciada = false;

        try {
            if($sucursalId <= 0 || $usuarioId <= 0)
                throw new DomainException('No fue posible identificar la sucursal o el usuario.');

            if(!$db->begin_transaction())throw new RuntimeException('No fue posible iniciar la transaccion.');
            $transaccionIniciada = true;

            $stocksProductos = $this->bloquearExistencias(stockproductossucursal::class, $sucursalId);
            $stocksInsumos = $this->bloquearExistencias(stockinsumossucursal::class, $sucursalId);
            $movimientosProductos = $this->prepararMovimientosProductos($stocksProductos, $sucursalId, $usuarioId, $nombreUsuario);
            $movimientosInsumos = $this->prepararMovimientosInsumos($stocksInsumos, $sucursalId, $usuarioId, $nombreUsuario);

            $this->reiniciarExistencias(stockproductossucursal::class, $stocksProductos);
            $this->reiniciarExistencias(stockinsumossucursal::class, $stocksInsumos);
            $this->guardarMovimientos(new movimientos_productos(), $movimientosProductos);
            $this->guardarMovimientos(new movimientos_insumos(), $movimientosInsumos);

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar el reinicio.');
            $transaccionIniciada = false;

            return ['exito'=>['Stock reiniciado completamente']];
        } catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        } catch (Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al reiniciar inventario: '.$error->getMessage());
            return ['error'=>['Error al reiniciar stock, intenta nuevamente']];
        }
    }

    private function bloquearExistencias(string $modelo, int $sucursalId): array{
        $existencias = $modelo::whereArray(['sucursalid'=>$sucursalId]);
        if(!$existencias)return [];

        $ids = array_map(static fn($stock):int => (int)$stock->id, $existencias);
        $existencias = $modelo::findManyForUpdate($ids);
        if(count($existencias) !== count($ids))
            throw new DomainException('El inventario cambio durante el reinicio. Intenta nuevamente.');

        foreach($existencias as $stock)
            if((int)$stock->sucursalid !== $sucursalId)
                throw new DomainException('Se encontro inventario de una sucursal diferente.');
        return $existencias;
    }

    private function reiniciarExistencias(string $modelo, array $existencias): void{
        if(!$existencias)return;
        $ids = array_map(static fn($stock):int => (int)$stock->id, $existencias);
        if(!$modelo::actualizar_ids(['stock'=>0, 'stockaux'=>0], $ids))
            throw new RuntimeException('No fue posible reiniciar todas las existencias.');
    }

    private function prepararMovimientosProductos(array $stocks, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $movimientos = [];
        foreach($stocks as $stock){
            if(!$this->tieneSaldo($stock))continue;
            $movimientos[] = new movimientos_productos([
                'idfksucursal'=>$sucursalId,
                'idproducto_id'=>(int)$stock->productoid,
                'id_usuarioid'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>'reinicio de inventario',
                'referencia'=>"reinicio de inventario a cero, stockaux anterior={$stock->stockaux}",
                'cantidad'=>abs((float)$stock->stock),
                'stockanterior'=>(float)$stock->stock,
                'stocknuevo'=>0,
                'comentario'=>'Stock reiniciado completamente'
            ]);
        }
        return $movimientos;
    }

    private function prepararMovimientosInsumos(array $stocks, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $movimientos = [];
        foreach($stocks as $stock){
            if(!$this->tieneSaldo($stock))continue;
            $movimientos[] = new movimientos_insumos([
                'fksucursal_id'=>$sucursalId,
                'id_subproductoid'=>(int)$stock->subproductoid,
                'idusuario_id'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>'reinicio de inventario',
                'referencia'=>"reinicio de inventario a cero, stockaux anterior={$stock->stockaux}",
                'cantidad'=>abs((float)$stock->stock),
                'stockanterior'=>(float)$stock->stock,
                'stocknuevo'=>0,
                'comentario'=>'Stock reiniciado completamente'
            ]);
        }
        return $movimientos;
    }

    private function guardarMovimientos(ActiveRecord $modelo, array $movimientos): void{
        if(!$movimientos)return;
        $resultado = $modelo->crear_varios_reg_arrayobj($movimientos);
        if(empty($resultado[0]))throw new RuntimeException('No fue posible registrar todos los movimientos.');
    }

    private function tieneSaldo(object $stock): bool{
        return (float)$stock->stock !== 0.0 || (float)$stock->stockaux !== 0.0;
    }
}
