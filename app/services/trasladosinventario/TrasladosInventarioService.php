<?php

namespace App\services\trasladosinventario;

use App\Models\inventario\detalletrasladoinv;
use App\Models\inventario\movimientos_insumos;
use App\Models\inventario\movimientos_productos;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\traslado_inv;
use App\Policies\TrasladoPolicy;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Ejecuta movimientos de inventario relacionados con traslados.
 *
 * No utiliza $_POST, $_SESSION, id_sucursal() ni envia notificaciones.
 */
final class TrasladosInventarioService
{
    private const ESTADO_EN_TRANSITO = 'entransito';
    private const ESTADO_ENTREGADA = 'entregada';
    private const TIPO_MOVIMIENTO = 'salida por traslado';
    private const REFERENCIA_MOVIMIENTO = 'descuento de unidades por traslado';
    private const TIPO_MOVIMIENTO_INGRESO = 'ingreso por traslado';
    private const REFERENCIA_MOVIMIENTO_INGRESO = 'ingreso de unidades por traslado';

    private TrasladoPolicy $policy;

    public function __construct(?TrasladoPolicy $policy = null)
    {
        $this->policy = $policy ?? new TrasladoPolicy();
    }


    /**
     * Aumenta los articulos recibidos y cambia la orden a entregada.
     */
    public function recibir(int $trasladoId, int $sucursalId, int $usuarioId, string $nombreUsuario): array{
        $db = traslado_inv::getDB();
        $transaccionIniciada = false;
        try{
            if($trasladoId <= 0 || $sucursalId <= 0 || $usuarioId <= 0 || trim($nombreUsuario) === '')
                throw new DomainException('Los datos necesarios para la recepcion no son validos.');

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la recepcion.');
            $transaccionIniciada = true;

            // La orden se bloquea antes de validar para impedir dos recepciones.
            $orden = traslado_inv::findForUpdate('id', $trasladoId);
            if(!$orden)throw new DomainException('La orden no existe.');
            if(!$this->policy->puedeRecibir($orden, $sucursalId))
                throw new DomainException('La orden no esta disponible para recepcion.');

            $sucursalRecepcionId = $this->policy->obtenerSucursalRecepcion($orden);
            $detalles = detalletrasladoinv::idregistrosForUpdate('id_trasladoinv', $trasladoId, false) ?? [];
            [$cantidadesProductos, $cantidadesInsumos] = $this->agruparCantidades($detalles);

            // Al recibir solo se exige que el stock este configurado; no se
            // necesita comprobar disponibilidad porque las unidades aumentan.
            $stocksProductos = $this->bloquearStocks(stockproductossucursal::class, 'productoid', $cantidadesProductos, $sucursalRecepcionId, 'producto', false);
            $stocksInsumos = $this->bloquearStocks(stockinsumossucursal::class, 'subproductoid', $cantidadesInsumos, $sucursalRecepcionId, 'insumo', false);

            // Los dos grupos quedaron validados antes del primer aumento.
            $this->aumentarProductos($stocksProductos, $cantidadesProductos, $sucursalRecepcionId, $usuarioId, trim($nombreUsuario));
            $this->aumentarInsumos($stocksInsumos, $cantidadesInsumos, $sucursalRecepcionId, $usuarioId, trim($nombreUsuario));
            $this->marcarDetallesRecibidos($detalles);

            $orden->estado = self::ESTADO_ENTREGADA;
            if(!$orden->actualizar())
                throw new RuntimeException('No fue posible actualizar la orden.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la recepcion.');

            return [
                'exito'=>['Orden procesada, mercancia recibida e ingresada a inventario'],
                'data'=>['id'=>(int)$orden->id, 'tipo'=>(string)$orden->tipo, 'estado'=>(string)$orden->estado],
            ];
        }catch(DomainException $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al recibir traslado '.$trasladoId.': '.$error->getMessage());
            return ['error'=>['Error al recibir la orden, intenta nuevamente.']];
        }
    }

