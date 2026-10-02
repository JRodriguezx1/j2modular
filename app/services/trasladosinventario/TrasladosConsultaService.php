<?php

namespace App\services\trasladosinventario;

use App\Models\configuraciones\usuarios;
use App\Models\inventario\detalletrasladoinv;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\traslado_inv;
use App\Models\inventario\unidadesmedida;
use App\Models\sucursales;
use App\Policies\TrasladoPolicy;

/**
 * Centraliza las consultas de traslados de inventario.
 *
 * Este servicio solo lee informacion: no cambia estados, no modifica stock y
 * no inicia transacciones de escritura. Todas las lecturas se realizan mediante
 * los metodos heredados de ActiveRecord; el servicio se limita a componer los
 * objetos que consumen las vistas y el frontend.
 */
final class TrasladosConsultaService{

    /** Estados que consumen actualmente los indicadores de las vistas. */
    private const ESTADOS_CONTADORES = [
        'pendiente',
        'aprobada',
        'rechazada',
        'entregada',
        'entransito',
    ];

    private TrasladoPolicy $policy;

    public function __construct(?TrasladoPolicy $policy = null){
        $this->policy = $policy ?? new TrasladoPolicy();
    }

    /**
     * Lista las ordenes en las que la sucursal actual es el destino
     * administrativo.
     *
     * En una Solicitud la sucursal actual debe despachar; en una Salida debe
     * recibir. La interpretacion operativa se mantiene fuera de esta consulta.
     *
     * @return array{ordenes: object[], contadores: array<string, int>}
     */
    public function listarComoDestino(int $sucursalId): array{
        return $this->listarPorSucursal('id_sucursaldestino', $sucursalId);
    }

    /**
     * Lista las ordenes iniciadas por la sucursal actual.
     *
     * @return array{ordenes: object[], contadores: array<string, int>}
     */
    public function listarComoOrigen(int $sucursalId): array{
        return $this->listarPorSucursal('id_sucursalorigen', $sucursalId);
    }

    /**
     * Obtiene la cabecera y los items de una orden visible para la sucursal.
     *
     * La politica se aplica antes de cargar usuarios, sucursales y detalles.
     * Retorna null cuando el ID es invalido, la orden no existe o la sucursal no
     * participa en ella.
     */
    public function obtenerDetalle(int $trasladoId, int $sucursalId): ?object{
        if($trasladoId <= 0 || $sucursalId <= 0)return null;

        $orden = traslado_inv::find('id', $trasladoId);
        if(!$orden || !$this->policy->esParticipante($orden, $sucursalId))return null;

        // como $orden dentro de hidratarCabeceras apunta a la misma instancia, no es necesario retornar el objeto.
        $this->hidratarCabeceras([$orden]);

        // Se conserva el nombre usado por el frontend actual para que la futura
        // adaptacion del controlador no necesite transformar los detalles.
        $orden->detalletrasladoinv = $this->consultarDetalles($trasladoId);
        return $orden;
    }

    /**
     * Obtiene una orden que la sucursal actual puede abrir para edicion.
     *
     * TrasladoPolicy exige estado pendiente, tipo valido y que la sucursal sea
     * el origen administrativo de la orden.
     */
    public function obtenerParaEditar(int $trasladoId, int $sucursalId): ?object{
        if($trasladoId <= 0 || $sucursalId <= 0)return null;
        $orden = traslado_inv::find('id', $trasladoId);
        if(!$orden || !$this->policy->puedeEditar($orden, $sucursalId))return null;
        $this->hidratarCabeceras([$orden]);
        return $orden;
    }

    /**
     * Ejecuta el listado comun cambiando solamente el lado de la relacion.
     *
     * $campoSucursal no proviene del usuario: los dos metodos publicos pasan
     * valores constantes. La lista permitida evita que un uso futuro entregue
     * una columna arbitraria al metodo generico de ActiveRecord.
     *
     * @return array{ordenes: object[], contadores: array<string, int>}
     */
    private function listarPorSucursal(string $campoSucursal, int $sucursalId): array{
        if($sucursalId <= 0)return $this->resultadoListadoVacio();
        if(!in_array($campoSucursal, ['id_sucursalorigen', 'id_sucursaldestino'], true))
            return $this->resultadoListadoVacio();

        $ordenes = traslado_inv::idregistros($campoSucursal, $sucursalId) ?? [];

        // idregistros no define un orden explicito. Ordenar en memoria produce
        // una salida estable sin introducir SQL en el servicio.
        usort($ordenes, static fn(object $a, object $b):int => (int)$a->id <=> (int)$b->id);

        $this->hidratarCabeceras($ordenes);
        return ['ordenes'=>$ordenes, 'count'=>$this->contarEstados($ordenes)];
    }

