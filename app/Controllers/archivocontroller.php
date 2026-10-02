<?php

namespace App\Controllers;

use App\Models\sucursales;

class archivocontroller{

    private static string $basePath = __DIR__ . '/../../public/build/documentos';
    private static string $PathIng = __DIR__ . '/../../public/build/img/';

    public static function descargarExcel()
    {
        $nombre = $_GET['file'] ?? 'Importar-productos.xlsx';
        $ruta = self::$basePath . '/' . $nombre;

        if (!file_exists($ruta)) {
            http_response_code(404);
            echo "Archivo Excel no encontrado.";
            return;
        }

        if(ob_get_level())ob_end_clean();

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"" . basename($ruta) . "\"");
        header("Content-Length: " . filesize($ruta));
        
        readfile($ruta);
        exit;
    }


    public static function descargarInsumosExcel()
    {
        $nombre = $_GET['file'] ?? 'Importar-insumos.xlsx';
        $ruta = self::$basePath . '/' . $nombre;

        if (!file_exists($ruta)) {
            http_response_code(404);
            echo "Archivo Excel no encontrado.";
            return;
        }

        if(ob_get_level())ob_end_clean();

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"" . basename($ruta) . "\"");
        header("Content-Length: " . filesize($ruta));
        
        readfile($ruta);
        exit;
    }


    public static function descargarInstrucciones()
    {
        $nombre = $_GET['file'] ?? 'instrucciones_importar_productos.png';
        $ruta = self::$basePath . '/' . $nombre;

        if (!file_exists($ruta)) {
            http_response_code(404);
            echo "Archivo no encontrado.";
            return;
        }

        if(ob_get_level())ob_end_clean();

        header("Content-Type: image/png");
        //header("Content-Disposition: attachment; filename=\"" . basename($ruta) . "\"");
        header("Content-Length: " . filesize($ruta));
        //header("Cache-Control: no-cache");
        
        readfile($ruta);
        exit;
    }


    public static function descargarLogo()
    {
        $sucursal = sucursales::find('id', id_sucursal());
        $ruta = self::$PathIng.$sucursal->logo??'Logoj2negro.png';

        if (!file_exists($ruta)) {
            http_response_code(404);
            echo "Archivo no encontrado.";
            return;
        }
        if(ob_get_level())ob_end_clean();
        header("Content-Type: image/png");
        header("Content-Length: " . filesize($ruta));
        readfile($ruta);
        exit;
    }
}