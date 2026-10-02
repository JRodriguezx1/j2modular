<?php

namespace App\services\configuracion;

use App\Models\configuraciones\deviceprinter as ImpresoraModel;
use InvalidArgumentException;
use Throwable;

final class ImpresorasConfiguracionService{

    private const CAMPOS_EDITABLES = ['nombre', 'nombrecompartido', 'estacion', 'mm'];

    /** @return ImpresoraModel[] */
    public function listarImpresoras(): array{
        return ImpresoraModel::all();
    }

    public function crearImpresora(array $datos): array{
        $impresora = new ImpresoraModel($this->obtenerDatosEditables($datos));
        $alertas = $impresora->validar();
        if(!empty($alertas['error']))return $alertas;

        try{
            [$creada, $idImpresora] = $impresora->crear_guardar();
            if(!$creada)return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];

            $impresora->id = (int)$idImpresora;
            $impresora->created_at = date('Y-m-d H:i:s');
            return ['exito'=>['Impresora creada correctamente'], 'printer'=>$impresora];
        }catch(Throwable $error){
            error_log('Error al crear impresora: '.$error->getMessage());
            return ['error'=>['Hubo un error en el proceso, intentalo nuevamente']];
        }
    }

    public function actualizarImpresora(int $idImpresora, array $datos): array{
        $this->validarId($idImpresora);
        try{
            $impresora = ImpresoraModel::find('id', $idImpresora);
            if(!$impresora)throw new InvalidArgumentException('Impresora no encontrada.', 404);

            $impresora->compara_objetobd_post($this->obtenerDatosEditables($datos));
            $impresora->id = $idImpresora;
            $alertas = $impresora->validar();
            if(!empty($alertas['error']))return $alertas;

            if(!$impresora->actualizar())return ['error'=>['Error al actualizar la impresora']];

            return ['exito'=>['Datos de la impresora actualizados'], 'printer'=>$impresora];
        }catch(InvalidArgumentException $error){
            throw $error;
        }catch(Throwable $error){
            error_log('Error al actualizar impresora '.$idImpresora.': '.$error->getMessage());
            return ['error'=>['Error al actualizar la impresora']];
        }
    }

    public function eliminarImpresora(int $idImpresora): array{
        $this->validarId($idImpresora);
        try{
            $impresora = ImpresoraModel::find('id', $idImpresora);
            if(!$impresora)throw new InvalidArgumentException('Impresora no encontrada.', 404);

            if(!$impresora->eliminar_registro())return ['error'=>['Error en el proceso de eliminacion']];
            return ['exito'=>['Impresora eliminada correctamente']];
        }catch(InvalidArgumentException $error){
            throw $error;
        }catch(Throwable $error){
            error_log('Error al eliminar impresora '.$idImpresora.': '.$error->getMessage());
            return ['error'=>['Error en el proceso de eliminacion']];
        }
    }

    private function validarId(int $idImpresora): void{
        if($idImpresora <= 0)throw new InvalidArgumentException('Identificador de impresora invalido.', 422);
    }

    private function obtenerDatosEditables(array $datos): array{
        return array_intersect_key($datos, array_flip(self::CAMPOS_EDITABLES));
    }
}
