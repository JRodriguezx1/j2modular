<?php

namespace App\Core\Routing;

class RouteLoader{

private Router $router;
    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function load(string $file): void{
        if (!file_exists($file)) throw new \RuntimeException("Archivo de rutas no encontrado: {$file}");
        $router = $this->router;
        require $file;  //se llama el archivo Modules/POS/routes.php y la variable $router queda visible en este archivo
    }
}