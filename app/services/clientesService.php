<?php 

namespace App\services;

use App\Models\caja\cierrescajas;
use App\Models\clientes\clientes;
use App\Models\clientes\direcciones;
use App\Models\clientes\preciosporcliente;
use App\Models\comisiones\comisiones;
use App\Models\comisiones\pagos_comisiones;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\usuarios;
use App\Models\gastos;
use App\Models\sucursales;
use App\Repositories\comisiones\comisionesRepository;
use App\Repositories\comisiones\pagosComisionesRepository;
use App\Repositories\clientes\clientesRepository;
use App\Repositories\clientes\direccionesRepository;
use App\Repositories\clientes\preciosPorClienteRepository;
use App\Repositories\creditos\creditosRepository;
use stdClass;

class clientesService{

    private $repoClientes;
    private $repoDirecciones;
    private $repoPreciosCliente;
    private $repoCreditos;
    private $repoComisiones;
    private $repoPagosComisiones;

    
    public function __construct()
    {
        $this->repoClientes = new clientesRepository();
        $this->repoDirecciones = new direccionesRepository();
        $this->repoPreciosCliente = new preciosPorClienteRepository();
        $this->repoCreditos = new creditosRepository();
        $this->repoComisiones = new comisionesRepository();
        $this->repoPagosComisiones = new pagosComisionesRepository();
    }


    /**
     * Crea cliente y direccion dentro de la misma transaccion. Si cualquiera
     * falla, no queda un cliente sin su direccion asociada.
     */
    public function crearCliente(array $datosCliente, array $datosDireccion):array
    {
        $cliente = new clientes($datosCliente);
        $cliente->validar(); // limpia las alertas estaticas heredadas durante la migracion
        $alertas = $cliente->validar_nuevo_cliente();

        if($this->repoClientes->identificacionExiste($cliente->identificacion))
            $alertas['error'][] = 'El cliente ya esta registrado';

        $direccion = new direcciones($datosDireccion);
        $direccion->validar();
        $alertasDireccion = $direccion->validarDireccion();
        if(!empty($alertasDireccion['error']))
            $alertas['error'] = array_merge($alertas['error'] ?? [], $alertasDireccion['error']);

        if(!empty($alertas['error']))return $alertas;

        $db = $this->repoClientes->getConexion();
        $db->begin_transaction();
        try {
            [$creado, $idCliente] = $this->repoClientes->insert($cliente);
            if(!$creado)throw new \RuntimeException('No fue posible crear el cliente.');

            $cliente->id = (int)$idCliente;
            $direccion->idcliente = (int)$idCliente;
            [$direccionCreada] = $this->repoDirecciones->insert($direccion);
            if(!$direccionCreada)throw new \RuntimeException('No fue posible crear la direccion del cliente.');

            $db->commit();
            return [
                'exito'=>['Cliente registrado correctamente.'],
                'nextID'=>(int)$idCliente,
                'cliente'=>[$cliente],
            ];
        } catch (\Throwable $th) {
            $db->rollback();
            $this->registrarErrorClientes('crear cliente', $th);
            return ['error'=>['Error al crear el cliente.']];
        }
    }


    /** Actualiza cliente y, cuando llega, una direccion del mismo cliente. */
    public function actualizarCliente(array $datos):array
    {
        $idCliente = (int)($datos['idcliente'] ?? $datos['id'] ?? 0);
        $cliente = $this->repoClientes->find($idCliente);
        if(!$cliente)return ['error'=>['Cliente no encontrado.']];

        // Solo estos datos personales pueden modificarse desde este formulario;
        // saldos y acumulados financieros permanecen protegidos en el servidor.
        foreach(['nombre', 'apellido', 'tipodocumento', 'identificacion', 'telefono', 'email', 'fecha_nacimiento'] as $campo){
            if(array_key_exists($campo, $datos))$cliente->$campo = $datos[$campo];
        }

        $cliente->validar();
        $alertas = $cliente->validar_nuevo_cliente();
        if($this->repoClientes->identificacionExiste($cliente->identificacion, $idCliente))
            $alertas['error'][] = 'La identificacion pertenece a otro cliente.';

        $direccion = null;
        $crearDireccion = false;
        if(isset($datos['direccion']) && strlen(trim((string)$datos['direccion'])) > 3){
            $idDireccion = (int)($datos['iddireccion'] ?? 0);
            if($idDireccion > 0){
                $direccion = $this->repoDirecciones->findDeCliente($idDireccion, $idCliente);
                if(!$direccion)$alertas['error'][] = 'La direccion no pertenece al cliente.';
            }else{
                $direccion = new direcciones(['idcliente'=>$idCliente]);
                $crearDireccion = true;
            }

            if($direccion){
                foreach(['idtarifa', 'iddepartamento', 'idciudad', 'pais', 'departamento', 'ciudad', 'direccion', 'codigopostal', 'observacion'] as $campo){
                    if(array_key_exists($campo, $datos))$direccion->$campo = $datos[$campo];
                }
                $direccion->idcliente = $idCliente;
                $direccion->validar();
                $alertasDireccion = $direccion->validarDireccion();
                if(!empty($alertasDireccion['error']))
                    $alertas['error'] = array_merge($alertas['error'] ?? [], $alertasDireccion['error']);
            }
        }

        if(!empty($alertas['error']))return $alertas + ['cliente'=>[$cliente], 'nextID'=>$idCliente];

        $db = $this->repoClientes->getConexion();
        $db->begin_transaction();
        try {
            if(!$this->repoClientes->update($cliente))
                throw new \RuntimeException('No fue posible actualizar el cliente.');

            if($direccion){
                if($crearDireccion){
                    [$direccionGuardada] = $this->repoDirecciones->insert($direccion);
                }else{
                    $direccionGuardada = $this->repoDirecciones->update($direccion);
                }
                if(!$direccionGuardada)
                    throw new \RuntimeException('No fue posible guardar la direccion del cliente.');
            }

            $db->commit();
            return [
                'exito'=>['Datos del cliente actualizados.'],
                'cliente'=>[$cliente],
                'nextID'=>$idCliente,
            ];
        } catch (\Throwable $th) {
            $db->rollback();
            $this->registrarErrorClientes('actualizar cliente', $th);
            return [
                'error'=>['Error al actualizar el cliente.'],
                'cliente'=>[$cliente],
                'nextID'=>$idCliente,
            ];
        }
    }


