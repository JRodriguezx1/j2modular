<?php

namespace App\services\configuracion;

use App\Models\configuraciones\bancos;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\deviceprinter;
use App\Models\configuraciones\emisores;
use App\Models\configuraciones\mediospago;
use App\Models\configuraciones\notificacionesws;
use App\Models\configuraciones\tarifas;
use App\Models\configuraciones\tipofacturador;
use App\Models\configuraciones\usuarios;
use App\Models\clientes\departments;
use App\Models\felectronicas\diancompanias;
use App\Models\parametrizacion\config_local;
use App\Models\sucursales;
use App\Policies\EmpleadoPolicy;
use App\Repositories\suscripcioncuenta\suscripcionPagosRepository;
use InvalidArgumentException;

final class ConfiguracionPaginaService{

    private suscripcionPagosRepository $suscripcionPagosRepository;

    public function __construct(?suscripcionPagosRepository $suscripcionPagosRepository = null){
        $this->suscripcionPagosRepository = $suscripcionPagosRepository ?? new suscripcionPagosRepository();
    }

    public function obtenerDatos(int $idSucursal, int $perfilActor): array{
        if($idSucursal <= 0)throw new InvalidArgumentException('La sucursal actual no es valida.');

        $emisores = emisores::whereArray(['idsucursal'=>$idSucursal]);
        $empleados = usuarios::whereArray(['idsucursal'=>$idSucursal, 'confirmado'=>1]);
        $empleados = array_values(array_filter($empleados, static fn($empleado):bool=>EmpleadoPolicy::puedeGestionarEmpleado($perfilActor, (int)$empleado->perfil)));

        $cajas = caja::whereArray(['idsucursalid'=>$idSucursal, 'estado'=>1]);
        $consecutivos = consecutivos::whereArray(['id_sucursalid'=>$idSucursal, 'estado'=>1]);
        $tipofacturadores = tipofacturador::all();

        $mapConsecutivo = array_column($consecutivos, 'nombre', 'id');
        foreach($cajas as $caja)$caja->nombreconsecutivo = $mapConsecutivo[$caja->idtipoconsecutivo] ?? '';
        $mapTipoFactura = array_column($tipofacturadores, 'nombre', 'id');
        foreach($consecutivos as $consecutivo)$consecutivo->nombretipofacturador = $mapTipoFactura[$consecutivo->idtipofacturador] ?? '';

        return [
            'empleados'=>$empleados,
            'emisores'=>$emisores,
            'nombreEmisores'=>array_column($emisores, 'nombre', 'id'),
            'cajas'=>$cajas,
            'facturadores'=>$consecutivos,
            'tipofacturadores'=>$tipofacturadores,
            'bancos'=>bancos::all(),
            'tarifas'=>tarifas::all(),
            'departments'=>departments::all(),
            'companias'=>diancompanias::all(),
            'mediospago'=>mediospago::all(),
            'impresoras'=>deviceprinter::whereArray(['estado'=>1]),
            'conflocal'=>config_local::getParamGlobal(),
            'contactsNotificationWS'=>notificacionesws::whereArray(['sucursal_idfk'=>$idSucursal, 'estado'=>1]),
            'suscripcionPagos'=>$this->suscripcionPagosRepository->all(),
            'sucursales'=>sucursales::all(),
        ];
    }
    
}
