<?php

declare(strict_types=1);

namespace Nayan\Router\Errors;

use Exception;

/**
 * Class ViewEngineNotFoundError
 *
 * An exception that is thrown when a view engine is not found.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

class ViewEngineNotFoundError extends Exception
{
    /**
     * ViewEngineNotFoundError constructor.
     *
     * This constructor accepts the name of the view engine and constructs the exception message.
     *
     */
    public function __construct()
    {
        parent::__construct("No view engine found.", 500);
    }
}