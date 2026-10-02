<?php

namespace App\Core\Modules;

use App\Core\Container\Container;
use App\Core\Routing\RouteManager;

class ModuleManager{
    public function __construct(private Container $container, private RouteManager $routeManager){
    }

    public function register(array $modules): void{
        foreach ($modules as $module) {
            $this->registerModule($module);
        }
    }

    private function registerModule(string $module): void{
        $providerClass = "App\\Modules\\{$module}\\Providers\\{$module}ServiceProvider";

        if(!class_exists($providerClass))
            return;
        
        $provider = new $providerClass(); //obtiene la clase del provider
        $provider->register($this->container); //registra la abstracion e implementacion para el contaier
    }


    public function loadRoutes(array $modules): void{
        $this->routeManager->loadModules($modules); //me va cargando las rutas definidas en "/Modules/{$module}/routes.php"; el cual por medio de loader va haceindo un requiere a cada archivo de rutas
    }

}