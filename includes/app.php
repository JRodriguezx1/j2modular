<?php 

use App\Core\Tenant\TenantResolver;
use App\Core\Tenant\TenantConfig;
use Dotenv\Dotenv;   //variables de entorno para el deploy
use App\Models\ActiveRecord;  //importa el archivo de modelo para la gestion de bd mysql
use App\Repositories\BaseRepository;

require __DIR__ . '/../vendor/autoload.php';  //localizar las clases

// Añadir Dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require 'funciones.php';  //archivo de funciones debuguear y sanitizar html
$tenantResolver = new TenantResolver();
$tenant = $tenantResolver->resolve(); //me obtiene el subdominio ej: negociobelen
$tenantConfig = new TenantConfig();
$config = $tenantConfig->get($tenant); //obitenemos la config de /config/tenants.php ej: 'cliente'=>['database'=>'contapos', 'modules'=>['Pos',]]
$tenantDatabase = $config['database'];

require 'database.php';    //archivo de conexion de bd mysql con variables de entorno

// Conectarnos a la base de datos
ActiveRecord::setDB($db); //llama al modelo o clase ActiveRecord y a su metodo setDB y se le pasa la conexion $db definido dentro database.php
BaseRepository::setDB($db);
//setDB es publico estatic no requiere instanciarse para acceder a este metodo