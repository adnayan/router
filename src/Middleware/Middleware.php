<?php

declare(strict_types=1);

namespace Nayan\Router\Middleware;

use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;

use Nayan\Router\Container\IContainer;

/**
 * Abstract class Middleware
 *
 * This class is the base class for all middlewares.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
abstract class Middleware implements IMiddleware
{
    /**
     * This method handles the request and returns the response.
     *
     * @param IRequest $request The request object.
     * @param callable $next The next middleware.
     * @return IResponse The response object.
     */
    abstract public function handle(IRequest $request, callable $next): IResponse;
}