    /**
     * Descuenta los articulos y cambia la orden a en transito.
     */
    public function despachar(int $trasladoId, int $sucursalId, int $usuarioId, string $nombreUsuario): array {
        $db = traslado_inv::getDB();
        $transaccionIniciada = false;
        try {
            /*
             * Validacion minima del contexto. La existencia del usuario queda
             * respaldada por la sesion y por las llaves foraneas de la BD.
             */
            if($trasladoId <= 0 || $sucursalId <= 0 || $usuarioId <= 0 || trim($nombreUsuario) === '')
                throw new DomainException('Los datos necesarios para el despacho no son validos.');

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar el despacho.');
            $transaccionIniciada = true;

            // Evita que la misma orden sea procesada simultaneamente.
            $orden = traslado_inv::findForUpdate('id', $trasladoId);
            if(!$orden)throw new DomainException('La orden no existe.');
            // La politica valida tipo, estado y sucursal de despacho.
            if(!$this->policy->puedeDespachar($orden, $sucursalId))
                throw new DomainException('La orden no esta disponible para despacho.');

            $sucursalDespachoId = $this->policy->obtenerSucursalDespacho($orden);
            $detalles = detalletrasladoinv::idregistrosForUpdate('id_trasladoinv', $trasladoId, false) ?? [];

            [$cantidadesProductos, $cantidadesInsumos] = $this->agruparCantidades($detalles);  //agruparCantidades me devuelve un array con dos arrays, uno de productos y otro de insumos, cada uno con el id del articulo y la cantidad a despachar ejemplo: [1 => 5, 2 => 10] para productos y [3 => 2, 4 => 8] para insumos

            /*
             * La existencia de cada articulo se comprueba indirectamente por
             * su configuracion de stock y las llaves foraneas de la BD.
             */
            $stocksProductos = $this->bloquearStocks(stockproductossucursal::class, 'productoid', $cantidadesProductos, $sucursalDespachoId, 'producto');
            $stocksInsumos = $this->bloquearStocks(stockinsumossucursal::class, 'subproductoid', $cantidadesInsumos, $sucursalDespachoId, 'insumo');
            // Todo el stock se valida antes de realizar el primer descuento.
            $this->descontarProductos($stocksProductos, $cantidadesProductos, $sucursalDespachoId, $usuarioId, trim($nombreUsuario));
            $this->descontarInsumos($stocksInsumos, $cantidadesInsumos, $sucursalDespachoId, $usuarioId, trim($nombreUsuario));

            $orden->estado = self::ESTADO_EN_TRANSITO;
            if(!$orden->actualizar())
                throw new RuntimeException('No fue posible actualizar la orden.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar el despacho.');

            return [
                'exito' => ['Orden procesada en transito e inventario descontado'],
                'data' => ['id' => (int) $orden->id, 'tipo' => (string) $orden->tipo, 'estado' => (string) $orden->estado, 'notificar_despacho' => true, 'orden' => $orden],
            ];
        }catch (DomainException $error) {
            if($transaccionIniciada)$db->rollback();

            return ['error' => [$error->getMessage()]];
        }catch(Throwable $error) {
            if($transaccionIniciada)$db->rollback();
            error_log('Error al despachar traslado '.$trasladoId.': '.$error->getMessage());
            return ['error' => ['Error al despachar la orden, intenta nuevamente.']];
        }

    }

    /**
     * Separa productos e insumos y acumula sus cantidades.
     *
     * @return array{
     *     0: array<int, float>,
     *     1: array<int, float>
     * }
     */
    private function agruparCantidades(array $detalles): array{
        if(!$detalles)
            throw new DomainException('La orden no contiene articulos para despachar.');

        $productos = [];
        $insumos = [];
        foreach($detalles as $detalle){
            $productoId = (int) ($detalle->fkproducto ?? 0);
            $insumoId = (int) ($detalle->idsubproducto_id ?? 0);
            $cantidad = (float) ($detalle->cantidad ?? 0);

            if($cantidad <= 0)throw new DomainException('Uno de los articulos tiene una cantidad invalida.');

            if($productoId > 0 && $insumoId <= 0){
                $productos[$productoId] = ($productos[$productoId] ?? 0) + $cantidad;
            }elseif($insumoId > 0 && $productoId <= 0) {
                $insumos[$insumoId] = ($insumos[$insumoId] ?? 0) + $cantidad;
            }else{
                throw new DomainException('Uno de los detalles de la orden no es valido.');
            }
        }

        return [$productos, $insumos];
    }