    /** Elimina el agregado; las FK eliminan direcciones y precios en cascada. */
    public function eliminarCliente(int $idCliente):array
    {
        if($idCliente === 1)return ['error'=>['El cliente general del sistema no puede eliminarse.']];
        $cliente = $this->repoClientes->find($idCliente);
        if(!$cliente)return ['error'=>['Cliente no encontrado.']];

        $creditosAbiertos = $this->repoCreditos->where([
            'cliente_id'=>$idCliente,
            'idestadocreditos'=>2,
        ]);
        if(!empty($creditosAbiertos))return ['error'=>['Cliente tiene credito pendiente.']];

        try {
            if(!$this->repoClientes->delete($idCliente))
                throw new \RuntimeException('No fue posible eliminar el cliente.');
            return ['exito'=>['Cliente eliminado correctamente.']];
        } catch (\Throwable $th) {
            $this->registrarErrorClientes('eliminar cliente', $th);
            return ['error'=>['Error al eliminar el cliente.']];
        }
    }


    /** Crea una direccion y opcionalmente devuelve el listado actualizado. */
    public function crearDireccion(array $datos, bool $devolverListado = false):array
    {
        $idCliente = (int)($datos['idcliente'] ?? 0);
        if(!$this->repoClientes->find($idCliente))return ['error'=>['Cliente no encontrado.']];

        $direccion = new direcciones($datos);
        $direccion->idcliente = $idCliente;
        $direccion->validar();
        $alertas = $direccion->validarDireccion();
        if(!empty($alertas['error']))return $alertas;

        try {
            [$creada] = $this->repoDirecciones->insert($direccion);
            if(!$creada)throw new \RuntimeException('No fue posible crear la direccion.');
            $respuesta = ['exito'=>['Direccion registrada correctamente.']];
            if($devolverListado)
                $respuesta['direcciones'] = $this->repoDirecciones->conTarifaPorCliente($idCliente);
            return $respuesta;
        } catch (\Throwable $th) {
            $this->registrarErrorClientes('crear direccion', $th);
            return ['error'=>['Error al crear la direccion.']];
        }
    }


    public function preciospersonalizados(array $data):array{
        $alertas = [];
        $asociar = new preciosporcliente($data);
        $asociar->validar();
        $alertas = $asociar->validar_nuevo_cliente();

        // Las llaves foraneas se validan antes del INSERT para devolver un
        // mensaje de negocio y no exponer un error SQL al navegador.
        if((int)$asociar->idcliente <= 0 || (int)$asociar->idproducto <= 0)
            $alertas['error'][] = 'Cliente o producto no valido.';
        if(!$this->repoClientes->find((int)$asociar->idcliente))
            $alertas['error'][] = 'Cliente no encontrado.';

        if(empty($alertas))try {
            $existe = $this->repoPreciosCliente->findPorClienteProducto(
                (int)$asociar->idcliente,
                (int)$asociar->idproducto
            );
            if($existe){//actualizar
                $existe->precioxcliente = $asociar->precioxcliente;
                if(array_key_exists('estado', $data))$existe->estado = $data['estado'];
                $ra = $this->repoPreciosCliente->update($existe);
                if($ra){
                    $alertas['exito'][] = "Precio personalizado actualizado.";
                }else{
                    $alertas['error'][] = "Hubo un error, intentalo nuevamente";
                }
            }else{//crear
                [$creado] = $this->repoPreciosCliente->insert($asociar);
                if($creado){
                    $alertas['exito'][] = "Precio relacionado al cliente.";
                }else{
                    $alertas['error'][] = "Hubo un error, intentalo nuevamente";
                }
            }
        } catch (\Throwable $th) {
            $this->registrarErrorClientes('guardar precio personalizado', $th);
            $alertas['error'][] = 'Error al guardar el precio personalizado.';
        }
        return $alertas;
    }