    /**
     * Añade nombres de usuario y sucursales a las ordenes ya consultadas.
     *
     * Los IDs se agrupan y se consultan con IN_Where para mantener una cantidad
     * fija de consultas. Esto evita tanto el N+1 como cargar todos los usuarios
     * del sistema mediante usuarios::all().
     *
     * @param object[] $ordenes
     */
    private function hidratarCabeceras(array $ordenes): void{
        if(!$ordenes)return;

        $idsUsuarios = [];
        $idsSucursales = [];
        foreach($ordenes as $orden){
            $usuarioId = (int)($orden->fkusuario ?? 0);
            $origenId = (int)($orden->id_sucursalorigen ?? 0);
            $destinoId = (int)($orden->id_sucursaldestino ?? 0);
            if($usuarioId > 0)$idsUsuarios[] = $usuarioId;
            if($origenId > 0)$idsSucursales[] = $origenId;
            if($destinoId > 0)$idsSucursales[] = $destinoId;
        }

        $idsUsuarios = array_values(array_unique($idsUsuarios));
        $idsSucursales = array_values(array_unique($idsSucursales));

        $usuariosPorId = $this->indexarPorId( $idsUsuarios ? usuarios::IN_Where('id', $idsUsuarios) : []);
        $sucursalesPorId = $this->indexarPorId($idsSucursales ? sucursales::IN_Where('id', $idsSucursales) : []);

        foreach($ordenes as $orden){
            $usuario = $usuariosPorId[(int)($orden->fkusuario ?? 0)] ?? null;
            $sucursalOrigen = $sucursalesPorId[(int)$orden->id_sucursalorigen] ?? null;
            $sucursalDestino = $sucursalesPorId[(int)$orden->id_sucursaldestino] ?? null;

            // Si el usuario fue eliminado, fkusuario puede ser NULL. En ese
            // caso se conserva la orden y se presenta un nombre vacio.
            $nombreUsuario = $usuario ? trim((string)$usuario->nombre.' '.(string)$usuario->apellido) : '';
            $nombreOrigen = (string)($sucursalOrigen->nombre ?? '');
            $nombreDestino = (string)($sucursalDestino->nombre ?? '');

            // Se mantienen ambos estilos de alias porque las vistas de listado
            // y las pantallas de detalle usan nombres diferentes actualmente.
            $orden->usuario = $nombreUsuario;
            $orden->nombreusuario = $nombreUsuario;
            $orden->sucursalorigen = $nombreOrigen;
            $orden->sucursaldestino = $nombreDestino;
            $orden->sucursal_origen = $nombreOrigen;
            $orden->sucursal_destino = $nombreDestino;
        }
    }

    /**
     * Carga los detalles y resuelve productos, insumos y unidades por lotes.
     *
     * @return object[]
     */
    private function consultarDetalles(int $trasladoId): array{
        $detalles = detalletrasladoinv::idregistros('id_trasladoinv', $trasladoId) ?? [];
        usort($detalles, static fn(object $a, object $b):int => (int)$a->id <=> (int)$b->id);
        if(!$detalles)return [];

        $idsProductos = [];
        $idsInsumos = [];
        foreach($detalles as $detalle){
            $productoId = (int)($detalle->fkproducto ?? 0);
            $insumoId = (int)($detalle->idsubproducto_id ?? 0);
            if($productoId > 0)$idsProductos[] = $productoId;
            if($insumoId > 0)$idsInsumos[] = $insumoId;
        }

        $idsProductos = array_values(array_unique($idsProductos));
        $idsInsumos = array_values(array_unique($idsInsumos));
        $productosPorId = $this->indexarPorId($idsProductos ? productos::IN_Where('id', $idsProductos) : []);
        $insumosPorId = $this->indexarPorId($idsInsumos ? subproductos::IN_Where('id', $idsInsumos) : []);

        // Las unidades tambien se consultan una sola vez, independientemente
        // de la cantidad de items que contenga la orden.
        $idsUnidades = [];
        foreach($productosPorId as $producto){
            $unidadId = (int)($producto->idunidadmedida ?? 0);
            if($unidadId > 0)$idsUnidades[] = $unidadId;
        }
        foreach($insumosPorId as $insumo){
            $unidadId = (int)($insumo->id_unidadmedida ?? 0);
            if($unidadId > 0)$idsUnidades[] = $unidadId;
        }
        $idsUnidades = array_values(array_unique($idsUnidades));
        $unidadesPorId = $this->indexarPorId($idsUnidades ? unidadesmedida::IN_Where('id', $idsUnidades) : []);

        foreach($detalles as $detalle){
            $producto = $productosPorId[(int)($detalle->fkproducto ?? 0)] ?? null;
            $insumo = $insumosPorId[(int)($detalle->idsubproducto_id ?? 0)] ?? null;

            if($producto){
                $unidad = $unidadesPorId[(int)$producto->idunidadmedida] ?? null;
                $detalle->nombre = (string)$producto->nombre;
                $detalle->unidadmedida = (string)($unidad->nombre ?? $producto->unidadmedida ?? '');
                continue;
            }
            if($insumo){
                $unidad = $unidadesPorId[(int)$insumo->id_unidadmedida] ?? null;
                $detalle->nombre = (string)$insumo->nombre;
                $detalle->unidadmedida = (string)($unidad->nombre ?? $insumo->unidadmedida ?? '');
                continue;
            }

            // Las llaves foraneas permiten NULL cuando se elimina un articulo;
            // el detalle historico continua disponible aunque no tenga nombre.
            $detalle->nombre = '';
            $detalle->unidadmedida = '';
        }
        return $detalles;
    }

    /**
     * Convierte una lista de modelos en un mapa [id => modelo].
     *
     * @param object[] $registros
     * @return array<int, object>
     */
    private function indexarPorId(array $registros): array{
        $indexados = [];
        foreach($registros as $registro){
            $id = (int)($registro->id ?? 0);
            if($id > 0)$indexados[$id] = $registro;
        }
        return $indexados;
    }

    /** @param object[] $ordenes */
    private function contarEstados(array $ordenes): array{
        $contadores = array_fill_keys(self::ESTADOS_CONTADORES, 0);
        foreach($ordenes as $orden){
            $estado = (string)($orden->estado ?? '');
            if(array_key_exists($estado, $contadores))$contadores[$estado]++;
        }
        return $contadores;
    }

    /** @return array{ordenes: array, contadores: array<string, int>} */
    private function resultadoListadoVacio(): array{
        return ['ordenes'=>[], 'count'=>array_fill_keys(self::ESTADOS_CONTADORES, 0)];
    }

}
