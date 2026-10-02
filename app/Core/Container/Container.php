<?php

namespace App\Core\Container;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Container{

    private array $bindings = [];
    private array $instances = [];

    /**
     * Relaciona una abstraccion con una implementacion.
     */
    public function bind(string $abstract, string $concrete): void{
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Registra una instancia ya existente.
     */
    public function instance(string $abstract, object $instance): void{
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resuelve una dependencia.
     */
    public function get(string $id): object{
        // Instancia previamente registrada
        if (isset($this->instances[$id]))
            return $this->instances[$id];

        // Existe un binding?
        $concrete = $this->bindings[$id] ?? $id;

        if(!class_exists($concrete))
            throw new RuntimeException("No se puede resolver la dependencia: {$id}");

        $reflection = new ReflectionClass($concrete);

        if(!$reflection->isInstantiable())
            throw new RuntimeException("La clase {$concrete} no puede ser instanciada.");

        $constructor = $reflection->getConstructor();

        // Clase sin constructor
        if($constructor === null){
            return new $concrete();
        }

        $dependencies = [];

        foreach($constructor->getParameters() as $parameter){
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException("No se puede resolver el parámetro \${$parameter->getName()} de {$concrete}");
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
    
}