<?php

namespace App\classes;

use App\Repositories\creditos\cuotasRepository;
use App\Repositories\creditos\productsSeparadosRepository;


class serviceLocatorApp{

    private static $db = null;
    private static array $instance = [];

    /*public static function getDB(){
        if(self::$db == null){
            $db = mysqli_connect(
                $_ENV['DB_HOST'] ?? '',
                $_ENV['DB_USER'] ?? '', 
                $_ENV['DB_PASS'] ?? '', 
                /*$_ENV['DB_NAME'] ?? ''*//*$selectDB['namedb']
            );
            mysqli_set_charset($db, "utf8");
            if (!$db) {
                echo "Error: No se pudo conectar a MySQL.";
                echo "errno de depuración: " . mysqli_connect_errno();
                echo "error de depuración: " . mysqli_connect_error();
                exit;
            }
        }
        return self::$db;
    }*/

    public static function repoVirtual($tabla){

    }

    public static function getCuotasRepo():cuotasRepository{
        if(!isset(static::$instance['cuotaRepo'])){
            self::$instance['cuotaRepo'] = new cuotasRepository(/*self::$db*/);
        }
        return self::$instance['cuotaRepo'];
    }


    public static function getproductsSeparadosRepo():productsSeparadosRepository{
        if(!isset(static::$instance['productsSeparadosRepo'])){
            self::$instance['productsSeparadosRepo'] = new productsSeparadosRepository();
        }
        return self::$instance['productsSeparados'];
    }
}