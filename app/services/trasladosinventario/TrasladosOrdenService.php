<?php

namespace App\services\trasladosinventario;

use App\Models\configuraciones\usuarios;
use App\Models\inventario\detalletrasladoinv;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\traslado_inv;
use App\Models\sucursales;
use App\Policies\TrasladoPolicy;
use DomainException;
use RuntimeException;
use Throwable;

/**
 * Crea y edita ordenes de traslado sin modificar existencias.
 *
 * Las operaciones que afectan la cabecera y sus detalles se ejecutan dentro de
 * una sola transaccion. Este servicio no lee variables HTTP ni datos de sesion:
 * el controlador debe entregarle una entrada normalizada y el contexto actual.
 */
final class TrasladosOrdenService{

    private const TIPO_SOLICITUD = 'Solicitud';
    private const TIPO_SALIDA = 'Salida';
    private const ESTADO_PENDIENTE = 'pendiente';
    private const LONGITUD_MAXIMA_OBSERVACION = 512;

    private TrasladoPolicy $policy;

    public function __construct(?TrasladoPolicy $policy = null){
        $this->policy = $policy ?? new TrasladoPolicy();
    }

    /**
     * Crea una solicitud iniciada por la sucursal actual.
     *
     * @param array{
     *   sucursal_destino_id:mixed,
     *   observacion?:mixed,
     *   items:mixed
     * } $datos
     */
    public function crearSolicitud(array $datos, int $sucursalId, int $usuarioId): array{
        return $this->crearOrden($datos,$sucursalId, $usuarioId, self::TIPO_SOLICITUD, 'Solicitud de mercancia enviada correctamente');
    }

    /**
     * Crea una salida iniciada por la sucursal actual.
     *
     * @param array{
     *   sucursal_destino_id:mixed,
     *   observacion?:mixed,
     *   items:mixed
     * } $datos
     */
    public function crearSalida(array $datos, int $sucursalId, int $usuarioId): array{
        return $this->crearOrden($datos, $sucursalId, $usuarioId, self::TIPO_SALIDA, 'Solicitud de transferencia enviada correctamente');
    }

    /**
     * Sincroniza la lista final de items de una orden pendiente.
     *
     * Los identificadores de detalle enviados por el navegador no se utilizan.
     * Cada fila se relaciona mediante su tipo e ID de articulo y luego se cruza
     * con los detalles bloqueados que realmente pertenecen a la orden.
     *
     * `sucursal_destino_id` y `observacion` son opcionales durante la migracion:
     * si el formulario actual no los envia, se conservan sus valores en la BD.
     *
     * @param array{
     *   sucursal_destino_id?:mixed,
     *   observacion?:mixed,
     *   items:mixed
     * } $datos
     */
    public function editar(int $trasladoId, array $datos, int $sucursalId, int $usuarioId): array{
        $db = traslado_inv::getDB();
        $transaccionIniciada = false;

        try{
            $this->validarIdPositivo($trasladoId, 'El identificador de la orden no es valido.');
            $this->validarIdPositivo($sucursalId, 'La sucursal actual no es valida.');
            $this->validarUsuario($usuarioId);
            $items = $this->normalizarItems($datos['items'] ?? null);
            $this->validarArticulosExistentes($items);

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la edicion de la orden.');
            $transaccionIniciada = true;

            // La cabecera se bloquea antes de comprobar estado y autorizacion
            // para que dos peticiones no editen la misma orden simultaneamente.
            $orden = traslado_inv::findForUpdate('id', $trasladoId);
            if(!$orden)throw new DomainException('La orden no existe.');
            if(!$this->policy->puedeEditar($orden, $sucursalId))
                throw new DomainException('La orden no esta disponible para edicion.');

            $destinoId = array_key_exists('sucursal_destino_id', $datos) ? $this->obtenerIdPositivo($datos['sucursal_destino_id'], 'La sucursal destino no es valida.') : (int)$orden->id_sucursaldestino;
            $observacion = array_key_exists('observacion', $datos) ? $this->normalizarObservacion($datos['observacion']) : (string)($orden->observacion ?? '');

            $this->validarSucursales($sucursalId, $destinoId);

            // Los detalles se bloquean y se sincronizan como un conjunto final:
            // lo ausente se elimina, lo existente se actualiza y lo nuevo se crea.
            $detallesActuales = detalletrasladoinv::idregistrosForUpdate('id_trasladoinv', $trasladoId, false) ?? [];
            [$idsEliminar, $detallesActualizar, $detallesCrear] =
                $this->prepararSincronizacion($trasladoId, $items, $detallesActuales);

            if($idsEliminar && !detalletrasladoinv::eliminar_idregistros('id', $idsEliminar))
                throw new RuntimeException('No fue posible retirar detalles de la orden.');
            if($detallesActualizar && !detalletrasladoinv::updatemultiregobj($detallesActualizar, ['cantidad']))
                throw new RuntimeException('No fue posible actualizar los detalles de la orden.');
            if($detallesCrear){
                [$creados] = (new detalletrasladoinv())->crear_varios_reg_arrayobj($detallesCrear);
                if(!$creados)throw new RuntimeException('No fue posible agregar detalles a la orden.');
            }

            if($destinoId !== (int)$orden->id_sucursaldestino || $observacion !== (string)($orden->observacion ?? '')){
                $orden->id_sucursaldestino = $destinoId;
                $orden->observacion = $observacion;
                if(!$orden->actualizar())throw new RuntimeException('No fue posible actualizar la cabecera de la orden.');
            }

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la edicion de la orden.');
            return [
                'exito'=>["{$orden->tipo} de transferencia actualizada correctamente"],
                'data'=>['id'=>(int)$orden->id, 'tipo'=>(string)$orden->tipo, 'estado'=>(string)$orden->estado],
            ];
        }catch(DomainException $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al editar traslado de inventario '.$trasladoId.': '.$error->getMessage());
            return ['error'=>['Error al actualizar la orden, intenta nuevamente.']];
        }
    }

