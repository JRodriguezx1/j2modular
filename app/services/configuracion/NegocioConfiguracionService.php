<?php

namespace App\services\configuracion;

use App\Models\sucursales;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class NegocioConfiguracionService{

    private const CAMPOS_EDITABLES = [
        'negocio',
        'nombre',
        'nit',
        'departamento',
        'ciudad',
        'direccion',
        'telefono',
        'movil',
        'email',
        'datosencabezados',
        'www',
        'ws',
        'facebook',
        'instagram',
        'tiktok',
        'youtube',
        'host',
        'timezone',
    ];

    public function __construct(private ImagenStorage $logoStorage){

    }

    /** @return array{sucursal:sucursales|null, alertas:array} */
    public function guardarNegocio(array $datos, ?array $archivoLogo, int $idSucursal): array{
        if($idSucursal <= 0)
            return ['sucursal'=>null, 'alertas'=>['error'=>['La sucursal actual no es valida.']]];

        $sucursal = sucursales::find('id', $idSucursal);
        $esNueva = !$sucursal;
        $datosEditables = array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));

        if($esNueva){
            $sucursal = new sucursales($datosEditables);
        }else{
            $sucursal->compara_objetobd_post($datosEditables);
        }

        $alertas = $sucursal->validar();
        if(!empty($alertas['error']))return ['sucursal'=>$sucursal, 'alertas'=>$alertas];

        $rutaNuevaRelativa = '';
        $logoAnterior = (string)$sucursal->logo;

        try{
            [, $rutaNuevaRelativa] = $this->logoStorage->guardar($archivoLogo);
            if($rutaNuevaRelativa !== '')$sucursal->logo = $rutaNuevaRelativa;

            if($esNueva){
                [$guardada, $idCreado] = $sucursal->crear_guardar();
                if(!$guardada)throw new RuntimeException('No fue posible crear la sucursal.');
                $sucursal->id = (int)$idCreado;
            }elseif(!$sucursal->actualizar()){
                throw new RuntimeException('No fue posible actualizar la sucursal.');
            }
        }catch(InvalidArgumentException $error){
            $this->logoStorage->eliminar($rutaNuevaRelativa);
            $sucursal->logo = $logoAnterior;
            return ['sucursal'=>$sucursal, 'alertas'=>['error'=>[$error->getMessage()]]];
        }catch(Throwable $error){
            $this->logoStorage->eliminar($rutaNuevaRelativa);
            $sucursal->logo = $logoAnterior;
            error_log('Error al guardar la configuracion del negocio: '.$error->getMessage());
            return ['sucursal'=>$sucursal, 'alertas'=>['error'=>['No fue posible guardar los datos de la sucursal.']]];
        }

        if($rutaNuevaRelativa !== '' && $logoAnterior !== '')$this->logoStorage->eliminar($logoAnterior);
        return ['sucursal'=>$sucursal, 'alertas'=>['exito'=>['Datos de la sucursal actualizado']]];
    }
}