    /**
     * Consulta, bloquea y valida los stocks involucrados.
     *
     * @param class-string $modelo
     * @return array<int, object>
     */
    private function bloquearStocks(string $modelo, string $campoArticulo, array $cantidades, int $sucursalId, string $nombreArticulo, bool $validarDisponibilidad = true): array {
        if(!$cantidades)return [];

        $consultados = $modelo::IN_Where($campoArticulo, array_keys($cantidades), ['sucursalid', $sucursalId]);
        $idsStock = array_map(static fn(object $stock): int => (int) $stock->id, $consultados);
        $bloqueados = $modelo::findManyForUpdate($idsStock);  //obtengo un arreglo de objetos con los stocks bloqueados para cada articulo, ejemplo: [0 => {id: 1, stock: 10}, 1 => {id: 2, stock: 5}]

        $stocksPorArticulo = [];
        foreach($bloqueados as $stock){
            if((int) $stock->sucursalid !== $sucursalId)continue;
            $articuloId = (int) $stock->$campoArticulo;
            $stocksPorArticulo[$articuloId] = $stock; //obtengo un arreglo donde la clave es el id del articulo y el valor es el objeto stock, ejemplo: [1 => {id: 1, stock: 10}, 2 => {id: 2, stock: 5}]
        }

        foreach ($cantidades as $articuloId => $cantidad) {
            $objitem = $stocksPorArticulo[$articuloId] ?? null; //obtengo el objeto stock del articulo actual, si no existe devuelve null
            if(!$objitem)
                throw new DomainException("Un {$nombreArticulo} no tiene stock configurado.");
            if($validarDisponibilidad && (float) $objitem->stock < $cantidad)
                throw new DomainException("No hay stock suficiente para un {$nombreArticulo}."); 
        }

        return $stocksPorArticulo;

    }
    

    private function descontarProductos(array $stocks, array $cantidades, int $sucursalId, int $usuarioId, string $nombreUsuario): void {
        if(!$stocks)return;

        $movimientos = [];
        foreach($stocks as $productoId => $stock){
            $cantidad = $cantidades[$productoId];
            $stockAnterior = (float) $stock->stock;
            $stockNuevo = round($stockAnterior - $cantidad, 2);
            $stock->stock = $stockNuevo;

            $movimientos[] = new movimientos_productos([
                'idfksucursal' => $sucursalId,
                'idproducto_id' => $productoId,
                'id_usuarioid' => $usuarioId,
                'nombreusuario' => $nombreUsuario,
                'tipo' => self::TIPO_MOVIMIENTO,
                'referencia' => self::REFERENCIA_MOVIMIENTO,
                'cantidad' => $cantidad,
                'stockanterior' => $stockAnterior,
                'stocknuevo' => $stockNuevo,
                'comentario' => self::REFERENCIA_MOVIMIENTO,
            ]);
        }

        if(!stockproductossucursal::updatemultiregobj(array_values($stocks), ['stock']))
            throw new RuntimeException('No fue posible descontar los productos.');

        [$registrados] = (new movimientos_productos(['idfksucursal' => $sucursalId]))->crear_varios_reg_arrayobj($movimientos);

        if(!$registrados)throw new RuntimeException('No fue posible registrar los movimientos de productos.');

    }


    private function descontarInsumos(array $stocks, array $cantidades, int $sucursalId, int $usuarioId, string $nombreUsuario): void {
        if(!$stocks)return;
        
        $movimientos = [];
        foreach($stocks as $insumoId => $stock){
            $cantidad = $cantidades[$insumoId];
            $stockAnterior = (float) $stock->stock;
            $stockNuevo = round($stockAnterior - $cantidad, 2);
            $stock->stock = $stockNuevo;

            $movimientos[] = new movimientos_insumos([
                'fksucursal_id' => $sucursalId,
                'id_subproductoid' => $insumoId,
                'idusuario_id' => $usuarioId,
                'nombreusuario' => $nombreUsuario,
                'tipo' => self::TIPO_MOVIMIENTO,
                'referencia' => self::REFERENCIA_MOVIMIENTO,
                'cantidad' => $cantidad,
                'stockanterior' => $stockAnterior,
                'stocknuevo' => $stockNuevo,
                'comentario' => self::REFERENCIA_MOVIMIENTO,
            ]);
        }

        if(!stockinsumossucursal::updatemultiregobj(array_values($stocks), ['stock']))
            throw new RuntimeException('No fue posible descontar los insumos.');

        [$registrados] = (new movimientos_insumos(['fksucursal_id' => $sucursalId]))->crear_varios_reg_arrayobj($movimientos);

        if(!$registrados)throw new RuntimeException('No fue posible registrar los movimientos de insumos.');
    }


