<?php

declare(strict_types=1);

namespace Nayan\Router\Middleware\Errors;

use Exception;

/**
 * Class MiddlewareError
 *
 * An exception that is thrown when a middleware error occurs.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
class MiddlewareError extends Exception
{
    /**
     * MiddlewareError constructor.
     *
     * This constructor accepts the message that describes the error and the error code.
     *
     * @param string $message The message that describes the error.
     * @param int $code The error code.
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
