<?php
namespace App\Core;

class Container
{
    private $registry = [];
    private $instances = [];

    public function set(string $name, \Closure $func): void
    {
        $this->registry[$name] = $func;
    }

    public function get(string $name): object
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (isset($this->registry[$name])) {
            $instance = $this->registry[$name]($this);
            $this->instances[$name] = $instance;
            return $instance;
        }

        $reflector = new \ReflectionClass($name);
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$name} is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            $instance = $reflector->newInstance();
            $this->instances[$name] = $instance;
            return $instance;
        }

        $dependencies = array_map(function ($param) {
            $type = $param->getType();
            if ($type === null) {
                throw new \Exception("Cannot resolve class dependency {$param->name}");
            }
            return $this->get($type->getName());
        }, $constructor->getParameters());

        $instance = $reflector->newInstanceArgs($dependencies);
        $this->instances[$name] = $instance;
        return $instance;
    }
}
?>
