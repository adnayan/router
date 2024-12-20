<?php

declare(strict_types=1);

namespace Nayan\Router\Container;

use Nayan\Router\Container\Errors\AbstractNotFoundError;
use Nayan\Router\Container\Errors\CannotResolveDependencyError;
use Nayan\Router\Container\Errors\CircularDependencyDetectedError;
use Nayan\Router\Container\Errors\ConcreteClassNotInstantiableError;

/**
 * Class Container
 *
 * A simple Inversion of Control (IoC) container for managing class dependencies and performing dependency injection.
 *
 * @package IoC
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/NayanAdhikari/ioc
 */
class Container implements IContainer
{
    /**
     * @var array $configurations Stores the configuration settings for the container.
     */
    private $configurations = [];

    /**
     * @var array $bindings Stores the bindings of interfaces to their concrete implementations.
     */
    private $bindings = [];

    /**
     * @var array $instances Stores the instances of the resolved classes.
     */
    private $instances = [];

    /**
     * @var array $resolving Keeps track of the classes that are currently being resolved to prevent circular dependencies.
     */
    private $resolving = [];

    /**
     * Registers the given configurations.
     *
     * This method accepts an array of configurations and assigns it to the internal configurations property of the container. It is used to set up or update the container's configurations.
     *
     * @param array $configurations An associative array of configurations.
     *
     * @return void
     */
    public function registerConfiguration(array $configurations): void
    {
        $this->configurations = $configurations;
    }

    /**
     * Retrieves the configuration settings.
     *
     * This method returns an array of configuration settings that have been previously set in the container. It is used to access the current configuration state of the container.
     *
     * @return array The array of configuration settings.
     */
    public function getConfiguration(): array
    {
        return $this->configurations;
    }

    /**
     * Registers a singleton binding in the container.
     *
     * This method binds an abstract type to a concrete implementation and marks it as a singleton, meaning the same instance will be shared throughout the application.
     *
     * @param string $abstract The abstract type or interface to bind.
     * @param mixed $concrete The concrete implementation or closure that resolves the abstract type.
     *
     * @return void
     */
    public function registerSingleton(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => true,
        ];
    }

    /**
     * Register a transient (non-shared) binding in the container.
     *
     * This method registers a binding in the container that will not be shared.
     * Each time the binding is resolved, a new instance will be created.
     *
     * @param string $abstract The abstract type or interface to bind.
     * @param mixed $concrete The concrete implementation or closure that resolves the abstract type.
     * @return void
     */
    public function registerTransient(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => false,
        ];
    }

    /**
     * Retrieve an instance of the given abstract type from the container.
     *
     * This method resolves and returns an instance of the specified abstract type.
     * If the type is already instantiated and stored in the container, it returns
     * the existing instance. Otherwise, it resolves the type, creates an instance,
     * and stores it if it is marked as shared.
     *
     * @param string $abstract The abstract type identifier to retrieve.
     * @return mixed The resolved instance of the abstract type.
     * @throws AbstractNotFoundError If the abstract type is not registered in the container.
     * @throws CircularDependencyDetectedError If a circular dependency is detected while resolving the type.
     */
    public function get(string $abstract)
    {
        if (isset($this->instances[$abstract]))
        {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract]))
        {
            throw new AbstractNotFoundError($abstract);
        }

        if (in_array($abstract, $this->resolving))
        {
            throw new CircularDependencyDetectedError($abstract);
        }

        $this->resolving[] = $abstract;

        $binding = $this->bindings[$abstract];
        $concrete = $binding['concrete'];

        if ($concrete instanceof \Closure)
        {
            $object = $concrete($this);
        }
        else
        {
            $object = $this->resolve($concrete);
        }

        if ($binding['shared'])
        {
            $this->instances[$abstract] = $object;
        }

        array_pop($this->resolving);

        return $object;
    }

    /**
     * Resolves and instantiates a given class.
     *
     * This method uses reflection to inspect the given class and its constructor
     * to resolve and inject dependencies automatically. If the class is not instantiable
     * or if any dependencies cannot be resolved, an exception is thrown.
     *
     * @param string $concrete The fully qualified class name to resolve.
     * @return object An instance of the resolved class.
     * @throws \Exception If the class is not instantiable or if a dependency cannot be resolved.
     */
    private function resolve(string $concrete)
    {
        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable())
        {
            throw new ConcreteClassNotInstantiableError("Class $concrete is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor))
        {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter)
        {
            $dependency = $parameter->getType();

            if ($dependency === null)
            {
                throw new CannotResolveDependencyError("Cannot resolve the dependency for parameter {$parameter->getName()} in class $concrete.");
            }

            $dependencies[] = $this->get($dependency->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
