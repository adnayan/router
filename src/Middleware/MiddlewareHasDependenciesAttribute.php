<?php

declare(strict_types=1);

namespace Nayan\Router\Middleware;

use Attribute;

/**
 * Class MiddlewareHasDependenciesAttribute
 *
 * This attribute is used to specify the dependencies of a middleware.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

#[Attribute(Attribute::TARGET_CLASS)]
class MiddlewareHasDependenciesAttribute
{
    private array $dependencies;

    /**
     * MiddlewareHasDependenciesAttribute constructor.
     *
     * This constructor accepts an array of dependencies.
     *
     * @param array $dependencies The dependencies of the middleware.
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }
}
