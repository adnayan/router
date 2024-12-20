<?php

declare(strict_types=1);

namespace Nayan\Router\Errors;

use Exception;

/**
 * Class ControllerNotFoundError
 *
 * An exception that is thrown when a controller is not found.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
class ControllerNotFoundError extends Exception
{
    /**
     * ControllerNotFoundError constructor.
     *
     * This constructor accepts the name of the controller and constructs the exception message.
     *
     * @param string $controller The name of the controller that was not found.
     */
    public function __construct(string $controller)
    {
        parent::__construct("Class '" . $controller . "' not found.", 500);
    }
}
