<?php

namespace App\services\configuracion;

use InvalidArgumentException;
use RuntimeException;

final class ImagenStorage{

    private const EXTENSIONES = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
    private string $documentRoot;
    private string $cliente;
    private string $subdirectorio;
    private string $prefijoNombre;

    public function __construct( string $documentRoot, string $cliente, string $subdirectorio, string $prefijoNombre, private int $maxBytes){
        $this->documentRoot = rtrim($documentRoot, '/\\');
        $this->cliente = strtolower(trim($cliente));
        $this->subdirectorio = trim(str_replace('\\', '/', strtolower($subdirectorio)), '/');
        $this->prefijoNombre = trim($prefijoNombre);

        if($this->documentRoot === '')
            throw new InvalidArgumentException('El directorio publico no es valido.');

        if($this->cliente === '' || !preg_match('/^[a-z0-9-]+$/', $this->cliente))
            throw new InvalidArgumentException('El identificador del cliente no es valido.');

        if($this->subdirectorio !== '' && !preg_match('/^[a-z0-9-]+(?:\/[a-z0-9-]+)*$/', $this->subdirectorio))
            throw new InvalidArgumentException('El subdirectorio de imagenes no es valido.');

        if($this->prefijoNombre === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $this->prefijoNombre))
            throw new InvalidArgumentException('El prefijo del nombre de imagen no es valido.');

        if($this->maxBytes <= 0)
            throw new InvalidArgumentException('El tamano maximo de imagen no es valido.');
    }


    /** @return array{0:string, 1:string} Ruta absoluta y ruta relativa. */
    public function guardar(?array $imagen): array{
        $alertas = $this->validar($imagen);
        if($alertas ===  [])return ['', ''];

        if(!empty($alertas['error']))
            throw new InvalidArgumentException(implode(' ', $alertas['error']));

        $directorioRelativo = $this->directorioRelativo();
        $directorioAbsoluto = $this->documentRoot.'/build/img/'.$directorioRelativo;
        if(!is_dir($directorioAbsoluto) && !mkdir($directorioAbsoluto, 0755, true))
            throw new RuntimeException('No fue posible preparar el directorio de imagenes.');

        $extension = $alertas['extension'];
        $nombreArchivo = sprintf('%s%s.%s', $this->prefijoNombre, bin2hex(random_bytes(16)), $extension);
        $rutaRelativa = $directorioRelativo.'/'.$nombreArchivo;
        $rutaAbsoluta = $directorioAbsoluto.DIRECTORY_SEPARATOR.$nombreArchivo;

        if(!move_uploaded_file((string)$imagen['tmp_name'], $rutaAbsoluta))
            throw new RuntimeException('No fue posible guardar la imagen.');

        if(function_exists('desactivarInterlacedPNG'))
            \desactivarInterlacedPNG($rutaAbsoluta);

        return [$rutaAbsoluta, $rutaRelativa];
    }

    public function eliminar(string $rutaRelativa): void{
        if($rutaRelativa === '')return;
        $rutaRelativa = ltrim(str_replace('\\', '/', $rutaRelativa), '/'); //cliente1/avatar/imagen.png
        $directorioRelativo = $this->directorioRelativo();  //cliente1/avatar
        $prefijoEsperado = $directorioRelativo.'/'; //cliente1/avatar/
        if(!str_starts_with($rutaRelativa, $prefijoEsperado)){
            error_log('Intento de eliminar una imagen fuera del directorio permitido: '.$rutaRelativa);
            return;
        }

        $nombreArchivo = substr($rutaRelativa, strlen($prefijoEsperado));
        if($nombreArchivo === '' || $nombreArchivo === '.' || $nombreArchivo === '..' || str_contains($nombreArchivo, '/')){
            error_log('Ruta de imagen no valida: '.$rutaRelativa);
            return;
        }

        $directorioReal = realpath($this->documentRoot.'/build/img/'.$directorioRelativo);
        $archivoReal = realpath($this->documentRoot.'/build/img/'.$rutaRelativa);
        if($directorioReal === false || $archivoReal === false)return;

        $prefijoAbsoluto = rtrim($directorioReal, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(!str_starts_with($archivoReal, $prefijoAbsoluto)){
            error_log('Intento de eliminar una imagen fuera del directorio permitido: '.$rutaRelativa);
            return;
        }

        if(is_file($archivoReal) && !unlink($archivoReal))
            error_log('No fue posible eliminar la imagen: '.$rutaRelativa);
    }


    private function directorioRelativo(): string{
        return $this->subdirectorio === '' ? $this->cliente : $this->cliente.'/'.$this->subdirectorio;
    }


    public function validar(?array $imagen): array{
        if($imagen === null || (int)($imagen['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
            return [];

        $error = (int)($imagen['error'] ?? UPLOAD_ERR_NO_FILE);
        if($error !== UPLOAD_ERR_OK)
            return ['error' => [$this->mensajeErrorSubida($error)]];

        $rutaTemporal = (string)($imagen['tmp_name'] ?? '');
        if($rutaTemporal === '' || !is_uploaded_file($rutaTemporal))
            return ['error' => ['El archivo recibido no es una subida valida.']];

        $tamano = filesize($rutaTemporal);
        if($tamano === false)
            return ['error' => ['No fue posible determinar el tamaño de la imagen.']];
        if($tamano > $this->maxBytes)
            return ['error' => ['La imagen supera el tamaño maximo permitido.']];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($rutaTemporal);
        $mime = is_string($mime) ? $mime : '';
        if(!isset(self::EXTENSIONES[$mime]))
            return ['error' => ['La imagen debe estar en formato JPEG o PNG.']];

        $informacion = @getimagesize($rutaTemporal);
        if($informacion === false || ($informacion['mime'] ?? '') !== $mime)
            return ['error' => ['El archivo no contiene una imagen valida.']];

        return ['extension' => self::EXTENSIONES[$mime]];
    }

    private function mensajeErrorSubida(int $error): string{
        return match($error){
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE=>'La imagen supera el tamano maximo permitido.',
            UPLOAD_ERR_PARTIAL=>'La imagen no termino de cargarse.',
            UPLOAD_ERR_NO_TMP_DIR=>'No esta disponible el directorio temporal.',
            UPLOAD_ERR_CANT_WRITE=>'No fue posible escribir la imagen en el servidor.',
            UPLOAD_ERR_EXTENSION=>'La carga fue detenida por el servidor.',
            default=>'Ocurrio un error durante la carga de la imagen.',
        };
    }
}
