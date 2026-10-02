<?php

namespace App\services;

use App\classes\Traits\DocumentTrait;
use App\Models\caja\cierrescajas;
use App\Models\caja\factmediospago;
use App\Models\clientes\clientes;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\factimpuestos;
use App\Models\felectronicas\adquirientes;
use App\Models\parametrizacion\config_local;
use App\Models\ventas\facturas;
use App\Models\ventas\ventas;
use App\Models\sucursales;
use App\Models\configuraciones\emisores;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Throwable;

/**
 * Coordina el caso de uso completo de facturacion del modulo de ventas.
 *
 * Su responsabilidad s decidir el flujo que corresponde y hacer que factura,
 * caja, credito, detalle, inventario, movimiento contable y comision
 * participen en la misma operacion transaccional.
 */
class facturacionService
{
    use DocumentTrait;
    private ?comisionesService $comisionServicio;
    private ?contableService $contableService;
    /** Datos y objetos normalizados de una solicitud. */
    private array $datos = [];
    private int $sucursalId;
    private string $estado;
    private string $tipoVenta;
    private array $carrito = [];
    private array $mediosPago = [];
    private array $impuestos = [];
    private stdClass $valoresCredito;
    private stdClass $datosAdquiriente;
    private facturas $facturaSolicitud;
    private array $parametros;
    private array $itemsStockMinimo = [];
    private array $inventarioVenta = [];
    private array $lineasActualizar = [];
    private array $lineasInsertar = [];
    private ?facturas $ordenOrigen = null;
    private int $devolverinv = 0;
    private float $puntos = 0; 
    /** Indica que las lineas deben obtenerse de la orden guardada, no del POST. */
    private bool $usarDetalleOrdenPersistido = false;

    /**
     * Permite sustituir colaboradores en pruebas. Los servicios no inyectados
     * se crean de forma diferida cuando el flujo realmente los necesita.
     */
    public function __construct(?comisionesService $comisionServicio = null, ?contableService $contableService = null){
        $this->comisionServicio = $comisionServicio;
        $this->contableService = $contableService;
        $this->parametros = config_local::getParamCaja();
    }

    /**
     * Punto de entrada llamado por ventascontrolador::facturar().
     * Normaliza la solicitud, valida sus reglas y selecciona uno de estos
     * caminos: editar una orden, crear una orden o crear una factura pagada.
     */
    public function procesar(array $datos, int $sucursalId):array{
        date_default_timezone_set('America/Bogota');
        try {
            $this->inicializarSolicitud($datos, $sucursalId);
            $alertasFactura = $this->facturaSolicitud->validar_nueva_factura();
            if(!empty($alertasFactura['error']))return $alertasFactura;

            $this->separarLineasOrden();
            $this->ordenOrigen = $this->buscarOrdenOrigen(); //devuelve la orden guardada (cotizacion/remision)

            //prepara items para mover y descontar de inventario
            $erroresStock = $this->prepararYValidarInventario();
            if(!empty($erroresStock))return ['error'=>$erroresStock];

            //contizacion/remision
            if($this->ordenOrigen && $this->estado !== 'Paga')
                return $this->editarOrdenExistente();

            //procesar factura o crear nueva cotizacion/remision o redimido
            return $this->procesarEnTransaccion();
        }catch(InvalidArgumentException $th){
            return ['error'=>[$th->getMessage()]];
        }catch(Throwable $th){
            return ['error'=>['Error al procesar la solicitud. '.$th->getMessage()]];
        }
    }


    public function facturarCotizacionExistente(array $datos, int $sucursalId):array{
        date_default_timezone_set('America/Bogota');
        try {
            // Este endpoint solo convierte una orden existente en una venta de
            // contado. Los discriminadores no quedan a criterio del navegador.
            $datos = $this->normalizarPagoCotizacionExistente($datos);
            $this->getProductosEImpuestosCotizacion($datos, $sucursalId);
            //prepara items para mover y descontar de inventario
            $erroresStock = $this->prepararYValidarInventario(false);
            if(!empty($erroresStock))return ['error'=>$erroresStock];

            return $this->procesarEnTransaccion();
        } catch (InvalidArgumentException $th) {
            return ['error'=>[$th->getMessage()]];
        } catch (Throwable $th) {
           return ['error'=>['Error al procesar la solicitud. '.$th->getMessage()]];
        }
    }


    public function anularOrden(array $datos, int $sucursalId): array{
        date_default_timezone_set('America/Bogota');
        try {
            $this->normalizarAnulacion($datos, $sucursalId);
            return $this->procesarAnulacionEnTransaccion();
            } catch (InvalidArgumentException $th) {
            return ['error'=>[$th->getMessage()]];
        } catch (Throwable $th) {
            return ['error'=>['Error al anular la orden. '.$th->getMessage()]];
        }
    }

