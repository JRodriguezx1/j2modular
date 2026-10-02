<?php

namespace App\services\configuracion;

use App\Models\configuraciones\usuarios;
use App\Models\configuraciones\usuarios_permisos;
use App\Policies\EmpleadoPolicy;
use RuntimeException;
use Throwable;

final class EmpleadosConfiguracionService{

    public function __construct(
        private ImagenStorage $avatarStorage
    ) {}

    public function crearEmpleado(array $datos, array $permisosSolicitados, ?array $archivoImagen, int $idSucursal, int $perfilActor): array {
        $rutaimgabsoluta = '';
        $rutaimgrelativa = '';
        $perfilSolicitado = filter_var($datos['perfil'] ?? null, FILTER_VALIDATE_INT);

        if($perfilSolicitado === false || !EmpleadoPolicy::puedeAsignarPerfil($perfilActor, (int)$perfilSolicitado))
            return ['error'=>['No tiene autorizacion para asignar este perfil.']];

        $camposPermitidos = [
            'nombre',
            'apellido',
            'cedula',
            'nickname',
            'movil',
            'email',
            'ws',
            'password',
            'password2',
            'ciudad',
            'direccion',
            'fecha_nacimiento',
            'porcentajeganancia',
        ];
        // array_intersect_key obtiene los elementos de datos cuya llave esten en array_flip(), array_flip convierte los valores en claves
        $datosEmpleado = array_intersect_key($datos, array_flip($camposPermitidos));
        $datosEmpleado['perfil'] = (int)$perfilSolicitado;
        $datosEmpleado['idsucursal'] = $idSucursal;

        $empleado = new usuarios($datosEmpleado);
        $empleado->validar();
        $alertas = $empleado->validarempleado();

        if(!empty($alertas['error']))return $alertas;

        // Por ahora solo se normalizan los IDs; la base de datos protege
        // la integridad referencial si llega un permiso inexistente.
        $idsPermisos = [];
        foreach($permisosSolicitados as $id){
            $id = (int) $id;
            if($id > 0)$idsPermisos[$id] = $id;
        }
        $idsPermisos = array_values($idsPermisos);

        $db = usuarios::getDB();
        $db->begin_transaction();
        try{
            [$rutaimgabsoluta, $rutaimgrelativa] = $this->avatarStorage->guardar($archivoImagen);
            $empleado->img = $rutaimgrelativa;
            $empleado->confirmado = 1;
            $empleado->hashPassword();
            [$usuarioCreado, $idEmpleado] = $empleado->crear_guardar();
            if(!$usuarioCreado)throw new RuntimeException('No fue posible crear el empleado.');

            if($idsPermisos !== []){
                $registrosPermisos = array_map(static fn(int $idPermiso):array=>['usuarioid'=>(int)$idEmpleado, 'permisoid'=>$idPermiso], $idsPermisos);

                $resultadoPermisos = (new usuarios_permisos())->crear_varios_reg($registrosPermisos);
                if(!($resultadoPermisos[0] ?? false))throw new RuntimeException('No fue posible asignar los permisos.');
            }

            $db->commit();
            return ['exito'=>['Usuario creado correctamente.'], 'idEmpleado'=>(int)$idEmpleado];
        }catch (\InvalidArgumentException $error) {
            $db->rollback();
            $this->avatarStorage->eliminar($rutaimgrelativa);
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            $db->rollback();
            $this->avatarStorage->eliminar($rutaimgrelativa);
            error_log('Error al crear empleado: '.$error->getMessage());
            $mensaje = str_contains($error->getMessage(), 'Duplicate entry')
                ? 'Ya existe un usuario con los datos suministrados.'
                : 'No fue posible crear el empleado.';

            return ['error'=>[$mensaje]];
        }
    }