    /** Cancela una orden pendiente cuando la sucursal actual es su origen. */
    public function cancelar(int $trasladoId, int $sucursalId, int $usuarioId): array{
        return $this->cambiarEstadoARechazada($trasladoId, $sucursalId, $usuarioId, 'cancelar');
    }

    /** Rechaza una orden pendiente cuando la sucursal actual es su destino. */
    public function rechazar(int $trasladoId, int $sucursalId, int $usuarioId): array{
        return $this->cambiarEstadoARechazada($trasladoId, $sucursalId, $usuarioId, 'rechazar');
    }

    /**
     * Resuelve la accion de la ruta heredada que comparten ambas pantallas.
     *
     * La sucursal origen cancela y la sucursal destino rechaza. La decision se
     * toma despues de bloquear la orden y nunca depende de un valor del cliente.
     */
    public function cancelarORechazar(int $trasladoId, int $sucursalId, int $usuarioId): array{
        return $this->cambiarEstadoARechazada($trasladoId, $sucursalId, $usuarioId, null);
    }

    /**
     * Cambia solamente el estado; conserva la cabecera, sus detalles y el stock.
     */
    private function cambiarEstadoARechazada(int $trasladoId, int $sucursalId, int $usuarioId, ?string $accion): array{
        $db = traslado_inv::getDB();
        $transaccionIniciada = false;
        try{
            $this->validarIdPositivo($trasladoId, 'El identificador de la orden no es valido.');
            $this->validarIdPositivo($sucursalId, 'La sucursal actual no es valida.');
            $this->validarUsuario($usuarioId);

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la anulacion de la orden.');
            $transaccionIniciada = true;

            // El bloqueo evita que la orden sea despachada, editada o anulada
            // simultaneamente por dos peticiones diferentes.
            $orden = traslado_inv::findForUpdate('id', $trasladoId);
            if(!$orden)throw new DomainException('La orden no existe.');

            $puedeCancelar = $this->policy->puedeCancelar($orden, $sucursalId);
            $puedeRechazar = $this->policy->puedeRechazar($orden, $sucursalId);

            if($accion === 'cancelar' && !$puedeCancelar)throw new DomainException('La orden no esta disponible para cancelacion.');
            if($accion === 'rechazar' && !$puedeRechazar)throw new DomainException('La orden no esta disponible para rechazo.');

            if($accion === null){
                if($puedeCancelar)$accion = 'cancelar';
                elseif($puedeRechazar)$accion = 'rechazar';
                else throw new DomainException('La orden no esta disponible para anulacion.');
            }

            $orden->estado = 'rechazada';
            if(!$orden->actualizar())throw new RuntimeException('No fue posible cambiar el estado de la orden.');
            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la anulacion de la orden.');

            $mensaje = $accion === 'cancelar' ? "{$orden->tipo} cancelada correctamente" : "{$orden->tipo} rechazada correctamente";
            return [
                'exito'=>[$mensaje],
                'data'=>['id'=>(int)$orden->id, 'tipo'=>(string)$orden->tipo, 'estado'=>(string)$orden->estado],
            ];
        }catch(DomainException $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al anular traslado de inventario '.$trasladoId.': '.$error->getMessage());
            return ['error'=>['Error al anular la orden, intenta nuevamente.']];
        }

    }

