<?php

declare(strict_types=1);

namespace WooExtender\Core;

use WooExtender\Vendor\Psr\Container\ContainerInterface;
use Exception;
use ReflectionClass;
use ReflectionException;

defined('ABSPATH') || exit;

class Container implements ContainerInterface
{
    private array $instances = [];
    private array $bindings = [];

    public function set(string $id, $concrete = null): void
    {
        $this->bindings[$id] = $concrete ?? $id;
    }

    public function get(string $id)
    {
        if (isset($this->instances[$id])) return $this->instances[$id];

        $instance = $this->resolve($id);
        $this->instances[$id] = $instance;

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->bindings[$id]);
    }

    public function forget(string $id): void
    {
        unset($this->instances[$id]);
        unset($this->bindings[$id]);
    }

    public function flush(): void
    {
        $this->instances = [];
        $this->bindings = [];
    }

    private function resolve(string $id)
    {
        $concrete = $this->bindings[$id] ?? $id;

        try {
            $reflectionClass = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new Exception("Class {$id} does not exist.");
        }

        if (!$reflectionClass->isInstantiable()) {
            throw new Exception("Class {$id} is not instantiable (it's likely an interface or abstract class).");
        }

        $constructor = $reflectionClass->getConstructor();

        if (is_null($constructor)) {
            return new $concrete();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type) {
                throw new Exception("Failed to resolve class {$id} because parameter '{$parameter->getName()}' has no type hint.");
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}