    private function aumentarProductos(array $stocks, array $cantidades, int $sucursalId, int $usuarioId, string $nombreUsuario): void{
        if(!$stocks)return;

        $movimientos = [];
        foreach($stocks as $productoId => $stock){
            $cantidad = $cantidades[$productoId];
            $stockAnterior = (float)$stock->stock;
            $stockNuevo = round($stockAnterior + $cantidad, 2);
            $stock->stock = $stockNuevo;

            $movimientos[] = new movimientos_productos([
                'idfksucursal'=>$sucursalId,
                'idproducto_id'=>$productoId,
                'id_usuarioid'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>self::TIPO_MOVIMIENTO_INGRESO,
                'referencia'=>self::REFERENCIA_MOVIMIENTO_INGRESO,
                'cantidad'=>$cantidad,
                'stockanterior'=>$stockAnterior,
                'stocknuevo'=>$stockNuevo,
                'comentario'=>self::REFERENCIA_MOVIMIENTO_INGRESO,
            ]);
        }

        if(!stockproductossucursal::updatemultiregobj(array_values($stocks), ['stock']))
            throw new RuntimeException('No fue posible aumentar los productos.');

        [$registrados] = (new movimientos_productos(['idfksucursal'=>$sucursalId]))->crear_varios_reg_arrayobj($movimientos);
        if(!$registrados)throw new RuntimeException('No fue posible registrar los movimientos de productos.');
    }


    private function aumentarInsumos(array $stocks, array $cantidades, int $sucursalId, int $usuarioId, string $nombreUsuario): void{
        if(!$stocks)return;

        $movimientos = [];
        foreach($stocks as $insumoId => $stock){
            $cantidad = $cantidades[$insumoId];
            $stockAnterior = (float)$stock->stock;
            $stockNuevo = round($stockAnterior + $cantidad, 2);
            $stock->stock = $stockNuevo;

            $movimientos[] = new movimientos_insumos([
                'fksucursal_id'=>$sucursalId,
                'id_subproductoid'=>$insumoId,
                'idusuario_id'=>$usuarioId,
                'nombreusuario'=>$nombreUsuario,
                'tipo'=>self::TIPO_MOVIMIENTO_INGRESO,
                'referencia'=>self::REFERENCIA_MOVIMIENTO_INGRESO,
                'cantidad'=>$cantidad,
                'stockanterior'=>$stockAnterior,
                'stocknuevo'=>$stockNuevo,
                'comentario'=>self::REFERENCIA_MOVIMIENTO_INGRESO,
            ]);
        }

        if(!stockinsumossucursal::updatemultiregobj(array_values($stocks), ['stock']))
            throw new RuntimeException('No fue posible aumentar los insumos.');

        [$registrados] = (new movimientos_insumos(['fksucursal_id'=>$sucursalId]))->crear_varios_reg_arrayobj($movimientos);
        if(!$registrados)throw new RuntimeException('No fue posible registrar los movimientos de insumos.');
    }


    /** Registra la recepcion total de cada detalle de la orden. */
    private function marcarDetallesRecibidos(array $detalles): void{
        foreach($detalles as $detalle){
            $detalle->cantidadrecibida = (float)$detalle->cantidad;
            $detalle->cantidadrechazada = 0;
        }

        if(!detalletrasladoinv::updatemultiregobj($detalles, ['cantidadrecibida', 'cantidadrechazada']))
            throw new RuntimeException('No fue posible actualizar las cantidades recibidas.');
    }

}