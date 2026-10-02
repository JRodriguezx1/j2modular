<?php

namespace App\DTO\configuracion;

use App\Models\configuraciones\usuarios;
use JsonSerializable;

final class EmpleadoResponseDTO implements JsonSerializable{

    /**
     * @param array<int, array{id:string, usuarioid:string, permisoid:string}> $idusuariospermisos
     * @param array<int, array{id:string, nombre:string}> $permisos
     */
    public function __construct(
        public readonly string $id,
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $nickname,
        public readonly string $cedula,
        public readonly string $movil,
        public readonly string $email,
        public readonly string $departamento,
        public readonly string $ciudad,
        public readonly string $direccion,
        public readonly string $perfil,
        public readonly string $porcentajeganancia,
        public readonly string $img,
        public readonly array $idusuariospermisos,
        public readonly array $permisos,
    ) {}


    /** @param object[] $relaciones */
    public static function desdeModelo(usuarios $empleado, array $relaciones): self{
        $permisos = array_map(static fn(object $relacion):array=>['id'=>(string)$relacion->id, 'nombre'=>(string)$relacion->nombre], $relaciones);

        $idusuariospermisos = array_map(
            static fn(object $relacion):array=>['id'=>(string)$relacion->ID, 'usuarioid'=>(string)$relacion->usuarioid, 'permisoid'=>(string)$relacion->permisoid], 
            $relaciones
        );

        return new self(
            id: (string)$empleado->id,
            nombre: (string)$empleado->nombre,
            apellido: (string)$empleado->apellido,
            nickname: (string)$empleado->nickname,
            cedula: (string)$empleado->cedula,
            movil: (string)$empleado->movil,
            email: (string)$empleado->email,
            departamento: (string)($empleado->departamento ?? ''),
            ciudad: (string)$empleado->ciudad,
            direccion: (string)$empleado->direccion,
            perfil: (string)$empleado->perfil,
            porcentajeganancia: (string)$empleado->porcentajeganancia,
            img: (string)$empleado->img,
            idusuariospermisos: $idusuariospermisos,
            permisos: $permisos,
        );
    }

    //este metodo se llama debido a la implementacion de la interfaz JsonSerializable, 
    //que permite convertir el objeto en un array con las propiedades exactas a retornar para luego ser convertido a JSON
    public function jsonSerialize(): array{
        return [
            'id'=>$this->id,
            'nombre'=>$this->nombre,
            'apellido'=>$this->apellido,
            'nickname'=>$this->nickname,
            'cedula'=>$this->cedula,
            'movil'=>$this->movil,
            'email'=>$this->email,
            'departamento'=>$this->departamento,
            'ciudad'=>$this->ciudad,
            'direccion'=>$this->direccion,
            'perfil'=>$this->perfil,
            'porcentajeganancia'=>$this->porcentajeganancia,
            'img'=>$this->img,
            'idusuariospermisos'=>$this->idusuariospermisos,
            'permisos'=>$this->permisos,
        ];
    }
}
