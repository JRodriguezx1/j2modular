<?php

namespace App\Policies;

/**
 * Reglas de jerarquia para la administracion de empleados.
 *
 * Un numero de perfil menor representa mayor autoridad. El perfil maestro
 * queda fuera de la administracion normal de empleados y no puede asignarse
 * desde este modulo.
 */
final class EmpleadoPolicy{
    
    public const PERFIL_MAESTRO = 1;
    public const PERFIL_SUPERVISOR = 2;
    public const PERFIL_ADMINISTRADOR = 3;
    public const PERFIL_ASESOR = 4;

    private const PERFILES_ASIGNABLES = [

        self::PERFIL_MAESTRO => [self::PERFIL_SUPERVISOR, self::PERFIL_ADMINISTRADOR, self::PERFIL_ASESOR],
        
        self::PERFIL_SUPERVISOR => [self::PERFIL_SUPERVISOR, self::PERFIL_ADMINISTRADOR, self::PERFIL_ASESOR],
        
        self::PERFIL_ADMINISTRADOR => [self::PERFIL_ADMINISTRADOR, self::PERFIL_ASESOR],
        
        self::PERFIL_ASESOR => [self::PERFIL_ASESOR],
    ];


    /** @return int[] */
    public static function perfilesAsignables(int $perfilActor): array{
        return self::PERFILES_ASIGNABLES[$perfilActor] ?? [];
    }

    public static function puedeAsignarPerfil(int $perfilActor, int $perfilSolicitado): bool{
        return in_array($perfilSolicitado, self::perfilesAsignables($perfilActor), true);
    }

    public static function puedeGestionarEmpleado(int $perfilActor, int $perfilEmpleado): bool{
        return self::puedeAsignarPerfil($perfilActor, $perfilEmpleado);
    }

    public static function puedeEliminarEmpleado(int $idActor, int $perfilActor, int $idEmpleado, int $perfilEmpleado): bool {
        if($idActor === $idEmpleado)
            return false;

        return self::puedeGestionarEmpleado($perfilActor, $perfilEmpleado);
    }

}
