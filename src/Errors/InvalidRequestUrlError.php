<?php

declare(strict_types=1);

namespace Nayan\Router\Errors;

use Exception;

/**
 * Class InvalidRequestUrlError
 *
 * An exception that is thrown when a requested resource is not found.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
class InvalidRequestUrlError extends Exception
{
    /**
     * InvalidRequestUrlError constructor.
     *
     * This constructor accepts the URL that was not found and constructs the exception message.
     *
     * @param string $url The URL that was not found.
     */
    public function __construct(string $url)
    {
        parent::__construct("Requested resource '" . $url . "' not found.", 404);
    }
}
