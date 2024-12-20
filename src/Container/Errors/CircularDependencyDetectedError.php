<?php

declare(strict_types=1);

namespace Nayan\Router\Container\Errors;

use Exception;

/**
 * Class CircularDependencyDetectedError
 *
 * An exception that is thrown when a circular dependency is detected in the container.
 *
 * @package IoC
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

class CircularDependencyDetectedError extends Exception
{
    /**
     * CircularDependencyDetectedError constructor.
     *
     * This constructor constructs the exception message.
     *
     * @param string $abstract The name of the abstract class that was not found.
     */
    public function __construct()
    {
        parent::__construct("Circular dependency detected in the container.");
    }
}
