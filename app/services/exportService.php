<?php 

namespace App\services;

use App\Models\ActiveRecord;


class exportService {    

    public static function exportproducts(array $datos, string $filename):void{
        if (empty($datos))exit('No hay datos para exportar.');
        while (ob_get_level())ob_end_clean();
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename");
        // BOM UTF-8
        echo "\xEF\xBB\xBF";
        $salida = fopen('php://output', 'w');
        $mostrarColumnas = false;
        foreach($datos as $value){
             $value = (array)$value;
            if(!$mostrarColumnas){
                fputcsv($salida, array_keys($value), ';');
                $mostrarColumnas = true;
            }
            fputcsv($salida, array_values($value), ';');
        }
        fclose($salida);
        exit;
    }



}