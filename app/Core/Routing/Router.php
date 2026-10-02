<?php


namespace App\Core\Routing;

use App\Core\Container\Container;
use ReflectionMethod;

class Router{

    public array $getRoutes = [];
    public array $postRoutes = [];
    protected array $data = [];
    private ?Container $container = null;

    public function setContainer(Container $container): void{
        $this->container = $container;
    }

    public function get($url, $fn){ $this->getRoutes[$url] = $fn; }

    public function post($url, $fn){ $this->postRoutes[$url] = $fn; }

    public function comprobarRutas(){
        //$url_actual = $_SERVER['PATH_INFO'] ?? '/';
        $url_actual = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        //$url_actual = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if($method === 'GET'){
            $fn = $this->getRoutes[$url_actual] ?? null;
        }else{
            $fn = $this->postRoutes[$url_actual] ?? null;
        }

        // Controller definido como [Clase::class, 'metodo']
        if($fn){
            $controllerClass = $fn[0];
            $controllerMethod = $fn[1];
            $reflectionMethod = new ReflectionMethod($controllerClass, $controllerMethod);
            // Controller antiguo
            if($reflectionMethod->isStatic()){
                call_user_func($fn, $this); //$this es el router
                return;
            }

            // Controller moderno con DI
            if($this->container === null)throw new \RuntimeException('No hay un Container configurado en el Router.');
            $controller = $this->container->get($controllerClass); //obtiene las dependencias por container
            call_user_func([$controller, $controllerMethod]);  // no se pasa router
            return;
        }else{
            echo "Pagina No Encontrada o Ruta no valida"; //header('Location: /404');
        }
    }

    public function render($view, $datos = [], ?string $layout = null){  //este metodo se llama desde el controlador carpeta controllers
        foreach($datos as $key => $value){
            $$key = $value;  //$key = variable variale, cada llave del arreglo asociativo es variable
            //$$key genera variables con los nombres de los keys del arreglo asociativo
        }

        ob_start();
        include_once __DIR__ . "/../../../views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer // limpia la memoria y en variable $contenido se almacena el include de arriba, y la variable $contenido se muestra en el include de abajo
        
        /*
        * Si el Controller especifica un layout,
        * lo utilizamos directamente.
        */
        if($layout !== null){
            $layoutPath = __DIR__. "/../../../views/{$layout}/restaurant-layout.php";
            if(!file_exists($layoutPath))
                throw new \RuntimeException("Layout no encontrado: {$layout}");
            include_once $layoutPath;
            return;
        }
        
        $url_actual = $_SERVER['REQUEST_URI'] ?? '/';
        if(str_contains($url_actual, '/Cliente')){
            include_once __DIR__ . '/../../../views/cliente-layout.php'; //pagina maestra para el dashboard admin-cliente
        }else{
            if(str_contains($url_actual, '/admin')){  //busca /admin en la cadena o variable $url_admin
                include_once __DIR__ . '/../../../views/admin-layout.php';  //pagina maestra para el dashboard admin
            }else{
                include_once __DIR__ . '/../../../views/layout.php';  //pagina maestra para las paginas externas publicas
            }
        }
        
    }

    public function set(string $key, $value): void{
        $this->data[$key] = $value;
    }

    public function getData(): array{
        return $this->data;
    }

    // método para obtener una sola variable
    public function share(string $key, $default = null){
        return $this->data[$key] ?? $default;
    }
}