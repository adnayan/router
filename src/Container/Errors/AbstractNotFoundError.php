<?php

declare(strict_types=1);

namespace Nayan\Router\Container\Errors;

use Exception;

/**
 * Class AbstractNotFoundError
 *
 * An exception that is thrown when an abstract class is not found in the container.
 *
 * @package IoC
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

class AbstractNotFoundError extends Exception
{
    /**
     * AbstractNotFoundError constructor.
     *
     * This constructor accepts the name of the abstract class that was not found and constructs the exception message.
     *
     * @param string $abstract The name of the abstract class that was not found.
     */
    public function __construct(string $abstract)
    {
        parent::__construct("Abstract class {$abstract} not found in the container.");
    }
}
