<?php

declare(strict_types=1);

namespace Nayan\Router\Container;

/**
 * Interface IContainer
 *
 * This interface defines the methods that a container must implement.
 *
 * @package IoC
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
interface IContainer
{
    /**
     * This method registers the configuration of the container.
     *
     * @param array $configurations The configuration of the container.
     * @return void
     */
    public function registerConfiguration(array $configurations): void;

    /**
     * This method returns the configuration of the container.
     *
     * @return array The configuration of the container.
     */
    public function getConfiguration(): array;

    /**
     * This method registers a singleton in the container.
     *
     * @param string $abstract The name of the abstract class.
     * @param $concrete The concrete class.
     * @return void
     */
    public function registerSingleton(string $abstract, $concrete): void;

    /**
     * This method registers a transient in the container.
     *
     * @param string $abstract The name of the abstract class.
     * @param $concrete The concrete class.
     * @return void
     */
    public function registerTransient(string $abstract, $concrete): void;

    /**
     * This method resolves the dependency.
     *
     * @param string $abstract The name of the abstract class.
     * @return mixed The resolved dependency.
     */
    public function get(string $abstract);
}