    public function actualizarEmpleado(int $idEmpleado, array $datos, array $permisosSolicitados, ?array $archivoImagen, int $idSucursal, int $perfilActor): array {
        if($idEmpleado <= 0)
            return ['error'=>['El identificador del empleado no es valido.']];

        // Por ahora solo se normalizan y deduplican los IDs. La llave
        // foranea protege la integridad si se recibe un permiso inexistente.
        $idsPermisos = [];
        foreach($permisosSolicitados as $idPermiso){
            $idPermiso = (int)$idPermiso;
            if($idPermiso > 0)$idsPermisos[$idPermiso] = true;
        }

        $rutaNuevaAbsoluta = '';
        $rutaNuevaRelativa = '';
        $fotoAnterior = '';
        $empleado = null;
        $db = usuarios::getDB();
        $db->begin_transaction();

        try{
            $empleado = usuarios::findForUpdate('id', $idEmpleado);
            if(!$empleado || (int)$empleado->idsucursal !== $idSucursal)
                throw new \InvalidArgumentException('Empleado no encontrado en la sucursal actual.');

            if(!EmpleadoPolicy::puedeGestionarEmpleado($perfilActor, (int)$empleado->perfil))
                throw new \InvalidArgumentException('No tiene autorizacion para administrar este empleado.');

            $perfilSolicitado =  filter_var($datos['perfil'], FILTER_VALIDATE_INT);

            if($perfilSolicitado === false || !EmpleadoPolicy::puedeAsignarPerfil($perfilActor, (int)$perfilSolicitado))
                throw new \InvalidArgumentException('No tiene autorizacion para asignar este perfil.');

            $camposEditables = [
                'nombre',
                'apellido',
                'cedula',
                'nickname',
                'movil',
                'email',
                'ws',
                'ciudad',
                'direccion',
                'fecha_nacimiento',
                'porcentajeganancia',
            ];

            $datosEditables = array_intersect_key($datos, array_flip($camposEditables));
            $datosEditables['perfil'] = (int)$perfilSolicitado;
            $fotoAnterior = (string)$empleado->img;

            $empleado->compara_objetobd_post($datosEditables);
            $empleado->id = $idEmpleado;
            $empleado->idsucursal = $idSucursal;
            $empleado->validar();
            $alertas = $empleado->validarempleadoexistente();
            if(!empty($alertas['error']))
                throw new \InvalidArgumentException(implode(' ', $alertas['error']));

            [$rutaNuevaAbsoluta, $rutaNuevaRelativa] = $this->avatarStorage->guardar($archivoImagen);
            if($rutaNuevaRelativa !== '')$empleado->img = $rutaNuevaRelativa;

            if(!$empleado->actualizar())
                throw new RuntimeException('No fue posible actualizar el empleado.');

            // Se consultan y bloquean las relaciones sin cargar cada permiso.
            $relacionesActuales = usuarios_permisos::idregistrosForUpdate('usuarioid', $idEmpleado, false);
            $relacionPorPermiso = [];
            foreach ($relacionesActuales as $relacion)
                $relacionPorPermiso[(int)$relacion->permisoid] = (int)$relacion->id;

            $idsRelacionesEliminar = [];
            foreach($relacionPorPermiso as $idPermiso=>$idRelacion)
                if(!isset($idsPermisos[$idPermiso]))$idsRelacionesEliminar[] = $idRelacion;

            $registrosCrear = [];
            foreach($idsPermisos as $idPermiso=>$_)
                if(!isset($relacionPorPermiso[$idPermiso]))
                    $registrosCrear[] = ['usuarioid'=>$idEmpleado, 'permisoid'=>$idPermiso];

            if($idsRelacionesEliminar !== [] && !usuarios_permisos::eliminar_idregistros('id', $idsRelacionesEliminar))
                throw new RuntimeException('No fue posible retirar los permisos.');

            if($registrosCrear !== []){
                $resultadoPermisos = (new usuarios_permisos())->crear_varios_reg($registrosCrear);
                if(!($resultadoPermisos[0] ?? false))
                    throw new RuntimeException('No fue posible asignar los permisos.');
            }

            if(!$db->commit())
                throw new RuntimeException('No fue posible confirmar la actualizacion.');
        }catch(\InvalidArgumentException $error){
            $db->rollback();
            $this->avatarStorage->eliminar($rutaNuevaRelativa);
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            $db->rollback();
            $this->avatarStorage->eliminar($rutaNuevaRelativa);
            error_log('Error al actualizar empleado: '.$error->getMessage());

            $mensaje = str_contains($error->getMessage(), 'Duplicate entry')
                ? 'Ya existe un usuario con los datos suministrados.'
                : 'No fue posible actualizar el empleado.';

            return ['error'=>[$mensaje]];
        }

        // El avatar anterior se elimina solo despues de confirmar la BD.
        if($rutaNuevaRelativa !== '' && $fotoAnterior !== '')$this->avatarStorage->eliminar($fotoAnterior);

        return ['exito'=>['Empleado actualizado correctamente.'], 'rutaimg'=>(string)$empleado->img];
    }


