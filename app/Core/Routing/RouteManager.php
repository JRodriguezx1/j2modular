<?php

namespace App\Core\Routing;

class RouteManager
{
    private RouteLoader $loader;
    public function __construct(RouteLoader $loader){
        $this->loader = $loader;
    }

    /*public function loadCoreRoutes(): void{
        $this->loader->load(dirname(__DIR__) . '/Auth/routes.php');
    }*/

    /**
     * Carga las rutas de un modulo.
     */
    public function loadModule(string $module): void{
        $file = dirname(__DIR__, 2). "/Modules/{$module}/routes.php";
        $this->loader->load($file); // load de la clase loader hace requiere al archivo routes.php segun el modulo configurado
    }

    /**
     * Carga varios modulos.
     */
    public function loadModules(array $modules): void{
        foreach ($modules as $module) $this->loadModule($module);
    }

}