    /** Crea la cabecera y todos sus detalles de forma atomica. */
    private function crearOrden(array $datos, int $sucursalId, int $usuarioId, string $tipo, string $mensajeExito): array{
        $db = traslado_inv::getDB();
        $transaccionIniciada = false;

        try{
            $this->validarIdPositivo($sucursalId, 'La sucursal origen no es valida.');
            $this->validarUsuario($usuarioId);
            $destinoId = $this->obtenerIdPositivo($datos['sucursal_destino_id'] ?? null, 'La sucursal destino no es valida.');
            $this->validarSucursales($sucursalId, $destinoId);
            $observacion = $this->normalizarObservacion($datos['observacion'] ?? '');
            $items = $this->normalizarItems($datos['items'] ?? null);
            $this->validarArticulosExistentes($items);

            if(!$db->begin_transaction())
                throw new RuntimeException('No fue posible iniciar la creacion de la orden.');
            $transaccionIniciada = true;

            // Origen, usuario, tipo y estado proceden del contexto confiable; no
            // pueden ser sustituidos por valores enviados desde el navegador.
            $orden = new traslado_inv([
                'id_sucursalorigen'=>$sucursalId,
                'id_sucursaldestino'=>$destinoId,
                'fkusuario'=>$usuarioId,
                'tipo'=>$tipo,
                'observacion'=>$observacion,
                'estado'=>self::ESTADO_PENDIENTE,
            ]);
            [$creada, $trasladoId] = $orden->crear_guardar();
            if(!$creada)throw new RuntimeException('No fue posible crear la cabecera de la orden.');

            $detalles = [];
            foreach($items as $item)$detalles[] = $this->crearDetalle((int)$trasladoId, $item);
            [$detallesCreados] = (new detalletrasladoinv())->crear_varios_reg_arrayobj($detalles);
            if(!$detallesCreados)throw new RuntimeException('No fue posible crear los detalles de la orden.');

            if(!$db->commit())throw new RuntimeException('No fue posible confirmar la creacion de la orden.');
            return [
                'exito'=>[$mensajeExito],
                'data'=>['id'=>(int)$trasladoId, 'tipo'=>$tipo, 'estado'=>self::ESTADO_PENDIENTE],
            ];
        }catch(DomainException $error){
            if($transaccionIniciada)$db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            if($transaccionIniciada)$db->rollback();
            error_log('Error al crear traslado de inventario: '.$error->getMessage());
            return ['error'=>['Error al crear la orden, intenta nuevamente.']];
        }
    }

    /**
     * Normaliza la lista con las validaciones minimas para poder guardarla.
     * Si un articulo viene repetido, se conserva la ultima cantidad recibida.
     *
     * @return array<int, array{tipo:string, item_id:int, cantidad:float}>
     */
    private function normalizarItems(mixed $items): array{
        if(!is_array($items) || !$items)throw new DomainException('La orden debe contener al menos un articulo.');

        $normalizados = [];
        foreach($items as $item){
            if(is_object($item))$item = get_object_vars($item);
            if(!is_array($item))throw new DomainException('Uno de los articulos no es valido.');

            $tipoRecibido = strtolower(trim((string)($item['tipo'] ?? '')));
            if($tipoRecibido === '0' || $tipoRecibido === 'producto')$tipo = 'producto';
            elseif(in_array($tipoRecibido, ['1', 'insumo', 'subproducto'], true))$tipo = 'insumo';
            else throw new DomainException('El tipo de uno de los articulos no es valido.');

            $itemId = $item['item_id'] ?? $item['iditem'] ?? null;
            $cantidad = $item['cantidad'] ?? null;
            if(!is_numeric($itemId) || (int)$itemId <= 0)
                throw new DomainException('Uno de los articulos no tiene un identificador valido.');
            if(!is_numeric($cantidad) || !is_finite((float)$cantidad) || (float)$cantidad <= 0)
                throw new DomainException('La cantidad de cada articulo debe ser mayor que cero.');

            $itemId = (int)$itemId;
            $cantidad = (float)$cantidad;
            $clave = $tipo.':'.$itemId;
            $normalizados[$clave] = ['tipo'=>$tipo, 'item_id'=>$itemId, 'cantidad'=>$cantidad];
        }
        return array_values($normalizados);
    }

    private function normalizarObservacion(mixed $observacion): string{
        if(!is_scalar($observacion) && $observacion !== null)
            throw new DomainException('La observacion no es valida.');
        $observacion = trim((string)$observacion);
        $longitud = function_exists('mb_strlen') ? mb_strlen($observacion) : strlen($observacion);
        if($longitud > self::LONGITUD_MAXIMA_OBSERVACION)
            throw new DomainException('La observacion no puede superar 512 caracteres.');
        return $observacion;
    }

    private function validarSucursales(int $origenId, int $destinoId): void{
        if($origenId === $destinoId)throw new DomainException('La sucursal destino debe ser diferente de la sucursal origen.');

        $sucursales = sucursales::IN_Where('id', [$origenId, $destinoId]);
        $idsEncontrados = array_map(static fn(object $sucursal):int => (int)$sucursal->id, $sucursales);
        if(!in_array($origenId, $idsEncontrados, true))
            throw new DomainException('La sucursal origen no existe.');
        if(!in_array($destinoId, $idsEncontrados, true))
            throw new DomainException('La sucursal destino no existe.');
    }