    public function eliminarEmpleado(int $idEmpleado, int $idSucursal, int $idActor, int $perfilActor): array{
        if($idEmpleado <= 0)return ['error'=>['El identificador del empleado no es valido.']];
        if($idSucursal <= 0 || $idActor <= 0)return ['error'=>['No fue posible determinar el contexto de la operacion.']];

        $fotoAnterior = '';
        $db = usuarios::getDB();
        $db->begin_transaction();

        try{
            $empleado = usuarios::findForUpdate('id', $idEmpleado);
            if(!$empleado || (int)$empleado->idsucursal !== $idSucursal)
                throw new \InvalidArgumentException('Empleado no encontrado en la sucursal actual.');

            if(!EmpleadoPolicy::puedeEliminarEmpleado($idActor, $perfilActor, (int)$empleado->id, (int)$empleado->perfil))
                throw new \InvalidArgumentException('No tiene autorizacion para eliminar este empleado.');

            $fotoAnterior = (string)$empleado->img;
            $relaciones = usuarios_permisos::idregistrosForUpdate('usuarioid', $idEmpleado, false);
            $idsRelaciones = array_map(static fn($relacion):int=>(int)$relacion->id, $relaciones);

            if($idsRelaciones !== [] && !usuarios_permisos::eliminar_idregistros('id', $idsRelaciones))
                throw new RuntimeException('No fue posible eliminar los permisos del empleado.');

            if(!$empleado->eliminar_registro())
                throw new RuntimeException('No fue posible eliminar el empleado.');

            if(!$db->commit())
                throw new RuntimeException('No fue posible confirmar la eliminacion del empleado.');
        }catch(\InvalidArgumentException $error){
            $db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al eliminar empleado: '.$error->getMessage());
            return ['error'=>['No fue posible eliminar el empleado.']];
        }

        if($fotoAnterior !== '')$this->avatarStorage->eliminar($fotoAnterior);
        return ['exito'=>['Empleado eliminado correctamente.']];
    }


    public function actualizarPassword(int $idEmpleado, string $password, int $idSucursal, int $perfilActor): array{
        if($idEmpleado <= 0)
            return ['error'=>['El identificador del empleado no es valido.']];
        if($idSucursal <= 0)
            return ['error'=>['No fue posible determinar la sucursal actual.']];
        if(strlen($password) > 60)
            return ['error'=>['El password no puede superar los 60 caracteres de longitud.']];
        if(strlen($password) < 3)
            return ['error'=>['El password es muy corto.']];

        $db = usuarios::getDB();
        $db->begin_transaction();

        try{
            $empleado = usuarios::findForUpdate('id', $idEmpleado);
            if(!$empleado || (int)$empleado->idsucursal !== $idSucursal)
                throw new \InvalidArgumentException('Empleado no encontrado en la sucursal actual.');
            if(!EmpleadoPolicy::puedeGestionarEmpleado($perfilActor, (int)$empleado->perfil))
                throw new \InvalidArgumentException('No tiene autorizacion para cambiar la contrasena de este empleado.');

            $empleado->password = $password;
            $empleado->hashPassword();

            if(!$empleado->actualizar())
                throw new RuntimeException('No fue posible actualizar la contrasena del empleado.');

            if(!$db->commit())
                throw new RuntimeException('No fue posible confirmar el cambio de contrasena.');
        }catch(\InvalidArgumentException $error){
            $db->rollback();
            return ['error'=>[$error->getMessage()]];
        }catch(Throwable $error){
            $db->rollback();
            error_log('Error al actualizar password de empleado: '.$error->getMessage());
            return ['error'=>['No fue posible cambiar la contrasena del empleado. '.$error->getMessage()]];
        }
        return ['exito'=>['Password cambiado correctamente.']];
    }

}
