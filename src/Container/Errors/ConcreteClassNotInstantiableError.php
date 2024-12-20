<?php

declare(strict_types=1);

namespace Nayan\Router\Container\Errors;

use Exception;

/**
 * Class ConcreteClassNotInstantiableError
 *
 * An exception that is thrown when a concrete class is not instantiable.
 *
 * @package IoC
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

class ConcreteClassNotInstantiableError extends Exception
{
    /**
     * ConcreteClassNotInstantiableError constructor.
     *
     * This constructor accepts the message that describes the error and constructs the exception message.
     *
     * @param string $message The message that describes the error.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