    private function validarUsuario(int $usuarioId): void{
        if(!usuarios::find('id', $usuarioId))throw new DomainException('El usuario actual no existe.');
    }

    /** Valida en lotes las llaves foraneas antes de iniciar una escritura. */
    private function validarArticulosExistentes(array $items): void{
        $idsProductos = [];
        $idsInsumos = [];
        foreach($items as $item){
            if($item['tipo'] === 'producto')$idsProductos[] = $item['item_id'];
            else $idsInsumos[] = $item['item_id'];
        }

        $productosEncontrados = productos::IN_Where('id', array_values(array_unique($idsProductos)));
        $insumosEncontrados = subproductos::IN_Where('id', array_values(array_unique($idsInsumos)));
        $idsProductosEncontrados = array_map(static fn(object $item):int => (int)$item->id, $productosEncontrados);
        $idsInsumosEncontrados = array_map(static fn(object $item):int => (int)$item->id, $insumosEncontrados);

        foreach($idsProductos as $id)
            if(!in_array($id, $idsProductosEncontrados, true))throw new DomainException('Uno de los productos no existe.');
        foreach($idsInsumos as $id)
            if(!in_array($id, $idsInsumosEncontrados, true))throw new DomainException('Uno de los insumos no existe.');
    }

    /**
     * Calcula las tres operaciones necesarias para reflejar la lista final.
     *
     * @param object[] $detallesActuales
     * @return array{0:int[], 1:object[], 2:detalletrasladoinv[]}
     */
    private function prepararSincronizacion(int $trasladoId, array $items, array $detallesActuales): array{
        $actualesPorArticulo = [];
        foreach($detallesActuales as $detalle){
            $clave = $this->obtenerClaveDetalle($detalle);
            if($clave !== null)$actualesPorArticulo[$clave] = $detalle; //mapea cada item db actual por su tipo e id de articulo, donde la clave es producto o insumo y el id del articulo
        }

        $conservados = [];
        $actualizar = [];
        $crear = [];
        foreach($items as $item){
            $clave = $item['tipo'].':'.$item['item_id'];
            $detalleActual = $actualesPorArticulo[$clave] ?? null;
            if(!$detalleActual){
                $crear[] = $this->crearDetalle($trasladoId, $item); //item nuevo.
                continue;
            }

            $conservados[(int)$detalleActual->id] = true;
            if((float)$detalleActual->cantidad !== $item['cantidad']){
                // Tanto el ID como la pertenencia del detalle provienen de las
                // filas bloqueadas en la BD, nunca del navegador.
                $detalleActual->id = (int)$detalleActual->id;
                $detalleActual->cantidad = $item['cantidad'];
                $actualizar[] = $detalleActual;
            }
        }

        $eliminar = [];
        foreach($detallesActuales as $detalle){
            $detalleId = (int)$detalle->id;
            if(!isset($conservados[$detalleId]))$eliminar[] = $detalleId;
        }
        return [$eliminar, $actualizar, $crear];
    }

    /** @param array{tipo:string, item_id:int, cantidad:float} $item */
    private function crearDetalle(int $trasladoId, array $item): detalletrasladoinv{
        return new detalletrasladoinv([
            'id_trasladoinv'=>$trasladoId,
            // ActiveRecord reconoce el literal NULL al construir inserciones
            // multiples; cada detalle debe apuntar solo a uno de los dos tipos.
            'fkproducto'=>$item['tipo'] === 'producto' ? $item['item_id'] : 'NULL',
            'idsubproducto_id'=>$item['tipo'] === 'insumo' ? $item['item_id'] : 'NULL',
            'cantidad'=>$item['cantidad'],
            'cantidadrecibida'=>0,
            'cantidadrechazada'=>0,
        ]);
    }

    private function obtenerClaveDetalle(object $detalle): ?string{
        $productoId = (int)($detalle->fkproducto ?? 0);
        $insumoId = (int)($detalle->idsubproducto_id ?? 0);
        if($productoId > 0 && $insumoId <= 0)return 'producto:'.$productoId;
        if($insumoId > 0 && $productoId <= 0)return 'insumo:'.$insumoId;
        return null;
    }

    private function obtenerIdPositivo(mixed $valor, string $mensaje): int{
        if(is_int($valor))$id = $valor;
        elseif(is_string($valor) && preg_match('/^\d+$/', $valor))$id = (int)$valor;
        else throw new DomainException($mensaje);
        $this->validarIdPositivo($id, $mensaje);
        return $id;
    }

    private function validarIdPositivo(int $id, string $mensaje): void{
        if($id <= 0)throw new DomainException($mensaje);
    }

}