    /**
     * Fuerza las reglas propias de ventascontrolador::facturarCotizacion().
     * Si el cliente envia valores incompatibles se rechaza la solicitud en vez
     * de permitir que este endpoint cree otra cotizacion, una remision o credito.
     */
    private function normalizarPagoCotizacionExistente(array $datos):array{
        if(isset($datos['estado']) && $datos['estado'] !== 'Paga')
            throw new InvalidArgumentException('El endpoint solo permite pagar una cotizacion o remision existente.');

        if(isset($datos['tipoventa']) && $datos['tipoventa'] !== 'Contado')
            throw new InvalidArgumentException('El endpoint solo permite facturacion de contado.');

        foreach(['id'=>'orden', 'idcaja'=>'caja', 'idconsecutivo'=>'consecutivo'] as $campo=>$nombre){
            $valor = filter_var($datos[$campo] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if($valor === false)
                throw new InvalidArgumentException("El identificador de {$nombre} no es valido.");
            $datos[$campo] = (string)$valor;
        }

        $datos['estado'] = 'Paga';
        $datos['tipoventa'] = 'Contado';
        return $datos;
    }

    /**
     * Convierte los campos serializados enviados por ventas.ts en estructuras
     * PHP y construye el encabezado inicial de la factura.
     */
    private function inicializarSolicitud(array $datos, int $sucursalId):void{
        // Permite reutilizar una instancia en pruebas sin mezclar el estado de
        // una solicitud anterior.
        $this->carrito = [];
        $this->mediosPago = [];
        $this->impuestos = [];
        $this->itemsStockMinimo = [];
        $this->inventarioVenta = [];
        $this->lineasActualizar = [];
        $this->lineasInsertar = [];
        $this->ordenOrigen = null;
        $this->usarDetalleOrdenPersistido = false;

        $this->datos = $datos;
        $this->sucursalId = $sucursalId;
        $this->estado = (string)($datos['estado'] ?? '');
        $this->tipoVenta = (string)($datos['tipoventa'] ?? 'Contado');
        $this->validarCamposBasicos();
        $this->carrito = $this->decodificarArray('carrito'); //obtengo los datos del carrito
        $this->mediosPago = $this->decodificarArray('mediosPago');
        $this->impuestos = $this->decodificarArray('factimpuestos');
        $this->valoresCredito = $this->decodificarObjeto('valoresCredito');
        $this->datosAdquiriente = $this->decodificarObjeto('datosAdquiriente');

        if(empty($this->carrito))
            throw new InvalidArgumentException('El carrito no contiene productos para procesar.');

        foreach($this->carrito as $linea){
            //if(!is_object($linea))
              //  throw new InvalidArgumentException('El carrito contiene una linea no valida.');
            $linea->cantidad = (float)($linea->stock ?? $linea->cantidad ?? 0);
            $promedio = (float)($linea->promediostock ?? 0);
            $linea->stockaux = $promedio > 0 ? $linea->cantidad / $promedio : 0;
        }

        $this->facturaSolicitud = new facturas($datos);
        $this->facturaSolicitud->id_sucursal = $sucursalId;
        // ActiveRecord conserva las alertas de forma estatica; se limpian antes
        // de validar para que otra operacion no contamine esta solicitud.
        $this->facturaSolicitud->validar();
    }


    private function getProductosEImpuestosCotizacion(array $datos, int $sucursalId):bool{
        $this->itemsStockMinimo = [];
        $this->datos = $datos;
        $this->sucursalId = $sucursalId;
        $this->estado = (string)($datos['estado'] ?? '');
        $this->tipoVenta = (string)($datos['tipoventa'] ?? 'Contado');
        $this->validarCamposBasicos();
        $this->ordenOrigen = $this->buscarOrdenOrigen();
        if(!$this->ordenOrigen)
            throw new RuntimeException('No fue posible obtener la orden.');

        $this->usarDetalleOrdenPersistido = true;
        $this->cargarDetalleOrdenPersistido();

        $this->mediosPago = $this->decodificarArray('mediosPago');
        $this->valoresCredito = (object)[
                                    'capital' => 0,
                                    'abonoinicial' => 0,
                                ];
        $this->datosAdquiriente = json_decode(json_encode(adquirientes::find('id', 1)));

        return true;
    }


    /** Normaliza exclusivamente los datos recibidos por el endpoint de anulacion. */
    private function normalizarAnulacion(array $datos, int $sucursalId):void{
        $idOrden = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        $devolverInventario = filter_var($datos['devolverinv'] ?? null,FILTER_VALIDATE_INT);
        if($idOrden === false)
            throw new InvalidArgumentException('El identificador de la orden no es valido.');
        if($sucursalId <= 0)
            throw new InvalidArgumentException('La sucursal no es valida.');

        $this->datos = $datos;
        $this->datos['id'] = $idOrden;
        $this->datos['observacioneliminacion'] = trim((string)($datos['observacioneliminacion'] ?? ''));
        $this->sucursalId = $sucursalId;

        if(!in_array($devolverInventario, [0, 1], true))
            throw new InvalidArgumentException('La opción de devolución de inventario no es válida.');
        if(mb_strlen($this->datos['observacioneliminacion']) > 255)
            throw new InvalidArgumentException('La observacion de anulacion no puede superar 255 caracteres.');

        $this->devolverinv = $devolverInventario;
        $this->carrito = $this->devolverinv === 1 ? $this->decodificarArray('inv') : [];
        if($this->devolverinv === 1 && empty($this->carrito))
            throw new InvalidArgumentException('Debe seleccionar al menos un producto para devolver a inventario.');
    }


    /** Valida los discriminadores que determinan el flujo de facturacion. */
    private function validarCamposBasicos():void{
        if(!in_array($this->estado, ['Paga', 'Guardado', 'Remision', 'Redimido'], true))
            throw new InvalidArgumentException('Estado de la solicitud no valido.');

        if($this->estado === 'Paga' && !in_array($this->tipoVenta, ['Contado', 'Credito'], true))
            throw new InvalidArgumentException('El tipo de venta no es valido.');

        if(isset($this->datos['id']) && $this->datos['id'] !== '' && !is_numeric($this->datos['id']))
            throw new InvalidArgumentException('El identificador de la orden no es valido.');
    
        if(isset($this->datos['puntos_descontados']) && !is_numeric($this->datos['puntos_descontados']))
            throw new InvalidArgumentException('Los puntos a descontar debe ser un numero valido');

        if(isset($this->datos['puntos_descontados']) && (float)$this->datos['puntos_descontados']<0)
            throw new InvalidArgumentException('Los puntos no pueden ser negativos.'); 

        if(in_array($this->estado, ['Guardado', 'Remision']) && ((float)$this->datos['puntos_descontados']!=0))
            throw new InvalidArgumentException('Para cotizacion o remision no se puede ajustar puntos.');

        //validamos contra los puntos del cliente.
    }

    /** Decodifica un campo JSON que debe contener una lista de objetos. */
    private function decodificarArray(string $campo):array{
        $valor = json_decode($this->datos[$campo] ?? '[]');
        if(json_last_error() !== JSON_ERROR_NONE || !is_array($valor))
            throw new InvalidArgumentException("El campo {$campo} no contiene un arreglo JSON valido.");
        return $valor;
    }

    /** Decodifica un campo JSON que debe contener un objeto. */
    private function decodificarObjeto(string $campo):stdClass{
        $valor = json_decode($this->datos[$campo] ?? '{}');
        if(json_last_error() !== JSON_ERROR_NONE || !($valor instanceof stdClass))
            throw new InvalidArgumentException("El campo {$campo} no contiene un objeto JSON valido.");
        return $valor;
    }
    /**
     * Recarga productos, insumos e impuestos directamente desde la orden.
     * Se ejecuta inicialmente para validar y otra vez despues del FOR UPDATE,
     * evitando facturar un detalle antiguo ante una edicion concurrente.
     */
    private function cargarDetalleOrdenPersistido():void{
        if(!$this->ordenOrigen)
            throw new RuntimeException('No fue posible obtener la orden.');

        $this->carrito = ventasService::adjuntarInsumos(  ventas::idregistros('idfactura', $this->ordenOrigen->id) );
        if(empty($this->carrito))
            throw new InvalidArgumentException('La orden no contiene productos para facturar.');

        $this->inventarioVenta = ventasService::prepararInventarioPersistido( $this->carrito, $this->sucursalId );

        // Calcula los impuestos de la cotizacion pagada desde ordenresumen.ts.
        $this->impuestos = [];
        $arrayImp = ['0'=>1, '5'=>2, '16'=>3, '19'=>4, 'excluido'=>5, '8'=>6];
        foreach($this->carrito as $value){
            $id_impuesto = $arrayImp[$value->impuesto];
            if(!isset($this->impuestos[$value->impuesto])){
                $this->impuestos[$value->impuesto] = (object)[
                    "id_impuesto"   => $id_impuesto,
                    "facturaid"     => $this->ordenOrigen->id,
                    "basegravable"  => 0,
                    "valorimpuesto" => 0,
                ];
            }
            $this->impuestos[$value->impuesto]->basegravable += $value->base;
            $this->impuestos[$value->impuesto]->valorimpuesto += $value->valorimp;
        }
    }
    /**
     * Resuelve productos simples e insumos de productos compuestos y aplica la
     * configuracion local que permite o impide vender sin existencias.
     */
    private function prepararYValidarInventario(bool $usarInventarioPersistido = true):array{
        $debeValidarStock = !$this->ordenOrigen || (int)$this->ordenOrigen->entregado === 0;
        if($usarInventarioPersistido)
            $this->inventarioVenta = ventasService::prepararInventarioXVenta($this->carrito, $this->sucursalId);
        if(($this->parametros['permitir_venta_de_productos_sin_stock']->valor_final ?? 0) == 0 && $debeValidarStock)
            return ventasService::validarDisponibilidadInventario( $this->inventarioVenta, $this->sucursalId );
        return [];
    }

    /** Separa las lineas persistidas de las agregadas al editar una orden. */
    private function separarLineasOrden():void{
        foreach($this->carrito as $linea){
            if(!empty($linea->id))$this->lineasActualizar[] = clone $linea;
            else $this->lineasInsertar[] = $linea;
        }
    }

    /**
     * Busca y valida la cotizacion/remision indicada por la solicitud. Impide
     * procesar ordenes de otra sucursal o convertidas previamente.
     */
    private function buscarOrdenOrigen(bool $bloquear = false):?facturas{
        $idOrden = (int)($this->datos['id'] ?? 0);
        if($idOrden <= 0)return null;
        // FOR UPDATE solo se usa despues de abrir una transaccion. La lectura
        // inicial permite responder pronto; la lectura bloqueada es definitiva.
        $orden = $bloquear ? facturas::findForUpdate('id', $idOrden) : facturas::find('id', $idOrden);
        $esCotizacionORemision = $orden && ((int)$orden->cotizacion === 1 || (int)$orden->remision === 1 || $orden->estado === 'Remision');

        if(!$orden || (int)$orden->id_sucursal !== $this->sucursalId || !$esCotizacionORemision || (int)$orden->cambioaventa !== 0)
            throw new InvalidArgumentException('La cotizacion o remision no esta disponible para procesar.');
        return $orden;
    }


    private function buscarOrdenAnulable(bool $bloquear = false): facturas{
        $idOrden = filter_var($this->datos['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if($idOrden === false)
            throw new InvalidArgumentException('El identificador de la orden no es valido.');

        $orden = $bloquear ? facturas::findForUpdate('id', $idOrden) : facturas::find('id', $idOrden);

        if(!$orden)
            throw new InvalidArgumentException('La orden no existe.');
        if((int)$orden->id_sucursal !== $this->sucursalId)
            throw new InvalidArgumentException('La orden no pertenece a la sucursal actual.');
        if(!in_array($orden->estado, ['Paga', 'Guardado', 'Remision'], true))
            throw new InvalidArgumentException('La orden no se encuentra disponible para anular.');
        return $orden;
    }

    /**
     * Actualiza encabezado y lineas de una orden recuperada dentro de una sola
     * transaccion coordinada por este servicio.
     */
    private function editarOrdenExistente():array{
        $esMismoTipo = ($this->estado === 'Guardado' && (int)$this->ordenOrigen->cotizacion === 1) || ($this->estado === 'Remision' && ((int)$this->ordenOrigen->remision === 1 || $this->ordenOrigen->estado === 'Remision'));
        if(!$esMismoTipo)return ['error'=>['No es posible cambiar el tipo de la orden recuperada.']];

        $db = facturas::getDB();
        $db->begin_transaction();
        try {
            // Impide que una edicion o facturacion simultanea use la misma
            // orden mientras se actualizan encabezado y lineas.
            $this->ordenOrigen = $this->buscarOrdenOrigen(true);
            $this->ordenOrigen->compara_objetobd_post($this->datos);
            $this->ordenOrigen->id_sucursal = $this->sucursalId;
            $this->normalizarTipoOrden($this->ordenOrigen);
            if(!$this->ordenOrigen->actualizar())
                throw new RuntimeException('No fue posible actualizar el encabezado de la orden.');

            $this->prepararLineasParaGuardar($this->lineasInsertar, (int)$this->ordenOrigen->id);
            ventasService::actualizarLineasCotizacion( $this->lineasActualizar, $this->lineasInsertar, (int)$this->ordenOrigen->id, false );
            if(!$db->commit())
                throw new RuntimeException('No fue posible confirmar la actualizacion de la orden.');
            return ['exito'=>['Orden actualizada con exito.']];
        }catch(Throwable $th){
            $db->rollback();
            return ['error'=>['Error al actualizar la orden. '.$th->getMessage()]];
        }
    }

    /**
     * Abre la frontera transaccional para una orden nueva o una factura pagada.
     * Todos los servicios llamados desde aqui usan la misma conexion mysqli.
     */
    private function procesarEnTransaccion():array{
        $db = facturas::getDB();
        $db->begin_transaction();
        try {
            if($this->ordenOrigen){
                // Segunda lectura y validacion bajo bloqueo. Si otra solicitud
                // ya la convirtio, cambioaventa=1 hace fallar esta operacion.
                $this->ordenOrigen = $this->buscarOrdenOrigen(true);

                if($this->usarDetalleOrdenPersistido){
                    $this->cargarDetalleOrdenPersistido();
                    $erroresStock = $this->prepararYValidarInventario(false);
                    if(!empty($erroresStock))
                        throw new RuntimeException(implode(' ', $erroresStock));
                }
            }

            $cierre = $this->obtenerOAbrirCierreCaja();
            $factura = $this->construirFactura($cierre);
            $this->asignarDatosFiscales($factura);
            $respuesta = $this->estado === 'Paga' ? $this->guardarFacturaPagada($factura, $cierre) :  ($this->estado === 'Redimido' ? $this->guardarRedimido($factura, $cierre): $this->guardarNuevaOrden($factura, $cierre));
            $db->commit();
            //enviar stock minimo a whatsapp
            if(!empty($this->itemsStockMinimo) && ($this->parametros['notificacion_por_whatsApp_stock_bajo']->valor_final ?? 0) == 1){
                try {
                    (new whatsAppService())->productoBajoStock($this->itemsStockMinimo);
                } catch (\Throwable $th) {
                   error_log("No fue posible notificar el stock minimo de la orden. ".$th->getMessage());
                }
            }
            return $respuesta;
        }catch(Throwable $th){
            $db->rollback();
            return ['error'=>['Error al procesar la solicitud. '.$th->getMessage()]];
        }
    }


    private function procesarAnulacionEnTransaccion():array{
        $db = facturas::getDB();
        $db->begin_transaction();
        try {
            // Esta es la unica lectura de la orden. FOR UPDATE mantiene la fila
            // bloqueada hasta confirmar o revertir toda la anulacion.
            $this->ordenOrigen = $this->buscarOrdenAnulable(true);
            if($this->ordenOrigen->estado === 'Paga'){
                $contextoNotificacion = $this->anularFacturaPagada();
            }elseif(in_array($this->ordenOrigen->estado, ['Guardado', 'Remision'], true)){
                $contextoNotificacion = $this->anularCotizacionRemision();
            }else{
                throw new RuntimeException('El estado de la orden no permite su anulacion.');
            }

            if(!$db->commit())
                throw new RuntimeException('No fue posible confirmar la anulacion de la orden.');

            if(($this->parametros['notificacion_por_whatsApp_eliminacion_de_factura']->valor_final ?? 0) == 1)
                (new whatsAppService())->sendTextOrdenEliminada( $contextoNotificacion['factura'], (int)$contextoNotificacion['idcaja'], (bool)$contextoNotificacion['devolvioInventario'], $contextoNotificacion['productos'] ?? [] );

            return ['exito'=>['Orden eliminada correctamente.']];
        } catch (Throwable $th) {
            $db->rollback();
            throw $th;
        }
    }


    /** Anula una factura definitiva y revierte todas sus relaciones operativas. */
    private function anularFacturaPagada():array{
        $factura = $this->ordenOrigen;
        $cierre = $this->obtenerCierreOrdenBloqueado($factura, true);
        $inventarioVenta = null;

        if($this->devolverinv === 1){
            if((int)$factura->entregado !== 1)
                throw new InvalidArgumentException('No es posible devolver inventario porque la orden aun no habia descontado existencias.');
            $inventarioVenta = $this->prepararInventarioDevolucion($factura);
        }

        $this->marcarOrdenEliminada($factura);
        $this->revertirIndicadoresFacturaPagada($factura, $cierre);

        if(!$factura->actualizar())
            throw new RuntimeException('No fue posible actualizar el estado de la factura.');
        if(!$cierre->actualizar())
            throw new RuntimeException('No fue posible actualizar el cierre de caja.');

        if((float)$factura->porcentgananciauser > 0 && (float)$factura->valorgananciauser > 0){
            $this->comisionServicio ??= new comisionesService();
            if(!$this->comisionServicio->eliminarComisionXFactura((int)$factura->id))
                throw new RuntimeException('No fue posible anular la comision de la factura.');
        }

        if($factura->tipoventa === 'Credito'){
            $resultadoCredito = creditosService::anularCredito((int)$factura->id);
            if(!empty($resultadoCredito['error']))
                throw new RuntimeException(implode(' ', $resultadoCredito['error']));
        }

        $this->contableService ??= new contableService();
        if(!$this->contableService->anularMovimiento(1, (int)$factura->id, (string)$factura->observacioneliminacion))
            throw new RuntimeException('No fue posible anular el movimiento de caja de la factura.');

        if($inventarioVenta !== null)
            ventasService::devolverInventarioXVenta( $inventarioVenta, $this->sucursalId, 'devolucion', "retorno de unidades por anulacion de factura #{$factura->id}", false );

        return [
            'notificar'=>true,
            'factura'=>$factura,
            'idcaja'=>(int)$cierre->idcaja,
            'devolvioInventario'=>$inventarioVenta !== null,
            'productos'=>$inventarioVenta !== null ? array_values($inventarioVenta['productosSimples'] ?? []) : [],
        ];
    }

    /** Anula una cotizacion o remision sin aplicar reglas financieras de venta. */
    private function anularCotizacionRemision():array{
        $orden = $this->ordenOrigen;
        $inventarioVenta = null;
        if($this->devolverinv === 1){
            if($orden->estado !== 'Remision' || (int)$orden->entregado !== 1)
                throw new InvalidArgumentException('Solo una remision despachada puede devolver inventario al anularse.');
            $inventarioVenta = $this->prepararInventarioDevolucion($orden);
        }

        if($orden->estado === 'Guardado'){
            $cierre = $this->obtenerCierreOrdenBloqueado($orden, true);
            $cierre->totalcotizaciones -= 1;
            if(!$cierre->actualizar())throw new RuntimeException('No fue posible actualizar el cierre de caja de la cotizacion.');
        }

        $this->marcarOrdenEliminada($orden);
        if(!$orden->actualizar())
            throw new RuntimeException('No fue posible actualizar el estado de la orden.');

        if($inventarioVenta !== null)
            ventasService::devolverInventarioXVenta( $inventarioVenta, $this->sucursalId, 'devolucion', "retorno de unidades por anulacion de remision #{$orden->id}", false );

        return [
            'notificar'=>false,
            'factura'=>$orden,
            'idcaja'=>(int)($orden->idcaja ?? 0),
            'devolvioInventario'=>$inventarioVenta !== null,
            'productos'=>[],
        ];
    }

    /** Obtiene y bloquea el cierre original; una venta solo se anula con caja abierta. */
    private function obtenerCierreOrdenBloqueado(facturas $orden, bool $exigirAbierto):cierrescajas{
        $idCierre = (int)($orden->idcierrecaja ?? 0);
        if($idCierre <= 0)throw new RuntimeException('La orden no tiene un cierre de caja asociado.');

        $cierre = cierrescajas::findForUpdate('id', $idCierre);
        if(!$cierre || (int)$cierre->idsucursal_id !== $this->sucursalId)
            throw new RuntimeException('No fue posible obtener el cierre de caja original.');
        if($exigirAbierto && (int)$cierre->estado !== 0)
            throw new InvalidArgumentException('El cierre de caja de la orden ya se encuentra cerrado.');
        return $cierre;
    }

    /** Prepara y valida que la seleccion produzca al menos un movimiento real. */
    private function prepararInventarioDevolucion(facturas $orden):array{
        $inventario = ventasService::prepararDevolucionParcialPersistida((int)$orden->id, $this->carrito, $this->sucursalId );
        if(empty($inventario['productosSimples']) && empty($inventario['insumos']))
            throw new InvalidArgumentException('No se seleccionaron cantidades positivas para devolver a inventario.');
        return $inventario;
    }

    /** Aplica el estado y la auditoria comunes a cualquier tipo de orden. */
    private function marcarOrdenEliminada(facturas $orden):void{
        $orden->estado = 'Eliminada';
        $orden->observacioneliminacion = $this->datos['observacioneliminacion'];
        $orden->fechaanulacion = date('Y-m-d H:i:s');
    }

    /** Revierte exactamente los indicadores incrementados al crear una factura. */
    private function revertirIndicadoresFacturaPagada(facturas $factura, cierrescajas $cierre):void{
        $cierre->totalfacturaseliminadas += 1;
        $tipoFacturador = (int)consecutivos::uncampo('id', $factura->idconsecutivo, 'idtipofacturador');
        if($tipoFacturador === 1){
            $cierre->facturaselectronicaselimnadas += 1;
            $cierre->valorfe -= (float)$factura->total;
            $cierre->descuentofe -= (float)$factura->descuento;
        }else{
            $cierre->facturasposeliminadas += 1;
            $cierre->valorpos -= (float)$factura->total;
            $cierre->descuentopos -= (float)$factura->descuento;
        }

        if($factura->tipoventa === 'Contado'){
            $cierre->ventasenefectivo -= $this->obtenerEfectivoFactura((int)$factura->id);
            $cierre->ingresoventas -= (float)$factura->total;
            $cierre->descuentocontado -= (float)$factura->descuento;
        }else{
            $cierre->creditocapital -= (float)$factura->total;
            $cierre->creditos -= (float)$factura->total - (float)$factura->abono;
            $cierre->descuentocredito -= (float)$factura->descuento;
        }

        $cierre->domicilios -= (float)$factura->valortarifa;
        $cierre->totaldescuentos -= (float)$factura->descuento;
        $cierre->valorimpuestototal -= (float)$factura->valorimpuestototal;
        $cierre->basegravable -= (float)$factura->base;
    }

    /** Suma todas las partes pagadas en efectivo de una factura de contado. */
    private function obtenerEfectivoFactura(int $idFactura):float{
        $total = 0.0;
        foreach(factmediospago::whereArray(['id_factura'=>$idFactura, 'idmediopago'=>1]) as $pago)
            $total += (float)($pago->valor ?? 0);
        return $total;
    }


    /** Obtiene el cierre abierto de la caja o crea uno, como lo hacía ventas. */
    private function obtenerOAbrirCierreCaja():cierrescajas{
        $idCaja = (int)($this->datos['idcaja'] ?? 0);
        if($idCaja <= 0)throw new RuntimeException('Caja no valida.');

        $cierre = cierrescajas::uniquewhereArray([ 'estado'=>0,  'idcaja'=>$idCaja, 'idsucursal_id'=>$this->sucursalId ]);

        if(!$cierre){
            $cajaSeleccionada = caja::find('id', $idCaja);
            if(!$cajaSeleccionada)throw new RuntimeException('La caja seleccionada no existe.');

            $cierre = new cierrescajas([ 'idcaja'=>$idCaja, 'nombrecaja'=>$cajaSeleccionada->nombre, 'estado'=>0, 'idsucursal_id'=>$this->sucursalId ]);
            [$creado, $idCierre] = $cierre->crear_guardar();
            if(!$creado)throw new RuntimeException('No fue posible abrir el cierre de caja.');
            $cierre->id = $idCierre;
        }
        return $cierre;
    }

    /**
     * Construye la entidad que se persistira. Al pagar una orden crea una copia
     * nueva y conserva el numero de la orden original como referencia.
     */
    private function construirFactura(cierrescajas $cierre):facturas{
        if($this->ordenOrigen){ //cuando ya existe una orden
            $numeroOrdenOrigen = $this->ordenOrigen->num_orden;
            $factura = clone $this->ordenOrigen;
            $factura->compara_objetobd_post($this->datos);
            $factura->id = null;
            $factura->referencia = $numeroOrdenOrigen;
            $factura->cambioaventa = 1;
        }else{ //cuando es nueva factura
            $factura = $this->facturaSolicitud;
        }

        $factura->id_sucursal = $this->sucursalId;
        $factura->idcaja = (int)$this->datos['idcaja'];
        $factura->idcierrecaja = $cierre->id;
        $factura->num_orden = facturas::calcularNumOrden($this->sucursalId);

        $factura->entrega == 'Presencial' ? $factura->entregado=1 : $factura->entregado=0;

        if($this->parametros['valor_por_punto']->valor_final && ((int)$factura->idcliente ?? 0)>0){
            if($this->estado == "Paga"){
                $this->puntos = (float)$factura->total/(float)$this->parametros['valor_por_punto']->valor_final;
                $factura->puntos_generados = $this->puntos;
            }
        }

        return $factura;
    }

    /** Guarda una cotizacion o remision nueva y sus lineas, sin inventario. */
    private function guardarRedimido(facturas $factura, cierrescajas $cierre){
        $this->normalizarTipoOrden($factura);
        [$creada, $idFactura] = $factura->crear_guardar();
        if(!$creada)throw new RuntimeException('No fue posible guardar la orden.');

        $this->actualizarPuntosCliente($factura, (int)$idFactura, false);
        $this->prepararLineasParaGuardar($this->carrito, (int)$idFactura);
        ventasService::guardarLineasVenta($this->carrito, false);

        $itemsStockMinimo = [];
        ventasService::descontarInventarioXVenta( $this->inventarioVenta, $this->sucursalId, 'venta', 'descuento de unidades por venta', false, $itemsStockMinimo);
        $this->itemsStockMinimo = $itemsStockMinimo;
        return ['exito'=>['Puntos redimidos exitosamente. producto dado de baja de inventario']];
    }

    /** Guarda una cotizacion o remision nueva y sus lineas, sin inventario. */
    private function guardarNuevaOrden(facturas $factura, cierrescajas $cierre):array{
        $this->normalizarTipoOrden($factura);
        [$creada, $idFactura] = $factura->crear_guardar();
        if(!$creada)throw new RuntimeException('No fue posible guardar la orden.');

        $this->prepararLineasParaGuardar($this->carrito, (int)$idFactura);
        ventasService::guardarLineasVenta($this->carrito, false);

        if($this->estado === 'Guardado')$cierre->totalcotizaciones += 1;
        if(!$cierre->actualizar())throw new RuntimeException('No fue posible actualizar el cierre de caja.');

        $mensaje = $this->estado === 'Guardado' ? 'Cotizacion guardada con exito.'  : 'Remision generada con exito.';
        return ['exito'=>[$mensaje]];
    }

    /** Fuerza valores coherentes para cotizaciones y remisiones. */
    private function normalizarTipoOrden(facturas $factura):void{
        $factura->estado = $this->estado;
        $factura->cotizacion = $this->estado === 'Guardado' ? 1 : 0;
        $factura->remision = $this->estado === 'Remision' ? 1 : 0;
        $factura->cambioaventa = 0;
    }

    /**
     * Ejecuta todas las etapas de una factura pagada dentro de la transaccion
     * abierta por procesarEnTransaccion().
     */
    private function guardarFacturaPagada(facturas $factura, cierrescajas $cierre):array{

        $esRemisionOrigen = $this->ordenOrigen && (int)$this->ordenOrigen->remision === 1;
        $inventarioYaDescontado = $esRemisionOrigen && (int)$this->ordenOrigen->entregado === 1;

        $consecutivo = $this->obtenerConsecutivoBloqueado();
        $this->normalizarFacturaPagada($factura, $consecutivo);

        [$creada, $idFactura] = $factura->crear_guardar();
        if(!$creada)throw new RuntimeException('No fue posible crear la factura.');

        $consecutivo->siguientevalor += 1;
        if(!$consecutivo->actualizar()) throw new RuntimeException('No fue posible actualizar el consecutivo.');

        $this->relacionarOrdenOrigen($factura, $cierre);
        self::createInvoiceElectronic( $this->carrito, $this->datosAdquiriente, $factura->idconsecutivo, $idFactura, $factura->num_consecutivo, $this->mediosPago,  $factura->descuento, $factura->valortarifa, $factura->observacion );
        $idCuota = $this->registrarCredito($factura, (int)$idFactura);
        $this->actualizarPuntosCliente($factura, (int)$idFactura);
        $this->actualizarCierreYRelaciones( $factura, $cierre, (int)$idFactura, $idCuota );
        $this->prepararLineasParaGuardar($this->carrito, (int)$idFactura);
        ventasService::guardarLineasVenta($this->carrito, false);

        if(!empty($this->mediosPago))
            (new factmediospago())->crear_varios_reg_arrayobj($this->mediosPago);
        if(!empty($this->impuestos))
            (new factimpuestos())->crear_varios_reg_arrayobj($this->impuestos);

        $itemsStockMinimo = [];
        if(!$inventarioYaDescontado&&((int)$factura->entregado === 1)){
            ventasService::descontarInventarioXVenta( $this->inventarioVenta, $this->sucursalId, 'venta', 'descuento de unidades por venta', false, $itemsStockMinimo);
            $this->itemsStockMinimo = $itemsStockMinimo;
        }
        $this->registrarMovimientoCaja($factura, (int)$idFactura);
        $this->registrarComision($factura, (int)$idFactura);

        $respuesta = [
            'exito'=>['Pago procesado con exito.'],
            'idfactura'=>$idFactura,
            'dataInvoice'=>ventasService::dataInvoiceForPrinterServer( $this->datosAdquiriente, $factura, $consecutivo )
        ];
        if($this->tipoVenta==='Credito'&&$idCuota!=='NULL')$respuesta['idcuota'] = $idCuota;

        return $respuesta;
    }

    /** Bloquea el consecutivo para evitar que dos ventas usen el mismo numero. */
    private function obtenerConsecutivoBloqueado():consecutivos{
        $idConsecutivo = (int)($this->datos['idconsecutivo'] ?? 0);
        $consecutivo = consecutivos::findForUpdate('id', $idConsecutivo);
        if(!$consecutivo || (int)$consecutivo->id_sucursalid !== $this->sucursalId)
            throw new RuntimeException('El consecutivo seleccionado no es valido.');
        return $consecutivo;
    }

    /** Establece los campos que distinguen una factura definitiva. */
    private function normalizarFacturaPagada(facturas $factura, consecutivos $consecutivo):void {
        $factura->estado = 'Paga';
        $factura->cotizacion = 0;
        $factura->remision = 0;
        $factura->fechapago = date('Y-m-d H:i:s');
        $factura->fechaentrega = (int)$factura->entregado === 1 ? date('Y-m-d H:i:s') : '';
        $factura->num_consecutivo = $consecutivo->siguientevalor;
        $factura->prefijo = $consecutivo->prefijo;
        $factura->abono = $this->valoresCredito->abonoinicial ?? 0;
        $factura->habilitada = 1;
    }

    /** Marca la orden recuperada como aceptada y la relaciona con la factura. */
    private function relacionarOrdenOrigen(facturas $factura, cierrescajas $cierre):void{
        if(!$this->ordenOrigen)return;
        $this->ordenOrigen->estado = 'Aceptada';
        $this->ordenOrigen->cambioaventa = 1;
        $this->ordenOrigen->referencia = $factura->num_orden;
        if(!$this->ordenOrigen->actualizar())
            throw new RuntimeException('No fue posible relacionar la orden con la factura.');
        $cierre->ncambiosaventa += 1;
    }

    /** Crea el credito y devuelve el id de la cuota para los medios de pago. */
    private function registrarCredito(facturas $factura, int $idFactura):int|string{
        if($this->tipoVenta !== 'Credito')return 'NULL';
        $resultado = creditosService::crearCredito( $this->valoresCredito, $idFactura, (int)is_numeric((int)$factura->idcliente), $factura->totalunidades, $factura->base, $factura->valorimpuestototal, (int)$factura->dctox100, $factura->descuento, (int)$factura->idcierrecaja, (int)$factura->idcaja, (int)$factura->idvendedor, $factura->idemisor );
        if(!empty($resultado['error']))throw new RuntimeException(implode(' ', $resultado['error']));
        return $resultado['idcuota'] ?? 'NULL';
    }


    private function actualizarPuntosCliente(facturas $factura, int $idFactura, bool $dir = true):void{
        if($this->parametros['valor_por_punto']->valor_final && ((int)$factura->idcliente ?? 0)>0){
           $cliente = clientes::findForUpdate('id', $factura->idcliente);
           $dir? ($cliente->puntos += ($this->puntos - $factura->puntos_descontados)): ($cliente->puntos -= $factura->puntos_descontados);
           $cliente->actualizar();
        }
    }

    /**
     * Actualiza indicadores del cierre y prepara las FK de pagos e impuestos.
     */
    private function actualizarCierreYRelaciones( facturas $factura, cierrescajas $cierre, int $idFactura, int|string $idCuota ):void {
        $cierre->descuentocontado += $this->tipoVenta === 'Credito' ? 0  : $factura->descuento;
        $cierre->descuentocredito += $this->tipoVenta === 'Contado' ? 0  : $factura->descuento;

        ventasService::datosDelCierreCajaXVenta( $cierre, $factura, $this->mediosPago, $this->impuestos, [true, $idFactura], $this->valoresCredito, $this->tipoVenta );
        // datosDelCierreCajaXVenta inicializa idcuota en NULL; para ventas a
        // credito se sustituye despues de crear la cuota.
        foreach($this->mediosPago as $pago)$pago->idcuota = $idCuota;
    }

    /** Prepara las columnas comunes antes del INSERT masivo de lineas. */
    private function prepararLineasParaGuardar(array $lineas, int $idFactura):void{
        foreach($lineas as $linea){  //aqui se modifica las propiedades de los objetos del arreglo $this->lineasInsertar, ya que los objetos se pansan por referencia
            $linea->idfactura = $idFactura;
            $linea->dato1 = '';
            $linea->dato2 = '';

            // Los productos libres llamados "Otros" se relacionan con el
            // producto y categoria reservados por el sistema.
            if((int)($linea->idproducto ?? 0) < 0
                && (int)($linea->idcategoria ?? 0) < 0){
                $linea->idproducto = 1;
                $linea->idcategoria = 1;
            }
        }
    }

    /** Registra el ingreso o abono inicial asociado con la factura. */
    private function registrarMovimientoCaja(facturas $factura, int $idFactura):void{
        $this->contableService ??= new contableService();
        $esContado = $this->tipoVenta === 'Contado';
        $movimiento = $this->contableService->createMovimiento([
            'fk_tipo_movimientocaja'=>$esContado ? 1 : 11,
            'fk_tipo_documento'=>1,
            'id_documento'=>$idFactura,
            'fk_tipo_tercero'=>1,
            'id_tercero'=>is_numeric((int)$factura->idcliente),
            'fk_caja'=>$factura->idcaja,
            'fk_usuario'=>$factura->idvendedor,
            'naturaleza'=>'I',
            'numero_documento'=>$factura->prefijo.$factura->num_consecutivo,
            'num_orden'=>null,
            'valor'=>$esContado ? $factura->total : $factura->abono,
            'concepto'=>$esContado ? 'PAGO DE CONTADO' : 'ABONO INICIAL',
            'observacion'=>$esContado ? 'PAGO DE CONTADO A FACTURA' : 'ABONO INICIAL A FACTURA CREDITO'
        ]);

        if(!($movimiento[0] ?? false))
            throw new RuntimeException('No fue posible registrar el movimiento de caja.');
    }

    /** Registra la comision solo cuando la factura calculo una ganancia. */
    private function registrarComision(facturas $factura, int $idFactura):void{
        if((float)$factura->valorgananciauser <= 0)return;
        $this->comisionServicio ??= new comisionesService();
        $this->comisionServicio->crearComision(  $idFactura, (int)$factura->idvendedor, (float)$factura->total, (float)$factura->porcentgananciauser, (float)$factura->valorgananciauser );
    }


    private function asignarDatosFiscales(facturas $factura): void{
        $sucursalId = $this->sucursalId;
        $sucursal = sucursales::find('id', $sucursalId);
        $caja = caja::uniquewhereArray(['id' => (int)$factura->idcaja, 'idsucursalid' => $sucursalId]);

        if(!$caja)throw new \Exception('La caja seleccionada no existe.');

        if(!empty($caja->idemisor)){
            $emisor = emisores::uniquewhereArray(['id' => (int)$caja->idemisor, 'idsucursal' => $sucursalId]);

            if(!$emisor)throw new \Exception('El emisor asociado a la caja no existe.');

            $factura->idemisor = (int)$emisor->id;
            $factura->nombrecompania = (string)$emisor->nombre;
            $factura->nit = (string)$emisor->nit;
            $factura->datosrut = (string)$emisor->datosencabezados;
            return;
        }

        $factura->idemisor = null;
        $factura->nombrecompania = (string)$sucursal->negocio;
        $factura->nit = (string)$sucursal->nit;
        $factura->datosrut = (string)$sucursal->datosencabezados;
    }

}