    public function eliminarPrecioPersonalizado(int $idcliente, int $idproducto):array{
        $alertas = [];
        if(!$this->repoPreciosCliente->findPorClienteProducto($idcliente, $idproducto))
            return ['error'=>['El precio personalizado no existe.']];

        try {
            $r = $this->repoPreciosCliente->eliminarPorClienteProducto($idcliente, $idproducto);
            if($r){
                $alertas['exito'][] = "Precio personalizado eliminado correctamente.";
            }else{
                $alertas['error'][] = "Hubo un error, intentalo nuevamente";
            }
        } catch (\Throwable $th) {
            $this->registrarErrorClientes('eliminar precio personalizado', $th);
            $alertas['error'][] = 'Error al eliminar el precio personalizado.';
        }
        return $alertas;
    }


    /** Registra el detalle tecnico sin incluirlo en la respuesta de la API. */
    private function registrarErrorClientes(string $contexto, \Throwable $th):void
    {
        error_log("Clientes ({$contexto}): {$th->getMessage()}");
    }



    //////////////////////////////****NO****///////////////////////////////


    public function crearComision(int $idfacturaid, int $idusuariofk, float $valorfactura, float $percentcomision, float $valorcomision):void{
        $this->repoComisiones->insert(new comisiones(['idfacturaid'=>$idfacturaid, 'idusuariofk'=>$idusuariofk, 'valorfactura'=>$valorfactura, 'percentcomision'=>$percentcomision, 'valorcomision'=>$valorcomision]));
    
    }
    

    public function liquidarComision(array $data):array{
        $alertas = [];
        $getDB = $this->repoPagosComisiones->getConexion();
        $liquidar = new pagos_comisiones($data);

        $alertas = $liquidar->validar();
        if(!empty($alertas))return $alertas;

        $ultimocierre = cierrescajas::uniquewhereArray(['estado'=>0, 'idcaja'=>$_POST['idcaja'], 'idsucursal_id'=>id_sucursal()]); //ultimo cierre por caja
        if(!isset($ultimocierre)){ // si la caja esta cerrada y luego aqui se hace apertura
            $ultimocierre = new cierrescajas(['idcaja'=>$_POST['idcaja'], 'nombrecaja'=>caja::find('id', $_POST['idcaja'])->nombre, 'estado'=>0, 'idsucursal_id'=>id_sucursal()]);
            $r = $ultimocierre->crear_guardar();
            if(!$r[0])$ultimocierre->estado = 1; //si no secrea el nuevo cierre de caja, estse nuevo se coloca en 1 el estado
            $ultimocierre->id = $r[1];
        }

        if($ultimocierre->estado == 0){ 
            $liquidar->fkcierrecaja = $ultimocierre->id;
            $ingresoGasto = new gastos($_POST);
            $ingresoGasto->idg_usuario = $_SESSION['id'];
            $ingresoGasto->idg_cierrecaja = $ultimocierre->id;
            $ingresoGasto->idcategoriagastos = 11;
            if($_POST['origengasto'] == 'gastocaja'){
                $ingresoGasto->idg_caja = $_POST['idcaja'];
                $ultimocierre->gastoscaja = $ultimocierre->gastoscaja + $ingresoGasto->valor;
            }else{ //si el gasto sale de un banco
                $ingresoGasto->idg_caja = $_POST['idcaja'];
                $ingresoGasto->id_banco = $_POST['idbanco'];
                $ultimocierre->gastosbanco = $ultimocierre->gastosbanco + $ingresoGasto->valor;
                $ingresoGasto->tipo_origen = 1; //1 = banco. origen del gasto es banco
            }
            ///// validar gastos en el modelo
            $alertas = $ingresoGasto->validar();
            if(empty($alertas)){
                $rig = $ingresoGasto->crear_guardar();
                if($rig[0]){
                    $ruc = $ultimocierre->actualizar();
                    if(!$ruc){
                        $alertas['error'][] = "error al actualizar los gastos en el cierre de caja actual";
                        /// borrar ultimo registro guardado de $ingresocaja
                        $ingresoGasto->eliminar_idregistros('id', [$rig[1]]);
                        return $alertas;
                    }
                }else{
                    $alertas['error'][] = "Error al guardar el gasto de dinero";
                    return $alertas;
                }
            }
        }

        $getDB->begin_transaction();
        try {
            $rpc = $this->repoPagosComisiones->insert($liquidar);
            $getDB->commit();
            //actualizar el gasto creado con el id de pagos_comisiones
            $gastoComision = gastos::find('id', $rig[1]);
            $gastoComision->idpagos_comisiones = $rpc[1];
            $gastoComision->actualizar();
            return $rpc;
        } catch (\Throwable $th) {
            $getDB->rollback();
            throw $th;
        }
    }

    
}
